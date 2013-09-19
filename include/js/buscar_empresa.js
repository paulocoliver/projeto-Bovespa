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
            url: "/include/ajax/ajax-empresas.php",
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
                $.ajax("/include/ajax/ajax-empresas.php?cvm="+cvm, {
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
			url: "/include/ajax/ajax-links.php?cvm="+cvm,
			dataType: "json"
		}).done(function(json) {
			if (json.sucess == true) {
				$("#elemItens").select2({
		            data:json.links
		        });
				if(json.links.length){
					$("#elemItens").select2("open");
					$("#relatorio-trimestre").html('');
				}
			}	
			
			if (json.msg)
				alert(json.msg);
		});
	}
    
    $("#elemEmpresas").on("select2-selecting", function(e) {
    	getLinksAjax(e.val);
	});
});