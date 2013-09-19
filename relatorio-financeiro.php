<?php 
include_once 'include/class/Bovespa/Empresa.php';
include_once 'include/class/Bovespa/Link.php';

if(!empty($_GET['cvm'])){
	$empresa = new Empresa();
	$res = $empresa->getEmpresa($_GET['cvm']);
}else{
	$res = new stdClass();
	$res->nome = '';
	$res->cvm  = '';
}
?>
<?php
$titlePage = 'Relatórios Financeiros';
include('include/layout/header.php')
?>
<script type="text/javascript">
	
 	/*$(document).ready(function() {
		
		$('#relatorio-trimestre').on('click','a[data-tab]',function (e) {
			e.preventDefault();
			var open = function(tab){
				$('#'+$(tab).data('tab')).show('slide',{ direction :'right'},1000);
				$(tab).addClass('active');
			}
			var active = $('.active');
			if(active.length){	
				$('#'+$(active).data('tab')).hide('slide',{ direction :'left'}, 1000, open(this));
				active.removeClass('active'); 		
			}
		});
 	});*/

 	
</script>

<style type="text/css">
.movie-result td {vertical-align: top }
.movie-info { padding-left: 10px; vertical-align: top; }
.movie-title { font-size: 15px; padding-bottom: 2px; }
.movie-synopsis { font-size: 12px; }
.nav-tabs > li > a {font-size: 12px;}
/*.tab-pane {float: left;}*/
.col-valor{
	text-align:right !important;
	width:60px;
}
</style>

<div class="container">
	<div class="page-header">
		<h1>Relatórios Financeiros</h1>
	</div>
	<div class="row">
		<div class="span6">
			<label for="elemEmpresas" class="label label-info">Empresa</label>
			<input type="hidden" id="elemEmpresas" value="<?php echo $res->cvm ?>" style="width:100%"/>
		</div>
		<div class="span6">
			<label for="elemItens" class="label label-info">Informações Trimestrais - ITR</label>
			<input type="hidden" id="elemItens" value="" style="width:100%"/>
		</div>	
	</div>
	<div class="row">
		<div class="span12">
			<br><br>
			<div id="relatorio-trimestre"></div>
		</div>
	</div>
</div>

<script type="text/javascript" src="/include/js/select2/select2.js"></script>
<script type="text/javascript" src="/include/js/buscar_empresa.js"></script>
<script type="text/javascript">
	$(document).ready(function(){

	    $("#elemItens").select2({data:[]});
	    $("#elemItens").on("select2-selecting", function(e) {  

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
	        $("#relatorio-trimestre").html(img);
	    });
	});
</script>
<?php include('include/layout/footer.php');?>