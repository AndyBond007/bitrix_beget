<?php

namespace Models\Lists; //описываем пространство имен для нашей таблицы

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\Entity; //подключаем для работы с сущностями БД (CRUD)
use Models\AbstractIblockPropertyValuesTable;


class CustomTable extends AbstractIblockPropertyValuesTable //обязательно наследовать свою таблицу от Entity\DataManager И обязательно чтобы название класса заканчивалось на ...Table
{
    public static function getTableName(): string //вернет название таблицы
    {
        return 'custom_draft_table';
    }

    public static function getMap(): array //при создании кастомной таблицы, в ней будут созданы такие поля
    {
        return array(
            new IntegerField(
                'ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                )
            ),
            new IntegerField(
                'USER_ID',
                array(
                    'required' => true,
                )
            ),
            new DatetimeField(
                'DATE_CREATE',
                array(
                    'default_value' => new \Bitrix\Main\Type\DateTime(),
                )
            ),
            new TextField('INFO'),
        );
    }

    public static function dropTable() //метод для удаления таблицы
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $connection->dropTable(self::getTableName());
    }
}
