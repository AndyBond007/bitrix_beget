<?php

namespace UserTypes;

class MyField
{
    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE'         => 'E', // Прототип типа свойства - привязка к элементам
            'USER_TYPE'             => 'MyField',
            'DESCRIPTION'           => 'Привязка к элементам с описанием', //Название нового типа свойства
            'GetPropertyFieldHtml'  => array(self::class, 'GetPropertyFieldHtml'),
            'GetSearchContent'      => array(self::class, 'GetSearchContent'), // метод поиска
            'GetAdminListViewHTML'  => array(self::class, 'GetAdminListViewHTML'),  // метод отображения значения в списке
            'GetPublicEditHTML'     => array(self::class, 'GetPropertyFieldHtml'), // метод отображения значения в форме редактирования
            'GetPublicViewHTML'     => array(self::class, 'GetPublicViewHTML'), // метод отображения значения            
            'ConvertToDB'           => array(self::class,'ConvertToDB'),
            'ConvertFromDB'         => array(self::class,'ConvertFromDB'),
        );
    }

    public static function PrepareSettings($arFields)
    {
        // return array("_BLANK" => ($arFields["USER_TYPE_SETTINGS"]["_BLANK"] == "Y" ? "Y" : "N"));
        if(is_array($arFields["USER_TYPE_SETTINGS"]) && $arFields["USER_TYPE_SETTINGS"]["_BLANK"] == "Y"){
            return array("_BLANK" =>  "Y");
        }else{
            return array("_BLANK" =>  "N");
        }
    }

    public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName)
    {
        $arSettings = self::PrepareSettings($arProperty);

$iIBlockId = intval($arProperty['SETTINGS']['IBLOCK_ID']);

        $arVals = array();
        if (!is_array($arProperty['VALUE'])) {
            $arProperty['VALUE'] = implode( ", ", array($arProperty['VALUE']));
            // $arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
        }
        // foreach ($arProperty['VALUE'] as $i => $value) {
        //     $arVals[$value] = $arProperty['DESCRIPTION'][$i];
        // }
var_dump($iIBlockId);
        $strResult = '';//$arValue['VALUE']; гдето тут ссылка
        $strResult =  '<a ' . ($arSettings["_BLANK"] == 'Y' ? 'target="_blank"' : '') . ' href="' . trim($arValue['VALUE']) . '">' . (trim($arVals[$arValue['VALUE']]) ? trim($arVals[$arValue['VALUE']]) : trim($arValue['VALUE'])) . '</a>';
        return $strResult;
    }

    public static function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName)
    {
        $arSettings = self::PrepareSettings($arProperty);
        ///Отвечает за отображение в админке
        $strResult = '';// $arValue['VALUE'];
        $strResult = '<a ' . ($arSettings["_BLANK"] == 'Y' ? 'target="_blank"' : '') . ' href="' . trim($arValue['VALUE']) . '">' . (trim($arValue['DESCRIPTION']) ? trim($arValue['DESCRIPTION']) : trim($arValue['VALUE'])) . '</a>';
        // $strResult = ($arSettings["_BLANK"] == 'Y' ? 'target="_blank"' : '') . ' href="' . trim($arValue['VALUE']);// . '">' . (trim($arValue['DESCRIPTION']) ? trim($arValue['DESCRIPTION']) : trim($arValue['VALUE'])) . '</a>';
        return $strResult;
    }

    // public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    // {
    //     return;
    // }

    public static function GetSearchContent($arProperty, $value, $strHTMLControlName)
    {
        if (trim($value['VALUE']) != '') {
            return $value['VALUE'] . ' ' . $value['DESCRIPTION'];
        }

        return '';
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $value["DESCRIPTION"] = unserialize($value["DESCRIPTION"]);

        $arItem = Array(
            "ID" => 0,
            "IBLOCK_ID" => 0,
            "NAME" => ""
        );

        if(intval($value["VALUE"]) > 0)
        {
            $arFilter = Array(
                "ID" => intval($value["VALUE"]),
                "IBLOCK_ID" => $arProperty["LINK_IBLOCK_ID"],
            );
            $arItem = \CIBlockElement::GetList(Array(), $arFilter, false, false, Array("ID", "IBLOCK_ID", "NAME"))->Fetch();
        }

        $html .= '<input name="'.$strHTMLControlName["VALUE"].'" id="'.$strHTMLControlName["VALUE"].'" value="'.htmlspecialcharsex($value["VALUE"]).'" size="5" type="text">';
        $html .= ' <span id="sp_'.md5($strHTMLControlName["VALUE"]).'_'.$key.'">'.$arItem["NAME"].'</span>';
        $html .= '<input type="button" value="Выбрать" onclick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANG.'&IBLOCK_ID='.$arProperty["LINK_IBLOCK_ID"].'&n='.$strHTMLControlName["VALUE"].'\', 600, 500);">';
        $html .= ' Количество:<input type="text" id="quan" name="'.$strHTMLControlName["DESCRIPTION"].'" value="'.htmlspecialcharsex($value["DESCRIPTION"]).'">';
        return  $html;
    }



    public static function ConvertToDB($arProperty, $value)
    {
        $return = false;
        
        if( is_array($value) && array_key_exists("VALUE", $value) && ($value['VALUE'] > 0))
        {
            $return = array(
                "VALUE" => $value["VALUE"],
                // "DESCRIPTION" => serialize($value["DESCRIPTION"]),
            );
        }    
        
        return $return; 
    }
        
    public static function ConvertFromDB($arProperty, $value)
    {
        $return = false;

        if(!is_array($value["VALUE"]))
        {
            $return = array(
                "VALUE" => $value["VALUE"],
            );
        }
            
        // if(!is_array($value["DESCRIPTION"]))
        // {
        //     $return["DESCRIPTION"] = unserialize($value["DESCRIPTION"]);
        // }

        if ($return['VALUE'] > 0):
            return $return;
        endif;
    }
}