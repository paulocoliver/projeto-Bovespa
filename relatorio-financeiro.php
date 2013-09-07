<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Empresa.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Link.php';

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
<script>
    function empresasFormatResult(empresas) {
        var markup = "<table class='movie-result'><tr><td class='movie-info'>";
        markup += "<div class='movie-title'>" + empresas.nome + "</div>";
        //markup += "<div class='movie-synopsis'>" + empresas.cvm + "</div>";
        markup += "</td></tr></table>"
        return markup;
    }

    function movieFormatSelection(empresas) {
        return empresas.nome;
    }
    $(document).ready(function() {
        $("#elemEmpresas").select2({
            placeholder: "Escolha a empresa",
            minimumInputLength: 1,
            id: 'cvm',
            ajax: {
                url: "/ajax-empresas.php",
                dataType: 'json',
                quietMillis: 100,
                data: function (term, page) { // page is the one-based page number tracked by Select2
                    return {
                        q: term, //search term
                        page_limit: 10, // page size
                        page: page, // page number
                    };
                },
                results: function (data, page) {
                    var more = (page * 10) < data.total; // whether or not there are more results available
                    return {results: data.empresas, id: 'cvm', more: more};
                }
            },
            initSelection: function(element, callback) {
                var cvm = $(element).val();                
                if (cvm !== "") {
                    $.ajax("/ajax-empresas.php?cvm="+cvm, {
                        data: {},
                        dataType: "json"
                    }).done(function(data) { 
                        callback(data); 
					});
                    getLinksAjax(cvm);
                }
            },
            formatResult: empresasFormatResult, // omitted for brevity, see the source of this page
            formatSelection: movieFormatSelection, // omitted for brevity, see the source of this page
            dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
            escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
        });

        function getLinksAjax(cvm) {
        	$.ajax({
				type: "GET",
				url: "/ajax-links.php?cvm="+cvm,
				dataType: "json"
			}).done(function(json) {
				if (json.sucess == true) {
					$("#elemItens").select2({
			            data:json.links
			        });
					$("#elemItens").select2("open");
					$("#relatorio-trimestre").html('');
				}	
				
				if (json.msg)
					alert(json.msg);
			});
		}
		
        $("#elemEmpresas").on("select2-selecting", function(e) {
        	getLinksAjax(e.val);
		});

        $("#elemItens").select2({data:[]});
        $("#elemItens").on("select2-selecting", function(e) {  

            var img = $('<img>');
            img.css('display', 'block');
            img.css('margin', '20px auto');
            img.attr('src', '/include/img/loading.gif');
            img.on("load", function(evt) {
            	$.ajax({
    				type: "GET",
    				url: "/ajax-result.php?id_link="+e.val,
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