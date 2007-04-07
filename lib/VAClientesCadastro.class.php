<?

	class VAClientesCadastro extends VAClientes {

		public function __construct() {
			parent::__construct();
		}
		
		
		public function processa($op=null) {
			$this->tpl->atribui("op",$op);

			if( ! $this->privPodeLer("_CLIENTES") ) {
				$this->privMSG();
				return;
			}
			
			$cltb_cliente = VirtexModelo::factory("cltb_cliente");


			$erros = array();

			$acao = @$_REQUEST["acao"];
			$cpf_cnpj = @$_REQUEST["cpf_cnpj"];

			$enviando = false;

			$reg = array();
			//$this->obtemPR($id_cliente);
			

			if( $acao ) {
				if( ! $this->privPodeGravar("_CLIENTES") ) {
					$this->privMSG();
					return;
				}		
				// Se ele recebeu o campo ação é pq veio de um submit
				$enviando = true;
			} else {
				// Se não recebe o campo ação e tem id_cliente é alteração, caso contrário é cadastro.
				if( $this->id_cliente ) {
					//$reg = $this->obtemCliente($id_cliente);
					$reg = $cltb_cliente->obtemUnico(array("id_cliente" => $this->id_cliente));

					$acao = "alt";
					$titulo = "Alterar";

				} else {
					if( ! $this->privPodeGravar("_CLIENTES") ) {
						$this->privMSG();
						return;
					}

					$acao = "cad";
					$titulo = "Cadastrar";
				}
			}

			if( $enviando ) {

				if( $cpf_cnpj ) {

					$tSQL  = "SELECT ";
					$tSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
					$tSQL .= "   rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
					$tSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
					$tSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, excluido ";
					$tSQL .= "   ativo,obs, info_cobranca ";
					$tSQL .= "FROM ";
					$tSQL .= "   cltb_cliente ";
					$tSQL .= "WHERE ";
					$tSQL .= "   cpf_cnpj = '$cpf_cnpj' AND excluido = 'f'";

					if( $acao == "alt" ) {
						$tSQL .= "   AND id_cliente != '". $this->id_cliente ."' ";
					}
					$checa = null;
					//$checa = $this->bd->obtemUnicoRegistro($tSQL);
					if($checa) {

						$erros[] = "CPF/CNPJ cadastrado para outro cliente.";

						// Atribui os campos
						$this->tpl->atribui("id_cliente",@$_REQUEST["id_cliente"]);
						$this->tpl->atribui("data_cadastro",@$_REQUEST["data_cadastro"]);
						$this->tpl->atribui("nome_razao",@$_REQUEST["nome_razao"]);// pega a info do db e atribui ao campo correspon do form
						$this->tpl->atribui("tipo_pessoa",@$_REQUEST["tipo_pessoa"]);
						$this->tpl->atribui("rg_inscr",@$_REQUEST["rg_inscr"]);
						$this->tpl->atribui("rg_expedicao",@$_REQUEST["rg_expedicao"]);
						$this->tpl->atribui("cpf_cnpj",@$_REQUEST["cpf_cnpj"]);
						$this->tpl->atribui("email",@$_REQUEST["email"]);
						$this->tpl->atribui("endereco",@$_REQUEST["endereco"]);
						$this->tpl->atribui("complemento",@$_REQUEST["complemento"]);
						$this->tpl->atribui("id_city",@$_REQUEST["id_cidade"]);
						$this->tpl->atribui("cidade",@$_REQUEST["cidade"]);
						$this->tpl->atribui("estado",@$_REQUEST["estado"]);
						$this->tpl->atribui("cep",@$_REQUEST["cep"]);
						$this->tpl->atribui("bairro",@$_REQUEST["bairro"]);
						$this->tpl->atribui("fone_comercial",@$_REQUEST["fone_comercial"]);
						$this->tpl->atribui("fone_residencial",@$_REQUEST["fone_residencial"]);
						$this->tpl->atribui("fone_celular",@$_REQUEST["fone_celular"]);
						$this->tpl->atribui("contato",@$_REQUEST["contato"]);
						$this->tpl->atribui("banco",@$_REQUEST["banco"]);
						$this->tpl->atribui("conta_corrente",@$_REQUEST["conta_corrente"]);
						$this->tpl->atribui("agencia",@$_REQUEST["agencia"]);
						$this->tpl->atribui("dia_pagamento",@$_REQUEST["dia_pagamento"]);
						$this->tpl->atribui("ativo",@$_REQUEST["ativo"]);
						$this->tpl->atribui("obs",@$_REQUEST["obs"]);

					}

				}

				if( !count($erros) ) {
					// Grava no banco.
					if( $acao == "cad" ) {
						// CADASTRO
						$msg_final = "Cliente Cadastrado com sucesso!";
						$id_cliente = $cltb_cliente->insere(@$_REQUEST);

					} else {
						// ALTERACAO
						$msg_final = "Cliente Alterado com sucesso!";						
						$cltb_cliente->altera(@$_REQUEST,array("id_cliente" => $this->id_cliente));

					}
					//////////echo "$sSQL";
					$this->bd->consulta($sSQL);  

					//if( $this->bd->obtemErro() != MDATABASE_OK ) {
					//	////////echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
					//	////////echo "QUERY: " . $sSQL . "<br>\n";
					//
					//}


					// Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
					$this->tpl->atribui("mensagem",$msg_final); 
					$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=cadastro&id_cliente=" . $this->id_cliente);
					$this->tpl->atribui("target","_top");

					$this->arquivoTemplate = "msgredirect.html";


					// cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
					return;
				}else{

				//$erros[] = "CPF/CNPJ cadastrado para outro cliente.";

				}

			}


			// Pegar lista de cidades que o provedor opera.
			$cftb_cidade = VirtexModelo::factory("cftb_cidade");
			$this->tpl->atribui("cidades_disponiveis",$cftb_cidade->obtemCidadesDisponiveis());



			//////////echo $erros;
			if ($acao == "cad"){
				$titulo = "Cadastrar";
			}else if ($acao == "alt"){
				$titulo = "Alterar";
			}

			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("mensagem",$erros);
			$this->tpl->atribui("acao",$acao);

			// Atribui as listas
			//global $_LS_ESTADOS;
			//$this->tpl->atribui("lista_estados",$_LS_ESTADOS);

			$this->tpl->atribui("lista_tp_pessoa",$cltb_cliente->listaTipoPessoa()); //lista_tp_pessoa recebe os dados do array LS_TP_PESSOA(status.defs.php) para mostrar do dropdown.
			$this->tpl->atribui("lista_ativo",$cltb_cliente->listaStatusCliente());
			$this->tpl->atribui("lista_dia_pagamento",$cltb_cliente->listaDiaPagamento());

			// Atribui os campos
			if(count($reg)) {
				while(list($vr,$vl) = each($reg)) {
					//echo "$vr = $vl<br>\n";
					$this->tpl->atribui($vr,$vl);
				}
			}
			
			$this->tpl->atribui("titulo",@$titulo);// para que no clientes_cadastro.html a variavel do smart titulo consiga pegar o que foi definido no $titulo.

			// Seta as variáveis do template.
			$this->arquivoTemplate = "clientes_cadastro.html";
			
		
		}
	
	
	}
	
?>
