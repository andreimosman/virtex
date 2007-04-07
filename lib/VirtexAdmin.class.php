<?

/**
 * Base do Aplicativo
 */
class VirtexAdmin extends MWebApp {

	protected $bd;
	protected $usar_bd;
	
	protected $spool;

	protected $lic;
	protected $usar_lic;
	
	
	
	protected $debug;
	
	public function VirtexAdmin($ini="etc/virtex.ini",$tpldir="template/default",$usar_bd=true,$usar_lic=true) {
		parent::__construct($ini,$tpldir);

		// Inicializa o sistema de persistencia
		VirtexModelo::init();

		if($usar_lic) {
			$this->lic = new VirtexAdminLicenca();
			$this->tpl->atribui("_licenca",$this->lic->obtemLicenca());
		} else {
			$this->lic = null;
		}
		$this->usar_lic = $usar_lic;
		
		$this->debug = (int)@$this->cfg->config["geral"]["debug"];
		$this->usar_bd = $usar_bd;
		
		$this->prefs = null;
		
		if( @$usar_bd && @$this->cfg->config["DB"]["dsn"] ) {
			// Instanciar BD;
			
			$this->bd = MDatabase::getInstance($this->cfg->config["DB"]["dsn"],$this->debug);

			if( $this->bd->obtemErro() != MDATABASE_OK ) {
				echo "ERRO: " . $this->bd->obtemMensagemErro() . "<br>\n";
			} else {

			}
			
		}
		
		$this->tpl->atribui("op",@$_REQUEST["op"]);
		
	}

	public function __destruct() {
		if( isset($_SESSION["admLogin"]) ) {
			$_SESSION["admLogin"]->bd = null;
		}
	}




	public function processa($op=null) {
		// Não faz nada por hora.
	}
	
	
	
	public function criptSenha($senha) {
		$sal = '$1$';
		for($i=0;$i<8;$i++) {
			$j = mt_rand(0,53);
			if($j<26)
				$sal .= chr(rand(65,90));
			  else if($j<52)
			  	$sal .= chr(rand(97,122));
			  else if($j<53)
			  	$sal .= '.';
				else
		 		$sal .= '/';
		 }
		$sal .= '$';
		return( crypt($senha,$sal) );
	}	
	
	public function licenca($tipo,$modulo){
		$lic = $this->lic->obtemLicenca();
		$licenca = (int)@$lic[$tipo][$modulo];
		return($licenca);
	}
	
	 			

}
			
	


?>
