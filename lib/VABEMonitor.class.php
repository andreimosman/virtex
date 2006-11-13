<?

	/**
	 * VABEMonitor.class.php
	 *
	 * Sistema do Backend para monitoramento de Infra-estrutura.
	 *
	 */

	require_once(PATH_LIB."/VirtexAdminBackend.class.php");
	require_once("SOFreeBSD.class.php");


	class VABEMonitor extends VirtexAdminBackend {
		protected $processados;
		protected $hosts;
		protected $ich;
		protected $icc_cache;
		
		// Preferencias
		protected $prefs;
		 
		
		/**
		 * Construtor
		 */
		public function __construct() {
			parent::__construct();
			$this->initVars();
			
			//$this->licencaBL = ((int)$this->licenca("backend","banda_larga"));
			//$this->licencaD  = ((int)$this->licenca("backend","discado"));
			
			// Configura o getopt e chama as opções para processamento posterior
			$this->_shortopts = "RUDACu:w:f:ESs:I:O:n:i:t:c:";
			$this->getopt();
		
		}
		
		/**
		 * Inicializa as propriedades do objeto;
		 */
		protected function initVars() {
			$this->processados=array();
			$this->ich = new ICHostInfo();
			$this->hosts=$this->ich->obtemListaServidores();
			$this->icc_cache=array();
			
			$sSQL  = "SELECT ";
			$sSQL .= "   emails,num_pings ";
			$sSQL .= "FROM ";
			$sSQL .= "   pftb_preferencia_monitoracao ";
			$sSQL .= "WHERE ";
			$sSQL .= "   id_provedor=1";
			$this->prefs = $this->bd->obtemUnicoRegistro($sSQL);
			
		}

		public function executa() {
			$this->testeRecursivo();
			$this->limpaCache();
		}
		
		
		/**
		 * Testa recursivamente os POPs
		 */
		public function testeRecursivo($id_pop="",$ip="",$tipo="",$ativar_monitoramento="") {
			//echo "Verificando: $id_pop\n";
			if( !$id_pop ) {
				$this->processados=array();
			} else {
				// TODO: Verificar se a recursividade não está em loop, usando o array processados.
				if( in_array($id_pop,$this->processados) ) {
					// TODO: Armazenar erro.
					
					// retorna
					return;
				} else {
					// 
					$this->processados[] = $id_pop;
				}
				
				$sSQL = "SELECT infoserver FROM cftb_pop WHERE id_pop='".$this->bd->escape($id_pop)."'";
				$pop = $this->bd->obtemUnicoRegistro($sSQL);
				
				if( $ativar_monitoramento && $ip ) {
					$num_pings =  !@$this->prefs["num_pings"] ? 2 : @$this->prefs["num_pings"];

					$r = $this->testePing($ip,$num_pings,"",@$pop["infoserver"]);

					$perdas=0;
					$minimo=999999999999;
					$maximo=0;
					// Computar
					$soma=0;
					for($i=0;$i<count($r);$i++) {
						//echo "PING: $ip: ".$r[$i]."\n";

						if(trim($r[$i])=="-") {
						   $perdas++;
						   $soma += 0;
						} else {
							$r[$i]=$r[$i]*1000;
							$soma += $r[$i];
							if( $r[$i] < $minimo ) {
								$minimo = $r[$i];
							}
							if( $r[$i] > $maximo ) {
								$maximo = $r[$i];
							}
						}
					}
					
					$sSQL  = "SELECT ";
					$sSQL .= " id_pop,min_ping,max_ping,media_ping,num_ping,num_perdas,status,num_erros ";
					$sSQL .= "FROM ";
					$sSQL .= "   sttb_pop_status ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_pop = '".$this->bd->escape($id_pop)."' ";
					
					$regs = $this->bd->obtemRegistros($sSQL);
					$num_erros=0;
					$status='OK';
					$media = (int)(count($r)?$soma/count($r):0);

					if($perdas == count($r)){
						$status = !count($r)?'IER':'ERR';
						$minimo=0;
						$maximo=0;
						$media=0;
					}
					if($perdas > 0 && $perdas < count($r)) $status = 'WRN';
					
					if( $status != 'OK' ) {
						$num_erros=1;
					}

					if( !count($regs) ) {
						// Insere no banco de dados.
						//echo "INSERINDO NO BD\n";
						$sSQL  = "INSERT INTO ";
						$sSQL .= "   sttb_pop_status (";
						$sSQL .= "      id_pop,min_ping,max_ping,media_ping,num_ping,num_perdas,status,num_erros, laststats ";
						$sSQL .= " ) VALUES ( ";
						$sSQL .= "   '".$this->bd->escape($id_pop)."', ";
						$sSQL .= "   '".$this->bd->escape($minimo)."', ";
						$sSQL .= "   '".$this->bd->escape($maximo)."', ";
						$sSQL .= "   '".$this->bd->escape($media)."', ";
						$sSQL .= "   '".$this->bd->escape(((int)count($r)))."', ";
						$sSQL .= "   '".$this->bd->escape($perdas)."', ";
						$sSQL .= "   '".$this->bd->escape($status)."', ";
						$sSQL .= "   '$num_erros', now() ";
						$sSQL .= ") ";
						//$sSQL = str_replace(".",",",$sSQL);
						$this->bd->consulta($sSQL);
						//echo "\n";
						//echo $sSQL."\n";
						
					} else {
						//echo "ALTERANDO DO BD\n";
						$reg = $regs[0];
						// Se o último status era ok
						if( $reg["status"] == 'OK' ) {
							if( $status != 'OK' ) {
								// Colocar na condição de alerta seja lá como isso for.
							}
							
						} else {
							if( $status == 'OK' ) {
								// Enviar mensagem de POP subiu.
							} else {
								$num_erros = $reg["num_erros"] + 1;
							}
						}
					
						// Atualiza o banco de dados.
						$sSQL  = "UPDATE ";
						$sSQL .= "   sttb_pop_status ";
						$sSQL .= "SET ";
						$sSQL .= "   min_ping='".$this->bd->escape($minimo)."', ";
						$sSQL .= "   max_ping='".$this->bd->escape($maximo)."', ";
						$sSQL .= "   media_ping='".$this->bd->escape($media)."', ";
						$sSQL .= "   num_ping='".$this->bd->escape(count($r))."', ";
						$sSQL .= "   num_perdas='".$this->bd->escape($perdas)."', ";
						$sSQL .= "   status='".$this->bd->escape($status)."', ";
						$sSQL .= "   num_erros='$num_erros', ";
						$sSQL .= "   laststats=now() ";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_pop = '" . $this->bd->escape($id_pop) . "' ";
						$this->bd->consulta($sSQL);


					}

				}
				
				// Fazer a média
				
				// Verificar ação.
				
			}
			
			$sSQL = "SELECT id_pop, nome, tipo, ipaddr, CASE WHEN ativar_monitoramento is true THEN 1 ELSE 0 END as ativar_monitoramento FROM cftb_pop WHERE ";
			if( $id_pop ) {
			   $sSQL .= " id_pop_ap = '".$this->bd->escape($id_pop)."' ";
			} else {
			   $sSQL .= " id_pop_ap is null ";
			}
			
			$pops = $this->bd->obtemRegistros($sSQL);
			
			for($i=0;$i<count($pops);$i++) {
				$this->testeRecursivo($pops[$i]["id_pop"],$pops[$i]["ipaddr"],$pops[$i]["tipo"],$pops[$i]["ativar_monitoramento"]);
			}

		}
		
		public function testePing($ip,$num_pacotes=2,$tamanho="",$icc_host="") {
			$r = array();
			if( $icc_host ) {
				$icc = $this->getICC($icc_host);

				if($icc) {
					// CONECTADO
					$r = $icc->getFPING($ip,$num_pacotes,$tamanho);
				}

			}
			
			// Retorna as respostas do ping
			return($r);
			
		}
		
		
		/**
		 * Obtem o objeto ICC conectado
		 */
		protected function getICC($icc_host) {
			// Verifica o cache
			if( ! @$this->icc_cache[$icc_host] ) {
				$info = $this->ich->obtemInfoServidor($icc_host);
				$this->icc_cache[$icc_host] = new ICClient();

				if( !@$this->icc_cache[$icc_host]->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"]) ) {
					$this->icc_cache[$icc_host] = null;
				}
			}
			
			// Verifica se o objeto está conectado.
			if( @$this->icc_cache[$icc_host] ) {
				
				if($this->icc_cache[$icc_host]->estaConectado()) {
					return($this->icc_cache[$icc_host]);
				}
				$this->icc_cache[$icc_host]->close();
				$this->icc_cache[$icc_host] = null;
			}
			return null;
			
		}		


		/**
		 * Fecha as conexões ao ICCServer
		 */
		public function limpaCache() {
			while( list($icc_host,$icc) = each($this->icc_cache) ) {
				if( $icc ) {
					$icc->close();
					$this->icc_cache[$icc_host] = null;
				}
			}
		}
	}

?>
