<?

/**
 * Arquivo de defini��es
 */
require_once("defs.php");




$sys = new VASuporte();
//$sys->executa();
if( $sys->adminLogin() ) {
   $sys->processa(@$_REQUEST["op"]);
}
$sys->exibe();




?>
