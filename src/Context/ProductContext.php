<?php

namespace uuf6429\RuneCatalogExample\Context;

class ProductContext extends AbstractContext
{
    /**
     * @var \uuf6429\RuneCatalogExample\Model\Product
     */
    public $product;

    /**
     * @param \uuf6429\RuneCatalogExample\Model\Product $product
     */
    public function __construct(\uuf6429\RuneCatalogExample\Model\Product $product = null)
    {
        parent::__construct();

        $this->product = $product;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ucwords(trim($this->product->colour . ' ' . $this->product->name));
    }
}
