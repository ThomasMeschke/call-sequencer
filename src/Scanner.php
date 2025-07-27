<?php

declare(strict_types=1);

namespace thomas\cseq;

class Scanner
{
    public function __construct(public string $rootNamespace = '')
    {

    }

    /**
     * @param array<string> $contents
     *
     * @return array<array<string>> Collection of Callstacks
     */
    public function scan(array $contents): array
    {
        $callstacks = [];
        foreach ($contents as $callstack) {
            $callstacks[] = $this->processCallstack($callstack);
        }

        return $callstacks;
    }

    /**
     * @return array<string> Collection of Calls
     */
    private function processCallstack(string $callstack): array
    {
        return explode(';', $callstack);
    }
}
