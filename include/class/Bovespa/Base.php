<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Connection/Abstract.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/simple_html/simple_html_dom.php';

abstract class Base extends Connection{
	
	protected $_lista;
	protected $_cookie 	= 'ASP.NET_SessionId=34i0whjzzittfn45sye22f45; __utma=121602778.2006470138.1377353516.1377353516.1377353516.1; __utmc=121602778; __utmz=121602778.1377353516.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); CVMWebCookie=SessionKey=98485118-f324-4cca-8532-c1b33d9061ef';
	
	public function __construct(){
		date_default_timezone_set('America/Sao_Paulo');
		ini_set('max_execution_time', 0);
		$this->_lista = array(
			'A','B','C','D','E','F','G','H','I','J','K','L','M',
			'N','O','P','Q','R','S','T','U','V','W','Y','X','Z',
			'0','1','2','3','4','5','6','7','8','9'
		);
	}
	
	public function setCookie($cookie){
		$this->_cookie = $cookie;
	}
	
	public function getLista(){
		return $this->_lista;
	}

	public function getOutput($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Cookie:'.$this->_cookie,
		));
		curl_setopt($ch, CURLOPT_URL, $url);
		
		$output = str_get_html(curl_exec($ch));
		curl_close($ch);
		
		if(empty($output)){
			die("Nenhum registro encontrado");
		}
		return $output;
	}
}