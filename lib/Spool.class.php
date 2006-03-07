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
	
	
	
	function bandalargaAdicionaRede($destino,$id_conta,$rede,$mac,$banda_upload_kbps,$banda_download_kbps){
		// Insere em sptb_spool instru��es para incluir um cliente de bandalarga
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "sptb_spool ( ";
		$sSQL .= "		registro,destino,tipo,op,id_conta,parametros,status ";
		$sSQL .= ") VALUES ( ";
		$sSQL .= "		now(), '".$destino."', 'BL', 'a', '". $rede ."', '".$id_conta.",".$mac.",".$banda_upload_kbps.",".$banda_download_kbps."','A' ";
		$sSQL .= ") ";
		
		$this->bd->consulta($sSQL);
		
		
		
		return;

	}
	
	function bandalargaExcluiRede($destino,$id_conta,$rede){
	
		$sSQL  = "INSERT INTO ";
		$sSQL .= "sptb_spool ( ";
		$sSQL .= "	registro,destino,tipo,op,id_conta,parametros,status ";
		$sSQL .= ") VALUES ( ";
		$sSQL .= "now(), '".$destino."', 'BL', 'x', '". $id_conta ."', '".$rede."' ";
		$sSQL .= ") ";

		$this->bd->consulta($sSQL);
		
		return;
	
	
	
	}
	
	
	

}
?>
