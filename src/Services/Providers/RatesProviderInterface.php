<?php

namespace Services\Providers;
interface RatesProviderInterface
{
    public function __construct(string $rateExchangeLink);

    public function getRate(string $currency): ?float;
}