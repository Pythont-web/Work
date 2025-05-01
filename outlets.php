<?php
require_once '.core/index_core.php';

$Outlets = \core\OutletsActions::GetOutlets();

include './templates/header.php';
?>

<div class ="container text-center py-4 px-4">
    <table class="table table-hover table-responsive">
        <thead>
        <tr>
            <th scope="col">id</th>
            <th scope="col">Торговая точка самовывоза</th>
            <th colspan="2"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($Outlets as $Outlet): ?>
            <tr>
                <td><?=htmlspecialchars($Outlet['outlet_id'])?></td>
                <td><a href="index.php?obj=orders&op=read&filter_id=<?=htmlspecialchars($Outlet['outlet_id'])?>"><?=htmlspecialchars($Outlet['outlet_address'])?></a></td>
                <td>
                    <a class="btn btn-primary" type="button" id="edit" href="index.php?obj=outlets&op=update&id=<?=htmlspecialchars($Outlet['outlet_id'])?>">Редактировать</a>
                </td>
                <td>
                    <a class="btn btn-danger delete" href="index.php?obj=outlets&op=delete&id=<?=htmlspecialchars($Outlet['outlet_id'])?>">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class ="container px-4">
    <a class="btn btn-primary" type="button" href="index.php?obj=outlets&op=create">Добавить торговую точку</a>
</div>