<?php
include_once 'auth.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';

$documento = new DocumentoUsuario($auth->getUsuario('id_usuario_empresa'));

if(!empty($_GET['id'])){
	$documento->loadDoc($_GET['id']);
}else{
	$documento->newDoc(1);
	if(empty($_POST)){
		$datasEmUso = $documento->getDataEmUso();
		$datas = array();
		
		for($i = 2010 ; $i <= date("Y") ; $i++ ){
			if(!in_array("31-03-$i", $datasEmUso)){
				$datas[]= "31-03-$i";
			}
			if(!in_array("30-06-$i", $datasEmUso)){
				$datas[]= "30-06-$i";
			}
			if(!in_array("30-09-$i", $datasEmUso)){
				$datas[]= "30-09-$i";
			}
		}
	}
}

if(!empty($_POST)){
	
	if(isset($_POST['data'])){
		$documento->setData($_POST['data']);
		unset($_POST['data']);
	}
	
	foreach($_POST AS $key => $valor){
		$documento->setValue($key, $valor);
	}
	$documento->inserir();
	header("location:/usuario/index.php");
}
$colunas = $documento->getColunas();

$titlePage = 'DRE';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/header.php';
?>

<script type="text/javascript">
$(document).ready(function(){
	$('.money').mask("#.##0,00", {reverse: true, maxlength: false});
});
</script>

<style>
<!--
@import url("/include/css/usuario/dre.css");
-->
</style>
<div class="container">

	<form action="" method="post" id="form_dre">
		
		<?php if(isset($datas)):?>
			<div id="datas-dre">
				<label for="data">Data</label>
				<select name="data" id="data" required="true" >
					<option value="">Selecionar...</option>
					<?php foreach($datas AS $data):?>
						<option value="<?php echo $data?>"><?php echo $data?></option>
					<?php endforeach;?>
				</select>
			</div>
		<?php endif;?>
		
		<a id="retornar" href="/usuario/index.php">Retornar</a>
		<div style="cler:both"></div>
	
		<table id="lista_dre">
			<tbody>
				<tr>
					<th style="width:100px;">Código</th>
					<th>Descricao</th>
					<th style="width:100px;">Valor</th>
				</tr>
				<?php 
					if(is_array($colunas)):
						$i=0;
						foreach ($colunas AS $coluna) :
				?>
							<tr style="background-color:<?php echo $i%2==0?'white':'#f5f5f5';?>">
								<td><?php echo utf8_encode($coluna['codigo'])?></td>
								<td><?php echo utf8_encode($coluna['descricao'])?></td>
								<td>
									<input placeHolder="R$0,00" type="text" class="money" name="<?php echo $coluna['id_coluna']?>" value="<?php echo $coluna['valor']?>"/>
								</td>
							</tr>
				<?php 
							$i++;
						endforeach;
					endif;
				?>
			</tbody>
		</table>
		<button type="submit" class="salvar">Gravar</button>
	</form>
</div>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/footer.php';?>
