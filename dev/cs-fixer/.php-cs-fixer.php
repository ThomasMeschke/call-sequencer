<?php

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache');