<?php

namespace core;

class OutletsTableModule extends TableModule
{
    protected function getTableName() : string
    {
        return "outlets";
    }
    protected function getTableColumns() : array
    {
        return ['outlet_address'];
    }

    protected function getTableValue() : array
    {
        return [':outlet_address'];
    }

    protected function getTablePrimaryKey() : string
    {
        return 'outlet_id';
    }
}