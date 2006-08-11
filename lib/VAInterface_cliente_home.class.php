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


	$this->tpl->atribui("tipo_conta_fix",$tipo_conta);
	$this->tpl->atribui("conta_mestre",$conta_mestre);
	$this->tpl->atribui("username",$username);
	$this->tpl->atribui("username_conf",$username);
	$this->tpl->atribui("dominio",$dominio);	
	$this->tpl->atribui("id_conta",$id_conta);
	$this->tpl->atribui("conta",$conta);
	
	$this->arquivoTemplate = "interface_home.html";
	
	if ($op == "dados"){
	
	$dados = "true";	
	
	$username = @$_REQUEST["username"];
	$tipo_conta = @$_REQUEST["tipo_conta"];
	$dominio = @$_REQUEST["dominio"];
	

		
		$this->tpl->atribui("tipo_conta",$tipo_conta);
		$this->tpl->atribui("username",$username);
		$this->tpl->atribui("dominio",$dominio);	
	
	
	
	$this->tpl->atribui("dados",$dados);

	$this->arquivoTemplate = "interface_home.html";
	
	
	}
		
	if ($op == "home"){
		
		$this->arquivoTemplate = "interface_home.html";

		}
		
		if ($op == "alterar_senha"){
		
		$id_conta2 = @$_REQUEST["id_conta"];
		$atual = @$_REQUEST["atual"];
		
		if (!$acao && !$id_conta2){
		
		
		$username = $_REQUEST["username"];
		$dominio = @$_REQUEST ["dominio"];
		$tipo_conta = @$_REQUEST ["tipo_conta"] ;
		
		}
		
		
		
		$alterar_senhas = "true";
		$msg = "Senha Alterada com sucesso!";
		$url ="index_home.php?op=home";
		
		if ($acao == "alterar" ){
			
		   if ($atual){
			

			
				$aSQL  = "SELECT senha FROM cntb_conta WHERE username= '" . $this->bd->escape(@$_REQUEST["username"]) . "' AND dominio = '" . $this->bd->escape(@$_REQUEST["dominio"]) . "' AND tipo_conta = '" . $this->bd->escape(@$_REQUEST["tipo_conta"]) . "' " ;

					$senha_conta = $this->bd->obtemUnicoRegistro($aSQL);
					$senha_atual = $senha_conta['senha'];


				if ($senha_atual == $this->bd->escape(@$_REQUEST["senha_atual"])){
			
					$msg = "Senha Alterada com sucesso!";
					$url ="index_home.php?op=home";

				}else{
			
					$msg = "Erro ao processar, as senhas não conferem.Tente Novamente";
					$url ="javascript:history.back();";

				}
			}
		
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



		
		if ($atual){
		
			$sSQL .= " AND senha = '" . $this->bd->escape(@$_REQUEST["senha_atual"]) . "' ";

		}


		$this->bd->consulta($sSQL);

			$this->tpl->atribui("mensagem",$msg); 
			$this->tpl->atribui("url",$url);
			$this->tpl->atribui("target","_top");

			$this->arquivoTemplate = "interface_msgredirect.html";

			$alterar_senhas = 0 ;
			return;
		
		}
		
		
		$this->tpl->atribui("tipo_conta",$tipo_conta);
		$this->tpl->atribui("username",$username);
		$this->tpl->atribui("dominio",$dominio);
		$this->tpl->atribui("alterar_senhas",$alterar_senhas);
		$this->arquivoTemplate = "interface_home.html";
		
		}
		
		if ($op == "contas"){
		
		$sSQL  = " SELECT ";
		$sSQL .= " cc.username, pp.nome, cc.id_cliente, cc.dominio, cc.id_conta , cc.tipo_conta , cc.id_cliente_produto ";
		$sSQL .= " FROM cntb_conta cc, cltb_cliente clc, prtb_produto pp, cbtb_cliente_produto prp  ";
		$sSQL .= " WHERE ";
		$sSQL .= " clc.id_cliente = prp.id_cliente ";
		$sSQL .= " AND cc.id_cliente = '$id_cliente' ";
		$sSQL .= " AND cc.id_cliente_produto = prp.id_cliente_produto ";
		$sSQL .= " AND pp.id_produto = prp.id_produto ";
		$sSQL .= " AND cc.status = 'A' ";

		//////////echo "$sSQL;	<br>";

		
		$contas = $this->bd->obtemRegistros($sSQL);
		
		/*		for($i=0;$i<count($contas);$i++) {
							
					$username = $contas[$i]["username"];
					$dominio = $contas[$i]["dominio"];
		
					$dSQL  = "SELECT ";
					$dSQL .= "	username, tipo_conta, dominio , email ";
					$dSQL .= "FROM ";
					$dSQL .= "	cntb_conta_email ";
					$dSQL .= "WHERE ";
					$dSQL .= "	dominio = '$dominio' ";
					$dSQL .= "	AND username = '$username'";


		
					///echo $dSQL ."<hr>\n";
		
					$contas_email = $this->bd->obtemRegistros($dSQL);
		
					$contas[$i]["conta"] = $contas_email;
		}	*/

	$this->tpl->atribui("contas",$contas);
	//$this->tpl->atribui("contas_email",$contas_email);
	$this->arquivoTemplate = "interface_home.html";
			
	}
	
	$this->arquivoTemplate = "interface_home.html";	
}
	public function __destruct() {
		parent::__destruct();
	}
	

}



?>