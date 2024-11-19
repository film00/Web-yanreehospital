<?php
require_once('./TCPDF-main/tcpdf.php');
$fontfile = './path_to_fonts/Sarabun-Regular.ttf';
$fontname = TCPDF_FONTS::addTTFfont($fontfile, 'TrueTypeUnicode', '', 32);
echo $fontname;
?>
