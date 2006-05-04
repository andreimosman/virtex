<?

require_once("jpgraph.php");
require_once("jpgraph_pie.php");
require_once("jpgraph_pie3d.php");

if ($op == "lista"){
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



	switch ($tipo){
		case 'POP':
			$sSQL .= "AND cb.id_pop = '$id' ";

		
		break;
		case 'NAS':
			$sSQL .= "AND cb.id_nas = '$id' ";
		
		break;
	
	}
	$sSQL .= " ORDER BY cc.username ASC";
	
	
	
	
	} else if($tipo == "AP"){
	
		$sSQL  = "SELECT ";
		$sSQL .= "cc.id_cliente_produto, cc.id_cliente, cc.username, cc.dominio, cc.tipo_conta, cc.id_conta, ";
		$sSQL .= "cb.username, cb.dominio, cb.tipo_conta, cb.ipaddr, cb.id_nas, cb.id_pop, cb.rede, ";
		$sSQL .= "cl.id_cliente, cl.nome_razao, ";
		$sSQL .= "pop.tipo ";
		$sSQL .= "FROM ";
		$sSQL .= "cntb_conta cc, cntb_conta_bandalarga cb, cltb_cliente cl, cftb_pop pop ";
		$sSQL .= "WHERE ";
		$sSQL .= "cc.username = cb.username AND ";
		$sSQL .= "cc.id_cliente = cl.id_cliente AND ";
		$sSQL .= "cc.tipo_conta = 'BL' AND ";
		$sSQL .= "pop.tipo = 'AP' ";
		
		//echo "QUERY: $sSQL <br>";
	
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
	
	$this->arquivoTemplate = "relatorio_config_cliente.html";

}else if ($op == "lista_banda"){
	
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
		$sSQL .= "   cftb_banda b LEFT OUTER JOIN cntb_conta_bandalarga cbl ON(cbl.upload_kbps = b.banda OR cbl.download_kbps = banda) ";
		$sSQL .= "GROUP BY ";
		$sSQL .= "   b.banda ";
		$sSQL .= "ORDER BY ";
		$sSQL .= "   b.banda";

		$bandas = $this->bd->obtemRegistros($sSQL);
		
		
		$extra = @$_REQUEST['extra'];
		
		$tp_grafico = "2d";
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
	




}





?>