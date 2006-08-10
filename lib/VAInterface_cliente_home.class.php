<?

require_once( PATH_LIB . "/Userlogin.class.php");
require_once( PATH_LIB . "/VirtexAdmin.class.php" );


class VAInterface_cliente_home extends VirtexAdmin {
		

	function processa($op="") {
	parent::VirtexAdmin();
	
	$acao = @$_REQUEST["acao"];
	
	$user = $this->usrLogin->obtemUser();
	$id_conta = $this->usrLogin->obtemId();
	$conta_mestre = $this->usrLogin->obtemConta();
	$id_cliente = $this->usrLogin->obtemIdCliente();	

	$this->tpl->atribui("conta_mestre",$conta_mestre);
	
	$sSQL  = " SELECT ";
	$sSQL .= " cl.nome_razao, cc.conta_mestre, cc.status, cc.tipo_conta, cc.id_cliente, cc.username, cc.id_conta, cc.dominio " ;
	$sSQL .= " FROM cntb_conta cc, cltb_cliente cl ";
	$sSQL .= " WHERE status = 'A' ";
	$sSQL .= " AND cc.id_cliente = cl.id_cliente ";
	$sSQL .= " AND id_conta = '$id_conta' ";
	
 	$this->bd->consulta($sSQL);

	$conta = $this->bd->obtemUnicoRegistro($sSQL);
	


	$username = $conta['username'];
	$conta_mestre = $conta['conta_mestre'];
	$dominio = $conta['dominio'];
	$tipo_conta = $conta['tipo_conta'];


	$this->tpl->atribui("tipo_conta",$tipo_conta);
	$this->tpl->atribui("conta_mestre",$conta_mestre);
	$this->tpl->atribui("username",$username);
	$this->tpl->atribui("username_conf",$username);
	$this->tpl->atribui("dominio",$dominio);	
	$this->tpl->atribui("id_conta",$id_conta);
	$this->tpl->atribui("conta",$conta);
	
	$this->arquivoTemplate = "interface_home.html";
	
	if ($op == "dados"){
	
	$dados = "true";	
	
	$id_conta2 = @$_REQUEST["id_conta"];
	$tipo_conta2 = @$_REQUEST["tipo_conta"];
	
	 if ($id_conta2){
	
		$sSQL  = " SELECT ";
		$sSQL .= " cl.nome_razao, cc.conta_mestre, cc.status, cc.tipo_conta, cc.id_cliente, cc.username, cc.id_conta, cc.dominio " ;
		$sSQL .= " FROM cntb_conta cc, cltb_cliente cl ";
		$sSQL .= " WHERE status = 'A' ";
		$sSQL .= " AND cc.id_cliente = cl.id_cliente ";
		$sSQL .= " AND cc.id_conta = '$id_conta2' ";
		$sSQL .= " AND cc.tipo_conta = '$tipo_conta2' ";

		
		$dados = $this->bd->obtemUnicoRegistro($sSQL);
		$tipo_conta = @$dados["tipo_conta"];
		$username = @$dados["username"];
		$dominio = @$dados["dominio"];
		
		$this->tpl->atribui("tipo_conta",$tipo_conta);
		$this->tpl->atribui("username",$username);
		$this->tpl->atribui("dominio",$dominio);	
	
	}
	
	$this->tpl->atribui("dados",$dados);

	$this->arquivoTemplate = "interface_home.html";
	
	
	}
		
	if ($op == "home"){
		
		$this->arquivoTemplate = "interface_home.html";

		}
		
		if ($op == "alterar_senha"){
		
		$id_conta2 = @$_REQUEST["id_conta"];
		
		if (!$acao && !$id_conta2){
		
		
		$username = $_REQUEST["username"];
		$dominio = @$_REQUEST ["dominio"];
		$tipo_conta = @$_REQUEST ["tipo_conta"] ;
		
		}
		
		
		
		$alterar_senhas = "true";	
		
		if ($acao == "alterar" ){

		
			$sSQL  = " UPDATE ";
			$sSQL .= " cntb_conta ";
			$sSQL .= " SET ";
			$sSQL .= " senha_cript = '" . $this->criptSenha($this->bd->escape(@$_REQUEST["senha"]))  . "', ";
			$sSQL .= " senha = '" . $this->bd->escape(@$_REQUEST["senha"])  . "' ";
			$sSQL .= " WHERE ";
			$sSQL .= " '" . $this->bd->escape(@$_REQUEST["senha"]) . "' = '" . $this->bd->escape(@$_REQUEST["senha_conf"]) . "' ";
			$sSQL .= " AND username= '" . $this->bd->escape(@$_REQUEST["username"]) . "' ";
			$sSQL .= " AND dominio = '" . $this->bd->escape(@$_REQUEST["dominio"]) . "' ";
			$sSQL .= " AND tipo_conta = '" . $this->bd->escape(@$_REQUEST["tipo_conta"]) . "' ";

			echo $sSQL;
			

			$this->bd->consulta($sSQL);
			$alterar_senhas = 0 ;
			echo "<script>alert('Senha Alterada com Sucesso!!');</script>";
			$this->arquivoTemplate = "interface_home.html";
		
		}
		
		
		$this->tpl->atribui("tipo_conta",$tipo_conta);
		$this->tpl->atribui("username",$username);
		$this->tpl->atribui("dominio",$dominio);
		$this->tpl->atribui("alterar_senhas",$alterar_senhas);
		$this->arquivoTemplate = "interface_home.html";
		
		}
		
		if ($op == "contas"){
		
		$sSQL  = " SELECT ";
		$sSQL .= " cc.username, pp.nome, cc.id_cliente, cc.dominio, cc.id_conta , cc.tipo_conta";
		$sSQL .= " FROM cntb_conta cc, cltb_cliente clc, prtb_produto pp, cbtb_cliente_produto prp  ";
		$sSQL .= " WHERE ";
		$sSQL .= " clc.id_cliente = prp.id_cliente ";
		$sSQL .= " AND pp.tipo = cc.tipo_conta ";
		$sSQL .= " AND cc.id_cliente = '$id_cliente' ";
		$sSQL .= " AND cc.id_cliente_produto = prp.id_cliente_produto ";
		$sSQL .= " AND pp.id_produto = prp.id_produto ";
		
		$contas = $this->bd->obtemRegistros($sSQL);

	$this->tpl->atribui("contas",$contas);
	$this->arquivoTemplate = "interface_home.html";
			
	}
	
	$this->arquivoTemplate = "interface_home.html";	
}
	public function __destruct() {
		parent::__destruct();
	}
	

}



?>