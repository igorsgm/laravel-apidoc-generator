<?php

namespace Mpociot\ApiDoc\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Mpociot\ApiDoc\Extracting\Generator;
use Mpociot\ApiDoc\Matching\RouteMatcher\Match;
use Mpociot\ApiDoc\Matching\RouteMatcherInterface;
use Mpociot\ApiDoc\Tools\DocumentationConfig;
use Mpociot\ApiDoc\Tools\Flags;
use Mpociot\ApiDoc\Tools\Utils;
use Mpociot\ApiDoc\Writing\Writer;
use Mpociot\Reflection\DocBlock;
use ReflectionClass;
use ReflectionException;

class GenerateDocumentation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apidoc:generate
                            {--force : Force rewriting of existing routes}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate your API documentation from existing Laravel routes.';

    /**
     * @var DocumentationConfig
     */
    private $docConfig;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    protected $routesGroup = [];

    /**
     * Execute the console command.
     *
     * @param RouteMatcherInterface $routeMatcher
     *
     * @return void
     */
    public function handle(RouteMatcherInterface $routeMatcher)
    {
        // Using a global static variable here, so fuck off if you don't like it.
        // Also, the --verbose option is included with all Artisan commands.
        Flags::$shouldBeVerbose = $this->option('verbose');

        $this->docConfig = new DocumentationConfig(config('apidoc'));
        $this->baseUrl = $this->docConfig->get('base_url') ?? config('app.url');
        $this->routesGroup = $this->docConfig->get('routes.0.group');

        URL::forceRootUrl($this->baseUrl);

        $routes = $routeMatcher->getRoutes($this->docConfig->get('routes'), $this->docConfig->get('router'));

        $generator = new Generator($this->docConfig);
        $parsedRoutes = $this->processRoutes($generator, $routes);
        $groupedRoutes = $this->groupRoutes($parsedRoutes);

        $writer = new Writer(
            $this,
            $this->docConfig,
            $this->option('force')
        );
        $writer->writeDocs($groupedRoutes);
    }

    /**
     * @param array $parsedRoutes
     */
    public function groupRoutes($parsedRoutes)
    {
        $parsedRoutes = collect($parsedRoutes);

        $sortByFunc = static function ($group) {
            /* @var $group Collection */
            return $group->first()['metadata']['groupName'];
        };

        $groupBy = 'metadata.' . ($this->routesGroup['type'] == 'permission' ? 'permissionGroupName' : 'groupName');
        $groupedRoutes = $parsedRoutes->groupBy($groupBy);

        // Controller grouping
        if ($this->routesGroup['type'] !== 'permission') {
            return $groupedRoutes->sortBy($sortByFunc, SORT_NATURAL);
        }

        $permissionsMapKeys = array_keys($this->routesGroup['permissions_map']);

        return $groupedRoutes->transform(function ($item, $key) use ($sortByFunc) {
            return $item->groupBy('metadata.groupName')->sortBy($sortByFunc, SORT_NATURAL);
        })->sortBy(function ($item, $key) {
            return $key;
        })->keyBy(function ($item, $key) use ($permissionsMapKeys) {
            return in_array($key, $permissionsMapKeys) ? $this->routesGroup['permissions_map'][$key] : $key;
        });
    }

    /**
     * Retrieve the routes group name, based on Controller name.
     *
     * @param Route $route
     * @return string
     */
    private function getRoutesControllerGroupName(Route $route)
    {
        if ($route->isClosure) {
            return 'Closures';
        }

        $controllerName = Str::parseCallback($route->getAction()['uses'], null)[0];
        $controllerName = class_basename($controllerName);
        return str_replace('Controller', '', $controllerName);
    }

    /**
     * Retrieve the routes group names, based on permission middleware names.
     *
     * @param Route $route
     * @return array
     */
    private function getRoutesPermissionGroupNames(Route $route)
    {
        if ($this->routesGroup['type'] !== 'permission') {
            return null;
        }

        $middlewares = $route->middleware();
        $permissions = [];

        foreach ($middlewares as $middleware) {
            if (str_contains($middleware, $this->routesGroup['permission_middleware'])) {
                $permissions = substr($middleware, strpos($middleware, ":") + 1);
                $permissions = explode(',', $permissions);
            }
        }

        // Ignoring 'any' middleware, to be considered as "Everyone"
        $permissions = collect($permissions)->reject(function ($permission) {
            return $permission == 'any';
        });

        if ($permissions->isEmpty()) {
            $permissions->push('Everyone');
        }

        return $permissions->toArray();
    }

    /**
     * @param \Mpociot\ApiDoc\Extracting\Generator $generator
     * @param Match[] $routes
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    private function processRoutes(Generator $generator, array $routes)
    {
        $parsedRoutes = [];
        $processedRoutesCount = 0;
        $skipTemplate = [
            'count' => 0,
            'list' => []
        ];

        $skippedRoutes = [
            'closure' => array_merge($skipTemplate, ['message' => 'Closure routes']),
            'invalid' => array_merge($skipTemplate, ['message' => 'Invalid routes']),
            'controller_method' => array_merge($skipTemplate, ['message' => 'Controller methods don\'t exist']),
            'hide' => array_merge($skipTemplate, ['message' => '@hideFromAPIDocumentation specified']),
            'exception' => array_merge($skipTemplate, ['message' => 'Exceptions.']),
        ];

        foreach ($routes as $routeItem) {
            /** @var Route $route */
            $route = $routeItem->getRoute();
            $route->isClosure = $this->isClosureRoute($route->getAction());
            $routeControllerAndMethod = Utils::getRouteClassAndMethodNames($route->getAction());

            if ($route->isClosure) {
                $a = 1;
            }

            try {
                if ($this->isValidRoute($route, $routeControllerAndMethod) &&
                    ($route->isClosure || $this->isRouteVisibleForDocumentation($routeControllerAndMethod))
                ) {
                    $parsedRoute = $generator->processRoute($route, $routeItem['apply'] ?? []);
                    $parsedRoute['metadata']['groupName'] = $this->getRoutesControllerGroupName($route);
                    $parsedRoute['metadata']['permissionGroupName'] = $this->getRoutesPermissionGroupNames($route);

                    $parsedRoutes[] = $parsedRoute;
                    $processedRoutesCount++;
                }
            } catch (\Exception $e) {

                $messageFormat = '%s route: [%s] %s';
                $routeMethods = implode(',', $generator->getMethods($route));
                $routePath = $generator->getUri($route);

                if ($this->isClosureRoute($route->getAction())) {
                    $message = sprintf($messageFormat, 'Skipping', $routeMethods, $routePath);
                    $this->warn($message . ': Closure routes are not supported.');
                    $skippedRoutes['closure']['count']++;
                    $skippedRoutes['closure']['list'][] = $message;
                    continue;
                }

                if (!$this->isValidRoute($route, $routeControllerAndMethod)) {
                    $message = sprintf($messageFormat, 'Skipping invalid', $routeMethods, $routePath);
                    $this->warn($message);
                    $skippedRoutes['invalid']['count']++;
                    $skippedRoutes['invalid']['list'][] = $message;
                    continue;
                }

                if (!$this->doesControllerMethodExist($routeControllerAndMethod)) {
                    $message = sprintf($messageFormat, 'Skipping', $routeMethods, $routePath) . ': ' . $route->getAction()['uses'];
                    $this->warn($message . ' - Controller method does not exist.');
                    $skippedRoutes['controller_method']['count']++;
                    $skippedRoutes['controller_method']['list'][] = $message;
                    continue;
                }

                if (!$this->isRouteVisibleForDocumentation($routeControllerAndMethod)) {
                    $message = sprintf($messageFormat, 'Skipping', $routeMethods, $routePath);
                    $this->warn($message . ': @hideFromAPIDocumentation was specified.');
                    $skippedRoutes['hide']['count']++;
                    $skippedRoutes['hide']['list'][] = $message;
                    continue;
                }

                $message = sprintf($messageFormat, 'Skipping', $routeMethods, $routePath) . ' - Exception ' . get_class($e) . ' encountered : ' . $e->getMessage();
                $this->warn($message);
                $skippedRoutes['exception']['count']++;
                $skippedRoutes['exception']['list'][] = $message;
            }
        }

        $this->displayRoutesReport($processedRoutesCount, $skippedRoutes);

        return $parsedRoutes;
    }

    /**
     * Validate the route. It will just consider the routes with the api middleware
     *
     * @param Route $route
     * @param array|null $routeControllerAndMethod
     *
     * @return bool
     */
    private function isValidRoute(Route $route, array $routeControllerAndMethod = null)
    {
        return true;
        $middleware = $route->middleware();
        if (empty($middleware) || $middleware[0] !== 'api') {
            return false;
        }

        if (is_array($routeControllerAndMethod)) {
            $routeControllerAndMethod = implode('@', $routeControllerAndMethod);
        }

        return ! is_callable($routeControllerAndMethod) && ! is_null($routeControllerAndMethod);
    }

    /**
     * @param array $routeAction
     *
     * @return bool
     */
    private function isClosureRoute(array $routeAction)
    {
        return $routeAction['uses'] instanceof \Closure;
    }

    /**
     * @param array $routeControllerAndMethod
     *
     * @throws ReflectionException
     *
     * @return bool
     */
    private function doesControllerMethodExist(array $routeControllerAndMethod)
    {
        list($class, $method) = $routeControllerAndMethod;
        $reflection = new ReflectionClass($class);

        if (! $reflection->hasMethod($method)) {
            return false;
        }

        return true;
    }

    /**
     * @param null|array $routeControllerAndMethod
     *
     * @throws ReflectionException
     *
     * @return bool
     */
    private function isRouteVisibleForDocumentation($routeControllerAndMethod)
    {
        list($class, $method) = $routeControllerAndMethod;
        $reflection = new ReflectionClass($class);

        $comment = $reflection->getMethod($method)->getDocComment();

        if ($comment) {
            $phpdoc = new DocBlock($comment);

            return collect($phpdoc->getTags())
                ->filter(function ($tag) {
                    return $tag->getName() === 'hideFromAPIDocumentation';
                })
                ->isEmpty();
        }

        return true;
    }

    /**
     * Display the route's report with the count of processed and skipped routes
     *
     * @param integer $processedRoutesCount
     * @param array $skippedRoutes
     */
    private function displayRoutesReport($processedRoutesCount, $skippedRoutes)
    {
        $skippedRoutes = collect($skippedRoutes);
        $totalNonProcessed = $skippedRoutes->sum('count');
        $this->line("-----------------------------------------------------------------");

        if (!empty($totalNonProcessed)) {

            $skippedRoutes = $skippedRoutes->sortByDesc(function ($item, $type) {
                return $item['count'];
            })->reject(function ($item) {
                return empty($item['count']);
            })->transform(function ($item) {
                return $item['count'] . ' ' . $item['message'];
            });

            $this->warn("(" . $skippedRoutes->implode(' | ') . ")");
            $this->warn("Total = " . $totalNonProcessed . " routes skipped or failed to process.");
        }

        $this->info("Total = " . $processedRoutesCount . " routes processed.");
        $this->line("-----------------------------------------------------------------");
    }
}
