<?php

namespace Litovka\Framework\Router;

use Litovka\Framework\Config\Config;
use Litovka\Framework\Router\Exceptions\{
    RouteKeyNotPassedException,
    IncorrectParamException,
    InvalidRouteNameException
};
use Litovka\Framework\Helpers\ControllerFactory;

/**
 * Class Router
 * @package Litovka\Framework\Router
 */
class Router
{

    const DEFAULT_REQUEST_METHOD = "GET";
    const DEFAULT_VAR_REGEXP = "[^\/]+";
    const DEFAULT_URI = '/';

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * MyRouter constructor.
     * @param array $config_route
     */
    public function __construct(array $config_route)
    {

        foreach ($config_route as $name => $params) {

            $existed_vars = $this->getExistedVars($params);

            $this->routes[$name] = [
                'pattern' => $this->getDefaultParam('pattern', $params),
                'method' => $this->checkRouteParam('method', $params) ? $params['method'] : $this->getDefaultMethod(),
                'controller' => $this->getControllerParam($params),
                "action" => $this->getDefaultParam('action', $params),
                "vars" => $existed_vars,
                "regexp" => $this->getRegexpFromRoute($params, $existed_vars)
            ];
        }
    }

    /**
     * Generates regexp strings according to the $existed_variables
     *
     * @param array $config_route
     * @param array $existed_variables
     * @return string
     */
    private function getRegexpFromRoute(array $config_route, array $existed_variables): string
    {
        $this->checkRouteParam("pattern", $config_route, true);
        $result = str_replace("/", "\/", $config_route["pattern"]);


        if (isset($config_route["params"])) {
            $vars_conf = $config_route["params"];

            for ($i = 0; $i < count($existed_variables); $i++) {
                $var_reg = "(" .
                    (array_key_exists($existed_variables[$i], $vars_conf) ? $vars_conf[$existed_variables[$i]] : self::DEFAULT_VAR_REGEXP)
                    . ")";
                $result = str_replace("{" . $existed_variables[$i] . "}", $var_reg, $result);
            }
        }

        return "/^" . $result . "$/";

    }

    /**
     * Generates array of results for parameters which match to a pattern
     *
     * @param array $config_route
     * @return array
     */
    private function getExistedVars(array $config_route): array
    {
        $this->checkRouteParam("pattern", $config_route, true);
        preg_match_all("/{\w+}/U", $config_route["pattern"], $variables);

        $res = [];

        if (!empty($variables[0])) {
            $res = array_map(function ($value) {
                return substr($value, 1, strlen($value) - 2);
            }, $variables[0]);
        }

        return $res;
    }

    /**
     * Get Controller name from config, checking existing and empty value
     *
     * @param array $config_route
     * @return string
     */
    private function getControllerParam(array $config_route): string
    {
        $this->checkRouteParam('controller', $config_route, true);
        return $this->getControllerName($config_route);
    }

    /**
     * Get any param from config, checking existing and empty value
     *
     * @param string $key
     * @param array $config_route
     * @return string
     */
    private function getDefaultParam(string $key, array $config_route): string
    {
        $this->checkRouteParam($key, $config_route, true);
        return $config_route[$key];
    }

    /**
     * Checks a route parameter whether it exist or empty
     *
     * @param string $key
     * @param array $config_route
     * @param bool $e
     * @return bool
     * @throws IncorrectParamException
     */
    private function checkRouteParam(string $key, array $config_route, $e = false): bool
    {
        if (!array_key_exists($key, $config_route) || empty($config_route[$key])) {
            if ($e) {
                throw new IncorrectParamException('incorrect param in route config for ' . "\"$key\"");
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Get default request method step by step: config -> const
     *
     * @return string
     */
    private function getDefaultMethod(): string
    {
        return !empty(Config::getConfig('consts')['default_request_method']) ? Config::getConfig('consts')['default_request_method'] : self::DEFAULT_REQUEST_METHOD;
    }

    /**
     * Get Controller name using Factory
     *
     * @param array $config_route
     * @return string
     */
    private function getControllerName(array $config_route): string
    {
        $controller_name = ControllerFactory::create($config_route["controller"], "Controller");
        return $controller_name;
    }

    /**
     * Build link
     *
     * @param string $route_name
     * @param array $params
     * @return string
     * @throws RouteKeyNotPassedException
     * @throws InvalidRouteNameException
     */
    public function getLink(string $route_name, array $params = []): string
    {
        if (array_key_exists($route_name, $this->routes)) {

            $this->checkRouteParam('pattern', $this->routes[$route_name]);
            preg_match_all("/\{([\w\d_]+)\}/", $link = $this->routes[$route_name]['pattern'], $keys);

            foreach ($keys[1] as $key) {
                if (!array_key_exists($key, $params)) {
                    throw new RouteKeyNotPassedException("Key \"$key\" is required for route \"$route_name\"");
                } else {
                    $link = str_replace("{" . $key . "}", $params[$key], $link);
                }
            }

            //@TODO add a full link using request parameters in request-commit

            return $link;
        } else {
            throw new InvalidRouteNameException("Route with name \"$route_name\" was not found in config");
        }
    }

    /**
     * @param $request
     */
    public function getRoute($request)
    {
        //@TODO add getting of a route via Request in request-commit
    }

}