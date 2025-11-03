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

use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Models\Lists\HospitalsTable;


//Список классов таблиц на создание
$entities = [
    HospitalsTable::class
];



echo "Try to create tables\n";

//Пробегаем по списку классов таблиц и при необходимости создаем
foreach ($entities as $entity) {
    echo 'Table [' . $entity::getTableName() . '] ';
    if (!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
        Base::getInstance($entity)->createDbTable();
        echo 'created';
        $entity::fillData();
        echo " and filled with demo data\n";
    } else echo "already created\n";
}

$connection = Application::getConnection();

$tableName = 'anb_doctor_hospital';

echo 'Help table [' . $tableName . '] ';
if (!$connection->isTableExists($tableName)) {
    $connection->queryExecute("
		CREATE TABLE {$tableName} (
            HOSPITAL_ID int NOT NULL,
			DOCTOR_ID int NOT NULL,			
			PRIMARY KEY (HOSPITAL_ID, DOCTOR_ID)
		)
	");
    echo 'created';
    $connection->add($tableName, ['DOCTOR_ID' => 37, 'HOSPITAL_ID' => 1]);
    $connection->add($tableName, ['DOCTOR_ID' => 37, 'HOSPITAL_ID' => 3]);
    $connection->add($tableName, ['DOCTOR_ID' => 38, 'HOSPITAL_ID' => 1]);
    $connection->add($tableName, ['DOCTOR_ID' => 39, 'HOSPITAL_ID' => 2]);
    $connection->add($tableName, ['DOCTOR_ID' => 40, 'HOSPITAL_ID' => 2]);
    $connection->add($tableName, ['DOCTOR_ID' => 41, 'HOSPITAL_ID' => 3]);
    echo " and filled with demo data\n";
} else echo "already created\n";

echo "Create finished\n";
