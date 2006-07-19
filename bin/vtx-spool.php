<?
	require_once("MUtils.class.php");

	// Volta para a base do sistema (o diretório anterior);
	chdir(MUtils::getPwd() . "/..");

	require_once( "defs.php" );
	require_once(PATH_LIB."/VABESpool.class.php");
	
	
	
	
	$sp = new VABESpool();
	
	$sp->executa();
	
	
	

?>