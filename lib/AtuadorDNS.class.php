<?
include ("defs.php");
	require_once(PATH_LIB."/Atuador.class.php");
	

  

	class AtuadorDNS extends Atuador {
		
		protected $dns_cfg;
		
		// Cache
		protected $servidores;
		protected $listasrv;
		protected $tipo;
		
		// Template
		protected $tpl;
	
		/**
		 * Recebe uma instância do banco de dados
		 */
		public function __construct($bd=NULL,$tpl=NULL) {
			
			parent::__construct($bd);
			
			$this->tpl = $tpl;
			
			$tcfg = new MConfig(PATH_ETC."/dns.ini");
			$this->dns_cfg = $tcfg->config;
			
			$this->loadCacheInfo();
			
		}
		
		protected function loadCacheInfo() {
			while(list($server,$dados) = each($this->dns_cfg)) {
				$this->servidores[$server] = $dados["enabled"];
				if( $dados["enabled"] ) {
					$this->listasrv[] = $server;
					$this->tipo = @$dados["type"];
				}
			}
		}
		
		public function obtemListaServidores() {
			return($this->listasrv);
		}
		

		public function processa($op,$id_conta,$parametros,$parametros_dns="") {
		
		if ($op == "a"){
		
			//$parametros = "$tipo_hospedagem,$username,$dominio_hospedagem";
			
			list($tipo_dns,$hosp_ns1,$hosp_ns2,$hosp_server) = explode(",",$parametros_dns);
			
			
			
			if( ($tipo_dns == "N1" && $this->tipo != 1) || ($tipo_dns == "N2" && $this->tipo != 2) ) {
				return(-1);
			}else{
			
				$dominio = $parametros;
				
				SOFreeBSD::installDir(PATH_ETC."/dns");
				//$dominio_gravar = str_replace(".","-",$dominio);
				
				
				$fd = fopen("/virtex/named/etc/namedb/master/".$dominio.".zone","w");
							
				$hoje = date("Ymd");
				$serial = $hoje."01";
				
				$this->tpl->atribui("hosp_ns1",$hosp_ns1);
				$this->tpl->atribui("hosp_ns2",$hosp_ns2);
				
				$this->tpl->atribui("serial",$serial);
				
				$this->tpl->atribui("dominio_hospedagem",$dominio);
				$this->tpl->atribui("hosp_server",$hosp_server);
				$configura = $this->tpl->obtemPagina("named.zone");

				fputs($fd,$configura);
				
				fclose($fd);
			
				$arquivo = "/virtex/named/etc/namedb/named.conf";

				copy("/virtex/named/etc/namedb/named.conf","/virtex/named/etc/namedb/_named.conf");


				$fd = fopen($arquivo,"a+");
				$conteudo = fread($fd, filesize($arquivo));

				//echo $conteudo ."<br>";

				list($inicio,$resto) = explode("//VTX:INIT",$conteudo);
				//list($meio,$final) = explode("//VTX:END",$resto);
				
				$novo1 = 'zone '.'"'.$dominio.'" { ';
				$novo2 = '        type master; ';
				$novo3 = '        file "master/'.$dominio.'.zone"; ';
				$novo4 = '}';
				
				$novo = $novo1."\n".$novo2."\n".$novo3."\n".$novo4."\n";
				
				$meio_b = "//VTX:INIT \n \n".$resto." \n ".$novo;


				$conteudo = $inicio." \n ".$meio_b;


				fputs($fd,$novo);
				
				
				//echo "INICIO\n $inicio \n";
				//echo "MEIO\n $meio_b \n";
				//echo "FINAL\n $final \n";


				fclose($fd);			
			
			
			
			
			}
									
			
			
	

						
		}
		
		
		}
		
		
	
	}

?>
