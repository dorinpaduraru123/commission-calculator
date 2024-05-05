<?php


namespace Services;

use Helpers\Helper;
use Services\Providers\BinProviderInterface;
use Services\Providers\InputProviderInterface;
use Services\Providers\RatesProviderInterface;

class CommissionCalculator
{
    private InputProviderInterface $inputProvider;
    private RatesProviderInterface $ratesProvider;
    private BinProviderInterface $binProvider;

    public function __construct(
        InputProviderInterface $inputProvider,
        RatesProviderInterface $ratesProvider,
        BinProviderInterface   $binProvider
    )
    {
        $this->inputProvider = $inputProvider;
        $this->ratesProvider = $ratesProvider;
        $this->binProvider = $binProvider;
    }

    public function getResults(): array
    {
        $resultedValues = [];

        foreach ($this->inputProvider->getContentLines() as $object) {
            $resultedValues[] = $this->parseOneResult($object);
        }

        return $resultedValues;
    }

    private function parseOneResult(\stdClass $object): string
    {
        $rate = $this->ratesProvider->getRate($object->currency);

        if (null === $rate) {
            return 'Rate Not Found';
        }

        $baseResult = $object->amount / $rate;

        $countryTag = $this->binProvider->getCountryTag($object->bin);

        if (null === $countryTag) {
            return 'Country Tag Not Found';
        }

        return (string) round(
            Helper::isEu($countryTag)
                ? $baseResult * 0.01
                : $baseResult * 0.02
            , 2
        );
    }
}