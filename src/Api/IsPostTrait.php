<?php

declare(strict_types=1);

namespace Jschaedl\Api;

trait IsPostTrait
{
    public function getMethod(): string
    {
        return 'POST';
    }
}
