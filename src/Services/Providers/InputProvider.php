<?php

namespace Services\Providers;

class InputProvider implements InputProviderInterface
{
    private array $contentLines;
    private string $sourceAddress;

    public function __construct(string $sourceAddress)
    {
        $this->sourceAddress = $sourceAddress;
        $this->contentLines = $this->decodeContent();
    }

    public function getContentLines(): array
    {
        return $this->contentLines;
    }

    private function decodeContent(): array
    {
        $fileRows = [];

        foreach (file($this->sourceAddress) as $row) {
            $fileRows[] = json_decode($row);
        }

        return $fileRows;
    }
}
