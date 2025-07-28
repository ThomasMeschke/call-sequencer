<?php

declare(strict_types=1);

use thomasmeschke\cseq\CompilerOptions;
use thomasmeschke\cseq\Parser;
use thomasmeschke\cseq\Scanner;
use thomasmeschke\cseq\SequenceDiagramCompiler;

require("./vendor/autoload.php");

if ($argc < 3) {
    echo "Usage: {$argv[0]} <inputfile> <outputfile>";
    exit(64);
}

$inputfile = $argv[1];
$outputfile = $argv[2];


$contents = file($inputfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$scanner = new Scanner();
$callstacks = $scanner->scan($contents);

$parser = new Parser();
$callGraph = $parser->parse($callstacks);

$options = new CompilerOptions();
$options->basePath = 'D:\\Repos\\eportal\\';
$options->cutOffNamespace = 'CodeIgniter';

$compiler = new SequenceDiagramCompiler($options);
$diagram = $compiler->compileCallGraph($callGraph, basename($inputfile));

file_put_contents($outputfile, $diagram);

exit(0);