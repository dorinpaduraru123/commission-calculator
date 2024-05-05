<?php

namespace Services\Providers;


class RatesProvider implements RatesProviderInterface
{
    private array $rates;

    public function __construct(string $rateExchangeLink)
    {
        $this->setRates($this->loadRates($rateExchangeLink));
    }

    public function getRate(string $currency): ?float
    {
        if (!isset($this->rates[$currency])) {
            return null;
        }

        return $this->rates[$currency];
    }

    private function loadRates(string $rateExchangeLink): ?array
    {
        // Hardcoded this result because the online file requires some key
        // Comment line 28 to use this provider properly
        return json_decode('{"rates":{"EUR": 1,"USD": 1.2,"JPY": 5.4}}', true)['rates'];

        return json_decode(file_get_contents($rateExchangeLink), true)['rates']
            ?? [];
    }

    private function setRates(array $rates): void
    {
        $this->rates = $rates;
    }
}
