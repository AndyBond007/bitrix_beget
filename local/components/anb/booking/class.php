<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;

class Booking extends \CBitrixComponent implements Controllerable, Errorable
{
    use ErrorableImplementation;
    
    public function configureActions(): array
    {
        return [
            // 'addBookingRecord' => [
            //     'preFilters' => [
            //         new \Bitrix\Main\Engine\ActionFilter\Authentication,
            //     ],
            // ],
        ];
    }

    public function executeComponent(): void
    {
        // $this->includeComponentTemplate();
    }

   
    // public function addBookingRecordAction(int $bookId): array
    // {
    //     // $this->errorCollection = new ErrorCollection();
    //     // try {
    //     //     $ormClass = $this->arParams['ORM_CLASS'];
    //     //     $ormClass::delete($bookId);
    //     // } catch (Exception $e) {
    //     //     $this->errorCollection->add([new Error($e->getMessage())]);
    //     // }

    //     return [];
    // }
}
