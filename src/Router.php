<?php

declare(strict_types=1);

namespace PCore\Routing;

use Closure;
use PCore\Utils\Traits\AutoFillProperties;
use function array_merge;
use function array_unique;
use function sprintf;

/**
 * Class Router
 * @package PCore\Routing
 * https://github.com/pcore-framework/routing
 */
class Router
{

    use AutoFillProperties;

    /**
     * Группировка промежуточного программного обеспечения
     *
     * @var array
     */
    protected array $middlewares = [];

    /**
     * Префикс
     *
     * @var string
     */
    protected string $prefix = '';

    /**
     * @var string
     */
    protected string $namespace = '';

    /**
     * Доменное имя
     *
     * @var string
     */
    protected string $domain = '';

    /**
     * @var array
     */
    protected array $patterns = [];

    /**
     * @var RouteCollector
     */
    protected RouteCollector $routeCollector;

    /**
     * @param array $options
     * @param RouteCollector|null $routeCollector
     */
    public function __construct(array $options = [], ?RouteCollector $routeCollector = null)
    {
        $this->fillProperties($options);
        $this->routeCollector = $routeCollector ?? new RouteCollector();
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function patch(string $uri, $action): Route
    {
        return $this->request($uri, $action, ['PATCH']);
    }

    /**
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function put(string $uri, $action): Route
    {
        return $this->request($uri, $action, ['PUT']);
    }

    /**
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function delete(string $uri, $action): Route
    {
        return $this->request($uri, $action, ['DELETE']);
    }

    /**
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function post(string $uri, $action): Route
    {
        return $this->request($uri, $action, ['POST']);
    }

    /**
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function get(string $uri, $action): Route
    {
        return $this->request($uri, $action, ['GET', 'HEAD']);
    }

    /**
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function options(string $uri, $action): Route
    {
        return $this->request($uri, $action, ['OPTIONS']);
    }

    /**
     * @param string $path
     * @param array|Closure|string $action
     * @param array $methods
     * @return Route
     */
    public function request(string $path, array|Closure|string $action, array $methods = ['GET', 'HEAD', 'POST']): Route
    {
        $route = new Route($methods, $this->prefix . $path, $action, $this, $this->domain);
        $this->routeCollector->add($route);
        return $route;
    }

    /**
     * Маршрутизация пакетов
     *
     * @param Closure $group
     */
    public function group(Closure $group): void
    {
        $group($this);
    }

    /**
     * Добавление промежуточного программного обеспечения
     *
     * @param string ...$middlewares
     * @return Router
     */
    public function middleware(string ...$middlewares): Router
    {
        $new = clone $this;
        $new->middlewares = array_unique([...$this->middlewares, ...$middlewares]);
        return $new;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return Router
     */
    public function domain(string $domain): Router
    {
        $new = clone $this;
        $new->domain = $domain;
        return $new;
    }

    /**
     * @param array $patterns
     *
     * @return Router
     */
    public function patterns(array $patterns): Router
    {
        $new = clone $this;
        $new->patterns = array_merge($this->patterns, $patterns);
        return $new;
    }

    /**
     * Установить префикс
     *
     * @param string $prefix
     * @return $this
     */
    public function prefix(string $prefix): Router
    {
        $new = clone $this;
        $new->prefix = $this->prefix . $prefix;
        return $new;
    }

    /**
     * @param string $namespace
     * @return Router
     */
    public function namespace(string $namespace): Router
    {
        $new = clone $this;
        $new->namespace = sprintf('%s\\%s', $this->namespace, $namespace);
        return $new;
    }

    /**
     * @return RouteCollector
     */
    public function getRouteCollector(): RouteCollector
    {
        return $this->routeCollector;
    }

}