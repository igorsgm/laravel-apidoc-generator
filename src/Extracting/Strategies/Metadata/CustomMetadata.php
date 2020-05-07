<?php

namespace Mpociot\ApiDoc\Extracting\Strategies\Metadata;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
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
        $this->routeMiddlewares = $route->middleware();
        return [
            'description' => $this->getLongDescription($route),
            'auth' => $this->getCustomAuth($route)
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

        if (!empty($this->routeMiddlewares)) {
            $descriptionArray['middlewares'] = $this->routeMiddlewares;
            $descriptionArray['role'] = $this->treatMiddlewares($this->routeMiddlewares, 'role');
            $descriptionArray[$this->routesGroup['permission_middleware']] = $this->treatMiddlewares($this->routeMiddlewares, $this->routesGroup['permission_middleware']);
        }

        return json_encode($descriptionArray);
    }

    /**
     * Return a list of permission middlewares
     *
     * @param array $middlewaresList
     * @param string $middlewareType
     * @return array
     */
    protected function treatMiddlewares($middlewaresList, $middlewareType)
    {
        $permissionStr = $middlewareType . ':';

        foreach ($middlewaresList as $key => $middleware) {
            if (Str::contains($middleware, $middlewareType)) {

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

    /**
     * Get custom routes based on specific middlewares
     *
     * @param Route $route
     * @return array
     */
    protected function getCustomAuth($route) {
        if (in_array('jwt-auth', $this->routeMiddlewares)) {
            return $this->makePostmanAuthArray('bearer', '{{OVERLAYS_JWT_TOKEN}}');
        }

        if (in_array('twitch-extensions-auth', $this->routeMiddlewares)) {
            return $this->makePostmanAuthArray('bearer', '{{EXTENSIONS_JWT_TOKEN}}');
        }

        if (in_array('user.token', $this->routeMiddlewares) || in_array('user.token:true', $this->routeMiddlewares)) {
            return $this->makePostmanAuthArray('bearer', '{{SLOBS_OAUTH_TOKEN}}');
        }

        if (in_array('restream-token', $this->routeMiddlewares)) {
            return $this->makePostmanAuthArray('bearer', '{{RESTREAM_API_TOKEN}}');
        }
    }
}
