<?
	$id_cliente = @$_REQUEST["id_cliente"];
		
	$sSQL  = "SELECT ";
	$sSQL .= "f.id_cliente_produto, f.data, f.valor, f.status, f.observacoes,f.descricao, f.reagendamento, f.pagto_parcial, ";
	$sSQL .= "f.data_pagamento, f.desconto, f.acrescimo, f.valor_pago, ";
	$sSQL .= "c.id_cliente_produto, c.id_cliente ";
	$sSQL .= "FROM ";
	$sSQL .= "cbtb_faturas f, cbtb_cliente_produto c ";
	$sSQL .= "WHERE ";
	$sSQL .= "id_cliente = '$id_cliente' ";
	$sSQL .= "AND ";
	$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
	$sSQL .= "ORDER BY f.data ASC ";

	$lista_faturas = $this->bd->obtemRegistros($sSQL);
	//echo "Lista: $sSQL <br>";
	
	$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
	$cliente = $this->bd->obtemUnicoRegistro($sSQL);
	
	
	$sSQL  = "SELECT ";
	$sSQL .= "	ct.id_cliente_produto, ct.data_contratacao, ct.vigencia, ct.id_produto, ct.tipo_produto, ct.valor_contrato, ct.status, ";
	$sSQL .= "	cl.id_cliente_produto, cl.id_cliente, ";
	$sSQL .= "	pr.id_produto, pr.nome ";
	$sSQL .= "FROM ";
	$sSQL .= "	cbtb_contrato ct, cbtb_cliente_produto cl, prtb_produto pr ";
	$sSQL .= "WHERE ";
	$sSQL .= "	cl.id_cliente_produto = ct.id_cliente_produto  AND cl.id_cliente = '$id_cliente' AND ct.id_produto = pr.id_produto";
	
	$lista_contrato = $this->bd->obtemRegistros($sSQL);
	
	echo "lista: $sSQL <br>";
	
	$this->tpl->atribui("lista_contrato",$lista_contrato);
	$this->tpl->atribui("cliente",$cliente);
	$this->tpl->atribui("id_cliente", $id_cliente);
	$this->tpl->atribui("lista_faturas",$lista_faturas);

?>