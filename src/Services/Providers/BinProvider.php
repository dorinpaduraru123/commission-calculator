<?php


namespace Services\Providers;


class BinProvider implements BinProviderInterface
{
    private string $sourceAddress;

    public function __construct(string $sourceAddress)
    {
        $this->sourceAddress = $sourceAddress;
    }

    public function getCountryTag(int $bin): ?string
    {
        // Hardcoded this result because the online file was causing issues for me
        // Comment line 20 to use this provider properly
        //return 'RO';

        $binResults = json_decode(file_get_contents($this->sourceAddress . $bin));

        return $binResults->country->alpha2 ?? null;
    }
}