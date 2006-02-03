<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );
// Criacao da classe VACobranca para uso no sistema de cobrança.
class VACobranca extends VirtexAdmin {

	public function VACobranca() {
		parent::VirtexAdmin();
	
	
	}
	
// metodo para pegar as propriedadas enviadas via menu na interface.
	public function processa($op=null) {	
	if($op == "bloqueados"){	
		$this->arquivoTemplate = "cobr_bloqueados.html";
	} else if ($op == "produtos"){
		$this->arquivoTemplate = "produtos.html";
	} else if ($op == "novo"){
		$this->arquivoTemplate = "cadastro_produto.html";
	} else if ($op == "desbloqOk"){
		$this->arquivoTemplate = "desbloqueado_ok.html";
		}
}


}



?>