<?php

namespace Mpociot\ApiDoc\Extracting\Strategies\QueryParameters;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Mpociot\ApiDoc\Extracting\ParamHelpers;
use Mpociot\ApiDoc\Extracting\Strategies\Strategy;
use Mpociot\Reflection\DocBlock;
use Mpociot\Reflection\DocBlock\Tag;
use ReflectionClass;
use ReflectionMethod;

class CustomGetFromConfig extends Strategy
{
    use ParamHelpers;

    /**
     * @param Route $route
     * @param ReflectionClass $controller
     * @param ReflectionMethod $method
     * @param array $routeRules
     * @param array $context
     * @return array
     */
    public function __invoke(Route $route, ReflectionClass $controller, ReflectionMethod $method, array $routeRules, array $context = [])
    {
        $queryParams = $this->config->get('postman.apply.queryParams');

        if (!empty($queryParams)) {
            return $queryParams;
        }
    }
}
