<?


	/**
	 * Classe base de neg�cios. 
	 * Caso essa classe cres�a e tenha v�rias funcionalidades comuns a outros sistemas jogar as funcionalidades no framework em uma classe
	 * MNegocio.
	 */
	class VirtexNegocio {

		public function __construct() {
			VirtexModelo::init();
		}
	
	
	}


?>
