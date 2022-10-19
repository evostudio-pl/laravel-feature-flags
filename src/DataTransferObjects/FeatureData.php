<?php

namespace Evolabs\FeatureFlags\DataTransferObjects;

class FeatureData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $group,
        public readonly bool $is_enabled
    ) {
    }
}
