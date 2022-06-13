<?php

declare(strict_types=1);

namespace PCore\Routing\Annotations;

use Attribute;

/**
 * Class PostMapping
 * @package PCore\Routing\Annotations
 * @github https://github.com/pcore-framework/routing
 */
#[Attribute(Attribute::TARGET_METHOD)]
class PostMapping extends RequestMapping
{

    /**
     * @var array
     */
    public array $methods = ['POST'];

}