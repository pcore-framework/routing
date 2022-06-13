<?php

declare(strict_types=1);

namespace PCore\Routing\Annotations;

use Attribute;

/**
 * Class PutMapping
 * @package PCore\Routing\Annotations
 * @github https://github.com/pcore-framework/routing
 */
#[Attribute(Attribute::TARGET_METHOD)]
class PutMapping extends RequestMapping
{

    /**
     * @var array
     */
    public array $methods = ['PUT'];

}