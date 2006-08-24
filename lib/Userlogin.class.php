<?

require_once("MDatabase.class.php");
require_once("VirtexAdmin.class.php");

class UserLogin extends VirtexAdmin {

	public $bd;

	protected $id_conta;
	protected $id_cliente;
	protected $username;
	protected $senha;
	protected $nome;
	protected $email;
	protected $privilegios;
	protected $logado;
	protected $contamestre;
	protected $conta;
	protected $dominio;
	
	
	function __sleep() {
		$this->bd = null;
		return(array("id_conta","id_cliente","username","senha","nome","email","privilegios","logado","contamestre"));
	}
	
	function __wakeup() {
	
	}

	/**
	 * Constructor
	 * Recebe referencia à instância do banco de dados.
	 */
	function VirtexAdminLogin($bd) {
		$this->bd = $bd;
		$this->logout();
	}

	/**
	 * Verifica usuário e senha
	 */
	function login($username,$senha,$conta,$dominio) {
		// Pega apenas os usuários vazios
		$sSQL = "SELECT ";
		$sSQL .= "   cc.id_conta, cc.dominio, cc.username, cc.senha, cc.senha_cript, clc.nome_razao, cc.status , cc.conta_mestre, clc.id_cliente as id ";
		//$sSQL .= "   CASE WHEN primeiro_login is true THEN 1 ELSE 0 END as primeiro_login ";
		$sSQL .= "FROM ";
		$sSQL .= "   cntb_conta cc , cltb_cliente clc ";
		$sSQL .= "WHERE ";
		$sSQL .= "   cc.username ilike '".$username."' ";
		$sSQL .= "   AND cc.status = 'A'";
		$sSQL .= "	 AND clc.id_cliente = cc.id_cliente ";
		$sSQL .= "	 AND cc.tipo_conta = '".$conta."' ";

		if ($conta == "E"){
		
			$sSQL .="AND dominio = '".$dominio."' ";
		
		}
		
		$this->bd->consulta($sSQL);
		$adm = $this->bd->obtemUnicoRegistro($sSQL);

		$salt = substr(@$adm['senha_cript'],0,12);
		$senhad_cript = crypt($senha,$salt);

		// Se encontrou o registro
		if( count($adm) ) {
			if( $senhad_cript == $adm["senha_cript"] ){
				// A senha confere
				
				// Preenche o objeto
				$this->logado 		= 1;
				$this->id_conta 	= $adm["id_conta"];
				$this->id_cliente	= $adm["id"];
				$this->nome 		= $adm["nome_razao"];
//				$this->email 		= $adm["email"];
				$this->admin 		= strtolower($username);
				$this->senha 		= $senha;
				$this->contamestre 	= (int)$adm["conta_mestre"];

				/*	
				if( $this->primeiroLogin ) {
				   // Primeiro login, não carrega os privilégios.
				   $this->privilegios = array();
				} else {
			   // Carrega os privilegios
				   $this->carregaPrivilegios();
				   $_SESSION["admLogin"] = $this;
				}*/
				
				return;

			} 
		   
		}
		
		// Zera as variáveis do objeto.
		$this->logout();

	}

	/**
 	 * Efetua o logout do sistema.
	 */
	function logout() {
		$this->id_conta = 0;
		$this->username = "";
		$this->senha = "";
		$this->nome = "";
		$this->email = "";
		$this->contamestre = "" ;
		$this->logado = 0;

	}

	/**
	 * Verifica se o usuário está logado
	 */
	function estaLogado() {
		return($this->logado);

	}
	
	
	/**
	 * Pega os privilégios do administrador e joga em $this-privilegios.
	 
	function carregaPrivilegios() {
		$sSQL  = "SELECT ";
		$sSQL .= "   p.id_priv, p.cod_priv, p.nome, CASE WHEN up.pode_gravar THEN 1 ELSE 0 END as pode_gravar ";
		$sSQL .= "FROM ";
		$sSQL .= "   adtb_usuario_privilegio up, adtb_privilegio p ";
		$sSQL .= "WHERE ";
		$sSQL .= "   p.id_priv = up.id_priv ";
		$sSQL .= "   AND up.id_admin = ".$this->id_admin . " ";

		$this->privilegios = $this->bd->obtemRegistros($sSQL);
	}
	
	Obtem a linha referente ao privilégio solicitado.
	
	
	function obtemPrivilegio($cod_priv) {
              for($i=0;$i<count($this->privilegios);$i++) {
                 if( trim($this->privilegios[$i]["cod_priv"]) == $cod_priv ) {
                    return( $this->privilegios[$i]);
                 }
              }
              return null;	
	}
	
	
	Retorna se o usuário tem o privilegio de leitura solicitado
	 
	function privPodeLer($cod_priv) {
		$prv = $this->obtemPrivilegio($cod_priv);
		if( !$prv ) return false;
		return $prv["id_priv"] ? true : false;
	}

	
	Retorna se o usuário tem o privilegio de gravação solicitado
	 
	function privPodeGravar($cod_priv) {
		$prv = $this->obtemPrivilegio($cod_priv);
		if( !$prv ) return false;
		return( $prv["pode_gravar"] );
	}*/
	

	 
	function obtemConta() {
		return($this->contamestre);
	}
	
	
	function obtemUser(){
		return($this->username);
		
	}
	
	function obtemNome(){
		return($this->nome);
	
	}

	function obtemId(){
		return($this->id_conta);
	
	}
	function obtemIdCliente(){
			return($this->id_cliente);
		
	}
	

}

?>
