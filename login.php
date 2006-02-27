<?

/**
 * Arquivo de definições
 */
require_once("defs.php");

$sys = new VALogin();
$sys->processa(@$_REQUEST["op"]);
$sys->exibe();

?>
