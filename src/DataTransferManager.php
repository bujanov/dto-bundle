<?php

namespace Bujanov\DtoBundle;

use Bujanov\DtoBundle\Annotation\AbstractAnnotation;
use Bujanov\DtoBundle\Annotation\DoctrineEntity\Property;
use Bujanov\DtoBundle\Annotation\DoctrineEntity\Relation;
use Bujanov\DtoBundle\Annotation\DoctrineEntity\Relations;
use Bujanov\DtoBundle\Handler\HandlerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class DataTransferManager
 * @package Bujanov\DtoBundle
 */
class DataTransferManager implements DataTransferManagerInterface
{
    /** @var Serializer */
    private $serializer;

    /** @var AnnotationReader */
    private $annotationReader;

    /** @var PropertyAccessor */
    private $accessor;

    /** @var ContainerInterface */
    private $container;

    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface */
    private $validator;

    private $annotations = [
        Property::class,
        Relation::class,
        Relations::class,
    ];

    /**
     * DataTransferManager constructor.
     * @param ContainerInterface $container
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->get('validator');
        $this->serializer = $container->get('serializer');
        $this->annotationReader = new AnnotationReader();
        $this->accessor = new PropertyAccessor();
    }

    /**
     * @param string $dtoClass
     * @param null $data
     * @param null|string $format
     * @param array $context
     * @return array|object
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function create(string $dtoClass, $data = null, ?string $format = null, array $context = [])
    {
        if (!$data || (\is_array($data) && empty($data))) {
            return new $dtoClass;
        }

        return $this->denormalize($data, $dtoClass, $format, $context);
    }

    /**
     * @param $object
     * @param DataTransferObjectInterface $dto
     * @return mixed
     * @throws \ReflectionException
     */
    public function populate($object, DataTransferObjectInterface $dto)
    {
        $reflectionClass = $this->getReflectionClass($dto);
        $propertyAnnotations = $this->getAnnotations($reflectionClass);

        foreach ($propertyAnnotations as $property => $annotation) {
            if (!$annotation instanceof AbstractAnnotation) {
                throw new \RuntimeException();
            }

            $this->write($object, $dto, $property, $annotation);
        }

        return $object;
    }

    /**
     * @param DataTransferObjectInterface $dto
     * @param null $groups
     * @return ConstraintViolationListInterface
     */
    public function validate(DataTransferObjectInterface $dto, $groups = null): ConstraintViolationListInterface
    {
        $violationList = null;

        foreach ($dto->getDirty() as $property) {
            /** @var ConstraintViolationListInterface $violationList */
            $violation = $this->validator->validateProperty($dto, $property, $groups);

            if (!$violationList) {
                $violationList = $violation;
                continue;
            }

            $violationList->addAll($violation);
        }

        return $violationList;
    }

    /**
     * @param $data
     * @param string $class
     * @param null|string $format
     * @param array $context
     * @return array|object
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    private function denormalize($data, string $class, ?string $format = null, array $context = [])
    {
        $defaultContext = [
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ];

        return $this->serializer->denormalize($data, $class, $format, array_merge($defaultContext, $context));
    }

    /**
     * @param $object
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    private function getReflectionClass($object): \ReflectionClass
    {
        return new \ReflectionClass(get_class($object));
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    private function getAnnotations(\ReflectionClass $reflectionClass): array
    {
        $propertyAnnotations = [];

        foreach ($this->annotations as $annotation) {
            foreach ($reflectionClass->getProperties() as $property) {
                if ($propertyAnnotation = $this->annotationReader->getPropertyAnnotation($property, $annotation)) {
                    $propertyAnnotation->name = $propertyAnnotation->name ?? $property->name;
                    $propertyAnnotations[$property->name] = $propertyAnnotation;
                    continue;
                }

                $propertyAnnotations[$property->name] = $propertyAnnotations[$property->name] ?? null;
            }
        }

        return $this->createDefaultPropertyAnnotation($propertyAnnotations);
    }

    /**
     * @param array $propertyAnnotations
     * @return array
     */
    private function createDefaultPropertyAnnotation(array $propertyAnnotations): array
    {
        foreach ($propertyAnnotations as $property => $annotation) {
            if (!$annotation) {
                $default = new Property();
                $default->name = $property;
                $propertyAnnotations[$property] = $default;
            }
        }

        return $propertyAnnotations;
    }

    /**
     * @param $object
     * @param DataTransferObjectInterface $dto
     * @param string $property
     * @param AbstractAnnotation $annotation
     */
    private function write($object, DataTransferObjectInterface $dto, string $property, AbstractAnnotation $annotation): void
    {
        if ($dto->isDirty($property) && $this->accessor->isWritable($object, $annotation->name)) {
            /** @var HandlerInterface $handler */
            $handler = $this->container->get($annotation->handler);

            $this->accessor->setValue(
                $object,
                $annotation->name,
                $handler->execute($property, $dto, $object, $annotation)
            );
        }
    }
}
