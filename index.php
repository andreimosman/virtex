<?

/**
 * Arquivo de defini��es
 */
require_once("defs.php");

$sys = new VAInterface_cliente();
$sys->processa(@$_REQUEST["op"]);
$sys->exibe();

?>
