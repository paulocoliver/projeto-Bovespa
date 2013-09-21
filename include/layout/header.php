<?php
	date_default_timezone_set('America/Sao_Paulo');
	include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Auth/Usuario.php';
	if(!isset($auth)){
		$auth = new Auth_Usuario();
	}
	$authenticated = $auth->isAuthenticated();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF8">
	<title><?php echo $titlePage ?></title>

	<script type="text/javascript" src="/include/js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="/include/js/jquery-mask.js"></script>
	<script type="text/javascript" src="/include/js/jquery-ui-1.10.3.custom.js"></script>
	<script type="text/javascript" src="/include/js/bootstrap.js"></script>

	<!-- Le styles -->
    <link href="/include/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
 		body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="/include/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/include/js/select2/select2.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="/include/css/layout.css">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="/js/html5.js"></script>
    <![endif]-->
</head>
<body>

<div id="header">
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
        	<div class="container">
				<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            		<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="brand" href="#">Projeto Bovespa</a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li class=""><a href="/index.php">Lista de Empresas</a></li>
						<li class=""><a href="/relatorio-financeiro.php">Relatórios Financeiros</a></li>
					</ul>
					<ul class="nav" style="float:right">
						<?php if($authenticated): ?>
							<li class=""><a href="/usuario/comparar.php">Comparar Empresas</a></li>
							<li class=""><a href="/usuario/index.php">Minhas DRE's</a></li>
							<li class=""><a href="/usuario/logout.php">Logout</a></li>
						<?php else:?>
							<li class=""><a href="/usuario/login.php">Login / Cadastrar</a></li>
						<?php endif;?>
					</ul>
          		</div>
			</div>
		</div>
    </div>
</div>

<div id="content" >
	<div>
