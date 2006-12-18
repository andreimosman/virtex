<?

/**
 * Não retirar os requires do jpgraph
 * Eles não serão alcançados no __autoload
 * TODO: Fazer mapeamento do jograph no autoload.
 */
require_once( "jpgraph.php" );
require_once( "jpgraph_line.php" );
require_once( "jpgraph_bar.php" );
require_once( "jpgraph_pie.php");
require_once( "jpgraph_pie3d.php");

class VARelatorio extends VirtexAdminWeb {

	public function __construct() {
		parent::__construct();
	}
	
	public function obtem_mes($numero_mes) {	
		global $_LS_MESES_ANO;
		return $_LS_MESES_ANO[$numero_mes];		
	}	
	
	public function processa($op=null) {
	if($op == "fatura"){
				if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
					$this->privMSG();
					return;
				}	
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
	
					if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
						$this->privMSG();
						return;
				}	
		//$this->arquivoTemplate = "cobranca_versaolight.html";
		$this->arquivoTemplate = "relatorio_cortesia.html";
					
		$acao = @$_REQUEST["acao"];
		
		$tipo_relatorio = @$_REQUEST["tipo_relatorio"];
		if (!$tipo_relatorio) $tipo_relatorio = "todos";
					
		$this->tpl->atribui("tipo_relatorio", $tipo_relatorio);
		$this->tpl->atribui("acao", $acao);	
		
					
				 $sSQL  = "SELECT ";
				 $sSQL .= "   cl.id_cliente,cl.nome_razao, ";
				 $sSQL .= "   to_char(ct.data_contratacao,'DD/MM/YYYY') as data_contratacao, ";
				 $sSQL .= "   pr.id_produto,pr.nome as nome_produto, pr.tipo, ";
				 $sSQL .= "   cnt.username,cnt.dominio  ";
				 $sSQL .= "FROM ";
				 $sSQL .= "   cltb_cliente cl,cbtb_cliente_produto cp,cbtb_contrato ct, ";
				 $sSQL .= "   prtb_produto pr, cntb_conta cnt ";
				 $sSQL .= "WHERE ";
				 $sSQL .= "   cl.id_cliente = cp.id_cliente ";
				 $sSQL .= "   AND pr.id_produto = cp.id_produto ";
				 $sSQL .= "   AND ct.id_cliente_produto = cp.id_cliente_produto ";
				 $sSQL .= "   AND cnt.id_cliente_produto = cp.id_cliente_produto ";
				 $sSQL .= "   AND cnt.conta_mestre is true ";
				 $sSQL .= "   AND cnt.tipo_conta = pr.tipo ";
         $sSQL .= "   AND pr.valor = 0 ";
		
		
		
		
		if ($acao == "consultar") {
		
		
				switch($tipo_relatorio){
				
					case "D":
						$sSQL .= " AND pr.tipo = 'D' ";
					break;
					case "H":
						$sSQL .= " AND pr.tipo = 'H' ";
					break;
					case "E":
						$sSQL .= " AND pr.tipo = 'E' ";
					break;
					case "BL":
						$sSQL .= " AND pr.tipo = 'BL' ";
					break;
				}
				
		}
			
