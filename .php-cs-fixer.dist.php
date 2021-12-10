<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'                     => true,
        '@PSR12'                       => true,
        'array_syntax'                 => ['syntax' => 'short'],
        'no_unused_imports'            => true,
        'blank_line_after_opening_tag' => true,
        'concat_space'                 => ['spacing' => 'one'],
        'binary_operator_spaces'       => [
            'default'   => 'align_single_space',
            'operators' => [
                '=>' => 'align_single_space',
                '='  => 'single_space',
            ],
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
