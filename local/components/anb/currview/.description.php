<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => GetMessage("ANB_CURRVIEW_COMPONENT_NAME"),
    'DESCRIPTION' => GetMessage("ANB_CURRVIEW_COMPONENT_DESCRIPTION"),
    'PATH' => [
        'ID' => GetMessage("ANB_CURRVIEW_COMPONENT_PATH_ID"),
        'NAME' => GetMessage("ANB_CURRVIEW_COMPONENT_GROUP"),
    ],
];
