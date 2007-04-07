<?


	/**
	 * Classe base de negócios. 
	 * Caso essa classe cresça e tenha várias funcionalidades comuns a outros sistemas jogar as funcionalidades no framework em uma classe
	 * MNegocio.
	 */
	class VirtexNegocio {

		public function __construct() {
			VirtexModelo::init();
		}
	
	
	}


?>
