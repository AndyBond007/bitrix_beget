<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\UI\Buttons\Color;
use Bitrix\Main\Error;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorableImplementation;

/**
 * Основной класс компонента отображения таблицы докторов
 */
class DoctorsGrid extends \CBitrixComponent implements Controllerable, Errorable
{
    use ErrorableImplementation;

    /**
     * Идентификатор таблицы 
     */
    protected const GRID_ID = 'DOCTORS_GRID';
    
    /**
     * Метод для обработки получаемых параметров компонента
     * @param $arParams Входящие параметры
     * @return array Парамеры
     */    
    public function onPrepareComponentParams($arParams): array
    {
        //Дополнительная обработка параметров
        $arParams[ 'ELEMENT_PREFIX' ] = strtolower( $arParams[ 'ELEMENT_PREFIX' ] );
        return $arParams;
    }

    /**
     * Метод для формирования параметров компонента, доступных из аякс
     * @return array Парамеры
     */
    public function listKeysSignedParameters(): array
    {
        return 
        [
            'ORM_CLASS',
        ];
    }

    /**
     * Конфигурация действия (префильтры и т.д.)
     * @return array Конфигурация
     */
    public function configureActions(): array
    {
        return 
        [
            'deleteElement' => 
            [
                'preFilters' => 
                [
                    new \Bitrix\Main\Engine\ActionFilter\Authentication,
                ],
            ],
            'addElement' => [],
        ];
    }

    /**
     * Метод для формирования действий компонента
     * @param array $fields Доступные поля
     * @return array Массив действий
     */      
    private function getElementActions( array $fields ): array
    {
        return 
        [
            [
                //Действие по двойному клику по строке (открытие элемента в новом окне)
                //Формируем ссылку и текст всплывающей подсказки
                'onclick' => "window.open( '{$SITE_DIR}/doctors/{$fields['TITLE']}')", 
                'text' => Loc::getMessage( 'DOCTORS_GRID_OPEN_DOCTOR', 
                [
                    '#ELEMENT_NAME#' => $fields[ 'SURNAME' ],
                ]),
                'default' => true,
            ],
            [
                //Удаление элемента таблицы
                'onclick' => sprintf( 'BX.Anb.DoctorsGrid.deleteDoctor(%d)', $fields[ 'IBLOCK_ELEMENT_ID' ] ),
                'text' => Loc::getMessage( 'DOCTORS_GRID_DELETE' ),
                'default' => true,
            ],
            [
                //Удаление элемента таблицы
                'onclick' => sprintf( 'BX.Anb.DoctorsGrid.deleteDoctorViaAjax(%d)', $fields[ 'IBLOCK_ELEMENT_ID' ] ),
                'text' => Loc::getMessage('DOCTORS_GRID_DELETE') . ' через AJAX',
                'default' => true,
            ],
        ];
    }

    /**
     * Метод добавления тестовой записи в таблицу
     * @param array $elementData Данные для добавления тестовой записи
     * @return array ID
     */ 
    public function addTestElementAction( array $elementData ): array
    {
        $newElementData = 
        [
            'TITLE' => $this->arParams[ 'ELEMENT_PREFIX' ] . $elementData[ 'elementTitle' ] ?? '',
            'DATE' => DateTime::createFromText( $elementData[ 'Date' ] ?? '' ),
        ];

        $addResult = DoctorsTable::add( $newElementData );
        if ( !$addResult->isSuccess() ) 
        {
            $this->errorCollection->add( [ new Error( 'Не удалось создать запись') ] );
            return [];
        }

        $newElementId = $addResult->getId();
        $newElement = DoctorsTable::getByPrimary( $newElementId )->fetchObject();

        //Шаблон для добавления связей
        // $authorIds = $elementData[ 'authors' ];
        // foreach ( $authorIds as $authorId ) 
        // {
        //     $author = AuthorTable::getByPrimary($authorId)->fetchObject();
        //     if ($author) {
        //         $newElement->addToAuthors($author);
        //     }
        // }
        // $updateResult = $newElement->save();
        // if ( !$updateResult->isSuccess() ) 
        // {
        //     $this->errorCollection->add( [ new Error( 'Не удалось добавить авторов')]);
        //     return [];
        // }

        return
        [
            'NEW_ELEMENT_ID' => $newElementId
        ];
    }

    /**
     * Метод формирования заголовков таблицы
     * @return array Массив заголовков
     */ 
    private function getHeaders(): array
    {
        return 
        [
            [
                'id' => 'IBLOCK_ELEMENT_ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
            ],
            [
                'id' => 'SURNAME',
                'name' => Loc::getMessage( 'DOCTORS_GRID_DOCTOR_SURNAME_LABEL' ),
                'sort' => 'SURNAME',
                'default' => true,
            ],
            [
                'id' => 'FIRSTNAME',
                'name' => Loc::getMessage( 'DOCTORS_GRID_DOCTOR_FIRSTNAME_LABEL' ),
                'sort' => 'FIRSTNAME',
                'default' => true,
            ],
            [
                'id' => 'MIDNAME',
                'name' => Loc::getMessage( 'DOCTORS_GRID_DOCTOR_MIDNAME_LABEL' ),
                'sort' => 'MIDNAME',
                'default' => true,
            ],
        ];
    }

