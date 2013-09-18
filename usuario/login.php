<?php
if(!empty($_POST['email'])){
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Auth/Usuario.php';
	$auth = new Auth_Usuario();
	
	try{
		if(!empty($_POST['razao_social'])){
			$auth->cadastrar(
				$_POST['razao_social'], 
				$_POST['cnpj'], 
				$_POST['email'], 
				$_POST['senha']
			);
		}else{
			$auth->login($_POST['email'], $_POST['senha']);
		}
	}catch(Exception $erro){
		$msg = $erro->getMessage();
	}
	$auth=null;
}
include_once 'auth.php';
$titlePage = 'Login';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/header.php';
?>
<script type="text/javascript">
$(document).ready(function(){
	<?php if(!empty($msg)):?>
		alert('<?php echo $msg?>');
	<?php endif;?>
	
	$("#cnpj").mask("99.99.999/9999-99");
	$('.mudar_tela').bind('click',function(){

		var ref = $(this).data('ref');
		$(this).closest('.tela').hide('fade',{},500,function(){
			$('#'+ref).show('fade',{},500);
		});
	});
});
</script>
<style>
<!--
@import url("/include/css/usuario/login.css");
-->
</style>

<div id="container_acesso">

	<div class="tela" id="login">
		<div class="mudar_tela" data-ref="cadastrar"> Cadastrar </div>
		<div style="clear:both"></div>
		<form action="" method="post">
			<label for="email">Email</label>
			<input type="email" name="email" id="email" required />
			
			<label for="senha">Senha</label>
			<input type="password" name="senha" id="senha" required />
			
			<button>Login</button>
			<div style="clear:both"></div>
		</form>
	</div>
	
	<div class="tela" id="cadastrar" style="display:none">
		<div class="mudar_tela" data-ref="login"> Login </div>
		<div style="clear:both"></div>
		<form action="" method="post">
			<label for="cad_email">Email</label>
			<input type="email" name="email" id="cad_email" required />
			
			<label for="senha">Senha</label>
			<input type="password" name="senha" id="senha" required/>
			
			<label for="razao_social">Razão Social</label>
			<input type="text" name="razao_social" id="razao_social" required />
			
			<label for="cnpj">CNPJ</label>
			<input type="text" name="cnpj" id="cnpj" required />
			
			<button>Cadastrar</button>
			<div style="clear:both"></div>
		</form>
	</div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/footer.php';?>