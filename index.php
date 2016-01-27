<?php

$xmldoc = new DOMDocument('1.0');
$xmldoc->load('init.xml');

// Создаем переменные $$name = value
// Если $_GET для переменной нет, то берем значения по умолчанию из init.xml <var name='name'>value</var>
// Добавляем в init.xml элемент userinput с введенными данными $_GET

$vars = $xmldoc->getElementsByTagName('var');
$userinput = $xmldoc->createElement('userinput');
$checkinput = $xmldoc->createElement('checkinput');
$eggog = false;

foreach ($vars as $var) {
    $name = $var->getAttribute('name');
    if (isset($_GET[$name])) {
        $$name = $_GET[$name];
        $userinput->setAttribute($name,$_GET[$name]);
    }
    else {
        $$name = $var->nodeValue;
    }
    $va[$name] = $$name;
    if (!($name == 'last_stage' or $name == 'save_result') and !is_numeric($$name)) {
        $checkinput->setAttribute($name,'error');
        $eggog = true;
    }
}

// Проверяем данные на соответствие правилам и в случае ошибки
// добавляем в init.xml элемент checkinput с описанием ошибочного поля

foreach ($va as $key => $value) {
    $k = $height/pow($aperture/2,2);
    $r = 1 / (2*$k);
    switch ($key) {
        case 'last_stage':
        case 'save_result':
            if ($value == 'yes' or $value == 'no') {continue 2;} else {break;}
        case 'height':
        case 'aperture':
            if ($value <= 0) {break;} else {continue 2;}
        case 'precX':
        case 'precY':
            if (!is_int(abs($value))) {break;} else {continue 2;}
        case 'width':
            if ($value <= 0 or $value < $aperture) {break;} else {continue 2;}
        case 'stepX':
            if ($value <= 0 or $value < pow(10,-$precX)) {break;} else {continue 2;}
        case 'bottom':
            if ($value < 0) {break;} else {continue 2;}
        case 'delta1':
            if ($value > $r) {break;} else {continue 2;}
    }
    $checkinput->setAttribute($key,'error');
    $eggog = true;
};

$xmldoc->documentElement->appendChild($userinput);
$xmldoc->documentElement->appendChild($checkinput);

// Расчет профиля:
// Добавляем элемент <profile type='main' />, куда кладем <point /> с расчетными значениями
if (!$eggog) {

    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = implode('/', explode('/', $url, -1)) . '/calc.php?' . http_build_query($va);

    $calc_result = file_get_contents($url);    
    $calc_result = explode('<?xml version="1.0"?>',$calc_result);
    $calc_result = $calc_result[1];
    
    $f = $xmldoc->createDocumentFragment();
    $f->appendXML($calc_result);
    $xmldoc->documentElement->appendChild($f);
}

$pipeline = simplexml_load_file("pipeline.xml")->pipeline;
foreach ($pipeline->execute as $xsl_task){
    $xsldoc = new DOMDocument();
    $xsldoc->load($xsl_task['href']);
    $xslt = new XSLTProcessor();
    $xslt->importStylesheet($xsldoc);
    if ($xsl_task['href'] == 'last.xsl') {
        if ($last_stage == 'no'){
            header("Content-Type: text/xml");
            echo $xmldoc->saveXML();
        }
        if ($last_stage == 'yes') {
            $xmldoc = $xslt->transformToXML($xmldoc);
            header("Content-Type: text/html");
            echo $xmldoc;
        }
    }
    else {
        $xmldoc = $xslt->transformToDoc($xmldoc);
    }
}

?>