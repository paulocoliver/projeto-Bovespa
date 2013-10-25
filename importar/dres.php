<?php
include $_SERVER['DOCUMENT_ROOT'].'/include/class/Bovespa/Link.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/BuscarDocumento.php';
if(!empty($_POST)){
	try{
		if(empty($_POST['id_link'])){
			throw new Exception('Link não informado');
		}
		$buscarDoc = new BuscarDocumento();
		$documento = $buscarDoc->getDocumento($_POST['id_link']);
		
		$response = array('success' => true);
		
	}catch(Exception $erro){
		$response = array('success' => false, 'msg' => $erro->getMessage());
	}
	die(json_encode($response));
}
$link  = new Link();
$links = $link->getLinks();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF8">

<title>Importação de dres</title>
<script type="text/javascript" src="/include/js/jquery-1.9.1.js"></script>

<script type="text/javascript">

var fila = <?php echo json_encode($links)?>;
var count = 0;
var process = 50;
var importar;
var request = new Array();
var partes;

importar = function (piece, num, call){
	
	request[piece] = $.ajax({
	     type    : 'POST',
	     dataType: 'json', 
	     data : { 
			id_link	 : fila[num].id_link
		 },
		 //async: false,
	     success : function(response){

	    	num++;
			$('#download-concluido').text(count++);
			if(call < partes && typeof fila[num] != 'undefined'){
				importar(piece, num, ++call);
			}else{
				$('#status').text('concluido');
			}
			
			if(response.success == false){
				console.log(response.msg);
			}
	     },
	     error:function(data){
	    	 cancelar();
	    	 $('#status').text('ERRO: Operação cancelada');
	    	 if(data.responseText){
		    	 alert(data.responseText);
	    	 }
	     }
	});
};

function iniciar(){
	
	$('#status').text('importando...');
	$('#iniciar').attr('disabled',true);
	$('#cancelar').attr('disabled',false);
	
	partes = parseInt(fila.length/process);

	for(var i = 0 ; i < process ; i++){
		importar(i, i*partes, 0);	
	}
}

function cancelar(){
	$('#iniciar').attr('disabled',false);
	$('#cancelar').attr('disabled',true);
	
	for(var index in request)
		request[index].abort();		
}

</script>

<style type="text/css">
#content{
	position:relative;
	margin:100px auto;
	width :500px;
	border:2px solid lightgray;
	box-shadow:0 0 2px lightgray;
	padding:20px;
}
#content > div{
	text-align:center;	
}
#step_1 > button{
	float:right;
	margin: 10px 46px 0 0;
}
</style>
</head>
	<body>
		<div id="content">
			
			<div>
				<div>
					<span id="download-concluido">0</span> / <span id="total"><?php echo count($links)?></span> Dres.
				</div>
				<p id="status"></p>
				
				<button id="iniciar" onclick="iniciar(this)">Iniciar</button>
				<button id="cancelar" onclick="cancelar(this)" disabled="disabled">Cancelar</button>
				
				<div id="historico">
				</div>
			</div>
		</div>
	</body>
</html>

