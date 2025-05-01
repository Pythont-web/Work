<?php

namespace core;

class OutletsActions extends OutletsTableModule
{
    public static function GetOutlets()
    {
        $outlets = new OutletsTableModule();
        return $outlets->get_table();
    }

    public static function GetOutletById($id)
    {
        $outlets = new OutletsTableModule();
        return $outlets->get_string_by_id($id);
    }

    public static function AddOutlet($outlet_address)
    {
        $outlets = new OutletsTableModule();
        if('POST' != $_SERVER['REQUEST_METHOD'])
        {
            return [];
        }
        if('add' != $_POST['action'])
        {
            return [];
        }
        $errors = \core\OutletsLogic::Error_Order_Search($outlet_address);
        if(!$errors)
        {
            $value = [$outlet_address];
            $outlets->create($value);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?obj=outlets&success_add=y');
            die();
        }
        return $errors;
    }

    public static function UpdateOutlet($id, $outlet_address)
    {
        $outlets = new OutletsTableModule();
        if('POST' != $_SERVER['REQUEST_METHOD'])
        {
            return [];
        }
        if('update' != $_POST['action'])
        {
            return [];
        }
        $errors = \core\OutletsLogic::Error_Order_Search($outlet_address);
        if(!$errors)
        {
            $outlets->update('outlet_address', $outlet_address, $id);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?obj=outlets&success_update=y&id='.$id);
            die();
        }
        return $errors;
    }

    public static function DeleteOutlet($id)
    {
        $outlets = new OutletsTableModule();
        $outlets->delete($id);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success_delete=y&obj=outlets&id='.$id);
        die();
    }

}