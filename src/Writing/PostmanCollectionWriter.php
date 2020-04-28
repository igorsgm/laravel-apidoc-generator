<?php

namespace Mpociot\ApiDoc\Writing;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mpociot\ApiDoc\Tools\Utils;
use Ramsey\Uuid\Uuid;
use ReflectionMethod;

class PostmanCollectionWriter
{
    /**
     * @var Collection
     */
    private $routeGroups;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var array|null
     */
    private $auth;

    /**
     * CollectionWriter constructor.
     *
     * @param Collection $routeGroups
     */
    public function __construct(Collection $routeGroups, $baseUrl)
    {
        $this->routeGroups = $routeGroups;
        $this->protocol = $this->getProtocol($baseUrl);
        $this->baseUrl = $this->getBaseUrl($baseUrl);
        $this->auth = config('apidoc.postman.auth');
        $this->event = config('apidoc.postman.event');
        $this->apply = config('apidoc.postman.apply');
        $this->routeGroupSettings = config('apidoc.routes.0.group');
    }

    public function getCollection()
    {
        $collection = [
            'variables' => [],
            'info' => [
                'name' => config('apidoc.postman.name') ?: config('app.name') . ' API',
                '_postman_id' => Uuid::uuid4()->toString(),
                'description' => config('apidoc.postman.description') ?: '',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => $this->routeGroups->map(function (Collection $routes, $groupName) {
                return $this->makeRouteGroup($routes, $groupName);
            })->values()->toArray(),
        ];

        if (!empty($this->auth)) {
            $collection['auth'] = $this->auth;
        }

        if (!empty($this->event)) {
            $collection['event'] = $this->event;
        }

        return json_encode($collection, JSON_PRETTY_PRINT);
    }

    /**
     * @param Collection $routes
     * @param $groupName
     * @return array
     */
    public function makeRouteGroup(Collection $routes, $groupName)
    {
        $firstRoute = $routes->first();
        $group = [
            'name' => $groupName,
            'description' => is_array($firstRoute) ? $firstRoute['metadata']['groupDescription'] : $routes->keys()->first(),
            'item' => $routes->map(function ($route) use ($groupName) {
                $groupName = is_array($route) ? $groupName : $route->first()['metadata']['groupName'];
                return $this->generateEndpointItem($route, $groupName);
            })->values()->toArray(),
        ];

        if (!empty($this->routeGroupSettings['events_group_map'][$groupName])) {
            $group['event'] = $this->routeGroupSettings['events_group_map'][$groupName];
        }

        return $group;
    }

    protected function generateEndpointItem($route, $groupName = null)
    {
        // Recursive function
        if (!is_array($route)) {
            return $this->makeRouteGroup($route, $groupName);
        }

        $mode = 'raw';

        $method = $route['methods'][0];

        return [
            'name' => $this->getRequestName($route),
            'request' => [
                'method' => $method,
                'header' => $this->resolveHeadersForRoute($route),
                'url' => $this->makeUrlData($route),
                'body' => [
                    'mode' => $mode,
                    $mode => json_encode($route['cleanBodyParameters'], JSON_PRETTY_PRINT),
                ],
                'description' => $route['metadata']['description'] ?? null,
                'response' => [],
            ],
        ];
    }

    /**
     * Retrieves the name of the request.
     *
     * @param array $route
     * @return string
     */
    protected function getRequestName($route)
    {
        // return $route['metadata']['title'] != '' ? $route['metadata']['title'] : $route['uri'];
        $name = $route['uri'];

        if ($titleDesc = $route['metadata']['title']) {
            $name .= ' (' . $titleDesc . ')';
        }

        return $name;
    }

    protected function resolveHeadersForRoute($route)
    {
        $headers = collect($route['headers']);

        // Exclude authentication headers if they're handled by Postman auth
        $authHeader = $this->getAuthHeader();
        if (! empty($authHeader)) {
            $headers = $headers->except($authHeader);
        }

        if (!empty($this->apply['headers'])) {
            $headers = $headers->union($this->apply['headers']);
        }

        return $headers
            ->union([
                'Accept' => 'application/json',
            ])
            ->map(function ($value, $header) {
                return [
                    'key' => $header,
                    'value' => $value,
                ];
            })
            ->values()
            ->all();
    }

    protected function makeUrlData($route)
    {
        // URL Parameters are collected by the `UrlParameters` strategies, but only make sense if they're in the route
        // definition. Filter out any URL parameters that don't appear in the URL.
        $urlParams = collect($route['urlParameters'])->filter(function ($_, $key) use ($route) {
            // return Str::contains($route['uri'], '{' . $key . '}');
            return preg_match('/\{\??' . $key . '\??\}/', $route['uri']);
        });

        /** @var Collection $queryParams */
        $base = [
            'protocol' => $this->protocol,
            'host' => $this->baseUrl,
            // Substitute laravel/symfony query params ({example}) to Postman style, prefixed with a colon
            'path' => preg_replace_callback('/\/{(\w+)\??}(?=\/|$)/', function ($matches) {
                return '/:' . $matches[1];
            }, $route['uri']),
            'query' => collect($route['queryParameters'])->map(function ($parameter, $key) {
                $param = [
                    'key' => $key,
                    'value' => $this->treatParamValueName($parameter['value']),
                    // Default query params to disabled if they aren't required and have empty values
                    'disabled' => $parameter['disabled'] || (!$parameter['required'] && empty($parameter['value'])),
                ];

                if (!empty($parameter['description'])) {
                    $param['description'] = $parameter['description'];
                }

                return $param;
            })->values()->toArray(),
        ];

        // If there aren't any url parameters described then return what we've got
        /** @var $urlParams Collection */
        if ($urlParams->isEmpty()) {
            return $base;
        }

        $base['variable'] = $urlParams->map(function ($parameter, $key) {
            $variable = [
                'key' => $key,
                'value' => $this->treatParamValueName($parameter['value']),
            ];

            if (!empty($parameter['description'])) {
                $variable['description'] = $parameter['description'];
            }

            return $variable;
        })->values()->toArray();

        return $base;
    }

    /**
     * Treats the parameter name considering Postman variables (that starts with "{{")
     *
     * @param string $parameterName
     * @return string
     */
    protected function treatParamValueName($parameterName)
    {
        return Str::startsWith($parameterName, '{{') ? $parameterName : urlencode($parameterName);
    }

    protected function getAuthHeader()
    {
        $auth = $this->auth;
        if (empty($auth) || ! is_string($auth['type'] ?? null)) {
            return null;
        }

        switch ($auth['type']) {
            case 'bearer':
                return 'Authorization';
            case 'apikey':
                $spec = $auth['apikey'];

                if (isset($spec['in']) && $spec['in'] !== 'header') {
                    return null;
                }

                return $spec['key'];
            default:
                return null;
        }
    }

    protected function getBaseUrl($baseUrl)
    {
        if (Str::contains(app()->version(), 'Lumen')) { //Is Lumen
            $reflectionMethod = new ReflectionMethod(\Laravel\Lumen\Routing\UrlGenerator::class, 'getRootUrl');
            $reflectionMethod->setAccessible(true);
            $url = app('url');

            return $reflectionMethod->invokeArgs($url, ['', $baseUrl]);
        }

        return Utils::urlFormatRoot('', $baseUrl);
    }

    protected function getProtocol($baseUrl)
    {
        if (Str::startsWith($baseUrl, '{{')) {
            return '';
        }

        Str::startsWith($baseUrl, 'https') ? 'https' : 'http';
    }
}
