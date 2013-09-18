<?php
include 'include/class/Documento/DocumentoBovespa.php';

$documento = new DocumentoBovespa();

//$documento->newDoc('35', '01/01/2013', 1);
$documento->select();