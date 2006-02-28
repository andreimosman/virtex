<?

require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VASuporte extends VirtexAdmin {

	public function VASuporte() {
		parent::VirtexAdmin();
	}

	// metodo para pegar as propriedadas enviadas via menu na interface.
	public function processa($op=null) {	
		if($op == "graf"){	
			$this->arquivoTemplate = "suporte_grafico.html";
		} else if ($op == "log"){
			$this->arquivoTemplate = "suporte_logradius.html";
		} else if ($op == "monit"){
			$this->arquivoTemplate = "suporte_monitoramento.html";
		} else if ($op == "calc"){
			$this->arquivoTemplate = "suporte_calculadoraip.html";

			$netmask = @$_REQUEST["netmask"];
			$ip = @$_REQUEST["ip"];

			if( !$netmask ) $netmask = 24;

			if( $ip ) {

				if( strlen($netmask) <= 2 ) {
					$oip = new RedeIP($ip."/".$netmask);
				} else {
					$oip = new RedeIP($ip,$netmask);
				}

				$ipaddr = array(
								"endereco" => bin2addr($oip->addr),
								"mascara"  => bin2addr($oip->mask),
								"wildcard" => bin2addr($oip->wildcard),
								"nw"       => bin2addr($oip->network),
								"bc"       => bin2addr($oip->broadcast),
								"maxhost"  => $oip->maxHost(),
								"minhost"  => $oip->minHost(),
								"numhosts" => $oip->numHosts()
								);

				$this->tpl->atribui("ip",$ip);
				$this->tpl->atribui("netmask",$netmask);
				$this->tpl->atribui("ipaddr",$ipaddr);

			}



		} else if ($op == "arp"){
		$this->arquivoTemplate = "suporte_arp.html";
		}
	}

	public function __destruct() {
		parent::__destruct();
	}

}



?>
