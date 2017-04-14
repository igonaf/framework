<?php

namespace Litovka\Framework\Config;

use Litovka\Framework\Config\Exceptions\{
    ConfigNotExistException,
    ConfigParamExistException
};

/**
 * Class Config
 * @package Litovka\Framework\Config
 */
class Config
{
    /**
     * @var array
     */
    private static $config = [];

    /**
     * get all exist configs
     *
     * @return array
     */
    public static function getAllConfig(): array
    {
        return self::$config;
    }

    /**
     * get configs by the key
     *
     * @param string $key
     * @return array
     * @throws ConfigNotExistException
     */
    public static function getConfig(string $key): array
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        throw new ConfigNotExistException(" '$key' " . " doesn't exist in config");
    }

    /**
     * add configs. Notice: Function overwrites current config if it exists!
     *
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        self::$config = $config;
    }

    /**
     * add configs to existing configs, or params to existing configs. Notice: Function does not edit current configs!
     *
     * @param $key
     * @param array $value
     */
    public static function addConfig($key, array $value)
    {
        if (array_key_exists($key, self::$config)) {
            $diff_array = array_intersect_key(self::$config[$key], $value);
            if (!count($diff_array)) {
                self::$config = self::$config[$key] + $value;
            } else {
                throw new ConfigParamExistException('Config parameter(s) "' . self::getStringKeys($diff_array) . '" already exist in ' . $key);
            }
        } else {
            self::$config[$key] = $value;
        }

    }

    /**
     * divides array to params
     *
     * @param array $data
     * @return string
     */
    private static function getStringKeys(array $data): string
    {
        return implode(', ', array_keys($data));
    }

}