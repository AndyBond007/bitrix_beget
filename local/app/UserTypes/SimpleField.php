<?php

namespace UserTypes;

use BX\DoctorBooking;
use Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
use Models\Lists\ProceduresPropertyValuesTable as ProceduresTable;

class SimpleField
{
    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE'         => 'E', // Прототип типа свойства - привязка к элементам
            'USER_TYPE'             => 'SimpleField',
            'DESCRIPTION'           => 'Привязка SimpleField', //Название нового типа свойства
            'GetPropertyFieldHtml'  => array(self::class, 'GetPropertyFieldHtml'),
            // 'GetSearchContent'      => array(self::class, 'GetSearchContent'), // метод поиска
            // 'GetAdminListViewHTML'  => array(self::class, 'GetAdminListViewHTML'),  // метод отображения значения в списке
            // 'GetPublicEditHTML'     => array(self::class, 'GetPropertyFieldHtml'), // метод отображения значения в форме редактирования
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


    public static function GetList($procedures)
    {
        $rsEnum = [];
     
        $procs = ProceduresTable::query()
            ->setSelect( [ "ID" => "ELEMENT.ID", 'NAME' => 'ELEMENT.NAME' ] )
            ->where( "ELEMENT.ID", "in", $procedures )
            ->fetchAll();
        $simpleData = [];
        foreach( $procs as $row )
        {
            $simpleData[] = $row[ 'NAME' ];
        }

        //GROUPS_ID - Администраторы, контент редакторы
        // $dbResultList = Procedures::getList([
        //         'select' => [
        //             'IBLOCK_ELEMENT_ID',
        //             'TITLE' => 'ELEMENT.NAME',
        //         ],
        //         'where' => ['']
        //     ])->Fetch();
        // while ($arResult = $dbResultList->Fetch()){
        //     $rsEnum[] = [
        //         'ID' => $arResult['ID'],
        //         //Формат отображения значений
        //         'VALUE' => $arResult['NAME'] . ' ' . $arResult['LAST_NAME'] . ' (' . $arResult['EMAIL'] . ')'
        //     ];
        // }

        return $simpleData;
    }

    public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName)
    {

        $arSettings = self::PrepareSettings($arProperty);
        //$arProperty["LINK_IBLOCK_ID"] ссылка на текущий  связаный инфоблок
        //$arProperty['IBLOCK_ID'] ссылка на инфоблонк, к кторому привязано свойство
        //$arProperty['ELEMENT_ID'] ссылка на строку данных
        // var_dump( $arProperty["LINK_IBLOCK_ID"]);
        

        //Формирование ссылки в виде номера
        //Тут мне кажется совсем не идеальный вариант
        $arVals = array();
        $arVals_ID = array();
        if (!is_array($arProperty['VALUE'])) {
            $arProperty['VALUE'] = array($arProperty['VALUE']);
        }
        foreach ($arProperty['VALUE'] as $i => $value) {
            $arVals_ID[$value] = unserialize($arProperty['VALUE'][$i]);
        }
        $temp = self::GetList($arVals_ID);
 
        $ii=0;
         foreach ($arProperty['VALUE'] as $i => $value) {
            $arVals[$value] = $temp[$ii];
            $ii=$ii+1;
        }
        
        $doctor = DoctorsTable::query()
        ->setSelect([
            'SURNAME',
            'FIRSTNAME',
            'MIDNAME',
        ])
        ->where('ELEMENT.ID', $arProperty['ELEMENT_ID'])
        ->fetch();
        

        $doctorName = $doctor['SURNAME'].' '.$doctor['FIRSTNAME'].' '.$doctor['MIDNAME'];
        $strResult ="<a href='#' onclick='BX.ready(function () { BX.DoctorBooking.showPopup(\"{$doctorName}\", {$arProperty['ELEMENT_ID']}, \"{$arVals[$arValue['VALUE']]}\", {$arVals_ID[$arValue['VALUE']]}); });'>" . $arVals[$arValue['VALUE']] . '</a>';
        return $strResult;
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

        $html = '<input name="'.$strHTMLControlName["VALUE"].'" id="'.$strHTMLControlName["VALUE"].'" value="'.htmlspecialcharsex($value["VALUE"]).'" size="5" type="text">';
        $html .= ' <span id="sp_'.md5($strHTMLControlName["VALUE"]).'_'.$key.'">'.$arItem["NAME"].'</span>';
        $html .= '<input type="button" value="Выбрать" onclick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANG.'&IBLOCK_ID='.$arProperty["LINK_IBLOCK_ID"].'&n='.$strHTMLControlName["VALUE"].'\', 600, 500);">';
        $html .= ' Количество:<input type="text" id="quan" name="'.$strHTMLControlName["DESCRIPTION"].'" value="'.htmlspecialcharsex($value["DESCRIPTION"]).'">';
        return  $html;
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return;
    }

    public static function ConvertToDB($arProperty, $value)
    {
        $return = false;
        
        if( is_array($value) && array_key_exists("VALUE", $value) && ($value['VALUE'] > 0))
        {
            $return = array(
                "VALUE" => serialize($value["VALUE"]),
                "DESCRIPTION" => serialize($value["DESCRIPTION"]),
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
                "VALUE" => unserialize($value["VALUE"]),
            );
        }
            
        if(!is_array($value["DESCRIPTION"]))
        {
            $return["DESCRIPTION"] = unserialize($value["DESCRIPTION"]);
        }

        if ($return['VALUE'] > 0):
            return $return;
        endif;
    }
}