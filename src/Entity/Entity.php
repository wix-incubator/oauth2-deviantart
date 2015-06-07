<?php

namespace DeviantArt\OAuth2\Client\Entity;

abstract class Entity
{
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new \OutOfRangeException(sprintf(
                '%s does not contain a property by the name of "%s"',
                __CLASS__,
                $name
            ));
        }
        return $this->{$name};
    }
}
