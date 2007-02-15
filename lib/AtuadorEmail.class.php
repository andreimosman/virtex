<?
include ("defs.php");
	require_once(PATH_LIB."/Atuador.class.php");

  

	class AtuadorEmail extends Atuador {
		
		protected $email_cfg;
		
		// Cache
		protected $servidores;
		protected $listasrv;
	
		/**
		 * Recebe uma instância do banco de dados
		 */
		public function __construct() {
			
			parent::__construct();
			
			$tcfg = new MConfig(PATH_ETC."/email.ini");
			$this->email_cfg = $tcfg->config;
			
			$this->loadCacheInfo();
			
		}
		
		protected function loadCacheInfo() {
			while(list($server,$dados) = each($this->email_cfg)) {
				$this->servidores[$server] = $dados["enabled"];
				if( $dados["enabled"] ) {
					$this->listasrv[] = $server;
				}
			}
		}
		
		public function obtemListaServidores() {
			return($this->listasrv);
		}
		

		public function processa($op,$id_conta,$parametros,$parametros_email="") {
		
		if ($op == "a"){
			list($username,$dominio) = explode("@",$parametros);

			$email_base = $parametros_email;
			$dir1 = $email_base."/".$dominio."/".$username."/Maildir/cur";
			$dir2 = $email_base."/".$dominio."/".$username."/Maildir/new";
			$dir3 = $email_base."/".$dominio."/".$username."/Maildir/tmp";

			SOFreeBSD::installDir($dir1);
			SOFreeBSD::installDir($dir2);
			SOFreeBSD::installDir($dir3);
			
			$path = $email_base."/".$dominio."/".$username."/Maildir";
			$comando  = "/usr/sbin/chown -R nobody ".$path;
			$comando2 = "/usr/sbin/chgrp -R nobody ".$path;
			
			SistemaOperacional::executa($comando);
			SistemaOperacional::executa($comando2);
			
			
			
		}
		
		
		}
		
		
	
	}

/**
	$t = new AtuadorEmail() ;
	
	$t->processa("a",0,"mosman@mosman.com.br","/mosman/virtex/dados/emails");
	
	$srv = $t->obtemListaServidores();
	
	for($i=0;$i<count($srv);$i++) {
		echo $srv[$i] . "\n";
	}
*/
?>
