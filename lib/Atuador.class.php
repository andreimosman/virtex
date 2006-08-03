<?

	/**
	 * Atuador.
	 *
	 * Middle-end entre o virtexadmin e o sistema operacional
	 *
	 */
	
	require_once("SOFreeBSD.class.php");
	
	
	
	class Atuador {

		protected $bd;
		protected $so;
		
		protected $debug;

		public function __construct($bd=NULL,$debug=0) {
			$this->bd = $bd;
			$this->debug = $debug;
			$this->so = new SOFreeBSD();
		}
		
		/**
		 * Dummy
		 */
		public function processa($op,$id_conta,$parametros) {
		
		
		}
		
		public function setDebug($val=1) {
			$this->debug=$val;
		}
		
		protected function debug($mensagem) {
			if( $this->debug ) {
				echo "DEBUG: " . $mensagem . "\n";
			}
		}
		
	
	
	}
	
	 



?>
