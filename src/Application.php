<?php
namespace Litovka\Framework;

use Litovka\Framework\Config\Config;


/**
 * Class Application
 * @package Litovka\Framework
 */
class Application
{

    /**
     * Application initialization
     * @param array $config
     */
    public function __construct(array $config)
    {
        Config::setConfig($config);
    }


    public function fire()
    {
        //@TODO add a fire action in request-commit
    }
}