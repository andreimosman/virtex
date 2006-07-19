<?

	require_once(PATH_LIB."/Atuador.class.php");
	require_once(PATH_LIB."/redes.class.php");

	class AtuadorBandaLarga extends Atuador {
		// Configurações de rede
		protected $net_cfg;
		
		
		// Arquivos de configuração específicos para cada tipo de atuador banda larga.
		protected $tcpip_cfg;		// Usado para fins gerais
		protected $pppoe_cfg;		// Usado apenas para o boot
		
		
		// Cache de informações (pra agilizar o processo)
		protected $ext_if;
		protected $nas_list;
		protected $iface_list;
		
		protected $lista_nas;
		
	
		/**
		 * Recebe uma instância do banco de dados
		 */
		public function __construct($bd=NULL) {
			
			parent::__construct($bd);
			
			
			// Carrega arquivos de configuração necessários
			$tcfg = new MConfig(PATH_ETC."/network.ini");
			$this->net_cfg = $tcfg->config;
			
			$tcfg = new MConfig(PATH_ETC."/tcpip.ini");
			$this->tcpip_cfg = $tcfg->config;

			$tcfg = new MConfig(PATH_ETC."/pppoe.ini");
			$this->pppoe_cfg = $tcfg->config;
			
			
			// Cacheia as informações relevantes
			$this->loadCacheInfo();
			
			//echo "ATUADOR BL!!!\n";
			
		}
		
		
		protected function obtemInterfaceExterna() {
			$n = $this->net_cfg;
			while( list($iface,$dados) = each($n) ) {
				if($dados["status"] == "up" && $dados["type"] == "external") {
					return($iface);
				}
			}
		}
		
		protected function loadCacheInfo() {
			$n = $this->tcpip_cfg;
			
			$this->ext_if = $this->obtemInterfaceExterna();
			
			$this->nas_list   = array();
			$this->iface_list = array();
			$this->lista_nas  = array();

			while( list($iface,$dados) = each($n) ) {
				if( $dados["enabled"] ) {
					$this->lista_nas[] = $dados["nas_id"];
					$this->nas_list[ $dados["nas_id"] ] = $iface;
					$this->iface_list[$iface] = $dados["nas_id"];
				}
			}

		}

		// Retorna quais NAS estão ativos neste sistema.
		public function obtemListaNasIPAtivos() {
			return($this->lista_nas);
		}

		
		protected function obtemNasConta($id_conta) {
		
			$sSQL  = "SELECT ";
			$sSQL .= "   cbl.id_nas ";
			$sSQL .= "FROM ";
			$sSQL .= "   cntb_conta c INNER JOIN cntb_conta_bandalarga cbl USING (username,dominio,tipo_conta) ";
			$sSQL .= "WHERE ";
			$sSQL .= "   c.id_conta = '".$this->bd->escape($id_conta)."'";
			
			$r = $this->bd->obtemUnicoRegistro($sSQL);
			
			return(@$r["id_nas"]);
		
		}

		

		public function processa($op,$id_conta,$parametros) {

			// TODO: PEGAR INTERFACES DO ARQUIVO DE CONFIGURAÇÕES
			$ext_if = $this->obtemInterfaceExterna();
			
			//$int_if = "rl1";
			
			$nas_id = $this->obtemNasConta($id_conta);
			if(!$nas_id) return;
			
			$int_if = @$this->nas_list[$nas_id];
			
			switch($op) {
				
				case 'a':
					/**
					 * Adicionar.
					 *
					 * Parametros: 
					 *   rede,mac,up,down,user
					 */
					 
					list($rede,$mac,$up,$down,$user) = explode(",",$parametros);
					 
					$r = new RedeIP($rede);
					 
					$ip_interface	= $r->minHost();
					$ip_cliente	= $r->maxHost();
					$mascara		= $r->mascara();
					 
					 
					$this->so->ifConfig($int_if,$ip_interface,$mascara);
					$this->so->adicionaRegraBW((int)$id_conta,SistemaOperacional::$FW_IP_BASERULE,
					                             SistemaOperacional::$FW_IP_BASEPIPE_IN, SistemaOperacional::$FW_IP_BASEPIPE_OUT,
					                             $int_if, $ext_if, $ip_cliente, $mac, $up, $down, $user );
					
					break;
					 
				case 'x':
					/**
					 * Excluir.
					 *
					 * Parametros: 
					 *   $ipaddr (ip da interface interna)
					 */
					
					$ipaddr = $parametros;
					
					$this->so->ifUnConfig($int_if,$ipaddr);
					$this->so->deletaRegraBW((int)$id_conta, SistemaOperacional::$FW_IP_BASERULE, 
												SistemaOperacional::$FW_IP_BASEPIPE_IN, SistemaOperacional::$FW_IP_BASEPIPE_OUT);
					
					break;
					
			
			}
		
		}
	
	}


?>