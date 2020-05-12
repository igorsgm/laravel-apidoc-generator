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
            'auth' => $this->getCustomAuth($route),
            'event' => $this->getCustomEvent($route)
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

    /**
     * Get custom test for routes
     * It's particularly useful to skip some postman requests while testing
     *
     * @param Route $route
     * @return array
     */
    protected function getCustomEvent($route)
    {
        $nextRequest = "postman.setNextRequest('%s');";

        $routesTest = [
            'loginsuccess' => sprintf($nextRequest, 'usingtwitchalerts/log'), // To skip the two '/dashboard/act-as' routes
            'oauth/apps/{clientId}' => sprintf($nextRequest, 'auth'), // To skip 'logout'
            'ideas/login' => sprintf($nextRequest, 'insided (Handle insided login)'), // To skip 'ideas/logout'
            'insided' => sprintf($nextRequest, 'api/v1.0/authorize'), // To skip 'insided/logout'
            'api/v5/slobs/user/notification/read' => sprintf($nextRequest, 'api/v5.1/user/{name}'), // To skip 'api/v5.1/user/logout'
            'api/v5/user/pro/subscription/cancel' => sprintf($nextRequest, '/api/v5/settings/uimode') // To skip 'api/v5/settings/revoketoken'
        ];

        $uri = $route->uri();
        if (array_key_exists($uri, $routesTest)) {
            return [
                $this->makeEventTestArray('test', [$routesTest[$uri]])
            ];
        }

        return [];
    }
}
