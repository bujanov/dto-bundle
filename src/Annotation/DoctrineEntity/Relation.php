<?php

namespace Bujanov\DtoBundle\Annotation\DoctrineEntity;

use Bujanov\DtoBundle\Annotation\AbstractAnnotation;
use Bujanov\DtoBundle\Handler\DoctrineEntity\RelationHandler;

/**
 * @Annotation
 */
class Relation extends AbstractAnnotation
{
    /** @var string */
    public $class;

    /** @var string */
    public $primaryKey = 'id';

    /** @var string */
    public $handler = RelationHandler::class;
}
