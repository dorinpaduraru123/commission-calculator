<?php

namespace Services\Providers;
interface BinProviderInterface
{
    public function __construct(string $sourceAddress);

    public function getCountryTag(int $bin): ?string;
}