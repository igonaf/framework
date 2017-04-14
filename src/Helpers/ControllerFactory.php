<?php

namespace Litovka\Framework\Helpers;

/**
 * Class ControllerFactory
 * @package Litovka\Framework\Helpers
 */
class ControllerFactory
{
    /**
     *creates new controller name
     *
     * @param $name
     * @param $suffix
     * @return MakeName
     */
    public static function create(string $name, string $suffix): string
    {
        $new_name = new MakeName($name, $suffix);
        return $new_name->makeNewName();
    }
}