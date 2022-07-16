<?php

declare(strict_types=1);

namespace PCore\Routing;

use PCore\Routing\Exceptions\{MethodNotAllowedException, RouteNotFoundException};
use Psr\Http\Message\ServerRequestInterface;
use function array_key_exists;
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
        foreach ($route->getMethods() as $method) {
            $this->routes[$method][] = $route;
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
    public function resolveRequest(ServerRequestInterface $request): Route
    {
        $path = '/' . trim($request->getUri()->getPath(), '/');
        $method = $request->getMethod();
        return $this->resolve($method, $path);
    }

    public function resolve(string $method, string $path)
    {
        $routes = $this->routes[$method] ?? throw new MethodNotAllowedException('Метод не разрешен: ' . $method, 405);
        foreach ($routes as $route) {
            if ($route->getPath() === $path) {
                return clone $route;
            }
            if (($compiledPath = $route->getCompiledPath()) && preg_match($compiledPath, $path, $match)) {
                $resolvedRoute = clone $route;
                if (!empty($match)) {
                    foreach ($route->getParameters() as $key => $value) {
                        if (array_key_exists($key, $match)) {
                            $resolvedRoute->setParameter($key, $match[$key]);
                        }
                    }
                }
                return $resolvedRoute;
            }
        }
        throw new RouteNotFoundException('Не найдено', 404);
    }

}