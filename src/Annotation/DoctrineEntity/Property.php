<?php

namespace Bujanov\DtoBundle\Annotation\DoctrineEntity;

use Bujanov\DtoBundle\Annotation\AbstractAnnotation;
use Bujanov\DtoBundle\Handler\DoctrineEntity\PropertyHandler;

/**
 * @Annotation
 */
class Property extends AbstractAnnotation
{
    /** @var string */
    public $handler = PropertyHandler::class;
}
