<?

/**
 * Definir o caminho de pesquisa
 */

require_once("MWebApp.class.php");
require_once("MDatabase.class.php");
require_once("MBoleto.class.php");
require_once("prefs.class.php");
require_once("VirtexAdminLicenca.class.php");


/**
 * Base do Aplicativo
 */
class VirtexAdmin extends MWebApp {

	protected $bd;
	protected $spool;
	protected $admLogin;

	protected $preferencias;
	protected $prefs;

	protected $lic;
	
	protected $debug;
	
	public function VirtexAdmin($ini="etc/virtex.ini",$tpldir="template/default",$usar_bd=true) {
		parent::MWebApp($ini,$tpldir);
		
		$this->lic = new VirtexAdminLicenca();
		$this->debug = (int)@$this->cfg->config["geral"]["debug"];
		
		@session_start();

		/**
		 *  Se o sistema só aceita https
		 */
		 
		if( @$this->cfg->config["geral"]["https_only"] ) {
			// Verificar se o HTTPs tá ok.
			if(@$_SERVER["REQUEST_METHOD"] && !@$_SERVER["SSL_PROTOCOL"]) {

				$url = "https://".$_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];
				$this->tpl->atribui("url",$url);

				$this->tpl->exibe("https_only.html");
				exit;
			}
		}

