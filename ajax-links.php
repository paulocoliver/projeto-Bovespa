<?php
include_once 'include/class/Bovespa/Link.php';
try{
	$cvm = !empty($_GET['cvm']) ? $_GET['cvm'] : null;
	$link = new Link();
	$res_links = $link->getLinks($cvm);
	$new_links = array();
	foreach ($res_links AS $value) {
		$new_links[] = array('id' => $value->id_link, 'text' => utf8_encode($value->descricao));
	}
	$response = array('sucess' => true, 'links' => $new_links);
}catch(Exception $erro){
	$response = array('sucess'=>false,'msg'=>$erro->getMessage());
}
die(json_encode($response));