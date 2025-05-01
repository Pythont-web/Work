<?php

namespace core;
class OrdersActions extends OrdersTableModule
{
    public static function GetOrders()
    {
        $orders = new OrdersTableModule();
        return $orders->get_table();
    }

    public static function GetOrderById($id)
    {
        $orders = new OrdersTableModule();
        return $orders->get_string_by_id($id);
    }

    public static function GetOrderByName($name, $value)
    {
        $orders = new OrdersTableModule();
        return $orders->get_string_by_name($name, $value);
    }
    public static function GetOrdersUnited()
    {
        $orders = new OrdersTableModule();
        $outlets = new OutletsTableModule();

        return $orders->get_table_united($outlets);
    }

    public static function GetOrdersFiltered($outlet_id)
    {
        $orders = new OrdersTableModule();
        $outlets = new OutletsTableModule();

        return $orders->get_table_united_filtered($outlets, 'outlet_id', $outlet_id);
    }

    public static function AddOrder($name, $outlet_id, $order_composition, $price, $file)
    {
        $orders = new OrdersTableModule();
        if('POST' != $_SERVER['REQUEST_METHOD'])
        {
            return [];
        }
        if('add' != $_POST['action'])
        {
            return [];
        }
        $errors = \core\OrdersLogic::Error_Order_Search("", $name, $outlet_id, $order_composition, $price, $file);
        if(!$errors)
        {
            $image_dir = './catalog_images/';
            $file['name'] = substr(md5($file['name']), 0, 10).'.jpg';
            move_uploaded_file($file['tmp_name'], $image_dir . $file['name']);
            $value = [$file['name'], $name, $outlet_id, $order_composition, $price];
            $orders->create($value);
            $filter_id = "";
            if(isset($_GET['filter_id']))
            {
                $filter_id = '&filter_id='.$_GET['filter_id'];
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?obj=orders&success_add=y'.$filter_id);
            die();
        }
        return $errors;
    }

    public static function UpdateOrder($id, $name, $outlet_id, $order_composition, $price, $file)
    {
        $orders = new OrdersTableModule();
        if('POST' != $_SERVER['REQUEST_METHOD'])
        {
            return [];
        }
        if('update' != $_POST['action'])
        {
            return [];
        }
        $errors = \core\OrdersLogic::Error_Order_Search($id, $name, $outlet_id, $order_composition, $price, $file);
        if(!$errors)
        {
            $order = $orders->get_string_by_id($id);
            if($file['size'] == 0)
            {
                $file['name'] = $order['img_path'];
            }
            $key_order = array_keys($order);
            $image_dir = './catalog_images/';
            if($order['img_path'] != $file['name'])
            {
                unlink($image_dir . $order['img_path']);
                $file['name'] = substr(md5($file['name']), 0, 10).'.jpg';
                move_uploaded_file($file['tmp_name'], $image_dir . $file['name']);
            }
            $update_order = [$file['name'], $name, $outlet_id, $order_composition, $price];
            for($i = 1; $i < count($order); $i++)
            {
                if($order[$key_order[$i]] != $update_order[$i - 1])
                {
                    $orders->update($key_order[$i], $update_order[$i - 1], $id);
                }
            }
            $filter_id = "";
            if(isset($_GET['filter_id']))
            {
                $filter_id = '&filter_id='.$_GET['filter_id'];
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success_update=y&obj=orders&id='.$id.$filter_id);
            die();
        }
        return $errors;
    }

    public static function DeleteOrder($id)
    {
        $orders = new OrdersTableModule();
        $order = $orders->get_string_by_id($id);
        $image_dir = './catalog_images/';
        unlink($image_dir . $order['img_path']);
        $orders->delete($id);
        $filter_id = "";
        if(isset($_GET['filter_id']))
        {
            $filter_id = '&filter_id='.$_GET['filter_id'];
        }
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success_delete=y&obj=orders&id='.$id.$filter_id);
        die();
    }
}