<?php

namespace App;

/**
 *
 */
trait ModelExtra
{
    public function getTableColumns()
    {
        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $columns = collect($columns)->except($this->hidden);
        $columns = $columns->filter(function ($item, $key) {
            if (!in_array($item, $this->hidden)) {
                return $item;
            }
        })->toArray();
        return $columns;
    }
}
