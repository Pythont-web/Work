<?php

require_once '.core/index_core.php';

$filter_id = "";
if(isset($_GET['filter_id']))
{
    $filter_id = '&filter_id='.$_GET['filter_id'];
}

if(isset($_GET['obj']))
{
    if($_GET['obj'] == 'orders' && isset($_GET['id']))
    {
        $href = 'index.php?obj=orders&op=read'.$filter_id;
        $table = "Заказы";
        $action = "Удаление заказа №".$_GET['id'];
        $Action = "Вы действительно хотите удалить заказ?";
        $success = "Заказ успешно удален";
        if(isset($_POST['delete']) && $_POST['delete'] == 'yes')
        {
            \core\OrdersActions::DeleteOrder($_GET['id']);
        }
    }
    if($_GET['obj'] == 'outlets' && isset($_GET['id']))
    {
        $href = 'index.php?obj=outlets&op=read';
        $table = "Точки самовывоза";
        $action = "Удаление торговой точки №".$_GET['id'];
        if(\core\OrdersActions::GetOrderByName('outlet_id', $_GET['id']))
        {
            $Action = "Невозможно удалить торговую точку    ";
            $success = "";
        }
        else
        {
            $Action = "Вы действительно хотите удалить торговую точку?";
            $success = "Торговая точка успешно удалена";
            if(isset($_POST['delete']) && $_POST['delete'] == 'yes')
            {
                \core\OutletsActions::DeleteOutlet($_GET['id']);
            }
        }
    }
}