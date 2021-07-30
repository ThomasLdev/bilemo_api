<?php

namespace App\Annotation;


use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Link
{
    /**
     * @Required
     */
    public $name;

    /**
     * @Required
     */
    public $route;

    public array $params = [];
}
