<?php

namespace Bujanov\DtoBundle\Handler\DoctrineEntity;

use Bujanov\DtoBundle\DataTransferObjectInterface;
use Bujanov\DtoBundle\Handler\HandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class RelationHandler
 * @package Bujanov\DtoBundle\Handler\DoctrineEntity
 */
class RelationHandler implements HandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * RelationHandler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $property
     * @param DataTransferObjectInterface $dto
     * @param $object
     * @param $annotation
     * @return mixed|null|object
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(string $property, DataTransferObjectInterface $dto, $object, $annotation)
    {
        $value = $dto->{$property};

        if (!$value) {
            return null;
        }

        return $this->em->getReference($annotation->class, $value);
    }
}
