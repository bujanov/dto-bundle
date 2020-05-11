<?php

namespace Bujanov\DtoBundle\Annotation\DoctrineEntity;

use Bujanov\DtoBundle\Annotation\AbstractAnnotation;
use Bujanov\DtoBundle\Handler\DoctrineEntity\RelationsHandler;

/**
 * @Annotation
 */
class Relations extends AbstractAnnotation
{
    /** @var string */
    public $class;

    /** @var string */
    public $primaryKey = 'id';

    /** @var string */
    public $handler = RelationsHandler::class;
}
