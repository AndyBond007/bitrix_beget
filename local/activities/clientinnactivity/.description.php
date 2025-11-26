<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * Описание activity
 * Поля с результатами
 */
$arActivityDescription = [
    'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_NAME' ),
    'DESCRIPTION' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_DESCR' ),
    'TYPE' => 'activity',
    'CLASS' => 'ClientInnActivity',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ],
    'RETURN' => [
        //Наименование компании
        'Company' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_COMPANY' ),
            'TYPE' => 'string',
        ],
        //Полное наименование компании
        'FullCompany' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_FULL_COMPANY' ),
            'TYPE' => 'string',
        ],
        //Адрес компании
        'Address' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_COMPANY_ADDRESS' ),
            'TYPE' => 'string',
        ],
        //КПП компании
        'KPP' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_COMPANY_KPP' ),
            'TYPE' => 'string',
        ],
        //ИНН компании
        'INN' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_COMPANY_INN' ),
            'TYPE' => 'string',
        ],
        //ОГРН компании
        'OGRN' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_COMPANY_OGRN' ),
            'TYPE' => 'string',
        ],
        //ОКПО компании
        'OKPO' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_COMPANY_OKPO' ),
            'TYPE' => 'string',
        ],
        //Телефон компании
        'Phone' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_COMPANY_PHONE' ),
            'TYPE' => 'string',
        ],
        //Почтовый ящик компании
        'Email' => [
            'NAME' => Loc::getMessage( 'CLIENT_INN_ACTIVITY_DESCR_FIELD_COMPANY_EMAIL' ),
            'TYPE' => 'string',
        ],
    ],
];
