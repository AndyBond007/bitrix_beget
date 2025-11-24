<?php

namespace Models\Lists; //описываем пространство имен для нашей таблицы

use Models\AbstractIblockPropertyValuesTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;


class BookingDataTable extends AbstractIblockPropertyValuesTable
{

    const IBLOCK_ID = 18;
    
	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() : array
	{
		return parent::getMap() + [
			'IBLOCK_ELEMENT_ID' => new IntegerField(
				'IBLOCK_ELEMENT_ID',
				[]
			),
			'DOCTOR' => new IntegerField(
				'DOCTOR',
				[]
			),
			'PROCEDURE' => new IntegerField(
				'PROCEDURE',
				[]
			),
			'DATETIME' => new TextField(
				'DATETIME',
				[]
			),
		];
	}
}