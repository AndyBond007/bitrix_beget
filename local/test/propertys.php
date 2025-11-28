<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

use Bitrix\Iblock\IblockTable;
use CIBlockElement;
use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

Loader::includeModule( 'crm' );

  //Получаем сделки
  $dealFactory = Container::getInstance()->getFactory( \CCrmOwnerType::Deal );
  //Получаем требуемую сделку
  $existedDealItem = $dealFactory->getItem( 5 );

  // if ( ( $existedDealItem->get( 'OPPORTUNITY' ) != $deal_summ ) ||
  //       ( $existedDealItem->get( 'TITLE' ) != $arFields[ 'NAME' ] ) ||
  //       ( $existedDealItem->get( 'ASSIGNED_BY_ID' ) != $assigned_by ) ) {
  //Естанавливаем новые параемтры сделки
  //Сумму

      $existedDealItem->set( 'OPPORTUNITY', "777" );//.$deal_summ );
      //Название
      $existedDealItem->set( 'TITLE', "THHH" );
      //Ответственного
      $existedDealItem->set( 'ASSIGNED_BY_ID', 1 );
      //Сохраняем изменения
      // $existedDealItem->save(); # Сохранение внесенные в Item изменений без выполнения проверок

      $dealUpdateOperation = $dealFactory->getUpdateOperation( $existedDealItem );
      $updateResult = $dealUpdateOperation->launch();


// $sort = ['ID' => 'ASC'];
// $filter = ['IBLOCK_ID' => 20, 'PROPERTY_SDELKA' =>5]; //тут число еще а не строку попробуйте, хотя это не должно никак повлиять
// $select = ['ID', 'IBLOCK_ID', 'PROPERTY_SUMM'];
// //если элементов несколько с таким значением - это выведет первый
// $nTopCount = false;
// //$nTopCount = ['nTopCount' => 1]; можно еще так ограничить
// $el = CIBlockElement::GetList($sort, $filter, false, $nTopCount, $select);
// while($ob = $el->GetNextElement()){
//   $arFields = $ob->GetFields();

//       $ell = new CIBlockElement;

//         $PROP = array();
//        $PROP['SDELKA'] = 5;
//         $PROP['SUMM'] = "777";  // свойству с кодом 12 присваиваем значение "Белый"
//         $PROP['WORKER'] = 4;        // свойству с кодом 3 присваиваем значение 38

//         $arLoadProductArray = Array(
//             // "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
//             "IBLOCK_ID" => 20,
//             "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
//             "NAME" => "FUCK",
//             "PROPERTY_VALUES"=> $PROP,
//         );
//         $PRODUCT_ID = $arFields['ID'];  // изменяем элемент с кодом (ID) 2


//         $res = $ell->Update($PRODUCT_ID, $arLoadProductArray);


//    // $PROPERTY_CODE = "SUMM";  // код свойства
//    // $PROPERTY_VALUE = "777";  // значение свойства
//    // // Установим новое значение для данного свойства данного элемента
//    // $dbr = CIBlockElement::GetList(array(), array("=ID"=>$arFields['ID']), false, false, array("ID", "IBLOCK_ID"));
//    // if ($dbr_arr = $dbr->Fetch())
//    // {
//    //    $IBLOCK_ID = 20;
//    //    CIBlockElement::SetPropertyValues($arFields['ID'], $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
//    // }


//   var_dump($arFields);
// }





// $propsDbres = \CIBlockElement::GetProperty( 20, 114, "sort", "asc", array(">ID" => 1));
//    $i = 0;
//    while ($prop = $propsDbres->GetNext()) {
//       $i = !isset($element['PROPS'][$prop['CODE'
//       ]]) ? 0 : $i+1;
//       $element['PROPS'][$prop['CODE']]['NAME'] = $prop['NAME'];
//       $element['PROPS'][$prop['CODE']]['TYPE'] = $prop['PROPERTY_TYPE'];
//       $element['PROPS'][$prop['CODE']]['ACTIVE'] = $prop['ACTIVE'];
//       $element['PROPS'][$prop['CODE']]['VALUES'][$i] = [
//          'VALUE' => $prop['VALUE'],
//          'DESCRIPTION' => $prop['DESCRIPTION'],
//       ];
//       if ($prop['PROPERTY_TYPE'] == 'F')
//          $element['PROPS'][$prop['CODE']]['VALUE'][$i]['PATH'] = \CFile::GetPath(intval($prop['VALUE']));
//         // var_dump( $element );
//    }


//    $db_props = CIBlockElement::GetProperty(20, 113, Array("sort"=>"asc"), Array("NAME"=>"Сделка"));
// var_dump( $db_props );

   require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>