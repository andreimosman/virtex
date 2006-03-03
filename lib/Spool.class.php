<?
class Spool {
	
	protected $bd;
	
	function Spool($bd) {
		$this->bd = $bd;
	}
	
	function radiusAdicionaNAS($ip,$secret) {
		// Insere em sptb_spool instruções para adicionar um nas no radius

	}
	
	function radiusExcluiNAS($ip) {
		// Insere em sptb_spool instruções para excluir um nas do radius
	}

}
?>
