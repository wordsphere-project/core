<?php

declare(strict_types=1);
it('can get health checks', function (): void {
    $this->get('/up')->assertOk();
});
