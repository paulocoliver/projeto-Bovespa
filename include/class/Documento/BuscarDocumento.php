<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Bovespa/Link.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoBovespa.php';

class BuscarDocumento{
	
	public function __construct(){}
	
	private function _getFile ($url_download, $id_link, $ext) {
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
	
		} catch (Exception $e) {
			return '';
		}
	}
	
	public function getDocumento($id_link){
		
		$link 	  = new Link();
		$res_link = $link->getLink($id_link);
	
		if (empty($res_link))
			throw new Exception('Link não encontrado');
	
		$documento = new DocumentoBovespa($res_link->cvm);
		$documento->setData($res_link->data);
	
		$id_documento_bovespa = $documento->selectByLink();
	
		if(empty($id_documento_bovespa)){
				
			$documento->newDoc(1);
	
			$uri            = explode(':', $res_link->link, 2);
			$scheme         = strtolower($uri[0]);
			$schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';
	
			$pattern = '~^((//)([^/?#]*))([^?#]*)(\?([^#]*))?(#(.*))?$~';
			$status  = @preg_match($pattern, $schemeSpecific, $matches);
			$query    = isset($matches[6]) === true ? $matches[6] : '';
			parse_str(html_entity_decode($query), $queryArray);
	
			$filename_open = $res_link->id_link;
	
			if (!empty($queryArray['NumeroSequencialDocumento'])) {
	
				$url_download = "http://www.rad.cvm.gov.br/enetconsulta/frmDownloadDocumento.aspx?CodigoInstituicao=".$queryArray['CodigoTipoInstituicao']."&NumeroSequencialDocumento=".$queryArray['NumeroSequencialDocumento'];
				$this->_getFile($url_download, $res_link->id_link, 'zip');
				include 'ler_arquivo_bovespa_v1.php';
	
			} elseif(!empty($queryArray['razao'])) {
	
				$queryArray['razao'] = str_replace(' ', '%20', $queryArray['razao']);
				$queryArray['pregao']= str_replace(' ', '%20', $queryArray['pregao']);
				$url_download ="http://www.bmfbovespa.com.br/dxw/Download.asp?moeda=L&site=".$queryArray['site']."&mercado=".$queryArray['mercado']."&razao=".$queryArray['razao']."&pregao=".$queryArray['pregao']."&ccvm=".$queryArray['ccvm']."&data=".$queryArray['data']."&tipo=1";//.$queryArray['tipo'];
	
				$this->_getFile($url_download, $res_link->id_link, 'WTL');
				include 'ler_arquivo_bovespa_v2.php';
			}
	
			if(!empty($response)):
			 	foreach ($response AS $linha):
					$documento->setValueByCode(
						1,
						"{$linha['codigo']}",
						"{$linha['descricao']}",
						$linha['val_1'],
						$linha['val_2'],
						$linha['val_3'],
						$linha['val_4']
					);
				endforeach;
				$documento->inserir();
			endif;
		}else{
			$documento->loadDoc($id_documento_bovespa);
		}
		
		return $documento;
	}
}
?>