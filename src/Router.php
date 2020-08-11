<?php

namespace Dburiy\Router;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class Router
{
    private $routes = [];
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Map route
     *
     * @param string $method
     * @param string $name
     * @param string $path
     * @param array $arguments
     *
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
     * Call router by name
     *
     * @param string $name
     * @param array $params
     * @param array $options
     *
     * @return Response
     * @throws GuzzleException
     */
    public function call(string $name, array $params = [], array $options = []): ResponseInterface
    {
        if (empty($this->routes[$name])) {
            throw new \Exception('route ['.$name.'] not found');
        }
        $request = $this->routes[$name];
        $method = strtolower($request['method']);
        $params = $params ? array_merge($request['arguments'], $params) : $request['arguments'];
        if ($params) {
            $options[$method == 'post' ? 'form_params' : 'query'] = $params;
        }
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
