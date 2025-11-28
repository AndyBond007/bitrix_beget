<?php

require_once __DIR__ . '/../../vendor/autoload.php';

require_once $_SERVER["DOCUMENT_ROOT"] . '/local/app/app_loader.php';

// include_once $_SERVER["DOCUMENT_ROOT"] . '/local/app/UserTypes/MyField.php';
// Добавим обработчик события и добавим наш пользовательский класс
// AddEventHandler('iblock', 'OnIBlockPropertyBuildList', ['MyField', 'GetIBlockPropertyDescription']);

//-------------------------------------------------------------------------
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;

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
//-------------------------------------------------------------------------
// пользовательский тип для свойства инфоблока
$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'UserTypes\MyField', // класс обработчик пользовательского типа свойства 
        'GetUserTypeDescription'
    ]
);
//-------------------------------------------------------------------------
// пользовательский тип для свойства инфоблока
$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'UserTypes\SimpleField', // класс обработчик пользовательского типа свойства 
        'GetUserTypeDescription'
    ]
);
//-------------------------------------------------------------------------
//Подключение JS doctor_booking
CJSCore::RegisterExt('doctor_booking',
  array(
    'js' => '/local/js/doctor_booking.js',
    'lang' => '/local/lang/' . LANGUAGE_ID . '/doctor_booking.js.php',
    'css' => '/local/css/doctor_booking.css',
    'rel' => array(
      'ajax',
      'popup'
    )
  )
);

CJSCore::Init('doctor_booking');
//-------------------------------------------------------------------------

// use Bitrix\Main\Page\Asset;
// use \Bitrix\Main\Application;
// // получаем url хита
// $context = Application::getInstance()->getContext();
// $request = $context->getRequest();
// $rDir = $request->getRequestedPageDirectory();

// // проверяем на директорию и инициализируем функцию объекта createButton()
// if($rDir == '/stream/') {
//     $asset = Asset::getInstance();
//     $asset->addString('<script>BX.ready(function () { BX.DoctorBooking.helloWorld(); });</script>');
// }



$eventManager = EventManager::getInstance();
//На изменение строки Заявки
$eventManager->addEventHandler("iblock", "OnAfterIBlockElementUpdate", ['Classes\MyEvent',
  'onElementAfterUpdate']);
//На добавление строки заявки подключаем евент от обновления
$eventManager->addEventHandler("iblock", "OnAfterIBlockElementAdd", ['Classes\MyEvent',
'onElementAfterUpdate']);
//На удаление заявки не делаю, это не логично

//На изменение сделки
$eventManager->addEventHandler("crm", "OnAfterCrmDealUpdate", ['Classes\MyEvent',
  'onElementAfterUpdateCRM']);
//На создание сделки
$eventManager->addEventHandler("crm", "OnAfterCrmDealAdd", ['Classes\MyEvent',
  'onElementAfterAddCRM']);
//На удаление сделки
$eventManager->addEventHandler("crm", "OnAfterCrmDealDelete", ['Classes\MyEvent',
  'onElementAfterDeleteCRM']);