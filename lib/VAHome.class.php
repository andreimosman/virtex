<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAHome extends VirtexAdmin {

	public function VAHome() {
		parent::VirtexAdmin();
	
		$adm = $this->admLogin->obtemAdmin();
		$this->tpl->atribui("admin",$adm);	



		$this->arquivoTemplate = "home.html";
	
	
	}
	
	
	public function processa($op=null) {
	
		if( $op == "home" ) {
		   
				$licenca = $this->lic->obtemLicenca();
				$hoje = Date("Y-m-d");
				//echo $hoje;
				if($licenca["geral"]["expira_em"] < $hoje && $licenca["geral"]["congela_em"] > $hoje){
					$status = "expirado";
				}else if ($licenca["geral"]["congela_em"] < $hoje ){
					$status = "congelado";
				}else if ($licenca["geral"]["congela_em"] > $hoje && $licenca["geral"]["expira_em"] > $hoje){
					$status = "ativo";
				}


		$sSQL  = "SELECT ";
		$sSQL .= "f.id_cliente_produto, cl.nome_razao, f.data_renovacao, f.valor_contrato, p.nome, ";
		$sSQL .= " c.id_cliente ";
		$sSQL .= "FROM ";
		$sSQL .= " cbtb_cliente_produto c, cltb_cliente cl, cbtb_contrato f, prtb_produto p ";
		$sSQL .= "WHERE ";
		$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
		$sSQL .= "AND p.tipo = f.tipo_produto ";
		$sSQL .= "AND f.data_renovacao <= now() + interval '30 day' ";
		$sSQL .= "AND cl.id_cliente = c.id_cliente  ";
		$sSQL .= "AND c.id_cliente_produto = f.id_cliente_produto ";
		$sSQL .= "AND p.id_produto = c.id_produto ";
		$sSQL .= "ORDER BY f.data_renovacao ASC ";
		
		$lista_contrato = $this->bd->obtemRegistros($sSQL);
		
		
			$cSQL  = "SELECT";
			$cSQL .= "   f.data,f.descricao,f.valor,f.status,ctt.status as cnt_status, ";
			$cSQL .= "   cp.id_cliente_produto, cnt.username, prd.tipo, ";
			$cSQL .= "	 cl.id_cliente, cl.nome_razao ";
			$cSQL .= "FROM ";
			$cSQL .= "	 ((cltb_cliente cl INNER JOIN cbtb_cliente_produto cp USING (id_cliente)) INNER JOIN cntb_conta cnt USING(id_cliente_produto)) ";
			$cSQL .= "	 INNER JOIN ";
			$cSQL .= "   (cbtb_faturas f INNER JOIN cbtb_contrato ctt USING(id_cliente_produto))";
			$cSQL .= "	 USING(id_cliente_produto), prtb_produto as prd " ;
			$cSQL .= "WHERE ";
			$cSQL .= "   prd.id_produto = cp.id_produto AND ";
			$cSQL .= "	 cnt.conta_mestre is true AND ";
			$cSQL .= "   CASE WHEN ";
			$cSQL .= "      f.reagendamento is not null ";
			$cSQL .= "   THEN ";
			$cSQL .= "      f.reagendamento < CAST(now() as date)  ";
			$cSQL .= "   ELSE ";
			$cSQL .= "      f.data < CAST(now() as date) - INTERVAL '10 days' ";
			$cSQL .= "   END  ";
			$cSQL .= "   AND (f.status != 'P' AND f.status != 'E' AND f.status != 'C') ";
			$cSQL .= "   AND ctt.status = 'A' AND cnt.status = 'A'";
			$cSQL .= "ORDER BY f.data, cl.nome_razao, f.descricao, f.status, f.valor";
			
		$lista_bloqueios = $this->bd->obtemRegistros($cSQL);
		
		echo $cSQL ;
	
		$this->tpl->atribui("status",$status);
		$this->tpl->atribui("licenca",$licenca);
		$this->tpl->atribui("lista_contrato",$lista_contrato);
		$this->tpl->atribui("lista_bloqueios",$lista_bloqueios);

	   $this->arquivoTemplate = "home_principal.html";
		   
		}
	
	
	
	
	}

public function __destruct() {
      	parent::__destruct();
}

}



?>
