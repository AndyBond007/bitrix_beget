<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
// use Otus\Orm\BookTable;
// use Otus\Orm\AuthorTable;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\UI\Buttons\Color;
use Bitrix\Main\Error;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorableImplementation;

class BookGrid extends \CBitrixComponent implements Controllerable, Errorable
{
    use ErrorableImplementation;

    protected const GRID_ID = 'BOOK_GRID';
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['BOOK_PREFIX'] = strtolower($arParams['BOOK_PREFIX']);
        return $arParams;
    }

    public function listKeysSignedParameters(): array
    {
        return [
            'ORM_CLASS',
        ];
    }

    public function configureActions(): array
    {
        return [
            'deleteElement' => [
                'preFilters' => [
                    new \Bitrix\Main\Engine\ActionFilter\Authentication,
                ],
            ],
            'addElement' => [],
        ];
    }

    private function getElementActions(array $fields): array
    {
        return [
            [
                'onclick' => "window.open('http://otus-08.localhost/bitrix/admin/perfmon_row_edit.php?lang=ru&table_name=aholin_book&pk%5BID%5D={$fields['ID']}')", // метод обработчик в js
                'text' => Loc::getMessage('DOCTORS_GRID_OPEN_DOCTOR', [
                    '#DOCTOR_NAME#' => $fields['TITLE'],
                ]),
                'default' => true,
            ],
            [
                'onclick' => sprintf('BX.Otus.BookGrid.deleteBook(%d)', $fields['IBLOCK_ELEMENT_ID']),
                'text' => Loc::getMessage('BOOK_GRID_DELETE'),
                'default' => true,
            ],
            [
                'onclick' => sprintf('BX.Otus.BookGrid.deleteBookViaAjax(%d)', $fields['IBLOCK_ELEMENT_ID']),
                'text' => Loc::getMessage('BOOK_GRID_DELETE') . ' через AJAX',
                'default' => true,
            ],
        ];
    }

    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function addTestBookElementAction(array $bookData): array
    {
        $newBookData = [
            'TITLE' => $this->arParams['BOOK_PREFIX'] . $bookData['bookTitle'] ?? '',
            'YEAR' => $bookData['publishYear'] ?? 2000,
            'PAGES' => $bookData['pageCount'] ?? 0,
            'PUBLISH_DATE' => DateTime::createFromText($bookData['publishDate'] ?? ''),
        ];

        $addResult = DoctorsTable::add($newBookData);
        if (!$addResult->isSuccess()) {
            $this->errorCollection->add([new Error('Не удалось создать книгу')]);
            return [];
        }

        $bookId = $addResult->getId();
        $book = DoctorsTable::getByPrimary($bookId)->fetchObject();

        $authorIds = $bookData['authors'];
        foreach ($authorIds as $authorId) {
            $author = AuthorTable::getByPrimary($authorId)->fetchObject();
            if ($author) {
                $book->addToAuthors($author);
            }
        }

        $updateResult = $book->save();

        if (!$updateResult->isSuccess()) {
            $this->errorCollection->add([new Error('Не удалось добавить авторов')]);
            return [];
        }

        return [
            'BOOK_ID' => $bookId
        ];
    }

    private function getHeaders(): array
    {
        return [
            [
                'id' => 'IBLOCK_ELEMENT_ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
            ],
            [
                'id' => 'SURNAME',
                'name' => Loc::getMessage('DOCTORS_GRID_DOCTOR_SURNAME_LABEL'),
                'sort' => 'SURNAME',
                'default' => true,
            ],
            [
                'id' => 'FIRSTNAME',
                'name' => Loc::getMessage('DOCTORS_GRID_DOCTOR_FIRSTNAME_LABEL'),
                'sort' => 'FIRSTNAME',
                'default' => true,
            ],
            [
                'id' => 'MIDNAME',
                'name' => Loc::getMessage('DOCTORS_GRID_DOCTOR_MIDNAME_LABEL'),
                'sort' => 'MIDNAME',
                'default' => true,
            ],
        ];
    }

    protected function getButtons(): array
    {
        return [
            [
                // 'link' - ссылка
                'click' => 'redirectToExcel', // метод обработчик в js
                'text' => Loc::getMessage('EXPORT_XLSX_BUTTON_TITLE'),
                'color' => Color::PRIMARY,
            ],
            [
                'link' => '/stream/',
                'text' => Loc::getMessage('BOOK_GRID_GO_TO_LIVE_STREAM'),
                'color' => Color::SECONDARY,
            ],
            [
                'click' => 'BX.Otus.BookGrid.addBook',
                'text' => 'Добавить книгу',
                'color' => Color::PRIMARY_DARK,
            ],
            [
                'click' => 'BX.Otus.BookGrid.createTestElementViaModule',
                'text' => 'Добавить тестовую книгу',
                'color' => Color::DANGER_DARK,
            ],
        ];
    }

    public function executeComponent(): void
    {
        if ($this->request->get('EXPORT_MODE') == 'Y') {
            $this->setTemplateName('excel');
        }

        $this->arResult['BUTTONS'] = $this->getButtons();
        $this->prepareGridData();

        $this->includeComponentTemplate();
    }

    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    private function prepareGridData(): void
    {
        $this->arResult['HEADERS'] = $this->getHeaders();
        $this->arResult['FILTER_ID'] = self::GRID_ID;

        $gridOptions = new GridOptions($this->arResult['FILTER_ID']);
        $this->arResult['USED_HEADERS'] = $gridOptions->getUsedColumns($this->arResult['HEADERS']);
        $navParams = $gridOptions->getNavParams();

        $nav = new PageNavigation($this->arResult['FILTER_ID']);

        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        $filterOption = new FilterOptions($this->arResult['FILTER_ID']);
        $filterData = $filterOption->getFilter([]);
        $filter = $this->prepareFilter($filterData);

        $sort = $gridOptions->getSorting([
            'sort' => [
                'IBLOCK_ELEMENT_ID' => 'DESC',
            ],
            'vars' => [
                'by' => 'by',
                'order' => 'order',
            ],
        ]);

        $bookIdsQuery = DoctorsTable::query()
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

        $bookIds = array_column($bookIdsQuery->exec()->fetchAll() ?? [], 'IBLOCK_ELEMENT_ID');

        if (!empty($bookIds)) {
            $books = DoctorsTable::getList([
                'filter' => ['IBLOCK_ELEMENT_ID' => $bookIds] + $filter,
                'select' => [
                    'IBLOCK_ELEMENT_ID',
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
            $filter['%TITLE'] = $filterData['FIND'];
        }

        // if (!empty($filterData['TITLE'])) {
        //     $filter['%TITLE'] = $filterData['TITLE'];
        // }

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
            $bookId = $book['IBLOCK_ELEMENT_ID'];

            if (!isset($groupedBooks[$bookId])) {
                $groupedBooks[$bookId] = [
                    'IBLOCK_ELEMENT_ID' => $book['IBLOCK_ELEMENT_ID'],
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
            //     $groupedBooks[$bookId]['AUTHORS'][] = implode(' ', array_filter([
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

    public function deleteElementAction(int $bookId): array
    {
        $this->errorCollection = new ErrorCollection();
        try {
            $ormClass = $this->arParams['ORM_CLASS'];
            $ormClass::delete($bookId);
        } catch (Exception $e) {
            $this->errorCollection->add([new Error($e->getMessage())]);
        }

        return [];
    }
}
