<?php

namespace Evence\Bundle\SoftDeleteableExtensionBundle\Mapping;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;

interface Driver
{
    /**
     * Read the extended metadata configuration for soft delete
     */
    public function readExtendedMetadata(ClassMetadata $meta, array &$config): array;

    /**
     * Sets the original mapping driver
     */
    public function setOriginalDriver(MappingDriver $driver): void;
}
