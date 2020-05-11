<?php

namespace Bujanov\DtoBundle;

interface DataTransferObjectInterface
{
    /**
     * @param string $property
     * @return bool
     */
    public function isDirty(string $property): bool;

    /**
     * @return array
     */
    public function getDirty(): array;
}
