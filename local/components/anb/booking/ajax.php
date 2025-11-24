<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Models\Lists\BookingDataTable as BookingData;
use Bitrix\Main\Error;
use Bitrix\Main\Type\DateTime;

class BookingAjaxController extends \Bitrix\Main\Engine\Controller
{
    public function configureActions(): array
    {
        return [
            'addBookingRecord' => [
                'preFilters' => [
                    new \Bitrix\Main\Engine\ActionFilter\Authentication,
                ],
            ],
        ];
    }

    public function addBookingRecordAction(int $docId , int $procId, string $name, string $datetime): array
    {
        try {
            CModule::IncludeModule("iblock");


            $addResult = BookingData::add([
                'NAME' => $name,
                'DOCTOR' => $docId,
                'PROCEDURE' => $procId,
                'DATETIME' => new DateTime($datetime, "Y-m-d H:i"),
            ]);
            
            if ( $addResult ) {
                $result['BOOKING_ID'] = 100; ///Тут не работает, так как абстрактный класс, надо попробовать с DataManager DoctorsTable-> $addResult->getId();
            } else {
                $this->errorCollection->add([new Error('Ошибка добавления записи')]);
                return [];
            }
        } catch (\Exception $e) {
            $this->errorCollection->add([new Error($e->getMessage())]);
            return [];
        }

        return $result;
    }

}
