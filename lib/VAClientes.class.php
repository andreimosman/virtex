<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAClientes extends VirtexAdmin {

	public function VAClientes() {
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
			$id_cliente = @$_REQUEST["id_cliente"];
			
			if( $acao ) {
			   // Se ele recebeu o campo ação é pq veio de um submit
			   $enviando = true;
			} else {
			   // Se não recebe o campo ação e tem id_cliente é alteração, caso contrário é cadastro.
			   $acao = $id_cliente ? "alt" : "cad";
			}
				
			
			$this->tpl->atribui("op",$op);
			$this->tpl->atribui("acao",$acao);
			$this->tpl->atribui("id_cliente",$id_cliente);
			
			
			// O cara clicou no botão enviar (submit).
			if( $enviando ) {
			   // Validar
			   $erros = $this->validaFormulario();
			   if( !count($erros) ) {
			      // Gravar no banco.
			      $sSQL = "";
			      if( $acao == "cad" ) {
			         $id_cliente = $this->bd->proximoID("clsq_id_cliente");

			         // Cadastro
			         $sSQL  = "INSERT INTO ";
			         $sSQL .= "   cltb_cliente( ";
				 $sSQL .= "      id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
			         $sSQL .= "      rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, ";
				 $sSQL .= "      cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
			         $sSQL .= "      fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
				 $sSQL .= "      ativo,obs ) ";
			         $sSQL .= "   VALUES (";
				 $sSQL .= "     '" . $this->bd->escape($id_cliente) . "', ";
				 $sSQL .= "     now(), ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome_razao"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo_pessoa"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["rg_inscr"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["expedicao"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["cpf_cnpj"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["email"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["endereco"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["complemento"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["cidade"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["estado"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["cep"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["bairro"]) . "', "; 
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["fone_comercial"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["fone_residencial"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["fone_celular"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["contato"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["banco"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["conta_corrente"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["agencia"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["dia_pagamento"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["ativo"]) . "', ";
				 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["obs"]) . "' ";
			         $sSQL .= "     )";
			         
			         
			      } else {
			         // Alteração
			      }
			      
			      $this->bd->consulta($sSQL);
			      
			      
			      // Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
			      $this->tpl->atribui("mensagem","Cliente cadastrado com sucesso");
			      $this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=listagem");
			      $this->tpl->atribui("target","_top");
			      
			      $this->arquivoTemplate="msgredirect.html";
			   
			      // cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
			      return;
			   }
			   
			}
			
			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			
			// Seta as variáveis do template.
			$this->arquivoTemplate = "clientes_cadastro.html";

		} else if($op == "pesquisa"){	
			$this->arquivoTemplate = "search_clientes.html";

		} else if ($op == "eliminar"){
			$this->arquivoTemplate = "eliminar_cliente.html";
		} else if ($op =="clientemod"){
			$this->arquivoTemplate = "ficha_cliente.html";
		} else if ($op == "clientedisc"){
			$this->arquivoTemplate = "cliente_discado.html";
		} else if ($op == "clienteAdisc"){
			$this->arquivoTemplate = "cliente_discado_altera.html";
		} else if ($op == "clienteAmail"){
			$this->arquivoTemplate = "cliente_email_altera.html";
		} else if ($op == "clienteAhosp"){
			$this->arquivoTemplate = "cliente_hospedagem_altera.html";
		} else if ($op == "clientehosp"){
			$this->arquivoTemplate = "cliente_hospedagem.html";
		} else if ($op == "clientebl"){
			$this->arquivoTemplate = "cliente_bandalarga.html";
		} else if ($op == "clienteAbl"){
			$this->arquivoTemplate = "cliente_bandalarga_altera.html";
		} else if ($op == "clienteCob"){
			$this->arquivoTemplate = "cliente_cobranca.html";
		} else if ($op == "clienteCobHist"){
			$this->arquivoTemplate = "cliente_cobranca_historico.html";
		} else if ($op == "clienteCobContr"){
			$this->arquivoTemplate = "cliente_cobranca_contratos.html";
		} else if ($op == "clienteAcob"){
			$this->arquivoTemplate = "cliente_cobranca_alteracob.html";
		} else if ($op == "clienteAcontr"){
			$this->arquivoTemplate = "cliente_cobranca_alteracontr.html";
		} else if ($op == "clienteContrExibe"){
			$this->arquivoTemplate = "cliente_contrato.html";
		} else if ($op == "clienteContr"){
			$this->arquivoTemplate = "cliente_novo_contrato.html";
		} else if ($op =="clnada"){
			$this->arquivoTemplate = "cliente_cobranca_nada.html";
		} else if ($op == "clBl"){
			$this->arquivoTemplate = "cliente_cobranca_bl.html";
		}else if ($op == "clDisc"){
			$this->arquivoTemplate = "cliente_cobranca_disc.html";
		} else if ($op == "clHosp"){
			$this->arquivoTemplate = "cliente_cobranca_hosp.html";
		}  else if ($op =="clEliminado"){
			$this->arquivoTemplate = "cliente_eliminadoOk.html";
		} else if ($op =="confirmacadcli"){
			$this->arquivoTemplate = "confirma_cadastro_pops.html";
		}else if ($op =="confirmaaltcli"){
			$this->arquivoTemplate = "confirma_alteracao_pops.html";
		}
	}
}
?>
