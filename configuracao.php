<?

/**
 * Arquivo de definições
 */
require_once("defs.php");




$sys = new VAConfiguracao();
//$sys->executa();
$sys->processa(@$_REQUEST["op"]);
$sys->exibe();




?>
