<?php
namespace Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Driver;

use Doctrine\Persistence\Mapping\Driver\FileDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\MappingException;

class Xml extends XmlDriver
{
    const EVENCE_NAMESPACE = 'http://rubenharms.nl/schema/soft-delete-extension';
    const DOCTRINE_NAMESPACE_URI = 'http://doctrine-project.org/schemas/orm/doctrine-mapping';

    protected function _loadMappingFile($file)
    {
        $result = [];
        // We avoid calling `simplexml_load_file()` in order to prevent file operations in libXML.
        // If `libxml_disable_entity_loader(true)` is called before, `simplexml_load_file()` fails,
        // that's why we use `simplexml_load_string()` instead.
        // @see https://bugs.php.net/bug.php?id=62577.
        $xmlElement = simplexml_load_string(file_get_contents($file));
        $xmlElement = $xmlElement->children(self::DOCTRINE_NAMESPACE_URI);
        $xmlElement->registerXPathNamespace('evence', self::EVENCE_NAMESPACE);

        if (isset($xmlElement->entity)) {
            foreach ($xmlElement->entity as $entityElement) {
                $entityName = $this->_getAttribute($entityElement, 'name');

                foreach ($entityElement->xpath('.//evence:on-soft-delete') as $softDeleteNode) {
                    $parentNode = $softDeleteNode->xpath('..')[0];
                    $fieldName = $this->_getAttribute($parentNode, 'field');
                    $type = $this->_getAttribute($softDeleteNode, 'type');

                    if (!isset($result[$entityName])) {
                        $result[$entityName] = [];
                    }

                    if (!isset($result[$entityName]['fields'])) {
                        $result[$entityName]['fields'] = [];
                    }

                    $result[$entityName]['fields'][$fieldName] = [
                        'onSoftDelete' => ['type' => $type]
                    ];
                }
            }
        }

        return $result;
    }

    protected function _getAttribute(\SimpleXMLElement $node, string $attributeName)
    {
        $attributes = $node->attributes();

        return (string) $attributes[$attributeName];
    }
}