		if( @$usar_bd && @$this->cfg->config["DB"]["dsn"] ) {
			// Instanciar BD;
			
			$this->bd = new MDatabase($this->cfg->config["DB"]["dsn"],$this->debug);

			if( $this->bd->obtemErro() != MDATABASE_OK ) {
				echo "ERRO: " . $this->bd->obtemMensagemErro() . "<br>\n";
			} else {

			}
			
			$this->spool = new Spool($this->bd);

			if( isset($_SESSION["admLogin"]) ) {
				$this->admLogin = $_SESSION["admLogin"];
				$this->admLogin->bd = $this->bd;
			} else {
				$this->admLogin = new AdminLogin($this->bd);
				//$_SESSION["admLogin"] = $this->admLogin;
			}
			
			//$this->obtemPreferencias();
			$this->prefs = new Preferencias($this->bd);

		}
		
	}

	public function __destruct() {
		if( isset($_SESSION["admLogin"]) ) {
			$_SESSION["admLogin"]->bd = null;
		}
	}

	/**
	 * Gerencia o sistema de login
	 *
	 * Retorna true se estiver tudo ok
	 * Retorna false se o administrador não estiver logado.
	 * O status retornado indicara se deve ou nao chamar processa.
	 */
	public function adminLogin() {
		 $op = @$_REQUEST["op"];

		if( !isset($_SESSION["admLogin"]) || $op == "logout" ) {
			// Se a variavel de sessao admLogin ainda não foi setada ou o
			// cara está fazendo um logout. Zera a sessao.
			$this->admLogin = new AdminLogin($this->bd);
		} else {
			// Pega da sessao.
			$this->admLogin = @$_SESSION["admLogin"];
			$this->admLogin->bd = $this->bd;
		}
		

		$op = @$_REQUEST["op"];
		$tmp = explode("/",$_SERVER["PHP_SELF"]);
		$arquivoPHP = $tmp[ count($tmp)-1 ];
		$veriPrimeiroLogin = !($arquivoPHP == "administrador.php" && $op == "altera");
		
		if( !$this->admLogin->estaLogado() ) {
			// Redireciona pra tela de login
			$url = 'login.php';
			$mensagem = 'Tentativa de acesso invalido ao sistema';
			$target = '_top';

			$this->tpl->atribui('url',$url);
			$this->tpl->atribui('mensagem',$mensagem);
			$this->tpl->atribui('target',$target);


			$this->arquivoTemplate = 'jsredir.html';

			return false;

		} else {
			/***************************************
			 *                                     *
			 * VERIFICACAO DA LICENCA              *
			 *                                     *
			 ***************************************/



			/**
			 * Se a licença não for valida ou congelou
			 */
			if( !$this->lic->isValid() || $this->lic->congelou() ) {
			
				$tmp = explode('/',$_SERVER["PHP_SELF"]);
				$arquivoPHP = $tmp[ count($tmp)-1 ];
				
				if( $arquivoPHP != "configuracao.php" || @$_REQUEST["op"] != "registro" ) {
					// Joga pra tela que o cara têm que atualizar a licença.
					$url      = 'configuracao.php?op=registro&x=fullscreen';
					$mensagem = $this->lic->isValid() ? "Terminado o tempo para atualizacao da licenca" : "Licença inválida";
					$target   = '_top';

					$this->tpl->atribui('url',$url);
					$this->tpl->atribui('mensagem',$mensagem);
					$this->tpl->atribui('target',$target);
					$this->arquivoTemplate = 'jsredir.html';
					return false;
				}
				
			}

			if( $this->lic->expirou() && !$this->lic->congelou() ) {
				// Exibir mensagem de que o sistema expirou e irá congelar dia X
				
				$licenca = $this->lic->obtemLicenca();
				$this->tpl->atribui("expirou",TRUE);
				$this->tpl->atribui("licenca",$licenca);
				
			}

		
			if( $veriPrimeiroLogin && $this->admLogin->primeiroLogin() ) {
				$url = 'administrador.php?op=altera';
				//$mensagem = 'Tentativa de acesso invalido ao sistema';
				$target = '_top';

				// Tela de alteração de senha
				$this->tpl->atribui('url',$url);
				//$this->tpl->atribui('mensagem',$mensagem);
				$this->tpl->atribui('target',$target);

				$this->arquivoTemplate = 'jsredir.html';
				return false;
			}

			
			

		}

		// Joga a variável pra sessao.
		$_SESSION["admLogin"] = $this->admLogin;
		return true;

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
	
	public function checa_preferencia(){
	
		$preferencias = $this->prefs->obtem("total");
		
		$obrigatorio_geral = false;
		$obrigatorio_email = false;
		$obrigatorio_cobranca = false;
		
		// campos completamente obrigatórios
		$dominio_padrao = $preferencias["dominio_padrao"];
		$nome = $preferencias["nome"];
		
		// campos obrigatorios para uso de e-mail
		$radius_server = $preferencias["radius_server"];
		$mail_server = $preferencias["mail_server"];
		$mail_uid = $preferencias["mail_uid"];
		$mail_gid = $preferencias["mail_gid"];
		$pop_host = $preferencias["pop_host"];
		$smtp_host = $preferencias["smtp_host"];
		
		// campos obrigatorios para cobranca
		$endereco = $preferencias["endereco"];
		$cep = $preferencias["cep"];
		$cnpj = $preferencias["cnpj"];
		$localidade = $preferencias["localidade"];
		$tx_juros = $preferencias["tx_juros"];
		$multa = $preferencias["multa"];
		$dia_venc = $preferencias["dia_venc"];
		$carencia = $preferencias["carencia"];
		$cod_banco = $preferencias["cod_banco"];
		$carteira = $preferencias["carteira"];
		$agencia = $preferencias["agencia"];
		$num_conta = $preferencias["num_conta"];
		$convenio = $preferencias["convenio"];
		$pagamento = $preferencias["pagamento"];
		$path_contrato = $preferencias["path_contrato"];
		
		
		
		if ($dominio_padrao == "" || !$dominio_padrao || !$nome || $nome == ""){
			$this->tpl->atribui("obrigatorio_geral",true);
		}
		
		if ($radius_server == "" || $mail_server == "" || $mail_uid == "" || $mail_gid == "" || $pop_host == "" || $smtp_host == ""){
			$this->tpl->atribui("obrigatorio_email",true);
		
		}
		
		if ($endereco == "" || $cep == "" || $cnpj == "" || $localidade == "" || $tx_juros == "" || $multa == "" || $dia_venc == "" || $carencia == "" || $cod_banco == "" || $carteira == "" || $agencia == "" || $num_conta == "" || $convenio == "" || $pagamento == "" || $path_contrato == ""){;
			$this->tpl->atribui("obrigatorio_cobranca",true);
		
		}
		
		return;
	}
			/**
			 * Privilégios
			 */
			public function obtemPrivilegio($cod_priv) {
				return $this->admLogin->obtemPrivilegio($cod_priv);
			}
		
			public function privPodeLer($cod_priv) {
				return $this->admLogin->privPodeLer($cod_priv);
			}
		
			public function privPodeGravar($cod_priv) {
				return $this->admLogin->privPodeGravar($cod_priv);
			}
			
			public function privMSG($mensagem="Você não possui privilégio para executar esta operação.",$url="home.php?op=home",$target="_top") {
				$this->tpl->atribui("mensagem",$mensagem);
				$this->tpl->atribui("url",$url);
				$this->tpl->atribui("target",$target);
				$this->arquivoTemplate="msgredirect.html";
			}



			public function licProib($mensagem="<br>Você não está habilitado a visualizar esse módulo.<br>Em caso de dúvida, entre com contato com Mosman Consultoria & Desenvolvimento.<br>www.mosman.com.br<br>consultoria@mosman.com.br ", $target="_top") {
				$this->tpl->atribui("mensagem",$mensagem);
				$this->tpl->atribui("url","javascript:history.back();");
				$this->tpl->atribui("target",$target);
				$this->arquivoTemplate="msgredirect.html";

			}

		/** 
    	 *  Interface cliente
		 */
	public function UserLogin() {
		 $op = @$_REQUEST["op"];

		if( !isset($_SESSION["usrLogin"]) || $op == "logout" ) {
			// Se a variavel de sessao ainda não foi setada ou o
			// cara está fazendo um logout. Zera a sessao.
			$this->usrLogin = new UserLogin($this->bd);
		} else {
			// Pega da sessao.
			$this->usrLogin = @$_SESSION["usrLogin"];
			$this->usrLogin->bd = $this->bd;
		}
		

		$op = @$_REQUEST["op"];
		$tmp = explode("/",$_SERVER["PHP_SELF"]);
		$arquivoPHP = $tmp[ count($tmp)-1 ];
		$veriPrimeiroLogin = !($arquivoPHP == "administrador.php" && $op == "altera");
		
		if( !$this->usrLogin->estaLogado() ) {
			// Redireciona pra tela de login
			$url = 'index.php';
			$mensagem = 'Tentativa de acesso invalido ao sistema';
			$target = '_top';

			$this->tpl->atribui('url',$url);
			$this->tpl->atribui('mensagem',$mensagem);
			$this->tpl->atribui('target',$target);


			$this->arquivoTemplate = 'jsredir.html';

			return false;

		}
		
/*					if( $veriPrimeiroLogin && $this->usrLogin->primeiroLogin() ) {
						$url = 'administrador.php?op=altera';
						//$mensagem = 'Tentativa de acesso invalido ao sistema';
						$target = '_top';
		
						// Tela de alteração de senha
						$this->tpl->atribui('url',$url);
						//$this->tpl->atribui('mensagem',$mensagem);
						$this->tpl->atribui('target',$target);
		
						$this->arquivoTemplate = 'jsredir.html';
						return false;
					}*/
					
				// Joga a variável pra sessao.
				$_SESSION["usrLogin"] = $this->usrLogin;
				return true;	
	}
	
	public function logAdm($operacao,$valor_original,$valor_alterado,$username,$id_cliente_produto,$tipo_conta,$extra){
	
	
			$id_admin = $this->admLogin->obtemId();
			$ip_admin = $_SERVER['REMOTE_ADDR']; 
			$agora = DATE("Y-m-d H:i:s");
	
			$sSQL  = "INSERT INTO lgtb_administradores  ";
			$sSQL .= "(id_admin,data,operacao,valor_original,valor_alterado,username,id_cliente_produto,tipo_conta,extras,ip) ";
			$sSQL .= " VALUES ";
			$sSQL .= "( $id_admin, '$agora', '$operacao', '$valor_original', '$valor_alterado', '$username', $id_cliente_produto, '$tipo_conta', '$extra', '$ip_admin' )  ";
			
			//echo "LOG: $sSQL <br>";
			$this->bd->consulta($sSQL);
			
			return;
	
	
	
	}
	
	public function licenca($tipo,$modulo){
		$lic = $this->lic->obtemLicenca();
		$licenca = (int)@$lic[$tipo][$modulo];
		return($licenca);
	
	
	}
	
	 			

}
			
	


?>
