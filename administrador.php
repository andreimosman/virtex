<?

/**
 * Arquivo de definições
 */
require_once("defs.php");




$sys = new VAAdministrador();
//$sys->executa();
if( $sys->adminLogin() ) {
   $sys->processa(@$_REQUEST["op"]);
}
$sys->exibe();




?>
