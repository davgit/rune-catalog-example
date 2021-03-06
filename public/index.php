<?php

require_once __DIR__ . '/../vendor/autoload.php';

use uuf6429\Rune;
use uuf6429\RuneCatalogExample\Action;
use uuf6429\RuneCatalogExample\Model;
use uuf6429\RuneCatalogExample\Context;

// load default data and override it with $_POST data (do some cleanup here)
$data = array_merge(
    require __DIR__ . '/data.php',
    array_map(
        function ($group) {
            return !is_array($group) ? $group : array_filter(
                array_values($group),
                'array_filter'
            );
        },
        $_POST
    )
);

/** @var Rune\Rule\GenericRule[] $rules */
$rules = array_map(
    function ($index, $data) {
        return new Rune\Rule\GenericRule($index + 1, $data[0], $data[1]);
    },
    array_keys($data['rules']),
    $data['rules']
);

$categories = [];

$categoryProvider = function ($id) use (&$categories) {
    return $id ? $categories[$id - 1] : null;
};

$categories = array_map(
    function ($index, $data) use ($categoryProvider) {
        return new Model\Category($index + 1, $data[0], $data[1], $categoryProvider);
    },
    array_keys($data['categories']),
    $data['categories']
);

$products = array_map(
    function ($index, $data) use ($categoryProvider) {
        return new Model\Product($index + 1, $data[0], $data[1], $data[2], $categoryProvider);
    },
    array_keys($data['products']),
    $data['products']
);

$exceptionHandler = new Rune\Exception\ExceptionCollectorHandler();
$engine = new Rune\Engine($exceptionHandler);
$action = new Action\PrintAction();
$context = new Context\ProductContext();
$descriptor = $context->getContextDescriptor();

// Provide code compiled from rule conditions
$output_generated = '';
$eval = new Rune\Util\SymfonyEvaluator();
$maxLength = 0;
foreach ($rules as $rule) {
    $maxLength = max($maxLength, strlen($rule->getName()));
}
foreach ($rules as $rule) {
    try {
        $eval->setFunctions($descriptor->getFunctions());
        $eval->setVariables($descriptor->getVariables());
        $code = $eval->compile($rule->getCondition());
    } catch (\Exception $ex) {
        $code = 'Compile Error (' . get_class($ex) . '): ' . $ex->getMessage();
    }
    $output_generated .= str_pad($rule->getName(), $maxLength)
        . ' => ' . $code . PHP_EOL;
}

// Provide triggered rules and any generated errors
ob_start();
foreach ($products as $product) {
    $context = new Context\ProductContext($product);
    $engine->execute($context, $rules, $action);
}
$output_result = htmlspecialchars(ob_get_clean(), ENT_QUOTES);
$output_errors = implode(PHP_EOL, $exceptionHandler->getExceptions());

// Provide some details use for dynamic editor
$json_tokens = json_encode([
    'constants' => [
        [
            'name' => 'true',
            'type' => 'boolean',
        ],
        [
            'name' => 'false',
            'type' => 'boolean',
        ],
        [
            'name' => 'null',
            'type' => 'null',
        ],
    ],
    'operators' => [
        '+', '-', '*', '/', '%', '**',                              // arithmetic
        '&', '|', '^',                                              // bitwise
        '==', '===', '!=', '!==', '<', '>', '<=', '>=', 'matches',  // comparison
        'not', '!', 'and', '&&', 'or', '||',                        // logical
        '~',                                                        // concatentation
        'in', 'not in',                                             // array
        '..',                                                       // range
        '?', '?:', ':',                                             // ternary
    ],
    'variables' => array_values($descriptor->getVariableTypeInfo()),
    'functions' => array_values($descriptor->getFunctionTypeInfo()),
    'typeinfo' => $descriptor->getDetailedTypeInfo(),
]);
$json_categories = json_encode($data['categories']);
$json_products = json_encode($data['products']);
$json_rules = json_encode($data['rules']);

require_once 'view.php';
