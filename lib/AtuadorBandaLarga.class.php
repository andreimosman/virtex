<?

	require_once(PATH_LIB."/Atuador.class.php");
	require_once(PATH_LIB."/redes.class.php");

	class AtuadorBandaLarga extends Atuador {
		// Configura��es de rede
		protected $net_cfg;
		
		
		// Arquivos de configura��o espec�ficos para cada tipo de atuador banda larga.
		protected $tcpip_cfg;		// Usado para fins gerais
		protected $pppoe_cfg;		// Usado apenas para o boot
		
		
		// Cache de informa��es (pra agilizar o processo)
		protected $ext_if;
		protected $nas_list;
		protected $iface_list;
		protected $lista_nas;
		protected $pppoe_nas_list;
		protected $pppoe_iface_list;
		protected $pppoe_lista_nas;
		
	
		/**
		 * Recebe uma inst�ncia do banco de dados
		 */
		public function __construct($bd=NULL) {
			
			parent::__construct($bd);
			
			
			// Carrega arquivos de configura��o necess�rios
			$tcfg = new MConfig(PATH_ETC."/network.ini");
			$this->net_cfg = $tcfg->config;
			
			$tcfg = new MConfig(PATH_ETC."/tcpip.ini");
			$this->tcpip_cfg = $tcfg->config;

			$tcfg = new MConfig(PATH_ETC."/pppoe.ini");
			$this->pppoe_cfg = $tcfg->config;
			
			
			// Cacheia as informa��es relevantes
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
		
		
		
		protected function loadInfo($tipo="TCPIP") {
			$n = $tipo == "PPPoE" ? $this->pppoe_cfg : $this->tcpip_cfg;
			
			if($tipo == "PPPoE") {
				$this->pppoe_nas_list 	= array();
				$this->pppoe_iface_list	= array();
				$this->pppoe_lista_nas 	= array();
			} else {
				$this->nas_list   = array();
				$this->iface_list = array();
				$this->lista_nas  = array();
			}

			while( list($iface,$dados) = each($n) ) {
				if( $dados["enabled"] ) {
					if( $tipo == "PPPoE" ) {
						$this->pppoe_lista_nas[] = $dados["nas_id"];
						$this->pppoe_nas_list[ $dados["nas_id"] ] = $iface;
						$this->pppoe_iface_list[$iface] = $dados["nas_id"];
					} else {
						$this->lista_nas[] = $dados["nas_id"];
						$this->nas_list[ $dados["nas_id"] ] = $iface;
						$this->iface_list[$iface] = $dados["nas_id"];
					}
				}
			}
		}
		
		
		protected function loadCacheInfo() {
			$n = $this->tcpip_cfg;
			$this->ext_if = $this->obtemInterfaceExterna();
			
			$this->loadInfo("TCPIP");
			$this->loadInfo("PPPoE");
		}

		// Retorna quais NAS est�o ativos neste sistema.
		public function obtemListaNasIPAtivos() {
			return($this->lista_nas);
		}

		// Retorna quais NAS est�o ativos neste sistema.
		public function obtemListaNasPPPoEAtivos() {
			return($this->pppoe_lista_nas);
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

		
		/**
		 * Os $parametros_pppoe s�o utilizados para que seja poss�vel usar o mesmo atuador para
		 * Processar requisi��es PPPoE.
		 */

		public function processa($op,$id_conta,$parametros,$parametros_pppoe="") {

			$ext_if = $this->obtemInterfaceExterna();
			
			$nas_id = $this->obtemNasConta($id_conta);
			if(!$nas_id) return;
			
			$tipo_nas = "TCPIP";
			$int_if = @$this->nas_list[$nas_id];
			
			if( !$int_if ) {
				$tipo_nas = "PPPoE";
				$int_if = @$this->pppoe_nas_list[$nas_id];
			}
			
			// Este NAS n�o deveria estar chamando desta m�quina. N�o est� habilitado ou configurado;
			if( !$int_if ) return;
			
			switch($op) {
				
				case 'a':
					/**
					 * Adicionar.
					 *
					 * Parametros: 
					 *   rede(ou ip pppoe),mac,up,down,user
					 *
					 * Parametros Adicionais PPPoE (situa��o especial):
					 *	 interface,pid
					 *
					 */
					
					@list($rede,$mac,$up,$down,$user) = explode(",",$parametros);
					if( !$rede ) return;	// Se n�o recebeu a rede n�o faz nada.
					
					if( $tipo_nas == "TCPIP" ) {
						$r = new RedeIP($rede);

						$ip_interface	= $r->minHost();
						$ip_cliente	= $r->maxHost();
						$mascara		= $r->mascara();
						
						$iface = $int_if;
					 
						$this->so->ifConfig($int_if,$ip_interface,$mascara);
						
						$baserule     = SistemaOperacional::$FW_IP_BASERULE;
						$basepipe_in  = SistemaOperacional::$FW_IP_BASEPIPE_IN;
						$basepipe_out = SistemaOperacional::$FW_IP_BASEPIPE_OUT;
						
						
					} else {
						@list($iface,$pid) = explode(",",$parametros);
						
						// A interface � requerida para uso PPPoE
						if( !$iface ) return;

						$baserule     = SistemaOperacional::$FW_PPPoE_BASERULE;
						$basepipe_in  = SistemaOperacional::$FW_PPPoE_BASEPIPE_IN;
						$basepipe_out = SistemaOperacional::$FW_PPPoE_BASEPIPE_OUT;


					}

					$this->so->adicionaRegraBW((int)$id_conta,$baserule,
					                             $basepipe_in, $basepipe_out,
					                             $iface, $ext_if, $ip_cliente, $mac, $up, $down, $user );
					
					break;
					 
				case 'x':
					/**
					 * Excluir.
					 *
					 * Parametros: 
					 *   $ipaddr (ip da interface interna, apenas pra tcpip)
					 */
					
					if( $tipo_nas == "TCPIP" ) {
						$ipaddr = $parametros;
					
						$this->so->ifUnConfig($int_if,$ipaddr);

						$baserule     = SistemaOperacional::$FW_IP_BASERULE;
						$basepipe_in  = SistemaOperacional::$FW_IP_BASEPIPE_IN;
						$basepipe_out = SistemaOperacional::$FW_IP_BASEPIPE_OUT;

					} else {

						$baserule     = SistemaOperacional::$FW_PPPoE_BASERULE;
						$basepipe_in  = SistemaOperacional::$FW_PPPoE_BASEPIPE_IN;
						$basepipe_out = SistemaOperacional::$FW_PPPoE_BASEPIPE_OUT;

					}

					$this->so->deletaRegraBW((int)$id_conta, $baserule, 
												$basepipe_in, $basepipe_out);
					
					break;
					
			
			}
		
		}
	
	}


?>