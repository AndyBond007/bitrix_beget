<?php

namespace Models\Lists;

use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Models\AbstractIblockPropertyValuesTable;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Models\Lists\HospitalsTable as HospitalsTable;

// /lang/en/
// <?php
// $MESS['ELEMENT_PROP_S17_ENTITY_IBLOCK_ELEMENT_ID_FIELD'] = "";
// $MESS['ELEMENT_PROP_S17_ENTITY_PROPERTY_64_FIELD'] = "";
// $MESS['ELEMENT_PROP_S17_ENTITY_PROPERTY_65_FIELD'] = "";
// $MESS['ELEMENT_PROP_S17_ENTITY_PROPERTY_66_FIELD'] = "";
// $MESS['ELEMENT_PROP_S17_ENTITY_PROPERTY_67_FIELD'] = "";

use Models\Lists\ProceduresPropertyValuesTable as ProceduresTable;

class DoctorsPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    const IBLOCK_ID = 17;

    public static function getMap(): array
    {
        return parent::getMap() + [
            'IBLOCK_ELEMENT_ID' => (new IntegerField(
                'IBLOCK_ELEMENT_ID',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_S16_ENTITY_IBLOCK_ELEMENT_ID_FIELD'))
                ->configurePrimary(true),
            'SURNAME' => (new TextField(
                'PROPERTY_64',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_S16_ENTITY_PROPERTY_64_FIELD')),
            'FIRSTNAME' => (new TextField(
                'PROPERTY_65',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_S16_ENTITY_PROPERTY_65_FIELD')),
            'MIDNAME' => (new TextField(
                'PROPERTY_66',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_S16_ENTITY_PROPERTY_66_FIELD')),
            'PROCEDURES' => (new TextField(
                'PROPERTY_67',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_S16_ENTITY_PROPERTY_67_FIELD')),

            (new ManyToMany('HOSPITALS', HospitalsTable::class))
                ->configureTableName('anb_doctor_hospital')
                ->configureLocalPrimary('IBLOCK_ELEMENT_ID', 'DOCTOR_ID')
                ->configureRemotePrimary('ID', 'HOSPITAL_ID'),
        ];
    }
}
