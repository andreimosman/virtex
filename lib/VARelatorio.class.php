<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );
require_once( "jpgraph.php" );
require_once( "jpgraph_line.php" );
require_once( "jpgraph_bar.php" );
require_once( "jpgraph_pie.php");
require_once( "jpgraph_pie3d.php");

class VARelatorio extends VirtexAdmin {

	public function VARelatorio() {
		parent::VirtexAdmin();
	
	
	}
	
	public function obtem_mes($numero_mes) {	
		global $_LS_MESES_ANO;
		return $_LS_MESES_ANO[$numero_mes];		
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
		$extra = @$_REQUEST["extra"];
		
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
			$nome = @$_REQUEST["nome"];
			$this->tpl->atribui("nome", $nome);
			
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
		
		
		if ($extra == "grafico") {
		
			$tp_grafico="3d";
		
			global $_LS_CORES;
			$base_cores = $_LS_CORES;
			$cores = array();

			if( $extra == 'grafico' ) {
				$valores = array();
				$legendas = array();
				for($i=0;$i<count($relat);$i++) {
					if( $tp_grafico != "3d" || $relat[$i]["num_contratos"] > 0 ) {
						$valores[]  = $relat[$i]["num_contratos"];
						$legendas[] = $relat[$i]["nome"];
						$cores[] = $base_cores[$i];
					}
				}
				// Exibir o gráfico
				$grafico = new PieGraph(450,250,"png");
				//$grafico->SetShadow();
				//$grafico->title->Set("Clientes por Banda");
				$grafico->title->SetFont(FF_FONT1,FS_BOLD);

				//$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
				//$grafico->SetMarginColor("#f1f1f1");


				if( $tp_grafico == "3d" ) {
					$pizza = new PiePlot3D($valores);
				} else {
					$pizza = new PiePlot($valores);
				}

				//$pizza->SetSize($size);
				$pizza->SetCenter(0.35);
				$pizza->SetLegends($legendas);
				$pizza->SetSliceColors($cores);
				$grafico->Add($pizza);

				$grafico->Stroke();

				$this->arquivoTemplate = "";

				//$pizza = new PiePlot($valores);

				return;

			}

		}		
		
		

		
		
		//echo $sSQL;
		
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_produtos_clientes.html";

	
	} else if ($op == "tproduto_cliente"){
		
		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
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
			$this->tpl->atribui("tipo",$tipo);
			
			$sSQL  = "SELECT ";
			$sSQL .= "	cl.id_cliente, cl.nome_razao, p.nome as produto, ";
			$sSQL .= "	p.tipo, cp.id_cliente_produto, ";
			$sSQL .= "	p.id_produto,cn.username ";
			$sSQL .= "      ";
			$sSQL .= "FROM  ";
			$sSQL .= "      cbtb_cliente_produto cp, prtb_produto p, ";
			$sSQL .= "      cltb_cliente cl, cntb_conta cn, ";
			$sSQL .= "      cbtb_contrato ct ";
			$sSQL .= "WHERE ";
			$sSQL .= "   cl.id_cliente = cp.id_cliente ";
			$sSQL .= "   AND p.id_produto = cp.id_produto ";
			$sSQL .= "   AND cn.id_cliente_produto = cp.id_cliente_produto ";
			$sSQL .= "   AND ct.id_cliente_produto = cp.id_cliente_produto ";
			$sSQL .= "   AND cn.tipo_conta = '$tipo' ";
			$sSQL .= "   AND cn.conta_mestre is true ";
			$sSQL .= "   AND ct.status != 'C' ";
			$sSQL .= "ORDER BY ";
			$sSQL .= "   cl.nome_razao ";
			
			
			/**
			
			
			$sSQL .= "	cbtb_contrato as cp INNER JOIN cntb_conta cnt USING (id_cliente_produto) ";
			$sSQL .= "      INNER JOIN cbtb_cliente_produto clp USING(id_cliente_produto) ";
			$sSQL .= "      INNER JOIN cntb_cliente
			$sSQL .= "	cltb_cliente as cl, ";
			$sSQL .= "	prtb_produto as pr, ";
			$sSQL .= "	cbtb_cliente_produto as clp, ";
			$sSQL .= "      cntb_conta cnt ";
			$sSQL .= "WHERE  ";
			$sSQL .= "	pr.tipo = '$tipo' ";
			$sSQL .= "	AND clp.id_cliente = cl.id_cliente ";
			$sSQL .= "      AND clp.id_produto = pr.id_produto ";
			$sSQL .= "	AND cl.excluido = 'FALSE' ";
			$sSQL .= "	AND cp.id_cliente_produto = clp.id_cliente_produto ";
			$sSQL .= "      AND cnt.id_cliente_produto = cp.id_produto ";
			$sSQL .= "      AND cnt.tipo_conta = '$tipo' ";
			$sSQL .= "      AND cnt.conta_mestre is true ";
			$sSQL .= "ORDER BY ";
			$sSQL .= "   cl.nome_razao, cl.id_cliente  ";
			
			*/
			
			//echo $sSQL;
		
		}
		
