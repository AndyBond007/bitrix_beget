<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Доктора");
$APPLICATION->SetAdditionalCSS('/doctors/style.css');

use Bitrix\Calendar\ICal\Builder\Alert;
use \Bitrix\Iblock\Iblock\Elements;
use Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
use Models\Lists\ProceduresPropertyValuesTable as ProceduresTable;
// use Models\Lists\HospitalsTable as HospitalsTable;

$APPLICATION->SetTitle('Доктора');

$doctors = []; //Массив для хранения данных по докторам
$doctor = []; //Массив для хранения данных по выбранному доктору
$procs = []; //Массив для хранения данных по процедурам

$path = trim($_GET['path'], '/'); //Путь
$action = ''; //Текущее активное действие
$doctor_name = ''; //Имя выбранного врача

//Если не пустой путь
if (!empty($path)) {
    $path_parts = explode('/', $path);
    $path_size = sizeof($path_parts);
    if ($path_size < 3) {
        if ($path_size == 2 && $path_parts[0] = 'edit') {
            $action = 'edit';
            $doctor_name = $path_parts[1];
        } else if ($path_size == 1 && in_array($path_parts[0], ['new', 'newproc'])) {
            $action = $path_parts[0];
        } else $doctor_name = $path_parts[0];
    }
}

if (!empty($doctor_name)) {
    $doctor = DoctorsTable::query()
        ->setSelect([
            '*',
            'NAME' => 'ELEMENT.NAME',
            'DETAIL_PICTURE' => 'ELEMENT.DETAIL_PICTURE',
            'SURNAME',
            'FIRSTNAME',
            'MIDNAME',
            'PROCEDURES',
            'HOSPITALS',
            'ID' => 'ELEMENT.ID',
        ])
        ->where('NAME', $doctor_name)
        ->fetch();

    $doctor_hospitals = DoctorsTable::getByPrimary(
        $doctor['ID'],
        [
            'select' => [
                '*',
                'HOSPITALS',
            ]
        ]
    )
        ->fetchObject();

    foreach ($doctor_hospitals->getHospitals() as $hospital) {
        $hospital_info['ID'] = $hospital->getId();
        $hospital_info['TITLE'] = $hospital->getTitle();
        $hospital_info['DESCRIPTION'] = $hospital->getDescription();
        $doctor['HOSPITALS'][] = $hospital_info;
    }

    if (is_array($doctor)) {
        if ($doctor['PROCEDURES']) {
            $procs = ProceduresTable::query()
                ->setSelect(['NAME' => 'ELEMENT.NAME', 'PROCLEN'])
                ->where("ELEMENT.ID", "in", $doctor['PROCEDURES'])
                ->fetchAll();
        }
    } else {
        header("Location: /doctors");
        exit();
    }
}

if (empty($doctor_name) && empty($action)) {
    $doctors = DoctorsTable::query()
        ->setSelect(['*', 'NAME' => 'ELEMENT.NAME', 'DETAIL_PICTURE' => 'ELEMENT.DETAIL_PICTURE', 'ID' => 'ELEMENT.ID', 'FIRSTNAME'])
        ->fetchAll();
}

if ($action == 'newproc') {
    if (isset($_POST['proc-submit'])) {
        unset($_POST['proc-submit']);
        if (ProceduresTable::add($_POST)) {
            header("Location: /doctors");
            exit();
        } else echo 'Произошла ошибка добавления процедуры';
    }
}

if ($action == 'new' || $action == 'edit') {
    if (isset($_POST['doctor-submit'])) {
        unset($_POST['doctor-submit']);
        if ($action == 'edit' && !empty($_POST['ID'])) {
            $ID = $_POST['ID'];
            unset($_POST['ID']);

            $_POST['IBLOCK_ELEMENT_ID'] = $ID;

            $procs = $_POST['PROCEDURES'];
            unset($_POST['PROCEDURES']);

            CIBlockElement::SetPropertyValues($ID, DoctorsTable::IBLOCK_ID, $procs, 'PROCEDURES');

            if (DoctorsTable::update($_POST['ID'], $_POST)) {
                header('Location: /doctors');
                exit();
            } else
                'Произошла ошибка обновления записи';
        }
        if ($action = 'new' && DoctorsTable::add($_POST)) {
            header('Location: /doctors');
            exit();
        } else
            'Произошла ошибка добавления доктора';
    }
    $proc_options = ProceduresTable::query()
        ->setSelect(['ID' => 'ELEMENT.ID', 'NAME' => 'ELEMENT.NAME'])
        ->fetchAll();
    if (!empty($doctor_name)) {
        $data = $doctor;
    }
}

if (isset($_POST['doctor-delete'])) {
    unset($_POST['doctor-delete']);

    if (empty($action) && !empty($doctor['ID'])) {
        if (DoctorsTable::delete($doctor['ID'])) {
            header('Location: /doctors');
            exit();
        } else 'Произошла ошибка удаления доктора';
    }
}
?>


