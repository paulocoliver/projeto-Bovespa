<?php 
include_once 'include/class/Bovespa/Link.php';


function getFile ($url_download, $id_link, $ext) {
	try {
		
		$file = $id_link.'.'.$ext;
		$path = $_SERVER['DOCUMENT_ROOT'].'/files/'.$file;
		if (!file_exists($path)) {
			$ch = curl_init($url_download);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			curl_close($ch);
			file_put_contents($path, $data);
			return $data;
		} else {
			return true;
		}

		//mkdir($_SERVER['DOCUMENT_ROOT'].'/files/'.$id_link);
		//ezip($file, $id_link.'/');
		
	} catch (Exception $e) {
		return '';
	}
}

try{
	$id_link = !empty($_GET['id_link']) ? $_GET['id_link'] : null;
	$link = new Link();
	$res_link = $link->getLink($id_link);
	if (empty($res_link))
		throw new Exception('Link não encontrado');
	
	$uri            = explode(':', $res_link->link, 2);
	$scheme         = strtolower($uri[0]);
	$schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';
	 
	$pattern = '~^((//)([^/?#]*))([^?#]*)(\?([^#]*))?(#(.*))?$~';
	$status  = @preg_match($pattern, $schemeSpecific, $matches);
	$query    = isset($matches[6]) === true ? $matches[6] : '';
	parse_str(html_entity_decode($query), $queryArray);
	
	$filename_open = $res_link->id_link;
	
	/*echo '<pre>';
	print_r($res_link);
	echo '</pre>';*/
	
	if (!empty($queryArray['NumeroSequencialDocumento'])) {
		$url_download = "http://www.rad.cvm.gov.br/enetconsulta/frmDownloadDocumento.aspx?CodigoInstituicao=".$queryArray['CodigoTipoInstituicao']."&NumeroSequencialDocumento=".$queryArray['NumeroSequencialDocumento'];
		getFile($url_download, $res_link->id_link, 'zip');
		
		include 'index1.php';
	} elseif(!empty($queryArray['razao'])) {
		$queryArray['razao'] = str_replace(' ', '%20', $queryArray['razao']);
		$queryArray['pregao']= str_replace(' ', '%20', $queryArray['pregao']);
		$url_download ="http://www.bmfbovespa.com.br/dxw/Download.asp?moeda=L&site=".$queryArray['site']."&mercado=".$queryArray['mercado']."&razao=".$queryArray['razao']."&pregao=".$queryArray['pregao']."&ccvm=".$queryArray['ccvm']."&data=".$queryArray['data']."&tipo=1";//.$queryArray['tipo'];
		/*echo $url_download.'<br>';
		echo '<pre>';
		print_r($res_link);
		echo '</pre>';*/
		getFile($url_download, $res_link->id_link, 'WTL');
		include 'index2.php';
	}
	
} catch(Exception $e) {
	echo 'Desculpe ocorreu um erro: '.$e->getMessage();
	
}
?>