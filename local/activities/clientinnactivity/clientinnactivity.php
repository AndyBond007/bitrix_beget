<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Crm\EntityRequisite;
use Bitrix\Crm\EntityAddress;
use Bitrix\Main\Loader;
use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\Activity\PropertiesDialog;

\Bitrix\Main\Loader::IncludeModule('crm');

class CBPClientINNActivity extends BaseActivity
{
    protected static $requiredModules = ["crm"];
    
    /**
     * Конструктор activity
     * @param $name string Название activity
     */
    public function __construct( $name )
    {
        parent::__construct( $name );

        $this->arProperties = [
            'Inn' => '',
            // return
            'Company' => null,
            'FullCompany' => null,
            'Address' => null,
            'KPP' => null,
            'INN' => null,
            'OGRN' => null,
            'OKPO' => null,
            'Phone' => null,
            'Email' => null,
        ];

        $this->SetPropertiesTypes( [
            'Company' => [ 'Type' => FieldType::STRING ],
            'FullCompany' => [ 'Type' => FieldType::STRING ],
            'Address' => [ 'Type' => FieldType::STRING ],
            'KPP' => [ 'Type' => FieldType::STRING ],
            'INN' => [ 'Type' => FieldType::STRING ],
            'OGRN' => [ 'Type' => FieldType::STRING ],
            'OKPO' => [ 'Type' => FieldType::STRING ],          
            'Phone' => [ 'Type' => FieldType::STRING ],
            'Email' => [ 'Type' => FieldType::STRING ],
        ] );
    }

    /**
     * Возвращает путь к файлу activity
     * @return string
     */
    protected static function getFileName(): string
    {
        return __FILE__;
    }

