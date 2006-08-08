<?

require_once("VirtexAdmin.class.php");
require_once("Userlogin.class.php");

/**
 * Tela de login.
 *
 * Trabalha com a variável de sessao $admLogin conforme setado em VirtexAdmin.class.php
 */

class VAInterface_cliente extends VirtexAdmin {

	function processa($op="") {

		$this->arquivoTemplate = "jsredir.html";
		$url = "index.php";
		$target = "_self";

		$admin = @$_REQUEST["admin"];
		$senha = @$_REQUEST["senha"];
		$conta = @$_REQUEST["tipo_conta_log"];

		$erro = "";
		
		$_SESSION["usrLogin"] = new userLogin();	// Zera a informação de login (faz logout)
		
		if( !$op || $op == "logout") {
			$this->arquivoTemplate = "interface_login.html";
		} else {
			$this->usrLogin = $_SESSION["usrLogin"];
			$this->usrLogin->bd = $this->bd;
			if( $op == "login" ) {
				if( !$admin || !$senha ) {
					$erro = "Entre com o usuário e a respectiva senha";
				}

				if( !$erro ) {
					
					$this->usrLogin->login($admin,$senha,$conta);

					if( !$this->usrLogin->estaLogado() ) {
						$erro = "Usuário inválido ou senha incorreta";
					} else {
							$url = "index_home.php";

						}
					}
				}
			}
			
			if($erro) $this->tpl->atribui("mensagem",$erro);
			$this->tpl->atribui("url",$url);
			$this->tpl->atribui("target",$target);
			$_SESSION["usrLogin"] = @$this->usrLogin;
			
		}

	
	
	
	
	
	
	public function __destruct() {
		parent::__destruct();
	}
	
 }
 
 



?>