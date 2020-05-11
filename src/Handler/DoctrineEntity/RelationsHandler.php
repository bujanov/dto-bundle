<?php

namespace Bujanov\DtoBundle\Handler\DoctrineEntity;

use Bujanov\DtoBundle\DataTransferObjectInterface;
use Bujanov\DtoBundle\Handler\HandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class RelationsHandler
 * @package Bujanov\DtoBundle\Handler\DoctrineEntity
 */
class RelationsHandler implements HandlerInterface
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
     * @return array|mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(string $property, DataTransferObjectInterface $dto, $object, $annotation)
    {
        $values = $dto->{$property};

        if (!$values) {
            $accessor = new PropertyAccessor();

            return $accessor->getValue($object, $property);
        }

        $relations = [];

        foreach ($values as $id) {
            $relations[] = $this->em->getReference($annotation->class, $id);
        }

        return $relations;
    }
}
