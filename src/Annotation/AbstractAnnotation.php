<?php

namespace Bujanov\DtoBundle\Annotation;

/**
 * Class AbstractAnnotation
 * @package Bujanov\DtoBundle\Annotation
 */
abstract class AbstractAnnotation
{
    /** @var string */
    public $name;

    /** @var string */
    public $handler;
}
