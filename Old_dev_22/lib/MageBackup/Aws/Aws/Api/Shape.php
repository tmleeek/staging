<?php
namespace MageBackup\Aws\Api;

/**
 * Base class representing a modeled shape.
 */
class Shape extends AbstractModel
{
    /**
     * Get a concrete shape for the given definition.
     *
     * @param array    $definition
     * @param ShapeMap $shapeMap
     *
     * @return mixed
     * @throws \RuntimeException if the type is invalid
     */
    public static function create(array $definition, ShapeMap $shapeMap)
    {
        static $map = [
            'structure' => 'MageBackup\Aws\Api\StructureShape',
            'map'       => 'MageBackup\Aws\Api\MapShape',
            'list'      => 'MageBackup\Aws\Api\ListShape',
            'timestamp' => 'MageBackup\Aws\Api\TimestampShape',
            'integer'   => 'MageBackup\Aws\Api\Shape',
            'double'    => 'MageBackup\Aws\Api\Shape',
            'float'     => 'MageBackup\Aws\Api\Shape',
            'long'      => 'MageBackup\Aws\Api\Shape',
            'string'    => 'MageBackup\Aws\Api\Shape',
            'byte'      => 'MageBackup\Aws\Api\Shape',
            'character' => 'MageBackup\Aws\Api\Shape',
            'blob'      => 'MageBackup\Aws\Api\Shape',
            'boolean'   => 'MageBackup\Aws\Api\Shape'
        ];

        if (isset($definition['shape'])) {
            return $shapeMap->resolve($definition);
        }

        if (!isset($map[$definition['type']])) {
            throw new \RuntimeException('Invalid type: '
                . print_r($definition, true));
        }

        $type = $map[$definition['type']];

        return new $type($definition, $shapeMap);
    }

    /**
     * Get the type of the shape
     *
     * @return string
     */
    public function getType()
    {
        return $this->definition['type'];
    }

    /**
     * Get the name of the shape
     *
     * @return string
     */
    public function getName()
    {
        return $this->definition['name'];
    }
}
