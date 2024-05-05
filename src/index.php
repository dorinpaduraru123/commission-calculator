<?php

require __DIR__ . '/../vendor/autoload.php';

use Services\CommissionCalculator;
use Services\Providers\BinProvider;
use Services\Providers\InputProvider;
use Services\Providers\RatesProvider;

$rateProvider = new RatesProvider('https://api.exchangeratesapi.io/latest');
$binProvider = new BinProvider('https://lookup.binlist.net/');
$inputProvider = new InputProvider('./input.txt');

$main  = new CommissionCalculator($inputProvider, $rateProvider, $binProvider);

foreach ($main->getResults() as $result) {
    echo $result;
    print "\n";
}