<section class="doctors">
    <h1><a href="/doctors">Доктора</a></h1>
    <?php if (empty($action)): ?>
        <div class="add-buttons">
            <?php if (empty($doctor_name)): ?>
                <a href="/doctors/new"><button>Добавить доктора</button></a>
                <a href="/doctors/newproc"><button>Добавить процедуру</button></a>
            <?php else: ?>
                <a href="/doctors/edit/<?= $doctor_name ?>"><button>Редактировать данные доктора</button></a>
                <form method="POST">
                    <button type="submit" name="doctor-delete">Удалить доктора</button>
                </form>

            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="my_card_list">
        <?php foreach ($doctors as $doc) { ?>
            <div class="card">
                <a href="/doctors/<?= $doc["NAME"] ?>">
                    <div class="fio">
                        <table>
                            <tr>
                                <td>
                                    <?php $arFields = CFile::GetFileArray($doc["DETAIL_PICTURE"]); ?>
                                    <img src="<?= $arFields['SRC'] ?>" alt="" />
                                </td>
                                <td>
                                    <?= $doc['SURNAME'] ?>
                                    <?= $doc['FIRSTNAME'] ?>
                                    <?= $doc['MIDNAME'] ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>

    <?php if (is_array($doctor) && sizeof($doctor) > 0 && $action != 'edit'): ?>
        <div class="doctor-page">
            <?php $arFields = CFile::GetFileArray($doctor["DETAIL_PICTURE"]); ?>
            <a href="<?= $arFields['SRC'] ?>"><img src="<?= $arFields['SRC'] ?>" heigh="200" width="200" alt="" /></a>

            <h2><?= $doctor['SURNAME'] . " " . $doctor['FIRSTNAME'] . " " . $doctor['MIDNAME'] ?></h2>
            <table>
                <tr>
                    <td>
                        <h3>Процедуры:</h3>
                    </td>
                    <td>
                        <h3>Принимает:</h3>
                    </td>
                </tr>

                <tr>
                    <td style="vertical-align: top;">
                        <ul>
                            <?php foreach ($procs as $pr): ?>
                                <li><?= $pr['NAME'] ?> (длительность, мин:<?= (int)$pr['PROCLEN'] ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td style="vertical-align: top;">
                        <ul>
                            <?php foreach ($doctor['HOSPITALS'] as $hospital): ?>
                                <li><?= $hospital['TITLE'] ?><br>Описание: <?= $hospital['DESCRIPTION'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
            </table>





        </div>
    <?php endif; ?>

    <?php if ($action == 'new' || $action == 'edit'): ?>
        <form method="POST">

            <h2 style="text-align: center;">Информация о докторе</h2>
            <div class="doctor-add-form">
                <?php if (isset($data['ID'])): ?>
                    <input type="hidden" name="ID" value="<?= $data['ID'] ?>" />
                <?php endif; ?>

                <input type="text" name="NAME" placeholder="Название страницы доктора" value="<?= $data['NAME'] ?? '' ?>"
                    <?php if ($action == 'edit'): ?>readonly<?php else: ?>required minlength="3" <?php endif; ?> />
                <input type="text" name="SURNAME" placeholder="Фамилия доктора" value="<?= $data['SURNAME'] ?? '' ?>" required minlength="3" />
                <input type="text" name="FIRSTNAME" placeholder="Имя доктора" value="<?= $data['FIRSTNAME'] ?? '' ?>" required minlength="3" />
                <input type="text" name="MIDNAME" placeholder="Отчество доктора" value="<?= $data['MIDNAME'] ?? '' ?>" required minlength="3" />
                <select multiple name="PROCEDURES[]">
                    <option value="" selected disabled>Процедуры</option>
                    <?php foreach ($proc_options as $proc): ?>
                        <option value="<?= $proc['ID'] ?>"
                            <?php if (isset($data['PROCEDURES']) && in_array($proc['ID'], $data['PROCEDURES'])): ?>selected<?php endif; ?>><?= $proc['NAME'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="add-buttons">
                    <button type="submit" name="doctor-submit">Сохранить изменения</button>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <?php if ($action == 'newproc'): ?>
        <form method="POST">
            <h2 style="text-align: center;">Добавить процедуру</h2>
            <div class="doctor-add-form">
                <input type="text" name="NAME" placeholder="Название процедуры" value="<?= $data['NAME'] ?? '' ?>" required minlength="3" />
                <input type="number" min="1" name="PROCLEN" placeholder="Длительность процедуры в минутах" value="<?= $data['PROCLEN'] ?? '' ?>" required minlength="3" />
                <div class="add-buttons">
                    <button type="submit" name="proc-submit">Добавить процедуру</button>
                </div>

            </div>
        </form>
    <?php endif; ?>

</section>