<?

/**
 * Arquivo de defini��es
 */
require_once("defs.php");




$sys = new VARelatorio();
//$sys->executa();
$sys->processa(@$_REQUEST["op"]);
$sys->exibe();




?>
