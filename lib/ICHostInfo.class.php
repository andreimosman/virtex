<?


	/**
	 * Informações 
	 */
	
	require_once("MConfig.class.php");
	
	class ICHostInfo {
		
		protected $cfg;
		
		
		protected $hosts;
		
		public function __construct() {
			
			// Carrega o arquivo de hosts
			
			$tcfg = new MConfig(PATH_ETC."/infocenter.hosts.ini");
			$this->cfg = $tcfg->config;
			
			$this->loadCache();
		
		}
		
		protected function loadCache() {
		
			$this->hosts = array();
			
			while( list($host,$dados) = each($this->cfg) ) {
			
				if(@$dados["enabled"]) {
					$this->hosts[] = $host;
				}
			
			}
		
		}
		
		public function obtemListaServidores() {
			return($this->hosts);
		}
		
		public function obtemInfoServidor($host) {
			return($this->cfg[$host]);
		
		}
		
		
	
	
	
	}
	



?>
