<?

	// require_once(PATH_LIB."/VirtexAdminWeb.class.php");

	/**
	 * VirtexAdminWeb
	 * Prove as funcionalidades basicas do ambiente web do VirtexAdmin.
	 * As classes web deverao descender de VirtexAdminWeb ao inves de VirtexAdmin agora.
	 * 
	 */
	class VirtexAdminWeb extends VirtexAdmin {
		protected $admLogin;
		protected $prefs;
		protected $preferencias;
		
		/**
		 * Construtor
		 */
		
		public function __construct() {
			parent::__construct();


			/**
			 * Inicia o suporte/tratamento de sessoes
			 */
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
			
			if( @$this->usar_bd && @$this->cfg->config["DB"]["dsn"] ) {
				// Instanciar BD;
				//echo "PREFERENCIAS!!!<br>\n";
				$this->prefs = new Preferencias($this->bd);

				$this->spool = new Spool($this->bd);

				if( isset($_SESSION["admLogin"]) ) {
					$this->admLogin = $_SESSION["admLogin"];
					$this->admLogin->bd = $this->bd;
				} else {
					$this->admLogin = new AdminLogin($this->bd);
					//$_SESSION["admLogin"] = $this->admLogin;
				}



			}
		}
	

		/**
		 * Gerencia o sistema de login de administrador
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
				if( !$this->admLogin->primeiroLogin() && (!$this->lic->isValid() || $this->lic->congelou()) ) {

					$tmp = explode('/',$_SERVER["PHP_SELF"]);
					$arquivoPHP = $tmp[ count($tmp)-1 ];

					// Se a licenca nao for valida nao considera primeiro login (pra nao entrar em loop)
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
		 * log das atividades do administrador
		 */
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
		
		/**
		 * Log do histórico das alterações nas contas
		 */
		public function logConta($cod_operacao, $id_cliente_produto, $tipo_conta, $username, $operacao, $dominio){
				$id_admin = $this->admLogin->obtemId();
				$ip_admin = $_SERVER['REMOTE_ADDR']; 
				$agora = DATE("Y-m-d H:i:s");

				$aSQL  = " INSERT INTO ";
				$aSQL .= " lgtb_status_conta (cod_operacao, id_cliente_produto , data_hora , tipo_conta , username, ip_admin, id_admin, operacao, dominio ) ";
				$aSQL .= " VALUES ('$cod_operacao', '$id_cliente_produto' , now() , '$tipo_conta', '$username' , '$ip_admin' , '$id_admin', '$operacao', '$dominio' ) ";
				$this->bd->consulta($aSQL) ;

				return;
		}



		/**
		 * Gerencia o sistema de login de clientes
		 *
		 * Retorna true se estiver tudo ok
		 * Retorna false se o administrador não estiver logado.
		 * O status retornado indicara se deve ou nao chamar processa.
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

			// Joga a variável pra sessao.
			$_SESSION["usrLogin"] = $this->usrLogin;
			return true;	
		}






	}
	



?>
