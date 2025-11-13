<?php

use Bitrix\Main\SystemException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\EventManager;

//Класс установщика модуля
class anb_firstmodule extends CModule
{
	//Идентификатор модуля
    public $MODULE_ID = 'anb.firstmodule';
    
	//Сортировака
	public $MODULE_SORT = 500;

	//Версия модуля
	public $MODULE_VERSION;
    
	//Дата версии модуля
	public $MODULE_VERSION_DATE;
    
	//Название модуля
	public $MODULE_NAME;
    
	//Описание модуля
	public $MODULE_DESCRIPTION;

	//Разработчик
	public $PARTNER_NAME;

	//Ссылка разработчика
	public $PARTNER_URI;

	public function __construct()
	{
		//Подключаем версию и устанавливаем ее модулю
		include __DIR__ . '/version.php';

		if ( isset( $arModuleVersion[ 'VERSION' ], $arModuleVersion[ 'VERSION_DATE' ] ) )
		{
			$this->MODULE_VERSION = $arModuleVersion[ 'VERSION' ];
			$this->MODULE_VERSION_DATE = $arModuleVersion[ 'VERSION_DATE' ];
		}

		//Устанавливаем название и описание модуля
		$this->MODULE_NAME = Loc::getMessage( 'MODULE_NAME' );
		$this->MODULE_DESCRIPTION = Loc::getMessage( 'MODULE_DESCRIPTION' );

		//Устанавливаем ссылку на разработчика
		$this->PARTNER_NAME = Loc::getMessage( 'MODULE_PARTNER_NAME' );
		$this->PARTNER_URI = Loc::getMessage( 'MODULE_PARTNER_URI' );
	}
	

	/**
	 * Проверка версии на соответствие требованиям модулю
	 * Возвращает результат проверки
	 */
	public function isVersionD7() : bool 
	{
		return version_compare( ModuleManager::getVersion( 'main' ), '20.00.00', ">" );	
	}

	/**
	 * Получить путь расположения модуля
	 */
	public function getPath( $notDocumentRoot = false ): string
    {
        if ($notDocumentRoot) 
		{
            return str_ireplace( Application::getDocumentRoot(), '', dirname( __DIR__ ) );
        } else
		{
            return dirname( __DIR__ );
        }
    }

	/**
	 * Возвращает список таблиц для создания/удаления
	 */
    private function getEntities(): array
    {
        return [
            // AuthorTable::class,
            // BookTable::class,
        ];
    }
	
	/**
	 * Заполнение требуемых таблиц демонстрационными данными
	 */
    private function addEntityElements( string $entityClass ): void
    {
        // if ( $entityClass === AuthorTable::class ) 
		// {
        //     TestDataInstaller::addAuthors();
        // } 
		// elseif ( $entityClass === BookTable::class ) 
		// {
        //     TestDataInstaller::addBooks();
        // }
    }

	/**
	 * Создание вспомогательных таблиц (таблицы без ORM)
	 */
    private function installHelpersTable(): void
    {
        // $connection = Application::getConnection();
        // $tableName = 'aholin_book_author';

        // if ( !$connection->isTableExists( $tableName ) ) 
		// {
        //     $connection->queryExecute("
        //     	CREATE TABLE {$tableName} (
        //         	BOOK_ID int NOT NULL,
        //         	AUTHOR_ID int NOT NULL,
        //         	PRIMARY KEY (BOOK_ID, AUTHOR_ID)
        //     	)
        // 	");
        // }
    }

	/**
	 * Удаление вспомогательных таблиц (таблицы без ORM)
	 */
    private function uninstallHelpersTable(): void
    {
        // $connection = Application::getConnection();
        // $tableName = 'aholin_book_author';

        // if ( $connection->isTableExists( $tableName ) ) 
		// {
        //     $connection->dropTable( $tableName );
        // }
    }

	/**
	 * Метод установки модуля
	 * Выполняет все основные требуемые методы для установки и настройки модуля
	 */
	public function DoInstall(): void
	{
		//Проверяем, чтобы установка выполнялась от Администратора
		global $USER;
		
		if ( !$USER->IsAdmin() || !$this->isVersionD7() ) 
		{
			//TODO Узнать как показать человеческое сообщение об ошибке
			throw new SystemException( Loc::getMessage( 'MODULE_INSTALL_ERROR' ) );
		}
		else
		{
			//Устанавливаем требуемые файлы
			$this->InstallFiles();
			//Создаем требуемые таблицы в БД и записи
			$this->InstallDB();
			//Устанавливаем обработчики событий
			$this->InstallEvents();

			//Добавить проверку ошибок копирования файлов, создания БД и т.д.
			//Регистрируем модуль
			ModuleManager::registerModule( $this->MODULE_ID );
		}
	}

