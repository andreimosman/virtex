<?

/**
 * Arquivo de definições
 */
require_once("defs.php");

$sys = new VAInterface_cliente_home();
//$sys->executa();
if( $sys->UserLogin() ) {
   $sys->processa(@$_REQUEST["op"]);
}

$sys->exibe();




?>
