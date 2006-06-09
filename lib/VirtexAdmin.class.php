<?

require_once("MWebApp.class.php");
require_once("MDatabase.class.php");
require_once("MBoleto.class.php");
require_once("prefs.class.php");

class VirtexAdmin extends MWebApp {

	protected $bd;
	protected $spool;
	protected $admLogin;

	protected $preferencias;
	protected $prefs;


	public function VirtexAdmin() {
	   parent::MWebApp("etc/virtex.ini",'template/default');

	   @session_start();

	   if( @$this->cfg->config["DB"]["dsn"] ) {
	      // Instanciar BD;

	      $this->bd = new MDatabase($this->cfg->config["DB"]["dsn"]);


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
		   $target = '_self';

		   $this->tpl->atribui('url',$url);
		   $this->tpl->atribui('mensagem',$mensagem);
		   $this->tpl->atribui('target',$target);


		   $this->arquivoTemplate = 'jsredir.html';

		   return false;

		} else if( $veriPrimeiroLogin && $this->admLogin->primeiroLogin() ) {
		   $url = 'administrador.php?op=altera';
		   //$mensagem = 'Tentativa de acesso invalido ao sistema';
		   $target = '_self';

		   // Tela de alteração de senha
		   $this->tpl->atribui('url',$url);
		   //$this->tpl->atribui('mensagem',$mensagem);
		   $this->tpl->atribui('target',$target);

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
		
	
	
}



?>
