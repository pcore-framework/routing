<?php

declare(strict_types=1);

namespace PCore\Routing\Annotations;

use Attribute;

/**
 * Class GetMapping
 * @package PCore\Routing\Annotations
 * @github https://github.com/pcore-framework/routing
 */
#[Attribute(Attribute::TARGET_METHOD)]
class GetMapping extends RequestMapping
{

    /**
     * @var array|string[]
     */
    public array $methods = ['GET', 'HEAD'];

}