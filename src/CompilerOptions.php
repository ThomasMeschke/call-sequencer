<?php

declare(strict_types=1);

namespace thomasmeschke\cseq;

class CompilerOptions
{
    public string $basePath = '';
    public string $cutOffNamespace = '';
    /**
     * @var array<string, string> $replaceOptions
     */
    public array $replaceOptions = [];

}
