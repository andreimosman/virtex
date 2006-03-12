<?

/**
 * Arquivo de definições
 */
require_once("defs.php");


require_once("jpgraph.php");
require_once("jpgraph_pie.php");
require_once("jpgraph_pie3d.php");



$sys = new VAGrafico();
if( $sys->adminLogin() ) {
   $sys->processa(@$_REQUEST["op"]);
}
$sys->exibe();




?>
