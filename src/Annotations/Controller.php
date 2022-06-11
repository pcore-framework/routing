<?php

declare(strict_types=1);

namespace PCore\Routing\Annotations;

use Attribute;

/**
 * Class Controller
 * @package PCore\Routing\Annotations
 * @github https://github.com/pcore-framework/routing
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Controller
{

    /**
     * @param string $prefix
     * @param array $middlewares
     */
    public function __construct(
        public string $prefix = '',
        public array $middlewares = []
    )
    {
    }

}