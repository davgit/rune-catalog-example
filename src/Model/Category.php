<?php

namespace uuf6429\RuneCatalogExample\Model;

use uuf6429\RuneCatalogExample\LazyProperties;

/**
 * @property \uuf6429\RuneCatalogExample\Model\Category $parent
 */
class Category
{
    use LazyProperties;

    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    protected $parentId;

    /** @var callable */
    protected $categoryProvider;

    /**
     * @param int $id
     * @param string $name
     * @param int $parentId
     * @param callable $categoryProvider returns category given $id as first param
     */
    public function __construct($id, $name, $parentId, $categoryProvider)
    {
        $this->id = $id;
        $this->name = $name;

        $this->parentId = $parentId;
        $this->categoryProvider = $categoryProvider;
    }

    /**
     * @return null|Category
     */
    protected function getParent(): ?Category
    {
        $call = $this->categoryProvider;

        return $call($this->parentId);
    }

    /**
     * Returns true if category name or any of its parents are identical to $name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function in($name): bool
    {
        if (strtolower($this->name) === strtolower($name)) {
            return true;
        }

        if ($this->parent !== null) {
            return $this->parent->in($name);
        }

        return false;
    }
}
