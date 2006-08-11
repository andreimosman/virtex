<?

	/**
	 * VABESpool.class.php
	 *
	 * Sistema do Backend para processamento da spool.
	 *
	 *
	 */
	
	require_once("SOFreeBSD.class.php");
	require_once(PATH_LIB."/VirtexAdminBackend.class.php");
	
	require_once( PATH_LIB . "/ICClient.class.php" );
	require_once( PATH_LIB . "/ICHostInfo.class.php");

	class VABEGrafico extends VirtexAdminBackend {
	
		protected $arquivoLog;
		protected $arquivoMRTG;
		protected $diretorioMRTG;
		
		
		
		protected $username;
	
	
		/**
		 * Construtor
		 */
		public function __construct() {
			// Chama construtor da superclasse
			parent::__construct();
			
			// Inicializa as variáveis
			$this->initVars();
			
			/**
			 * GETOPT SHORT OPTIONS
			 *
			 * -U usuario
			 * 
			 */
			$this->_shortopts = "U:";
			$this->getopt();

		}
		
		protected function initVars() {

			/**********************************************************
			 *                                                        *
			 * CONFIGURAÇÕES GERAIS                                   *
			 *                                                        *
			 **********************************************************/

			$this->arquivoLog 		= PATH_ETC."/stats.log";
			$this->arquivoMRTG 		= PATH_ETC."/mrtg.users.cfg";
			$this->diretorioMRTG	= "/mosman/virtex/dados/estatisticas";
			
			$username 				= "";
		
		}
		
		/**
		 *
		 */
		protected function geraArquivosIndividuais($contas) {
		
			/**
			 * Gera arquivo truncado para todas as contas
			 */
			for($i=0;$i<count($contas);$i++) {
				$arq = $this->diretorioMRTG ."/valog-" . strtolower( trim($contas[$i]["username"]) );
				$fd=fopen($arq,"w");
				if($fd) {
					fputs($fd,"0\n0\n0\n0");
					fclose($fd);
				}
			}
		
		
			$fd = fopen($this->arquivoLog,"r");
			while( ($linha=fgets($fd)) && !feof($fd) ) {
				@list($user,$up,$down) = explode(",",$linha);
				if( $user ) {
					$user = str_replace("/","_",$user);
					$arq = $this->diretorioMRTG ."/valog-" . strtolower(trim($user));
					$fc = fopen($arq,"w");
					if($fc) {
						$info = ((int)$down) . "\n". ((int)$up) . "\n". "0\n0";
						fputs($fc,$info);
						fclose($fc);
					}
				}
			}
			
		}
		
		public function executa() {
			parent::executa();	
			
			/**********************************************************
			 *                                                        *
			 * CONFIGURAÇÃO DE EXECUÇÃO                               *
			 *                                                        *
			 **********************************************************/


			for($i=0;$i<count($this->options);$i++) {
			
				switch($this->options[$i][0]) {
					case 'U':
						$this->username = $this->options[$i][1];
						break;
				
				}
			
			}
			
			/**
			 * Verificar as estatísticas no arquivo
			 */
			if( $this->username ) {
				$fd = fopen($this->arquivoLog,"r");
				
				if(!$fd) {
					echo "0\n0\n0\n0";
					return(0);
				}
				
				/**
				 * Varre o arquivo
				 */
				while( ($linha=fgets($fd)) && !feof($fd) ) {
					$linha = trim($linha);
					if( $linha ) {
						@list($user,$up,$down) = explode(",",$linha);
						if( trim($user) == trim($this->username) ) {
							echo ((int)$down) . "\n";
							echo ((int)$up) . "\n";
							echo "0\n0";
							return(0);
						}
					}
				
				}
			
				echo "0\n0\n0\n0";
				return(0);
			
			}



			
			/**********************************************************
			 *                                                        *
			 * COLETAR AS ESTATÍSTICAS DE TODOS OS HOSTS CONFIGURADOS *
			 *                                                        *
			 **********************************************************/
			
			$ich = new ICHostInfo();
			$icc = new ICClient();

			$hosts = $ich->obtemListaServidores();

			$arp = array();
			
			
			$arqtmp = tempnam( "/tmp" , "vastat-" );
			
			$fh = fopen($arqtmp,"w");
			if(!$fh) die("Cannot open temp file '$arqtmp' for writting\n");
			
			for($i=0;$i<count($hosts);$i++) {
				$info = $ich->obtemInfoServidor($hosts[$i]);

				if(!$icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {
					continue;
				}

				// $arp[] = array("host"=>$hosts[$i], "tabela" => $icc->getARP($ip) );
				
				fwrite($fh,$icc->getStats());

			}
			
			fclose($fh);
			
			// Copia pro arquivo utilizado pelo sistema para gerar as estatísticas
			copy($arqtmp,$this->arquivoLog);
			
			unlink($arqtmp);
			
			/**********************************************************
			 *                                                        *
			 * VARRER O BANCO DE DADOS E GERAR OS ARQUIVOS DO MRTG    *
			 *                                                        *
			 **********************************************************/
			
			$sSQL  = "SELECT ";
			$sSQL .= "   c.username,cbl.upload_kbps,cbl.download_kbps ";
			$sSQL .= "FROM ";
			$sSQL .= "   cntb_conta c INNER JOIN cntb_conta_bandalarga cbl USING(username,dominio,tipo_conta) ";
			//$sSQL .= "WHERE ";
			//$sSQL .= "   c.status = 'A' ";
			
			// echo $sSQL . "\n\n";
			
			/**
			$sSQL .= "";
			$sSQL .= "";
			
			*/
			
			$contas = $this->bd->obtemRegistros($sSQL);
			
			for($i=0;$i<count($contas);$i++) {
				$contas[$i]["maxbytes"] = ($contas[$i]["download_kbps"]/8)*1000;
				$contas[$i]["username"] = strtolower(trim($contas[$i]["username"]));
			}
			
			
			// Criar diretório alvo para geração dos rrds
			SOFreeBSD::installDir($this->diretorioMRTG);
			
			// Gera as informações
			$this->geraArquivosIndividuais($contas);

			
			$fd = fopen($this->arquivoMRTG,"w");
			
			if( !$fd ) die("Cannot write '".$this->arquivoMRTG."'");
			
			
			// Passar para o smarty para gerar o arquivo do MRTG
			$this->tpl->atribui("workdir",$this->diretorioMRTG);
			$this->tpl->atribui("contas",$contas);
			fwrite($fd,$this->tpl->obtemPagina("mrtg.users.conf"));
			fclose($fd);
			
			
			// Executa o MRTG
			SOFreeBSD::executa("/usr/local/bin/mrtg " . $this->arquivoMRTG);
			
			
			
		}
		
	}
?>
