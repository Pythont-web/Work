<?php

require_once '.core/index_core.php';

$filter_id = "";
if(isset($_GET['filter_id']))
{
    $filter_id = '&filter_id='.$_GET['filter_id'];
}

if(isset($_GET['obj']))
{
    $Orders = \core\OrdersActions::GetOrders();
    $Outlets = \core\OutletsActions::GetOutlets();
    $errors = [];

    if($_GET['obj']=="orders")
    {
        $Massive_outlet_id = [];
        foreach($Outlets as $outlet)
        {
            $Massive_outlet_id[$outlet['outlet_id']] = "";
        }

        $value_name = "";
        $value_order_composition = "";
        $value_price = "";
        $value_outlet_id = "";
        $id = "";
        $href = 'index.php?obj=orders&op=read'.$filter_id;
        $table = "Заказы";
        $success_update = "Заказ успешно обновлен";
        $success_add = "Заказ успешно создан";

        if(isset($_GET['id']))
        {
            $action = "Редактирование заказа №".$_GET['id'];
            $Action = "Редактировать заказ";
            $action_type = "update";
            if(\core\OrdersActions::GetOrderById($_GET['id']))
            {
                $value_order_id = $_GET['id'];
                $order = \core\OrdersActions::GetOrderById($_GET['id']);
                $Massive_outlet_id[$order['outlet_id']] = "selected";
                $value_name = $order['name'];
                $value_order_composition = $order['order_composition'];
                $value_price = $order['price'];
            }
            else
            {
                header('Location: index.php?obj=orders&op=create');
            }
        }
        else
        {
            if(isset($_GET['filter_id']))
            {
                $Massive_outlet_id[$_GET['filter_id']] = "selected";
            }
            $action = "Новый заказ";
            $Action = "Добавить заказ";
            $action_type = "add";
        }

        if(count($_POST) > 0)
        {
            if($_FILES)
            {
                $file = array_shift($_FILES);
            }
            if(isset($_POST['name']))
            {
                $value_name = $_POST['name'];
            }
            if(isset($_POST['outlet_id']) && $_POST['outlet_id'] != "")
            {
                $Massive_outlet_id[$_POST['outlet_id']] = "selected";
                $value_outlet_id = $_POST['outlet_id'];
            }
            if(isset($_POST['order_composition']))
            {
                $value_order_composition = $_POST['order_composition'];
            }
            if(isset($_POST['price']))
            {
                $value_price = $_POST['price'];
            }
            if(isset($_GET['id']) && $_GET['id'] != "")
            {
                $id = $_GET['id'];
                $errors = \core\OrdersActions::UpdateOrder($id, $value_name, $value_outlet_id, $value_order_composition, $value_price, $file);
            }
            else
            {
                $errors = \core\OrdersActions::AddOrder($value_name, $value_outlet_id, $value_order_composition, $value_price, $file);
            }
        }
    }

    if($_GET['obj']=="outlets")
    {
        $value_outlet_address = "";

        $href = 'index.php?obj=outlets&op=read';
        $table = "Точки самовывоза";
        $success_update = "Торговая точка успешно обновлена";
        $success_add = "Торговая точка успешно создана";
        if(isset($_GET['id']))
        {
            $action = "Редактирование торговой точки №".$_GET['id'];
            $Action = "Редактировать торговую точку";
            $action_type = "update";
            if(\core\OutletsActions::GetOutletById($_GET['id']))
            {
                $value_outlet_address = \core\OutletsActions::GetOutletById($_GET['id'])['outlet_address'];
            }
            else
            {
                header('Location: change.php?obj=outlets');
            }
        }
        else
        {
            $action = "Новая торговая точка";
            $Action = "Добавить торговую точку";
            $action_type = "add";
        }
        if(count($_POST) > 0)
        {
            if(isset($_POST['outlet_address']))
            {
                $value_outlet_address = $_POST['outlet_address'];
            }
            if(isset($_GET['id']) && $_GET['id'] != "")
            {
                $id = $_GET['id'];
                $errors = \core\OutletsActions::UpdateOutlet($id, $value_outlet_address);
            }
            else
            {
                $errors = \core\OutletsActions::AddOutlet($value_outlet_address);
            }
        }
    }

}