<?php

namespace uuf6429\RuneCatalogExample\Model;

class StringUtils
{
    /**
     * Lowercases some text.
     *
     * @param string $text
     *
     * @return string
     */
    public function lower($text): string
    {
        return strtolower($text);
    }

    /**
     * Uppercases some text.
     *
     * @param string $text
     *
     * @return string
     */
    public function upper($text): string
    {
        return strtoupper($text);
    }
}
