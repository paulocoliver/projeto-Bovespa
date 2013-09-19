<?php
session_start();
$_SESSION['usuario']=null;
header("location:/usuario/login.php");