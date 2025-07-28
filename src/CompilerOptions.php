<?php

declare(strict_types=1);

namespace thomasmeschke\cseq;

use stdClass;

class CompilerOptions
{
    public string $basePath = '';
    public string $cutOffNamespace = '';
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
