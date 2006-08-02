<?
require_once(PATH_LIB . "/VirtexAdmin.class.php" );


class HospedagemSpool extends VirtexAdmin {



	public function HospedagemSpool() {
		parent::__construct();

	}

	public function processa($op=null){
	
			$sSQL  = "SELECT h.username,h.tipo_conta,h.dominio,h.tipo_hospedagem,n.id_conta,h.dominio_hospedagem ";
			$sSQL .= "FROM cntb_conta_hospedagem h, cntb_conta n ";
			$sSQL .= "WHERE ";
			$sSQL .= "h.username = n.username AND ";
			$sSQL .= "h.dominio = n.dominio AND ";
			$sSQL .= "h.tipo_conta = n.tipo_conta ";
			
			$hospedagens = $this->bd->obtemRegistros($sSQL);
			echo "SQL: $sSQL <br>";
			
			
			$server = $this->prefs->obtem("geral","hosp_server");
			
			for($i=0;$i<count($hospedagens);$i++){
			
				$id_conta = $hospedagens[$i]["id_conta"];
				$tipo_hospedagem = $hospedagens[$i]["tipo_hospedagem"];
				$dominio = $hospedagens[$i]["dominio"];
				$dominio_hospedagem = @str_replace("/","",$hospedagens[$i]["dominio_hospedagem"]);
				$username = $hospedagens[$i]["username"];
			
				if ($hospedagens[$i]["tipo_hospedagem"] == "D"){
				
					$ns1 = $this->prefs->obtem("geral","hosp_ns1");
					$ns2 = $this->prefs->obtem("geral","hosp_ns2");

					$this->spool->configuraDNS($ns1, "N1", $id_conta, $dominio_hospedagem);
					$this->spool->configuraDNS($ns2, "N2", $id_conta, $dominio_hospedagem);

				
				
				
				}
				
				
				

				
				$this->spool->hospedagemAdicionaRede($server,$id_conta,$tipo_hospedagem,$username,$dominio,$dominio_hospedagem);
			
			}
			
			
			echo "PAPOCOU A BAGAÇA!!! <BR>";
			return;
			
			
			
			
			
			
	
	}


	
}	


	
?>	