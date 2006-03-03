<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAAdministrador extends VirtexAdmin {

	public function VAAdministrador() {
		parent::VirtexAdmin();


	}

	protected function validaFormulario() {
		$erros = array();
		return $erros;
		
	}



	public function processa($op=null) {
		if ($op == "cadastro"){
	    	$erros = array();

			$acao = @$_REQUEST["acao"];
			$id_admin = @$_REQUEST["id_admin"];

			$enviando = false;

			$reg = array(); //
		
			if( $acao ) {
		    	// Se ele recebeu o campo ação é pq veio de um submit
		    	$enviando = true;
			} else {
			    // Se não recebe o campo ação e tem id_admin é alteração, caso contrário é cadastro.
			   
				if( $id_admin ) {
			   	// SELECT
					$sSQL  = "SELECT ";
					$sSQL .= "   id_admin, admin, senha, status, ";
					$sSQL .= "   nome, email, primeiro_login ";
					$sSQL .= "FROM adtb_admin ";
					$sSQL .= "WHERE id_admin = $id_admin ";
			        
			   
					$reg = $this->bd->obtemUnicoRegistro($sSQL);
			     
					$acao = "alt";  
			 
			 
				} else {
					$acao = "cad";
				}
			}
			 			
			if ($acao == "cad"){
				$msg_final = "Administrador cadastrado com sucesso!";
				$titulo = "Cadastrar" ;
			 			   
			} else {
			  $msg_final = "Administrador alterado com sucesso!";
			  $titulo = "Alterar" ;
			}
			
			$this->tpl->atribui("op",$op);  //tpl = template
			$this->tpl->atribui("acao",$acao);  // atribui o que está em acao para acao.
			$this->tpl->atribui("id_admin",$id_admin);//

			// O cara clicou no botão enviar (submit).
			if( $enviando ) {
				// Validar
				$erros = $this->validaFormulario();
				if( count($erros) ) {
					$reg = $_REQUEST;
				}else {
					// Gravar no banco.
					$sSQL = "";
			      if( $acao == "cad" ) {
	  		          $primeiro_login = true; //deve receber verdadeiro;
                                  $id_admin = $this->bd->proximoID("adsq_id_admin");

                           // Cadastro
			   $sSQL  = "INSERT INTO ";
			   $sSQL .= "   adtb_admin( ";
			   $sSQL .= "      id_admin, admin, senha, status, ";
			   $sSQL .= "      nome, email, primeiro_login) ";
			   $sSQL .= "   VALUES (";
			   $sSQL .= "     '" . $this->bd->escape($id_admin) . "', ";
			   $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["admin"]) . "', ";
			   $sSQL .= "     '" . md5($this->bd->escape(@$_REQUEST["senha"])) . "', ";
			   $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["status"]) . "', ";
			   $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
			   $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["email"]) . "', ";
			   $sSQL .= "     '" . $this->bd->escape($primeiro_login) . "' ";
			   $sSQL .= "     )";
                      }else {
                         // alteracao

			$sSQL  = "UPDATE ";
			$sSQL .= "   adtb_admin ";
			$sSQL .= "SET ";
			$sSQL .= "   admin = '" . $this->bd->escape(@$_REQUEST["admin"]) . "', ";
			$sSQL .= "   senha = '" . md5($this->bd->escape(@$_REQUEST["senha"])) . "', ";
       			$sSQL .= "   status = '" . $this->bd->escape(@$_REQUEST["status"]) . "', ";
       			$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
       			$sSQL .= "   email = '" . $this->bd->escape(@$_REQUEST["email"]) . "' ";
//      			$sSQL .= "   primeiro_login = '" . $this->bd->escape($primeiro_login) . "' ";
       			$sSQL .= "WHERE ";
			$sSQL .= "   id_admin = '" . $this->bd->escape(@$_REQUEST["id_admin"]) . "' ";
			         

                      }
                      
                      $this->bd->consulta($sSQL);
                      
                       if( $this->bd->obtemErro() != MDATABASE_OK ) {
		      	 echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
		         echo "QUERY: " . $sSQL . "<br>\n";
		      	 }

		      // Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
		      $this->tpl->atribui("mensagem",$msg_final);
		      $this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=listagem");
		      $this->tpl->atribui("target","_top");
		      
		      $this->arquivoTemplate="msgredirect.html";

		      // cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
		      return;

                 } // fim if($enviado)

}	//encerra funcion processa



                // Atribui a variável de erro no template.
		$this->tpl->atribui("erros",$erros);
		
		global $_LS_ST_ADMIN;
			$this->tpl->atribui("lista_status",$_LS_ST_ADMIN);
		
			
		// Atribui os campos
		$this->tpl->atribui("id_admin",@$reg["id_admin"]);
		$this->tpl->atribui("admin",@$reg["admin"]);
		$this->tpl->atribui("senha",@$reg["senha"]);
		$this->tpl->atribui("status",@$reg["status"]);
		$this->tpl->atribui("nome",@$reg["nome"]);
		$this->tpl->atribui("email",@$reg["email"]);
		$this->tpl->atribui("primeiro_login",@$reg["primeiro_login"]);
		
		$this->tpl->atribui("titulo",$titulo);
		
		// Seta as variáveis do template.
		$this->arquivoTemplate = "administrador_cadastro.html";


	// processa os links
	    } else if($op == "lista"){
	    
			$sSQL  = "SELECT ";
			$sSQL .= "   id_admin, admin, senha, status, ";
			$sSQL .= "   nome, email, primeiro_login ";
			$sSQL .= "FROM adtb_admin ";
			$sSQL .= "ORDER BY nome ASC ";
			
			$lista = $this->bd->obtemRegistros($sSQL);
			
			$this->tpl->atribui("lista_admin",$lista);
			$this->tpl->atribui("id_admin",@$lista["id_admin"]);
			$this->tpl->atribui("admin",@$lista["admin"]);
			$this->tpl->atribui("senha",@$lista["senha"]);
			$this->tpl->atribui("status",@$lista["status"]);
			$this->tpl->atribui("nome",@$lista["nome"]);
			$this->tpl->atribui("email",@$lista["email"]);
			$this->tpl->atribui("primeiro_login",@$lista["primeiro_login"]);
	    
			$this->arquivoTemplate = "administrador_lista.html";	 		   
	 		   
	    
	    }else if ($op == "altera"){
	    		$erro = array();
		
			$acao = @$_REQUEST["acao"];
			//$id_admin = @$_REQUEST["id_admin"];
			$senha = @$_REQUEST["senha"];
		
			$enviando = false;
		
			$reg = array(); 
			
			$nome = $this->admLogin->obtemNome();
			$adm = $this->admLogin->obtemAdmin();
			$id_admin = $this->admLogin->obtemId();
			

			if( !$acao ) {
		    // Se ele recebeu o campo ação é pq veio de um submit
				$enviando = false;

					$this->tpl->atribui("nome",$nome);
					$this->tpl->atribui("admin",$adm);	
					//echo "acao: ". $acao;
					//echo "bosta";
				$this->arquivoTemplate = "administrador_alterarsenha.html";
				return;						    
			 
			} else {
				// Valida senha e confirmação
				
			
				$enviando = true;
				
			}
			
			$mensagem = "";
			$url = "administrador.php?op=altera";
			$target = "_top";
			
			
			if( $erro ) {
				$mensagem = $erro;
			} else {
				if( !$enviando ) {
					// Atribui as variáveis de exibicao.
					
					$this->tpl->atribui("nome",$nome);
					$this->tpl->atribui("admin",$admin);
					
					
					
					
				} else {
			
					$mensagem = "Senha alterada com sucesso. ";
				
					if( $this->admLogin->primeiroLogin() ) {
					   $mensagem .= " Bem vindo ao sistema VirtexAdmin";
					   $url = "home.php";
					}

					$sSQL  = "UPDATE ";
					$sSQL .= "   adtb_admin ";
					$sSQL .= "SET ";
					$sSQL .= "   senha = '" . md5(@$_REQUEST["senha"]) . "', ";
					$sSQL .= "   primeiro_login = 'f' ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_admin = '$id_admin' ";
					
					$this->bd->consulta($sSQL);

					if( $this->bd->obtemErro() != MDATABASE_OK ) {
						echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
						echo "QUERY: " . $sSQL . "<br>\n";
					}

					$this->admLogin->login($adm,$senha);
					
				}

			     		
			}
			
			$this->tpl->atribui("url",$url);
			$this->tpl->atribui("target",$target);
			$this->tpl->atribui("mensagem",$mensagem);

	    
	    	
	    	$this->arquivoTemplate = "jsredir.html";
	    	
	    	
	        
	    } else if ($op == "privilegio"){
	    
	    	$id_priv = Array();
	    
	    	$id_admin = @$_REQUEST['id_admin'];
	    	$oper = @$_REQUEST['oper'];
	    	$id_priv = @$_REQUEST['id_priv'];
	    	
	    	
	    	//Infos do admininstrador
	    	$tSQL  = "SELECT ";
			$tSQL .= "   id_admin, admin, status, nome, email ";
			$tSQL .= "FROM ";
			$tSQL .= "   adtb_admin ";
			$tSQL .= "WHERE ";
			$tSQL .= "   id_admin = '$id_admin' ";

	    	
	    	// LISTA DOS PRIVILÉGIOS
         	$sSQL  = "SELECT ";
         	$sSQL .= "   p.id_priv, p.cod_priv,p.nome, ";
         	$sSQL .= "   CASE WHEN up.pode_gravar is true THEN 1 ELSE 0 END as pode_gravar, ";
         	$sSQL .= "   CASE WHEN up.pode_gravar is null THEN '' ELSE 'checked' END as checked ";
         	$sSQL .= "FROM ";
         	$sSQL .= "   adtb_privilegio p LEFT OUTER JOIN adtb_usuario_privilegio up ON (p.id_priv = up.id_priv AND up.id_admin = $id_admin) ";
         	$sSQL .= "ORDER BY ";
         	$sSQL .= "   p.nome ";
	    	
	    	$privilegios = $this->bd->obtemRegistros($sSQL);
	    	$admin = $this->bd->obtemUnicoRegistro($tSQL);
	    	
	    	$this->tpl->atribui("privilegios",$privilegios);
	    	global $_LS_PRIVILEGIO;
			$this->tpl->atribui("lista_privilegio",$_LS_PRIVILEGIO);
			
			$this->tpl->atribui("admin",$admin);
			
			if ( $oper == "alt" ) {
			
				$dSQL = "DELETE FROM adtb_usuario_privilegio WHERE id_admin = $id_admin";
				$this->bd->consulta($dSQL);
				
					while(list($id,$valor)=each($_REQUEST['$id'])){

						$sSQL  = "INSERT INTO ";
						$sSQL .= "   adtb_usuario_privilegio( ";
						$sSQL .= "      id_admin, id_priv, pode_gravar ";
						$sSQL .= "   ) VALUES (";
						$sSQL .= "      '". @$_REQUEST['$id_admin'] ."', ";
						$sSQL .= "      '$id', ";
						$sSQL .= "      '". @$_REQUEST['$pode_gravar'] ."' ";
						$sSQL .= "   ) ";
						$sSQL .= "";

						$this->bd->consulta($sSQL);

				}
				
			
			}

	    
	    
	        $this->arquivoTemplate = "administrador_direitos.html";

	    }else if ($op == "alt"){
		$this->arquivoTemplate = "administrador_altera.html";
	   }
      }//function processa
      


public function __destruct() {
      	parent::__destruct();
}
      


}//class VAAdministrador




?>
