<?php

require_once '.core/index_core.php';


$href = 'index.php?obj=orders&op=create';
$href_update = 'index.php?obj=orders&op=update&id=';
$href_delete = 'index.php?obj=orders&op=delete&id=';
$Orders = \core\OrdersActions::GetOrdersUnited();

if(isset($_GET['filter_id']))
{
    $Outlets = \core\OutletsActions::GetOutlets();
    $Massive_outlet_id = [];
    foreach($Outlets as $outlet)
    {
        $Massive_outlet_id[$outlet['outlet_id']] = "";
    }
    $Massive_outlet_id[$_GET['filter_id']] = "selected";
    $href_update = 'index.php?obj=orders&op=update&filter_id='.$_GET['filter_id'].'&id=';
    $href_delete = 'index.php?obj=orders&op=delete&filter_id='.$_GET['filter_id'].'&id=';
    $href = 'index.php?obj=orders&op=create&filter_id='.$_GET['filter_id'];
    if($_GET['filter_id'] != "")
    {
        $Orders = \core\OrdersActions::GetOrdersFiltered($_GET['filter_id']);
    }
}

