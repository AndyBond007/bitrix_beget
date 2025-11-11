<?php

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Loader;
use Anb\FirstmoduleServices\LikeService;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

class UserCardComponent extends CBitrixComponent
{
	/**
	 * Подготавливаем входные параметры
	 *
	 * @param  array $arParams
	 *
	 * @return array
	 */
	public function onPrepareComponentParams($arParams)
	{
		$arParams['USER_ID'] ??= 0;
		$arParams['SHOW_EMAIL'] ??= 'Y';

		return $arParams;
	}
	/**
	 * Основной метод выполнения компонента
	 *
	 * @return void
	 */

	public function executeComponent()
	{
		if (!Loader::includeModule('anb.firstmodule'))
		{
			ShowError('Модуль anb.firstmodule не установлен');

			return;
		}

		// кешируем результат, чтобы не делать постоянные запросы к базе
		if ($this->startResultCache())
		{
           $this->initResult();

			// в случае если ничего не найдено, отменяем кеширование
			if (empty($this->arResult))
			{
				$this->abortResultCache();
				ShowError('Пользователь не найден');

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
		$userId = (int)$this->arParams['USER_ID'];
		if ($userId < 1)
		{
			return;
		}

		$user = \Bitrix\Main\UserTable::query()
			->setSelect([
				'ID',
				'NAME',
				'EMAIL',
				'PERSONAL_PHOTO',
			])
			->where('ID', $userId)
			->fetch()
		;
		if (empty($user))
		{
			return;
		}

		$this->arResult = [
			'NAME' => $user['NAME'],
			'EMAIL' => $user['EMAIL'],
			'HAS_LIKE' => $this->isUserLiked((int)$user['ID']),
		];

		// получаем путь до аватарки, в случае если она указана
		if (!empty($user['PERSONAL_PHOTO']))
		{
			$this->arResult['PERSONAL_PHOTO_SRC'] = \CFile::GetPath($user['PERSONAL_PHOTO']);
		}
	}

 	/**
	 * Проверяем, поставил ли текущий пользователь лайк
	 *
	 * @return void
	 */
	private function isUserLiked(int $userId): bool
	{
		/**
		 * @var LikeService $service
		 */
		$service = ServiceLocator::getInstance()->get(LikeService::class);

		return $service->isLiked($userId);
	}
}