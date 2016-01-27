<?php

foreach ($_GET as $key => $value) {
    $$key = $value;
}

function addElement($xmldoc, $profile, $result_file, $value) {
    $profile->appendChild($xmldoc->createElement('point', $value));
    fwrite($result_file, $value . "\r\n");
}

$filename = 'h' . $height . '_w' . $width . '_a' . $aperture . '_b' . $bottom . '_d' . $delta1 . '_sX' . $stepX . '_pX' . $precX . '_pY' . $precY . '.elm';

$file_list = scandir('result/');
foreach ($file_list as $file) {
    if (!is_dir($file)) unlink('result/' . $file);
}

$result_file = fopen('result/' . $filename,'w');

$xmldoc = new DOMDocument('1.0');

    $profile = $xmldoc->createElement('profile');
    $profile->setAttribute('type','main');
    $profile->setAttribute('filename', $filename);
    addElement($xmldoc, $profile, $result_file, ' V 1996.03  ELM File');
    addElement($xmldoc, $profile, $result_file, ' 1' . "\t" . '100');

    $count = 0;
    $k = $height/pow($aperture/2,2);
    for ($x = 0; $x <= $aperture/2; $x = $x+$stepX) {
        $y = $k * pow($x,2) + $bottom;
        $xd = round(($x - (2 * $delta1 * $k * $x) / sqrt(1 + pow(2*$k*$x, 2))), $precX);
        $yd = round(($y + $delta1 / sqrt(1 + pow(2*$k*$x, 2))), $precY);
        if ($y > $height) break;
        $count++;
        addElement($xmldoc, $profile, $result_file, $xd . "\t" . $yd);
        $xda[$count]=$xd*(-1);
        $yda[$count]=$yd;
    }

    addElement($xmldoc, $profile, $result_file, ($aperture/2-$delta1) . "\t" . ($height+$bottom));
    if ($width/2 > $aperture/2-$delta1) addElement($xmldoc, $profile, $result_file, ($width/2) . "\t" . ($height+$bottom));
    addElement($xmldoc, $profile, $result_file, ($width/2) . "\t" . 0);
    addElement($xmldoc, $profile, $result_file, -($width/2) . "\t" . 0);
    if ($width/2 > $aperture/2-$delta1) addElement($xmldoc, $profile, $result_file, -($width/2) . "\t" . ($height+$bottom));
    addElement($xmldoc, $profile, $result_file, -($aperture/2-$delta1) . "\t" . ($height+$bottom));

    for ($count; $count > 0; $count--) addElement($xmldoc, $profile, $result_file, $xda[$count] . "\t" . $yda[$count]);

    addElement($xmldoc, $profile, $result_file, '#');
    addElement($xmldoc, $profile, $result_file, ' 1' . "\t" . '100');
    addElement($xmldoc, $profile, $result_file, '#');

    $xmldoc->appendChild($profile);

fclose($result_file);
echo $xmldoc->saveXML();

?>
