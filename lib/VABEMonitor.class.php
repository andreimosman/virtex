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
		protected $icc;
		
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
			$this->icc = new ICClient();
			$this->hosts=$this->ich->obtemListaServidores();

		}

		public function executa() {
		
			//for($i=0;$i<count($this->hosts);$i++) {
			//	echo $this->hosts[$i]."\n";
			//}
		
		
			$this->testeRecursivo();
		}
		
		
		/**
		 * Testa recursivamente os POPs
		 */
		public function testeRecursivo($id_pop="",$ip="",$tipo="") {
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
				
				if( $ip ) {
				
					$r = $this->testePing($ip,2,"",@$pop["infoserver"]);

					$perdas=0;
					$minimo=999999999999;
					$maximo=0;
					// Computar
					for($i=0;$i<count($r);$i++) {
						//echo "PING: $ip: ".$r[$i]."\n";

						if(trim($r[$i])=="-") {
						   $perdas++;
						} else {
							$r[$i]=$r[$i]*1000;
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
					$media = ($maximo + $minimo)/count($r);

					if($perdas == count($r)){
						$status = 'ERR';
						$minimo=0;
						$maximo=0;
						$media=0;
					}
					if($perdas > 0 && $perdas < count($r)) $status = 'WRN';
					
					if( $status != 'OK' ) {
						$num_erros=1;
					}
					
					//$status='ERR';
					$minimo=$minimo;
					$maximo=$maximo;
					$media=$media;
					

					if( !count($regs) ) {
						// Insere no banco de dados.
						//echo "INSERINDO NO BD\n";
						$sSQL  = "INSERT INTO ";
						$sSQL .= "   sttb_pop_status (";
						$sSQL .= "      id_pop,min_ping,max_ping,media_ping,num_ping,num_perdas,status,num_erros ";
						$sSQL .= " ) VALUES ( ";
						$sSQL .= "   '".$this->bd->escape($id_pop)."', ";
						$sSQL .= "   '".$this->bd->escape($minimo)."', ";
						$sSQL .= "   '".$this->bd->escape($maximo)."', ";
						$sSQL .= "   '".$this->bd->escape($media)."', ";
						$sSQL .= "   '".$this->bd->escape(((int)count($r)))."', ";
						$sSQL .= "   '".$this->bd->escape($perdas)."', ";
						$sSQL .= "   '".$this->bd->escape($status)."', ";
						$sSQL .= "   '$num_erros' ";
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
						$sSQL .= "   num_erros='$num_erros' ";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_pop = '" . $this->bd->escape($id_pop) . "' ";
						$this->bd->consulta($sSQL);
						//echo "\n";
						//echo "$sSQL;\n";


					}
					/**

					echo "IP: $ip\n";
					echo "   p env.: ".count($r)."\n";
					echo "   perdas: $perdas\n";
					if( $perdas != count($r) ) {
						echo "   minimo: $minimo\n";
						echo "   maximo: $maximo\n";
					}
					echo "-----------------------\n";
					
					*/
				}
				
				// Fazer a média
				
				// Verificar ação.
				
			}
			
			$sSQL = "SELECT id_pop, nome, tipo, ipaddr FROM cftb_pop WHERE ";
			if( $id_pop ) {
			   $sSQL .= " id_pop_ap = '".$this->bd->escape($id_pop)."' ";
			} else {
			   $sSQL .= " id_pop_ap is null ";
			}
			
			$pops = $this->bd->obtemRegistros($sSQL);
			
			for($i=0;$i<count($pops);$i++) {
				$this->testeRecursivo($pops[$i]["id_pop"],$pops[$i]["ipaddr"],$pops[$i]["tipo"]);
			}

		}
		
		/**
		 * TODO: Definir retorno.
		 */
		public function testePing($ip,$num_pacotes=2,$tamanho="",$icc_host="") {
			// 
			//echo "pingando $ip...\n";
			$r = array();
			
			
			//echo "ICCHOST: $icc_host";
			
			
			/**

			$result = exec("/usr/local/sbin/fping -C $num_pacotes -q $ip 2>&1");
			list($host,$info) = explode(":",$result,2);
			$host=trim($host);
			$info=trim($info);
			
			$r = explode(" ",$info);
			
			*/
			if( $icc_host ) {
				//$r=SOFreeBSD::fping($ip,$num_pacotes,$tamanho);
				$info = $this->ich->obtemInfoServidor($icc_host);

				if($this->icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {
					// Conseguiu conectar o servidor
					$r = $this->icc->getFPING($ip,$num_pacotes,$tamanho);
				}
			}

			// Retorna as respostas do ping
			return($r);
			
			//echo "R: $result";
			
		}

	}

?>