			$sSQL .= " ORDER BY cl.nome_razao ASC ";
			
			
			$rel_cortesia = $this->bd->obtemRegistros($sSQL);
			$this->tpl->atribui("rel_cortesia",$rel_cortesia);
			$this->arquivoTemplate = "relatorio_cortesia.html";		
		
	} else if ($op == "geral"){
	
					if( ! $this->privPodeLer("_RELATORIOS_CLIENTE") ) {
						$this->privMSG();
						return;
				}	
	
	
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
				$aSQL .= "WHERE c.id_cidade = cl.id_cidade ";
				$aSQL .= "AND cl.excluido = false ";
				$aSQL .= " AND cl.ativo = true";
				
				
				///echo $aSQL;
						
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
				$aSQL .= "AND cl.excluido = false ";
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
			$aSQL .= "AND cl.excluido = false ";
			$aSQL .= " AND cl.ativo = true";
												
			
			///////echo $aSQL ;
			
			
			$reg = $this->bd->obtemRegistros($aSQL);
											
			$this->tpl->atribui("ult_cli",$reg);

		
		
		}
	
	
		$this->arquivoTemplate = "relatorio_clientes.html";
	
	
	} else if ($op == "estat"){
				if( ! $this->privPodeLer("_RELATORIOS_CLIENTE") ) {
					$this->privMSG();
					return;
				}	


		$this->arquivoTemplate = "cobranca_versaolight.html";
		//$this->arquivoTemplate = "relatorio_estat.html";





	} else if ($op == "filtro"){
					if( ! $this->privPodeLer("_RELATORIOS_CLIENTE") ) {
						$this->privMSG();
						return;
				}	
	
			
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		$this->tpl->atribui("op",$op);
	
		$this->arquivoTemplate = "cobranca_versaolight.html";
		//$this->arquivoTemplate = "relatorio_filtro.html";
		
		
	} else if ($op == "config"){
					if( ! $this->privPodeLer("_RELATORIO_CONFIG") ) {
						$this->privMSG();
						return;
				}		
	
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
				if( ! $this->privPodeLer("_RELATORIO_CONFIG") ) {
							$this->privMSG();
							return;
				}	
		$this->tpl->atribui("grop",@$_REQUEST["grop"]); 	// OP enviada para o gráfico
		$this->tpl->atribui("tipo",@$_REQUEST["tipo"]); 	// Tipo do gráfico
		$this->tpl->atribui("rl",@$_REQUEST["rl"]);		// Parametro extra e relatório

		$this->arquivoTemplate = "relatorio_grafico.html";


	} else if ($op == "produto_cliente"){
	
				if( ! $this->privPodeLer("_RELATORIOS_CLIENTE") ) {
							$this->privMSG();
							return;
				}	
		
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
		
				if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
								$this->privMSG();
								return;
				}	
		
		
		$tipo_pesquisa = "ativos";
		
		$tipo_pesquisa = @$_REQUEST["tipo_pesquisa"];
		
		//echo $tipo_pesquisa;
		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
		if (!$acao) $acao = "geral";
		
		if ($acao == "geral") {
		

			$sSQL  = "SELECT ";
			$sSQL .= " COUNT(cp.tipo_produto) as num_contratos, ";
			$sSQL .= " cp.tipo_produto as tipo ";
			$sSQL .= "FROM cbtb_contrato as cp ";
			
			if ($tipo_pesquisa == "ativos" || $tipo_pesquisa == "" ){
			
				$sSQL .= " WHERE cp.status = 'A'   ";
			
			}
			
			$sSQL .= "GROUP BY cp.tipo_produto ";
			$sSQL .= "ORDER BY cp.tipo_produto ";
			
			///echo $sSQL;
			
		
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
			
			if ($tipo_pesquisa == "ativos" || $tipo_pesquisa == "" ){
			
				$sSQL .= "   AND ct.status != 'C' ";
				
			}
			
			
			$sSQL .= "ORDER BY ";
			$sSQL .= "   cl.nome_razao ";
			
			//////////////echo $sSQL;
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
		$this->tpl->atribui("tipo_pesquisa",$tipo_pesquisa);
		/////////////echo '<Br>'.'<Br>'.'<Br>'.$tipo_pesquisa;

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
						$legendas[] = (trim($relat[$i]["tipo"])=='BL')? "Banda Larga" : (trim($relat[$i]["tipo"]) == 'H')? "Hospedagem" : "Discado" ;
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
				if( ! $this->privPodeLer("_RELATORIOS_CLIENTE") ) {
						$this->privMSG();
						return;
				}	
		
		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
		if (!$acao) $acao = "geral";
		$sop = @$_REQUEST["sop"];
		
		
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
			$sSQL .= "ORDER BY cid.cidade ASC ";
			
			
		} else if($acao == "sub_cid") {
		
			$id_cidade = @$_REQUEST["id_cidade"];
		
			$sSQL  = "SELECT ";
			$sSQL .= "	cnt.id_cliente, ";
			$sSQL .= "	cnt.nome_razao, ";
			$sSQL .= "	cid.id_cidade, ";
			$sSQL .= "	cid.cidade, ";
			$sSQL .= "	cid.uf, cn.status ";
			$sSQL .= "FROM ";
			$sSQL .= "  cntb_conta cn,";
			$sSQL .= "	cltb_cliente as cnt, ";
			$sSQL .= "	(SELECT ";
			$sSQL .= "		id_cidade, ";
			$sSQL .= "		cidade, ";
			$sSQL .= "		uf ";
			$sSQL .= "	FROM ";
			$sSQL .= "		cftb_cidade ";
			$sSQL .= "	ORDER BY uf) cid ";
			$sSQL .= "WHERE ";
			$sSQL .= "  cn.id_cliente = cnt.id_cliente AND ";
			$sSQL .= "	cnt.id_cidade = cid.id_cidade AND cnt.id_cidade = $id_cidade AND";
			$sSQL .= "	cnt.excluido = 'FALSE' ";
			$sSQL .= "ORDER BY cid.uf, cid.id_cidade, cnt.nome_razao";	
			
			//echo "QUERY: $sSQL <br>\n";
			if ($sop == "contratos"){
				if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
									$this->privMSG();
									return;
				}	
				/*$sSQL  = "SELECT ";
				$sSQL .= "DISTINCT(cnt.nome_razao) cnt.id_cliente,cid.cidade,cid,uf ";
				$sSQL .= "FROM ";
				$sSQL .= "cltb_cliente cnt, (SELECT id_cidade,cidade,uf FROM cftb_cidade ORDER BY uf) cid, cbtb_cliente_produto cb  ";
				$sSQL .= "WHERE ";
				$sSQL .= "cnt.id_cidade = cid.id_cidade AND ";
				$sSQL .= "cnt.id_cidade = $id_cidade AND ";
				$sSQL .= "cnt.excluido is false ";
				$sSQL .= " AND cb.id_cliente = cnt.id_cliente ";
				$sSQL .= "ORDER BY cid.uf,cid.id_cidade,cnt.nome_razao ";
				//$sSQL .= "GROUP BY cnt.nome_razao,cnt.id_cliente,cid.cidade,cid.uf ";*/
				
				$sSQL = "SELECT DISTINCT(cnt.nome_razao) as nome_razao,cnt.id_cliente,cid.cidade,cid.uf   
								 FROM cltb_cliente cnt, (SELECT id_cidade,cidade,uf FROM cftb_cidade) cid, cbtb_cliente_produto cb 
								 WHERE cnt.id_cidade = cid.id_cidade AND cnt.id_cidade = $id_cidade AND
								 cnt.excluido is false AND
								 cb.id_cliente = cnt.id_cliente 
								 ORDER BY nome_razao";
			
				//echo $sSQL ."<br>";
			
				$this->tpl->atribui("contrato"," com contratos");
			
			}
			
			
			
			
			
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
				$agrupar = $prefs["agrupar"];
				//$agrupar = 20;
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
				if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
								$this->privMSG();
								return;
				}	
		
		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
		$periodo = @$_REQUEST["periodo"];
		
		if (!$acao) $acao = "geral";
		if (!$periodo) $periodo = "12";
						
		if ($acao == "geral")	$relat = $this->adesao($acao,$periodo);
		//if (!$extra) $extra = "grafico";
		
		$mes = @$_REQUEST["mes"];
		$ano = @$_REQUEST["ano"];
		
		if ($acao == "sub_ade"){
		
					$mes = @$_REQUEST["mes"];
					$ano = @$_REQUEST["ano"];
					$data = $mes.",".$ano;
					
					$relat = $this->adesao($acao,$periodo,$data);
					$this->tpl->atribui("mes",$mes);
					$this->tpl->atribui("ano",$ano);
		
		}
		
		
		/*
		$this->tpl->atribui("data_ini", $data_ini);
		$this->tpl->atribui("data_fim", $data_fim);*/
		
		global $_LS_TP_CONSULTA;
		global $_LS_MESES_ANO;
		$this->tpl->atribui("meses_ano", $_LS_MESES_ANO);
		$this->tpl->atribui("tpconsulta", $_LS_TP_CONSULTA);
		$this->tpl->atribui("periodo", $periodo);
		//echo "$acao";
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_adesoes.html";
			
		
		if ($extra == "grafico") {
			
			

			//$relat = $this->bd->obtemRegistros($sSQL);
					
			$pontos = array();
			$legendas = array();

			for($i=0;$i<count($relat);$i++) {
			   $mes_corrente = substr($_LS_MESES_ANO[$relat[$i]["mes"]],0,3);
			   $legendas[] =  $mes_corrente . "/" . $relat[$i]["ano"];
			   $pontos[] = $relat[$i]["num_contratos"];			   
			}
					
					
			// GERA O Gráfico

			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,250,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			//$grafico->title->Set('Relatório de Adesões');
			$grafico->img->SetMargin(40,40,40,80);
			
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
			//$gBarras->xaxis->SetLabelAngle(90);

			
			//$gBarras->SetShadow("darkblue"); 
			//$grafico->xaxis->labels = $legendas;
			//$gBarras->label->Set($legendas);
			
			// título das barras
			$grafico->xaxis->SetTickLabels($legendas);
			$grafico->xaxis->SetLabelAngle(90);
			// adicionar mostrage de barras ao gráfico 
			$grafico->Add($gBarras); 

			// imprimir gráfico 
			$grafico->Stroke();
			
			$this->arquivoTemplate = '';		
			return;
		
		}
	
	} else if($op == "cancelamento") {
				if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
							$this->privMSG();
							return;
				}	

		$acao = @$_REQUEST["acao"];
		$extra = @$_REQUEST["extra"];
		$periodo = @$_REQUEST["periodo"];
		
		if (!$acao) $acao = "geral";
		if (!$periodo) $periodo = "12";
						
				
		if ($acao == "geral") $relat = $this->cancelamento($acao,$periodo);
		
		if($acao == "sub_ade") {
			
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];
									
			$data = $mes.",".$ano;
			$relat = $this->cancelamento($acao,$periodo,$data);

		
		}
		
		
	
		
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
					

			$relat = $relat;
					
			$pontos = array();
			$legendas = array();

			for($i=0;$i<count($relat);$i++) {
			   $mes_corrente =  substr($_LS_MESES_ANO[$relat[$i]["mes"]],0,3);
			   $legendas[] =  $mes_corrente . "/" . $relat[$i]["ano"];
			   $pontos[] = $relat[$i]["num_contratos"];			   
			}
					
					
			// GERA O Gráfico

			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,250,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			//$grafico->title->Set('Relatório de Cancelamentos');
			$grafico->img->SetMargin(40,40,40,80);
			
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
			$grafico->xaxis->SetLabelAngle(90);
			// adicionar mostrage de barras ao gráfico 
			$grafico->Add($gBarras); 

			// imprimir gráfico 
			$grafico->Stroke();
			
			$this->arquivoTemplate = '';		
			return;
		
		}

	
		
	} else if($op == "inadimplencia"){
	
						if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
							$this->privMSG();
							return;
				}	
	
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
			$sSQL .= "   		f.reagendamento <  CAST( EXTRACT(year from now()) || '-' ||EXTRACT(month from now()) ||'-01' as date) ";
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
			$sSQL .= "	AND EXTRACT(month from f.data) = '$mes' AND EXTRACT(year from f.data) = '$ano' ";
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
			   $legendas[] =  $mes_corrente . "\n" . $relat[$i]["ano"]; 
			   $pontos[] = $relat[$i]["num_contratos"];			   
			}


			// GERA O Gráfico

			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,250,"png");


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
			$gBarras->SetFillGradient("#aa0000","red",GRAD_VER);
			$gBarras->SetColor("#aa0000");

			//$gBarras->SetShadow("darkblue"); 
			//$grafico->xaxis->labels = $legendas;
			//$gBarras->label->Set($legendas);

			// título das barras
			$grafico->xaxis->SetTickLabels($legendas);
			//$grafico->xaxis->SetLabelAngle(90);
			$grafico->xaxis->title->SetFont(FF_FONT2,FS_NORMAL,1);


			// adicionar mostrage de barras ao gráfico 
			$grafico->Add($gBarras); 

			// imprimir gráfico 
			$grafico->Stroke();

			$this->arquivoTemplate = '';		
			return;

		}
		
		
		
		$this->arquivoTemplate = 'relatorio_inadimplente.html';

	
	} else if($op == "bloqueios"){
				if( ! $this->privPodeLer("_COBRANCA_BLOQUEIOS") ) {
								$this->privMSG();
								return;
				}	
		$op = @$_REQUEST["op"];
		$acao = @$_REQUEST["acao"];
		$periodo = @$_REQUEST["periodo"];
		$extra = @$_REQUEST["extra"];
		
		if(!$periodo) $periodo = "12";
		
		if(!$acao) $acao = "geral";
		
		$hoje = date('d/m/Y');
		
		list($dia,$mes,$ano) = explode("/",$hoje);
		
		///echo '<br><br>';
		//echo $dia . "/" . $mes . "/" . $ano ;
		
						
		
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
			$sSQL .= "ORDER BY ano, mes, tipo ";
			*/	
						
		
			$sSQL  = "SELECT ";
			$sSQL .= " * ";
			$sSQL .= "FROM ";
			$sSQL .= " (SELECT  ";
			$sSQL .= "    extract(month from data_hora) as mes, extract(year from data_hora) as ano, count(tipo) as bloqueados  ";
			$sSQL .= " FROM  ";
			$sSQL .= "    lgtb_bloqueio_automatizado  ";
			$sSQL .= " WHERE ";
			$sSQL .= " 	  data_hora > (CAST('$ano-$mes-01' as date)) - INTERVAL '$periodo months' AND ";
			$sSQL .= "    tipo = 'D' ";
			$sSQL .= " GROUP BY ";
			$sSQL .= "    mes,ano) dbq ";
			$sSQL .= " FULL OUTER JOIN ";
			$sSQL .= " (SELECT  ";
			$sSQL .= "   extract(month from data_hora) as mes, extract(year from data_hora) as ano, count(tipo) as desbloqueados  ";
			$sSQL .= "  FROM  ";
			$sSQL .= "   lgtb_bloqueio_automatizado  ";
			$sSQL .= "  WHERE ";			
			$sSQL .= " 	  data_hora > (CAST('$ano-$mes-01' as date)) + INTERVAL '1 month' - INTERVAL '$periodo months' AND ";
			$sSQL .= "   tipo = 'B' OR  tipo = 'S'  ";		
			$sSQL .= "   GROUP BY mes,ano	 ";		
			$sSQL .= "  ) blq USING(mes,ano) ORDER BY mes DESC,ano ASC";
			///echo $sSQL ;
			
	
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
			$sSQL .= "	(cltb_cliente clt INNER JOIN cbtb_cliente_produto cp USING(id_cliente)) INNER JOIN lgtb_bloqueio_automatizado lba USING(id_cliente_produto) INNER JOIN cntb_conta cnt USING(id_cliente_produto),  ";
			$sSQL .= "	prtb_produto as prd ";
			$sSQL .= "WHERE ";
			$sSQL .= "  prd.id_produto = cp.id_produto ";
			$sSQL .= "	AND data_hora < CAST('$ano-$mes-01' as date) + INTERVAL '1 month' ";
			$sSQL .= "	AND data_hora < CAST('$ano-$mes-01' as date) + INTERVAL '1 month' ";
			$sSQL .= "	AND data_hora >= CAST('$ano-$mes-01' as date) ";
			$sSQL .= "ORDER BY ano, mes, dia DESC ,clt.nome_razao ASC";
			
			///echo $sSQL ;
			
			
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
			
			if ($tipo == "D"){
			
				$sSQL .= "  AND lba.tipo = '$tipo'";
			
			}
			
			$sSQL .= "  AND cnt.tipo_conta <> 'E' ";
			$sSQL .= "	ORDER BY ano, mes, dia DESC , clt.nome_razao ASC ";
			
			//////////echo ($sSQL);
			
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
	
						if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
							$this->privMSG();
							return;
				}   
	
	
		$relat = $this->evolucao();
		
		$this->tpl->atribui("relat",$relat);
		$this->arquivoTemplate = "relatorio_produtos_evolucao.html";
		




		if (@$_REQUEST["extra"] == "grafico") {
			
			

			//$relat = $this->bd->obtemRegistros($sSQL);
					
			$evolucao 		= array();
			$adesoes  		= array();
			$cancelaments 	= array();
			$legendas 		= array();
			
			global $_LS_MESES_ANO;

			for($i=count($relat)-1;$i>=0;--$i) {
			   $mes_corrente = substr($_LS_MESES_ANO[$relat[$i]["mes"]],0,3);
			   $legendas[] 		=  $mes_corrente . "/" . $relat[$i]["ano"];
			   $evolucao[] 		= $relat[$i]["num_contratos"];
			   $adesoes[] 		= $relat[$i]["adD"] + $relat[$i]["adBL"] + $relat[$i]["adH"];
			   $cancelamentos[] = ($relat[$i]["cnD"] + $relat[$i]["cnBL"] + $relat[$i]["cnH"]) ;
			}
					
					
			// GERA O Gráfico

			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			$grafico = new Graph(450,250,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			//$grafico->title->Set('Relatório de Adesões');
			$grafico->img->SetMargin(40,40,40,80);
			
			//Imagem de Fundo
			$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
			$grafico->SetMarginColor("white");


			// ADESOES
			$gAdesoes = new LinePlot($adesoes); 
			//$gAdesoes->SetFillGradient("#00aa00","green",GRAD_VER);;
			$gAdesoes->SetColor("#00aa00");

			// CANCELAMENTOS
			$gCancela = new LinePlot($cancelamentos); 
			$gCancela->SetFillGradient("#aa0000","red",GRAD_VER);;
			$gCancela->SetColor("#aa0000");


						
			// EVOLUCAO
			$gEvolucao = new LinePlot($evolucao); 
			//$gEvolucao->SetFillGradient("#0000aa","blue",GRAD_VER);;
			$gEvolucao->SetColor("#0000aa");
			
			// título das barras
			$grafico->xaxis->SetTickLabels($legendas);
			$grafico->xaxis->SetLabelAngle(90);
			// adicionar mostrage de barras ao gráfico 
			$grafico->Add($gAdesoes);
			$grafico->Add($gCancela);
			$grafico->Add($gEvolucao); 

			// imprimir gráfico 
			$grafico->Stroke();
			
			$this->arquivoTemplate = '';		
			return;
		
		}












	
	
	}else if  ($op == "lista"){
	
				if( ! $this->privPodeLer("_RELATORIOS_CLIENTE") ) {
							$this->privMSG();
							return;
				}	
	
	
	
		$tipo = @$_REQUEST["tipo"];
		$id = @$_REQUEST["id"];
	
		if ($tipo == "NAS" || $tipo == "POP"){
	
		$sSQL  = "SELECT ";
		$sSQL .= "cc.id_cliente_produto, cc.id_cliente, cc.username, cc.dominio, cc.tipo_conta, cc.id_conta, ";
		$sSQL .= "cb.username, cb.dominio, cb.tipo_conta, cb.ipaddr, cb.id_nas, cb.id_pop, cb.rede, ";
		$sSQL .= "cl.id_cliente, cl.nome_razao ";
		$sSQL .= "FROM ";
		$sSQL .= "cntb_conta cc, cntb_conta_bandalarga cb, cltb_cliente cl ";
		$sSQL .= "WHERE ";
		$sSQL .= "cc.username = cb.username AND ";
		$sSQL .= "cc.id_cliente = cl.id_cliente AND ";
		$sSQL .= "cc.tipo_conta = 'BL' ";
		$sSQL .= "	AND cb.status = 'A' ";
	
	
	
		switch ($tipo){
			case 'POP':
				$sSQL .= "AND cb.id_pop = '$id' ";
	
			
			break;
			case 'NAS':
				$sSQL .= "AND cb.id_nas = '$id' ";
			
			break;
		
		}
		$sSQL .= " ORDER BY cc.username ASC";
		
		
		//echo $sSQL;
		
		} else if($tipo == "AP"){
		
			$sSQL  = "SELECT ";
			$sSQL .= "cc.id_cliente_produto, cc.id_cliente, cc.username, cc.dominio, cc.tipo_conta, cc.id_conta, ";
			$sSQL .= "cb.username, cb.dominio, cb.tipo_conta, cb.ipaddr, cb.id_nas, cb.id_pop, cb.rede, ";
			$sSQL .= "cl.id_cliente, cl.nome_razao, ";
			$sSQL .= "pop.tipo ";
			$sSQL .= "FROM ";
			$sSQL .= "cntb_conta cc, cntb_conta_bandalarga cb, cltb_cliente cl, cftb_pop pop ";
			$sSQL .= "WHERE ";
			$sSQL .= "cb.id_pop = '$id' AND ";
			$sSQL .= "cb.id_pop = pop.id_pop AND ";
			$sSQL .= "cc.username = cb.username AND ";
			$sSQL .= "cc.id_cliente = cl.id_cliente AND ";
			$sSQL .= "cc.tipo_conta = 'BL' AND ";
			$sSQL .= "pop.tipo = 'AP' ";
			
			//echo $sSQL;
		
		}else if ($tipo == "TODOS"){
			
			$sSQL  = "SELECT ";
			$sSQL .= "cc.id_cliente_produto, cc.id_cliente, cc.username, cc.dominio, cc.tipo_conta, cc.id_conta, ";
			$sSQL .= "cb.username, cb.dominio, cb.tipo_conta, cb.ipaddr, cb.id_nas, cb.id_pop, cb.rede, ";
			$sSQL .= "cl.id_cliente, cl.nome_razao, cl.endereco,cl.id_cidade, ";
			$sSQL .= "cd.cidade,cd.uf,";
			$sSQL .= "pop.tipo ";
			$sSQL .= "FROM ";
			$sSQL .= "cntb_conta cc, cntb_conta_bandalarga cb, cltb_cliente cl, cftb_pop pop ";
			$sSQL .= "WHERE ";
			$sSQL .= "cl.id_cidade = cd.id_cidade ";
			//$sSQL .= "cb.id_pop = '$id' AND ";
			$sSQL .= "cb.id_pop = pop.id_pop AND ";
			$sSQL .= "cc.username = cb.username AND ";
			$sSQL .= "cc.id_cliente = cl.id_cliente AND ";
			$sSQL .= "cc.tipo_conta = 'BL' AND ";
			$sSQL .= "pop.tipo = 'AP' ";
		}
		
		$lista = $this->bd->obtemRegistros($sSQL);
		
		$sSQL = "SELECT nome FROM ";
		
			switch ($tipo){
				case 'POP':
					$sSQL .= "cftb_pop ";
					$sSQL .= "WHERE ";
					$sSQL .= "id_pop = '$id'";
				break;
				case 'AP':
					$sSQL .= "cftb_pop ";
					$sSQL .= "WHERE ";
					$sSQL .= "id_pop = '$id'";
				break;
				case 'NAS':
					$sSQL .= "cftb_nas ";
					$sSQL .= "WHERE ";
					$sSQL .= "id_nas = '$id'";
				break;
	
			}
			
			
		
		$infra = $this->bd->obtemUnicoRegistro($sSQL);
		
		for($i=0;$i<count($lista);$i++) {
			if( $lista[$i]["rede"] ) {
		    	$r = new RedeIP($lista[$i]["rede"]);
		    	$lista[$i]["ipaddr"] = $r->maxHost();
			} else {
		    	//$lista[$i]["ip_cliente"] = $lista[$i]["ipaddr"];
			}
		}
		
		
		
		$this->tpl->atribui("infra",$infra);
		$this->tpl->atribui("tipo",$tipo);
		$this->tpl->atribui("lista",$lista);
		
		if ($tipo == "TODOS"){
			$this->arquivoTemplate = "lista_aps.html";
		}else{
			$this->arquivoTemplate = "relatorio_config_cliente.html";
		}
	}else if ($op == "lista_banda"){

						if( ! $this->privPodeLer("_RELATORIOS_CLIENTE") ) {
							$this->privMSG();
							return;
					}	
	
		
		$banda = @$_REQUEST["banda"]; 
		
	
		if($banda || $banda == "0"){
		
			$sSQL  = "SELECT ";
			$sSQL .= "cbl.username,cbl.upload_kbps,cbl.download_kbps,cbl.tipo_conta,cbl.dominio,cn.id_cliente,cn.username,cn.tipo_conta,cn.dominio, cl.id_cliente, cl.nome_razao as nome ";
			$sSQL .= "FROM ";
			$sSQL .= "cntb_conta_bandalarga cbl, cntb_conta cn, cltb_cliente cl ";
			$sSQL .= "WHERE ";
			$sSQL .= "(cbl.upload_kbps = '$banda' OR cbl.download_kbps = '$banda') AND ";
			$sSQL .= "cbl.username = cn.username AND ";
			$sSQL .= "cbl.tipo_conta = cn.tipo_conta AND ";
			$sSQL .= "cbl.dominio = cn.dominio AND ";
			$sSQL .= "cl.id_cliente = cn.id_cliente ";
			$sSQL .= "ORDER BY ";
			$sSQL .= "nome, cbl.username, cbl.upload_kbps, cbl.download_kbps ASC";
			
			$contas_banda = $this->bd->obtemRegistros($sSQL);
			//echo "BANDA: $sSQL <br>";
			
			if (!count($contas_banda)){
				
				$msg = "Não existe cliente cadastrado com esta banda";
				$contas_banda = "nada";
				$this->tpl->atribui("msg",$msg);
	
			}
			$this->tpl->atribui("banda",$banda);
			$this->tpl->atribui("contas",$contas_banda);
			
		} else {
	
			$sSQL  = "SELECT ";
			$sSQL .= "   b.banda, count(cbl.username) as num_contas "; 
			$sSQL .= "FROM ";
			$sSQL .= "   cftb_banda b LEFT OUTER JOIN cntb_conta_bandalarga cbl ON(cbl.upload_kbps = b.id OR cbl.download_kbps = id) ";
			$sSQL .= "GROUP BY ";
			$sSQL .= "   b.id , b.banda ";
			$sSQL .= "ORDER BY ";
			$sSQL .= "   b.id";
	
			$bandas = $this->bd->obtemRegistros($sSQL);
			////echo "BANDA: $sSQL <br>";
			
			$total_contas = 0;
			for($i=0;$i<count($bandas);$i++) {
			   $total_contas += $bandas[$i]["num_contas"];
			
			}
			
			$this->tpl->atribui("total_contas",$total_contas);
	
			$extra = @$_REQUEST['extra'];
			
			
			$tp_grafico = "3d";
			global $_LS_CORES;
			
			$cores = array();
			
			if( $extra == 'grafico' ) {
				$valores = array();
				$legendas = array();
				for($i=0;$i<count($bandas);$i++) {
					if( $tp_grafico != "3d" || $bandas[$i]["num_contas"] > 0 ) {
						$valores[]  = $bandas[$i]["num_contas"];
						$legendas[] = $bandas[$i]["banda"] ? $bandas[$i]["banda"] : "SEM CONTROLE";
						$cores[] = $_LS_CORES[$i];
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
			
			
			$this->tpl->atribui("bandas",$bandas);
			
			
			
	
			
			
			
			
			
	
		}
		
		$this->arquivoTemplate = "relatorio_banda.html";
		
	
	
	
	
	}else if ($op == "cidades_produto"){
				if( ! $this->privPodeLer("_RELATORIOS_COBRANCA") ) {
							$this->privMSG();
							return;
				}	
	

		$sSQL  = "SELECT ";
		$sSQL .= "   cid.id_cidade,cid.cidade, p.tipo as tipo_conta, ctt.status, count(tipo) as quantidade ,sum(valor_contrato) ";
		$sSQL .= "FROM ";
		$sSQL .= "   cftb_cidade cid INNER JOIN cltb_cliente cli  USING(id_cidade) INNER JOIN  ";
		$sSQL .= "   cbtb_cliente_produto cp USING(id_cliente) INNER JOIN prtb_produto p USING(id_produto), ";
		$sSQL .= "   cbtb_contrato ctt ";
		$sSQL .= "WHERE ";
		$sSQL .= "   ctt.id_cliente_produto = cp.id_cliente_produto ";
		$sSQL .= "   AND cp.excluido is false ";
		$sSQL .= "   AND ctt.status != 'M' ";
		$sSQL .= "   AND ctt.status != 'C' ";
		$sSQL .= "GROUP BY ";
		$sSQL .= "   cid.id_cidade,cid.cidade,p.tipo,ctt.status ";
		$sSQL .= "ORDER BY ";
		$sSQL .= "   cid.cidade,p.tipo,status ";
		$retorno = $this->bd->obtemRegistros($sSQL);
		//echo $sSQL ;

		$dados = array();
		$ultimo_id=0;
		$_id= "";
		$dados_cid = array();
		$ultima_cidade = "";

		for($i=0;$i<count($retorno);$i++) {
			if($retorno[$i]["id_cidade"] != $ultimo_id) {
				if( $ultimo_id ) {
					$dados[] = array('cidade' => $ultima_cidade, 'dados' => $dados_cid, 'id' => $_id);
					$dados_cid = array();
				}
			}
			
			$dados_cid[] = $retorno[$i];
			
			//echo $dados[$i]['cidade']."<br>\n";
			
			$ultimo_id = $retorno[$i]["id_cidade"];
			$ultima_cidade = $retorno[$i]["cidade"];            
			$_id = $retorno[$i]["id_cidade"];

		}
		
		
		$dados[] = array('cidade' => $ultima_cidade, 'dados' => $dados_cid, 'id' => $_id);
		
		
		$this->tpl->atribui("dados",$dados);
		$this->arquivoTemplate = "relatorio_cidades_produtos.html";

	
	
	
	
	}else if ($op == "anatel"){
	
		$sSQL = "SELECT nome,id_pop FROM cftb_pop WHERE tipo = 'AP' and status = 'A' ORDER BY nome";
		$aps = $this->bd->obtemRegistros($sSQL);
		//echo "APS: $sSQL<br>";
		
		for ($i=0;$i<count($aps);$i++){
		
			$sSQL  = "SELECT cn.username,cl.nome_razao,cl.endereco, cl.id_cidade,cd.cidade,cd.uf,cbl.ipaddr,cbl.rede ";
			$sSQL .= "FROM cntb_conta cn, cltb_cliente cl, cftb_cidade cd, cntb_conta_bandalarga cbl ";
			$sSQL .= "WHERE ";
			$sSQL .= "cbl.id_pop = '".$aps[$i]["id_pop"]."' AND ";
			$sSQL .= "cn.tipo_conta = 'BL' AND ";
			$sSQL .= "cn.id_cliente = cl.id_cliente AND ";
			$sSQL .= "cn.username = cbl.username and cn.dominio = cbl.dominio and ";
			$sSQL .= "cl.id_cidade = cd.id_cidade ";
			$sSQL .= "ORDER BY cl.nome_razao ASC ";
			$cli = $this->bd->obtemRegistros($sSQL);
			
			//echo "CLI$i: $sSQL<br>";
			$aps[$i]["cli"] = $cli;
			
			
		
		
		}
		
		$this->tpl->atribui("aps",$aps);
		$this->arquivoTemplate = "relatorio_anatel.html";
	
	
	
	}else if ($op == "sem_mac"){
	
		$sSQL  = "SELECT bl.username, bl.dominio, bl.id_pop, bl.tipo_bandalarga, bl.ipaddr, bl.rede, bl.upload_kbps, bl.download_kbps, bl.status, bl.mac, ";
		$sSQL .= "cn.id_cliente, cl.nome_razao ";
		$sSQL .= "FROM cntb_conta_bandalarga bl, cntb_conta cn, cltb_cliente cl ";
		$sSQL .= "WHERE ";
		$sSQL .= "bl.tipo_conta = 'BL' AND ";
		$sSQL .= "bl.username = cn.username AND bl.tipo_conta = cn.tipo_conta AND bl.dominio = cn.dominio AND ";
		$sSQL .= "cl.id_cliente = cn.id_cliente AND bl.mac is null ";
		$sSQL .= "ORDER BY cl.nome_razao,bl.username";
		$lista = $this->bd->obtemRegistros($sSQL);
		
		if (!$lista){
			$mostrar='false';
		}else{
			$mostrar='true';
		}
		
		//echo "LISTA: $sSQL <br>";
	
		$this->tpl->atribui("lista",$lista);
		$this->tpl->atribui("mostrar",$mostrar);		
		$this->arquivoTemplate = "relatorio_clientes_sem_mac.html";
	
	}else if ($op == "faturamento_comp"){
	
		if( !$this->privPodeGravar("_FATURAMENTO") ) {
					$this->privMSG();
					return;
		}	
		
		$ano = @$_REQUEST["ano"];
		$ano_atual = Date("Y");
		$metodo = @$_REQUEST["metodo"];
		
		
		if (!$ano ){
			$ano = $ano_atual;
			$metodo = "2";
		}
		
		if ($metodo == "1"){
			$titulo = "Comparativo";		
		}else{
			$titulo = "Acumulativo";
		}
		
		$data_inicio = $ano."-01-01";
		$data_final = $ano."-12-31";
		
		
		

		$sSQL  = "SELECT "; 
		$sSQL .= "SUM(valor_pago) as faturamento, ";
		$sSQL .= "EXTRACT(day from data_pagamento) as dia, ";  
		$sSQL .= "EXTRACT(month from data_pagamento) as mes, ";  
		$sSQL .= "EXTRACT(year from data_pagamento) as ano   ";
		$sSQL .= "FROM   ";
		$sSQL .= "cbtb_faturas   ";
		$sSQL .= "WHERE   ";
		$sSQL .= "status = 'P' ";
		$sSQL .= "AND data_pagamento BETWEEN   ";
		$sSQL .= "CAST( '$data_inicio' as date)  ";
		$sSQL .= "AND CAST( '$data_final' as date )  ";
		$sSQL .= "GROUP BY   ";
		$sSQL .= "ano, mes, dia  ";
		$sSQL .= "ORDER BY   ";
		$sSQL .= "dia, mes, ano  ";
		
		$fat = $this->bd->obtemRegistros($sSQL);
		
		$tabela = array();
		
		for($i=0;$i<count($fat);$i++) {
			$tabela[   ((int)$fat[$i]["dia"]) ][   ((int)$fat[$i]["mes"]) ] = $fat[$i]["faturamento"] ;
			//echo "tabela[".((int)$fat[$i]["dia"])."][".((int)$fat[$i]["mes"]) . "] = " . $fat[$i]["faturamento"] . "<br>\n";
			//echo "D: " . ((int)$fat[$i]["dia"]) . "<br>\n";
			//echo "M: " . ((int)$fat[$i]["mes"]) . "<br>\n";
			//echo "V: " . ((int)$fat[$i]["faturamento"]) . "<br>\n";
		}
		
		for($i=1;$i<=31;$i++) {
			if( !@$tabela[$i] ) {
				$tabela[$i]=array();
			}
			for($x=1;$x<=12;$x++) {
				if( !@$tabela[$i][$x] ) {
					$tabela[$i][$x] = 0;
				}
			}
			//echo $tabela[$i]["1"] . " - " . $tabela[$i]["2"] . " - ". $tabela[$i]["3"] . "<br>\n";
		}
		
		
		$this->tpl->atribui("metodo",$metodo);
		$this->tpl->atribui("titulo",$titulo);
		$this->tpl->atribui("ano",$ano);
		$this->tpl->atribui("tabela",$tabela);
		$this->arquivoTemplate = "relatorio_faturamento_periodo.html";

		
	
	
	
	
	}
	
	
	
	
	

	
	//////////////////////////////////////////////////////////////
	
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
				$sSQL .= "	AND cbl.status = 'A' ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     pop.id_ap ";
				$sSQL .= "   ) cli_pop ON( p.id_pop = cli_pop.id_ap)  ";
				$sSQL .= "WHERE ";
				$sSQL .= "   p.tipo = 'AP' AND p.status = 'A'";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   p.nome ";
				
				///echo $sSQL;
				
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
				$sSQL .= " 	AND cbl.status= 'A' ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     pop.id_pop ";
				$sSQL .= "   ) cli_pop ON( p.id_pop = cli_pop.id_pop)  ";
				$sSQL .= "WHERE ";
				$sSQL .= "   p.status != 'D'  ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   p.nome ";
				
				//echo $sSQL;	

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
				$sSQL .= " 	AND cbl.status = 'A' ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     nas.id_nas ";
				$sSQL .= "   ) cli_nas ON( nas.id_nas = cli_nas.id_nas) ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   nas.nome ";

				///echo $sSQL;	

				break;
				
				
		}
		
		return $sSQL ? $this->bd->obtemRegistros($sSQL) : array();
    
    }
    
    public function adesao($acao,$periodo,$data=null){
    
    			
    			if (!$data){
					$data = date("m,Y");

				}
    				list($mes,$ano) = explode(",",$data);
    				//echo "DATA: $data<br>MES: $mes<br>ANO: $ano<br>ACAO:$acao<br>";

    			if ($acao == "geral"){
						global $_LS_MESES_ANO;
						$ls_ultimos_meses = array();

						for ($i=0; $i<12; $i++) {
							list($ca, $cm) = explode("-", date("Y-m", mktime(0, 0, 0, $mes - $i, 1, $ano)));

							//$cperiodo = array( "ano" => $ca, "mes" => $cm);
							$ls_ultimos_meses[] = array( "valor" => $ca."-". $cm, "texto" => $_LS_MESES_ANO[(int)$cm] . "/" . $ca ); 
						}

						


						$sSQL  = "SELECT ";
						$sSQL .= "	count(*) as num_contratos, ";
						$sSQL .= "	EXTRACT( 'month' FROM data_contratacao) as mes, ";
						$sSQL .= "	EXTRACT( 'year' FROM data_contratacao) as ano ";
						$sSQL .= "FROM ";
						$sSQL .= "	cbtb_contrato ";
						$sSQL .= "WHERE ";
						$sSQL .= "	 data_contratacao between (now()) - INTERVAL '$periodo months' AND now() ";
						$sSQL .= "GROUP BY ano, mes ";
						$sSQL .= "ORDER BY ano DESC, mes  DESC ";
							
						/////////////////////echo $sSQL;
						
						
						if ($periodo=="12"){
						$relatorio = $this->bd->obtemRegistros($sSQL);
						
						$relat = array();


						for($i=0;$i<count($ls_ultimos_meses);$i++) {
							$achou = false;
							list($ano,$mes) = explode("-",$ls_ultimos_meses[$i]["valor"]);

							$mes = (int)$mes;
							$ano = (int)$ano;

							for($x=0;$x<count($relatorio);$x++) {
								$m = (int)$relatorio[$x]["mes"];
								$a = (int)$relatorio[$x]["ano"];

							   if( $m == $mes && $a == $ano  ) {
									$achou = true;
									$relat[] = $relatorio[$x];
							   }
							}

							if( !$achou ) {
								$relat[] = array("num_contratos" => 0, "ano" => $ano, "mes" => $mes);
							}


						}				 
						
					}else{
					
						$relat = $this->bd->obtemRegistros($sSQL);
					
					}

					} else if($acao == "sub_ade") {

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
						
						////echo "QUERY: $sSQL<br>";

						$relat = $this->bd->obtemRegistros($sSQL);

					}

						

						for($i=0;$i<count($relat);$i++) {

							$mes = $relat[$i]["mes"];
							$ano = $relat[$i]["ano"];


							///echo $mes = $relat[$i]["mes"];
							///echo $ano = $relat[$i]["ano"];


							$dSQL  = "SELECT ";
							$dSQL .= "	tipo_produto, count(*) as num_contratos,  ";
							$dSQL .= "	EXTRACT( 'month' FROM data_contratacao) as mes, ";
							$dSQL .= "	EXTRACT( 'year' FROM data_contratacao) as ano ";
							$dSQL .= "FROM ";
							$dSQL .= "	cbtb_contrato ";
							$dSQL .= "WHERE ";
							$dSQL .= "	EXTRACT( 'month' FROM data_contratacao) = '$mes' ";
							$dSQL .= " AND EXTRACT( 'year' FROM data_contratacao) = '$ano' ";
							$dSQL .= " AND data_contratacao between now() - INTERVAL '$periodo months' AND now() ";
							$dSQL .= " GROUP BY tipo_produto, ano, mes ";


							//echo $dSQL ."<hr>\n";

							$tp_produto = $this->bd->obtemRegistros($dSQL);

							$relat[$i]["D"] = "0";
							$relat[$i]["BL"] = "0";
							$relat[$i]["H"] = "0";


							for($x=0;$x<count($tp_produto);$x++) {
								
								$relat[$i][ trim($tp_produto[$x]["tipo_produto"])]  = (string)$tp_produto[$x]["num_contratos"];
								///echo $tp_produto[$x]["tipo_produto"] . " - " . (string)$tp_produto[$x]["num_contratos"] . "<bR>\n";
							}

						$relat[$i]["tp_produto"] = $tp_produto ;

						if (($relat[$i]["tp_produto"]) ==""){
						
						$relat[$i]["tp_produto"] = "0";
						
						}

					}
					return($relat);
    		$this->tpl->atribui("periodo",$periodo);
    
    }
    
    public function cancelamento($acao,$periodo,$data=null){
    
    
    	if ($data){
    	
    		list($mes,$ano) = explode(",",$data);
    	
    	}

			global $_LS_MESES_ANO;

			$hoje = date("Y-m-d");
			list($ano, $mes, $dia) = explode("-", $hoje);

			//TODO: Inver ter alista de periodos

			//Cria um array referente aos ultimos 
			$ls_ultimos_meses = array();

    
    
    	if($acao == "geral"){

			for ($i=0; $i<12; $i++) {
				list($ca, $cm) = explode("-", date("Y-m", mktime(0, 0, 0, $mes - $i, 1, $ano)));

				//$cperiodo = array( "ano" => $ca, "mes" => $cm);
				$ls_ultimos_meses[] = array( "valor" => $ca."-". $cm, "texto" => $_LS_MESES_ANO[(int)$cm] . "/" . $ca ); 

			}



				$sSQL  = "SELECT ";
				$sSQL .= "	count(*) as num_contratos, ";
				$sSQL .= "	EXTRACT( 'month' FROM data_alt_status) as mes, ";
				$sSQL .= "	EXTRACT( 'year' FROM data_alt_status) as ano ";
				$sSQL .= "FROM ";
				$sSQL .= "	cbtb_contrato ";
				$sSQL .= "WHERE ";
				$sSQL .= " 	data_alt_status between now() - INTERVAL '$periodo months' AND now() ";
				//$sSQL .= "	AND EXTRACT( 'month' FROM data_alt_status)  = '$cm' ";
				//$sSQL .= "  AND   EXTRACT( 'year' FROM data_alt_status) = '$ca' ";
				$sSQL .= "	AND status = 'C' ";
				$sSQL .= "GROUP BY ano, mes ";
				$sSQL .= "ORDER BY ano, mes ";

				//echo $sSQL . "<br><hr>\n" ;

				$relatorio = $this->bd->obtemRegistros($sSQL);

				$relat = array();

				for($i=0;$i<count($ls_ultimos_meses);$i++) {
					$achou = false;
					list($ano,$mes) = explode("-",$ls_ultimos_meses[$i]["valor"]);

					$mes = (int)$mes;
					$ano = (int)$ano;

					for($x=0;$x<count($relatorio);$x++) {
						$m = (int)$relatorio[$x]["mes"];
						$a = (int)$relatorio[$x]["ano"];

					   if( $m == $mes && $a == $ano  ) {
							$achou = true;
							$relat[] = $relatorio[$x];
					   }
					}

					if( !$achou ) {
						$relat[] = array("num_contratos" => 0, "ano" => $ano, "mes" => $mes);
					}


				}
    	
    	}else if ($acao == "sub_ade"){
    	
    			$ano = @$_REQUEST["ano"];
    			$mes = @$_REQUEST["mes"];
    	
				$data_inicial = date("Y-m-d", mktime(0,0,0,$mes, 1, $ano));
				$data_final = date("Y-m-d",mktime(0,0,0,$mes+1, 1, $ano));

				$sSQL  = "SELECT ";
				$sSQL .= "	clt.id_cliente, clt.nome_razao, cnt.data_contratacao, prd.id_produto, prd.nome, prd.tipo,  ";
				$sSQL .= "	EXTRACT('day' FROM cnt.data_alt_status) as dia, ";
				$sSQL .= "	EXTRACT('month' FROM cnt.data_alt_status) as mes, ";
				$sSQL .= "	EXTRACT('year' FROM cnt.data_alt_status) as ano ";
				$sSQL .= "FROM ";
				$sSQL .= "	prtb_produto prd, cbtb_contrato cnt, cbtb_cliente_produto cp, cltb_cliente clt ";
				$sSQL .= "WHERE  ";
				$sSQL .= "	EXTRACT('month' FROM cnt.data_alt_status) = '$mes' ";
				$sSQL .= " AND	EXTRACT('year' FROM cnt.data_alt_status) = '$ano' AND ";
				$sSQL .= "	cp.id_cliente_produto = cnt.id_cliente_produto AND ";
				$sSQL .= "  cnt.status = 'C' AND ";
				$sSQL .= "	prd.id_produto = cp.id_produto AND clt.id_cliente = cp.id_cliente ";
				$sSQL .= "ORDER BY cnt.data_contratacao, clt.nome_razao ASC ";

				$relat = $this->bd->obtemRegistros($sSQL);
				////echo $sSQL ;
    	
    	}
    	//echo "QUERY: $sSQL<br>";
    	

			

						for($i=0;$i<count($relat);$i++) {


							$mes = $relat[$i]["mes"];
							$ano = $relat[$i]["ano"];

							$relat[$i]["D"] = "0";
							$relat[$i]["BL"] = "0";
							$relat[$i]["H"] = "0";




							if( (int)@$relat[$i]["num_contratos"] ) {

								$dSQL  = "SELECT ";
								$dSQL .= "	tipo_produto, count(*) as num_contratos,  ";
								$dSQL .= "	EXTRACT( 'month' FROM data_alt_status) as mes, ";
								$dSQL .= "	EXTRACT( 'year' FROM data_alt_status) as ano ";
								$dSQL .= "FROM ";
								$dSQL .= "	cbtb_contrato ";
								$dSQL .= "WHERE ";
								$dSQL .= "	EXTRACT( 'month' FROM data_alt_status)  = '$mes' ";
								$dSQL .= "AND   EXTRACT( 'year' FROM data_alt_status) = '$ano' ";
								$dSQL .= " AND	data_alt_status between now() - INTERVAL '$periodo months' AND now() ";
								$dSQL .= " AND status = 'C' ";
								$dSQL .= " GROUP BY tipo_produto, ano, mes ";
								$dSQL .= " ORDER BY tipo_produto,  ano, mes ";

								$tp_produto = $this->bd->obtemRegistros($dSQL);


								for($x=0;$x<count($tp_produto);$x++) {
									$relat[$i][ trim($tp_produto[$x]["tipo_produto"]) ]  = (string)$tp_produto[$x]["num_contratos"];
									///echo $tp_produto[$x]["tipo_produto"] . " - " . (int)$tp_produto[$x]["num_contratos"] . "<bR>\n";
								}

							}

						}

					return($relat);
    
    
    }

	protected function evolucao() {


	
		$adesoes = $this->adesao('geral','12');
		$cancelamentos = $this->cancelamento('geral','12');

		for($i=0;$i<count($adesoes);$i++) {

			// VALORES BRUTOS (ORIGINAL)
			$adesoes[$i]["adBL"] = $adesoes[$i]["BL"];
			$adesoes[$i]["adD"] = $adesoes[$i]["D"];
			$adesoes[$i]["adH"] = $adesoes[$i]["H"];

			$adesoes[$i]["cnBL"] = $cancelamentos[$i]["BL"];
			$adesoes[$i]["cnD"] = $cancelamentos[$i]["D"];
			$adesoes[$i]["cnH"] = $cancelamentos[$i]["H"];

			$adesoes[$i]["ad_num_contratos"] = $adesoes[$i]["num_contratos"];
			$adesoes[$i]["cn_num_contratos"] = $cancelamentos[$i]["num_contratos"];

			// VALORES JÁ CALCULADOS (MODIFICADOS)
			$adesoes[$i]["BL"] = $adesoes[$i]["BL"] - $cancelamentos[$i]["BL"];
			$adesoes[$i]["D"]  = $adesoes[$i]["D"]  - $cancelamentos[$i]["D"];
			$adesoes[$i]["H"]  = $adesoes[$i]["H"]  - $cancelamentos[$i]["H"];
			$adesoes[$i]["num_contratos"] = $adesoes[$i]["num_contratos"] - $cancelamentos[$i]["num_contratos"];
		} 
	
		return($adesoes);
		
	
	}


}



?>
