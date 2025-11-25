<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

use Classes\Dadata as Dadata;

/**
 * @var CMain $Application
 */
$APPLICATION->SetTitle('Использование Dadata');

$token = "be15db6e663bd80461fc9f8211a6a4b60c9210fa"; // ваш api ключ
$secret = "8be69a41ba37a0cf67c797b3fc971c1bd35cadbe"; // ваш секретный ключ
$dadata = new \Dadata\DadataClient($token, $secret);

$response = $dadata->findById("party", "7707083893");
var_dump($response[0]['value']);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>