<?

class AdminLogin {

	public $bd;

	protected $id_admin;
	protected $admin;
	protected $senha;
	protected $nome;
	protected $email;
	protected $privilegios;
	protected $logado;
	protected $primeiroLogin;
	
	
	function __sleep() {
		$this->bd = null;
		return(array("id_admin","admin","senha","nome","email","privilegios","logado","primeiroLogin"));
	}
	
	function __wakeup() {
	
	}

	/**
	 * Constructor
	 * Recebe referencia � inst�ncia do banco de dados.
	 */
	function __construct($bd=NULL) {
		$this->bd = $bd;
		VirtexModelo::init();
		$this->logout();
	}

	/**
	 * Verifica usu�rio e senha
	 */
	function login($admin,$senha) {
		$adtb_admin = VirtexModelo::factory("adtb_admin");		
		$adm = $adtb_admin->obtemUnico( array("admin" => "%:".$admin, "status" => "A") );
		
		// Se encontrou o registro
		if( count($adm) ) {
			if( $adm["senha"] == md5($senha) ) {
				// A senha confere
				
				// Preenche o objeto
				$this->logado 		= 1;
				$this->id_admin 	= $adm["id_admin"];
				$this->nome 		= $adm["nome"];
				$this->email 		= $adm["email"];
				$this->admin 		= strtolower($admin);
				$this->senha 		= $senha;
				$this->primeiroLogin 	= (int)$adm["primeiro_login"];

				if( $this->primeiroLogin ) {
				   // Primeiro login, n�o carrega os privil�gios.
				   $this->privilegios = array();
				} else {
				   // Carrega os privilegios
				   $this->carregaPrivilegios();
				   //$_SESSION["admLogin"] = $this;
				}
				
				return;

			} 
		   
		}
		
		// Zera as vari�veis do objeto.
		$this->logout();

	}

	/**
 	 * Efetua o logout do sistema.
	 */
	function logout() {
		$this->id_admin = 0;
		$this->admin = "";
		$this->senha = "";
		$this->nome = "";
		$this->email = "";
		$this->privilegios = Array();
		$this->logado = 0;

	}

	/**
	 * Verifica se o usu�rio est� logado
	 */
	function estaLogado() {
		return($this->logado);
	}
	
	
	/**
	 * Pega os privil�gios do administrador e joga em $this-privilegios.
	 */
	function carregaPrivilegios() {
		$adtb_usuario_privilegio = VirtexModelo::factory("adtb_usuario_privilegio");
		$this->privilegios = $adtb_usuario_privilegio->obtemPrivilegiosUsuario($this->id_admin);
	}
	
	/**
	 * Obtem a linha referente ao privil�gio solicitado.
	 */
	
	function obtemPrivilegio($cod_priv) {
              for($i=0;$i<count($this->privilegios);$i++) {
                 if( trim($this->privilegios[$i]["cod_priv"]) == trim($cod_priv) ) {
                    return( $this->privilegios[$i]);
                 }
              }
              return null;	
	}
	
	/**
	 * Retorna se o usu�rio tem o privilegio de leitura solicitado
	 */
	function privPodeLer($cod_priv) {
		$prv = $this->obtemPrivilegio(trim($cod_priv));
		if( !$prv ) return false;
		return $prv["id_priv"] ? true : false;
	}

	/**
	 * Retorna se o usu�rio tem o privilegio de grava��o solicitado
	 */
	function privPodeGravar($cod_priv) {
		$prv = $this->obtemPrivilegio(trim($cod_priv));
		if( !$prv ) return false;
		$retorno = (($prv["pode_gravar"] == 't' || $prv["pode_gravar"] == 1 ) ? true : false);
		return( $retorno );
	}
	
	/**
	 * Retorna se � o primeiro login do usu�rio.
	 */
	function primeiroLogin() {
		return($this->primeiroLogin);
	}
	function obtemAdmin(){
		return($this->admin);
		
	}
	
	function obtemNome(){
		return($this->nome);
	
	}

	function obtemId(){
		return($this->id_admin);
	
	}
	

}

?>
