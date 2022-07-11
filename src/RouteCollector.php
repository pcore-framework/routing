<?php

declare(strict_types=1);

namespace PCore\Routing;

use PCore\Routing\Exceptions\{MethodNotAllowedException, RouteNotFoundException};
use Psr\Http\Message\ServerRequestInterface;
use function array_key_exists;
use function is_null;
use function preg_match;

/**
 * Class RouteCollector
 * @package PCore\Routing
 * @github https://github.com/pcore-framework/routing
 */
class RouteCollector
{

    /**
     * Все маршруты, которые не сгруппированы
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * Добавление маршрута
     *
     * @param Route $route
     * @return void
     */
    public function add(Route $route): void
    {
        $domain = $route->getCompiledDomain();
        foreach ($route->getMethods() as $method) {
            $this->routes[$method][$domain][] = $route;
        }
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->routes;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function resolve(ServerRequestInterface $request): Route
    {
        $path = '/' . trim($request->getUri()->getPath(), '/');
        $method = $request->getMethod();
        $map = $this->routes[$method] ?? throw new MethodNotAllowedException('Метод не разрешен: ' . $method, 405);
        $routes = $map[''] ?? [];
        foreach ($map as $domain => $item) {
            if ($domain === '') {
                continue;
            }
            if (preg_match($domain, $request->getUri()->getHost())) {
                $routes = array_merge($item, $routes);
            }
        }
        $resolvedRoute = null;
        /* @var Route $route */
        foreach ($routes as $route) {
            if ($route->getPath() === $path) {
                $resolvedRoute = clone $route;
            } else {
                $regexp = $route->getRegexp();
                if (!is_null($regexp) && preg_match($regexp, $path, $match)) {
                    $resolvedRoute = clone $route;
                    if (!empty($match)) {
                        foreach ($route->getParameters() as $key => $value) {
                            if (array_key_exists($key, $match)) {
                                $resolvedRoute->setParameter($key, trim($match[$key], '/'));
                            }
                        }
                    }
                }
            }
            if (!is_null($resolvedRoute)) {
                return $resolvedRoute;
            }
        }
        throw new RouteNotFoundException('Не найдено', 404);
    }

}