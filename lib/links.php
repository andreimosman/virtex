<?

	$id_link = @$_REQUEST["id_link"];
	$sop = @$_REQUEST["sop"];
	$pp = @$_REQUEST["pp"];
	
	//Mostra os Links
	
	$sSQL = "SELECT * FROM cftb_links ORDER BY titulo";
	$lista_link = $this->bd->obtemRegistros($sSQL);
	
	$this->tpl->atribui("lista",$lista_link);

	if ($id_link){
		$acao = "alt";
	} else {

		$acao = "cad";

	}	
	
	if (!$sop && ($acao == "alt"||$pp == "new")){
	
		//echo "ACAO: $acao<br>";
		$sSQL = "SELECT * FROM cftb_links WHERE id_link = $id_link";
		$link = $this->bd->ObtemUnicoRegistro($sSQL);
		
		$this->tpl->atribui("lista",$link);
		$this->tpl->atribui("acao",$acao);
		$this->tpl->atribui("op",$op);
		$this->arquivoTemplate = "configuracao_cadastro_links.html";
		return;
	
	
	
	
	}
	
	if ($sop == "ok"){

		$titulo = @$_REQUEST["titulo"];
		$url = @$_REQUEST["url"];
		$target = @$_REQUEST["target"];
		$descricao = @$_REQUEST["descricao"];

		
		if ($acao == "alt") {
			
				if($pp == "excluir"){
					$sSQL = "DELETE FROM cftb_links WHERE id_link = $id_link";
					$this->bd->consulta($sSQL);
				}else{
				
					$sSQL = "UPDATE cftb_links SET titulo = '$titulo', url = '$url', target = '$target', descricao = '$descricao' WHERE id_link = $id_link";
					$this->bd->consulta($sSQL);
				}
				
			
			
			
		}else if ($acao == "cad"){
			
				$sSQL = "INSERT INTO cftb_links (titulo,url,target,descricao) VALUES ('$titulo','$url','$target','$descricao') ";
				$this->bd->consulta($sSQL);
			
			
			
		
		
		
		}
			//echo "SQL: $sSQL <br>";
			$sSQL = "SELECT * FROM cftb_links ORDER BY titulo";
			$lista_link = $this->bd->obtemRegistros($sSQL);
				
			$this->tpl->atribui("lista",$lista_link);
			
	}
	
	$this->arquivoTemplate = "configuracao_links_lista.html";
	
	
	
	
	
	

	
	
	
	
	
	
	




?>