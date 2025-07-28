<?php

declare(strict_types=1);

use thomasmeschke\cseq\CompilerOptions;
use thomasmeschke\cseq\Parser;
use thomasmeschke\cseq\Scanner;
use thomasmeschke\cseq\SequenceDiagramCompiler;

require("./vendor/autoload.php");

if ($argc < 3) {
    echo "Usage: {$argv[0]} -i<inputfile> -o<outputfile> [-c<optionsfile>]" . PHP_EOL;
    echo "with the options being:" . PHP_EOL;
    echo "    -i<inputfile>    required  The xdebug trace file to process" . PHP_EOL;
    echo "    -o<outputfile>   required  The plantUML output file to generate" . PHP_EOL;
    echo "    -c<optionsfile>  optional  A JSON file containing the compiler options" . PHP_EOL;
    echo PHP_EOL;
    echo "    Compiler Options are:" . PHP_EOL;
    echo "      'basePath'        (string)    Path segment to be removed from absolute paths," . PHP_EOL;
    echo "                                    e.g. the projects root path. This gets removed" . PHP_EOL;
    echo "                                    from all absolute paths, like include paths." . PHP_EOL;
    echo "      'cutOffNamespace' (string)    Namespace that the diagram will follow into," . PHP_EOL;
    echo "                                    e.g. the used frameworks base namespace." . PHP_EOL;
    echo "                                    Only the first stackframe inside this namespace" . PHP_EOL;
    echo "                                    will be included in every callstack." . PHP_EOL;
    echo "      'replaceOptions'  (object)    A collection of keys and string values," . PHP_EOL;
    echo "                                    specifying which string is to be replace with which." . PHP_EOL;
    echo "                                    Can be used to redact sensitive information like" . PHP_EOL;
    echo "                                    company names from the resulting diagram." . PHP_EOL;
    exit(64);
}

$inputSpecified = false;
$outputSpecified = false;
$optionsSpecified = false;

foreach ($argv as $arg) {
    if (str_starts_with($arg, '-i')) {
        $inputfile = substr($arg, 2);
        $inputSpecified = true;
        continue;
    }
    if (str_starts_with($arg, '-o')) {
        $outputfile = substr($arg, 2);
        $outputSpecified = true;
        continue;
    }
    if (str_starts_with($arg, '-c')) {
        $optionsfile = substr($arg, 2);
        $optionsSpecified = true;
        continue;
    }
}

if (! $inputSpecified) {
    echo "Error: No input file was specified!" . PHP_EOL;
    echo "Call {$argv[0]} without parameters to learn about its usage.";
    exit(64);
}

if (! $outputSpecified) {
    echo "Error: No output file was specified!" . PHP_EOL;
    echo "Call {$argv[0]} without parameters to learn about its usage.";
    exit(64);
}

$options = new CompilerOptions();
if ($optionsSpecified) {
    $optionsJson = file_get_contents($optionsfile);
    $options = CompilerOptions::fromJson($optionsJson);
}

$contents = file($inputfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$scanner = new Scanner();
$callstacks = $scanner->scan($contents);

$parser = new Parser();
$callGraph = $parser->parse($callstacks);

$outputfileExtension = pathinfo($outputfile, PATHINFO_EXTENSION);
$outputfileName = basename($outputfile, ".{$outputfileExtension}");

$compiler = new SequenceDiagramCompiler($options);
$diagram = $compiler->compileCallGraph($callGraph, $outputfileName);

file_put_contents($outputfile, $diagram);

exit(0);