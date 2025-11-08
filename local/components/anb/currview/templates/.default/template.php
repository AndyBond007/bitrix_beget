<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyTable;
use Bitrix\Main\Localization\Loc;

Loader::includeModule('currency');

$currencyData = CurrencyTable::getById($arResult['CURR_ID'])->fetch();

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */
?>

<div class="my-curr-card">

    <?php if ($currencyData): ?>
        <div class="curr_text">Курс валюты <?= $currencyData['CURRENCY'] ?> </div>
        <h2 class="my-curr_value">
            <?= $currencyData['AMOUNT'] ?>

        <?php else: ?>
            <div>Валюта не найдена </div>
        <?php endif; ?>
</div>