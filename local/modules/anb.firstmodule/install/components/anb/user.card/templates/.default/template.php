<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$additionalUserCardContainers = $arResult['HAS_LIKE'] ? 'my-user-card__like-text--liked' : '';

?>

<div class="my-user-card">
    <?php if (isset($arResult['PERSONAL_PHOTO_SRC'])): ?>
    <div class="my-user-card__avatar">
        <img
            class="js-my-user-card-avatar-show-in-new-page"
            src="<?= $arResult['PERSONAL_PHOTO_SRC'] ?>"
            alt="<?= $arResult['NAME'] ?>"
        >
    </div>
    <?php endif; ?>
    
    <div class="my-user-card__info">
        <h2 class="my-user-card__name">
            <?= $arResult['NAME'] ?>
        </h2>
        
        <?php if ($arParams['SHOW_EMAIL'] === 'Y'): ?>
        <p class="my-user-card__email">
            <span><?= Loc::getMessage('USER_CARD_EMAIL_LABEL') ?></span>
            <span><?= $arResult['EMAIL'] ?></span>
        </p>
        <?php endif; ?>
    </div>
    <?php if ($USER->IsAuthorized()): ?>
    <div class="my-user-card__like-container">
        <span 
            class="my-user-card__like-text js-my-user-card-like <?= $additionalUserCardContainers ?>"
            data-user-id="<?= $arParams['USER_ID'] ?>"
        >
            Нравится
        </span>
    </div>
    <?php endif; ?>
</div>