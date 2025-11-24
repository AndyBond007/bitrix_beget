<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

use Models\Lists\BookingDataTable as BookingData;
use Bitrix\Main\Type\DateTime;

/**
 * @var CMain $Application
 */
$APPLICATION->SetTitle('Проверка таблицы');

BookingData::add([
                'NAME' => 'test1234',
                'DOCTOR' => 38,
                'PROCEDURE' => 34,
                // 'DATETIME' => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
            ]);
            
    $books = BookingData::getList([
                'select' => [
                    'IBLOCK_ELEMENT_ID',
                    'TITLE' => 'ELEMENT.NAME',
                    'DOCTOR',
                    'PROCEDURE',
                    'DATETIME',
                ],
            ]);
            $deals = [];
while ($deal = $books->fetch()) {
    $deals[] = $deal;
}

dump($deals);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>