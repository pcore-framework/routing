<?php

declare(strict_types=1);

namespace PCore\Routing\Annotations;

use Attribute;
use PCore\Routing\Contracts\MappingInterface;

/**
 * Class RequestMapping
 * @package PCore\Routing\Annotations
 * @github https://github.com/pcore-framework/routing
 */
#[Attribute(Attribute::TARGET_METHOD)]
class RequestMapping implements MappingInterface
{

    /**
     * Метод по умолчанию
     *
     * @var array|string[]
     */
    public array $methods = ['GET', 'POST', 'HEAD'];

    /**
     * @param string $path путь
     * @param array|string[] $methods метод
     * @param array $middlewares промежуточный слой
     */
    public function __construct(
        public string $path = '/',
        array $methods = [],
        public array $middlewares = []
    )
    {
        if (!empty($methods)) {
            $this->methods = $methods;
        }
    }

}