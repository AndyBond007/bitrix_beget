<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyTable;

Loader::includeModule('currency');

// Получаем список всех валют
$result = CurrencyTable::getList([
    'select' => ['CURRENCY', 'AMOUNT', 'AMOUNT_CNT', 'SORT', 'BASE'],
    'order' => ['SORT' => 'ASC']
]);

// пустой массив для типов валюты 
$arCurrList = array();

while ($currency = $result->fetch()) {
    $arCurrList[$currency['CURRENCY']] = $currency['CURRENCY'];
}
// dump($arCurrList);

$arComponentParameters = [
    'GROUPS' => [
        'CURR_PARAMS' => [
            'NAME' => 'Параметры отображения валюты',
        ],
    ],
    'PARAMETERS' => [
        'CURR_ID' => [
            'PARENT' => 'CURR_PARAMS',
            'NAME' => 'Выберите валюту',
            'TYPE' => 'LIST',
            'VALUES' => $arCurrList,
            'REFRESH' => 'Y',
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "N",
        ],
    ],
];
