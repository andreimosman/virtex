<?


	/**
	 * Classe do Backend p/ Servidor de Informações
	 */

	require_once(PATH_LIB."/VirtexAdminBackend.class.php");
	require_once(PATH_LIB."/ICServer.class.php");
	 
	class VABEInfoServer extends VirtexAdminBackend {
		
		protected $userbase;
		protected $srv_cfg;
		
		protected $fork;
	
		public function __construct() {
			parent::__construct(false);	// Sem banco de dados
			
			// Arquivo de Configuração do Servidor
			$cfg = new MConfig(PATH_ETC."/infocenter.server.ini");
			$this->srv_cfg = $cfg->config;
			
			// Arquivo de Configuração dos Usuários do Servidor
			$cfg = new MConfig(PATH_ETC."/infocenter.users.ini");
			$this->userbase = $cfg->config;
			
			
			// HARDCODDED
			$this->fork = 1;
			
			

		}

		public function loop() {
			$pid = pcntl_fork();

			if ($pid == -1) {
				die("could not fork");
			} else if( $pid ) {
				// Parent
			} else {
				while(true) {
					$this->executa();
				}
			}

		}

		
		public function executa() {
		
			//echo "CHAVE: " . ;		
		
		
			// TODO: Pegar dados do arquivo de configuração
			$srv = new ICServer(@$this->srv_cfg["geral"]["chave"],@$this->srv_cfg["geral"]["host"],@$this->srv_cfg["geral"]["port"],$this->userbase);
			
			$srv->start();
		
		
			
		
		
		}
	
	}