	/**
	 * Копирование требуемых файлов модуля (компоненты, стили, админка и т.д.)
	 */
	public function InstallFiles(): void
	{
		//Необходимо копировать все файлы включая административную часть
        $component_path = $this->getPath() . '/install/components';

		//Если папка установки существует
        if ( Directory::isDirectoryExists( $component_path ) ) 
		{
            CopyDirFiles( $component_path, $_SERVER[ 'DOCUMENT_ROOT' ] . '/bitrix/components', true, true );
        } else 
		{
            throw new InvalidPathException( $component_path );
        }
	}

	/**
	 * Создаем требуемые таблицы и наполняем их данными
	 */
	public function InstallDB(): void
	{
        // Loader::includeModule( $this->MODULE_ID );

        // $entities = $this->getEntities();

        // foreach ( $entities as $entity ) 
		// {
        //     if ( !Application::getConnection( $entity::getConnectionName() )->isTableExists( $entity::getTableName() ) ) 
		// 	{
        //         Base::getInstance( $entity )->createDbTable();
        //     }
        // }

        // $this->installHelpersTable();

        // foreach ( $entities as $entity ) 
		// {
        //     $this->addEntityElements( $entity );
        // }
	}

	/**
	 * Устанавливаем обработчики событий
	 */
	public function InstallEvents(): void
	{
		$eventManager = EventManager::getInstance();

		$eventManager->registerEventHandler(
			'crm',
			'onEntityDetailsTabsInitialized',
			$this->MODULE_ID, 
			'\\Anb\\Firstmodule\\Crm\\Handlers',
			'updateTabs'
		);
	} 

	/**
	 * Выполнение удаления модуля
	 */
	public function DoUninstall(): void
	{
		//Проверяем, чтобы установка выполнялась от Администратора
		global $USER;
		
		if (!$USER->IsAdmin())
		{
			return;
		}

		//Устанавливаем установленные файлы
		$this->UnInstallFiles();
		//Удаляем созданные таблицы в БД и записи
		//TODO добавить вопрос на удаление ???
		$this->UnInstallDB();
		//Удаляем обработчики событий
		$this->UnInstallEvents();		
		//Удаляем регистрацию модуля
		ModuleManager::unRegisterModule($this->MODULE_ID);
	}

	/**
	 * Удаление установленных файлов модуля (компоненты, стили, админка и т.д.)
	 */
	public function UnInstallFiles(): void
	{
        $component_path = $this->getPath() . '/install/components';

        if ( Directory::isDirectoryExists( $component_path ) ) 
		{
            $installed_components = new \DirectoryIterator( $component_path );
            foreach ( $installed_components as $component )
			{
                if ( $component->isDir() && !$component->isDot() ) 
				{
                    $target_path = $_SERVER[ 'DOCUMENT_ROOT' ] . '/bitrix/components/' . $component->getFilename();
                    if ( Directory::isDirectoryExists( $target_path ) ) 
					{
                        Directory::deleteDirectory( $target_path );
                    }
                }
            }
        } else 
		{
            throw new InvalidPathException( $component_path );
        }
	}

	/**
	 * Удаляем ранее созданные таблицы
	 */
	public function UnInstallDB(): void
	{
        // Loader::includeModule( $this->MODULE_ID) ;

        // $connection = Application::getConnection();

        // $entities = $this->getEntities();
        // $this->uninstallHelpersTable();

        // foreach ( $entities as $entity ) {
        //     if ( Application::getConnection( $entity::getConnectionName() )->isTableExists( $entity::getTableName() ) ) 
		// 	{
        //         $connection->dropTable( $entity::getTableName() );
        //     }
        // }
	}

	/**
	 * Удаляем обработчики событий
	 */
	public function UnInstallEvents(): void
	{
		$eventManager = EventManager::getInstance();

		$eventManager->unRegisterEventHandler(
			'crm',
			'onEntityDetailsTabsInitialized',
			$this->MODULE_ID, 
			'\\Anb\\Firstmodule\\Crm\\Handlers',
			'updateTabs'
		);
	} 	

}
?>