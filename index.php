<?php 
	include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Empresa.php';
	
	$ch = !empty($_GET['ch']) ? $_GET['ch'] : 'a';
	$empresa = new Empresa();
	$response = $empresa->getEmpresas($ch);
	$listaCh  = $empresa->getLista();
?>

<?php 
$titlePage = 'Lista de empresas';
include('include/layout/header.php')
?>


<div class="container">
	<div class="row ">
		<div class="span12">
			<div class="page-header">
				<h1>Lista de empresas</h1>
			</div>
			<div id="paginacao">
			<?php foreach($listaCh as $ch):?>
				<a href="/index.php?ch=<?php echo $ch?>"><?php echo $ch?></a>
			<?php endforeach;?>
			</div>
			<table id="lista-empresas" class="table table-bordered table-striped">
				<tbody>
					<tr>
						<th style="width: 135px;">CNPJ</th>
						<th>NOME</th>
						<th style="width: 80px;">CVM</th>
						<th style="width: 160px;">SITUAÇÃO REGISTRO</th>
						<th style="width: 80px;">OPÇÕES</th>
					</tr>
				
				<?php 
				if(count($response)):
					$i = 0;
					foreach( $response as $res ): 
				?>
						<tr >
							<td><?php echo $res->cnpj?></td>
							<td><?php echo utf8_encode($res->nome)?></td>
							<td><?php echo $res->cvm?></td>
							<td><?php echo utf8_encode($res->situacao_registro)?></td>
							<td class="link-view">
								<a href="/relatorio-financeiro.php?cvm=<?php echo $res->cvm?>">
									<img src="/imagem/icon/view.png" alt="Visualizar" title="Visualizar" />
								</a>
							</td>
						</tr>
				<?php 
						$i++;
					endforeach;
				else:
				?>
					<tr>
						<td style="color:red; text-align:center" colspan="5">Nenhum registro encontrado.</td>
					</tr>
				<?php 
				endif;
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>		

<?php include('include/layout/footer.php');?>