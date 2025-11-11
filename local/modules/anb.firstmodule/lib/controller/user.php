<?php

namespace Anb\FirstmoduleController;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Anb\FirstmoduleServices\LikeService;

class User extends Controller
{
 	/**
	 * Настройка фильтров для действий
	 *
	 * @return array
	 */
	protected function getDefaultPreFilters()
	{
		return [
			new ActionFilter\Authentication(),
			new ActionFilter\HttpMethod([
				ActionFilter\HttpMethod::METHOD_POST,
			]),
			new ActionFilter\Csrf(),
		];
	}

	/**
	 * Действие для обработки лайков
	 *
	 * @param  int $likedUserId
	 *
	 * @return void
	 */
	public function likeAction(LikeService $service, int $likedUserId)
	{
		if ($likedUserId < 1)
		{
			$this->addError(new Error('Неверный ID пользователя'));

			return null;
		}

		$isLikeAction = !$service->isLiked($likedUserId);
		if ($isLikeAction)
		{
			$service->likeUser($likedUserId);
		}
		else
		{
			$service->dislikeUser($likedUserId);
		}

		return [
			'liked' => $isLikeAction,
		];
	}
}