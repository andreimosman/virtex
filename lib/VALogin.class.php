<?



/**
 * Tela de login.
 *
 * Trabalha com a variável de sessao $admLogin conforme setado em VirtexAdmin.class.php
 */

class VALogin extends VirtexAdminWeb {

	function __construct() {
		parent::__construct();
	}

	function processa($op="") {

		$lic_geral= 'sim';
		
		$licenca = $this->lic->obtemLicenca();
		if((($licenca["frontend"]["discado"]) == "0")&&(($licenca["frontend"]["hospedagem"]) == "0")&&(($licenca["frontend"]["email"]) == "0")&&(($licenca["frontend"]["bandalarga"]) == "0")
		&&(($licenca["frontend"]["interface"]) == "0")){

			$lic_geral = 'nao';

		}

		$this->tpl->atribui("lic_geral",$lic_geral);


		$this->arquivoTemplate = "jsredir.html";
		$url = "login.php";
		$target = "_self";

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
