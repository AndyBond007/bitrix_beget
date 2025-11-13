<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
use Bitrix\Main\Error;

/**
 * Класс контроллера действий компонента\
 * 
 * В настоящее время это шаблон для реализации необходимых действия на примере двух методов
 */
class DoctorsGridAjaxController extends \Bitrix\Main\Engine\Controller
{
    //Конфигурация действия
    public function configureActions(): array
    {
        return 
        [
            'deleteElement' => 
            [
                'prefilters' => [],
            ],
            'addElement' => 
            [
                'prefilters' => [],
                'postfilters' => [],
            ],
        ];
    }

    /**
     * Метод добавления нового элемента
     */
    public function addElementAction(): array
    {
        try 
        {
            //Реализация шаблона добавления нового элемента
            //Необходимо добавить првоерку требуемых полей
            $elementTitle = $this->request->get( 'elementTitle' );

            if ( empty( $elementTitle ) ) 
            {
                $this->errorCollection->add( [ new Error( 'Не передано название элемента' ) ] );
                return [];
            }

            $addResult = DoctorsTable::add(
            [
                'TITLE' => $elementTitle,
            ] );

            //Если добавление успешно
            if ( $addResult->isSuccess() ) 
            {
                $result[ 'ELEMENT_ID' ] = $addResult->getId();
            } else 
            {
                //В противном случае возвращаем ошибку
                $this->errorCollection->add( $addResult->getErrorMessages()) ;
                return [];
            }
        } catch ( \Exception $e ) 
        {
            $this->errorCollection->add( [ new Error( $e->getMessage() ) ] );
            return [];
        }
        return $result;
    }

    /**
     * Метод удаление нового элемента
     * @param int elementId Индекс элемента для удаления
     */
    public function deleteElementAction(int $elementId): array
    {
        $result = [];

        try 
        {
            $deleteResult = DoctorsTable::delete( elementId );

            if ( $deleteResult->isSuccess() ) 
            {
                return $result;
            } else 
            {
                $this->errorCollection->add( $deleteResult->getErrorMessages() );
                return [];
            }

        } catch ( \Exception $e ) 
        {
            $this->errorCollection->add( [ new Error( $e->getMessage() ) ] );
            return [];
        }
    }
}
