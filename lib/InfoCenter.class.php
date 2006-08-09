<?

	/**
	 * Protocolo de comunicação do sistema de informações centralizadas
	 */

	Class InfoCenter {

		protected $conn;		// Conexão (cliente ou servidor)
		
		
		protected $chave;		// Shared Key
		protected $challenge;	// Challenge
		
		

		public function __construct() {

		}


		/**
		 * Criptografia
		 */
		function criptografa($texto,$chave) {
			srand();
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

			return(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $chave, $texto, MCRYPT_MODE_ECB, $iv));
		}

		function decriptografa($texto,$chave) {
			srand();
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

			return(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $chave, $texto, MCRYPT_MODE_ECB, $iv));
		}
		
		
		
		/**
		 * Comunicação
		 */

		// Monta string para envio
		function talk($op,$conteudo,$chave) {
			$cnt=base64_encode($this->criptografa($conteudo,$chave));
			//echo "TALK:  [$op]    [$cnt]\n";
			return($op."-".$cnt."\n");

		}

		// Analisa recebimento de comando
		function listen($linha,$chave) {
			$comando 	= substr($linha,0,4);
			$parametros	= trim(substr($linha,5));

			//echo "LIST:  [$comando]    [$parametros]\n";

			return(array("comando" => $comando,"parametros" => $this->decriptografa(base64_decode($parametros),$chave)));
		}

	}


?>
