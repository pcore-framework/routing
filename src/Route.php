<?php

declare(strict_types=1);

namespace PCore\Routing;

use Closure;
use function preg_replace_callback;
use function sprintf;
use function trim;

/**
 * Class Route
 * @package PCore\Routing
 * @github https://github.com/pcore-framework/routing
 */
class Route
{

    /**
     * Правила по умолчанию
     */
    protected const DEFAULT_PATTERN = '[^\/]+';

    /**
     * Путь
     *
     * @var string
     */
    protected string $path;

    /**
     * Параметры маршрутизации
     *
     * @var array
     */
    protected array $parameters = [];

    /**
     * @var string|null
     */
    protected ?string $regexp = null;

    /**
     * @var array
     */
    protected array $middlewares = [];

    /**
     * @var string
     */
    protected string $compiledDomain = '';

    /**
     * @var array
     */
    protected array $withoutMiddleware = [];

    /**
     * @var string
     */
    protected string $domain = '';

    /**
     * @param array $methods
     * @param string $path
     * @param string|Closure|array $action
     * @param Router $router
     * @param string $domain
     */
    public function __construct(
        protected array $methods,
        string $path,
        protected string|Closure|array $action,
        protected Router $router,
        string $domain = '',
    )
    {
        $this->setPath($path)->domain($domain);
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): Route
    {
        $this->path = '/' . trim($path, '/');
        $regexp = preg_replace_callback('/<(\w+)>/', function ($matches) {
            [, $name] = $matches;
            $this->setParameter($name, null);
            return sprintf('(?P<%s>%s)', $name, $this->getPattern($name));
        }, $this->path);
        if ($regexp !== $this->path) {
            $this->regexp = sprintf('#^%s$#iU', $regexp);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getCompiledDomain(): string
    {
        return $this->compiledDomain;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function domain(string $domain): Route
    {
        if ($domain !== '') {
            $this->domain = $domain;
            $this->compiledDomain = '#^' . str_replace(['.', '*'], ['\.', '(.+?)'], $domain) . '$#iU';
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string|null
     */
    public function getRegexp(): ?string
    {
        return $this->regexp;
    }

    /**
     * Получение правил параметров маршрутизации
     *
     * @param string $key
     * @return string
     */
    public function getPattern(string $key): string
    {
        return $this->getPatterns()[$key] ?? static::DEFAULT_PATTERN;
    }

    /**
     * @return array
     */
    public function getPatterns(): array
    {
        return $this->router->getPatterns();
    }

    /**
     * Задать один параметр маршрутизации
     *
     * @param string $name
     * @param $value
     * @return void
     */
    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Установить все параметры маршрутизации
     *
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Получить один параметр маршрутизации
     *
     * @param string $name
     * @return string|null
     */
    public function getParameter(string $name): ?string
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Получить все параметры маршрутизации
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Настройка промежуточного программного обеспечения
     *
     * @param string|array $middlewares
     * @return $this
     */
    public function middlewares(string|array $middlewares): Route
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }
        $this->middlewares = $middlewares;
        return $this;
    }

    /**
     * Исключенное промежуточное программное обеспечение
     *
     * @param string $middleware
     * @return $this
     */
    public function withoutMiddleware(string $middleware): Route
    {
        $this->withoutMiddleware[] = $middleware;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function addMethod(string $method): static
    {
        if (!in_array($method, $this->methods)) {
            $this->methods[] = $method;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array|Closure|string
     */
    public function getAction(): array|string|Closure
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        $middlewares = array_unique([...($this->router?->getMiddlewares() ?? []), ...$this->middlewares]);
        return array_diff($middlewares, $this->withoutMiddleware);
    }

}