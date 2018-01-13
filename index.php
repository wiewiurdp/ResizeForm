<!doctype html>
<?php
require __DIR__ . '/vendor/autoload.php';
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Index</title>
    <!--    <link rel="stylesheet" media="screen"-->
    <!--          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">-->
</head>
<body>
<b>Formularz</b>
<br><br>

<form action="" method="post" ENCTYPE="multipart/form-data">
    Wybór pliku do wysłania <input type="file" name="fileToUpload" id="fileToUpload"/>
    <br>
    Wysokość <input type="number" name="height"/>
    <br>
    Szerokość<input type="number" name="width">
    <br>
    <input type="submit" value="Upload Img" name="submit">
</form>
</body>
</html>
<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Eventviva\ImageResize;

$log = new Logger('logi');
$log->pushHandler(new StreamHandler(__DIR__ . '/logi.txt', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $height = $_POST['height'];
    $width = $_POST['width'];
    $uploadDir = __DIR__ . "/images/";
    $uploadBasename = basename($_FILES['fileToUpload']['name']);
    $uploadFile = $uploadDir . $uploadBasename;
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadFile)) {
        $image = new ImageResize($uploadFile);
        $image->resize($width, $height);
        $image->save($uploadFile);
        $exif = exif_read_data($uploadFile);
        $fileName = $exif['FileName'];
        $fileSize = $exif['FileSize'];
        $imageSize = $exif['COMPUTED']['html'];
        $DataTime = new DateTime();
        $DataTime->setTimestamp($exif['FileDateTime']);
        $FileCreationDateTime = $DataTime->format('Y-m-d H:i:s');
        $log->addInfo("File name - $fileName");
        $log->addInfo("File size - $fileSize");
        $log->addInfo("Image size  - $imageSize");
        $log->addInfo("File Creation Date  - $FileCreationDateTime");
    }
    echo "<img src='images/$uploadBasename'>";

}
?>
