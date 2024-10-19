<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

$jsonFile = __DIR__.'/data/github_issues.json';
$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

$client = new Client([
    'base_uri' => 'https://api.github.com/graphql',
    'headers' => [
        'Authorization' => 'bearer '.$_ENV['GITHUB_TOKEN'],
        'Content-Type' => 'application/json',
    ],
]);

$org = $_ENV['GITHUB_ORG'];
$repo = $_ENV['GITHUB_REPO'];
$projectNumber = (int) $_ENV['GITHUB_PROJECT_NUMBER'];

// First, get the project ID
$query = <<<'GRAPHQL'
query($org: String!, $repo: String!, $projectNumber: Int!) {
  organization(login: $org) {
    repository(name: $repo) {
      id
    }
    projectV2(number: $projectNumber) {
      id
    }
  }
}
GRAPHQL;

try {
    $response = $client->post('', [
        'json' => [
            'query' => $query,
            'variables' => [
                'org' => $org,
                'repo' => $repo,
                'projectNumber' => $projectNumber,
            ],
        ],
    ]);
    $result = json_decode($response->getBody(), true);

    if (isset($result['errors'])) {
        throw new Exception('GraphQL Error: '.json_encode($result['errors']));
    }
    $repoId = $result['data']['organization']['repository']['id'];
    $projectId = $result['data']['organization']['projectV2']['id'];
} catch (GuzzleException $e) {
    exit('Error fetching project: '.$e->getMessage()."\n");
} catch (Exception $e) {
    exit($e->getMessage()."\n");
}

foreach ($data['issues'] as $issue) {
    // Create the issue
    $mutation = <<<'GRAPHQL'
mutation($repositoryId: ID!, $title: String!, $body: String!) {
  createIssue(input: {repositoryId: $repositoryId, title: $title, body: $body}) {
    issue {
      id
    }
  }
}
GRAPHQL;

    try {
        $response = $client->post('', [
            'json' => [
                'query' => $mutation,
                'variables' => [
                    'repositoryId' => $repoId,
                    'title' => $issue['title'],
                    'body' => $issue['description'],
                ],
            ],
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['errors'])) {
            throw new Exception('GraphQL Error: '.json_encode($result['errors']));
        }

        $issueId = $result['data']['createIssue']['issue']['id'];

        // Add the issue to the project
        $mutation = <<<'GRAPHQL'
mutation($projectId: ID!, $contentId: ID!) {
  addProjectV2ItemById(input: {projectId: $projectId, contentId: $contentId}) {
    item {
      id
    }
  }
}
GRAPHQL;

        $response = $client->post('', [
            'json' => [
                'query' => $mutation,
                'variables' => [
                    'projectId' => $projectId,
                    'contentId' => $issueId,
                ],
            ],
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['errors'])) {
            throw new Exception('GraphQL Error: '.json_encode($result['errors']));
        }

        echo "Created issue: {$issue['title']} and added to project.\n";
    } catch (GuzzleException $e) {
        echo "Error creating issue {$issue['title']}: ".$e->getMessage()."\n";
    } catch (Exception $e) {
        echo "Error for issue {$issue['title']}: ".$e->getMessage()."\n";
    }
}

echo "Process completed.\n";
