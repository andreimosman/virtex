<?


	/**
	 * VA InfoCenter
	 *
	 * Implementa��o Cliente
	 */
	require_once(PATH_LIB."/InfoCenter.class.php");
	require_once("SOFreeBSD.class.php");

	class ICClient extends InfoCenter {
	
		protected $conectado;

		public function __construct() {
		
			$this->conectado = false;
			
		}
		
		/**
		 * Autentica��o
		 */
		protected function clientAuth($chave,$user,$pass) {
			if( $this->conectado ) {
				/**
				 * Interpreta a solicita��o
				 */
				
				// Zera a indica��o de conectado.
				$this->conectado 	= false;
				$this->chave 		= "";

				while( ($linha=fgets($this->conn)) && !feof($this->conn) ) {

					$proc = $this->listen($linha,$chave);

					switch($proc["comando"]) {

						case 'VACH':
							// Recebeu o challenge

							$challenge = $proc["parametros"];
							$infoauth = "$user::$pass";
							$cript_auth = base64_encode($this->criptografa($infoauth,$challenge));

							// Enviar resposta
							fputs($this->conn,$this->talk("VARP",$cript_auth,$chave));
							break;

						case 'VAOK':
							$this->challenge 	= $challenge;
							$this->chave		= $chave;
							$this->conectado 	= true;
							return(true);
							break;


						case 'VAER':
							return(false);
							break;

					}
				}
			}
			
			return(false);
		}

		/**
		 * Obtem Dados
		 */
		protected function getData($comando,$parametros) {
			if( !$this->conectado ) return "";

			fputs($this->conn,$this->talk($comando,$parametros,$this->chave));
			$recebendo = false;
			$dados = "";
			while( ($linha=fgets($this->conn)) && !feof($this->conn) ) {
				if(!$recebendo) {
					$proc = $this->listen($linha,$this->chave);

					switch($proc["comando"]) {
						case 'VAER':
							return("");
							break;

						case 'VAAS':
							// INICIANDO O ARP SEND
						case 'VASI':
							// STATS INIT



							$recebendo = true;
							break;

					}
				} else {
					if( trim($linha) == "." ) {
						// Final de transmiss�o
						//echo "-------------------------------\n";
						//echo "$dados\n";
						//echo "--------------------------------\n";

						$dados = $this->decriptografa(base64_decode($dados),$this->challenge);

						//echo "-------------------------------\n";
						//echo "$dados\n";
						//echo "--------------------------------\n";

						return $dados;


					}
					$dados .= $linha;
				}
			}

		}

		
		/**
		 * Abre uma conex�o
		 */
		public function open($host,$porta,$chave,$user,$pass) {
			$this->conn = fsockopen($host,$porta,$errno,$errstr,30);
			
			$this->conectado = false;
			if( !$this->conn ) {
				return(false);
			} else {
				$this->conectado = true;
				if( !$this->clientAuth($chave,$user,$pass) ) {
					return(false);
				}
			}
			
			$this->conectado = true;
			
			return(true);
			
			
		}
		
		/**
		 * Fecha a conex�o
		 */
		public function close() {
			@fclose($this->conn);
		}
		
		/**
		 * Obtem a tabela arp geral ou do endere�o especificado
		 */
		public function getARP($ip="") {
			$dados = $this->getData("VAAR",$ip);
			
			$arp = array();
			
			$linhas = explode("\n",$dados);
			
			for($i=0;$i<count($linhas);$i++) {
				
				@list($addr,$mac,$iface) = explode(',',$linhas[$i]);
				
				$arp[] = array( "addr" => $addr, "mac" => $mac, "iface" => $iface );
			}
			
			return($arp);
		}
		
		/**
		 * Obtem as estat�sticas de acesso
		 * Retorna lista no formato padr�o
		 */
		public function getStats() {
			$dados = $this->getData("VASR","");
			return($dados);
		}
		
		





	}

?>