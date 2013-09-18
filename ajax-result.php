<?php 
include_once 'include/class/Bovespa/Link.php';
include_once 'include/class/Documento/DocumentoBovespa.php';

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
		
	} catch (Exception $e) {
		return '';
	}
}

try{
	$id_link  = !empty($_GET['id_link']) ? $_GET['id_link'] : null;
	$link 	  = new Link();
	$res_link = $link->getLink($id_link);
	
	if (empty($res_link))
		throw new Exception('Link não encontrado');
	
	$documento = new DocumentoBovespa($res_link->cvm);
	$documento->setData($res_link->data);
	$id_documento_bovespa = $documento->selectByLink();
	
	//if(empty($id_documento_bovespa)){
		
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
			getFile($url_download, $res_link->id_link, 'zip');
				
			include 'ler_arquivo_bovespa_v1.php';
				
		} elseif(!empty($queryArray['razao'])) {
				
			$queryArray['razao'] = str_replace(' ', '%20', $queryArray['razao']);
			$queryArray['pregao']= str_replace(' ', '%20', $queryArray['pregao']);
			$url_download ="http://www.bmfbovespa.com.br/dxw/Download.asp?moeda=L&site=".$queryArray['site']."&mercado=".$queryArray['mercado']."&razao=".$queryArray['razao']."&pregao=".$queryArray['pregao']."&ccvm=".$queryArray['ccvm']."&data=".$queryArray['data']."&tipo=1";//.$queryArray['tipo'];
		
			getFile($url_download, $res_link->id_link, 'WTL');
			include 'ler_arquivo_bovespa_v2.php';
		}
		
// 		foreach ($response AS $linha):
// 			$documento->setValueByCode("{$linha['codigo']}", $linha['val_1']);
// 		endforeach;
		
// 		$documento->inserir();
// 	}else{
// 	}
	?>
	<div id="myTabContent" class="tab-content">
		<table class="table table-bordered table-striped">
			<tbody>
				<tr>
					<th colspan="6" style="text-align: center;"><?php echo $documento->getDescricao(); ?></th>
				</tr>
				<tr>
					<th style="text-align: center;">Código da Conta</th>
					<th style="text-align: center;">Descrição da Conta</th>
					<th style="text-align: center;"></th>
					<th style="text-align: center;"></th>
					<th style="text-align: center;"></th>
					<th style="text-align: center;"></th>
				</tr>
				
	<?php foreach ($response AS $linha): ?>
				<tr>
					<td><?php echo $linha['codigo'] ?></td>
					<td></td>
					<td><?php echo $linha['val_1'] ?></td>
					<td><?php echo $linha['val_2'] ?></td>
					<td><?php echo $linha['val_3'] ?></td>
					<td><?php echo $linha['val_4'] ?></td>
				</tr>
	<?php endforeach; ?>
	
			</tbody>
		</table>
	</div>
	<br /><br />
<?php 	
} catch(Exception $e) {
	echo 'Desculpe ocorreu um erro: '.$e->getMessage();
}
?>