<?php
declare(strict_types=1);

namespace Monogo\TypesenseCmsPages\Model\Config\Source;

use Monogo\TypesenseCore\Model\Config\Source\AbstractTable;

class Schema extends AbstractTable
{
    /**
     * @return array[]
     */
    protected function getTableData(): array
    {
        return [
            'name' => [
                'label' => 'Fields',
                'values' => function () {
                    $options = [];
                    $tableFields = $this->getTableFields();
                    foreach ($tableFields as $key => $label) {
                        $options[$key] = $label;
                    }

                    return $options;
                },
            ],
            'type' => [
                'label' => __('Field type'),
                'values' => [
                    'string' => __('String values'),
                    'string[]' => __('Array of strings'),
                    'int32' => __('Integer values up to 2,147,483,647'),
                    'int32[]' => __('Array of int32'),
                    'int64' => __('Integer values larger than 2,147,483,647'),
                    'int64[]' => __('Array of int64'),
                    'float' => __('Floating point / decimal numbers'),
                    'float[]' => __('Array of floating point / decimal numbers'),
                    'bool' => __('true or false'),
                    'bool[]' => __('Array of booleans'),
                    'geopoint' => __('Latitude and longitude specified as [lat, lng]'),
                    'geopoint[]' => __('Arrays of Latitude and longitude specified as [[lat1, lng1], [lat2, lng2]]'),
                    'object' => __('Nested objects'),
                    'object[]' => __('Arrays of nested objects'),
                    'string*' => __('Special type that automatically converts values to a string or string[]'),
                    'auto' => __('Special type that automatically attempts to infer the data type based on the documents added to the collection'),
                ],
            ],
            'facet' => [
                'label' => __('Facet'),
                'values' => [0 => __('No'), 1 => __('Yes')],
            ],
            'optional' => [
                'label' => __('Is optional'),
                'values' => [0 => __('No'), 1 => __('Yes')],
            ],
            'index' => [
                'label' => __('Index field'),
                'values' => [0 => __('No'), 1 => __('Yes')],
            ],
            'infix' => [
                'label' => __('Infix Search'),
                'values' => [0 => __('No'), 1 => __('Yes')],
            ],
            'sort' => [
                'label' => __('Sort field'),
                'values' => [0 => __('No'), 1 => __('Yes')],
            ],
            'locale' => [
                'label' => __('Locale'),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getTableFields(): array
    {
        $tableSchema = [];
        $fields = $this->connection->getConnection()->describeTable('cms_page');
        foreach ($fields as $field) {
            $tableSchema[$field['COLUMN_NAME']] = $field['COLUMN_NAME'] . ' (' . $field['DATA_TYPE'] . ')';
        }
        unset($tableSchema['page_id']);
        return $tableSchema;
    }
}
