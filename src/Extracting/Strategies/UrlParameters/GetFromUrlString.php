<?php

namespace Mpociot\ApiDoc\Extracting\Strategies\UrlParameters;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Mpociot\ApiDoc\Extracting\ParamHelpers;
use Mpociot\ApiDoc\Extracting\Strategies\Strategy;
use ReflectionClass;
use ReflectionMethod;

class GetFromUrlString extends Strategy
{
    use ParamHelpers;

    public function __invoke(Route $route, ReflectionClass $controller, ReflectionMethod $method, array $routeRules, array $context = [])
    {
        preg_match_all('/\{(.*?)\}/', $route->uri(), $routeParams);
        $routeParams = collect($routeParams[1]);

        if (!$routeParams->isEmpty()) {
            $urlParamsMap = collect($this->config->get('postman.urlParamsMap'))
                ->keyBy(function ($item, $key) {
                    return $this->treatParamKeyName($key);
                });

            $routeParams = $routeParams->filter(function ($item) use ($urlParamsMap) {
                $item = $this->treatParamKeyName($item);
                return $urlParamsMap->has($item);
            })
                ->transform(function ($item) use ($urlParamsMap) {
                    $item = str_replace('?', '', $item);
                    return [
                        'key' => $item,
                        'value' => $urlParamsMap->get($this->treatParamKeyName($item)),
                    ];
                })->keyBy(function ($item) {
                    return $item['key'];
                });
        }

        return $routeParams->toArray();
    }

    private function treatParamKeyName($name)
    {
        $name = str_replace('?', '', $name);
        return strtolower(Str::camel($name));
    }
}
