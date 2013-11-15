<?php
function normalize($params, $id, $text){
	$response = array();
	foreach($params AS $param):
		array_push($response, array('id' => $param[$id], 'text' => utf8_encode($param[$text]))); 
	endforeach;
	
	return $response;
}

include_once '../auth.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Segmento/Segmento.php';

$documento = new DocumentoUsuario($auth->getUsuario('id_usuario_empresa'));
$segmento  = new Segmento();

$segmentos = normalize($segmento->select(), 'id_segmento', 'descricao');
$DREs 	   = normalize($documento->select(), 'id_documento_usuario_empresa', 'data');

$cols = $documento->getDocCols(1);

$titlePage = 'Comparar DRE\'s';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/header.php';
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="/include/js/select2/select2.js"></script>
<script type="text/javascript" src="/include/js/buscar_empresa.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	function check(){
		if( $("#usuario-dre").val() == '' ){
			return false;
		}
		if( $("#elemSegmento").val() == '' ){
			return false;
		}
		if( $('#listaEmpresas').find('input:checked').length == 0 ){
			return false;
		}
		return true;
	}
	
	function displaySubmit(){
		$('#submit').css('display', ( check() ? 'block' : 'none') );
		$('#relatorio').empty();
	}

	function loadingImage(){
		return $('<img>')
				.css('display', 'block')
	     		.css('margin', '20px auto')
	     		.attr('src', '/include/img/loading.gif');
	}
	
	$("#usuario-dre").select2({
	    data :<?php echo json_encode($DREs);?>
	})
	.on("change",function(){
		displaySubmit();
	})
	.select2(open ? "open": "close");
	
	$("#elemSegmento").select2({
		data:<?php echo json_encode($segmentos);?>
	}).on("change", function(e){
		
		var img = loadingImage();
		var lista = $("#listaEmpresas");
		lista.html(img);
	     
		var id = $(this).val();                
        if (id !== "") {
            $.ajax("/include/ajax/ajax-empresas-segmento.php?id_segmento="+id, {
                data: {},
                dataType: "json"
            }).done(function(data) { 
               	img.remove();

               	if(data.success){
	               	for(var index in data.empresas){
	                   	if(data.empresas[index]['nome']){
		               		var div   = $('<div>').addClass('span6').css('margin','0');
		                   	$('<input>')
                   			.attr('type','checkbox')
                   			.attr('checked','true')
                   			.attr('name','cvm[]')
                   			.attr('value',data.empresas[index]['cvm'])
                   			.attr('id',data.empresas[index]['cvm'])
                   			.on('change',function(){
								displaySubmit();
                       		})
                   			.appendTo(div);
		
		                   $('<label>')
		       				.text(data.empresas[index]['nome'])
		       				.attr('for',data.empresas[index]['cvm'])
		       				.appendTo(div);
		
		       				div.appendTo(lista);
	                   	}

	                   	displaySubmit();
	               	}
               	}else{
                   	alert(data.error)
               	}
			});
        }
        displaySubmit();
	});
	
    $("#select-comparar").bind("submit", function(event) {  

   	 	event.preventDefault();

		if( !check()){
           alert("Selecione todos os campos");
           return;
		}
	     
		var img = loadingImage();
		$("#relatorio").html(img);
			    
	   	$.ajax({
			type	 : "POST",
			data	 : $(this).serialize(),
			url		 : "/include/ajax/ajax-comparativo-segmento.php?",
			dataType : "text"
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
.grafico_cols {
	width: 100%;
}
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
					<label for="elemSegmento" class="label label-info">Com o Segmento : </label>
					<input type="hidden" id="elemSegmento" name="id_segmento" style="width:100%" required/>
				</div>
				
				<div class="span12" id="lista-empresas">
					<hr>
					<label for="listaEmpresas" class="label label-info">Filtrar Empresas </label>
					<div id="listaEmpresas">
					</div>
				</div>
				
				<div class="span12">
					<hr>
					<label for="listaEmpresas" class="label label-info">Dados do gráfico</label>
				</div>
				<div class="span6">
					<label for="listaEmpresas" class="">Gráfico X</label>
					<select name="grafico_col_x" class="grafico_cols">
						<?php
						$cols_options = '';
						foreach ($cols as $col) {
							$cols_options .= '<option value="'.$col['id_coluna'].'">'.utf8_encode($col['descricao']).'</option>';
						}
						echo $cols_options;
						?>
    				</select>
    			</div>
				<div class="span6">
					<label for="listaEmpresas" class="">Gráfico Y</label>
					<select name="grafico_col_y" class="grafico_cols">
						<?php
						echo $cols_options;
						?>
    				</select>
				</div>
				
			</div>
			
			<button type="submit" id="submit" style="display:none;">Buscar</button>
		</form>
		
		<div class="row">
			<div class="span12">
				<br><br>
				<div id="relatorio"></div>
			</div>
		</div>
</div>

<script type="text/javascript">
	$(document).ready(function() { $(".grafico_cols").select2(); });
	google.load("visualization", "1", {packages:["corechart"], 'language': 'pt-br'});
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/footer.php';?>


