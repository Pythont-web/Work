<?php
require_once  './logics/logic_change.php';

include './templates/header.php';
?>

<div class="container px-4">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href=<?=$href?>><?=$table?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?=$action?>
            </li>
        </ol>
    </nav>

    <h1><?=$Action?></h1>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?=$action_type?>">

        <?php if(isset($_GET['obj']) && $_GET['obj'] == 'orders'):?>
        <div class="row">
            <div class="col">
                <label for="name">Имя заказчика</label>
                <input type="text" name ="name" class="form-control" placeholder="Иван" id="name" value=<?=htmlspecialchars($value_name)?>>
            </div>
            <div class="col">
                <label for="order_composition">Состав заказа</label>
                <textarea type="text" name ="order_composition" class="form-control" placeholder="Ролл с курицей" id="order_composition"><?=htmlspecialchars($value_order_composition)?></textarea>
            </div>
            <div class="col">
                <label for="price">Стоимость заказа, руб.</label>
                <input type="number" name ="price" class="form-control" placeholder="1000" id="price"
                       value=<?=htmlspecialchars($value_price)?>>
            </div>
            <div class="col">
                <label for="img">Фотография заказчика</label>
                <input type="file" name="img" class="form-control" id="img">
            </div>
        </div>
        <label for="order_id">Торговая точка самовывоза</label>
        <div class="col-4 py-2">
            <div class="input-group">
                <select name ="outlet_id" class="form-control" id="order_id">
                    <option value="" selected>Выберите торговую точку</option>
                    <?php foreach($Outlets as $outlet):?>
                        <option value=<?=$outlet['outlet_id']?> <?=$Massive_outlet_id[$outlet['outlet_id']] ?>><?=htmlspecialchars($outlet['outlet_address'])?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <?php elseif (isset($_GET['obj']) && $_GET['obj'] == 'outlets'):?>
        <div class="row">
            <div class="col">
                <label for="outlet_address">Адрес точки самовывоза</label>
                <textarea type="text" name ="outlet_address" class="form-control" placeholder="ул. Ленина" id="outlet_address"><?=htmlspecialchars($value_outlet_address)?></textarea>
            </div>
        </div>
        <?php endif;?>

        <?php if(count($errors) > 0):?>
            <div class="py-2">
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <?php foreach ($errors as $error):?>
                            <li>
                                <?=$error?>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        <?php endif?>

        <?php if(isset($_GET['success_add'])):?>
            <div class="alert alert-success" role="alert">
                <?=$success_add?>
            </div>
        <?php endif;?>

        <?php if(isset($_GET['success_update'])):?>
            <div class="alert alert-success" role="alert">
                <?=$success_update?>
            </div>
        <?php endif;?>

        <div class="col-4 py-2">
            <button class="btn btn-primary" type="submit">Отправить</button>
        </div>
    </form>
</div>

<?php
include './templates/footer.php';
?>
