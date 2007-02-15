<?

	/**
	 * VABEPPPoE.class.php
	 *
	 * Sistema do Backend atuando em conjunto com pppoe.
	 *
	 */

	class VABEPPPoE extends VirtexAdminBackend {
	
		protected $who;
		protected $linkup;
		protected $linkdown;
		protected $kick;
		protected $rc;
		
		protected $username;
		protected $hisaddr;
		protected $interface;
		protected $pid;
	
	
	
		/**
		 * Construtor
		 */
		public function __construct() {
			parent::__construct();
			$this->initVars();
			// Configura o getopt e chama as opções para processamento posterior
			$this->_shortopts = "WUDKRIi:a:u:p:";

			$this->getopt();
		
		}

		/**
		 * Inicializa as propriedades do objeto;
		 */
		protected function initVars() {
			$this->who 				= 0;
			$this->kick 			= 0;
			$this->linkup			= 0;
			$this->linkdown			= 0;
			$this->rc				= 0;
			$this->info				= 0;
			
			$this->username 		= "";
			$this->hisaddr	 		= "";
			$this->interface 		= "";
			$this->pid				= 0;

		}
		
		
		/**
		 * Executa o aplicativo
		 * TODO: syslog e/ou sistema de logs interno para suporte
		 */
		public function executa() {
		
			/**
			 * Varre as opções
			 *
			 *    Cada linha é um array contendo um par 0 => opcao, 1 => parametro
			 *
			 */


			for($i=0;$i<count($this->options);$i++) {
				switch($this->options[$i][0]) {
					/**
					 * Opções de execução
					 */
					case 'W':
						$this->who = 1;
						break;

					case 'U':
						$this->linkup = 1;
						break;

					case 'D':
						$this->linkdown = 1;
						break;

					case 'K':
						$this->kick = 1;
						break;

					case 'R':
						$this->rc = 1;
						break;
					
					case 'I':
						$this->info = 1;
						break;
					/**
					 * Parâmetros gerais
					 */
					case 'u':
						$this->username = $this->options[$i][1];
						break;
					
					case 'a':
						$this->hisaddr = $this->options[$i][1];
						break;
					
					case 'i':
						$this->interface = $this->options[$i][1];
						break;

					case 'p':
						// Somente pra up
						$this->pid = $this->options[$i][1];
						break;
				}
			}
			
			
			/**
			 * Info
			 */
			if( $this->info ) {
				$this->print_info();
				return;
			}
			
			/**
			 * Who
			 */
			if( $this->who ) {
				$this->print_who();
				return;
			}
			
			/**
			 * Kick
			 */
			if( $this->kick ) {
				return($this->dokick());
			}
			
			/**
			 * rcstart, rcstop
			 */
			 
			if( $this->rc ) {
				if( !$this->linkup && !$this->linkdown ) {
					$this->linkup = 1;
					$this->linkdown = 1;
				}
				
				if( $this->linkdown ) {
					$this->rcstop();
				}
				
				if( $this->linkup ) {
					$this->rcstart();
				}
				
				return(0);
				
			}
			
			/**
			 * Verificações
			 */
			
			if( ($this->linkup && $this->linkdown) || (!$this->linkup && !$this->linkdown) ) {
				// Tem que estar subindo ou descendo, e nunca os dois ao mesmo tempo.
				return(-1);
			}
			
			if( ($this->linkup && ( !$this->username || !$this->hisaddr || !$this->interface || !$this->pid )) ||
			    ($this->linkdown && (!$this->username || !$this->interface))
			  ) {
				// Se os parametros requeridos para cada uma das operações não foram recebidos
				return(-1);
			}
			
			
			/**
			 * Coleta de dados
			 */
			
			$dominio_padrao = $this->prefs->obtem("geral","dominio_padrao");


			$sSQL  = "SELECT ";
			$sSQL .= "       cn.id_conta, cbl.mac, cbl.upload_kbps, cbl.download_kbps, cbl.id_nas ";
			$sSQL .= "FROM ";
			$sSQL .= "       cntb_conta_bandalarga cbl INNER JOIN cntb_conta cn USING(username, tipo_conta) ";
			$sSQL .= "WHERE ";
			$sSQL .= "       cn.username = '" . $this->username . "' ";
			$sSQL .= "       AND cn.dominio = '" . $dominio_padrao . "' ";
			$sSQL .= "       AND cn.tipo_conta = 'BL' ";
			
			$info = $this->bd->obtemUnicoRegistro($sSQL);
			
			if( !@$info["id_conta"] ) return(-1);
			

			$abl = new AtuadorBandaLarga();

			
			if( $this->linkup ) {
			
				$ip = $this->hisaddr;
				$mac = "";
				$up = $info["upload_kbps"];
				$dn = $info["download_kbps"];
				$user = $this->username;

				$parametros = "$ip,$mac,$up,$dn,$user";
				$parametros_pppoe = $this->interface . "," . $this->pid;

				$abl->processa('a',$info["id_conta"],$parametros,$parametros_pppoe);
				
			} else {
			
				$abl->processa('x',$info["id_conta"],"");
			
			}
			
		}
		
		/**
		 * Starta o PPPoE em todas as interfaces
		 */
		public function rcstart() {
			
			$bl = new AtuadorBandaLarga();
			$interfaces = $bl->obtemListaNasPPPoEAtivos();
			
			for ($i=0;$i<count($interfaces);$i++){
				$comando_start = "/usr/libexec/pppoed -d -P /var/run/pppoe.pid -p '*' -l pppoe-in ".$interfaces[$i];
				//echo $comando_start."\n";
				// SOFreeBSD::executa("echo \"$comando_start\"|/bin/sh\n");
			}
			
		}
		
		/**
		 * Mata o processo PPPoE nas interfaces
		 */
		public function rcstop() {
		
			$comando_stop_pppoe = "/usr/bin/killall -HUP pppoed";
			$comando_stop_ppp = "/usr/bin/killall -HUP ppp";
			
			SOFreeBSD::executa($comando_stop_pppoe);			
			SOFreeBSD::executa($comando_stop_ppp);
			
		}
		
		
	
		protected function print_who() {
		
		}
		
		protected function print_info() {
			$bl = new AtuadorBandaLarga();

			$ifaces = $bl->obtemListaIfacesPPPoEAtivos();
			
			for($i=0;$i<count($ifaces);$i++) {
				echo $ifaces[$i]."\n";
			}
		}
		
		protected function dokick() {
		
		}
		
		
		

	}
	

	
?>
