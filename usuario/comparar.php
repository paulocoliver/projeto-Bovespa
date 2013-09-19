<?php
include_once 'auth.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';

$documento = new DocumentoUsuario($auth->getUsuario('id_usuario_empresa'));
$DREs 	   = $documento->select();
$response  = array();
foreach($DREs AS $DRE):
	$response[] = array( 'id' => $DRE['id_documento_usuario_empresa'], 'text' => $DRE['data'] );
endforeach;

$titlePage = 'Comparar DRE\'s';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/header.php';
?>
<script type="text/javascript" src="/include/js/select2/select2.js"></script>
<script type="text/javascript" src="/include/js/buscar_empresa.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	$("#usuario-dre").select2({
	    data:<?php echo json_encode($response);?>
	});

    $("#select-comparar").bind("submit", function(event) {  
    	 event.preventDefault();
    	 var check = $( this ).serializeArray();

		 for(var index in check){
			if( check[index].value == ''){
               alert("Selecione todos os campos");
               return;
			}
	     }

		 var img = $('<img>');
	     img.css('display', 'block');
	     img.css('margin', '20px auto');
	     img.attr('src', '/include/img/loading.gif');
	     img.on("load", function(evt) {
	    	$.ajax({
				type: "GET",
				url: "/include/ajax/ajax-result.php?id_link="+e.val,
				dataType: "html"
			}).done(function(data) {
				$("#relatorio-trimestre").html(data);
			});
	     });
	     $("#relatorio").html(img);
		    
    	 $.ajax({
			type: "GET",
			url: "/include/ajax/ajax-comparativo.php?"+$( this ).serialize(),
			dataType: "html"
		 }).done(function(data) {
			 img.remove();
			$("#relatorio").html(data);
		 });
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
				<div class="span6">
					<label for="usuario-dre" class="label label-info">Comparar minha DRE do périodo : </label>
					<input type="hidden" id="usuario-dre" name="documento_usuario" style="width:100%" required/>
				</div>
				<div class="span6">
					<label for="elemEmpresas" class="label label-info">Com a da empresa : </label>
					<input type="hidden" id="elemEmpresas" name="cvm" style="width:100%" required/>
				</div>
			</div>
			
			<button type="submit">Buscar</button>
		</form>
		
		<div class="row">
			<div class="span12">
				<br><br>
				<div id="relatorio"></div>
			</div>
		</div>
</div>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/footer.php';?>
