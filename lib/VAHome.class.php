<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAHome extends VirtexAdmin {

	public function VAHome() {
		parent::VirtexAdmin();
	
		$adm = $this->admLogin->obtemAdmin();
		$this->tpl->atribui("admin",$adm);	



		$this->arquivoTemplate = "home.html";
	
	
	}
	
	
	public function processa($op=null) {
	
		if( $op == "home" ) {
		   
				$licenca = $this->lic->obtemLicenca();
				$hoje = Date("Y-m-d");
				//echo $hoje;
				if($licenca["geral"]["expira_em"] < $hoje && $licenca["geral"]["congela_em"] > $hoje){
					$status = "expirado";
				}else if ($licenca["geral"]["congela_em"] < $hoje ){
					$status = "congelado";
				}else if ($licenca["geral"]["congela_em"] > $hoje && $licenca["geral"]["expira_em"] > $hoje){
					$status = "ativo";
				}
		   
		   $this->tpl->atribui("status",$status);
		   $this->tpl->atribui("licenca",$licenca);

		   
		   $this->arquivoTemplate = "home_principal.html";
		   
		}
	
	
	
	
	}

public function __destruct() {
      	parent::__destruct();
}

}



?>
