<?php

namespace core;
class OrdersTableModule extends TableModule
{
    protected function getTableName() : string
    {
        return "orders";
    }
    protected function getTableColumns() : array
    {
        return ['img_path', 'name', 'outlet_id', 'order_composition', 'price'];
    }

    protected function getTableValue() : array
    {
        return [':img_path', ':name', ':outlet_id', ':order_composition', ':price'];
    }

    protected function getTablePrimaryKey() : string
    {
        return 'order_id';
    }
}