    /**
     * Метод формирования дополнительных кнопок
     * @return array Массив заголовков
     */     
    protected function getButtons(): array
    {
        return [
            [
                'click' => 'redirectToExcel', // 'click' - метод обработчик в js, 'link' - ссылка
                'text' => Loc::getMessage( 'DOCTORS_GRID_EXPORT_XLSX_BUTTON' ),
                'color' => Color::PRIMARY,
            ],
            [
                'link' => '/stream/',
                'text' => Loc::getMessage( 'DOCTORS_GRID_GO_TO_LIVE_STREAM' ),
                'color' => Color::SECONDARY,
            ],
            // [
            //     'click' => 'BX.Anb.DoctorsGrid.addBook',
            //     'text' => Loc::getMessage( 'DOCTORS_GRID_ADD_ELEMENT' ),
            //     'color' => Color::PRIMARY_DARK,
            // ],
            // [
            //     'click' => 'BX.Anb.DoctorsGrid.createTestElementViaModule',
            //     'text' => Loc::getMessage( 'DOCTORS_GRID_ADD_TEST_ELEMENT' ),
            //     'color' => Color::DANGER_DARK,
            // ],
        ];
    }

    /**
     * Основной метод компонента
     */
    public function executeComponent(): void
    {
        //Если из необходимо выполнить экспорт - меняем шаблон 
        if ( $this->request->get( 'EXPORT_MODE' ) == 'Y') 
        {
            $this->setTemplateName( 'excel' );
        }

        //Заполняем данные
        $this->arResult[ 'BUTTONS' ] = $this->getButtons();
        //Готовим данные таблицы
        $this->prepareGridData();

        $this->includeComponentTemplate();
    }

    private function prepareGridData(): void
    {
        //Заполняем заголовки
        $this->arResult[ 'HEADERS' ] = $this->getHeaders();
        //Устанавливаем идентификатор фильтров (и грида)
        $this->arResult[ 'FILTER_ID' ] = self::GRID_ID;

        //Создаем опции грида
        $gridOptions = new GridOptions( $this->arResult[ 'FILTER_ID' ] );
        //Устанавливаем используемые колонки
        $this->arResult[ 'USED_HEADERS' ] = $gridOptions->getUsedColumns( $this->arResult[ 'HEADERS' ] );
        //Формируем параметры навигации
        $navParams = $gridOptions->getNavParams();
        $nav = new PageNavigation( $this->arResult[ 'FILTER_ID' ] );

        $nav->allowAllRecords( true )
            ->setPageSize( $navParams[ 'nPageSize' ] )
            ->initFromUri();

        //Настройка фильтра
        $filterOption = new FilterOptions( $this->arResult[ 'FILTER_ID' ] );
        $filterData = $filterOption->getFilter([]);
        $filter = $this->prepareFilter( $filterData );

        //Сортировка
        $sort = $gridOptions->getSorting(
        [
            'sort' => 
            [
                'IBLOCK_ELEMENT_ID' => 'DESC',
            ],
            'vars' => 
            [
                'by' => 'by',
                'order' => 'order',
            ],
        ]);

        $doctorIdsQuery = DoctorsTable::query()
            ->setSelect(['IBLOCK_ELEMENT_ID'])
            ->setFilter($filter)
            ->setLimit($nav->getLimit())
            ->setOffset($nav->getOffset())
            ->setOrder($sort['sort'])
        ;

        $countQuery = DoctorsTable::query()
            ->setSelect(['IBLOCK_ELEMENT_ID'])
            ->setFilter($filter)
        ;
        $nav->setRecordCount($countQuery->queryCountTotal());

        $doctorIds = array_column($doctorIdsQuery->exec()->fetchAll() ?? [], 'IBLOCK_ELEMENT_ID');

        if (!empty($doctorIds)) {
            $books = DoctorsTable::getList([
                'filter' => ['IBLOCK_ELEMENT_ID' => $doctorIds] + $filter,
                'select' => [
                    'IBLOCK_ELEMENT_ID',
                    'TITLE' => 'ELEMENT.NAME',
                    'SURNAME',
                    'FIRSTNAME',
                    'MIDNAME',
                    // 'YEAR',
                    // 'PAGES',
                    // 'PUBLISH_DATE',
                    // 'AUTHOR_ID' => 'AUTHORS.ID',
                    // 'AUTHOR_FIRST_NAME' => 'AUTHORS.FIRST_NAME',
                    // 'AUTHOR_LAST_NAME' => 'AUTHORS.LAST_NAME',
                    // 'AUTHOR_SECOND_NAME' => 'AUTHORS.SECOND_NAME',
                ],
                'order' => $sort['sort'],
            ]);

            $this->arResult['GRID_LIST'] = $this->prepareGridList($books);
        } else {
            $this->arResult['GRID_LIST'] = [];
        }

        $this->arResult['NAV'] = $nav;
        $this->arResult['UI_FILTER'] = $this->getFilterFields();
    }

