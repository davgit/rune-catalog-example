<?php

namespace uuf6429\RuneCatalogExample;

use PHPUnit\Framework\TestCase;
use uuf6429\Rune\Util\TypeInfoClass;
use uuf6429\Rune\Util\TypeInfoMember;

class TypeInfoTest extends TestCase
{
    public function testExampleTypeInfo(): void
    {
        $context = new Context\ProductContext();
        $descriptor = $context->getContextDescriptor();

        $this->assertEquals(
            [
                'product' => new TypeInfoMember('product', ['\\' . Model\Product::class]),
                'String' => new TypeInfoMember('String', ['\\' . Model\StringUtils::class]),
            ],
            $descriptor->getVariableTypeInfo(),
            'Assert variable type information'
        );

        $this->assertEquals(
            [
                //'lower' => new Util\TypeInfoMember('lower', ['method'], '<div class="cm-signature"><span class="type">string</span> <span class="name">lower</span>(<span class="args"><span class="arg" title=""><span class="type">string </span>$text</span></span>)</span></div>Lowercases some text.'),
            ],
            $descriptor->getFunctionTypeInfo(),
            'Assert function type information'
        );

        $this->assertEquals(
            [
                Context\ProductContext::class => new TypeInfoClass(
                    Context\ProductContext::class,
                    [
                        'product' => new TypeInfoMember('product', ['\\' . Model\Product::class]),
                        'getContextDescriptor' => new TypeInfoMember('getContextDescriptor', ['method'], '<div class="cm-signature"><span class="type"></span> <span class="name">getContextDescriptor</span>(<span class="args"></span>)</span></div>'),
                        'String' => new TypeInfoMember('String', ['\\' . Model\StringUtils::class], ''),
                    ]
                ),
                '\\' . Model\Product::class => new TypeInfoClass(
                    '\\' . Model\Product::class,
                    [
                        'id' => new TypeInfoMember('id', ['integer']),
                        'name' => new TypeInfoMember('name', ['string']),
                        'colour' => new TypeInfoMember('colour', ['string']),
                        'category' => new TypeInfoMember('category', ['\\' . Model\Category::class]),
                    ]
                ),
                '\\' . Model\Category::class => new TypeInfoClass(
                    '\\' . Model\Category::class,
                    [
                        'id' => new TypeInfoMember('id', ['integer']),
                        'name' => new TypeInfoMember('name', ['string']),
                        'in' => new TypeInfoMember('in', ['method'], '<div class="cm-signature"><span class="type">bool</span> <span class="name">in</span>(<span class="args"><span class="arg" title=""><span class="type">string </span>$name</span></span>)</span></div>Returns true if category name or any of its parents are identical to $name.'),
                        'parent' => new TypeInfoMember('parent', ['\\' . Model\Category::class]),
                    ]
                ),
                '\\' . Model\StringUtils::class => new TypeInfoClass(
                    '\\' . Model\StringUtils::class,
                    [
                        'lower' => new TypeInfoMember('lower', ['method'], '<div class="cm-signature"><span class="type">string</span> <span class="name">lower</span>(<span class="args"><span class="arg" title=""><span class="type">string </span>$text</span></span>)</span></div>Lowercases some text.'),
                        'upper' => new TypeInfoMember('upper', ['method'], '<div class="cm-signature"><span class="type">string</span> <span class="name">upper</span>(<span class="args"><span class="arg" title=""><span class="type">string </span>$text</span></span>)</span></div>Uppercases some text.'),
                    ]
                ),
            ],
            $descriptor->getDetailedTypeInfo(),
            'Assert detailed type information'
        );
    }
}
