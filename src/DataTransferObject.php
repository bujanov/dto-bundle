<?php

namespace Bujanov\DtoBundle;

class DataTransferObject implements DataTransferObjectInterface
{
    /** @var array */
    private $dirty = [];

    private $excluded = [];

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->{$property};
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        if ($property === 'writeListener') {
            throw new \RuntimeException();
        }

        if (!\in_array($property, $this->dirty)) {
            $this->dirty[] = $property;
        }

        if ($this->hasProperty($property)) {
            $this->{$property} = $value;
        } else {
            $this->excluded[$property] = $value;
        }
    }

    /**
     * @param string $property
     * @return bool
     */
    public function isDirty(string $property): bool
    {
        return \in_array($property, $this->dirty);
    }

    /**
     * @return array
     */
    public function getDirty(): array
    {
        return $this->dirty;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function hasProperty(string $name): bool
    {
        return \in_array(
            $name,
            \array_keys(
                \get_class_vars(
                    \get_class($this)
                )
            )
        );
    }
}
