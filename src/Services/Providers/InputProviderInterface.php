<?php

namespace Services\Providers;
interface InputProviderInterface
{
    public function __construct(string $sourceAddress);

    public function getContentLines(): array;
}