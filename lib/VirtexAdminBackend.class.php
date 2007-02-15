<?


//require_once(PATH_LIB."/VirtexAdmin.class.php");
//require_once("Console/Getopt.php");

/**
 * Classe do BackEnd
 */
class VirtexAdminBackend extends VirtexAdmin {

	
	protected $_console;
	protected $_args;
	protected $_options;
	protected $_shortopts;
	protected $_longopts;
	
	
	protected $options;


	
	public function __construct($usar_bd=true,$usar_lic=false) {
		// Inicializações da classe pai
		parent::__construct("etc/virtex.ini","template/backend",$usar_bd,$usar_lic);
		
		$this->_console = new Console_Getopt;
		$this->_args = $this->_console->readPHPArgv();

		if( !count($this->_args) ) {
			exit(-1); // Não tá chamando no console;
		}
		
		// Tira o executável dos argumentos
		//array_shift($this->_args);

		$this->_shortopts = NULL;
		$this->_longopts  = NULL;
		
		$this->_options = array();

		if( @$this->usar_bd && @$this->cfg->config["DB"]["dsn"] ) {
			// Instanciar BD;
			//echo "PREFERENCIAS!!!<br>\n";


			$this->prefs = new Preferencias($this->bd);


		}
		
	}
	
	/**
	 * Quando tiver um erro no getOpt será chamada esta função
	 */
	protected function usage() {
		echo "Erro!\n\n";
	}
	
	/**
	 * Faz o parse das opções via linha de comando
	 */
	protected function getopt() {


		if( $this->_shortopts || $this->_longopts ) {


			//echo "GETTING OPTIONS\n";
			$this->_options = $this->_console->getopt($this->_args,$this->_shortopts,$this->_longopts);
		
			if(PEAR::isError($this->_options) || count($this->_options[1])) {
				$this->usage();
				exit(-1);
			}

			
			$this->options = $this->_options[0];

		}

	}
	
	// Já faz o getopt	
	public function executa() {
		
	}
	
}




?>
