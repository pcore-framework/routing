<?php

declare(strict_types=1);

namespace PCore\Routing;

use Closure;
use function array_merge;
use function array_unique;
use function sprintf;

/**
 * Class Router
 * @package PCore\Routing
 * @github https://github.com/pcore-framework/routing
 */
class Router
{

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
        if ($options !== []) {
            foreach ($options as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
        $this->routeCollector = $routeCollector ?? new RouteCollector();
    }

    /**
     * Разрешить почти все методы
     *
     * @param string $path путь запроса
     * @param array|Closure|string $action способ обработки
     * @return Route
     */
    public function any(string $path, array|Closure|string $action): Route
    {
        return $this->request($path, $action, ['GET', 'HEAD', 'POST', 'OPTIONS', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Метод PATCH
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function patch(string $uri, string|array|Closure $action): Route
    {
        return $this->request($uri, $action, ['PATCH']);
    }

    /**
     * Метод PUT
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function put(string $uri, string|array|Closure $action): Route
    {
        return $this->request($uri, $action, ['PUT']);
    }

    /**
     * Метод DELETE
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function delete(string $uri, string|array|Closure $action): Route
    {
        return $this->request($uri, $action, ['DELETE']);
    }

    /**
     * Метод POST
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function post(string $uri, string|array|Closure $action): Route
    {
        return $this->request($uri, $action, ['POST']);
    }

    /**
     * Метод GET
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function get(string $uri, string|array|Closure $action): Route
    {
        return $this->request($uri, $action, ['GET', 'HEAD']);
    }

    /**
     * Метод OPTIONS
     *
     * @param string $uri
     * @param $action
     * @return Route
     */
    public function options(string $uri, string|array|Closure $action): Route
    {
        return $this->request($uri, $action, ['OPTIONS']);
    }

    /**
     * Объединение пространств имен и контроллеров вместе
     *
     * @param string $controller
     * @return string
     */
    protected function longNameController(string $controller): string
    {
        return trim($this->namespace . '\\' . ltrim($controller, '\\'), '\\');
    }

    /**
     * Разрешить несколько методов запроса
     *
     * @param string $path
     * @param array|Closure|string $action
     * @param array $methods
     * @return Route
     */
    public function request(string $path, array|Closure|string $action, array $methods = ['GET', 'HEAD', 'POST']): Route
    {
        if (is_array($action)) {
            [$controller, $action] = $action;
            $action = [$this->longNameController($controller), $action];
        }
        if (is_string($action)) {
            $action = $this->longNameController($action);
        }
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
        $new->namespace = sprintf('%s\\%s', $this->namespace, trim($namespace, '\\'));
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