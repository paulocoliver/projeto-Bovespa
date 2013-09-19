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
			getFile($url_download, $res_link->id_link, 'zip');
			
			include 'ler_arquivo_bovespa_v1.php';
				
		} elseif(!empty($queryArray['razao'])) {
				
			$queryArray['razao'] = str_replace(' ', '%20', $queryArray['razao']);
			$queryArray['pregao']= str_replace(' ', '%20', $queryArray['pregao']);
			$url_download ="http://www.bmfbovespa.com.br/dxw/Download.asp?moeda=L&site=".$queryArray['site']."&mercado=".$queryArray['mercado']."&razao=".$queryArray['razao']."&pregao=".$queryArray['pregao']."&ccvm=".$queryArray['ccvm']."&data=".$queryArray['data']."&tipo=1";//.$queryArray['tipo'];
		
			getFile($url_download, $res_link->id_link, 'WTL');
			include 'ler_arquivo_bovespa_v2.php';
		}
		
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
	}else{
		$documento->loadDoc($id_documento_bovespa);
 	}
	$colunas = $documento->getColunas();
	
	$mes = substr($documento->getData(),5,2);
	$ano = substr($documento->getData(),0,4);
	
	switch($mes):
		case '06' : 
			$th1 = "01/04/$ano à 30/06/$ano";
			$th2 = "01/01/$ano à 30/06/$ano";
			$th3 = "01/04/".($ano-1)." à 30/06/".($ano-1);
			$th4 = "01/01/".($ano-1)." à 30/06/".($ano-1);
		break;
		case '09' : 
			$th1 = "01/07/$ano à 30/09/$ano";
			$th2 = "01/01/$ano à 30/09/$ano";
			$th3 = "01/07/".($ano-1)." à 30/09/".($ano-1);
			$th4 = "01/01/".($ano-1)." à 30/09/".($ano-1);
		break;
		default : 
			$th1 = "01/01/$ano à 31/03/$ano";
			$th3 = "01/01/".($ano-1)." à 30/03/".($ano-1);
		break;	
	endswitch;
	?>
	
	<div id="myTabContent" class="tab-content">
		<table class="table table-bordered table-striped">
			<tbody>
				<tr>
					<th colspan="6" style="text-align: center;"><?php echo utf8_encode($documento->getDescricao()); ?></th>
				</tr>
				<tr>
					<th style="text-align: center;">Código da Conta</th>
					<th style="text-align: center;">Descrição da Conta</th>
					<th style="text-align: center;"><?php echo $th1?></th>
					<?php if(isset($th2)):?>
						<th style="text-align: center;"><?php echo $th2?></th>
					<?php endif;?>
					
					<th style="text-align: center;"><?php echo $th3?></th>
					
					<?php if(isset($th4)):?>
						<th style="text-align: center;"><?php echo $th4?></th>
					<?php endif;?>
				</tr>
				
	<?php foreach ($colunas AS $coluna): ?>
				<tr>
					<td><?php echo $coluna['codigo'] ?></td>
					<td><?php echo utf8_encode($coluna['descricao']) ?></td>
					<td><?php echo $coluna['valor'] ?></td>
					
					<?php if(isset($th2)):?>
						<td><?php echo $coluna['total'] ?></td>
					<?php endif;?>
					
					<td><?php echo $coluna['valor_ano_anterior'] ?></td>
					
					<?php if(isset($th4)):?>
						<td><?php echo $coluna['total_ano_anterior'] ?></td>
					<?php endif;?>
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