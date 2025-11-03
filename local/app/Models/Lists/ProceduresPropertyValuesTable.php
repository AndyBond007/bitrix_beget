<?php

namespace Models\Lists;

use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Models\AbstractIblockPropertyValuesTable;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;



// <?php
// $MESS['ELEMENT_PROP_S16_ENTITY_IBLOCK_ELEMENT_ID_FIELD'] = "";
// $MESS['ELEMENT_PROP_S16_ENTITY_PROPERTY_68_FIELD'] = "";

class ProceduresPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    const IBLOCK_ID = 16;
}
