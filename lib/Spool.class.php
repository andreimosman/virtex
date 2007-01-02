<?
class Spool {
	
	protected $bd;
	
	function Spool($bd) {
		$this->bd = $bd;
	}
	
	function radiusAdicionaNAS($ip,$secret) {
		// Insere em sptb_spool instruções para adicionar um nas no radius
		/////////////
		
		

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
		// Insere em sptb_spool instruções para excluir um nas do radius
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "	sptb_spool ( ";
		$sSQL .= "     registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= "  ) VALUES (";
		$sSQL .= "     now(),'RADIUS','RD', 'x', NULL, '". $ip ."', 'A' ";
		$sSQL .= "  ) ";
		
		$this->bd->consulta($sSQL);
		
		return;

				
	}
	
	
	
	function bandalargaAdicionaRede($destino,$id_conta,$rede,$mac,$banda_upload_kbps,$banda_download_kbps,$username){
		// Insere em sptb_spool instruções para incluir um cliente de bandalarga
		
		
		$parametros = "$rede,$mac,$banda_upload_kbps,$banda_download_kbps,$username";
		
		if ($parametros != '' && $parametros){
			$sSQL  = "INSERT INTO ";
			$sSQL .= "sptb_spool ( ";
			$sSQL .= "		registro,destino,tipo,op,id_conta,parametros,status ";
			$sSQL .= ") VALUES ( ";
			$sSQL .= "		now(), '".$destino."', 'BL', 'a', '$id_conta', '$parametros' ,'A' ";
			$sSQL .= ") ";
			//echo "SPOOL INCLUIR: " . $sSQL . ";<br>\n";
			//
			//echo "ID: ". $id_conta . "<br>";
			//echo "REDE: ". $rede . "<br>";
			$this->bd->consulta($sSQL);
		}
		
		
		return;

	}
	
	function bandalargaExcluiRede($destino,$id_conta,$rede){
	
		$parametros = "$rede";
		
		if ($parametros != '' && $parametros){
	
			$sSQL  = "INSERT INTO ";
			$sSQL .= "sptb_spool ( ";
			$sSQL .= "	registro,destino,tipo,op,id_conta,parametros,status ";
			$sSQL .= ") VALUES ( ";
			$sSQL .= "now(), '".$destino."', 'BL', 'x', '". $id_conta ."', '".$parametros."', 'A' ";
			$sSQL .= ") ";
			//echo "SPOOL EXCLUIR: " . $sSQL . ";<br>\n";

			$this->bd->consulta($sSQL);
			
		}
		return;
	
	
	
	}
		function bandalargaExcluiRedePPPoE($destino,$id_conta,$rede){
		
			$parametros = "$rede";
			
			if ($parametros != '' && $parametros){

				$sSQL  = "INSERT INTO ";
				$sSQL .= "sptb_spool ( ";
				$sSQL .= "	registro,destino,tipo,op,id_conta,parametros,status ";
				$sSQL .= ") VALUES ( ";
				$sSQL .= "now(), '".$destino."', 'BL', 'k', '". $id_conta ."', '".$parametros."', 'A' ";
				$sSQL .= ") ";
				//echo "SPOOL EXCLUIR: " . $sSQL . ";<br>\n";

				$this->bd->consulta($sSQL);
			}
			return;
		
		
		
	}
	
	
	function hospedagemAdicionaRede($server,$id_conta, $tipo_hospedagem,$username,$dominio,$dominio_hospedagem){
	
		if ($tipo_hospedagem == "U"){
		
			$parametros = "$tipo_hospedagem,$username,$dominio";
		
		} else if ($tipo_hospedagem == "D"){
		
			$parametros = "$tipo_hospedagem,$username,$dominio_hospedagem";
		
		}
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "	sptb_spool ( ";
		$sSQL .= "		registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= ") VALUES ( ";
		$sSQL .= "		now(), '$server', 'H', 'a', '$id_conta', '$parametros', 'A' ";
		$sSQL .= ")";
		
		//echo "QUERY SPOOL :  $sSQL <br>\n";
		
		$this->bd->consulta($sSQL);
		
		
		
		
		return;
		
	}

	function hospedagemExcluiRede($server,$id_conta,$tipo_hospedagem,$username,$dominio_padrao,$dominio_hospedagem){
	// INCOMPLETO !! //////////////////////////////////////	
		if ($tipo_hospedagem == "U"){
				
			$parametros = "$tipo_hospedagem,$username,$dominio_padrao";// FALTA CNF ou CPL
				
		} else if ($tipo_hospedagem == "D"){
				
			$parametros = "$tipo_hospedagem,$username,$dominio_hospedagem";
				
		}

		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "	sptb_spool (";
		$sSQL .= "		registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= ") VALUES (";
		$sSQL .= "		now(), '$server','H', 'x', '$id_conta', '$parametros', 'A'";
		
	}
	
	function configuraDNS ($destino, $tipo, $id_conta, $dominio){
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "	sptb_spool (";
		$sSQL .= "		registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= ") VALUES (";
		$sSQL .= "		now(), '$destino', '$tipo', 'a', '$id_conta', '$dominio', 'A'";
		$sSQL .= ")";
		
		$this->bd->consulta($sSQL);
		
		
		return;
		
	
	}
	
	
	function adicionarEmail($server, $id_conta, $username, $dominio){
	
	
		$parametros = "". $username ."@". $dominio ."";
	
		$sSQL  = "INSERT INTO ";
		$sSQL .= "	sptb_spool (";
		$sSQL .= "		registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= ") VALUES (";
		$sSQL .= "		now(), '$server', 'E', 'a', '$id_conta', '$parametros', 'A' ";
		$sSQL .= ")";
		
		
		$this->bd->consulta($sSQL);

		return;	
		
	
	}
	
	function excluirEmail($server, $id_conta, $username, $dominio){
	
	
		$parametros = "". $username ."@". $dominio ."";
	
		$sSQL  = "INSERT INTO ";
		$sSQL .= "	sptb_spool (";
		$sSQL .= "		registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= ") VALUES (";
		$sSQL .= "		now(), '$server', 'E', 'x', '$id_conta', '$parametros', 'A' ";
		$sSQL .= ")";
		
		
		$this->bd->consulta($sSQL);

		return;	
		
	
	}
	
	function adicionaIpExterno($id_nas,$ip_externo,$ipaddr,$id_conta){
	
		$paramentros = $ip_externo.",".$ipaddr;
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= " sptb_spool ( ";
		$sSQL .= "	registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= " ) VALUES ( ";
		$sSQL .= "     now(), '$id_nas', 'D', 'a', '$id_conta', '$paramentros', 'A' ";
		$sSQL .= " )";
	
		$this->bd->consulta($sSQL);
		
		//echo "SPOOL: $sSQL <br>";
		
		return;
	
	
	}
	
	function excluiIpExterno($id_nas,$ip_externo,$id_conta){

		$paramentros = $ip_externo;
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= " sptb_spool ( ";
		$sSQL .= "	registro, destino, tipo, op, id_conta, parametros, status ";
		$sSQL .= " ) VALUES ( ";
		$sSQL .= "     now(), '$id_nas', 'D', 'x', '$id_conta', '$paramentros', 'A' ";
		$sSQL .= " )";
	
		$this->bd->consulta($sSQL);
		
		return;
	
		
	
	
	}
	

}
?>
