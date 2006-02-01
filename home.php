<?

/**
 * Arquivo de definições
 */
require_once("defs.php");




$sys = new VAHome();
//$sys->executa();
$sys->processa($_REQUEST["op"]);
$sys->exibe("home.html");




?>
