<?

/**
 * Arquivo de definições
 */
require_once("defs.php");
require_once(PATH_LIB . '/VAAjax.class.php');

$sys = new VAAjax();
//$sys->executa();
if( $sys->adminLogin() ) {
   $sys->processa(@$_REQUEST["op"]);
}
$sys->exibe();

?>
