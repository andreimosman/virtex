<?

/**
 * Arquivo de defini��es
 */
require_once("defs.php");

$sys = new VAInterface_cliente_home();
//$sys->executa();
if( $sys->userLogin() ) {
   $sys->processa(@$_REQUEST["op"]);
}

$sys->exibe();




?>
