<?php

namespace Mpociot\ApiDoc\Extracting\Strategies\Metadata;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;
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
    public function __invoke(Route $route, ReflectionClass $controller, ReflectionMethod $method, array $routeRules, array $context = []) {
        $this->routesGroup = $this->config->get('routes.0.group');
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
        $descriptionArray = ['action' => $route->getActionName('uses')];
        $middlewares = $route->middleware();

        if (!empty($middlewares)) {
            $descriptionArray['middlewares'] = $middlewares;
            $descriptionArray[$this->routesGroup['permission_middleware']] = $this->treatMiddlewares($middlewares);
        }

        return json_encode($descriptionArray, JSON_PRETTY_PRINT);
    }

    /**
     * Return a list of permission middlewares
     *
     * @param array $middlewares
     * @return array
     */
    protected function treatPermissionMiddlewares($middlewares)
    {
        $permissionStr = $this->routesGroup['permission_middleware'] . ':';

        foreach ($middlewares as $key => $middleware) {
            if (Str::contains($middleware, $this->routesGroup['permission_middleware'])) {

                if (Str::contains($middleware, ',')) {
                    $permissionMiddlewares = explode(',', $middleware);
                    $permissionMiddlewares = collect($permissionMiddlewares)
                        ->transform(function ($item) use ($permissionStr) {
                            return str_replace($permissionStr, '', $item);
                        })->toArray();
                } else {
                    $permissionMiddlewares = [str_replace($permissionStr, '', $middleware)];
                }

                return $permissionMiddlewares;
            }
        }

        return [];
    }

}