    /**
     * Выполнение activity
     */
    protected function internalExecute(): ErrorCollection 
    {
        $errors = parent::internalExecute(); 

        //Получаем из БП параметры доступа к Dadata
        $rootActivity = $this->GetRootActivity();
        $token = $rootActivity->GetVariable("TOKEN"); 
        $secret =  $rootActivity->GetVariable("SECRET"); 

        // $token = "be15db6e663bd80461fc9f8211a6a4b60c9210fa"; // API ключ Dadata
        // $secret = "8be69a41ba37a0cf67c797b3fc971c1bd35cadbe"; // Cекретный ключ Dadata

        //Создаем объект Dadata
        $dadata = new \Dadata\DadataClient($token, $secret);

        //Запрашиваем данные по указанному ИНН
        $response = $dadata->findById("party", $this->Inn );

        if ( $response ) { // если копания найдена
            
            // по ИНН возвращается массив в котором может бытьнесколько элементов (компаний)
            //Используем первый блок по головной компании
            $company_name = $response[ 0 ][ 'data' ][ 'name' ][ 'short_with_opf' ];
            $company_full_name = $response[ 0 ] [ 'data' ][ 'name' ][ 'full_with_opf' ];
            $company_address = $response[ 0 ][ 'data' ][ 'address' ][ 'value' ];
            $company_kpp = $response[ 0 ][ 'data' ][ 'kpp' ];
            $company_inn = $response[ 0 ][ 'data' ][ 'inn' ];
            $company_ogrn = $response[ 0 ][ 'data' ][ 'ogrn' ];
            $company_okpo = $response[ 0 ][ 'data' ][ 'okpo' ];
            $company_phone = $response[ 0][ 'phones' ][ 0 ];
            $company_email = $response[ 0][ 'emails' ][ 0 ];

            $this->preparedProperties[ 'Company' ] = $company_name;
            $this->preparedProperties[ 'FullCompany' ] = $company_full_name;
            $this->preparedProperties[ 'Address' ] = $company_address;
            $this->preparedProperties[ 'KPP' ] = $company_kpp;
            $this->preparedProperties[ 'INN' ] = $company_inn;
            $this->preparedProperties[ 'OGRN' ] = $company_ogrn;
            $this->preparedProperties[ 'OKPO' ] = $company_okpo;
            $this->preparedProperties[ 'Phone' ] = $company_phone;
            $this->preparedProperties[ 'Email' ] = $company_email;

            $respomsible = 1;   //Указываем от кого производится добавление

            //Важно - Нет проверки на случай, если обнаружена компания в списке компаний битрикса
            //Готовим массив данных по компании для добавления
            $new_company = array(
                'TITLE' => $this->preparedProperties[ 'Company' ],
                'OPENED' => 'Y',
                'COMPANY_TYPE' => 'CUSTOMER',
                'ASSIGNED_BY_ID' => $respomsible,
            );
            //Если указан телефон - добавляем
            if ( $this->preparedProperties[ 'Phone' ] )
                $new_company['FM']['PHONE'] = array (
                    'n0' => array(
                        'VALUE_TYPE' => 'WORK',
                        'VALUE' => $this->preparedProperties[ 'Phone' ],
                    )
                );
            //Если указана электронная почта - добавляем
            if ( $this->preparedProperties[ 'Email' ] )
                $new_company['FM']['EMAIL'] = array (
                    'n0' => array(
                        'VALUE_TYPE' => 'WORK',
                        'VALUE' => $this->preparedProperties[ 'Email' ],
                    )
                );                
            
            //Обращаемся к CRM Company
            $company = new CCrmCompany( false ); //false - провеврка прав доступа к компаниям
            //Добавляем компанию и получаем ее ID
            $company_id = $company->Add( $new_company ); 

            //Если компания успешно добавлена
            if ( $company_id ) {
                //Выводим в журнал информацию
                $this->log( 'Обновлена информация о компании: ' 
                    . 'ID '. $company_id
                    . ', ' . $this->preparedProperties[ 'Company' ]
                    . ', ' . $this->preparedProperties[ 'FullCompany' ] 
                    . ', Адрес: ' . $this->preparedProperties[ 'Address' ] 
                    . ', Телефон: ' . $this->preparedProperties[ 'Phone' ] 
                    . ', E-mail: ' . $this->preparedProperties[ 'Email' ] 
                );

                //Добавляем реквизиты
                //Создаем объект реквизитов
                $requisite = new \Bitrix\Crm\EntityRequisite();
                //Получаем списко реквизитов компании
                $rs = $requisite->getList( [
                    "filter" => ["ENTITY_ID" => $company_id, "ENTITY_TYPE_ID" => CCrmOwnerType::Company ]
                ] );

                $reqData = $rs->fetchAll();

                // Подготовка полей
                $fields = array(
                    'ENTITY_ID' => $company_id,
                    'ENTITY_TYPE_ID' => \CCrmOwnerType::Company,
                    'PRESET_ID' => 1, // 1-Организация, 3-ИП, 5-Физлицо
                    'NAME' => $this->preparedProperties[ 'Company' ],
                    'SORT' => 500,
                    'ACTIVE' => 'Y',
                    'RQ_COMPANY_NAME' => $this->preparedProperties[ 'Company' ],
                    'RQ_COMPANY_FULL_NAME' => $this->preparedProperties[ 'FullCompany' ]
                );

                //Добавление ИНН, КПП и т.д.
                $fields['RQ_INN'] = $this->preparedProperties[ 'INN' ];
                $fields['RQ_KPP'] = $this->preparedProperties[ 'KPP' ];
                $fields['RQ_OGRN'] = $this->preparedProperties[ 'OGRN' ];
                $fields['RQ_OKPO'] = $this->preparedProperties[ 'OKPO' ];

                //Если реквизиты существуют, сначала их удаляем, а потом добавляем, иначе будет несколько инн-ов
                $requisite->deleteByEntity( CCrmOwnerType::Company, $reqData[ 0 ][ 'ENTITY_ID' ] );
                $res = $requisite->add( $fields );
                //Получаем ID добавленных реквизитов
                $requisite_id = $res->getId();
                //Если не получилось добавить реквизиты
                if ( !$requisite_id ) {
                    $this->log( 'Ошибка добавления реквизитов компании!' );
                }
                //В противном случае
                else
                {
                    //Если указан адрес компании
                    if ( $this->preparedProperties[ 'Address' ] ) {
                        //Создаем объект адресов
                        $address = new EntityAddress();
                        //Готовим массив данных по адресу компании     
                        $addressFields = array(
                            "ADDRESS_1" => $this->preparedProperties[ 'Address' ],
                            "CITY" => $this->preparedProperties[ 'Address' ],
                            "POSTAL_CODE" => $this->preparedProperties[ 'Address' ],
                            "COUNTRY" => $this->preparedProperties[ 'Address' ]
                        );

                        //Регистрируем адрес
                        //Регистрация адреса: тип 8 (реквизит), тип адреса 6 (юридический)
                        $addressResult = $address->register(8, $requisite_id, 6, $addressFields);

                        //Выводим сообщение при ошибке добавления адреса
                        //НО ЭТО НЕ РАБОТАЕТ ПРОВЕРКА, адрес добавлен отлично, но выводится ошибка
                        // if ( !$addressResult ) {
                        //     $this->Log( 'Ошибка добавления адреса к реквизиту' );
                        // }

                        $this->log( 'Обновлены реквизиты компании: ' 
                            . 'Реквизит ID '. $requisite_id
                            . 'ID '. $company_id
                            . ', ' . $this->preparedProperties[ 'Company' ]
                            . ', ИНН: ' . $this->preparedProperties[ 'INN' ] 
                            . ', КПП: ' . $this->preparedProperties[ 'KPP' ] 
                            . ', ОКПО: ' . $this->preparedProperties[ 'OKPO' ] 
                            . ', ОГРН: ' . $this->preparedProperties[ 'OGRN' ] 
                        );   
                    }                 
                }
            }
            else
                $this->log( 'Организация не обнаружена по ИНН [' . $this->Inn . '] !!!');
        }

        /*
        В Этом нет необходимости для передачи результатов дальше по процессу
        $rootActivity = $this->GetRootActivity(); // получаем объект активити
        // сохранение полученных результатов работы активити в переменную бизнес процесса
        // $rootActivity->SetVariable("TEST", $this->preparedProperties['Text']); 

        // получение значения полей документа в активити        
        $documentType = $rootActivity->getDocumentType(); // получаем тип документа
        $documentId = $rootActivity->getDocumentId(); // получаем ID документа 

        // получаем объект документа над которым выполняется БП (элемент сущности Компания)
        $documentService = CBPRuntime::GetRuntime(true)->getDocumentService(); 
        // $documentService = $this->workflow->GetService("DocumentService");   

        // поля документа
        $documentFields =  $documentService->GetDocumentFields($documentType);
        //$arDocumentFields = $documentService->GetDocument($documentId);   

        foreach ($documentFields as $key => $value) {
            if($key == 'UF_CRM_1718872462762'){ // поле номер ИНН
                $fieldValue = $documentService->getFieldValue($documentId, $key, $documentType);
                $this->log('значение поля Инн:'.' '.$fieldValue);
            }

            if($key == 'UF_COMPANY_INN'){ // поле UF_COMPANY_INN
                $fieldValue = $documentService->getFieldValue($documentId, $key, $documentType);
                $this->log('значение поля UF_COMPANY_INN:'.' '.$fieldValue);
            }
        }*/
        

        return $errors;
    }

    /**
     * Подготовить массив входных параметров (свойств) для activity
     * @return array[] 
     */
    public static function getPropertiesDialogMap( ?PropertiesDialog $dialog = null ) : array
    {
        $map = [
            'Inn' => [
                'Name' => Loc::getMessage('CLIENT_INN_ACTIVITY_FIELD_SUBJECT'),
                'FieldName' => 'inn',
                'Type' => FieldType::STRING,
                'Required' => true,
                'Options' => [],
            ],
        ];
        return $map;
    }
}