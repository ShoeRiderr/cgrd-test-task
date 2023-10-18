<?php

namespace App\Handler\Repository\Trait;

trait QueryParamHelper
{
    /**
     * Return string for PDO param binding in update query
     * Result eg.: 'title=:title, description=:description'
     *
     * @return string
     */
    protected function prepareForUpdateParamBinding(array $data): string
    {
        unset($data['id']);

        // Prepare all entity columns for binding params
        $columns = array_keys($data);
        $columns = array_map(fn ($column) => $column . '=:' . $column, $columns);

        return implode(', ', $columns);
    }

    /**
     * Return array with columns and values names for PDO param binding in insert query
     * Result eg.:
     * [
     *  'columns' => 'title, description',
     *  'values' => '?, ?'
     * ]
     *
     * @return array
     */
    protected function prepareForCreateParamBinding(array $data): array
    {
        unset($data['id']);

        $columns = array_keys($data);

        $values = str_repeat('? ', count($columns));
        $values = trim($values);
        $values = str_replace(' ', ', ', $values);

        $columns = implode(', ', $columns);

        return [
            'columns' => $columns,
            'values' => $values,
        ];
    }
}
