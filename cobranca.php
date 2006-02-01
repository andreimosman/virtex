<?

/**
 * Arquivo de definições
 */
require_once("defs.php");



// Instancia a classe pertinente ao modulo usado (VACOBRANCA() instancia a classe em VACobranca.class.php
$sys = new VACobranca();
//$sys->executa();
$sys->processa($_REQUEST["op"]);
$sys->exibe();




?>
