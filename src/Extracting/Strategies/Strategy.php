<?php

namespace Mpociot\ApiDoc\Extracting\Strategies;

use Illuminate\Routing\Route;
use Mpociot\ApiDoc\Tools\DocumentationConfig;
use Ramsey\Uuid\Uuid;
use ReflectionClass;
use ReflectionMethod;

abstract class Strategy
{
    /**
     * @var DocumentationConfig The apidoc config
     */
    protected $config;

    /**
     * @var string The current stage of route processing
     */
    protected $stage;

    public function __construct(string $stage, DocumentationConfig $config)
    {
        $this->stage = $stage;
        $this->config = $config;
    }

    /**
     * @param Route $route
     * @param ReflectionClass $controller
     * @param ReflectionMethod $method
     * @param array $routeRules Array of rules for the ruleset which this route belongs to.
     * @param array $context Results from the previous stages
     *
     * @throws \Exception
     *
     * @return array
     */
    abstract public function __invoke(Route $route, ReflectionClass $controller, ReflectionMethod $method, array $routeRules, array $context = []);

    /**
     * @param string $type
     * @param string $value
     * @return array
     */
    public function makePostmanAuthArray($type, $value)
    {
        return [
            "type" => $type,
            "bearer" => [
                [
                    "key" => "token",
                    "value" => $value,
                    "type" => "string"
                ]
            ]
        ];
    }

    /**
     * @param string|integer $value
     * @param bool $disabled
     * @return array
     */
    public function makeQueryParamArray($value, $disabled = false)
    {
        return [
            'value' => $value,
            'disabled' => $disabled
        ];
    }

    /**
     * @param string $listenType    'test' or 'prerequest'
     * @param array $testsArray
     * @return array
     */
    public function makeEventTestArray($listenType, $testsArray)
    {
        return [
            'listen' => $listenType,
            'script' => [
                'id' => Uuid::uuid4()->toString(),
                'exec' => $testsArray,
                'type' => 'text/javascript'
            ]
        ];
    }
}
