# WordSphere Core


<p>
    <a href="https://github.com/wordsphere-project/core/actions">
        <img src="https://github.com/wordsphere-project/core/workflows/run-tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://github.com/sponsors/wordsphere-project/?sponsor=1">
        <img src="https://img.shields.io/github/sponsors/wordsphere-project" alt="GitHub Sponsors">
    </a>
    <a href="https://packagist.org/packages/wordsphere-project/core">
        <img src="https://img.shields.io/packagist/dt/wordsphere-project/core" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/wordsphere-project/core">
        <img src="https://img.shields.io/packagist/v/wordsphere-project/core" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/wordsphere-project/core">
        <img src="https://img.shields.io/github/license/wordsphere-project/core" alt="License">
    </a>
</p>

## Introduction

WordSphere Core is the heart of the WordSphere content management system, built with Domain-Driven Design principles. This package provides the core functionality of WordSphere and can be integrated into existing Laravel applications. WordSphere is designed to be a flexible, scalable, and powerful CMS solution for modern web applications.

## Key Features

- Domain-Driven Design Architecture
- Flexible Content Management
- Advanced User Management
- Theming System
- Plugin Architecture
- API-First Approach
- SEO Optimization
- Media Management
- Localization Support
- Performance Optimized

## Installation

You can install the package via composer:

```bash
composer require wordsphere/core
```

## Development
1. Clone this repository
2. Run the following commands from within `core` directory:

```shell
npm install
composer update
composer dev
```

3. Start developing WordSphere. The development server will start, and you can access the application at `http://localhost:8000`.

## Admin Dashboard

When running WordSphere via the `composer dev` command, a default user will be created.
You can log in to the dashboard at /admin using these credentials:

- *Email*: `test@wordsphere.io`
- *Password*: `password`

## Testing
To run tests, use the following command:

```shell
composer test
```

### Contributing
We welcome contributions to WordSphere! Please start by helping to write our Contributing Guide ;).

### Security Vulnerabilities
If you discover a security vulnerability within WordSphere, please send an e-mail to our security team via security@wordsphere.io. All security vulnerabilities will be promptly addressed.

### License
WordSphere Core is open-sourced software licensed under the [MIT license](LICENSE.md).
