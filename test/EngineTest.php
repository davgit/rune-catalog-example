<?php

namespace uuf6429\RuneCatalogExample;

use PHPUnit\Framework\TestCase;
use uuf6429\Rune\Engine;
use uuf6429\Rune\Exception\ExceptionCollectorHandler;
use uuf6429\Rune\Rule\GenericRule;

class EngineTest extends TestCase
{
    public function testEngineExecution(): void
    {
        $this->expectOutputString(implode(PHP_EOL, [
            'Rule 1 (Red Products) triggered for Red Bricks.',
            'Rule 5 (Toys) triggered for Red Bricks.',
            'Rule 3 (Green Socks) triggered for Green Soft Socks.',
            'Rule 4 (Socks) triggered for Green Soft Socks.',
            'Rule 6 (Clothes) triggered for Green Soft Socks.',
            'Rule 4 (Socks) triggered for Yellow Sporty Socks.',
            'Rule 6 (Clothes) triggered for Yellow Sporty Socks.',
            'Rule 5 (Toys) triggered for Lego Blocks.',
            'Rule 6 (Clothes) triggered for Black Adidas Jacket.',
        ]) . PHP_EOL);

        $exceptions = new ExceptionCollectorHandler();
        $engine = new Engine($exceptions);
        $action = new Action\PrintAction();

        foreach ($this->getProducts() as $product) {
            $context = new Context\ProductContext($product);
            $engine->execute($context, $this->getRules(), $action);
        }

        $this->assertSame(
            '',
            implode(PHP_EOL, $exceptions->getExceptions()),
            'RuleEngine should not generate errors.'
        );
    }

    /**
     * @return GenericRule[]
     */
    private function getRules(): array
    {
        return [
            new GenericRule(1, 'Red Products', 'product.colour == String.lower("Red")'),
            new GenericRule(2, 'Red Socks', 'String.upper(product.colour) == "RED" and (product.name matches "/socks/i") > 0'),
            new GenericRule(3, 'Green Socks', 'product.colour == "green" and (product.name matches "/socks/i") > 0'),
            new GenericRule(4, 'Socks', 'product.category.in("Socks")'),
            new GenericRule(5, 'Toys', 'product.category.in("Toys")'),
            new GenericRule(6, 'Clothes', 'product.category.in("Clothes")'),
        ];
    }

    /**
     * @return Model\Product[]
     */
    private function getProducts(): array
    {
        $cp = $this->getCategoryProvider();

        return [
            new Model\Product(1, 'Bricks', 'red', 3, $cp),
            new Model\Product(2, 'Soft Socks', 'green', 6, $cp),
            new Model\Product(3, 'Sporty Socks', 'yellow', 6, $cp),
            new Model\Product(4, 'Lego Blocks', '', 3, $cp),
            new Model\Product(6, 'Adidas Jacket', 'black', 5, $cp),
        ];
    }

    /**
     * @return Model\Category[]
     */
    private function getCategories(): array
    {
        $cp = $this->getCategoryProvider();

        return [
            new Model\Category(1, 'Root', 0, $cp),
            new Model\Category(2, 'Clothes', 1, $cp),
            new Model\Category(3, 'Toys', 1, $cp),
            new Model\Category(4, 'Underwear', 2, $cp),
            new Model\Category(5, 'Jackets', 2, $cp),
            new Model\Category(6, 'Socks', 4, $cp),
        ];
    }

    /**
     * @return callable
     */
    private function getCategoryProvider(): callable
    {
        /**
         * @param int $id
         *
         * @return Model\Category|null
         */
        return function ($id): ?Model\Category {
            foreach ($this->getCategories() as $category) {
                if ($category->id === $id) {
                    return $category;
                }
            }

            return null;
        };
    }
}
