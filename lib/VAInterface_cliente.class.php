<?

/**
 * Tela de login.
 *
 * Trabalha com a variável de sessao $admLogin conforme setado em VirtexAdmin.class.php
 */

class VAInterface_cliente extends VirtexAdminWeb {
	
	public function __construct() {
		parent::__construct();
	}


	function processa($op="") {

			$lic_interface = 'nao';
			$lic_email = 'nao';
			$lic_hospedagem = 'nao';
			$lic_interface = 'nao';
			$lic_discado = 'nao';
			$lic_bandalarga = 'nao';
	
			 $licenca = $this->lic->obtemLicenca();
	
				if(($licenca["frontend"]["interface"]) == "1"){
	
					$lic_interface = 'sim';
	
				}
				
				if(($licenca["frontend"]["discado"]) == "1"){

					$lic_discado = 'sim';

				}
				if(($licenca["frontend"]["banda_larga"]) == "1"){

					$lic_bandalarga = 'sim';

				}
				if(($licenca["frontend"]["email"]) == "1"){

					$lic_email = 'sim';
				}
				if(($licenca["frontend"]["hospedagem"]) == "1"){

					$lic_hospedagem = 'sim';

				}
				if(($licenca["frontend"]["interface"]) == "1"){

					$lic_interface = 'sim';

				}
				
				


		$this->tpl->atribui("lic_discado",$lic_discado);
		$this->tpl->atribui("lic_email",$lic_email);
		$this->tpl->atribui("lic_hospedagem",$lic_hospedagem);
		$this->tpl->atribui("lic_email",$lic_email);
		$this->tpl->atribui("lic_interface",$lic_interface);
		$this->tpl->atribui("lic_bandalarga",$lic_bandalarga);
		$this->tpl->atribui("lic_interface",$lic_interface);

		$this->arquivoTemplate = "jsredir.html";
		$url = "index.php";
		$target = "_self";

		$admin = @$_REQUEST["admin"];
		$senha = @$_REQUEST["senha"];
		$conta = @$_REQUEST["tipo_conta_log"];
		
		
			$admin_username = explode("@",$admin);

			$admin = $admin_username[0];

		if ($conta == "E"){
		
		
		$dominio = $admin_username[1];

		}

		$erro = "";
		
		$_SESSION["usrLogin"] = new UserLogin();	// Zera a informação de login (faz logout)
		
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
					
					$this->usrLogin->login($admin,$senha,$conta,@$dominio);

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
