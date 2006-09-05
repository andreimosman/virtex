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
		protected $pppoe_nas_list;
		protected $pppoe_iface_list;
		protected $pppoe_lista_nas;
		protected $fator;
		
		public $FW_IP_BASERULE;
		public $FW_IP_BASEPIPE_IN;
		public $FW_IP_BASEPIPE_OUT;
		public $FW_PPPoE_BASERULE;
		public $FW_PPPoE_BASEPIPE_IN;
		public $FW_PPPoE_BASEPIPE_OUT;		
		
	
		/**
		 * Recebe uma instância do banco de dados
		 */
		public function __construct($bd=NULL,$debug=0) {
			
			parent::__construct($bd,$debug);
			
			
			// Carrega arquivos de configuração necessários
			$tcfg = new MConfig(PATH_ETC."/network.ini");
			$this->net_cfg = $tcfg->config;
			
			$tcfg = new MConfig(PATH_ETC."/tcpip.ini");
			$this->tcpip_cfg = $tcfg->config;

			$tcfg = new MConfig(PATH_ETC."/pppoe.ini");
			$this->pppoe_cfg = $tcfg->config;
			
			$this->initVars();
			
			// Cacheia as informações relevantes
			$this->loadCacheInfo();
			
			//echo "ATUADOR BL!!!\n";
			
		}
		
		protected function initVars() {
			$this->fator = array();

			$this->pppoe_nas_list 	= array();
			$this->pppoe_iface_list	= array();
			$this->pppoe_lista_nas 	= array();

			$this->nas_list   = array();
			$this->iface_list = array();
			$this->lista_nas  = array();

		}
		
		
		protected function obtemInterfaceExterna() {
			$n = $this->net_cfg;
			while( list($iface,$dados) = each($n) ) {
				if($dados["status"] == "up" && $dados["type"] == "external") {
					return($iface);
				}
			}
			
			return("");
		}
		
		/**
		 * Utilizado no Boot
		 */
		public function networkUP() {
			//$this->so->ifConfig($int_if,$ip_interface,$mascara);
			
			$n = $this->net_cfg;
			
			while( list($iface,$dados) = each($n) ) {
				if( @$dados["status"] == "up" ) {
					
					// Se não tiver ip e máscara dá só um "ifconfig $iface up"
					$this->so->ifConfig($iface,@$dados["ipaddr"],@$dados["netmask"]);
					
					// Somente interfaces do tipo "external" podem ter gateway configurado.
					if(@$dados["type"] == "external" && @$dados["gateway"]) {
						$this->so->routeAdd("default",$dados["gateway"]);
						
						if((int)@$dados["nat"] == 1) {
							// TODO: NAT
							$this->so->setNAT($iface);
						}
					}

				}

			}

		}
		
		protected function loadInfo($tipo="TCPIP") {
			$n = $tipo == "PPPoE" ? $this->pppoe_cfg : $this->tcpip_cfg;
			
			while( list($iface,$dados) = each($n) ) {
				if( $dados["enabled"] ) {
					$this->fator[ trim($dados["nas_id"]) ] = (@$dados["fator"] ? $dados["fator"] : 1);
					$this->debug("loadInfo: I/[$iface] N/[" . $dados["nas_id"] . "] F/[" . (@$dados["fator"] ? $dados["fator"] : 1) . "] " );
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

		// Retorna quais NAS estão ativos neste sistema.
		public function obtemListaNasIPAtivos() {
			return($this->lista_nas);
		}

		// Retorna quais NAS estão ativos neste sistema.
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
		 * Os $parametros_pppoe são utilizados para que seja possível usar o mesmo atuador para
		 * Processar requisições PPPoE.
		 */

		public function processa($op,$id_conta,$parametros,$parametros_pppoe="") {

			$ext_if = $this->obtemInterfaceExterna();
			
			$nas_id = trim($this->obtemNasConta($id_conta));
			if(!$nas_id) return;

			$this->debug("processa: N/[" . $nas_id . "]" );			
			$tipo_nas = "TCPIP";
			$int_if = @$this->nas_list[$nas_id];
			
			if( !$int_if ) {
				$tipo_nas = "PPPoE";
				$int_if = @$this->pppoe_nas_list[$nas_id];
			}
			
			// Este NAS não deveria estar chamando desta máquina. Não está habilitado ou configurado;
			if( !$int_if ) return;
			
			switch($op) {
				
				case 'a':
					/**
					 * Adicionar.
					 *
					 * Parametros: 
					 *   rede(ou ip pppoe),mac,up,down,user
					 *
					 * Parametros Adicionais PPPoE (situação especial):
					 *	 interface,pid
					 *
					 */
					
					@list($rede,$mac,$up,$down,$user) = explode(",",$parametros);
					if( !$rede ) return;	// Se não recebeu a rede não faz nada.
					
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
						
						// A interface é requerida para uso PPPoE
						if( !$iface ) return;
						
						$ip_cliente = $rede;

						$baserule     = SistemaOperacional::$FW_PPPoE_BASERULE;
						$basepipe_in  = SistemaOperacional::$FW_PPPoE_BASEPIPE_IN;
						$basepipe_out = SistemaOperacional::$FW_PPPoE_BASEPIPE_OUT;


					}

					
					$fator = @$this->fator[ $nas_id ];
					$this->debug("processa: F/[" . $fator . "] " );
					if( !$fator ) $fator = 1;

					$this->so->adicionaRegraBW((int)$id_conta,$baserule,
					                             $basepipe_in, $basepipe_out,
					                             $iface, $ext_if, $ip_cliente, $mac, $up*$fator, $down*$fator, $user );
					
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
