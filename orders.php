<?php
require_once './logics/logic_orders.php';

include './templates/header.php';
?>

<?php if(isset($_GET['filter_id'])):?>
<div class ="container">
    <form method="get">
        <div class="col-4 py-2 px-4">
            <label for="order_id">Фильтр по торговой точке</label>
            <div class="input-group">
                <select name ="filter_id" class="form-control" id="order_id">
                    <option value="" selected>Выберите торговую точку</option>
                    <?php foreach($Outlets as $outlet):?>
                        <option value=<?=$outlet['outlet_id']?> <?=$Massive_outlet_id[$outlet['outlet_id']] ?>><?=htmlspecialchars($outlet['outlet_address'])?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="row py-2">
                <div class="col-4">
                    <button class="btn btn-primary" type="submit">Применить фильтр</button>
                </div>
                <div class="col-4">
                    <a class="btn btn-danger" href="index.php?obj=orders&op=read&filter_id=">Сбросить фильтр</a>
                </div>
                <div class="col-4">
                    <a class="btn btn-danger" href="index.php?obj=outlets&op=read">Вернуться</a>
                </div>
            </div>

        </div>
    </form>
</div>
<?php endif?>

<div class ="container text-center py-4 px-4">
    <table class="table table-hover table-responsive">
        <thead>
        <tr>
            <th scope="col">id</th>
            <th scope="col">Фотография заказчика</th>
            <th scope="col">Имя заказчика</th>
            <th scope="col">Торговая точка самовывоза</th>
            <th scope="col">Заказ</th>
            <th scope="col">Стоимость</th>
            <th colspan="2"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($Orders as $Order): ?>
            <tr>
                <td><?=htmlspecialchars($Order['order_id'])?></td>
                <th scope="row"><img src="catalog_images/<?=htmlspecialchars($Order['img_path'])?>" alt="img" class ="ImgStyle" </th>
                <td><?=htmlspecialchars($Order['name'])?></td>
                <td><?=htmlspecialchars($Order['outlet_address'])?></td>
                <td><?=htmlspecialchars($Order['order_composition'])?></td>
                <td><?=htmlspecialchars($Order['price'])?></td>
                <td>
                    <a class="btn btn-primary" type="button" id="edit" href=<?=$href_update.htmlspecialchars($Order['order_id'])?>>Редактировать</a>
                </td>
                <td>
                    <a class="btn btn-danger delete" href=<?=$href_delete.htmlspecialchars($Order['order_id'])?>>Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class ="container px-4">
    <a class="btn btn-primary" type="button" href=<?=$href?>>Добавить заказ</a>
</div>

<?php
include './templates/footer.php';
?>