		//echo($sSQL);
		
		$relat = $this->bd->obtemRegistros($sSQL);
		

		if ($extra == "grafico") {
		
			$tp_grafico="3d";
						
			global $_LS_CORES;
			$base_cores = $_LS_CORES;
			$cores = array();

			if( $extra == 'grafico' ) {
				$valores = array();
				$legendas = array();
				for($i=0;$i<count($relat);$i++) {
					if( $tp_grafico != "3d" || $relat[$i]["num_contratos"] > 0 ) {
						$valores[]  = $relat[$i]["num_contratos"];
						$legendas[] = (trim($relat[$i]["tipo"]) == 'BL')? "Banda Larga" : (trim($relat[$i]["tipo"]) == 'H')? "Hospedagem" : "Discado" ;
						$cores[] = $base_cores[$i];	
					}
				}
				// Exibir o gráfico
				$grafico = new PieGraph(450,250,"png");
				//$grafico->SetShadow();
				//$grafico->title->Set("Clientes por Banda");
				$grafico->title->SetFont(FF_FONT1,FS_BOLD);

				//$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
				//$grafico->SetMarginColor("#f1f1f1");


				if( $tp_grafico == "3d" ) {
					$pizza = new PiePlot3D($valores);
				} else {
					$pizza = new PiePlot($valores);
				}

				//$pizza->SetSize($size);
				$pizza->SetCenter(0.35);
				$pizza->SetLegends($legendas);
				$pizza->SetSliceColors($cores);
				$grafico->Add($pizza);

				$grafico->Stroke();

				$this->arquivoTemplate = "";

				//$pizza = new PiePlot($valores);

				return;

			}

		}
		
		
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_tipoprodutos_clientes.html";	
	
	} else if ($op == "cidade_cliente"){
		
		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
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
			$sSQL .= "   WHERE ";
			$sSQL .= "      excluido = 'FALSE' ";
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
				
						
		if ($extra == "grafico") {
		
			$tp_grafico="2d";
						
			global $_LS_CORES;
			$base_cores = $_LS_CORES;
			$cores = array();


			if( $extra == 'grafico' ) {
				$valores = array();
				$legendas = array();
				$outros = 0;
				$prefs = $this->prefs->obtem("geral");
				//$agrupar = $prefs["agrupar"];
				$agrupar = 20;
				//$agrupar_cidades_com_menos_de = 20;
				
				for($i=0;$i<count($relat);$i++) {
					//if( $tp_grafico != "3d" || $relat[$i]["num_clientes"] > 0 ) {
					//	$valores[]  = $relat[$i]["num_clientes"];
					//	$legendas[] = $relat[$i]["cidade"];
					//	$cores[] = $base_cores[$i];	
					//}
					
					if( $tp_grafico != "3d" || $relat[$i]["num_clientes"] > 0 ) {
						if( $relat[$i]["num_clientes"] > $agrupar ) {
							$valores[]  = $relat[$i]["num_clientes"];
							$legendas[] = $relat[$i]["cidade"];
							//$cores = $base_cores[$i];
							//echo "COR: " . $base_cores[$i] . "<br>\n";
						} else {
							$outros++;
						}

					}
					
					
					
					
				}
				
				if( $outros > 0 ) {
								$valores[] = $outros;
								$legendas[] = "*OUTRAS LOCALIDADES";
								$cores[] = $base_cores[$i];
				}

				$incremento = 0;
				if( count($valores) > 10 ) {
					// Se tiver mais informacoes tem que expandir o grafico verticalmente;
					$incremento = (count($valores) - 10) * 20;
				}
				// Exibir o gráfico
				$grafico = new PieGraph(480,270 + $incremento,"png");
				//$grafico->SetShadow();
				//$grafico->title->Set("Clientes por Banda");
				$grafico->title->SetFont(FF_FONT1,FS_BOLD);

				//$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
				//$grafico->SetMarginColor("#f1f1f1");

				//Imagem de Fundo
				//$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
				//$grafico->SetMarginColor("white");
						



				if( $tp_grafico == "3d" ) {
					$gr = new PiePlot3D($valores);
					//$gr = new BarPlot3D($valores);
				} else {
					$gr = new PiePlot($valores);
					//$gr = new BarPlot($valores);
				}

				//$gr->SetFillGradient("#aa0000","red",GRAD_VER);;
				//$gr->SetColor("#aa0000");

				//$size = 0.4;
				$size=0.3;
				$gr->SetSize($size);
				$gr->SetCenter(0.35);
				$gr->SetLegends($legendas);
				//$gr->SetSliceColors($cores);
				//$grafico->xaxis->SetTickLabels($legendas);

				$grafico->Add($gr);

				$grafico->Stroke();

				$this->arquivoTemplate = "";


				return;

			}

		}
		
		
				
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_cidades_clientes.html";	
	
	}  else if ($op == "adesao"){
		
		
		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
		$periodo = @$_REQUEST["periodo"];
		
		if (!$acao) $acao = "geral";
		if (!$periodo) $periodo = "12";
						
				
		if ($acao == "geral") {	
		
			$sSQL  = "SELECT ";
			$sSQL .= "	count(*) as num_contratos, ";
			$sSQL .= "	EXTRACT( 'month' FROM data_contratacao) as mes, ";
			$sSQL .= "	EXTRACT( 'year' FROM data_contratacao) as ano ";
			$sSQL .= "FROM ";
			$sSQL .= "	cbtb_contrato ";
			$sSQL .= "WHERE ";
			$sSQL .= "	data_contratacao > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '$periodo months' ";
			$sSQL .= "GROUP BY ano, mes ";
			$sSQL .= "ORDER BY ano, mes ";		
					
		} else if($acao = "sub_ade") {
			
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];
			
			$this->tpl->atribui("mes", $mes);
			$this->tpl->atribui("ano", $ano);
									
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
		global $_LS_MESES_ANO;
		$this->tpl->atribui("meses_ano", $_LS_MESES_ANO);
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
			   $mes_corrente = $_LS_MESES_ANO[$relat[$i]["mes"]];
			   $legendas[] =  $mes_corrente . "/" . $relat[$i]["ano"];
			   $pontos[] = $relat[$i]["num_contratos"];			   
			}
					
					
			// GERA O Gráfico

			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,200,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			//$grafico->title->Set('Relatório de Adesões');
			$grafico->img->SetMargin(40,40,40,40);
			
			//Imagem de Fundo
			$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
			$grafico->SetMarginColor("white");
						
			//Cria uma nova mostragem gráfica
			$gBarras = new BarPlot($pontos); 

			//$grafico->xaxis->SetMajTickPositions($positions,$titulos);

			// ajuste de cores 
			//$gBarras->SetFillColor("#ff0000");
			$gBarras->SetFillGradient("#aa0000","red",GRAD_VER);;
			$gBarras->SetColor("#aa0000");

			
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
		if (!$periodo) $periodo = "12";
						
				
		if ($acao == "geral") {	
		
			

			$sSQL  = "SELECT ";
			$sSQL .= "	count(*) as num_contratos, ";
			$sSQL .= "	EXTRACT( 'month' FROM data_status) as mes, ";
			$sSQL .= "	EXTRACT( 'year' FROM data_status) as ano ";
			$sSQL .= "FROM ";
			$sSQL .= "	cbtb_contrato ";
			$sSQL .= "WHERE ";
			$sSQL .= "	data_status > CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '$periodo months' ";
			$sSQL .= "	AND status = 'C' ";
			$sSQL .= "GROUP BY ano, mes ";
			$sSQL .= "ORDER BY ano, mes ";
				
					
		
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
		global $_LS_MESES_ANO;
		$this->tpl->atribui("meses_ano", $_LS_MESES_ANO);
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
			   $mes_corrente = $_LS_MESES_ANO[$relat[$i]["mes"]];
			   $legendas[] =  $mes_corrente . "/" . $relat[$i]["ano"];
			   $pontos[] = $relat[$i]["num_contratos"];			   
			}
					
					
			// GERA O Gráfico

			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,200,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			//$grafico->title->Set('Relatório de Cancelamentos');
			$grafico->img->SetMargin(40,40,40,40);
			
			//Imagem de Fundo
			$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
			$grafico->SetMarginColor("white");
						
			//Cria uma nova mostragem gráfica
			$gBarras = new BarPlot($pontos); 

			//$grafico->xaxis->SetMajTickPositions($positions,$titulos);

			// ajuste de cores 
			//$gBarras->SetFillColor("#ff0000");
			$gBarras->SetFillGradient("#aa0000","red",GRAD_VER);;
			$gBarras->SetColor("#aa0000");

			
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

	
		
	} else if($op == "inadimplencia"){
	
		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
		$periodo = @$_REQUEST["periodo"];
		
		if (!$acao) $acao = "geral";
		if (!$periodo) $periodo = "12";	
	
		if ($acao == "geral") {
			
			$sSQL  = "SELECT ";
			$sSQL .= "	count(*) as num_contratos, ";
			$sSQL .= "	EXTRACT(year from f.data) as ano, ";
			$sSQL .= "	EXTRACT(month from f.data) as mes ";
			$sSQL .= "FROM ";
			$sSQL .= "	cbtb_faturas f INNER JOIN cbtb_contrato ctt USING(id_cliente_produto)  ";
			$sSQL .= "WHERE ";
			$sSQL .= "  CASE WHEN ";
			$sSQL .= "     f.status = 'P' ";
			$sSQL .= "  THEN ";
			$sSQL .= "	   data_pagamento > data ";
			$sSQL .= "	ELSE ";
			$sSQL .= "   	CASE WHEN ";
			$sSQL .= "   		f.reagendamento IS NOT NULL ";
			$sSQL .= "   	THEN ";
			$sSQL .= "   		f.reagendamento >= CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '$periodo months' AND ";
			$sSQL .= "   		f.reagendamento < CAST( EXTRACT(year from now()) || '-' ||EXTRACT(month from now()) ||'-01' as date) ";
			$sSQL .= "   	ELSE ";
			$sSQL .= "   		f.data >= CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '$periodo months' AND ";
			$sSQL .= "  		f.data < CAST( EXTRACT(year from now()) || '-' ||EXTRACT(month from now()) ||'-01' as date) ";
			$sSQL .= "   	END ";
			$sSQL .= "	END AND ";
			$sSQL .= "	(f.status != 'E' AND f.status != 'C') AND ctt.status = 'A' ";
			$sSQL .= "GROUP BY ano, mes ";
			$sSQL .= "ORDER BY ano, mes ";
			
			//echo $sSQL . "<br>\n";

		
		}else if($acao == "sub_ina") {
		
			$ano = @$_REQUEST["ano"];
			$mes = @$_REQUEST["mes"];
			
			$this->tpl->atribui("mes", $mes);
			$this->tpl->atribui("ano", $ano);
			
			
			$sSQL  = "SELECT ";
			$sSQL .= "	cl.id_cliente, cl.nome_razao, cp.id_cliente_produto, ";
			$sSQL .= "  pr.nome as nome_produto, cn.username, ";
			$sSQL .= "	EXTRACT(year from f.data) as ano,  ";
			$sSQL .= "	EXTRACT(month from f.data) as mes, ";
			$sSQL .= "	EXTRACT(day from f.data) as dia, ";
			$sSQL .= "	CASE WHEN ";
			$sSQL .= "		f.status = 'P' ";
			$sSQL .= "  THEN ";
			$sSQL .= "		'PA' ";
			$sSQL .= "	ELSE ";
			$sSQL .= "		'AT' ";
			$sSQL .= "	END as st_atrazo ";
			$sSQL .= "FROM ";
			$sSQL .= "	cbtb_faturas f, ";
			$sSQL .= "	cbtb_contrato ctt,  ";
			$sSQL .= "	cbtb_cliente_produto cp,  ";
			$sSQL .= "	cltb_cliente cl, ";
			$sSQL .= "	prtb_produto pr, ";
			$sSQL .= "  cntb_conta cn ";
			$sSQL .= "WHERE ";
			$sSQL .= "	f.id_cliente_produto = ctt.id_cliente_produto ";
			$sSQL .= "	AND ctt.id_cliente_produto = cp.id_cliente_produto ";
			$sSQL .= "	AND cp.id_cliente = cl.id_cliente AND pr.id_produto = cp.id_produto ";
			$sSQL .= "  AND cn.id_cliente_produto = cp.id_cliente_produto ";
			$sSQL .= "  AND cn.tipo_conta = pr.tipo ";
			$sSQL .= "  AND cn.conta_mestre is true ";
			$sSQL .= "	AND ";
			$sSQL .= "  CASE WHEN ";
			$sSQL .= "     f.status = 'P' ";
			$sSQL .= "  THEN ";
			$sSQL .= "	   data_pagamento > data ";
			$sSQL .= "	ELSE ";

			$sSQL .= "		CASE WHEN  ";
			$sSQL .= "			f.reagendamento IS NOT NULL  ";
			$sSQL .= "		THEN ";
			$sSQL .= "			f.reagendamento >= CAST(( '$ano' ||  '-$mes' ||'-01') as date) AND ";
			$sSQL .= "			f.reagendamento < CAST(( '$ano' ||  '-$mes' ||'-01') as date) + INTERVAL '1 month' ";
			$sSQL .= "		ELSE ";
			$sSQL .= "			f.data >= CAST(( '$ano' || '-$mes' ||'-01') as date) AND ";
			$sSQL .= "			f.data < CAST(( '$ano' || '-$mes' ||'-01') as date) + INTERVAL '1 month' ";
			$sSQL .= "		END ";
			
			$sSQL .= "	END ";

			$sSQL .= "	AND (f.status != 'E' AND f.status != 'C') AND ctt.status = 'A' ";
			$sSQL .= "ORDER BY ano, mes, dia, nome_razao ";	
		
		}
		
		
		//echo $sSQL;
		$relat = $this->bd->obtemRegistros($sSQL);
		
		global $_LS_TP_CONSULTA;
		global $_LS_MESES_ANO;
		$this->tpl->atribui("meses_ano", $_LS_MESES_ANO);
		$this->tpl->atribui("tpconsulta", $_LS_TP_CONSULTA);
		$this->tpl->atribui("periodo", $periodo);
		$this->tpl->atribui("relat",$relat);
		$this->tpl->atribui("acao" ,$acao);
		$this->tpl->atribui("op", $op);
		
		
		if ($extra == "grafico") {

			$relat = $this->bd->obtemRegistros($sSQL);

			$pontos = array();
			$legendas = array();

			for($i=0;$i<count($relat);$i++) {
			   $mes_corrente = $_LS_MESES_ANO[$relat[$i]["mes"]];
			   $legendas[] =  $mes_corrente . "/" . $relat[$i]["ano"];
			   $pontos[] = $relat[$i]["num_contratos"];			   
			}


			// GERA O Gráfico

			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,200,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			//$grafico->title->Set('Relatório de Inadimplência');
			$grafico->img->SetMargin(40,40,40,40);

			//Imagem de Fundo
			$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
			$grafico->SetMarginColor("white");

			//Cria uma nova mostragem gráfica
			$gBarras = new BarPlot($pontos); 

			//$grafico->xaxis->SetMajTickPositions($positions,$titulos);

			// ajuste de cores 
			//$gBarras->SetFillColor("#ff0000");
			$gBarras->SetFillGradient("#aa0000","red",GRAD_VER);;
			$gBarras->SetColor("#aa0000");

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
		
		
		
		$this->arquivoTemplate = 'relatorio_inadimplente.html';

	
	} else if($op == "bloqueios"){
		
		$op = @$_REQUEST["op"];
		$acao = @$_REQUEST["acao"];
		$periodo = @$_REQUEST["periodo"];
		$extra = @$_REQUEST["extra"];
		
		if(!$periodo) $periodo = "12";
		
		if(!$acao) $acao = "geral";
						
		
		if ($acao == "geral") {
		/*
			$sSQL  = "SELECT ";
			$sSQL .= "	count(*) as num_bloqueios, tipo, ";
			$sSQL .= "	EXTRACT(year from data_hora) as ano, ";
			$sSQL .= "	EXTRACT(month from data_hora) as mes ";
			$sSQL .= "FROM ";
			$sSQL .= "	lgtb_bloqueio_automatizado ";
			$sSQL .= "WHERE ";
			$sSQL .= "	data_hora > (CAST(EXTRACT(year from now()) || '-' || EXTRACT(month from now()) || '-01' as date) + INTERVAL '1 month') - INTERVAL '12 months' ";
			$sSQL .= "GROUP BY tipo, ano, mes ";
			$sSQL .= "ORDER BY ano, mes, tipo ";*/
			
						
			
			$sSQL  = "SELECT ";
			$sSQL .= " * ";
			$sSQL .= "FROM ";
			$sSQL .= " (SELECT  ";
			$sSQL .= "    extract(month from data_hora) as mes, extract(year from data_hora) as ano, count(tipo) as bloqueados  ";
			$sSQL .= " FROM  ";
			$sSQL .= "    lgtb_bloqueio_automatizado  ";
			$sSQL .= " WHERE ";
			$sSQL .= " 	  data_hora > (CAST(EXTRACT(year FROM now()) || '-' || EXTRACT(month from now()) || '-01' as date)) + INTERVAL '1 month' - INTERVAL '$periodo months' AND ";
			$sSQL .= "    tipo = 'D' ";
			$sSQL .= " GROUP BY ";
			$sSQL .= "    mes,ano) dbq ";
			$sSQL .= " FULL OUTER JOIN ";
			$sSQL .= " (SELECT  ";
			$sSQL .= "   extract(month from data_hora) as mes, extract(year from data_hora) as ano, count(tipo) as desbloqueados  ";
			$sSQL .= "  FROM  ";
			$sSQL .= "   lgtb_bloqueio_automatizado  ";
			$sSQL .= "  WHERE ";			
			$sSQL .= " 	  data_hora > (CAST(EXTRACT(year FROM now()) || '-' || EXTRACT(month from now()) || '-01' as date)) + INTERVAL '1 month' - INTERVAL '$periodo months' AND ";
			$sSQL .= "   tipo = 'B'	 ";		
			$sSQL .= "   GROUP BY mes,ano	 ";		
			$sSQL .= "  ) blq USING(mes,ano) ";
	
		} else if ($acao == "sub_geral") {
		
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];
			
			$this->tpl->atribui("mes", $mes);
			$this->tpl->atribui("ano", $ano);
	
					
			$sSQL  = "SELECT ";
			$sSQL .= "	clt.id_cliente, clt.nome_razao, ";
			$sSQL .= "	cnt.username, prd.nome, cp.id_cliente_produto, ";
			$sSQL .= "	lba.data_hora, lba.admin, lba.data_hora, lba.tipo, ";
			$sSQL .= "	EXTRACT(day from data_hora) as dia, ";
			$sSQL .= "	EXTRACT(month from data_hora) as mes, ";
			$sSQL .= "	EXTRACT(year from data_hora) as ano ";
			$sSQL .= "FROM ";
			//$sSQL .= "	(cltb_cliente clt INNER JOIN cbtb_cliente_produto USING(id_cliente)) INNER JOIN lgtb_bloqueio_automatizado lba USING(id_cliente_produto) ";
			$sSQL .= "	(cltb_cliente clt INNER JOIN cbtb_cliente_produto cp USING(id_cliente)) INNER JOIN lgtb_bloqueio_automatizado lba USING(id_cliente_produto) INNER JOIN cntb_conta cnt USING(id_cliente_produto), ";
			$sSQL .= "	prtb_produto as prd ";
			$sSQL .= "WHERE ";
			$sSQL .= "  prd.id_produto = cp.id_produto ";
			$sSQL .= "	AND data_hora < CAST('$ano-$mes-01' as date) + INTERVAL '1 month' ";
			$sSQL .= "	AND data_hora >= CAST('$ano-$mes-01' as date) ";
			$sSQL .= "ORDER BY ano, mes, dia, clt.nome_razao ";
			
			
		}else if ($acao == "sub_blo") {
		
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];
			$tipo = @$_REQUEST["tipo"];
			
			$this->tpl->atribui("tipo", $tipo);
			$this->tpl->atribui("mes", $mes);
			$this->tpl->atribui("ano", $ano);
					
			$sSQL  = "SELECT ";
			$sSQL .= "	clt.id_cliente, clt.nome_razao, ";
			$sSQL .= "	cnt.username, prd.nome, cp.id_cliente_produto, ";
			$sSQL .= "	lba.data_hora, lba.admin, lba.data_hora, lba.tipo, ";
			$sSQL .= "	EXTRACT(day from data_hora) as dia, ";
			$sSQL .= "	EXTRACT(month from data_hora) as mes, ";
			$sSQL .= "	EXTRACT(year from data_hora) as ano ";
			$sSQL .= "FROM ";
			$sSQL .= "	(cltb_cliente clt INNER JOIN cbtb_cliente_produto cp USING(id_cliente)) INNER JOIN lgtb_bloqueio_automatizado lba USING(id_cliente_produto) INNER JOIN cntb_conta cnt USING(id_cliente_produto),  ";
			$sSQL .= "	prtb_produto as prd ";
			$sSQL .= "WHERE ";
			$sSQL .= "  prd.id_produto = cp.id_produto ";
			$sSQL .= "	AND data_hora < CAST('$ano-$mes-01' as date) + INTERVAL '1 month' ";
			$sSQL .= "	AND data_hora < CAST('$ano-$mes-01' as date) + INTERVAL '1 month' ";
			$sSQL .= "	AND data_hora >= CAST('$ano-$mes-01' as date) ";
			$sSQL .= "  AND lba.tipo = '$tipo'";
			$sSQL .= "ORDER BY ano, mes, dia, clt.nome_razao ";
			
		}
		
		//echo ($sSQL);
			
		$relat = $this->bd->obtemRegistros($sSQL);
		global $_LS_MESES_ANO;
		global $_LS_TP_CONSULTA;
		
		
		if($extra=="grafico") {		
			$datay=array();
			$datay2=array();
			$legendas=array();
			$datazero=array(0,0,0,0);
			
			
			for ($i=0; $i<count($relat); $i++) {
				$mes_corrente = $relat[$i]['mes'];
				$datay[] = $relat[$i]['bloqueados'];
				$datay2[] = $relat[$i]['desbloqueados'];	
				$legendas[] = substr($_LS_MESES_ANO[$mes_corrente],0,3) . "/" . $relat[$i]['ano'];
			}
			
			// GERA O Gráfico
			
			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,200,"png");
			$grafico->SetScale("textlin"); 
			$grafico->img->SetMargin(40,40,40,40);

			//Imagem de Fundo
			$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
			$grafico->SetMarginColor("white");

			//Cria uma nova mostragem gráfica
			$gBarras = new BarPlot($datay); 
			$gBarras2 = new BarPlot($datay2); 

			//$grafico->xaxis->SetMajTickPositions($positions,$titulos);

			// ajuste de cores 
			//$gBarras->SetFillColor("#ff0000");
			$gBarras->SetFillGradient("navy","lightsteelblue",GRAD_VER);
			$gBarras2->SetFillGradient("#aa0000","red",GRAD_VER);
			
			$gBarras->SetColor("navy");
			$gBarras2->SetColor("#aa0000");

			$groupbar = new GroupBarPlot(array($gBarras, $gBarras2));

			//$gBarras->SetShadow("darkblue"); 
			//$grafico->xaxis->labels = $legendas;
			//$gBarras->label->Set($legendas);

			// título das barras
			$grafico->xaxis->SetTickLabels($legendas);

			// adicionar mostrage de barras ao gráfico 
			$grafico->Add($groupbar); 

			// imprimir gráfico 
			$grafico->Stroke();

			$this->arquivoTemplate = '';		
			return;
		
		}
		
				
		$this->tpl->atribui("tpconsulta", $_LS_TP_CONSULTA);
		$this->tpl->atribui("meses_ano", $_LS_MESES_ANO);
		$this->tpl->atribui("relat", $relat);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("periodo",$periodo);
		
		$this->arquivoTemplate = "relatorio_bloqueios.html";
			
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
