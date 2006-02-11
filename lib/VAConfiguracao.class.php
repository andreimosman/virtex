<?
require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAConfiguracao extends VirtexAdmin {

	public function VAConfiguracao() {
		parent::VirtexAdmin();


	}


	protected function validaFormulario() {
	   $erros = array();
	   return $erros;
	}

		public function processa($op=null) {// Cria função processa
		
			if ($op == "cadastro"){
		
			$erros = array();

			$acao = @$_REQUEST["acao"];
			$id_pop = @$_REQUEST["id_pop"];

			$enviando = false;
			
			
			$reg = array();


			if( $acao ) {
			   // Se ele recebeu o campo ação é pq veio de um submit
			   $enviando = true;
			} else {
			   // Se não recebe o campo ação e tem id_cliente é alteração, caso contrário é cadastro.
			   if( $id_cliente ) {
			      // SELECT
			      $sSQL  = "SELECT ";
			      $sSQL .= "   id_pop, nome, info, interface, ";
			      $sSQL .= "FROM cftb_cliente ";
			      $sSQL .= "WHERE id_pop = $pop ";


				
			      $reg = $this->bd->obtemUnicoRegistro($sSQL);
			      
			      
			      
			      $acao = "alt";
			      
			      
			      
			      
			   } else {
			      $acao = "cad";
			   }
			}
			
			if ($acao == "cad"){
			   $msg_final = "POP cadastrado com sucesso!";
			   $titulo = "Cadastrar";
			   
			}else{
			   $msg_final = "POP alterado com sucesso!";
			   $titulo = "Alterar";
			   }
			

			$this->tpl->atribui("op",$op);
			$this->tpl->atribui("acao",$acao);
			$this->tpl->atribui("id_pop",$id_pop);


			// O cara clicou no botão enviar (submit).
			if( $enviando ) {
			   // Validar
			   $erros = $this->validaFormulario();
			   
			   if( count($erros) ) {
			      $reg = $_REQUEST;
			      
			   } else {
			      // Gravar no banco.
			      $sSQL = "";
			      if( $acao == "cad" ) {
			         $id_pop = $this->bd->proximoID("cfsq_id_pop");

			         // Cadastro
			         $sSQL  = "INSERT INTO ";
			         $sSQL .= "   cftb_pop( ";
				 $sSQL .= "      id_pop, nome, info, interface ) ";
			         $sSQL .= "   VALUES (";
				 $sSQL .= "     '" . $this->bd->escape($id_pop) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["info"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["interface"]) . "' ";
			         $sSQL .= "     )";


			      } else {
			         // Alteração
			         $sSQL  = "UPDATE ";
			         $sSQL .= "   cftb_pop ";
			         $sSQL .= "SET ";
			         $sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
			         $sSQL .= "   info = '" . $this->bd->escape(@$_REQUEST["info"]) . "', ";
       			         $sSQL .= "   interface = '" . $this->bd->escape(@$_REQUEST["interface"]) . "' ";
			         $sSQL .= "WHERE ";
			         $sSQL .= "   id_pop = '" . $this->bd->escape(@$_REQUEST["id_pop"]) . "' ";  // se idcliente for =  ao passado.
			         
    		         
			      }

			      $this->bd->consulta($sSQL);  //mostra mensagem de erro
			      
			      if( $this->bd->obtemErro() != MDATABASE_OK ) {
			         echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
			         echo "QUERY: " . $sSQL . "<br>\n";
			      }


			      // Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
			      $this->tpl->atribui("mensagem",$msg_final); //pega o conteúdo de msg_final e envia para mensagem que é uma val do smart.
			      $this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=listagem");
			      $this->tpl->atribui("target","_top");

			       //  if (count($checa)){
			       //  $this->arquivoTemplate="clientes_cadastro.html";
			       // }


			      $this->arquivoTemplate="msgredirect.html"; //faz exibir o msgredirect.html que tem vai receber a mensagem de erro ou sucesso.

			      // cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
			      return;
			   }

			}

			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			
			/*// Atribui as listas
			global $_LS_ESTADOS;
			$this->tpl->atribui("lista_estados",$_LS_ESTADOS);
			
			global $_LS_TP_PESSOA;
			$this->tpl->atribui("lista_tp_pessoa",$_LS_TP_PESSOA); //lista_tp_pessoa recebe os dados do array LS_TP_PESSOA(status.defs.php) para mostrar do dropdown.
			
			global $_LS_ST_CLIENTE;
			$this->tpl->atribui("lista_ativo",$_LS_ST_CLIENTE);

			global $_LS_DIA_PGTO;
			$this->tpl->atribui("lista_dia_pagamento",$_LS_DIA_PGTO);*/

			
			
			// Atribui os campos
		        $this->tpl->atribui("id_pop",@$reg["id_pop"]);
		        $this->tpl->atribui("info",@$reg["info"]);
		        $this->tpl->atribui("interface",@$reg["interface"]);// pega a info do db e atribui ao campo correspon do form

		        
		        $this->tpl->atribui("titulo",$titulo);// para que no clientes_cadastro.html a variavel do smart titulo consiga pegar o que foi definido no $titulo.
		        

			// Seta as variáveis do template.
			$this->arquivoTemplate = "pop_cadastro.html";

				}
		
			}// fecha if op=cadastro
		}// fecha processa



















?>