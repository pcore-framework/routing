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
    protected const DEFAULT_VARIABLE_REGEX = '[^\/]+';

    protected const VARIABLE_REGEX = '\{\s*([a-zA-Z_][a-zA-Z0-9_-]*)\s*(?::\s*([^{}]*(?:\{(?-1)\}[^{}]*)*))?\}';

    /**
     * Путь
     *
     * @var string
     */
    protected string $path;

    /**
     * @var string
     */
    protected string $compiledPath = '';

    /**
     * Параметры маршрутизации
     *
     * @var array
     */
    protected array $parameters = [];

    /**
     * Промежуточное ПО маршрутизации
     *
     * @var array
     */
    protected array $middlewares = [];

    /**
     * @param array $methods
     * @param string $path
     * @param Closure|array $action
     * @param array $patterns
     */
    public function __construct(
        protected array $methods,
        string $path,
        protected Closure|array $action,
        protected array $patterns = []
    )
    {
        $this->path = $path = '/' . trim($path, '/');
        $compiledPath = preg_replace_callback(sprintf('#%s#', self::VARIABLE_REGEX), function ($matches) {
            $name = $matches[1];
            if (isset($matches[2])) {
                $this->patterns[$name] = $matches[2];
            }
            $this->setParameter($name, null);
            return sprintf('(?P<%s>%s)', $name, $this->getPattern($name));
        }, $path);
        if ($compiledPath !== $path) {
            $this->compiledPath = sprintf('#^%s$#iU', $compiledPath);
        }
    }

    /**
     * Получение правил параметров маршрутизации
     *
     * @param string $key
     * @return string
     */
    public function getPattern(string $key): string
    {
        return $this->getPatterns()[$key] ?? static::DEFAULT_VARIABLE_REGEX;
    }

    /**
     * @return array
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * Возвращает скомпилированное регулярное выражение
     */
    public function getCompiledPath(): string
    {
        return $this->compiledPath;
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
        if (($key = array_search($middleware, $this->middlewares)) !== false) {
            unset($this->middlewares[$key]);
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
        return $this->middlewares;
    }

}