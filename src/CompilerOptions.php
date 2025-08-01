<?php

declare(strict_types=1);

namespace thomasmeschke\cseq;

class CompilerOptions
{
    public string $basePath = '';
    /**
     * @var array<string> $cutOffNamespaces
     */
    public array $cutOffNamespaces = [];
    /**
     * @var array<string, string> $replaceOptions
     */
    public array $replaceOptions = [];

    public static function fromJson(string $json): CompilerOptions
    {
        $instance = new CompilerOptions();

        $options = json_decode($json, associative: true);
        foreach ($options as $key => $value) {
            $instance->{$key} = $value;
        }

        return $instance;
    }
}
