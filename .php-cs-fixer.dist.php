<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->exclude([
        'vendor',
        'build',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'binary_operator_spaces' => [
            'default' => 'align_single_space_minimal',
            'operators' => ['=>' => 'align_single_space'],
        ],
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try', 'foreach', 'for', 'while', 'if'],
        ],
        'single_quote' => false,
        'no_superfluous_phpdoc_tags' => true
    ])
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
