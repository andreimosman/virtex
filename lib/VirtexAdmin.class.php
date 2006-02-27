<?

require_once("MWebApp.class.php");
require_once("MDatabase.class.php");

class VirtexAdmin extends MWebApp {

	protected $bd;
	protected $admLogin;

	public function VirtexAdmin() {
	   parent::MWebApp("etc/virtex.ini",'template/default');

	   session_start();

	   if( @$this->cfg->config["DB"]["dsn"] ) {
	      // Instanciar BD;

	      $this->bd = new MDatabase($this->cfg->config["DB"]["dsn"]);


	      if( $this->bd->obtemErro() != MDATABASE_OK ) {
	         echo "ERRO: " . $this->bd->obtemMensagemErro() . "<br>\n";
	      } else {

	      }

		   if( isset($_SESSION["admLogin"]) ) {
			  $this->adminLogin = $_SESSION["admLogin"];
			  $this->adminLogin->bd = $this->bd;
		   }

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

		} else if( $this->admLogin->primeiroLogin() ) {
		   $url = 'administrador.php?op=altera';
		   //$mensagem = 'Tentativa de acesso invalido ao sistema';
		   $target = '_top';

		   // Tela de alteração de senha
		   $this->tpl->atribui('url',$url);
		   //$this->tpl->atribui('mensagem',$mensagem);
		   $this->tpl->atribui('target',$target);
		   //echo "Primeiro login";


		   $this->arquivoTemplate = 'jsredir.html';
		   return false;

		}

		// Joga a variável pra sessao.
		$_SESSION["admLogin"] = $this->admLogin;
		return true;

	}


	public function processa($op=null) {
	   // Não faz nada por hora.
	}

/**
	public function exibe($arq="") {
		MWebApp::exibe($arq);
	}
*/

	public function __destruct() {
		if( isset($_SESSION["admLogin"]) ) {
			$_SESSION["admLogin"]->bd = null;
		}
	}

}



?>
