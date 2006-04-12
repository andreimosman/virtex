<?

	$sSQL  = "SELECT ";
	$sSQL .= "f.id_cliente_produto, f.data, f.valor, f.status, f.observacoes, f.reagendamento, f.pagto_parcial, ";
	$sSQL .= "f.data_pagamento, f.desconto, f.acrescimo, f.valor_pago, ";
	$sSQL .= "c.id_cliente_produto, c.id_cliente ";
	$sSQL .= "FROM ";
	$sSQL .= "cbtb_faturas f, cbtb_cliente_produto c ";
	$sSQL .= "WHERE ";
	$sSQL .= "id_cliente = '$id_cliente' ";
	$sSQL .= "AND ";
	$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";

	$lista_faturas = $this->bd->obtemRegistros($sSQL);
	//echo "SQL: $sSQL<br>";
	//echo "lista: $lista_faturas<br>";
	
	/*$idclienteprod = $lista_faturas[1]["id_cliente_produto"];
	
	$sSQL  = "SELECT id_produto, nome, descricao ";
	$sSQL .= "FROM ";
	$sSQL .= "prtb_produtos ";
	$sSQL .= "WHERE ";
	$sSQL .= "id_produto in (SELECT id_produto FROM cbtb_contrato WHERE id_cliente_produto = '$idclienteprod')";
	
	$_prod = $this->bd->obtemRegistros($sSQL);
	
	echo "SQL2: $sSQL<br>";
	echo "prod: $_prod<br>";
	$lista_faturas = array_push($lista_faturas,$_prod);*/
	
	
	$this->tpl->atribui("lista_faturas",$lista_faturas);


?>