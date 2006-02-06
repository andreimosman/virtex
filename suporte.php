<?

/**
 * Arquivo de definições
 */
require_once("defs.php");




$sys = new VASuporte();
//$sys->executa();
@$sys->processa($_REQUEST["op"]);
$sys->exibe();




?>
