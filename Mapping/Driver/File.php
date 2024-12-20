<?php

namespace Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Driver;

use Doctrine\Persistence\Mapping\Driver\FileLocator;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Driver;

abstract class File implements Driver
{
    protected $locator;
    protected $_extension;
    protected $_originalDriver;

    public function setLocator(FileLocator $locator): void
    {
        $this->locator = $locator;
    }

    public function setOriginalDriver(MappingDriver $driver): void
    {
        $this->_originalDriver = $driver;
    }

    abstract protected function _loadMappingFile($file);
}
