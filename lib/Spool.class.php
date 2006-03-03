<?
class Spool {
	
	protected $bd;
	
	function Spool($bd) {
		$this->bd = $bd;
	}
	
	function radiusAdicionaNAS($ip,$secret) {
		// Insere em sptb_spool instru��es para adicionar um nas no radius
		
		

		$sSQL  = "INSERT INTO ";
		$sSQL .= "	sptb_spool (";
		$sSQL .= "		registro,destino,tipo, op, id_conta, parametros, status ";
		$sSQL .= "	) VALUES (";
		$sSQL .= "		now(),'RADIUS','RD', 'a', NULL, '". $ip .",". $secret ."', 'A' ";
		$sSQL .= "	) ";
		
		$this->bd->consulta($sSQL);
		
		return;
		


	}
	
	function radiusExcluiNAS($ip) {
		// Insere em sptb_spool instru��es para excluir um nas do radius
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "	sptb_spool ( ";
		$sSQL .= "     registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= "  ) VALUES (";
		$sSQL .= "     now(),'RADIUS','RD', 'x', NULL, '". $ip ."', 'A' ";
		$sSQL .= "  ) ";
		
		$this->bd->consulta($sSQL);
		
		return;

				
	}

}
?>
