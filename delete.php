<?php
require_once './logics/logic_delete.php';
?>

<?php
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


    <?php if(isset($_GET['success_delete'])):?>
        <div class="alert alert-success" role="alert">
            <?=$success?>
        </div>
    <?php else:?>
    <h1><?=$Action?></h1>

    <form method="post">
        <div class="row">
            <?php if($success != ""):?>
            <div class="col-1">
                <button class="btn btn-danger" name="delete" value="yes" type="submit">Удалить</button>
            </div>
            <?php endif;?>
            <div class="col-1">
                <a class="btn btn-primary" href=<?=$href?>>Отмена</a>
            </div>
        </div>
    </form>
    <?php endif?>
</div>

<?php
include './templates/footer.php';
?>
