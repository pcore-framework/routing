<?php

declare(strict_types=1);

namespace PCore\Routing\Annotations;

use Attribute;

/**
 * Class PatchMapping
 * @package PCore\Routing\Annotations
 * @github https://github.com/pcore-framework/routing
 */
#[Attribute(Attribute::TARGET_METHOD)]
class PatchMapping extends RequestMapping
{

    /**
     * @var array|string[]
     */
    public array $methods = ['PATCH'];

}