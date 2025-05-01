<?php
require_once '.core/index_core.php';
include './templates/header.php';
require_once './script/backup.php';
?>


<div class="container col-md-3 mx-auto">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="backup">
        <div class="form-group">
            <label for="export">Бэкап сайта и базы данных</label>
        </div>
        <div class="row px-3 py-2">
            <input type="submit" value="Бэкап" class="btn btn-primary">
        </div>
        <?php if($success_db && $success_web):?>
            <div class="row">
                <div class="col">
                    Бэкап прошел успешно. Ссылки для скачивания:
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <a href="../<?=htmlspecialchars($ZipName)?>"> База данных </a>
                </div>
                <div class="col-5">
                    <a href="../<?=htmlspecialchars($WebName)?>"> Сайт </a>
                </div>
            </div>
        <?php endif;?>
    </form>
</div>

<?php
include './templates/footer.php';
?>

