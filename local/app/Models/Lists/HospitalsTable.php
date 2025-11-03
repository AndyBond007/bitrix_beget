<?php

namespace Models\Lists; //описываем пространство имен для нашей таблицы

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\Entity;
use Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;


class HospitalsTable extends DataManager
{
    public static function getTableName(): string //вернет название таблицы
    {
        return 'anb_hospitals';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('Title'))
                ->configureRequired()
                ->configureSize(255),

            (new TextField('Description')),

            (new ManyToMany('DOCTORS', DoctorsTable::class))
                ->configureTableName('anb_doctor_hospital')
                ->configureLocalPrimary('ID', 'HOSPITAL_ID')
                ->configureRemotePrimary('IBLOCK_ELEMENT_ID', 'DOCTOR_ID'),
        ];
    }

    //Заполняем таблицу демонстрационными данными
    public static function fillData()
    {
        HospitalsTable::add(['Title' => 'Городская больница №1', 'Description' => 'Городская больница, работа по ОМС']);
        HospitalsTable::add(['Title' => 'Семейная поликлиника №2', 'Description' => 'Оказание платных услуг']);
        HospitalsTable::add(['Title' => 'Мой доктор', 'Description' => 'Оказание платных услуг, работа по ОМС']);
    }

    public static function dropTable() //метод для удаления таблицы
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $connection->dropTable(self::getTableName());
    }
}
