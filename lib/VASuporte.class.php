<?

require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VASuporte extends VirtexAdmin {

	public function VASuporte() {
		parent::VirtexAdmin();
	}

	// metodo para pegar as propriedadas enviadas via menu na interface.
/*	public function obtemRede(){
	
	}*/
	
	
	
	public function processa($op=null) {	
		if( ! $this->privPodeLer("_SUPORTE") ) {
					$this->privMSG();
					return;
		}		
	
	
		if($op == "graf"){	
		
			$pesquisa = @$_REQUEST["pesquisa"];
			$rotina = @$_REQUEST["rotina"];
		
		
			$sSQL  = "SELECT ";
			$sSQL .= "id_pop, nome ";
			$sSQL .= "FROM ";
			$sSQL .= "cftb_pop";
			
			$pops = $this->bd->obtemRegistros($sSQL);
			
			
			if ($pesquisa){
			
				$sSQL  = "SELECT ";
				$sSQL .= "username, id_pop, mac ";
				$sSQL .= "FROM ";
				$sSQL .= "cntb_conta_bandalarga ";
				$sSQL .= "WHERE id_pop = '".$_REQUEST["pesquisa"]."'";
				
				$cli = $this->bd->obtemRegistros($sSQL);
				$this->tpl->atribui("cli",$cli);
				//echo $sSQL;
			
			}
			
			
			$this->tpl->atribui("pops",$pops);
			$this->tpl->atribui("pesquisa",$pesquisa);
			
			if($rotina == "mostra"){
			
			$this->arquivoTemplate = "popup_grafico.html";
			return;
			
			}
		
		
			//$this->arquivoTemplate = "cobranca_versaolight.html";
			$this->arquivoTemplate = "suporte_grafico.html";
		} else if ($op == "log"){
		
			//$this->arquivoTemplate = "cobranca_versaolight.html";
			$this->arquivoTemplate = "suporte_radiuslog.html";
			
			$limite = @$_REQUEST["limite"];
			$username = @$_REQUEST["username"];
			$op = @$_REQUEST["op"];
			
			if(!$limite) $limite = 50;
			

			$sSQL  = "SELECT ";
			$sSQL .= "username as usuario, ";
			$sSQL .= "to_char(login,'DD/MM/YYYY HH24:MI:SS') as inicio, ";
			$sSQL .= "to_char(logout,'DD/MM/YYYY HH24:MI:SS') as fim, "; 
			$sSQL .= "tempo, caller_id as origem, session_id, ";
			$sSQL .= "terminate_cause as mensagem, bytes_in, bytes_out ";
			$sSQL .= "FROM ";
			$sSQL .= "	rdtb_accounting ";
			if($username) {
				$sSQL .= "WHERE ";
				$sSQL .= "	username LIKE '$username' ";
			}
			$sSQL .= "ORDER BY CASE WHEN logout is NULL then login ELSE logout END DESC ";
			$sSQL .= "LIMIT $limite ";
			
			
			$relat = $this->bd->obtemRegistros($sSQL);
			
			for ($i=0; $i<count($relat); $i++) {
				
				@list($tipo, $lixo) = explode(":",$relat[$i]["session_id"]);
				/*
				if ($tipo == "E") $relat[$i]["tipo_ng"] = "#E08E8E";
				else if ($tipo == "A") $relat[$i]["tipo_ng"] = "#E0BB8E";
				else if ($tipo == "I") $relat[$i]["tipo_ng"] = "#8E94E0";
				else $relat[$i]["tipo_ng"] = "#8EE098";
				*/
				
				if ($tipo == "E") $relat[$i]["tipo_ng"] = "#F2C6C6";
				else if ($tipo == "A") $relat[$i]["tipo_ng"] = "#F2DEC6";
				else if ($tipo == "I") $relat[$i]["tipo_ng"] = "#D8E8F3";
				else $relat[$i]["tipo_ng"] = "#D1F2C6";
				
			}
			
						
			$this->tpl->atribui("op",$op);
			$this->tpl->atribui("relat", $relat);
			$this->tpl->atribui("username", $username);
			$this->tpl->atribui("limite", $limite);
			
			//echo $sSQL;
			
		} else if ($op == "monit"){
			$this->arquivoTemplate = "cobranca_versaolight.html";
			//$this->arquivoTemplate = "suporte_monitoramento.html";
		} else if ($op == "calc"){
			$this->arquivoTemplate = "suporte_calculadoraip.html";

			$netmask = @$_REQUEST["netmask"];
			// adicionado pelo grande Hugo.. retira a / caso tenham colocado no campo mascara
			$netmask = substr(strrchr($netmask, "/"), 1);
			
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
			
			$ip = @$_REQUEST["ip"];
			
			$arp=array();
			
			if( $ip ) {
				// Recebeu IP faz a consulta do ARP
				if( $ip == "-a" ) {
					$cmd = "arp -an";
					$fd = popen($cmd,"r");
					
				} else {
					$cmd = "/sbin/ping -c 1 '" . $ip . "' 2>&1 > /dev/null";
					system($cmd);
					
					$cmd = "/usr/sbin/arp -an |grep '(" . $ip . ")' 2>&1 "; 
					$fd = popen($cmd,"r");
					
				}
				
				if( $fd ) {
					while(!feof($fd)) {
					
					   $linha = fgets($fd,4096);
					   //echo $linha;
					   chop($linha);
					   
						if( $linha ) {
							// SPLIT
							@list($shit,$addr,$at,$mac,$on,$on,$iface) = preg_split('/[\s]+/',$linha);

							//echo "shit: $shit<br>\n";
							//echo "addr: $addr<br>\n";
							//echo "mac: $mac<br>\n";
							//echo "iface: $iface<br>\n";
							
							if( strstr($mac,"incomplete")) {
								$mac = "ARP Não Enviado";
								$iface = "N/A";
							}
							
							$arp[] = array("addr" => $addr, "mac" => $mac , "iface" => $iface);
							
						}
					   
					   
					   
					}

					pclose($fd);
				}
				
			}
			
			$this->tpl->atribui("ip",$ip);
			$this->tpl->atribui("arp",$arp);
			$this->tpl->atribui("op",$op);
			
			
		}else if ($op == "help_desk"){
			$this->arquivoTemplate = "cobranca_versaolight.html";
		
		}else if ($op == "ping") {
		
			$this->arquivoTemplate = "suporte_ping.html";
			
			$ping_limite = 20;
			$ping_max_pkg = 1400;
			
			
			$erros = array();
			
			
			$tamanho = @$_REQUEST["tamanho"];
			$ip = @$_REQUEST["ip"];
			$pacotes = @$_REQUEST["pacotes"];
			$acao = @$_REQUEST["acao"];
			$extra = @$_REQUEST["extra"];
			$op = @$_REQUEST["op"];
			
			
			if(!$tamanho || $tamanho < 1) $tamanho = 32;
			if(!$pacotes || $pacotes < 1) $pacotes = 4;


			if($acao == "ping") {
						
				if($pacotes > $ping_limite) $erros[] = "O limite máximo de pacotes enviados por esta operação é de $ping_limite pacotes";
				if($tamanho > $ping_max_pkg) $erros[] = "O tamanho máximo dos pacotes desta operação é de $ping_max_pkg bytes";
				if(!$ip || trim($ip) == "") $erros[] = "Não foi especificado um IP para esta operação";
				
				$this->tpl->atribui("erros", $erros);
			}
			
			
			$this->tpl->atribui("tamanho", $tamanho);
			$this->tpl->atribui("pacotes", $pacotes);
			$this->tpl->atribui("ip", $ip);
			$this->tpl->atribui("op", $op);
			$this->tpl->atribui("acao", $acao);
			//$this->tpl->atribui("ping_list", $ping_list);
			
			
			if ($extra == "ping") {
				header("pragma: no-cache");
				header("connection: keep-state");
				echo "<font face='courier' size=-2>\n";
				if (!count($erros)) {			
					//$pinglist = `ping $ip -c $pacotes -s $tamanho`;
					$fd = popen("/bin/ping  -n -c " . escapeshellarg($pacotes) . " -s " . escapeshellarg($tamanho) . " " . escapeshellarg($ip),"r");
					
					while(!feof($fd)) {
						for($x=1;$x<250;$x++) {
							echo "<!-- BUFFER -->\n";
							flush();
						}

						$linha = fgets($fd);
						echo nl2br($linha);
						
						flush();
						
						//usleep(1);
					}
					
					fclose($fd);
					
					
				} else {
					for($i=0; $i<count($erros); $i++) echo "$erros[$i]<br>";
				}
				echo "</font>";
				$this->arquivoTemplate = "";
			} 
				
		}else if($op == "backup") {
			if( ! $this->privPodeGravar("_SUPORTE_BACKUP") ) {
				$this->privMSG();
				return;
			}		
						
			$this->arquivoTemplate="suporte_backup.html";
			
			$opcoes = @$_REQUEST["opcoes"];
			$acao = @$_REQUEST["acao"];
			$op = @$_REQUEST["op"];
			
			if ($acao == "backup") {
			
				//Diretórios para a criação do backup
				$bkp_dir = "/tmp/vitexbkp/";
				$bkp_dir_bd = $bkp_dir . "bd/";
				$bkp_dir_so = $bkp_dir . "so/";
				$bkp_dir_vtx = $bkp_dir . "vtx/";
				$bkp_dir_utl = $bkp_dir . "utl/";

				//
				$arquivos_bkp = "";
				
				//Verifica se já existe um diretório de bkp previamente criado
				if(is_dir($bkp_dir)) {
					$info = `rmdir -rf $bkp_dir`;
				}
				
				//Configuração dos arquivos a serem usados no backup
				$arquivos_bkp_so = array(
										"/etc/rc.conf",
										"/etc/rc.firewall"
									);

				$arquivos_bkp_vtx = array(
									);									
				$arquivos_bkp_utl = array(
									);
				
			}
		}
	}

	public function __destruct() {
		parent::__destruct();
	}

}



?>
