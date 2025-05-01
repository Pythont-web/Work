<?php

$filter_id = "";
if(isset($_GET['filter_id']))
{
    $filter_id = '&filter_id='.$_GET['filter_id'];
}

if(isset($_GET['obj']) && isset($_GET['op']))
{
    if($_GET['obj'] == 'orders')
    {
        if($_GET['op'] == 'read')
        {
            header('Location: orders.php?op=read'.$filter_id);
        }
        if($_GET['op'] == 'create')
        {
            header('Location: change.php?obj=orders'.$filter_id);
        }
        if($_GET['op'] == 'update' && isset($_GET['id']))
        {
            header('Location: change.php?obj=orders&id='.$_GET['id'].$filter_id);
        }
        if($_GET['op'] == 'delete' && isset($_GET['id']))
        {
            header('Location: delete.php?obj=orders&id='.$_GET['id'].$filter_id);
        }
    }
    if($_GET['obj'] == 'outlets')
    {
        if($_GET['op'] == 'read')
        {
            header('Location: outlets.php');
        }
        if($_GET['op'] == 'create')
        {
            header('Location: change.php?obj=outlets');
        }
        if($_GET['op'] == 'update' && isset($_GET['id']))
        {
            header('Location: change.php?obj=outlets&id='.$_GET['id']);
        }
        if($_GET['op'] == 'delete' && isset($_GET['id']))
        {
            header('Location: delete.php?obj=outlets&id='.$_GET['id']);
        }
    }
}
else
{
    header('Location: rolldelivery.php');
}


