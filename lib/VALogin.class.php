<?



/**
 * Tela de login.
 *
 * Trabalha com a variável de sessao $admLogin conforme setado em VirtexAdmin.class.php
 */

class VALogin extends VirtexAdmin {

	function processa($op="") {

		$this->arquivoTemplate = "jsredir.html";
		$url = "login.php";
		$target = "_top";

		$admin = @$_REQUEST["admin"];
		$senha = @$_REQUEST["senha"];

		$erro = "";
		
		$_SESSION["admLogin"] = new AdminLogin();	// Zera a informação de login (faz logout)
		
		if( !$op || $op == "logout") {
			$this->arquivoTemplate = "login.html";
		} else {
			$this->admLogin = $_SESSION["admLogin"];
			$this->admLogin->bd = $this->bd;
			if( $op == "login" ) {
				if( !$admin || !$senha ) {
					$erro = "Entre com o usuário e a respectiva senha";
				}

				if( !$erro ) {
					
					$this->admLogin->login($admin,$senha);

					if( !$this->admLogin->estaLogado() ) {
						$erro = "Usuário inválido ou senha incorreta";
					} else {
						if( $this->admLogin->primeiroLogin() ) {
							$url = "administrador.php?op=altera";
						} else {
							$url = "home.php";
						}
					}
				}
			}
			
			if($erro) $this->tpl->atribui("mensagem",$erro);
			$this->tpl->atribui("url",$url);
			$this->tpl->atribui("target",$target);
			$_SESSION["admLogin"] = $this->admLogin;
			
		}
	}
	
	public function __destruct() {
		parent::__destruct();
	}
	

}



?>
