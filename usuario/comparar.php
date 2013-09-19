<?php
include_once 'auth.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';

$documento = new DocumentoUsuario($auth->getUsuario('id_usuario_empresa'));
$documento->newDoc(1);

$colunas = $documento->getColunas();
$datas   = $documento->getDataEmUso();
$response = array();
foreach($datas AS $data):
	$response[] = array( 'id' => $data, 'text' => $data );
endforeach;

include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/header.php';
?>
<script type="text/javascript" src="/include/js/select2/select2.js"></script>
<script type="text/javascript" src="/include/js/buscar_empresa.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#usuario-dre").select2({
	    data:<?php echo json_encode($response);?>
	});
});
</script>
<style>
<!--
@import url("/include/css/usuario/dre.css");
@import url("/include/css/usuario/comparar.css");
-->
</style>
<div class="container">
		
		<form actino="" method="post" id="select-comparar">
			
			<div class="row">
				<div class="span4">
					<label for="usuario-dre" class="label label-info">Comparar minha DRE do périodo : </label>
					<input type="hidden" id="usuario-dre"  style="width:100%" />
				</div>
				<div class="span4">
					<label for="elemEmpresas" class="label label-info">Com a da empresa : </label>
					<input type="hidden" id="elemEmpresas" value="<?php echo $res->cvm ?>" style="width:100%"/>
				</div>
				<div class="span4">
					<label for="elemItens" class="label label-info">Do périodo : </label>
					<input type="hidden" id="elemItens" value="" style="width:100%"/>
				</div>	
			</div>
			
			<button type="submit">Buscar</button>
	
		</form>
		
		<table id="lista_dre">
			<tbody>
				<tr>
					<th style="width:100px;">Código</th>
					<th>Descricao</th>
				</tr>
				<?php 
					if(is_array($colunas)):
						$i=0;
						foreach ($colunas AS $coluna) :
				?>
							<tr style="background-color:<?php echo $i%2==0?'white':'#f5f5f5';?>">
								<td><?php echo utf8_encode($coluna['codigo'])?></td>
								<td><?php echo utf8_encode($coluna['descricao'])?></td>
							</tr>
				<?php 
							$i++;
						endforeach;
					endif;
				?>
			</tbody>
		</table>
</div>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/footer.php';?>
