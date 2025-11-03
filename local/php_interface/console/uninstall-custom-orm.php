<?php
if (php_sapi_name() != 'cli') {
    die();
}

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_NO_ACCELERATOR_RESET", true);
define("BX_CRONTAB", true);
define("STOP_STATISTICS", true);
define("NO_AGENT_STATISTIC", "Y");
define("DisableEventsCheck", true);
define("NO_AGENT_CHECK", true);

//Local
$_SERVER['DOCUMENT_ROOT'] = realpath('/home/bitrix/www');

//Beget
//$_SERVER['DOCUMENT_ROOT'] = realpath('/home/c/ck31758/public_html');

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Models\Lists\HospitalsTable;


//Список классов таблиц на удаление
$entities = [
    HospitalsTable::class
];
echo "Try to delete tables\n";
//Пробегаем по списку классов таблиц и при необходимости создаем
$connection = Application::getConnection();
foreach ($entities as $entity) {
    if ($connection->isTableExists($entity::getTableName())) {
        $connection->dropTable($entity::getTableName());
        echo "Table [" . $entity::getTableName() . "] deleted\n";
    };
}

//Список вспомогательных таблиц на удаление
$help_tables = [
    'anb_doctor_hospital'
];

foreach ($help_tables as $entity) {
    if ($connection->isTableExists($entity)) {
        $connection->dropTable($entity);
        echo "Table [" . $entity . "] deleted\n";
    };
}

echo "Delete finished\n";
