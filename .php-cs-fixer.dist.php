<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'vendor',
    ])
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'phpdoc_align' => false,
        'yoda_style' => false,
        'increment_style' => false,
        'no_unneeded_curly_braces' => false,
        'standardize_increment' => false,
        'single_line_throw' => false,
    ])
    ->setFinder($finder);