<?php

namespace Litovka\Framework\Helpers;

/**
 * Class MakeName
 * @package Litovka\Framework\Helpers
 */
class MakeName
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $suffix;

    /**
     * MakeName constructor.
     * @param string $name
     * @param string $suffix
     */
    public function __construct(string $name, string $suffix)
    {
        $this->name = $name;
        $this->suffix = $suffix;
    }

    /**
     * unites 2 params for factory
     *
     * @return string
     */
    public function makeNewName(): string
    {
        return $this->name . $this->suffix;
    }
}