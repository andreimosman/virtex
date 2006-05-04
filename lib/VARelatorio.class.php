<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );
require_once( "jpgraph.php" );
require_once( "jpgraph_line.php" );
require_once( "jpgraph_bar.php" );

class VARelatorio extends VirtexAdmin {

	public function VARelatorio() {
		parent::VirtexAdmin();
	
	
	}
		
	public function processa($op=null) {
	
	if($op == "fatura"){
		$this->arquivoTemplate = "relatorio_fatura.html";
		
		$acao = @$_REQUEST["acao"];
		
		$tipo_relatorio = @$_REQUEST["tipo_relatorio"];
		if (!$tipo_relatorio) $tipo_relatorio = 'PG';
		
		$data_ini = @$_REQUEST["data_ini"];
		$data_fim = @$_REQUEST["data_fim"];
					
		if(!$data_ini) $data_ini = date("d") . "/" . date("m") ."/". date("Y");
		if(!$data_fim) $data_fim = date("d") . "/" . date("m") ."/". date("Y");
		
		$this->tpl->atribui("tipo_relatorio", $tipo_relatorio);
		$this->tpl->atribui("data_ini", $data_ini);
		$this->tpl->atribui("data_fim", $data_fim);
		$this->tpl->atribui("acao", $acao);
		
		
		//TODO: comentas as duas linhas abaixo que serão usadas somente pra evitar erros até o termino do projeto
		$eSQL = "";
		$sSQL = "";
		
		if ($acao == "consultar"){ 
			//echo "fdsafdsafdsa<br>";
			//Pega datas nevessárias Á consulta
						
			
			list($d, $m, $a) = explode("/", $data_ini);
			$data_ini = "$a-$m-$d";

			list($d, $m, $a) = explode("/", $data_fim);
			$data_fim = "$a-$m-$d";

						
			
			if($tipo_relatorio == "PG") { //Faturas pagas
				
				$sSQL =  "SELECT ";
				$sSQL .= "	cl.id_cliente, cl.nome_razao, cp.id_cliente_produto, ";
				$sSQL .= "	cp.id_produto, ft.data, ft.data_pagamento, ";
				$sSQL .= "	ft.valor, ft.desconto, ft.reagendamento, ";
				$sSQL .= "	ft.valor_pago, ft.data, ft.desconto, ft.acrescimo ";
				$sSQL .= "FROM ";
				$sSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$sSQL .= "WHERE ";
				$sSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$sSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$sSQL .= "	ft.status = 'P' AND ";
				$sSQL .= "	ft.data_pagamento BETWEEN '$data_ini' AND '$data_fim'";
				$sSQL .= "ORDER BY cl.id_cliente";


				$eSQL =  "SELECT ";
				$eSQL .= "	COUNT(ft.data) AS faturas, "; 
				$eSQL .= "	SUM(ft.valor) as estimativa, ";
				$eSQL .= "	SUM(ft.desconto) as descontos, ";
				$eSQL .= "	SUM(ft.acrescimo) as acrescimos, "; 
				$eSQL .= "	COUNT(ft.reagendamento) as reagendamentos, ";
				$eSQL .= "	SUM(ft.valor_pago) as recebido ";
				$eSQL .= "FROM ";
				$eSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$eSQL .= "WHERE ";
				$eSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$eSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$eSQL .= "	ft.status = 'P' AND ";
				$eSQL .= "	ft.data_pagamento BETWEEN '$data_ini' AND '$data_fim'";											
				
			} else if($tipo_relatorio == "PG+") { //Pagas incluindo adesão
				
				//TODO: ainda falta fazer as pagas incluindo adesão
				$sSQL =  "SELECT ";
				$sSQL .= "	cl.id_cliente, cl.nome_razao, cp.id_cliente_produto, ";
				$sSQL .= "	cp.id_produto, ft.data, ft.data_pagamento, ";
				$sSQL .= "	ft.valor, ft.desconto, ft.reagendamento, ";
				$sSQL .= "	ft.valor_pago, ft.data, ft.desconto, ft.acrescimo ";
				$sSQL .= "FROM ";
				$sSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$sSQL .= "WHERE ";
				$sSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$sSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$sSQL .= "	ft.status = 'P' AND ";
				$sSQL .= "	ft.data_pagamento BETWEEN '2006-04-15' AND '2006-04-15' ";
				$sSQL .= "ORDER BY cl.id_cliente";


				$eSQL =  "SELECT ";
				$eSQL .= "	COUNT(ft.data) AS faturas, "; 
				$eSQL .= "	SUM(ft.valor) as estimativa, ";
				$eSQL .= "	SUM(ft.desconto) as descontos, ";
				$eSQL .= "	SUM(ft.acrescimo) as acrescimos, "; 
				$eSQL .= "	COUNT(ft.reagendamento) as reagendamentos, ";
				$eSQL .= "	SUM(ft.valor_pago) as recebido ";
				$eSQL .= "FROM ";
				$eSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$eSQL .= "WHERE ";
				$eSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$eSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$eSQL .= "	ft.status = 'P' AND ";
				$eSQL .= "	ft.data_pagamento BETWEEN '$data_ini' AND '$data_fim'";
				

			} else if($tipo_relatorio == "PD+") { //Pagas com desconto

				$sSQL = "SELECT ";
				$sSQL .= "	cl.id_cliente, cl.nome_razao, cp.id_cliente_produto, ";
				$sSQL .= "	cp.id_produto, ft.data, ft.data_pagamento, ";
				$sSQL .= "	ft.valor, ft.desconto, ft.reagendamento, ";
				$sSQL .= "	ft.valor_pago, ft.data, ft.desconto, ft.acrescimo ";
				$sSQL .= "FROM ";
				$sSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$sSQL .= "WHERE ";
				$sSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$sSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$sSQL .= "	ft.status = 'P' AND ";
				$sSQL .= "	ft.desconto > 0 AND ";
				$sSQL .= "	ft.data_pagamento BETWEEN '$data_ini' AND '$data_fim' ";
				$sSQL .= "ORDER BY cl.id_cliente";

				$eSQL =  "SELECT ";
				$eSQL .= "	COUNT(ft.data) AS faturas, "; 
				$eSQL .= "	SUM(ft.valor) as estimativa, ";
				$eSQL .= "	SUM(ft.desconto) as descontos, ";
				$eSQL .= "	SUM(ft.acrescimo) as acrescimos, "; 
				$eSQL .= "	COUNT(ft.reagendamento) as reagendamentos, ";
				$eSQL .= "	SUM(ft.valor_pago) as recebido ";
				$eSQL .= "FROM ";
				$eSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$sSQL .= "WHERE ";
				$sSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$sSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$sSQL .= "	ft.status = 'P' AND ";
				$sSQL .= "	ft.desconto > 0 AND ";
				$sSQL .= "	ft.data_pagamento BETWEEN '$data_ini' AND '$data_fim' ";

			} else if($tipo_relatorio == "AT") { //Em atraso

				$sSQL = "SELECT ";
				$sSQL .= "	cl.id_cliente, cl.nome_razao, cp.id_cliente_produto, ";
				$sSQL .= "	cp.id_produto, ft.data, ft.data_pagamento, ";
				$sSQL .= "	ft.valor, ft.desconto, ft.reagendamento, ";
				$sSQL .= "	ft.valor_pago, ft.data, ft.desconto, ft.acrescimo ";
				$sSQL .= "FROM ";
				$sSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$sSQL .= "WHERE ";
				$sSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$sSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$sSQL .= "	((ft.data < CURRENT_DATE AND ft.status = 'A' AND ft.data BETWEEN '$data_ini' AND '$data_fim' ) OR ";
				$sSQL .= "	(ft.reagendamento < CURRENT_DATE AND ft.status = 'R' AND ft.reagendamento BETWEEN '$data_ini' AND '$data_fim'))";
				$sSQL .= "ORDER BY cl.id_cliente";


				$eSQL =  "SELECT ";
				$eSQL .= "	COUNT(ft.data) AS faturas, "; 
				$eSQL .= "	SUM(ft.valor) as estimativa, ";
				$eSQL .= "	SUM(ft.desconto) as descontos, ";
				$eSQL .= "	SUM(ft.acrescimo) as acrescimos, "; 
				$eSQL .= "	COUNT(ft.reagendamento) as reagendamentos, ";
				$eSQL .= "	SUM(ft.valor_pago) as recebido ";
				$eSQL .= "FROM ";
				$eSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$eSQL .= "WHERE ";
				$eSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$eSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$eSQL .= "	((ft.data < CURRENT_DATE AND ft.status = 'A' AND ft.data BETWEEN '$data_ini' AND '$data_fim' ) OR ";
				$eSQL .= "	(ft.reagendamento < CURRENT_DATE AND ft.status = 'R' AND ft.reagendamento BETWEEN '$data_ini' AND '$data_fim'))";

			} else if($tipo_relatorio == "AD") { //Adesões
				//TODO: Fazer consulta de adesões
				$sSQL =  "SELECT";
				$sSQL .= "	cl.id_cliente, cl.nome_razao, cp.id_cliente_produto, cp.id_produto, ";
				$sSQL .= "	ft.data, ft.data_pagamento, ft.valor, ft.desconto, ft.reagendamento, ft.valor_pago";
				$sSQL .= "FROM ";
				$sSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft";
				$sSQL .= "WHERE ";
				$sSQL .= "cl.id_cliente = cp.id_cliente AND ft.id_cliente_produto = cp.id_cliente_produto AND ft.status = 'R' ";
				$sSQL .= "AND ft.data > '$data_ini' AND dt.data < '$data_fim' AND ";

			} else if($tipo_relatorio == "AB") { //Em aberto incluindo atrazadas
				//TODO: Fazer consulta de adesões em atraso
				$sSQL = "SELECT ";
				$sSQL .= "	cl.id_cliente, cl.nome_razao, cp.id_cliente_produto, ";
				$sSQL .= "	cp.id_produto, ft.data, ft.data_pagamento, ";
				$sSQL .= "	ft.valor, ft.desconto, ft.reagendamento, ";
				$sSQL .= "	ft.valor_pago, ft.data, ft.desconto, ft.acrescimo ";
				$sSQL .= "FROM ";
				$sSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$sSQL .= "WHERE ";
				$sSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$sSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$sSQL .= "	(((ft.data < CURRENT_DATE AND ft.status = 'A' AND ft.data BETWEEN $data_ini AND $data_fim ) OR ";
				$sSQL .= "	(ft.reagendamento < CURRENT_DATE AND ft.status = 'R' AND ft.reagendamento BETWEEN '$data_ini' AND '$data_fim'))) ";
				$sSQL .= "	OR ft.status = 'A'";
				$sSQL .= "ORDER BY cl.id_cliente";


				$eSQL =  "SELECT ";
				$eSQL .= "	cl.id_cliente, ";
				$eSQL .= "	COUNT(ft.data) AS faturas, "; 
				$eSQL .= "	SUM(ft.valor) as estimativa, ";
				$eSQL .= "	SUM(ft.desconto) as descontos, ";
				$eSQL .= "	SUM(ft.acrescimo) as acrescimos, "; 
				$eSQL .= "	COUNT(ft.reagendamento) as reagendamentos, ";
				$eSQL .= "	SUM(ft.valor_pago) as recebido ";
				$eSQL .= "FROM ";
				$eSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$eSQL .= "WHERE ";
				$eSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$eSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$eSQL .= "	(((ft.data < CURRENT_DATE AND ft.status = 'A' AND ft.data BETWEEN $data_ini AND $data_fim ) OR ";
				$eSQL .= "	(ft.reagendamento < CURRENT_DATE AND ft.status = 'R' AND ft.reagendamento BETWEEN '$data_ini' AND '$data_fim'))) ";
				$eSQL .= "	OR ft.status = 'A'";
				$eSQL .= "GROUP BY cl.id_cliente";
				
			} else if($tipo_relatorio == "AB-") { //Em aberto excluindo atrazadas
				
				$sSQL = "SELECT ";
				$sSQL .= "	cl.id_cliente, cl.nome_razao, cp.id_cliente_produto, ";
				$sSQL .= "	cp.id_produto, ft.data, ft.data_pagamento, ";
				$sSQL .= "	ft.valor, ft.desconto, ft.reagendamento, ";
				$sSQL .= "	ft.valor_pago, ft.data, ft.desconto, ft.acrescimo ";
				$sSQL .= "FROM ";
				$sSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$sSQL .= "WHERE ";
				$sSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$sSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$sSQL .= "	(((ft.data < CURRENT_DATE AND ft.status = 'A' AND ft.data BETWEEN $data_ini AND $data_fim ) OR ";
				$sSQL .= "	(ft.reagendamento < CURRENT_DATE AND ft.status = 'R' AND ft.reagendamento BETWEEN '$data_ini' AND '$data_fim'))) ";
				$sSQL .= "	OR ft.status = 'A'";
				$sSQL .= "ORDER BY cl.id_cliente";


				$eSQL =  "SELECT ";
				$eSQL .= "	COUNT(ft.data) AS faturas, "; 
				$eSQL .= "	SUM(ft.valor) as estimativa, ";
				$eSQL .= "	SUM(ft.desconto) as descontos, ";
				$eSQL .= "	SUM(ft.acrescimo) as acrescimos, "; 
				$eSQL .= "	COUNT(ft.reagendamento) as reagendamentos, ";
				$eSQL .= "	SUM(ft.valor_pago) as recebido ";
				$eSQL .= "FROM ";
				$eSQL .= "	cltb_cliente cl, cbtb_cliente_produto cp, cbtb_faturas ft ";
				$eSQL .= "WHERE ";
				$eSQL .= "	cl.id_cliente = cp.id_cliente AND ";
				$eSQL .= "	ft.id_cliente_produto = cp.id_cliente_produto AND ";
				$eSQL .= "	(((ft.data < CURRENT_DATE AND ft.status = 'A' AND ft.data BETWEEN $data_ini AND $data_fim ) OR ";
				$eSQL .= "	(ft.reagendamento < CURRENT_DATE AND ft.status = 'R' AND ft.reagendamento BETWEEN '$data_ini' AND '$data_fim'))) ";
				$eSQL .= "	OR ft.status = 'A'";
				$eSQL .= "GROUP BY cl.id_cliente";
				
			}


			
			//echo "<br>$sSQL<br>";
			//echo "<br>$eSQL<br>";
			
			$rel_faturas = $this->bd->obtemRegistros($sSQL);
			$rel_totais = $this->bd->obtemUnicoRegistro($eSQL);
			
			$this->tpl->atribui("rel_faturas", $rel_faturas);
			$this->tpl->atribui("rel_totais", $rel_totais);
			$this->arquivoTemplate = "relatorio_fatura.html";
			return;
		
		}
		

		
	
		//$this->arquivoTemplate = "cobranca_versaolight.html";
		
	} else if ($op == "cortesia"){
		//$this->arquivoTemplate = "cobranca_versaolight.html";
		$this->arquivoTemplate = "relatorio_cortesia.html";
					
		$acao = @$_REQUEST["acao"];
		
		$tipo_relatorio = @$_REQUEST["tipo_relatorio"];
		if (!$tipo_relatorio) $tipo_relatorio = "todos";
					
		$this->tpl->atribui("tipo_relatorio", $tipo_relatorio);
		$this->tpl->atribui("acao", $acao);
		
		if ($acao == "consultar") {
		
			//TODO: Fazer a entrada das SQL's
			
			if ($tipo_relatorio == "todos") {
			} else if($tipo_relatorio == "D") {
			} else if($tipo_relatorio == "H") {
			} else if($tipo_relatorio == "BL") {
			} else if($tipo_relatorio == "E") {
			}
		}
			
		
		
	} else if ($op == "geral"){
	// RELATORIO GERAL DE CLIENTES
			$erros = array();
			$inicial = @$_REQUEST['inicial'];
			$inicial_up = strtoupper($inicial);
			$inicial_lo = strtolower($inicial);
			$acao = @$_REQUEST['acao'];
																	
										
																
		if( !$inicial ){	
		
		
		
		
			if( $acao ){
				
				$aSQL  = "SELECT ";
				$aSQL .= "   cl.id_cliente, cl.data_cadastro, cl.nome_razao, cl.tipo_pessoa, ";
				$aSQL .= "   cl.rg_inscr, cl.rg_expedicao, cl.cpf_cnpj, cl.email, cl.endereco, cl.complemento, cl.id_cidade, ";
				$aSQL .= "   cl.cidade, cl.estado, cl.cep, cl.bairro, cl.fone_comercial, cl.fone_residencial, ";
				$aSQL .= "   cl.fone_celular, cl.contato, cl.banco, cl.conta_corrente, cl.agencia, cl.dia_pagamento, ";
				$aSQL .= "   cl.ativo, cl.obs, ";
				$aSQL .= "   c.id_cidade, c.cidade ";				
				$aSQL .= "FROM ";
				$aSQL .= "cltb_cliente cl, cftb_cidade c ";
				$aSQL .= "WHERE c.id_cidade = cl.id_cidade";

						
				$reg = $this->bd->obtemRegistros($aSQL);
																		
				$this->tpl->atribui("ult_cli",$reg);
			
			}else if (!$acao){

				$aSQL  = "SELECT ";
				$aSQL .= "   cl.id_cliente, cl.data_cadastro, cl.nome_razao, cl.tipo_pessoa, ";
				$aSQL .= "   cl.rg_inscr, cl.rg_expedicao, cl.cpf_cnpj, cl.email, cl.endereco, cl.complemento, cl.id_cidade, ";
				$aSQL .= "   cl.cidade, cl.estado, cl.cep, cl.bairro, cl.fone_comercial, cl.fone_residencial, ";
				$aSQL .= "   cl.fone_celular, cl.contato, cl.banco, cl.conta_corrente, cl.agencia, cl.dia_pagamento, ";
				$aSQL .= "   cl.ativo, cl.obs, ";
				$aSQL .= "   c.id_cidade, c.cidade ";
				$aSQL .= "FROM cltb_cliente cl, cftb_cidade c ";
				$aSQL .= "WHERE c.id_cidade = cl.id_cidade ";
				$aSQL .= "ORDER BY id_cliente DESC LIMIT (5)";
											
				$reg = $this->bd->obtemRegistros($aSQL);
											
				$this->tpl->atribui("ult_cli",$reg);
			}
	
	
		}else {
		
			$aSQL  = "SELECT ";
			$aSQL .= "   cl.id_cliente, cl.data_cadastro, cl.nome_razao, cl.tipo_pessoa, ";
			$aSQL .= "   cl.rg_inscr, cl.rg_expedicao, cl.cpf_cnpj, cl.email, cl.endereco, cl.complemento, cl.id_cidade, ";
			$aSQL .= "   cl.cidade, cl.estado, cl.cep, cl.bairro, cl.fone_comercial, cl.fone_residencial, ";
			$aSQL .= "   cl.fone_celular, cl.contato, cl.banco, cl.conta_corrente, cl.agencia, cl.dia_pagamento, ";
			$aSQL .= "   cl.ativo, cl.obs, ";
			$aSQL .= "   c.id_cidade, c.cidade ";
			$aSQL .= "FROM cltb_cliente cl, cftb_cidade c ";
			$aSQL .= "WHERE (cl.nome_razao ilike '$inicial_up%' OR cl.nome_razao ilike '$inicial_lo%') AND c.id_cidade = cl.id_cidade ";
												
			$reg = $this->bd->obtemRegistros($aSQL);
												
			$this->tpl->atribui("ult_cli",$reg);

		
		
		}
	
	
		$this->arquivoTemplate = "relatorio_clientes.html";
	
	
	} else if ($op == "estat"){



		$this->arquivoTemplate = "cobranca_versaolight.html";
		//$this->arquivoTemplate = "relatorio_estat.html";





	} else if ($op == "filtro"){
	
			
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		$this->tpl->atribui("op",$op);
	
		$this->arquivoTemplate = "cobranca_versaolight.html";
		//$this->arquivoTemplate = "relatorio_filtro.html";
		
		
	} else if ($op == "config"){
	
		$pg = @$_REQUEST["pg"];
	
		switch($pg) {
			
			case 'carga_ap':
			
				$tipo = "AP";
				break;

			case 'carga_pop':
			
				$tipo = "POP";
				break;
				
			case 'carga_nas':
				$tipo = "NAS";
			
				break;
				
		}
		
		
		//$carga = $this->bd->obtemRegistros($sSQL);
		$carga = $this->obtemCarga($tipo);

		$bgcolor1="#FFFFFF";
		$bgcolor2="#F1F1F1";
		$bgcolor=$bgcolor1;

		for($x=0;$x<count($carga);$x++) {
			$carga[$x]["carga_up"] = $carga[$x]["carga_up"] ? $carga[$x]["carga_up"] : "-";
			$carga[$x]["carga_down"] = $carga[$x]["carga_down"] ? $carga[$x]["carga_down"] : "-";
			$carga[$x]["bgcolor"] = $bgcolor;
			$bgcolor = $bgcolor == $bgcolor1 ? $bgcolor2 : $bgcolor1;
		}
		
		$this->tpl->atribui("tipo",$tipo);
		$this->tpl->atribui("carga",$carga);

		$this->arquivoTemplate = "relatorio_config_carga.html";

	} else if ($op == "grafico"){
		$this->tpl->atribui("grop",@$_REQUEST["grop"]); 	// OP enviada para o gráfico
		$this->tpl->atribui("tipo",@$_REQUEST["tipo"]); 	// Tipo do gráfico
		$this->tpl->atribui("rl",@$_REQUEST["rl"]);		// Parametro extra e relatório

		$this->arquivoTemplate = "relatorio_grafico.html";


	} else if ($op == "produto_cliente"){
		
		$acao = @$_REQUEST["acao"];
		
		if (!$acao) $acao = "geral";		
		
		if ($acao == "geral"){
		
			$sSQL  = "SELECT ";
			$sSQL .= " p.id_produto,p.nome,p.tipo,p.valor,p.disponivel,num_contratos ";
			$sSQL .= "FROM ";
			$sSQL .= " prtb_produto p INNER JOIN ";
			$sSQL .= "(SELECT p.id_produto,count(cp.id_produto) as num_contratos ";
			$sSQL .= " FROM prtb_produto p LEFT OUTER JOIN cbtb_contrato cp USING(id_produto) ";
			$sSQL .= "GROUP BY p.id_produto ";
			$sSQL .= " ) c USING(id_produto) ";
			$sSQL .= "ORDER BY num_contratos DESC";			
			
			
		}else if($acao == "sub_prd") {
		
			$id_produto = @$_REQUEST["id_produto"];
			
			$sSQL  = "SELECT ";
			$sSQL .= "	clt.id_cliente, clt.nome_razao, ";
			$sSQL .= "	prd.id_produto, prd.nome_produto, prd.tipo, ";
			$sSQL .= "	cnt.id_cliente_produto, cnt.valor_contrato ";
			$sSQL .= "FROM ";
			$sSQL .= "	(SELECT id_cliente_produto, valor_contrato FROM cbtb_contrato) cnt , ";
			$sSQL .= "	(SELECT id_produto, nome AS nome_produto, tipo FROM prtb_produto) prd, ";
			$sSQL .= "	(SELECT id_cliente, nome_razao, excluido FROM cltb_cliente) clt, ";
			$sSQL .= "	cbtb_cliente_produto as clp ";
			$sSQL .= "WHERE ";
			$sSQL .= "	clt.id_cliente = clp.id_cliente AND ";
			$sSQL .= "	prd.id_produto = $id_produto AND ";
			$sSQL .= "	clt.excluido = 'FALSE' AND ";
			$sSQL .= "	prd.id_produto = clp.id_produto AND ";
			$sSQL .= "	cnt.id_cliente_produto = clp.id_cliente_produto ";
			$sSQL .= "ORDER BY prd.tipo, prd.id_produto, clt.nome_razao ";

		}
		
		$relat = $this->bd->obtemRegistros($sSQL);
		
		//echo $sSQL;
		
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_produtos_clientes.html";

	
	} else if ($op == "tproduto_cliente"){
		
		$acao = @$_REQUEST["acao"];
		
		if (!$acao) $acao = "geral";
		
		if ($acao == "geral") {

			$sSQL  = "SELECT ";
			$sSQL .= " COUNT(cp.tipo_produto) as num_contratos, ";
			$sSQL .= " cp.tipo_produto as tipo ";
			$sSQL .= "FROM cbtb_contrato as cp ";
			$sSQL .= "GROUP BY cp.tipo_produto ";
			$sSQL .= "ORDER BY cp.tipo_produto ";
		
		} else if ($acao == "sub_tprd") {
		
			$tipo = @$_REQUEST["tipo"];
		
			$sSQL  = "SELECT ";
			$sSQL .= "	cl.id_cliente, cl.nome_razao, ";
			$sSQL .= "	cp.tipo_produto as tipo,  ";
			$sSQL .= "	pr.id_produto ";
			$sSQL .= "FROM  ";
			$sSQL .= "	cbtb_contrato as cp, ";
			$sSQL .= "	cltb_cliente as cl, ";
			$sSQL .= "	prtb_produto as pr, ";
			$sSQL .= "	cbtb_cliente_produto as clp ";
			$sSQL .= "WHERE  ";
			$sSQL .= "	pr.tipo = '$tipo' AND ";
			$sSQL .= "	clp.id_cliente = cl.id_cliente AND clp.id_produto = pr.id_produto AND ";
			$sSQL .= "	cl.excluido = 'FALSE' AND ";
			$sSQL .= "	cp.id_cliente_produto = clp.id_cliente_produto ";
			$sSQL .= "ORDER BY cl.nome_razao, cl.id_cliente  ";
		
		}
		
		//echo($sSQL);
		
		$relat = $this->bd->obtemRegistros($sSQL);
		
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_tipoprodutos_clientes.html";	
	
	} else if ($op == "cidade_cliente"){
		
		$acao = @$_REQUEST["acao"];
		
		if (!$acao) $acao = "geral";
		
		if ($acao == "geral") {		
			
			$sSQL  = "SELECT ";
			$sSQL .= "   cnt.id_cidade,cnt.num_clientes, cid.cidade, cid.uf ";
			$sSQL .= "FROM ";
			$sSQL .= "   cftb_cidade cid, ";
			$sSQL .= "   (SELECT ";
			$sSQL .= "      id_cidade,count(*) as num_clientes ";
			$sSQL .= "   FROM ";
			$sSQL .= "      cltb_cliente ";
			$sSQL .= "   GROUP BY ";
			$sSQL .= "      id_cidade) cnt ";
			$sSQL .= "WHERE ";
			$sSQL .= "   cid.id_cidade = cnt.id_cidade ";
			
		} else if($acao == "sub_cid") {
		
			$id_cidade = @$_REQUEST["id_cidade"];
		
			$sSQL  = "SELECT ";
			$sSQL .= "	cnt.id_cliente, ";
			$sSQL .= "	cnt.nome_razao, ";
			$sSQL .= "	cid.id_cidade, ";
			$sSQL .= "	cid.cidade, ";
			$sSQL .= "	cid.uf ";
			$sSQL .= "FROM ";
			$sSQL .= "	cltb_cliente as cnt, ";
			$sSQL .= "	(SELECT ";
			$sSQL .= "		id_cidade, ";
			$sSQL .= "		cidade, ";
			$sSQL .= "		uf ";
			$sSQL .= "	FROM ";
			$sSQL .= "		cftb_cidade ";
			$sSQL .= "	ORDER BY uf) cid ";
			$sSQL .= "WHERE ";
			$sSQL .= "	cnt.id_cidade = cid.id_cidade AND cnt.id_cidade = $id_cidade AND";
			$sSQL .= "	cnt.excluido = 'FALSE' ";
			$sSQL .= "ORDER BY cid.uf, cid.id_cidade, cnt.nome_razao";	
			
		}
		
		$relat = $this->bd->obtemRegistros($sSQL);	
		//echo $sSQL;
				
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_cidades_clientes.html";	
	
	}  else if ($op == "adesao"){
		
		
		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
		$periodo = @$_REQUEST["periodo"];
		
		if (!$acao) $acao = "geral";
		if (!$periodo) $periodo = "UA";
						
				
		if ($acao == "geral") {	
		
			
			switch($periodo) {
				
				case "UA":	//Ultimo ano
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_contratacao) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_contratacao) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_contratacao > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '12 month' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;
				
				case "US":	//Ultimo Simestre
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_contratacao) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_contratacao) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_contratacao > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '6 month' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;

