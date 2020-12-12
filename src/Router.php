<?php

namespace Dburiy\Router;

use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Router
 * @package Dburiy\Router
 */
class Router
{
    private $routes = [];
    private $client;
    private $settings;

    /**
     * Router constructor
     *
     * @param array $config
     *
     * Available settings
     *   urlParamsFromOptions - (bool) get url params from options['urlParams'], default FALSE
     */
    public function __construct(array $config = [])
    {
        $this->client = new Client($config);
        $this->settings = $config;
    }

    /**
     * Map route
     *
     * @param string $method
     * @param string $name
     * @param string $path
     * @param array $arguments
     * @return Router
     */
    public function map(string $method, string $name, string $path, array $arguments = []): Router
    {
        $this->routes[$name] = [
            'method' => $method,
            'path' => $path,
            'arguments' => $arguments
        ];

        return $this;
    }

    /**
     * Has route
     *
     * @param string $name
     * @return bool
     */
    public function hasRoute(string $name): bool
    {
        return isset($this->routes[$name]);
    }

    /**
     * Remove route
     *
     * @param string $name
     */
    public function removeRoute(string $name)
    {
        if (isset($this->routes[$name])) {
            unset($this->routes[$name]);
        }
    }

    /**
     * Get route by name
     *
     * @param string $name
     * @return array
     */
    public function getRoute(string $name): array
    {
        return $this->routes[$name] ?? [];
    }

    /**
     * Get all routes
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Remove all routes
     */
    public function cleanRoutes()
    {
        $this->routes = [];
    }

    /**
     * Map get request
     *
     * @param string $name
     * @param string $path
     * @param array $arguments
     * @return $this
     */
    public function get(string $name, string $path, array $arguments = []): Router
    {
        $this->routes[$name] = [
            'method' => 'get',
            'path' => $path,
            'arguments' => $arguments
        ];

        return $this;
    }

    /**
     * Map post request
     *
     * @param string $name
     * @param string $path
     * @param array $arguments
     * @return $this
     */
    public function post(string $name, string $path, array $arguments = []): Router
    {
        $this->routes[$name] = [
            'method' => 'post',
            'path' => $path,
            'arguments' => $arguments
        ];

        return $this;
    }

    /**
     * Call router by name
     *
     * @param string $name
     * @param array $params
     * @param array $options
     * @return ResponseInterface
     * @throws Exception
     */
    public function call(string $name, array $params = [], array $options = []): ResponseInterface
    {
        if (empty($this->routes[$name])) {
            throw new Exception('route ['.$name.'] not found');
        }
        $request = $this->routes[$name];
        $method = strtolower($request['method']);
        $params = $params ? array_merge($request['arguments'], $params) : $request['arguments'];
        if (preg_match_all('~\{([^\}]+)\}~', $request['path'], $m)) {
            $isUrlParamsFromOptions = (bool) ($this->settings['urlParamsFromOptions'] ?? false);
            $urlParams = $isUrlParamsFromOptions
                ? ($options['urlParams'] ?? [])
                : $params
            ;
            foreach ($m[1] as $param) {
                if (!isset($params[$param])) {
                    throw new Exception("required param '{$param}' not found");
                }
                $request['path'] = str_replace('{'.$param.'}', $urlParams[$param], $request['path']);
                if (!$isUrlParamsFromOptions) {
                    unset($params[$param]);
                }
            }
        }
        $options[$method == 'post' ? 'form_params' : 'query'] = $params;

        return $this->client->request($method, $request['path'], $options);
    }

    /**
     * Get guzzle client
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
