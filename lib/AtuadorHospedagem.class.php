<?
include ("defs.php");
	require_once(PATH_LIB."/Atuador.class.php");
	

  

	class AtuadorHospedagem extends Atuador {
		
		protected $hospedagem_cfg;
		
		// Cache
		protected $servidores;
		protected $listasrv;
		
		// Template
		protected $tpl;
	
		/**
		 * Recebe uma instância do banco de dados
		 */
		public function __construct($tpl=NULL) {
			
			parent::__construct();
			
			$this->tpl = $tpl;
			
			$tcfg = new MConfig(PATH_ETC."/hospedagem.ini");
			$this->hospedagem_cfg = $tcfg->config;
			
			$this->loadCacheInfo();
			
		}
		
		protected function loadCacheInfo() {
			while(list($server,$dados) = each($this->hospedagem_cfg)) {
				$this->servidores[$server] = $dados["enabled"];
				if( $dados["enabled"] ) {
					$this->listasrv[] = $server;
				}
			}
		}
		
		public function obtemListaServidores() {
			return($this->listasrv);
		}
		

		public function processa($op,$id_conta,$parametros,$parametros_hospedagem="") {
		
		if ($op == "a"){
		
			//$parametros = "$tipo_hospedagem,$username,$dominio_hospedagem";
			
			list($tipo_hospedagem,$username,$dominio_hospedagem) = explode (",",$parametros);
			list($hosp_base,$hosp_server) = explode(",",$parametros_hospedagem);
						
			if ($tipo_hospedagem == "D"){
			
				$dir1 = $hosp_base."/".$dominio_hospedagem."/www";
				$dir2 = $hosp_base."/".$dominio_hospedagem."/logs";
				SOFreeBSD::installDir($dir1);
				SOFreeBSD::installDir($dir2);
				
				SOFreeBSD::installDir(PATH_ETC."/hospedagem");
				
				$dominio_gravar = str_replace(".","-",$dominio_hospedagem);
				//$arqtmp = tempnam("/tmp","hconf-");
				$fd = fopen("etc/hospedagem/httpd.".$dominio_gravar.".conf","w");
				
				
				//$hosp_server = $this->prefs->obtem("geral","hosp_server");
				
				$this->tpl->atribui("home",$hosp_base);
				$this->tpl->atribui("dominio_hospedagem",$dominio_hospedagem);
				$this->tpl->atribui("hosp_server",$hosp_server);
				$configura = $this->tpl->obtemPagina("httpd.dominio.conf");
				
				fputs($fd,$configura);
				//copy($arqtmp, "etc/hospedagem/httpd.".$dominio_gravar.".conf");
				fclose($fd);
				
			
			
			}else if ($tipo_hospedagem == "U"){
			
				$dir1 = $hosp_base."/USUARIOS/".$username;
				SOFreeBSD::installDir($dir1);
				
			
			}
						
		}
		
		
		}
		
		
	
	}

?>
