<?php

namespace Classes;

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Bitrix\Crm\Service\Container;
use CIBlockElement;
use \Bitrix\Crm;
use Bitrix\Main\Type\DateTime;

class MyEvent
{
    
    public static function onElementAfterUpdate(&$arFields) 
    {
        // Указываем ID инфоблока
        $iblockId = 20;
        
        if ( $iblockId == $arFields[ 'IBLOCK_ID' ] )
        {
            $cur_time =  new DateTime();

            //Получаю данные по сделке в измененной сроке данных таблицы по индексу
            $db_props = CIBlockElement::GetProperty( $iblockId, $arFields[ 'ID' ], array( "sort" => "asc" ), Array( "CODE"=>"SDELKA" ) )->Fetch();
            //Если данные получены
            if ( $db_props )
            {   //Определен ID сделки
                $deal_id = $db_props[ 'VALUE' ];
                //Получаем сумму
                $db_props = CIBlockElement::GetProperty( $iblockId, $arFields[ 'ID' ], array( "sort" => "asc" ), Array( "CODE"=>"SUMM" ) )->Fetch();
                $deal_summ = $db_props[ 'VALUE' ];
                //Получаем Ответственного
                $db_props = CIBlockElement::GetProperty( $iblockId, $arFields[ 'ID' ], array( "sort" => "asc" ), Array( "CODE"=>"WORKER" ) )->Fetch();
                $assigned_by = $db_props[ 'VALUE' ];

                //Если указана сумма
                if ( $deal_summ )
                {
                    //Подключаем модуль CRM
                    Loader::includeModule( 'crm' );

                    //Получаем сделки
                    $dealFactory = Container::getInstance()->getFactory( \CCrmOwnerType::Deal );
                    //Получаем требуемую сделку
                    $existedDealItem = $dealFactory->getItem( $deal_id );

                    //Проверяем, если устанавливаемые поля равны установленным - прерываемся
                    if ( ( $existedDealItem->get( 'OPPORTUNITY' ) != $deal_summ ) ||
                         ( $existedDealItem->get( 'TITLE' ) != $arFields[ 'NAME' ] ) ||
                         ( $existedDealItem->get( 'ASSIGNED_BY_ID' ) != $assigned_by ) ) {

                        file_put_contents( $_SERVER[ 'DOCUMENT_ROOT' ] . '/log_e_from_iblock.txt', 
                            $cur_time . "\n", FILE_APPEND );

                        file_put_contents( $_SERVER[ 'DOCUMENT_ROOT' ] . '/log_e_from_iblock.txt', 
                            'сработал onElementAfterUpdate на таблицу Заявок' . "\n", FILE_APPEND );                            
                    
                        //Естанавливаем новые параемтры сделки
                        //Сумму
                        $existedDealItem->set( 'OPPORTUNITY', $deal_summ );
                        //Название
                        $existedDealItem->set( 'TITLE', $arFields[ 'NAME' ] );
                        //Ответственного
                        $existedDealItem->set( 'ASSIGNED_BY_ID', $assigned_by );
                        //Сохраняем изменения
                        // $existedDealItem->save(); # Сохранение внесенные в Item изменений без выполнения проверок

                        $dealUpdateOperation = $dealFactory->getUpdateOperation( $existedDealItem );
                        $updateResult = $dealUpdateOperation->launch();

                        file_put_contents( $_SERVER[ 'DOCUMENT_ROOT' ].'/log_e_from_iblock.txt',
                            'Обновяем сделку [' . $deal_id . '] в CRM после onElementAfterUpdate на таблицу Заявок' . "\n", FILE_APPEND );
                    }
                }
            }
        }
    }

    public static function onElementAfterUpdateCRM(&$arFields) 
    {
        //Берем текущее время для записи журнала
        $cur_time =  new DateTime();

        // Указываем ID инфоблока
        $iblockId = 20;
        //Подключаем модуль IBlock
        Loader::includeModule('iblock');


        $sort = [ 'ID' => 'ASC' ];
        $filter = [ 'IBLOCK_ID' => $iblockId, 'PROPERTY_SDELKA' => $arFields[ 'ID' ] ]; 
        $select = [ 'ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_SUMM', 'PROPERTY_SDELKA', 'PROPERTY_WORKER' ];
        //если элементов несколько с таким значением - это выведет первый
        $nTopCount = false;
        //$nTopCount = ['nTopCount' => 1]; можно еще так ограничить
        $el = CIBlockElement::GetList( $sort, $filter, false, $nTopCount, $select );
        // Пробегаем по всем элементам таблицы заявок
        while( $ob = $el->GetNextElement() ) {
            $elFields = $ob->GetFields();

            $summ_item = explode( '|', $elFields['PROPERTY_SUMM_VALUE'])[0];                      
            //Если значения не одинаковые, обновляем
            if ( ( $summ_item != $arFields['OPPORTUNITY'] ) ||
                 ( $elFields['PROPERTY_SDELKA_VALUE'] != $arFields['ID'] ) ||
                 ( $elFields['NAME'] != $arFields['TITLE'] ) ||
                 ( $elFields['PROPERTY_WORKER_VALUE'] != $arFields['ASSIGNED_BY_ID'] ) ) {
                file_put_contents( $_SERVER[ 'DOCUMENT_ROOT' ] . '/log_e_from_CRM.txt', 
                    $cur_time . "\n", FILE_APPEND );

                $ell = new CIBlockElement;

                //Готовим массив свойств для обновления
                $PROP = array();
                $PROP['SDELKA'] = $arFields[ 'ID' ];
                $PROP['SUMM'] = "".$arFields['OPPORTUNITY'];  
                $PROP['WORKER'] = $arFields['ASSIGNED_BY_ID'];  

                //Готовим массив для обновления
                $arLoadProductArray = Array(
                    // "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                    "IBLOCK_ID" => $iblockId,
                    "IBLOCK_SECTION" => false,           
                    "NAME" => $arFields[ 'TITLE' ],
                    "PROPERTY_VALUES"=> $PROP,
                );
                $order_id = $elFields['ID'];  
                $res = $ell->Update($order_id, $arLoadProductArray);
                if ( $res )
                    file_put_contents( $_SERVER[ 'DOCUMENT_ROOT' ] . '/log_e_from_CRM.txt', 
                        'Обновили данные по заявке [' . $order_id . '] в таблице заявок' . "\n", FILE_APPEND );
            }
        }
    }
}