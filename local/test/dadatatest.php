<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

use Classes\Dadata as Dadata;

/**
 * @var CMain $Application
 */
$APPLICATION->SetTitle('Использование Dadata');


\Bitrix\Main\Loader::requireModule('crm');

$entityResult = \CCrmCompany::GetListEx(
    [],
    [
        'TITLE' => 'ООО "ФОРПОСТ"',
        'CHECK_PERMISSIONS' => 'N'
    ],
    false,
    false,
    [
        'ID',
        'TITLE'
    ]
);

$company_cnt = 0;
while( $entity = $entityResult->fetch() )
{
    $company_cnt++;
}
var_dump( $company_cnt );


// $token = "be15db6e663bd80461fc9f8211a6a4b60c9210fa"; // API ключ Dadata
// $secret = "8be69a41ba37a0cf67c797b3fc971c1bd35cadbe"; // Cекретный ключ Dadata
// $dadata = new \Dadata\DadataClient($token, $secret);

// //Форпост
// // $test_inn = "7743183734";
// //СВК
// $test_inn = "7717629042";
// //Сбербанк
// // $test_inn = "7707083893";
// $response = $dadata->findById("party", $test_inn);
// // var_dump($response[0]['data']);
// var_dump($response[0]['data']['name']['short_with_opf']);
// var_dump($response[0]['data']['name']['full_with_opf']);
// var_dump($response[0]['data']['address']['value']);
// var_dump($response[0]['data']['kpp']);
// var_dump($response[0]['data']['inn']);
// var_dump($response[0]['data']['ogrn']);
// var_dump($response[0]['data']['okpo']);
// var_dump($response[0]['phones'][0]);
// var_dump($response[0]['emails'][0]);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>