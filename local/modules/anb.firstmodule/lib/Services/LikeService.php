<?php

namespace Anb\FirstmoduleServices;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Web\Json;

class LikeService
{
	private const COOKIE_NAME = 'liked_users';

	public function isLiked(int $userId): bool
	{
		return in_array($userId, $this->getLikedUsersIds());
	}

	public function likeUser(int $userId): void
	{
		$likedUsers = $this->getLikedUsersIds();
		$likedUsers[] = $userId;

		$this->setLikedUsersIds(
			array_unique($likedUsers)
		);
	}

	public function dislikeUser(int $userId): void
	{
		$likedUsers = array_filter($this->getLikedUsersIds(), static fn($i) => (int)$i !== $userId);

		$this->setLikedUsersIds(
			array_unique($likedUsers)
		);
	}

	private function getLikedUsersIds(): array
	{
		try
		{
			$cookieValue = Context::getCurrent()->getRequest()->getCookie(self::COOKIE_NAME);
			if (empty($cookieValue))
			{
				return [];
			}

			$value = Json::decode($cookieValue);
			if (!is_array($value))
			{
				return [];
			}

			return $value;
		}
		catch (ArgumentException)
		{
			return [];
		}
	}

	private function setLikedUsersIds(array $likedUsers): void
	{
		Context::getCurrent()->getResponse()->addCookie(
			new Cookie(
				self::COOKIE_NAME,
				Json::encode($likedUsers),
				time() + 60 * 60 * 24 * 30 // 30 days
			)
		);
	}
}