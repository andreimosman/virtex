<?

/**
 * Arquivo de defini��es
 */
require_once("defs.php");




$sys = new VAConfiguracao();
//$sys->executa();
$sys->processa(@$_REQUEST["op"]);
$sys->exibe();




?>
