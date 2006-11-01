<?


	/**
	 * VA InfoCenter
	 *
	 * Implementa��o Servidor
	 */
	require_once(PATH_LIB."/InfoCenter.class.php");
	require_once("SOFreeBSD.class.php");

	class ICServer extends InfoCenter {

		protected $host;
		protected $port;
		
		protected $userbase;
		
		/**
		 * Instancia o servidor
		 */
		public function __construct($chave,$host="0.0.0.0",$port="11000",$userbase=array()) {
			$this->initVars();

			$this->chave 	= trim($chave);
			$this->host 	= trim($host);
			$this->port 	= trim($port);
			$this->userbase = $userbase;

		}
		
		protected function initVars() {
			$this->host = "0.0.0.0";
			$this->port = "11000";
		}
		
		

		


		/**
		 * Autentica��o do usu�rio
		 */

		protected function auth($user,$pass) {
			if( @$this->userbase[trim($user)]["enabled"] ) {
				// Usu�rio ativo
				
				if( trim($pass) && trim($pass) == trim(@$this->userbase[trim($user)]["password"]) ) {
					return true;
				}

			}			
			return false;
		}	






		/**
		 * Realiza a conversa
		 */
		protected function serverDialog() {
			// Gera o challenge
			$challenge = crypt( rand(1000,2000) ,"VA");

			@fwrite($this->conn,$this->talk("VACH",$challenge,$this->chave));


			$transmissao = false;
			$tipo_transmissao = '';
			$dados = '';



			/**
			 * Interpreta a solicita��o
			 */
			while( ($linha=fgets($this->conn)) && !feof($this->conn) ) {

				if( !$transmissao ) {
					$proc = $this->listen($linha,$this->chave);

					switch($proc["comando"]) {
						/**
						 * Autentica��o
						 */
						case 'VARP':
							// Resposta de challenge - username:senha criptografado com o challenge

							// Decriptografa
							$cript_auth = $proc["parametros"];
							$infoauth = $this->decriptografa(base64_decode($cript_auth),$challenge);


							// TODO: Autentica��o

							@list($user,$pass) = explode("::",$infoauth);

							if( !$this->auth($user,$pass) ) {
								@fwrite($this->conn,$this->talk("VAER","Autentica��o Inv�lida",$challenge));
								return(-1);
							}

							@fwrite($this->conn,$this->talk("VAOK","Bem vindo",$challenge));

							break;
						/**
						 * SendStats - O host cliente deseja enviar as estat�sticas de acesso.
						 * In�cio de transmiss�o
						 */				
						case 'VASS':
							$tipo_transmissao = 'stats';
							@fwrite($this->conn,$this->talk("VAOK","Aguardando in�cio de transmiss�o",$challenge));
							$transmissao = true;
							break;

						/**
						 * Envia a lista de ARP para o cliente.
						 */				
						case 'VAAR': // ARP REQUEST
							// Primeiro envia um "VAAS" (iniciando ARP SEND)
							@fwrite($this->conn,$this->talk("VAAS","",$challenge));
							$ip = trim($proc["parametros"]);
							
							if(!$ip) $ip = "-a";
							$arp = SOFreeBSD::obtemARP($ip);
							
							
							$tabelaarp="";
							for($i=0;$i<count($arp);$i++) {
								$tabelaarp .= $arp[$i]["addr"].",".$arp[$i]["mac"].",".$arp[$i]["iface"]."\n";
							}
							
							$this->puts(base64_encode($this->criptografa($tabelaarp,$challenge)));
							$this->puts("\n.\n");
							

							break;
						/**
						 * Evia um FPING
						 */
						case 'VAFP':
							// Primeiro envia um "VAFS" (iniciando FPING SEND)
							@fwrite($this->conn,$this->talk("VAFS","",$challenge));
							list($ip,$num_pacotes,$tamanho) = explode(":",trim($proc["parametros"]));

							
							$ping = (!$ip ? array() : SOFreeBSD::fping($ip,$num_pacotes,$tamanho));
							$resposta = implode(":",$ping);
							
							$this->puts(base64_encode($this->criptografa($resposta,$challenge)));
							$this->puts("\n.\n");
							
							break;
							
						/**
						 * Envia a lista de estat�sticas para o cliente.
						 */				
						case 'VASR': // STAT REQUEST
							// Primeiro envia um "VASI" (iniciando STAT INIT)
							@fwrite($this->conn,$this->talk("VASI","",$challenge));
							$stats = SOFreeBSD::obtemEstatisticas();
							
							
							$estatisticas="";

							while( list($username,$dados) = each($stats) ) {
								$estatisticas .= $username . "," . $dados["up"] . "," . $dados["down"] . "\n";
							}


							
							$this->puts(base64_encode($this->criptografa($estatisticas,$challenge)));
							$this->puts("\n.\n");
							
							break;

					}
				} else {
					if( trim($linha) == "." ) {
						// Fim de transmiss�o
						$transmissao = false;

						//echo "--------------------------------\n";
						//echo $dados;
						//echo "\n--------------------------------\n";


						// Decriptografa
						$dados = $this->decriptografa(base64_decode($dados),$challenge);

						//

						//echo "--------------------------------\n";
						//echo $dados;
						//echo "\n--------------------------------\n";



						// Salva no local esperado


						// Retorna OK
						@fwrite($this->conn,$this->talk("VAOK","Dados Recebidos",$challenge));

					} else {
						$dados .= $linha;
					}
				}

			}

		}
		
		
		public function start() {

			$server_str = "tcp://" . $this->host . ":" . $this->port;

			$socket = @stream_socket_server($server_str, $errno, $errstr);

			if( !$socket ) {
				echo "$errstr ($errno)\n";
				exit(-1);
			} else {
				/**
				 * Loop Principal
				 */
				while( true ) {
					while( $this->conn = @stream_socket_accept($socket) ) {

						/**
						 * Conversa 
						 */
						$this->serverDialog();


						/**
						 * Fecha a conex�o
						 */
						fclose($this->conn);

					}
				}
				fclose($socket);
			}

		}



	
	
	}
	
?>
