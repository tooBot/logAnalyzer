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