<?


require_once(PATH_LIB."/VirtexAdmin.class.php");
require_once("Console/Getopt.php");

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


	
	public function __construct() {
		// Inicializa��es da classe pai
		parent::__construct("etc/virtex.ini","template/backend");
		
		$this->_console = new Console_Getopt;
		$this->_args = $this->_console->readPHPArgv();
		
		if( !count($this->_args) ) {
			exit(-1); // N�o t� chamando no console;
		}
		
		// Tira o execut�vel dos argumentos
		//array_shift($this->_args);
		
		$this->_shortopts = NULL;
		$this->_longopts  = NULL;
		
		$this->_options = array();
		
	}
	
	/**
	 * Quando tiver um erro no getOpt ser� chamada esta fun��o
	 */
	protected function usage() {
		echo "Erro!\n\n";
	}
	
	/**
	 * Faz o parse das op��es via linha de comando
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
	
	// J� faz o getopt	
	public function executa() {
		
		

	}
	
	

}




?>