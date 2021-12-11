<?php
/*
 * Additional rules or rules to override.
 * These rules will be added to default rules or will override them if the same key already exists.
 */
$additionalRules = [
    'native_function_invocation' => [
        'include' => [
            '@all',
        ],
        'scope' => 'all',
        'strict' => true,
    ],
    'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
    'phpdoc_align' => true,
    'phpdoc_return_self_reference' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types_order' => ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_last'],
    'phpdoc_to_comment' => false,
    'phpdoc_var_without_name' => false,
    '@Symfony' => true,
];

$config = new PhpCsFixer\Config();
$config->setUsingCache(false);
$config->setRiskyAllowed(true);
$config->setRules($additionalRules);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

$config->setFinder($finder);

return $config;
