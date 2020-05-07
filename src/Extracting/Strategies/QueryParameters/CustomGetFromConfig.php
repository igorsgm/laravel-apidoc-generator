<?php

namespace Mpociot\ApiDoc\Extracting\Strategies\QueryParameters;

use Illuminate\Routing\Route;
use Mpociot\ApiDoc\Extracting\ParamHelpers;
use Mpociot\ApiDoc\Extracting\Strategies\Strategy;
use ReflectionClass;
use ReflectionMethod;

class CustomGetFromConfig extends Strategy
{
    use ParamHelpers;

    /**
     * @var array
     */
    protected $middlewares;

    /**
     * @var array
     */
    protected $queryParams;

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
        $this->queryParams = $this->config->get('postman.apply.queryParams') ?? [];

        $this->middlewares = $route->middleware();

        if ($this->isToEnableToken($route)) {
            $this->queryParams['token']['disabled'] = false;
        }

        $this->addCloudBotParams()
            ->addBroadcasterParams()
            ->addIdentifierParams()
            ->renameNeededQueryParameters($route);

        if (in_array('printful-webhook', $this->middlewares)) {
            $this->queryParams['key'] = $this->makeQueryParamArray('{{PRINTFUL_SL_API_KEY}}');
        }

        return $this->queryParams;
    }

    /**
     * Check if route should have the token query parameter enabled
     *
     * @param Route $route
     * @return bool
     */
    public function isToEnableToken($route)
    {
        $urisToEnable = [
            'service/get-socket-token',
            'embed/chat',
            'api/donations',
            'api/v1.0/donation-page/settings',
            'api/v1.0/widgets/event-list/settings',
            'api/v1.0/widgets/alert-box/media-sharing/settings',
            'api/v1.0/widgets/tip-jar/settings'
        ];

        return in_array('auth.global', $this->middlewares) || in_array($route->uri(), $urisToEnable);
    }


    /**
     * Adding query parameters related to CloudBot
     * @return $this
     */
    public function addCloudBotParams()
    {
        if (in_array('auth.cloudbot.mediashare', $this->middlewares)) {
            $this->queryParams['key'] = $this->makeQueryParamArray(env('CLOUDBOT_MEDIASHARE_SECRET'));
        }

        if (in_array('cloudbot-internal-api', $this->middlewares)) {
            $this->queryParams['key'] = $this->makeQueryParamArray('{{CLOUDBOT_API_SECRET_KEY}}');
        }

        return $this;
    }

    /**
     * Adding query parameters related to Broadcast
     * @return $this
     */
    public function addBroadcasterParams()
    {
        $broadcasterParam = $this->makeQueryParamArray('{{PARAM_BROADCASTER}}');

        if (in_array('auth.viewer.api', $this->middlewares)) {
            $this->queryParams['broadcaster'] = $broadcasterParam;
            $this->queryParams['platform'] = $this->makeQueryParamArray('{{PARAM_PLATFORM_ACCOUNT}}');
        }

        if (in_array('facemask.checkout', $this->middlewares)) {
            $this->queryParams['broadcaster'] = $broadcasterParam;
        }

        return $this;
    }

    /**
     * Adding query parameters related to Identifier Parameters
     * @return $this
     */
    public function addIdentifierParams()
    {
        $identifierParam = $this->makeQueryParamArray(1);

        if (in_array('user.identifier', $this->middlewares)) {
            $this->queryParams['streamerId'] = $identifierParam;
        }

        if (in_array('user.identifier:fromUserId,fromUser', $this->middlewares)) {
            $this->queryParams['fromUserId'] = $identifierParam;
        }

        if (in_array('user.identifier:toUserId,toUser', $this->middlewares)) {
            $this->queryParams['toUserId'] = $identifierParam;
        }

        return $this;
    }

    /**
     * Rename some specific route parameters
     *
     * @param Route $route
     * @return $this
     */
    public function renameNeededQueryParameters($route)
    {
        if ($route->uri() == 'api/donations') {
            $this->queryParams['access_token'] = $this->queryParams['token'];
            unset($this->queryParams['token']);
        }

        if ($route->uri() == 'api/v5/rst/user/{id}/targets') {
            $this->queryParams['id'] = $this->makeQueryParamArray('{{LARAVEL_USER_ID}}');
        }

        return $this;
    }
}
