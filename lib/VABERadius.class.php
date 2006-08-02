<?

	/**
	 * VABERadius.class.php
	 *
	 * Sistema do Backend atuando como módulo externo do xtradius.
	 *
	 */

	require_once(PATH_LIB."/VirtexAdminBackend.class.php");

	class VABERadius extends VirtexAdminBackend {
	
		/**
		 * Constantes
		 */
		public static $log_ok		= "OK";
		public static $log_erro		= "E";
		public static $log_info 	= "I";
		public static $log_alerga 	= "A";
		
		
		/**
		 * Opções de execução
		 */
	
		protected $auth;		// Autenticação
		protected $acct;		// Accounting
		protected $tipo_conta;	// Tipo da conta que está tentando fazer autenticação
		
		/**
		 * Opções de inicialização
		 */
		protected $rc;
		protected $rcup;
		protected $rcdown;
		
		/**
		 * Parametros de autenticação
		 */
		protected $username;
		protected $password;
		protected $foneinfo;
		
		/**
		 * Parametros de accounting (registro)
		 */
		
		protected $entrada;
		protected $saida;
		protected $session;
		protected $bytes_in;
		protected $bytes_out;
		protected $nas;
		protected $ip_addr;
		protected $tempo;
		protected $terminate_cause;
		
		
		/**
		 * Construtor
		 */
		public function __construct() {
			parent::__construct();
			$this->initVars();
			
			// Configura o getopt e chama as opções para processamento posterior
			$this->_shortopts = "RUDACu:w:f:ESs:I:O:n:i:t:c:";
			$this->getopt();
		
		}
		
		
		/**
		 * Inicializa as propriedades do objeto;
		 */
		protected function initVars() {
			$this->auth 			= 0;
			$this->acct 			= 0;
			$this->tipo_conta		= "";
			
			$this->rc 				= 0;
			$this->rcup 			= 0;
			$this->rcdown 			= 0;
			
			$this->username 		= "";
			$this->password 		= "";
			$this->foneinfo 		= "";
			
			$this->entrada 			= 0;
			$this->saida 			= 0;
			$this->session 			= "";
			$this->bytes_in		 	= "";
			$this->bytes_out 		= "";
			$this->nas 				= "";
			$this->ip_addr 			= "";
			$this->tempo 			= "";
			$this->terminate_cause 	= "";
		
		}
		
		
		/**
		 * Executa o aplicativo
		 * TODO: syslog e/ou sistema de logs interno para suporte
		 */
		public function executa() {
		
			/**
			 * Varre as opções
			 *
			 *    Cada linha é um array contendo um par 0 => opcao, 1 => parametro
			 *
			 */

			for($i=0;$i<count($this->options);$i++) {
				switch($this->options[$i][0]) {
					/**
					 * Opções de execução
					 */
					case 'A':
						$this->auth = 1;
						break;
					case 'C':
						$this->acct = 1;
						break;
					
					/**
					 * RC start/stop
					 */
					case 'R':
						$this->rc = 1;
						break;
					case 'U':
						$this->rcup = 1;
						break;
					case 'D':
						$this->rcdown = 1;
						break;
					
					/**
					 * Parâmetros de Autenticação
					 */
					case 'u':
						$this->username = $this->options[$i][1];
						break;
					
					case 'w':
						$this->password = $this->options[$i][1];
						break;
					
					case 'f':
						$this->foneinfo = $this->options[$i][1];
						break;
					
					/**
					 * Parâmetros de accounting
					 */
					case 'E':
						$this->entrada = 1;
						break;

					case 'S':
						$this->saída = 1;
						break;
					
					case 's':
						$this->session = $this->options[$i][1];
						break;
					
					case 'I':
						$this->bytes_in = $this->options[$i][1];
						break;

					case 'O':
						$this->bytes_out = $this->options[$i][1];
						break;

					case 'n':
						$this->nas = $this->options[$i][1];
						break;
				
					case 'i':
						$this->ip_addr = $this->options[$i][1];
						break;

					case 't':
						$this->tempo = $this->options[$i][1];
						break;

					case 'c':
						$this->terminate_cause = $this->options[$i][1];
						break;
				}			
			}
			
			$mensagem = "";
			
			/**
			 * Rotina de inicialização
			 */
			
			if( $this->rc ) {

				if( !$this->rcup && !$this->rcdown) {
					// Restart
					$this->rcup		= 1;
					$this->rcdown 	= 1;
				}

				// Para o Radius				
				if( $this->rcdown ) {
					$this->rcstop();
				}
				
				// Inicia o Radius
				if( $this->rcup ) {
					$this->rcstart();
				}
			
				return(0);
			}
			


			/**
			 * Consistências de parâmetros
			 */			
			
			if( ($this->auth && $this->acct) || (!$this->auth && !$this->acct) ) {
				// A execução requer um (e somente um) parametro de tipo de operação (autenticação ou accounting)
				return(-1);
			}
			
			
			
			if( $this->auth ) {

				/*********************************************
				 *                                           *
				 * AUTENTICACAO                              *
				 *                                           *
				 *********************************************/


				if( !$this->username ) {
					$mensagem = "Username em branco";
					$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
					return(-1);
				}

				if( !$this->password ) {
					$mensagem = "Senha em branco";
					$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
					return(-1);
				}

				if( !$this->foneinfo ) {
					$mensagem = "Origem (caller-id) em branco";
					$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
					return(-1);
				}
				
				/**
				 * Identificação do tipo de acesso
				 */
				
				$mac_pattern = "^([0-9a-fA-F]{1,2}:){5}[0-9a-fA-F]{1,2}$";
				$this->tipo_conta = ereg($mac_pattern,$this->foneinfo) ? "BL" : "D";
				
				$dominio_padrao = $this->prefs->obtem("geral","dominio_padrao");
				
				// Obter usuario
				$sSQL  = "SELECT ";
				$sSQL .= "       cnt.username as usuario,cnt.dominio,cnt.tipo_conta,cnt.senha_cript,cnt.id_cliente_produto,cbl.username,cd.username, ";
				$sSQL .= "       trim(cnt.status) as st_conta, trim(ctt.status) as st_contrato, ctt.id_cliente_produto as id_contrato, cbl.mac, ";
				$sSQL .= "   cd.foneinfo, cnt.id_conta, cbl.ipaddr ";
				$sSQL .= "FROM ";
				$sSQL .= "   cntb_conta_bandalarga cbl ";
				$sSQL .= "   RIGHT OUTER JOIN cntb_conta cnt USING(username,dominio,tipo_conta) ";
				$sSQL .= "   LEFT OUTER JOIN cntb_conta_discado cd USING(username,dominio,tipo_conta), ";
				$sSQL .= "   cbtb_contrato ctt INNER JOIN cbtb_cliente_produto cp USING(id_cliente_produto), ";
				$sSQL .= "   prtb_produto_discado pc RIGHT OUTER JOIN prtb_produto p USING(id_produto) ";
				$sSQL .= "   LEFT OUTER JOIN prtb_produto_bandalarga pbl USING(id_produto) ";
				$sSQL .= "WHERE ";
				$sSQL .= "       ctt.id_cliente_produto = cnt.id_cliente_produto ";
				$sSQL .= "       AND ctt.id_cliente_produto = cnt.id_cliente_produto ";
				$sSQL .= "       AND p.id_produto = cp.id_produto ";
				$sSQL .= "       AND cnt.tipo_conta = '" . $this->tipo_conta . "' ";
				$sSQL .= "       AND cnt.username = '".$this->username."' ";
				$sSQL .= "       AND cnt.dominio = '".$dominio_padrao."' ";
				
				$user = $this->bd->obtemUnicoRegistro($sSQL);

				/**
				 * Usuário inexistente
				 */
				if( !@$user["usuario"] ) {
					$mensagem = "Usuário inexistente";
					$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
					return(-1);
				}
				
				/**
				 * Verificacao de status
				 */
				if( $user["st_conta"] != "A" ) {
					$mensagem = "Conta " . ($user["st_conta"]?"suspensa (pagamento)":"bloqueada");
					$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
					return(-1);
				}
				
				/**
				 * Verificacao de status
				 */
				if( $user["st_contrato"] != "A" ) {
					$mensagem = "Contrato " . ($user["st_conta"]?"suspenso (pagamento)":"cancelado");
					$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
					return(-1);
				}

				/**
				 * Verificacao de senha
				 */
				
				// Criptografa a senha recebida com o salt extraído da senha do BD
				$salt = substr($user["senha_cript"],0,12);
				$sc = crypt( $this->password, $salt);
				
				if( $sc != $user["senha_cript"] ) {
					$mensagem = "Senha inválida";
					$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
					return(-1);				
				}
				
				/**
				 * Validações específicas
				 */
				
				if( $this->tipo_conta == "BL" ) {
					// BANDA LARGA
					
					$this->foneinfo = $this->mac($this->foneinfo);
					
					/**
					 * Validação/Registro de MAC.
					 */
					if( @$user["mac"] ) {
						// MAC CADASTRADO, COMPARA

						$user_mac = $this->mac($user["mac"]);

						if ($this->foneinfo != $user_mac){	
							
							$mensagem = "MAC não confere";
							$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
							return(-1);
							
						}
					} else {
						// MAC NÃO CADASTRADO. CADASTRA!!!
						
						$mensagem = "Registrando MAC";
						$this->log(VABERadius::$log_info,$this->username,$mensagem,$this->foneinfo,$this->nas);
						
						$sSQL  = "UPDATE ";
						$sSQL .= "   cntb_conta_bandalarga ";
						$sSQL .= "SET ";
						$sSQL .= "   mac = '" . $this->foneinfo . "' ";
						$sSQL .= "WHERE ";
						$sSQL .= "   username ilike '" . $this->username . "' ";
						$sSQL .= "   AND dominio = '" . $dominio_padrao . "' ";
						$sSQL .= "   AND tipo_conta = 'BL' ";
						
						$this->bd->consulta($sSQL);
					}

					/**
					 * TODO: Verificação de NAS.
					 */
					
					
					
					/**
					 * Se chegou até aqui envia o Framed-IP-Address para o cara
					 */
					echo "Framed-IP-Address = " . $user["ipaddr"] . "\n";
					echo "Framed-Compression = Van-Jacobson-TCP-IP\n";
					echo "Framed-Protocol = PPP\n";
					echo "Service-Type = Framed-User\n";
					 
				} else {
					// DISCADO
					
					/**
					 * Validação do número de telefone
					 */
					
					if( !@$user["foneinfo"] ) {
						// Logar um warning
					} else {
						// Verificar o foneinfo
						if( !ereg($user["foneinfo"],$this->foneinfo) ) {
							$mensagem = "Telefone não confere";
							$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
							return(-1);	
						}
					}

				}
			
			} else {
			
				/*********************************************
				 *                                           *
				 * ACCOUNTING                                *
				 *                                           *
				 *********************************************/
				
				if( (!$this->entrada && !$this->saída) || ($this->entrada && $this->saida) ) {
					// Só pode haver um... já dizia highlander.
					return(-1);
				}
				
				if( !$this->session ) {
					$mensagem = "Erro processando acct " . ($this->entrada?"START":"STOP") . ": Não recebeu session_id";
					$this->log(VABERadius::$log_erro,$this->username,$mensagem,$this->foneinfo,$this->nas);
					return(-1);
				}
				
				if( $this->entrada ) {
					// REGISTRA ENTRADA
					$this->acctStart($this->session,$this->username,$this->foneinfo,$this->nas,$this->ip_addr);
					
				} else {
					// REGISTRA SAIDA
					$this->acctStop($this->session,$this->tempo,$this->terminate_cause,$this->bytes_in,$this->bytes_out);
				
				}
			
			}
			
			return(0);	// Execução OK

		}
		
		
		/**
		 * Sistema de Accounting
		 */
		protected function acctStart($session,$username,$caller_id,$nas,$framed_ip_address) {
			$sSQL  = "INSERT INTO ";
			$sSQL .= "   rdtb_accounting( ";
			$sSQL .= "      session_id,username,login,caller_id,nas,framed_ip_address ";
			$sSQL .= "   ) VALUES ( ";
			$sSQL .= "      '".$session."', ";
			$sSQL .= "      '".$username."', ";
			$sSQL .= "      now(), ";
			$sSQL .= "      '".$caller_id."', ";
			$sSQL .= "      '".$nas."', ";
			$sSQL .= "      '".$framed_ip_address."' ";
			$sSQL .= "   ) ";
			$this->bd->consulta($sSQL);
		}
		
		protected function acctStop($session,$tempo,$terminate_cause,$bytes_in,$bytes_out) {
			$sSQL  = "UPDATE ";
			$sSQL .= "   rdtb_accounting ";
			$sSQL .= "SET ";
			$sSQL .= "   logout=now(), ";
			$sSQL .= "   tempo='".$tempo."', ";
			$sSQL .= "   terminate_cause='".$terminate_cause."', ";
			$sSQL .= "   bytes_in='".$bytes_in."', ";
			$sSQL .= "   bytes_out='".$bytes_out."' ";
			$sSQL .= "WHERE   ";
			$sSQL .= "   session_id = '".$session."'";
			$this->bd->consulta($sSQL);
		}

		/**
		 * Sistema de log.
		 * TODO: adicionar syslog
		 */
		protected function log($tipo,$username,$mensagem,$caller_id,$nas) {

			// RDTB_LOG (Log geral)
			$sSQL  = "INSERT INTO ";
			$sSQL .= "   rdtb_log(";
			$sSQL .= "      tipo,username,mensagem,caller_id ";
			$sSQL .= "   ) VALUES (";
			$sSQL .= "      '".$tipo."', ";
			$sSQL .= "      '".$username."', ";
			$sSQL .= "      '".$mensagem."', ";
			$sSQL .= "      '".$caller_id."' ";
			$sSQL .= "   ) ";

			$this->bd->consulta($sSQL);
			
			// RDTB_ACCOUNTING (Log de Accounting)
			$sSQL  = "INSERT INTO ";
			$sSQL .= "   rdtb_accounting( ";
			$sSQL .= "      session_id,username,logout,tempo,caller_id,nas,terminate_cause ";
			$sSQL .= "   ) VALUES ( ";
			$sSQL .= "      '".$tipo.":'||nextval('rdsq_id_accounting'), ";
			$sSQL .= "      '".$username."', ";
			$sSQL .= "      now(), ";
			$sSQL .= "      0, ";
			$sSQL .= "      '".$caller_id."', ";
			$sSQL .= "      '".$nas."', ";
			$sSQL .= "      '".$mensagem."' ";
			$sSQL .= "   ) ";

			$this->bd->consulta($sSQL);

		}


		/**
		 * Padronização do MAC (para fins de comparação)
		 */
		
		protected function mac($mac){
		
			$mac = strtoupper($mac);
			$el = explode(":",$mac);
			for($i=0;$i<count($el);$i++) {
				if( strlen($el[$i]) < 2 ) {
					$el[$i] = "0".$el[$i];
				}
			}
			$mac = implode(":",$el);

			return($mac);


		}


		/**
		 * RC Scripts
		 */	
		protected function rcstart() {
			$comando = "/mosman/virtex/radius/sbin/radiusd -y 2>&1";
			SistemaOperacional::executa($comando);
		}

		protected function rcstop() {
			$comando = "/usr/bin/killall -9 radiusd";
			SistemaOperacional::executa($comando);
		}

	}


?>
