<?php
namespace core;

require 'class/HtmlBuilder.php';

$html = '';

if ($_POST['logId'] > 0) {
    $htmlBuilder = new HtmlBuilder();

    $html = $htmlBuilder->buildHTML($_POST['logId']);
}

if (strlen($html)) {
    echo $html;
}

?>

<link rel="stylesheet" href="css/tables.css">

<form method="post" action="index.php">
    <input type="text" name="logId" placeholder="Номер лога боя..."><br />
    <input type="submit" class="btn btn-default" value="Загрузить"">
</form>

<hr>



