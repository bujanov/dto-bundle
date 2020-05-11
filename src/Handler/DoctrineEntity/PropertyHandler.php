<?php

namespace Bujanov\DtoBundle\Handler\DoctrineEntity;

use Bujanov\DtoBundle\Annotation\DoctrineEntity\Property;
use Bujanov\DtoBundle\DataTransferObjectInterface;
use Bujanov\DtoBundle\Handler\HandlerInterface;

/**
 * Class PropertyHandler
 * @package Bujanov\DtoBundle\Handler\DoctrineEntity
 */
class PropertyHandler implements HandlerInterface
{
    /**
     * @param string $property
     * @param DataTransferObjectInterface $dto
     * @param $object
     * @param Property $annotation
     * @return mixed
     */
    public function execute(string $property, DataTransferObjectInterface $dto, $object, $annotation)
    {
        return $dto->{$property};
    }
}
