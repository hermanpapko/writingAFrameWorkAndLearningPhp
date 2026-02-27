<?php
$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/../')
    ->exclude('config')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder);