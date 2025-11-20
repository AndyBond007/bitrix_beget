<?php

require_once __DIR__ . '/../../vendor/autoload.php';

require_once $_SERVER["DOCUMENT_ROOT"] . '/local/app/app_loader.php';

// include_once $_SERVER["DOCUMENT_ROOT"] . '/local/app/UserTypes/MyField.php';
// Добавим обработчик события и добавим наш пользовательский класс
// AddEventHandler('iblock', 'OnIBlockPropertyBuildList', ['MyField', 'GetIBlockPropertyDescription']);

use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

// пользовательский тип для свойства инфоблока
$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'UserTypes\IBLink', // класс обработчик пользовательского типа свойства 
        'GetUserTypeDescription'
    ]
);

// пользовательский тип для свойства инфоблока
$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'UserTypes\MyField', // класс обработчик пользовательского типа свойства 
        'GetUserTypeDescription'
    ]
);

// пользовательский тип для свойства инфоблока
$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'UserTypes\SimpleField', // класс обработчик пользовательского типа свойства 
        'GetUserTypeDescription'
    ]
);