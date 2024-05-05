<?php

namespace tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Services\CommissionCalculator;
use Services\Providers\BinProvider;
use Services\Providers\InputProvider;
use Services\Providers\RatesProvider;

final class CommissionCalculatorTest extends TestCase
{
    private CommissionCalculator $commissionCalculator;
    private MockObject $inputProviderMock;
    private MockObject $ratesProviderMock;
    private MockObject $binProviderMock;


    protected function setUp(): void
    {
        parent::setUp();

        $this->inputProviderMock = $this->createMock(InputProvider::class);
        $this->ratesProviderMock = $this->createMock(RatesProvider::class);
        $this->binProviderMock = $this->createMock(BinProvider::class);

        $this->commissionCalculator = new CommissionCalculator(
            $this->inputProviderMock,
            $this->ratesProviderMock,
            $this->binProviderMock
        );
    }

    public static function basicProvider(): array
    {
        return [
            'empty input'  => [
                'inputProviderLines' => [],
                'rates' => [],
                'countryTags' => [],
                'expectedResult' => [],
            ],
            'one object '  => [
                'inputProviderLines' => [
                    json_decode('{"bin":"45717360","amount":"100.00","currency":"EUR"}'),
                ],
                'rates' => [
                    ['EUR', 1.0]
                ],
                'countryTags' => [
                    [45717360, 'RO'],
                ],
                'expectedResult' => [
                    1,
                ],
            ],
            'multiple objects'  => [
                'inputProviderLines' => [
                    json_decode('{"bin":"45717360","amount":"100.00","currency":"EUR"}'),
                    json_decode('{"bin":"45717361","amount":"1120.00","currency":"JPY"}'),
                    json_decode('{"bin":"45717362","amount":"500.00","currency":"RON"}'),
                ],
                'rates' => [
                    ['EUR', 1.0],
                    ['JPY', 5.6],
                    ['RON', 5.0],
                ],
                'countryTags' => [
                    [45717360, 'RO'],
                    [45717361, 'NON-EU'],
                    [45717362, 'RO'],
                ],
                'expectedResult' => [
                    1,
                    4,
                    1,
                ],
            ],
            'multiple objects with failing providers'  => [
                'inputProviderLines' => [
                    json_decode('{"bin":"45717360","amount":"100.00","currency":"EURO"}'),
                    json_decode('{"bin":"45717361","amount":"1120.00","currency":"JPY"}'),
                    json_decode('{"bin":"45717362","amount":"500.00","currency":"RON"}'),
                ],
                'rates' => [
                    ['EUR', 1.0],
                    ['JPY', 5.6],
                    ['RON', 5.0],
                ],
                'countryTags' => [
                    [45717361, 'NON-EU'],
                    [45717362, null],
                ],
                'expectedResult' => [
                    'Rate Not Found',
                    4,
                    'Country Tag Not Found',
                ],
            ]
        ];
    }

    /**
     * @dataProvider basicProvider
     */
    public function testBasic(
        array $inputProviderLines,
        array $rates,
        array $countryTags,
        array $expectedResult
    ): void {
        $this->inputProviderMock->expects($this->once())->method('getContentLines')->willReturn($inputProviderLines);

        $this
            ->ratesProviderMock
            ->expects($this->exactly(count($rates)))
            ->method('getRate')
            ->willReturnMap($rates)
        ;

        $this
            ->binProviderMock
            ->expects($this->exactly(count($countryTags)))
            ->method('getCountryTag')
            ->willReturnMap($countryTags)
        ;

        $results = $this->commissionCalculator->getResults();
        $this->assertEquals($expectedResult, $results);
    }
}
