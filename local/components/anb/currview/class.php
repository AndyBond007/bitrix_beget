<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
class CurrVewComponent extends CBitrixComponent
{
    /**
     * Подготавливаем входные параметры
     *
     * @param array $arParams
     *
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CURR_ID'] ??= '';
        return $arParams;
    }
    /**
     * Основной метод выполнения компонента
     *
     * @return void
     */
    public function executeComponent()
    {
        // Кешируем результат, чтобы не делать постоянные запросы к базе
        if ($this->startResultCache()) {
            $this->initResult();

            // Если ничего не найдено, отменяем кеширование
            if (empty($this->arResult)) {
                $this->abortResultCache();
                ShowError('Валюта не установлена');

                return;
            }

            $this->includeComponentTemplate();
        }
    }
    /**
     * Инициализируем результат
     *
     * @return void
     */
    private function initResult(): void
    {
        $curr_id = $this->arParams['CURR_ID'];
        if (empty($curr_id)) {
            return;
        }

        $this->arResult = [
            'CURR_ID' => $curr_id,
        ];
    }
}