				case "UT":	//Ultimo Treimestre
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_contratacao) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_contratacao) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_contratacao > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '3 month' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;

				case "UB":	//Ultimo Bimestre
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_contratacao) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_contratacao) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_contratacao > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '2 month' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;
				
				case "UM":	//Ultimo mês
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_contratacao) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_contratacao) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_contratacao > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '2 month' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;				
				
					
			}
			
			
					
		
		} else if($acao = "sub_ade") {
			
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];
									
			$data_inicial = date("Y-m-d", mktime(0,0,0,$mes, 1, $ano));
			$data_final = date("Y-m-d",mktime(0,0,0,$mes+1, 1, $ano));
		
			$sSQL  = "SELECT ";
			$sSQL .= "	clt.id_cliente, clt.nome_razao, cnt.data_contratacao, prd.id_produto, prd.nome, prd.tipo,  ";
			$sSQL .= "	EXTRACT('day' FROM cnt.data_contratacao) as dia, ";
			$sSQL .= "	EXTRACT('month' FROM cnt.data_contratacao) as mes, ";
			$sSQL .= "	EXTRACT('year' FROM cnt.data_contratacao) as ano ";
			$sSQL .= "FROM ";
			$sSQL .= "	prtb_produto prd, cbtb_contrato cnt, cbtb_cliente_produto cp, cltb_cliente clt ";
			$sSQL .= "WHERE  ";
			$sSQL .= "	cnt.data_contratacao >= '$data_inicial' AND cnt.data_contratacao < '$data_final' AND ";
			$sSQL .= "	cp.id_cliente_produto = cnt.id_cliente_produto AND ";
			$sSQL .= "	prd.id_produto = cp.id_produto AND clt.id_cliente = cp.id_cliente ";
			$sSQL .= "ORDER BY cnt.data_contratacao, clt.nome_razao ASC ";
		
		}
		
		
		$relat = $this->bd->obtemRegistros($sSQL);	
	
		/*
		$this->tpl->atribui("data_ini", $data_ini);
		$this->tpl->atribui("data_fim", $data_fim);*/
		
		global $_LS_TP_CONSULTA;
		$this->tpl->atribui("tpconsulta", $_LS_TP_CONSULTA);
		$this->tpl->atribui("periodo", $periodo);
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_adesoes.html";
			
		
		if ($extra == "grafico") {
					

			$relat = $this->bd->obtemRegistros($sSQL);
					
			$pontos = array();
			$legendas = array();

			for($i=0;$i<count($relat);$i++) {
			   $legendas[] = $relat[$i]["mes"] . "/" . $relat[$i]["ano"];
			   $pontos[] = $relat[$i]["num_contratos"];			   
			}
					
					
			// GERA O Gráfico

			//header("pragma: no-cache")
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,200,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			$grafico->title->Set('Relatório de Adesões');
			$grafico->img->SetMargin(40,40,40,40);
			
			//Imagem de Fundo
			$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
			$grafico->SetMarginColor("#f1f1f1");
						
			//Cria uma nova mostragem gráfica
			$gBarras = new BarPlot($pontos); 

			//$grafico->xaxis->SetMajTickPositions($positions,$titulos);

			// ajuste de cores 
			//$gBarras->SetFillColor("#ff0000");
			$gBarras->SetFillGradient("#aa0000","#ff0000",GRAD_MIDVER);

			
			//$gBarras->SetShadow("darkblue"); 
			//$grafico->xaxis->labels = $legendas;
			//$gBarras->label->Set($legendas);
			
			// título das barras
			$grafico->xaxis->SetTickLabels($legendas);

			// adicionar mostrage de barras ao gráfico 
			$grafico->Add($gBarras); 

			// imprimir gráfico 
			$grafico->Stroke();
			
			$this->arquivoTemplate = '';		
			return;
		
		}
	
	} else if($op == "cancelamento") {
	

		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
		$periodo = @$_REQUEST["periodo"];
		
		if (!$acao) $acao = "geral";
		if (!$periodo) $periodo = "UA";
						
				
		if ($acao == "geral") {	
		
			
			switch($periodo) {
				
				case "UA":	//Ultimo ano
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_status) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_status) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_status > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '12 month' ";
					$sSQL .= "	AND status = 'C' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;
				
				case "US":	//Ultimo Simestre
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_status) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_status) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_status > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '6 month' ";
					$sSQL .= "	AND status = 'C' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;

				case "UT":	//Ultimo Treimestre
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_status) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_status) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	AND status = 'C' ";
					$sSQL .= "	data_status > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '3 month' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;

				case "UB":	//Ultimo Bimestre
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_status) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_status) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_status > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '2 month' ";
					$sSQL .= "	AND status = 'C' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;
				
				case "UM":	//Ultimo mês
					$sSQL  = "SELECT ";
					$sSQL .= "	count(*) as num_contratos, ";
					$sSQL .= "	EXTRACT( 'month' FROM data_status) as mes, ";
					$sSQL .= "	EXTRACT( 'year' FROM data_status) as ano ";
					$sSQL .= "FROM ";
					$sSQL .= "	cbtb_contrato ";
					$sSQL .= "	AND status = 'C' ";
					$sSQL .= "WHERE ";
					$sSQL .= "	data_status > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '1 month' ";
					$sSQL .= "GROUP BY ano, mes ";
					$sSQL .= "ORDER BY ano, mes ";
				break;				
				
					
			}
			
			
					
		
		} else if($acao = "sub_ade") {
			
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];
									
			$data_inicial = date("Y-m-d", mktime(0,0,0,$mes, 1, $ano));
			$data_final = date("Y-m-d",mktime(0,0,0,$mes+1, 1, $ano));
		
			$sSQL  = "SELECT ";
			$sSQL .= "	clt.id_cliente, clt.nome_razao, cnt.data_contratacao, prd.id_produto, prd.nome, prd.tipo,  ";
			$sSQL .= "	EXTRACT('day' FROM cnt.data_status) as dia, ";
			$sSQL .= "	EXTRACT('month' FROM cnt.data_status) as mes, ";
			$sSQL .= "	EXTRACT('year' FROM cnt.data_status) as ano ";
			$sSQL .= "FROM ";
			$sSQL .= "	prtb_produto prd, cbtb_contrato cnt, cbtb_cliente_produto cp, cltb_cliente clt ";
			$sSQL .= "WHERE  ";
			$sSQL .= "	cnt.data_contratacao >= '$data_inicial' AND cnt.data_contratacao < '$data_final' AND ";
			$sSQL .= "	cp.id_cliente_produto = cnt.id_cliente_produto AND ";
			$sSQL .= "	prd.id_produto = cp.id_produto AND clt.id_cliente = cp.id_cliente ";
			$sSQL .= "ORDER BY cnt.data_contratacao, clt.nome_razao ASC ";
		
		}
		
		
		$relat = $this->bd->obtemRegistros($sSQL);	
	
		/*
		$this->tpl->atribui("data_ini", $data_ini);
		$this->tpl->atribui("data_fim", $data_fim);*/
		
		global $_LS_TP_CONSULTA;
		$this->tpl->atribui("tpconsulta", $_LS_TP_CONSULTA);
		$this->tpl->atribui("periodo", $periodo);
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_cancelamentos.html";
			
		
		if ($extra == "grafico") {
					

			$relat = $this->bd->obtemRegistros($sSQL);
					
			$pontos = array();
			$legendas = array();

			for($i=0;$i<count($relat);$i++) {
			   $legendas[] = $relat[$i]["mes"] . "/" . $relat[$i]["ano"];
			   $pontos[] = $relat[$i]["num_contratos"];			   
			}
					
					
			// GERA O Gráfico

			//header("pragma: no-cache")
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,200,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			$grafico->title->Set('Relatório de Cancelamentos');
			$grafico->img->SetMargin(40,40,40,40);
			
			//Imagem de Fundo
			$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
			$grafico->SetMarginColor("#f1f1f1");
						
			//Cria uma nova mostragem gráfica
			$gBarras = new BarPlot($pontos); 

			//$grafico->xaxis->SetMajTickPositions($positions,$titulos);

			// ajuste de cores 
			//$gBarras->SetFillColor("#ff0000");
			$gBarras->SetFillGradient("#aa0000","#ff0000",GRAD_MIDVER);

			
			//$gBarras->SetShadow("darkblue"); 
			//$grafico->xaxis->labels = $legendas;
			//$gBarras->label->Set($legendas);
			
			// título das barras
			$grafico->xaxis->SetTickLabels($legendas);

			// adicionar mostrage de barras ao gráfico 
			$grafico->Add($gBarras); 

			// imprimir gráfico 
			$grafico->Stroke();
			
			$this->arquivoTemplate = '';		
			return;
		
		}

	
		
	} else if ($op == "evolucao"){
	
	
	
		$sSQL  = "SELECT ";
		$sSQL .= "p.id_produto,p.nome,p.tipo,p.valor,p.disponivel,mes,num_contratos ";
		$sSQL .= "FROM ";
		$sSQL .= "prtb_produto p INNER JOIN ";
		$sSQL .= "(SELECT p.id_produto,count(cp.id_produto) as num_contratos, EXTRACT( 'month' from data_contratacao) as mes ";
		$sSQL .= "FROM ";
		$sSQL .= "prtb_produto p LEFT OUTER JOIN cbtb_contrato cp USING(id_produto) ";
		$sSQL .= "WHERE ";
		$sSQL .= "data_contratacao > now() - INTERVAL '6 months' OR data_contratacao is null ";
		$sSQL .= "GROUP BY ";
		$sSQL .= "mes, p.id_produto ) c USING(id_produto) ";
		
		
		$relat = $this->bd->obtemRegistros($sSQL);
		
		
		
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_produtos_evolucao.html";
		

	
	
	}
	require_once("hugo_relatorio.php");
	
}
	
	function obtem_mes($params, &$smarty) {
		global $LS_MESES_ANO;
		
		if(empty($params['mes'])) $params['mes']=1;
		
		$numero_mes = $params['mes'];
		
		return $LS_MESES_ANO[$numero_mes];		
	}
	
	public function __destruct() {
			parent::__destruct();
	}


	/**
	 * Métodos também usada nos gráficos
	 */
	 
    public function obtemCarga($tipo) {
    
        $sSQL = "";
    
		switch(strtolower($tipo)) {
			
			case 'ap':
				$sSQL  = "SELECT ";
				$sSQL .= "   p.id_pop,p.nome, ";
				$sSQL .= "   CASE WHEN ";
				$sSQL .= "      cli_pop.clientes_associados is null ";
				$sSQL .= "   THEN ";
				$sSQL .= "      0 ";
				$sSQL .= "   ELSE ";
				$sSQL .= "      cli_pop.clientes_associados ";
				$sSQL .= "   END as clientes_associados, ";
				$sSQL .= "   cli_pop.carga_up,cli_pop.carga_down ";
				$sSQL .= "FROM ";
				$sSQL .= "   cftb_pop p LEFT OUTER JOIN ";
				$sSQL .= "   ( ";
				$sSQL .= "  SELECT ";
				$sSQL .= "     pop.id_ap,count(cbl.id_pop) as clientes_associados, ";
				$sSQL .= "        sum(upload_kbps) as carga_up, sum(download_kbps) as carga_down ";
				$sSQL .= "  FROM ";
				$sSQL .= "     cntb_conta_bandalarga cbl, ";
				$sSQL .= "     ( ";
				$sSQL .= "          SELECT ";
				$sSQL .= "             p.id_pop, ";
				$sSQL .= "             CASE WHEN ";
				$sSQL .= "                p.id_pop_ap is null ";
				$sSQL .= "             THEN ";
				$sSQL .= "                p.id_pop ";
				$sSQL .= "             ELSE ";
				$sSQL .= "                p.id_pop_ap ";
				$sSQL .= "             END as id_ap ";
				$sSQL .= "          FROM ";
				$sSQL .= "             cftb_pop p  ";
				$sSQL .= "     ) pop ";
				$sSQL .= "  WHERE ";
				$sSQL .= "     cbl.id_pop = pop.id_pop ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     pop.id_ap ";
				$sSQL .= "   ) cli_pop ON( p.id_pop = cli_pop.id_ap)  ";
				$sSQL .= "WHERE ";
				$sSQL .= "   p.tipo = 'AP' ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   p.nome ";
				break;

			case 'pop':
				$sSQL  = "SELECT ";
				$sSQL .= "   p.id_pop,p.nome, ";
				$sSQL .= "   CASE WHEN ";
				$sSQL .= "      cli_pop.clientes_associados is null ";
				$sSQL .= "   THEN ";
				$sSQL .= "      0 ";
				$sSQL .= "   ELSE ";
				$sSQL .= "      cli_pop.clientes_associados ";
				$sSQL .= "   END as clientes_associados, ";
				$sSQL .= "   cli_pop.carga_up,cli_pop.carga_down ";
				$sSQL .= "FROM ";
				$sSQL .= "   cftb_pop p LEFT OUTER JOIN ";
				$sSQL .= "   ( ";
				$sSQL .= "  SELECT ";
				$sSQL .= "     pop.id_pop,count(cbl.id_pop) as clientes_associados,sum(upload_kbps) as carga_up, sum(download_kbps) as carga_down  ";
				$sSQL .= "  FROM ";
				$sSQL .= "     cntb_conta_bandalarga cbl, ";
				$sSQL .= "     cftb_pop pop ";
				$sSQL .= "  WHERE ";
				$sSQL .= "     cbl.id_pop = pop.id_pop ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     pop.id_pop ";
				$sSQL .= "   ) cli_pop ON( p.id_pop = cli_pop.id_pop)  ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   p.nome ";

				break;
				
			case 'nas':
				$sSQL  = "SELECT ";
				$sSQL .= "   nas.id_nas,nas.nome, ";
				$sSQL .= "   CASE WHEN ";
				$sSQL .= "      cli_nas.clientes_associados is null ";
				$sSQL .= "   THEN ";
				$sSQL .= "      0 ";
				$sSQL .= "   ELSE ";
				$sSQL .= "      cli_nas.clientes_associados ";
				$sSQL .= "   END as clientes_associados, ";
				$sSQL .= "   cli_nas.carga_up,cli_nas.carga_down ";
				$sSQL .= "FROM ";
				$sSQL .= "   cftb_nas nas LEFT OUTER JOIN ";
				$sSQL .= "   ( ";
				$sSQL .= "  SELECT ";
				$sSQL .= "     nas.id_nas,count(cbl.id_nas) as clientes_associados,sum(upload_kbps) as carga_up, sum(download_kbps) as carga_down  ";
				$sSQL .= "  FROM ";
				$sSQL .= "     cntb_conta_bandalarga cbl, ";
				$sSQL .= "     cftb_nas nas ";
				$sSQL .= "  WHERE ";
				$sSQL .= "     cbl.id_nas = nas.id_nas ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     nas.id_nas ";
				$sSQL .= "   ) cli_nas ON( nas.id_nas = cli_nas.id_nas) ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   nas.nome ";
			
				break;
				
		}
		
		return $sSQL ? $this->bd->obtemRegistros($sSQL) : array();
    
    }



}



?>