    private function prepareFilter(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter['%SURNAME'] = $filterData['FIND'];
        }

        if (!empty($filterData['SURNAME'])) {
            $filter['%SURNAME'] = $filterData['SURNAME'];
        }

        if (!empty($filterData['FIRSTNAME'])) {
            $filter['%FIRSTNAME'] = $filterData['FIRSTNAME'];
        }
        
        if (!empty($filterData['MIDNAME'])) {
            $filter['%SURNMIDNAMEME'] = $filterData['MIDNAME'];
        }        

        // if (!empty($filterData['YEAR_from'])) {
        //     $filter['>=YEAR'] = $filterData['YEAR_from'];
        // }

        // if (!empty($filterData['YEAR_to'])) {
        //     $filter['<=YEAR'] = $filterData['YEAR_to'];
        // }

        // if (!empty($filterData['PUBLISH_DATE_from'])) {
        //     $filter['>=PUBLISH_DATE'] = $filterData['PUBLISH_DATE_from'];
        // }

        // if (!empty($filterData['PUBLISH_DATE_to'])) {
        //     $filter['<=PUBLISH_DATE'] = $filterData['PUBLISH_DATE_to'];
        // }

        return $filter;
    }

    private function prepareGridList(Result $books): array
    {
        $gridList = [];
        $groupedBooks = [];

        while ($book = $books->fetch()) {
            $doctorId = $book['IBLOCK_ELEMENT_ID'];

            if (!isset($groupedBooks[$doctorId])) {
                $groupedBooks[$doctorId] = [
                    'IBLOCK_ELEMENT_ID' => $book['IBLOCK_ELEMENT_ID'],
                    'TITLE' => $book['TITLE'],
                    'SURNAME' => $book['SURNAME'],
                    'FIRSTNAME' => $book['FIRSTNAME'],
                    'MIDNAME' => $book['MIDNAME'],
                    // 'TITLE' => $book['TITLE'],
                    // 'YEAR' => $book['YEAR'],
                    // 'PAGES' => $book['PAGES'],
                    // // 'PUBLISH_DATE' => $book['PUBLISH_DATE'],
                    // // 'AUTHORS' => [],
                ];
            }

            // if ($book['AUTHOR_ID']) {
            //     $groupedBooks[$doctorId]['AUTHORS'][] = implode(' ', array_filter([
            //         $book['AUTHOR_LAST_NAME'],
            //         $book['AUTHOR_FIRST_NAME'],
            //         $book['AUTHOR_SECOND_NAME']
            //     ]));
            // }
        }

        foreach ($groupedBooks as $book) {
            $gridList[] = [
                'data' => [
                    'IBLOCK_ELEMENT_ID' => $book['IBLOCK_ELEMENT_ID'],
                    'TITLE' => $book['TITLE'],
                    'SURNAME' => $book['SURNAME'],
                    'FIRSTNAME' => $book['FIRSTNAME'],
                    'MIDNAME' => $book['MIDNAME'],                    
                    // 'TITLE' => $book['TITLE'],
                    // 'YEAR' => $book['YEAR'],
                    // 'PAGES' => $book['PAGES'],
                    // 'AUTHORS' => implode(', ', $book['AUTHORS']),
                    // 'PUBLISH_DATE' => $book['PUBLISH_DATE']?->format('d.m.Y'),
                ],
                'actions' => $this->getElementActions($book),
            ];
        }

        return $gridList;
    }

    private function getFilterFields(): array
    {
        return [
            [
                'id' => 'SURNAME',
                'name' => Loc::getMessage('DOCTORS_GRID_DOCTOR_SURNAME_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'FIRSTNAME',
                'name' => Loc::getMessage('DOCTORS_GRID_DOCTOR_FIRSTNAME_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'MIDNAME',
                'name' => Loc::getMessage('DOCTORS_GRID_DOCTOR_MIDNAME_LABEL'),
                'type' => 'string',
                'default' => true,
            ],            
            // [
            //     'id' => 'FIRSTNAME',
            //     'name' => Loc::getMessage('DOCTORS_GRID_DOCTOR_FIRSTNAME_LABEL'),
            //     'type' => 'number',
            //     'default' => true,
            // ],
            // [
            //     'id' => 'MIDNAME',
            //     'name' => Loc::getMessage('DOCTORS_GRID_DOCTOR_MIDNAME_LABEL'),
            //     'type' => 'date',
            //     'default' => true,
            // ],
        ];
    }

    public function deleteElementAction(int $doctorId): array
    {
        $this->errorCollection = new ErrorCollection();
        try {
            $ormClass = $this->arParams['ORM_CLASS'];
            $ormClass::delete($doctorId);
        } catch (Exception $e) {
            $this->errorCollection->add([new Error($e->getMessage())]);
        }

        return [];
    }
}
