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
	
	private function obtemCliente($id_cliente) {

		$sSQL  = "SELECT ";
		$sSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
		$sSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
		$sSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
		$sSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
		$sSQL .= "   ativo,obs ";
		$sSQL .= "FROM ";
		$sSQL .= "   cltb_cliente ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_cliente = '$id_cliente' ";
   
		return( $this->bd->obtemUnicoRegistro($sSQL) );

	}
	
	private function obtemNAS($id_nas) {
		$sSQL = "SELECT ";
		$sSQL .= "   id_nas,nome,ip,secret,tipo_nas ";
		$sSQL .= "FROM ";
		$sSQL .= "   cftb_nas ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_nas = '". $this->bd->escape($id_nas) . "' ";
		
		return( $this->bd->obtemUnicoRegistro($sSQL) );
		
	}
	
	private function obtemIP($id_nas) {

		$sSQL = "SELECT ";
		$sSQL .= "   	i.ipaddr ";
		$sSQL .= "FROM ";
		$sSQL .= "   cntb_conta_bandalarga cbl RIGHT OUTER JOIN cftb_ip i USING(ipaddr), "; 
		$sSQL .= "   cftb_rede r,cftb_nas_rede nr, cftb_nas n ";
		$sSQL .= "WHERE ";
		$sSQL .= "	nr.id_nas = n.id_nas ";
		$sSQL .= "    AND r.rede = nr.rede ";
	   	$sSQL .= "	AND nr.id_nas = n.id_nas ";
	   	$sSQL .= "	AND n.id_nas=$id_nas ";
		$sSQL .= "   	AND i.ipaddr << r.rede ";
	   	$sSQL .= "	AND r.tipo_rede = 'C' ";
	   	$sSQL .= "	AND cbl.ipaddr is null ";
		$sSQL .= "ORDER BY ";
	   	$sSQL .= "	i.ipaddr ";
		$sSQL .= "LIMIT ";
	   	$sSQL .= "	1 ";

		return( $this->bd->obtemUnicoRegistro($sSQL) );
	
	}
	
	private function obtemRede($id_nas) {
		$sSQL = "SELECT ";
		$sSQL .= "   r.rede ";
		$sSQL .= "FROM ";
		$sSQL .= "   cntb_conta_bandalarga cbl RIGHT OUTER JOIN cftb_rede r USING(rede), cftb_nas_rede nr, cftb_nas n ";
		$sSQL .= "WHERE ";
		$sSQL .= "   nr.rede = r.rede ";
		$sSQL .= "   AND nr.id_nas = n.id_nas ";
		$sSQL .= "   AND n.id_nas=$id_nas ";
		$sSQL .= "   AND cbl.rede is null ";
		$sSQL .= "   and r.tipo_rede = 'C' ";
		$sSQL .= "ORDER BY ";
		$sSQL .= "   r.rede DESC ";
		$sSQL .= "LIMIT ";
		$sSQL .= "   1 ";

		return( $this->bd->obtemUnicoRegistro($sSQL) );
	}
	
	private function obtemDowUp($id_produto){
		$sSQL = "SELECT ";
		$sSQL .= "   banda_upload_kbps, ";
		$sSQL .= "   banda_download_kbps ";
		$sSQL .= "FROM ";
		$sSQL .= "   prtb_produto_bandalarga ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_produto = $id_produto";
		$sSQL .= "LIMIT ";
		$sSQL .= "   1 ";
		return( $this->bd->obtemUnicoRegistro($sSQL) );;	
	}

	public function processa($op=null) {
		$id_cliente = @$_REQUEST["id_cliente"];
		$tipo = @$_REQUEST["tipo"];
		// Variáveis gerais de template
		$this->tpl->atribui("op",$op);
		$this->tpl->atribui("id_cliente",$id_cliente);
		$this->tpl->atribui("tipo",$tipo);
		
		// Utilizado pelo menu ou por outras funcionalidades quaisquer.
		if( $id_cliente ) {
			$cliente = $this->obtemCliente($id_cliente);   
			$this->tpl->atribui("cliente",$cliente);
		}


		if ($op == "cadastro"){

			$erros = array();

			$acao = @$_REQUEST["acao"];
			$id_cliente = @$_REQUEST["id_cliente"];
			$cpf_cnpj = @$_REQUEST["cpf_cnpj"];
			//$msg_final = @$_REQUEST["msg_final"];

			$enviando = false;
			
			
			$reg = array();


			if( $acao ) {
			   // Se ele recebeu o campo ação é pq veio de um submit
			   $enviando = true;
			} else {
				// Se não recebe o campo ação e tem id_cliente é alteração, caso contrário é cadastro.
				if( $id_cliente ) {
					$reg = $this->obtemCliente($id_cliente);

					$acao = "alt";
					$titulo = "Alterar";

				} else {
					$acao = "cad";
					$titulo = "Cadastrar";
				}
			}
			
			if( $enviando ) {

				if( $cpf_cnpj ){

					$tSQL  = "SELECT ";
					$tSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
					$tSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
					$tSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
					$tSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
					$tSQL .= "   ativo,obs ";
					$tSQL .= "FROM ";
					$tSQL .= "   cltb_cliente ";
					$tSQL .= "WHERE ";
					$tSQL .= "   cpf_cnpj = '$cpf_cnpj' ";
					
					if( $acao == "alt" ) {
					   $tSQL .= "   AND id_cliente != '". $id_cliente ."' ";
					}

					$checa = $this->bd->obtemUnicoRegistro($tSQL);
					if($checa){
					
					$erros[] = "CPF/CNPJ cadastrado para outro cliente.";
					
					// Atribui os campos
					$this->tpl->atribui("id_cliente",@$_REQUEST["id_cliente"]);
					$this->tpl->atribui("data_cadastro",@$_REQUEST["data_cadastro"]);
					$this->tpl->atribui("nome_razao",@$_REQUEST["nome_razao"]);// pega a info do db e atribui ao campo correspon do form
					$this->tpl->atribui("tipo_pessoa",@$_REQUEST["tipo_pessoa"]);
					$this->tpl->atribui("rg_inscr",@$_REQUEST["rg_inscr"]);
					$this->tpl->atribui("expedicao",@$_REQUEST["expedicao"]);
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
				   		
						$id_cliente = $this->bd->proximoID("clsq_id_cliente");

					
						$sSQL  = "INSERT INTO ";
						$sSQL .= "   cltb_cliente ( ";
						$sSQL .= "      id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
						$sSQL .= "      rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
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
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["id_cidade"]) . "', ";
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
					   // ALTERACAO
						$msg_final = "Cliente Alterado com sucesso!";


						$sSQL  = "UPDATE ";
						$sSQL .= "   cltb_cliente ";
						$sSQL .= "SET ";
						$sSQL .= "   nome_razao = '" . $this->bd->escape(@$_REQUEST["nome_razao"]) . "', ";
						$sSQL .= "   tipo_pessoa = '" . $this->bd->escape(@$_REQUEST["tipo_pessoa"]) . "', ";
						$sSQL .= "   rg_inscr = '" . $this->bd->escape(@$_REQUEST["rg_inscr"]) . "', ";
						$sSQL .= "   expedicao = '" . $this->bd->escape(@$_REQUEST["expedicao"]) . "', ";
						$sSQL .= "   cpf_cnpj = '" . $this->bd->escape(@$_REQUEST["cpf_cnpj"]) . "', ";
						$sSQL .= "   email = '" . $this->bd->escape(@$_REQUEST["email"]) . "', ";
						$sSQL .= "   endereco = '" . $this->bd->escape(@$_REQUEST["endereco"]) . "', ";
						$sSQL .= "   complemento = '" . $this->bd->escape(@$_REQUEST["complemento"]) . "', ";
						$sSQL .= "   id_cidade = '" . $this->bd->escape(@$_REQUEST["id_cidade"]) . "', ";
						$sSQL .= "   cidade = '" . $this->bd->escape(@$_REQUEST["cidade"]) . "', ";
						$sSQL .= "   estado = '" . $this->bd->escape(@$_REQUEST["estado"]) . "', ";
						$sSQL .= "   cep = '" . $this->bd->escape(@$_REQUEST["cep"]) . "', ";
						$sSQL .= "   bairro = '" . $this->bd->escape(@$_REQUEST["bairro"]) . "', ";
						$sSQL .= "   fone_comercial = '" . $this->bd->escape(@$_REQUEST["fone_comercial"]) . "', ";
						$sSQL .= "   fone_residencial = '" . $this->bd->escape(@$_REQUEST["fone_residencial"]) . "', ";
						$sSQL .= "   fone_celular = '" . $this->bd->escape(@$_REQUEST["fone_celular"]) . "', ";
						$sSQL .= "   contato = '" . $this->bd->escape(@$_REQUEST["contato"]) . "', ";
						$sSQL .= "   banco = '" . $this->bd->escape(@$_REQUEST["banco"]) . "', ";
						$sSQL .= "   conta_corrente = '" . $this->bd->escape(@$_REQUEST["conta_corrente"]) . "', ";
						$sSQL .= "   agencia = '" . $this->bd->escape(@$_REQUEST["agencia"]) . "', ";
						$sSQL .= "   dia_pagamento = '" . $this->bd->escape(@$_REQUEST["dia_pagamento"]) . "', ";
						$sSQL .= "   ativo = '" . $this->bd->escape(@$_REQUEST["ativo"]) . "', ";
						$sSQL .= "   obs = '" . $this->bd->escape(@$_REQUEST["obs"]) . "' ";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_cliente = '" . $this->bd->escape(@$_REQUEST["id_cliente"]) . "' ";  // se idcliente for =  ao passado.


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

					$this->arquivoTemplate = "msgredirect.html";
					
					
					// cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
					return;
				}else{
				
				//$erros[] = "CPF/CNPJ cadastrado para outro cliente.";
				
				}
				
			}
			
			
			// Pegar lista de cidades que o provedor opera.
			
				$eSQL  = "SELECT ";
				$eSQL .= "   id_cidade, uf, cidade, disponivel ";
				$eSQL .= "FROM cftb_cidade ";
				$eSQL .= "WHERE disponivel = 't' ";
				$eSQL .= "ORDER BY cidade ASC";

				$cidades_disponiveis = $this->bd->obtemRegistros($eSQL);
				
				$this->tpl->atribui("cidades_disponiveis",$cidades_disponiveis);
			
				
				
				//echo $erros;
			if ($acao == "cad"){
				$titulo = "Cadastrar";
			}else if ($acao == "alt"){
				$titulo = "Alterar";
			}
			
			
			
			
			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("mensagem",$erros);
			$this->tpl->atribui("acao",$acao);
			$this->tpl->atribui("op",$op);
			
			// Atribui as listas
			//global $_LS_ESTADOS;
			//$this->tpl->atribui("lista_estados",$_LS_ESTADOS);
			
			global $_LS_TP_PESSOA;
			$this->tpl->atribui("lista_tp_pessoa",$_LS_TP_PESSOA); //lista_tp_pessoa recebe os dados do array LS_TP_PESSOA(status.defs.php) para mostrar do dropdown.
			
			global $_LS_ST_CLIENTE;
			$this->tpl->atribui("lista_ativo",$_LS_ST_CLIENTE);

			global $_LS_DIA_PGTO;
			$this->tpl->atribui("lista_dia_pagamento",$_LS_DIA_PGTO);

			// Atribui os campos
		        $this->tpl->atribui("id_cliente",@$reg["id_cliente"]);
		        $this->tpl->atribui("data_cadastro",@$reg["data_cadastro"]);
		        $this->tpl->atribui("nome_razao",@$reg["nome_razao"]);// pega a info do db e atribui ao campo correspon do form
		        $this->tpl->atribui("tipo_pessoa",@$reg["tipo_pessoa"]);
		        $this->tpl->atribui("rg_inscr",@$reg["rg_inscr"]);
		        $this->tpl->atribui("expedicao",@$reg["expedicao"]);
		        $this->tpl->atribui("cpf_cnpj",@$reg["cpf_cnpj"]);
		        $this->tpl->atribui("email",@$reg["email"]);
		        $this->tpl->atribui("endereco",@$reg["endereco"]);
		        $this->tpl->atribui("complemento",@$reg["complemento"]);
		        $this->tpl->atribui("id_city",@$reg["id_cidade"]);
		        $this->tpl->atribui("cidade",@$reg["cidade"]);
		        $this->tpl->atribui("estado",@$reg["estado"]);
		        $this->tpl->atribui("cep",@$reg["cep"]);
		        $this->tpl->atribui("bairro",@$reg["bairro"]);
		        $this->tpl->atribui("fone_comercial",@$reg["fone_comercial"]);
		        $this->tpl->atribui("fone_residencial",@$reg["fone_residencial"]);
		        $this->tpl->atribui("fone_celular",@$reg["fone_celular"]);			
		        $this->tpl->atribui("contato",@$reg["contato"]);			
		        $this->tpl->atribui("banco",@$reg["banco"]);
		        $this->tpl->atribui("conta_corrente",@$reg["conta_corrente"]);			
		        $this->tpl->atribui("agencia",@$reg["agencia"]);			
		        $this->tpl->atribui("dia_pagamento",@$reg["dia_pagamento"]);			
		        $this->tpl->atribui("ativo",@$reg["ativo"]);			
		        $this->tpl->atribui("obs",@$reg["obs"]);
		        
		        $this->tpl->atribui("titulo",@$titulo);// para que no clientes_cadastro.html a variavel do smart titulo consiga pegar o que foi definido no $titulo.
		        
					

			
			
			// Seta as variáveis do template.
			$this->arquivoTemplate = "clientes_cadastro.html";
			
		} else if ( $op == "pesquisa" ){
		
		
			$erros = array();
			$cond = @$_REQUEST['cond'];
			$campo_pesquisa = @$_REQUEST['campo_pesquisa'];
			
			if( !$campo_pesquisa ){
			 	// Faz alguma coisa
			 	
			 	$aSQL  = "SELECT ";
				$aSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
				$aSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
				$aSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
				$aSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
				$aSQL .= "   ativo,obs ";
				$aSQL .= "FROM cltb_cliente ";
				$aSQL .= "ORDER BY id_cliente DESC LIMIT (10)";
							
				$listagem_clientes = $this->bd->obtemRegistros($aSQL);
				$this->tpl->atribui("listagem_clientes", $listagem_clientes);
			 	
			 	
			 	
			 	
			  	if( $cond ) {
			  		$erros[] = "Você se esqueceu de preencher os parâmetros da pesquisa.";
			  	} else {
			  		$cond = "nome";
			  	}
			  
			} else {

				$sSQL  = "SELECT ";
				$sSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
				$sSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
				$sSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
				$sSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
				$sSQL .= "   ativo,obs ";
				$sSQL .= "FROM cltb_cliente ";
				//$sSQL .= "WHERE $campo = '$campo_pesquisa' ";
				$sSQL .= "WHERE ";

				switch($cond) {

				   case 'nome':
				      $sSQL .= "   nome_razao ilike '%$campo_pesquisa%' ";
				      break;
				   case 'CPF':
				      $sSQL .= "   cpf_cnpj = '" . $this->bd->escape(@$_REQUEST["campo_pesquisa"]) . "' ";
				      break;
				   case 'cod':
				      $sSQL .= "   id_cliente = '" . $this->bd->escape(@$_REQUEST["campo_pesquisa"]) . "' ";
				      break;
				}
		
				$clientes = $this->bd->obtemRegistros($sSQL);
		
				if( $this->bd->obtemErro() ) {
				   echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
				   echo "SQL: $sSQL <br>\n";
				}
		
				$this->tpl->atribui("lista_clientes", $clientes);
				
				if( !count($clientes) ) {
				   $erros[] = "A pesquisa não retornou resultados";
				}
				
				
		
			} //fecha o else do mostra				
			
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("cond",$cond);
			$this->tpl->atribui("campo_pesquisa",$campo_pesquisa);
			
			$this->arquivoTemplate="clientes_pesquisa.html";
				

				
				
				
				
	
		} else if ($op == "cobranca") {
			// Sistema de contratação de produtos e resumo de cobrança
			
			$rotina = @$_REQUEST["rotina"];
			$acao = @$_REQUEST["acao"];
			$email_igual = @$_REQUEST['email_igual'];

			
			$this->tpl->atribui("rotina",$rotina);
			$this->arquivoTemplate = "cliente_cobranca.html";
			
			$erros = array();

			
			if( !$rotina ) $rotina = "resumo";
			
			if( $rotina == "resumo" ) {

				$this->arquivoTemplate = "cliente_cobranca_resumo.html";

			} else if( $rotina == "contratar" ) {

				$enviando = false;
				$exibeForm = true;
				
				if($acao == "cad" ) {
					$enviando = true;
				}

				if( $enviando ) {
				
					// Pega dominio padrão 
					$sSQL  = "select dominio_padrao from cftb_preferencias";
					$lista_dominop = $this->bd->obtemUnicoRegistro($sSQL);
					$dominioPadrao = $lista_dominop["dominio_padrao"]; 

					// Valida os dados
					
					// TODO: Colocar isso em uma funcao private
					$sSQL  = "SELECT ";
					$sSQL .= "   username ";
					$sSQL .= "FROM ";
					$sSQL .= "   cntb_conta ";
					$sSQL .= "WHERE ";
					$sSQL .= "   username = '".$this->bd->escape(trim(@$_REQUEST["username"]))."' ";
					$sSQL .= "   and tipo_conta = '". $this->bd->escape(trim(@$_REQUEST["tipo"])) ."' ";					
					$sSQL .= "   and dominio = '".$dominioPadrao."' ";
					$sSQL .= "ORDER BY ";
					$sSQL .= "   username ";
					
					$lista_user = $this->bd->obtemUnicoRegistro($sSQL);

					if(@$lista_user["username"]){
						// ver como processar 
						$erros[] = "Já existe outra conta cadastrada com esse usermane";
					}
					
					// Se nao tiver erros faz o cadastro
					if( !count($erros) ) {
					
						// pega id_cliente_prodruto
						$id_cliente_produto = $this->bd->proximoID("cbsq_id_cliente_produto");
						
						// Insere no banco de dados

						$sSQL  = "INSERT INTO ";
						$sSQL .= "   cbtb_cliente_produto( ";
						$sSQL .= "      id_cliente_produto,id_cliente, id_produto ) ";
						$sSQL .= "   VALUES (";
						$sSQL .= "     '" . $id_cliente_produto . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["id_cliente"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["id_produto"]) . "' ";
						$sSQL .= "     )";						
						
						$this->bd->consulta($sSQL);  
						//if( $this->bd->obtemErro() ) {
						//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
						//	echo "SQL: $sSQL <br>\n";
						//}
								
						$senhaCr = $this->criptSenha($this->bd->escape(trim(@$_REQUEST["senha"])));
						
						$id_conta = $this->bd->proximoID("cnsq_id_conta");
						
						$sSQL  = "INSERT INTO ";
						$sSQL .= "   cntb_conta( ";
						$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript) ";
						$sSQL .= "   VALUES (";
						$sSQL .= "			'".$id_conta."', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
						$sSQL .= "     '" . $dominioPadrao . "', ";
						$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo"])) . "', ";
						$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
						$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
						$sSQL .= "     '" .	$id_cliente_produto . "', ";
						$sSQL .= "     '" . $senhaCr . "' ";
						$sSQL .= "     )";						
												
						$this->bd->consulta($sSQL);  
						//if( $this->bd->obtemErro() ) {
						//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
						//	echo "SQL: $sSQL <br>\n";
						//}
						
						
						if ($email_igual == "1"){
						
							$id_conta = $this->bd->proximoID("cnsq_id_conta");
							
							$sSQL  = "INSERT INTO ";
							$sSQL .= "   cntb_conta( ";
							$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript) ";
							$sSQL .= "   VALUES (";
							$sSQL .= "			'". $id_conta. "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
							$sSQL .= "     '" . $dominioPadrao . "', ";
							$sSQL .= "     'E', ";
							$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
							$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
							$sSQL .= "     '" .	$id_cliente_produto . "', ";
							$sSQL .= "     '" . $senhaCr . "' ";
							$sSQL .= "     )";						
																		
							$this->bd->consulta($sSQL);  
							//if( $this->bd->obtemErro() ) {
							//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
							//	echo "SQL: $sSQL <br>\n";
							//}
							
							
							$id_produto = @$_REQUEST['id_produto'];
							$prod = $this->obtemProduto($id_produto);	
							
							if ($prod["quota_por_conta"] == "" || !$prod ){
								$quota = "0";
							}else {
								$quota = $produto["quota_por_conta"];
							}
							
							$sSQL  = "INSERT INTO ";
							$sSQL .= "	cntb_conta_email( ";
							$sSQL .= "		username, tipo_conta, dominio, quota, email) ";
							$sSQL .= "VALUES (";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
							$sSQL .= "     'E', ";
							$sSQL .= "     '" . $dominioPadrao . "', ";
							$sSQL .= "     '$quota', ";
							$sSQL .= "     '". $this->bd->escape(@$_REQUEST["username"])."@". $dominioPadrao ."' ";
							$sSQL .= " )";
							
							$this->bd->consulta($sSQL);
							//if( $this->bd->obtemErro() ) {
							//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
							//	echo "SQL: $sSQL <br>\n";
							//}

							
						
						}
						
						
						$tipo = @$_REQUEST["tipo"];
						
						switch($tipo) {
							case 'D':
								// PRODUTO DISCADO
								break;	
							case 'BL':
								// PRODUTO BANDA LARGA
								$tipo_de_ip = $this->bd->escape(trim(@$_REQUEST["selecao_ip"]));
								if($tipo_de_ip == "A"){
									$nas = $this->obtemNAS($_REQUEST["id_nas"]);
									if( $nas["tipo_nas"] == "I" ) {
									   // Cadastrar REDE em cntb_conta
									   $rede_disponivel = $this->obtemRede($nas["id_nas"]);
									   $rede_disp = $rede_disponivel["rede"];
									   $ip_disp = "NULL";
									} else if( $nas["tipo_nas"] == "P" ) {
									   // Cadastrar IPADDR em cntb_conta
									   $ip_disponivel = $this->obtemIP($nas["id_nas"]);
									   $ip_disp = $ip_disponivel["ipaddr"];
									   $rede_disp = "NULL";
									
																			
									}
									
								}
								
								
								if($rede_disp != "NULL"){
								
									$rede_disp = "'".$rede_disponivel["rede"]."'";
								
								
								}
								
								if($ip_disp !="NULL"){
								
									$ip_disp = "'".$ip_disponivel["ipaddr"]."'";
								
								
								}
								
								$id_produto = $this->bd->escape(@$_REQUEST["id_produto"]);
								$bandaUp_dow = $this->obtemDowUp($id_produto);
								$MAC = @$_REQUEST["mac"];
								
								if($MAC ==""){
									$_MAC = "NULL";
								}else {
									$_MAC = "'".$MAC."'";
								}
								
								$id_conta_banda_larga = $this->bd->proximoID("clsq_id_conta_bandalarga_seq");
								
								// INSERE EM CNTB_CONTA_BANDALARGA
								$sSQL  = "INSERT INTO ";
								$sSQL .= "   cntb_conta_bandalarga( ";
								$sSQL .= "      username, ";
								$sSQL .= "      tipo_conta, ";
								$sSQL .= "      dominio, ";
								$sSQL .= "      id_pop, ";
								$sSQL .= "      tipo_bandalarga, ";
								$sSQL .= "      ipaddr, ";
								$sSQL .= "      rede, ";
								$sSQL .= "      upload_kbps, ";
								$sSQL .= "      download_kbps, ";
								$sSQL .= "      status, ";
								$sSQL .= "      id_nas, ";
								$sSQL .= "      mac ";
								$sSQL .= ") ";
								$sSQL .= "   VALUES (";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"])  . "', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo"])). "', ";
								$sSQL .= "     '" . $dominioPadrao . "', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_pop"])) . "', ";
								$sSQL .= "     '" . $nas["tipo_nas"] . "', ";
								$sSQL .= "     " . $ip_disp . ", ";
								$sSQL .= "     " . $rede_disp . ", ";
								$sSQL .= "     '" . $bandaUp_dow["banda_upload_kbps"] . "', ";
								$sSQL .= "     '" . $bandaUp_dow["banda_download_kbps"] . "', ";
								$sSQL .= "     'A', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_nas"])) . "', ";
								$sSQL .= "     ". $_MAC ." ";
								$sSQL .= "     )";						
								
								$this->bd->consulta($sSQL);  
								//if( $this->bd->obtemErro() ) {
								//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
								//	echo "SQL: $sSQL <br>\n";
								//}
								
								break;
								
							case 'H':
								// PRODUTO HOSPEDAGEM
								
								break;

						}
						
						
						
						
						// Envia instrucao pra spool
						if ($nas["tipo_nas"] == "I"){
						
							$id_nas = $_REQUEST["id_nas"];
							$banda_upload_kbps = $bandaUp_dow["banda_upload_kbps"];
							$banda_download_kbps = $bandaUp_dow["banda_download_kbps"];
							$rede = $rede_disponivel["rede"];
							$mac = $_REQUEST["mac"];
							
							$sSQL  = "SELECT ";
							$sSQL .= "   id_nas, nome, ip, tipo_nas ";
							$sSQL .= "FROM ";
							$sSQL .= "   cftb_nas ";
							$sSQL .= "WHERE ";
							$sSQL .= "   id_nas = '$id_nas'";
							//echo "SQL : " . $sSQL . "<br>\n";
							
							$nas = $this->bd->obtemUnicoRegistro($sSQL);
							$this->tpl->atribui("n",$nas);
							$this->tpl->atribui("tipo_nas",$nas["tipo_nas"]);

							$r =new RedeIP($rede);
							$ip_gateway = $r->minHost();
							$ip_cliente	= $r->maxHost(); // TODO: ObtemProximoIP();
							$mascara    = $r->mascara();
							
							$this->tpl->atribui("ip_gateway",$ip_gateway);
							$this->tpl->atribui("mascara",$mascara);
							$this->tpl->atribui("ip_cliente",$ip_cliente);
							
							
							$this->tpl->atribui("tipo",$tipo);
							$destino = $nas['ip'];	
							
						
							$this->spool->bandalargaAdicionaRede($destino,$rede,$id_conta,$mac,$banda_upload_kbps,$banda_download_kbps);
							
							
							
							
							
						
							
						}
							// LISTA DE POPS
							$sSQL  = "SELECT ";
							$sSQL .= "   id_pop, nome ";
							$sSQL .= "FROM ";
							$sSQL .= "   cftb_pop ";
							$sSQL .= "WHERE ";
							$sSQL .= "   id_pop = '". $this->bd->escape(trim(@$_REQUEST["id_pop"])) ."'";
						
							$lista_pops = $this->bd->obtemUnicoRegistro($sSQL);
							
							
							
							

						
						// Joga a mensagem de produto contratado com sucesso.
						$this->tpl->atribui("username",@$_REQUEST["username"]);
						//$this->tpl->atribui("tipo_produto",$tipo_produto);
						$this->tpl->atribui("pop",$lista_pops["nome"]);
						$this->tpl->atribui("nas",$nas["nome"]);
						$this->tpl->atribui("mac",$_MAC);
						$this->tpl->atribui("ip",$ip_disp);
						
						
						
						
						
						
						
						
						$this->arquivoTemplate="cliente_cobranca_intro.html";
											
						
						return;
						$exibeForm = false;
						
						
						
					}
					
				
				}
				
				$this->tpl->atribui("erros",$erros);

			
			
				if( $exibeForm ) {

					$this->arquivoTemplate = "cliente_cobranca_contratar.html";

					$sSQL  = "SELECT ";
					$sSQL .= "   dominio,id_cliente ";
					$sSQL .= "FROM ";
					$sSQL .= "   dominio ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_cliente = '".$this->bd->escape($id_cliente)."' ";
					$sSQL .= "ORDER BY ";
					$sSQL .= "   DOMINIO ";

					$lista_dominios = $this->bd->obtemRegistros($sSQL);

					$sSQL  = "SELECT ";
					$sSQL .= "   id_produto,nome,descricao,tipo,valor ";
					$sSQL .= "";
					$sSQL .= "FROM ";
					$sSQL .= "   prtb_produto ";
					$sSQL .= "WHERE ";
					$sSQL .= "   disponivel is true ";
					$sSQL .= "";

					$cond_discado    = "   AND tipo='D' ";
					$cond_bandalarga = "   AND tipo='BL' ";
					$cond_hospedagem = "   AND tipo='H' ";

					$ordem = "ORDER BY nome ";

					$lista_discado    = $this->bd->obtemRegistros("$sSQL $cond_discado $ordem");
					$lista_bandalarga = $this->bd->obtemRegistros("$sSQL $cond_bandalarga $ordem");
					$lista_hospedagem = $this->bd->obtemRegistros("$sSQL $cond_hospedagem $ordem");

					$this->tpl->atribui("lista_discado",$lista_discado);
					$this->tpl->atribui("lista_bandalarga",$lista_bandalarga);
					$this->tpl->atribui("lista_hospedagem",$lista_hospedagem);

					// LISTA DE POPS
					$sSQL  = "SELECT ";
					$sSQL .= "   id_pop, nome ";
					$sSQL .= "FROM ";
					$sSQL .= "   cftb_pop ";
					$sSQL .= "ORDER BY ";
					$sSQL .= "   nome";

					$lista_pops = $this->bd->obtemRegistros($sSQL);
					$this->tpl->atribui("lista_pops",$lista_pops);

					// LISTA DE NAS
					$sSQL  = "SELECT ";
					$sSQL .= "   id_nas, nome, ip, tipo_nas ";
					$sSQL .= "FROM ";
					$sSQL .= "   cftb_nas ";
					$sSQL .= "WHERE ";
					$sSQL .= "   tipo_nas = 'I' OR tipo_nas = 'P' ";
					$sSQL .= "ORDER BY ";
					$sSQL .= "   tipo_nas,nome ";

					global $_LS_TIPO_NAS;


					$lista_nas = $this->bd->obtemRegistros($sSQL);
					for($i=0;$i<count($lista_nas);$i++) {
					   $lista_nas[$i]["tp"] = $_LS_TIPO_NAS[ $lista_nas[$i]["tipo_nas"] ];
					}

					$this->tpl->atribui("lista_nas",$lista_nas);
					
					
					$this->tpl->atribui("username",@$_REQUEST["username"]);
					$this->tpl->atribui("dominio",@$_REQUEST["dominio"]);
					$this->tpl->atribui("id_pop",@$_REQUEST["id_pop"]);
					$this->tpl->atribui("id_nas",@$_REQUEST["id_nas"]);
					$this->tpl->atribui("selecao_ip",@$_REQUEST["selecao_ip"]);
					$this->tpl->atribui("endereco_ip",@$_REQUEST["endereco_ip"]);
					$this->tpl->atribui("mac",@$_REQUEST["mac"]);
					
					$this->tpl->atribui("id_produto",@$_REQUEST["id_produto"]);
					//$this->tpl->atribui("",@$_REQUEST[""]);
					//$this->tpl->atribui("",@$_REQUEST[""]);
					//$this->tpl->atribui("",@$_REQUEST[""]);
					//$this->tpl->atribui("",@$_REQUEST[""]);
					//$this->tpl->atribui("",@$_REQUEST[""]);

				}

				
				
			} else if( $rotina == "relatorio" ) {
				

				$this->arquivoTemplate = "cliente_cobranca_relatorio.html";
			
			} else if( $rotina == "cad_clinte_produto" ){
			
			
			
			
			}
			
			
			
		
		
		} else if ($op == "produto") {


			// PRECISA PASSAR O TIPO PRO MENU
			//$tipo = @$_REQUEST["tipo"];
			
			
			//SELECTS PARA POPULAR OS CAMPOS DROP-DOWN
						
			//Lista de Clientes
			//$aSQL  = "SELECT ";
			//$aSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
			//$aSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
			//$aSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
			//$aSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
			//$aSQL .= "   ativo,obs ";
			//$aSQL .= "FROM cltb_cliente ";
			
			
			//Lista de Produtos
			//$bSQL  = "SELECT ";
			//$bSQL .= "   id_produto, nome, descricao, tipo, ";
			//$bSQL .= "   valor, disponivel ";
			//$bSQL .= "FROM prtb_produto ";
			
			
			//Lista de POP
			//$cSQL  = "SELECT ";
			//$cSQL .= "   id_pop, nome, info, tipo, id_pop_ap ";
			//$cSQL .= "FROM cftb_pop ";
			
					
			//Lista de NAS
			//$dSQL  = "SELECT ";
			//$dSQL .= "   id_nas, nome, ip, secret, tipo_nas ";
			//$dSQL .= "FROM cftb_nas ";
			//$dSQL .= "WHERE id_nas = '$id_nas'";
			
			//Pega Provedor Padrão
			//$eSQL  = "SELECT ";
			//$eSQL .= "	id_provedor, dominio_padrao, nome, localidade ";
			//$eSQL .= "FROM ";
			//$eSQL .= "cftb_provedor ";
			//$eSQL .= "WHERE ";
			//$eSQL .= "id_provedor = '1'";
			
			//Lista Dominios
			//$fSQL  = "SELECT ";
			//$fSQL .= "	dominio, id_cliente ";
			//$fSQL .= "FROM ";
			//$fSQL .= "	dominio ";
			//$fSQL .= "WHERE ";
			//$fSQL .= "	id_cliente = '". $this->bd->escape(@$_REQUEST["id_cliente"]) ."' ";
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			//$lista_clientes = $this->bd->obtemRegistros($aSQL);
			//$lista_produtos = $this->bd->obtemRegistros($bSQL);
			//$lista_pop = $this->bd->obtemRegistros($cSQL);
			//$lista_nas = $this->bd->obtemRegistros($dSQL);
			//$provedor_padrao = $this->bd->obtemUnicoRegistro($eSQL);
			//$dominios = $this->bd->obtemUnicoRegistro($fSQL);
			
			
			//Atribuição de valores ao template
			//$this->tpl->atribui($lista_clientes);
			//$this->tpl->atribui($lista_produtos);
			//$this->tpl->atribui($lista_pop);
			//$this->tpl->atribui($lista_nas);
			//$this->tpl->atribui($provedor_padrao);
			//$this->tpl->atribui($dominios);
			
			//$this->tpl->atribui("tipo",$tipo);
			
			
			$sSQL = "SELECT p.nome,cp.id_cliente_produto FROM cbtb_cliente_produto cp INNER JOIN prtb_produto p USING( id_produto ) WHERE cp.id_cliente='$id_cliente' AND p.tipo = '$tipo' ";
			$produtos = $this->bd->obtemRegistros($sSQL);
			
			for($i=0;$i<count($produtos);$i++) {

			   $id_cp = $produtos[$i]["id_cliente_produto"];
			   $sSQL = "select username from cntb_conta WHERE id_cliente_produto = '$id_cp'";
			   $contas = $this->bd->obtemRegistros($sSQL);
			   $produtos[$i]["contas"] = $contas;
			}
			
			
			$this->tpl->atribui("produtos",$produtos);
			
			$this->arquivoTemplate = "cliente_produto.html";
			
		
		} else if ($op == "helpdesk") {
		
			$this->arquivoTemplate = "cliente_helpdesk.html";
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
			
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
	
public function __destruct() {
      	parent::__destruct();
}
	
public function contrataProduto(){

	
}
public function obtemProduto($id_produto){

    $zSQL = "SELECT * FROM prtb_produto where id_produto = '". $this->bd->escape(@$_REQUEST["id_produto"]) ."'";
	$produto = $this->bd->obtemUnicoRegistro($zSQL);
	return;

}
	

				
				
}
			














?>
