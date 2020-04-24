<?php

namespace Mpociot\ApiDoc\Extracting\Strategies\Metadata;

use Illuminate\Routing\Route;
use Mpociot\ApiDoc\Extracting\Strategies\Strategy;
use ReflectionClass;
use ReflectionMethod;

class CustomMetadata extends Strategy
{
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
        return [
            'description' => $this->getLongDescription($route)
        ];
    }

    /**
     * Building long description to display the Action and Middlewares
     * @param Route $route
     * @return string
     */
    protected function getLongDescription($route)
    {
        $description = "**Action:** `" . $route->getActionName('uses') . "`";

        if (!empty($route->middleware())) {
            $description .= "\n\n**Middlewares:** `" . implode(' | ', $route->middleware()) . "`";
        }

        return $description;
    }
}
