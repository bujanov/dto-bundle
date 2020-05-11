<?php

namespace Bujanov\DtoBundle;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface DataTransferManagerInterface
{
    /**
     * @param string $dtoClass
     * @param null $data
     * @param null|string $format
     * @param array $context
     * @return mixed
     */
    public function create(string $dtoClass, $data = null, ?string $format = null, array $context = []);

    /**
     * @param $object
     * @param DataTransferObjectInterface $dto
     * @return mixed
     */
    public function populate($object, DataTransferObjectInterface $dto);

    /**
     * @param DataTransferObjectInterface $dto
     * @param null $groups
     * @return ConstraintViolationListInterface
     */
    public function validate(DataTransferObjectInterface $dto, $groups = null): ConstraintViolationListInterface;
}
