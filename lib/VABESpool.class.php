<?

	/**
	 * VABESpool.class.php
	 *
	 * Sistema do Backend para processamento da spool.
	 *
	 *
	 */
	
	class VABESpool extends VirtexAdminBackend {
	
		/**
		 * Configurações de execução
		 */
		protected $boot;
		protected $daemon;
		
		protected $debug;
		
		//protected $licencaBL;
		//protected $licencaE;
		//protected $licencaH;
		
		
		
		/**
		 * Construtor
		 */
		public function __construct() {
			// Chama construtor da superclasse
			parent::__construct();
			
			
			$this->boot   = 0;
			$this->daemon = 0;
			
			$this->debug = 0;
			
			//$this->carregaLicenca();
			
			/**
			 * GETOPT SHORT OPTIONS
			 *
			 * -b|--boot	Boot
			 * 
			 */
			$this->_shortopts = "bdD";
			$this->_longopts = array("boot","daemon","--debug");
			
			$this->getopt();

		}
		
		//protected function carregaLicenca() {
		//	$this->licencaBL = $this->licenca("backend","banda_larga");
		//	$this->licencaE = $this->licenca("backend","email");
		//	$this->licencaH = $this->licenca("backend","hospedagem");		
		//}
		
		public function usage() {
		
			echo "\n  Use: \n\n";
			echo "\tphp vtx-spool.php [<-b|--boot>|<-d|--daemon>]\n\n";
		
		}
		
		
		public function executa() {
			// Execuções inicias
			parent::executa();
			
			
			
			
			/**
			 * Varre as opções
			 *
			 *    Cada linha é um array contendo um par 0 => opcao, 1 => parametro
			 *
			 */

			for($i=0;$i<count($this->options);$i++) {
			
				switch($this->options[$i][0]) {
				
					case 'b':
					case '--boot':
						$this->boot = 1;
						break;
					
					case 'd':
					case '--daemon':
						$this->daemon = 1;
						break;
					
					case 'D':
					case '--debug':
						$this->debug = 1;
						break;
				
				
				}
			
			}
			
			if( $this->boot && $this->daemon ) {
				$this->usage();
				exit(-1);
			}
			
			/**
			 * Executa os procedimentos de boot
			 */
			if( $this->boot ) {
				// No caso de boot atualiza a data/hora do servidor e carrega a licenca novamente
				SOFreeBSD::ntpDate();
				//$this->carregaLicenca();
				
				
				// Atuador BandaLarga é usado inclusive para a configuração de rede
				$abl = new AtuadorBandaLarga($this->bd,$this->debug);
				
				/**
				 * Configurações de rede
				 */
				 //$abl->networkUP();
				
				//$ext_if = $abl->obtemInterfaceExterna();
				
				/**
				 * BandaLarga: TCP/IP
				 */
				 
				$lista_nas = $abl->obtemListaNasIPAtivos();
				
				if(count($lista_nas)) {
					/**
					 * Ergue os endereços e regras de infra-estrutura
					 */
					
					$sSQL  = "SELECT ";
					$sSQL .= "   r.rede,r.id_rede,nr.id_nas ";
					$sSQL .= "FROM ";
					$sSQL .= "   cftb_nas_rede nr INNER JOIN cftb_rede r USING(rede) ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_nas IN (".implode(",",$lista_nas).") ";
					$sSQL .= "   AND r.tipo_rede = 'I' ";
					
					$infra = $this->bd->obtemRegistros($sSQL);
					
					for($i=0;$i<count($infra);$i++) {
						//echo "Infra: \n";
						//echo "   " . $infra[$i]["rede"] . " - " . $infra[$i]["id_rede"] . "\n";
						$abl->infraUP($infra[$i]["rede"],$infra[$i]["id_rede"],$infra[$i]["id_nas"]);
					}
					
					//return;
					
				
				
				
					//if ($this->licencaBL == 1) { 
				
						// Obtem os clientes ativos para os NAS operados nesta máquina
						$sSQL  = "SELECT ";
						$sSQL .= "   c.username,c.dominio,c.tipo_conta,c.id_conta,cbl.id_pop,cbl.id_nas, ";
						$sSQL .= "   cbl.rede,cbl.upload_kbps,cbl.download_kbps,c.status,cbl.mac,";
						$sSQL .= "   cbl.ip_externo ";
						$sSQL .= "FROM ";
						$sSQL .= "   cntb_conta c INNER JOIN cntb_conta_bandalarga cbl USING(username,dominio,tipo_conta) ";
						$sSQL .= "WHERE";
						$sSQL .= "   id_nas IN (".implode(",",$lista_nas).")";
						$sSQL .= "   AND c.status = 'A' ";

						$contas = $this->bd->obtemRegistros($sSQL);


						for($i=0;$i<count($contas);$i++) {
							$parametros = $contas[$i]["rede"] . "," . $contas[$i]["mac"] . "," . 
											$contas[$i]["upload_kbps"] . "," . $contas[$i]["download_kbps"] . "," . $contas[$i]["username"];

							$abl->processa("a",$contas[$i]["id_conta"],$parametros);

						}
					//}
				}
				
				
				/**
				 * BandaLarga: PPPoE
				 */
				//SOFreeBSD::executa("/usr/local/bin/php /mosman/virtex/app/bin/vtx-pppoe.php -RU");
				//require_once(PATH_LIB."/VABEPPPoE.class.php");
				//$p = new VABEPPPoE();
				//$p->rcstart();
				

				/**
				 * Radius
				 */
				//SOFreeBSD::executa("/usr/local/bin/php /mosman/virtex/app/bin/vtx-radius.php -RU &");
				//require_once(PATH_LIB."/VABERadius.class.php");
				//$r = new VABERadius();
				//$r->rcstart();
				
				/**
				 * Hospedagem
				 */
				
				
				
				
				/**
				 * Email
				 */
				

			}
			
			/**
			 * Daemon
			 */
			if( $this->daemon ) {
				$this->daemonize();
				exit(0);
			}
			
			$this->spool();

		}
		
		protected function daemonize() {
		
			$pid = pcntl_fork();
			
			if ($pid == -1) {
				echo "Não é possível rodar como daemon\n\n";
				exit(-1);

			} else if($pid) {
				// Processo principal

			} else {
				// Processo filho
				
				
				/**
				 * Main loop
				 */
				
				while(1) {

					// Processa a spool
					$this->spool();

					// Aguarda 10 segundos
					sleep(10);
				
				}

			
			}
		
		}
		
		/**
		 * Verifica a tabela de spool
		 */
		protected function spool() {

			/**
			 * Consulta Spool Bandalarga p/ esta máquina
			 */

			$abl = new AtuadorBandaLarga($this->bd,$this->debug);
			$lista_nas = $abl->obtemListaNasIPAtivos();
			
			if( count($lista_nas) ) {
			
				//if ($this->licencaBL != 1) return -1 ;
				
				/**
				 * Processo de infra-estrutura
				 */

				// Início da transação
				//$this->bd->consulta("BEGIN");

				$sSQL  = "SELECT ";
				$sSQL .= "   id_spool, op, id_conta, parametros ";
				$sSQL .= "FROM ";
				$sSQL .= "   sptb_spool ";
				$sSQL .= "WHERE ";
				$sSQL .= "   tipo = 'IF' ";
				$sSQL .= "   AND status = 'A' ";
				$sSQL .= "   AND destino in ('". implode("','",$lista_nas) ."') ";
				$sSQL .= "FOR UPDATE";
				
				$fila = $this->bd->obtemRegistros($sSQL);
				
				for($i=0;$i<count($fila);$i++) {

					$sSQL = "UPDATE sptb_spool SET status = 'ERR' WHERE id_spool = '".$this->bd->escape($fila[$i]["id_spool"])."'";
					$this->bd->consulta($sSQL);


					// TODO: TRATAR ERRO DE PROCESSAMENTO E JOGAR PRO BANCO
					$abl->processaIF($fila[$i]["op"],$fila[$i]["id_conta"],$fila[$i]["parametros"]);
					
					$sSQL = "UPDATE sptb_spool SET status = 'OK' WHERE id_spool = '".$this->bd->escape($fila[$i]["id_spool"])."'";
					$this->bd->consulta($sSQL);
				
				}
				
				// Fim da transação
				//$this->bd->consulta("END");




				
				
			
			
				/**
				 * Processo de cliente banda larga
				 */
			
				// Início da transação
				$this->bd->consulta("BEGIN");

				$sSQL  = "SELECT ";
				$sSQL .= "   id_spool, op, id_conta, parametros ";
				$sSQL .= "FROM ";
				$sSQL .= "   sptb_spool ";
				$sSQL .= "WHERE ";
				$sSQL .= "   tipo = 'BL' ";
				$sSQL .= "   AND status = 'A' ";
				$sSQL .= "   AND destino in ('". implode("','",$lista_nas) ."') ";
				$sSQL .= "FOR UPDATE";
				
				$fila = $this->bd->obtemRegistros($sSQL);
				
				for($i=0;$i<count($fila);$i++) {
					// TODO: TRATAR ERRO DE PROCESSAMENTO E JOGAR PRO BANCO
					$abl->processa($fila[$i]["op"],$fila[$i]["id_conta"],$fila[$i]["parametros"]);
					
					$sSQL = "UPDATE sptb_spool SET status = 'OK' WHERE id_spool = '".$this->bd->escape($fila[$i]["id_spool"])."'";
					$this->bd->consulta($sSQL);
				
				}
				
				// Fim da transação
				$this->bd->consulta("END");
			}
			
			/**
			 * Consulta Spool Email p/ esta mquina
			 */

			 $ae = new AtuadorEmail($this->bd);

			 $lista_srve = $ae->obtemListaServidores();

			 if( count($lista_srve) ) {
					// Início da transação
					
					
					//if ($this->licencaE != 1) return -1;
					
					
					$this->bd->consulta("BEGIN");

					// FAZ O SELECT		 		
					$sSQL  = "SELECT ";
					$sSQL .= "   id_spool, op, id_conta, parametros ";
					$sSQL .= "FROM ";
					$sSQL .= "   sptb_spool ";
					$sSQL .= "WHERE ";
					$sSQL .= "   tipo = 'E' ";
					$sSQL .= "   AND status = 'A' ";
					$sSQL .= "   AND destino in ('". implode("','",$lista_srve) ."') ";
					$sSQL .= "FOR UPDATE";

					$fila = $this->bd->obtemRegistros($sSQL);

					$base_email = $this->prefs->obtem("geral","email_base");


					for($i=0;$i<count($fila);$i++) {
						$ae->processa($fila[$i]["op"],$fila[$i]["id_conta"],$fila[$i]["parametros"],$base_email);

						$sSQL = "UPDATE sptb_spool SET status = 'OK' WHERE id_spool = '".$this->bd->escape($fila[$i]["id_spool"])."'";
						$this->bd->consulta($sSQL);
					}

					// Fim da transação
					$this->bd->consulta("END");		 		
			 }

			/**
			 * Consulta Spool Hospedagem p/ esta mquina
			 */

			 $ah = new AtuadorHospedagem($this->bd,$this->tpl);

			 $lista_srvh = $ah->obtemListaServidores();

			 if( count($lista_srvh) ) {
			 
			 		//if ($this->licencaH != 1) {
						$this->bd->consulta("BEGIN");

						// FAZ O SELECT		 		
						$sSQL  = "SELECT ";
						$sSQL .= "   id_spool, op, id_conta, parametros ";
						$sSQL .= "FROM ";
						$sSQL .= "   sptb_spool ";
						$sSQL .= "WHERE ";
						$sSQL .= "   tipo = 'H' ";
						$sSQL .= "   AND status = 'A' ";
						$sSQL .= "   AND destino in ('". implode("','",$lista_srvh) ."') ";
						$sSQL .= "FOR UPDATE";

						$fila = $this->bd->obtemRegistros($sSQL);

						$base_hosp = $this->prefs->obtem("geral","hosp_base");

						$hosp_server = $this->prefs->obtem("geral","hosp_server");

						$parametros_hospedagem = $base_hosp.",".$hosp_server;


						for($i=0;$i<count($fila);$i++) {
							$ah->processa($fila[$i]["op"],$fila[$i]["id_conta"],$fila[$i]["parametros"],$parametros_hospedagem);

							$sSQL = "UPDATE sptb_spool SET status = 'MODULO H NÃO LICENCIADO' WHERE id_spool = '".$this->bd->escape($fila[$i]["id_spool"])."'";
							$this->bd->consulta($sSQL);
						}

						// Fim da transação
						$this->bd->consulta("END");		 		

			 			//return -1;
			 			
			 		//}
			 
			 
					// Início da transação
					$this->bd->consulta("BEGIN");

					// FAZ O SELECT		 		
					$sSQL  = "SELECT ";
					$sSQL .= "   id_spool, op, id_conta, parametros ";
					$sSQL .= "FROM ";
					$sSQL .= "   sptb_spool ";
					$sSQL .= "WHERE ";
					$sSQL .= "   tipo = 'H' ";
					$sSQL .= "   AND status = 'A' ";
					$sSQL .= "   AND destino in ('". implode("','",$lista_srvh) ."') ";
					$sSQL .= "FOR UPDATE";

					$fila = $this->bd->obtemRegistros($sSQL);

					$base_hosp = $this->prefs->obtem("geral","hosp_base");

					$hosp_server = $this->prefs->obtem("geral","hosp_server");

					$parametros_hospedagem = $base_hosp.",".$hosp_server;


					for($i=0;$i<count($fila);$i++) {
						$ah->processa($fila[$i]["op"],$fila[$i]["id_conta"],$fila[$i]["parametros"],$parametros_hospedagem);

						$sSQL = "UPDATE sptb_spool SET status = 'OK' WHERE id_spool = '".$this->bd->escape($fila[$i]["id_spool"])."'";
						$this->bd->consulta($sSQL);
					}

					// Fim da transação
					$this->bd->consulta("END");		 		
			}

			/**
			 * Consulta Spool DNS p/ esta mquina
			 */

			 $ad = new AtuadorDNS($this->bd,$this->tpl);

			 $lista_srvd = $ad->obtemListaServidores();

			 if( count($lista_srvd) ) {

					//if ($this->licencaBL != 1) return -1;

				// Início da transação
				$this->bd->consulta("BEGIN");

				// FAZ O SELECT		 		
				$sSQL  = "SELECT ";
				$sSQL .= "   id_spool, op, id_conta, parametros, tipo ";
				$sSQL .= "FROM ";
				$sSQL .= "   sptb_spool ";
				$sSQL .= "WHERE ";
				$sSQL .= "   (tipo = 'N1' OR tipo = 'N2') ";
				$sSQL .= "   AND status = 'A' ";
				$sSQL .= "   AND destino in ('". implode("','",$lista_srvd) ."') ";
				$sSQL .= "FOR UPDATE";

				$fila = $this->bd->obtemRegistros($sSQL);

				$hosp_ns1 = $this->prefs->obtem("geral","hosp_ns1");

				$hosp_ns2 = $this->prefs->obtem("geral","hosp_ns2");
				$hosp_server = $this->prefs->obtem("geral","hosp_server");
				//$parametros_dns = $tipo.",".$hosp_ns1.",".$hosp_ns2;




				for($i=0;$i<count($fila);$i++) {

					$parametros_dns = $fila[$i]["tipo"].",".$hosp_ns1.",".$hosp_ns2.",".$hosp_server;
					$ad->processa($fila[$i]["op"],$fila[$i]["id_conta"],$fila[$i]["parametros"],$parametros_dns);

					$sSQL = "UPDATE sptb_spool SET status = 'OK' WHERE id_spool = '".$this->bd->escape($fila[$i]["id_spool"])."'";
					$this->bd->consulta($sSQL);
				}

				// Fim da transação
				$this->bd->consulta("END");		 		
			}

		}

	}
	
?>