<?php

namespace Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Driver;

use Doctrine\Persistence\Mapping\ClassMetadata;

class Xml extends File
{
    const EVENCE_NAMESPACE = 'http://rubenharms.nl/schema/soft-delete-extension';
    const DOCTRINE_NAMESPACE_URI = 'http://doctrine-project.org/schemas/orm/doctrine-mapping';

    protected $_extension = '.orm.xml';

    protected function _getAttribute(\SimpleXMLElement $node, string $attributeName): string
    {
        $attributes = $node->attributes();
        return (string) $attributes[$attributeName];
    }

    protected function _loadMappingFile($file)
    {
        $result = [];
        // Utiliser simplexml_load_string au lieu de simplexml_load_file
        $xmlElement = simplexml_load_string(file_get_contents($file));
        $xmlElement = $xmlElement->children(self::DOCTRINE_NAMESPACE_URI);
        $xmlElement->registerXPathNamespace('evence', self::EVENCE_NAMESPACE);

        if (isset($xmlElement->entity)) {
            foreach ($xmlElement->entity as $entityElement) {
                $entityName = $this->_getAttribute($entityElement, 'name');
                $result[$entityName] = $entityElement;
            }
        }

        return $result;
    }

    public function readExtendedMetadata(ClassMetadata $meta, array &$config): array
    {
        $xml = $this->_getMapping($meta->getName());

        if (!$xml) {
            return $config;
        }

        // Parcourir toutes les relations avec evence:on-soft-delete
        foreach ($xml->xpath('.//evence:on-soft-delete') as $softDeleteNode) {
            $parentNode = $softDeleteNode->xpath('..')[0];
            $fieldName = $this->_getAttribute($parentNode, 'field');
            $type = $this->_getAttribute($softDeleteNode, 'type');

            if (!isset($config['fields'])) {
                $config['fields'] = [];
            }

            $config['fields'][$fieldName] = [
                'onSoftDelete' => ['type' => $type]
            ];
        }

        return $config;
    }

    protected function _getMapping(string $className)
    {
        $mappingFile = $this->locator->findMappingFile($className);
        return $this->_loadMappingFile($mappingFile)[$className] ?? null;
    }
}
