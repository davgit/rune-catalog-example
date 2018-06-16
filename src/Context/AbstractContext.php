<?php

namespace uuf6429\RuneCatalogExample\Context;

use uuf6429\Rune\Context\ClassContext;
use uuf6429\RuneCatalogExample\Model\StringUtils;

abstract class AbstractContext extends ClassContext
{
    /**
     * @var \uuf6429\RuneCatalogExample\Model\StringUtils
     */
    public $String;

    public function __construct()
    {
        $this->String = new StringUtils();
    }
}
