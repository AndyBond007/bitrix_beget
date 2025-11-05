<?php 
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
    /**
     * @var CMain $Application
     */
    $APPLICATION->SetTitle('Проверка загрузки файла на сайт');
/*
    Рабочий вариант загрузки файла в папку /upload/test_up/
*/
    if (isset($_POST['submit'])) {
        unset($_POST['submit']);
        print_r($_FILES);
        print "<P>\n";
        $file_name = $_SERVER['DOCUMENT_ROOT'] . "/upload/test_up/" . $_FILES['uploadFile']['name'];
        move_uploaded_file ($_FILES['uploadFile']['tmp_name'], $file_name );
        $APPLICATION->SetTitle( $file_name);
    }

?>
<section>
    <html>
        <head>
            <title>File Upload Form</title>
        </head>
        <body>
This form allows you to upload a file to the server.<br>
            <form method="post" enctype="multipart/form-data"><br>
Type (or select) Filename: <input type="file" name="uploadFile">
                <input type="submit" name="submit" value="Upload File">
            </form>
        </body>
    </html>
</section>