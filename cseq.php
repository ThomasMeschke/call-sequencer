<?php

declare(strict_types=1);

use thomas\cseq\CompilerOptions;
use thomas\cseq\Parser;
use thomas\cseq\Scanner;
use thomas\cseq\SequenceDiagramCompiler;

if ($argc < 3) {
    echo "Usage: {$argv[0]} <inputfile> <outputfile>";
    exit(64);
}

$inputfile = $argv[1];
$outputfile = $argv[2];

require("./vendor/autoload.php");

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