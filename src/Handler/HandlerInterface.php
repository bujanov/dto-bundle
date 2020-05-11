<?php

namespace Bujanov\DtoBundle\Handler;

use Bujanov\DtoBundle\Annotation\AbstractAnnotation;
use Bujanov\DtoBundle\DataTransferObjectInterface;

interface HandlerInterface
{
    /**
     * @param string $property
     * @param DataTransferObjectInterface $dto
     * @param $object
     * @param $annotation
     * @return mixed
     */
    public function execute(string $property, DataTransferObjectInterface $dto, $object, $annotation);
}
