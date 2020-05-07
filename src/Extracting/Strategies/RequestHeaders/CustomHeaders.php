<?php

namespace Mpociot\ApiDoc\Extracting\Strategies\RequestHeaders;

use Illuminate\Routing\Route;
use Mpociot\ApiDoc\Extracting\Strategies\Strategy;
use ReflectionClass;
use ReflectionMethod;

class CustomHeaders extends Strategy
{
    public function __invoke(Route $route, ReflectionClass $controller, ReflectionMethod $method, array $routeRules, array $context = [])
    {
        $headersToApply = $this->config->get('postman.apply.headers');

        if (!empty($headersToApply)) {
            return $headersToApply;
        }

        return [];
    }
}
