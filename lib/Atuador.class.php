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

		public function __construct($bd=NULL) {
			$this->bd = $bd;
			$this->so = new SOFreeBSD();
		}
		
		/**
		 * Dummy
		 */
		public function processa($op,$id_conta,$parametros) {
		
		
		}
	
	
	}
	
	 



?>
