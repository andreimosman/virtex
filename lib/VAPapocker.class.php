<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAPapocker extends VirtexAdmin {

	public function VAPapocker() {
		parent::VirtexAdmin();
	}
	
	
	public function processa($op="") {
	
	
		if ($op == "acerto_contrato"){
		
			$sSQL = "SELECT * FROM cbtb_cliente_produto";
			$produtos = $this->bd->obtemRegistros($sSQL);
			
			
			
			for ($i=0;$i<count($produtos);$i++){
			
				$id_cliente_produto = $produtos[$i]["id_cliente_produto"];
				$id_produto = $produtos[$i]["id_produto"];
			
			
				$sSQL = "UPDATE cbtb_contrato SET id_produto = $id_produto WHERE id_cliente_produto = $id_cliente_produto";
				$this->bd->consulta($sSQL);
			
				echo "<br>$i<br>";
				echo "id_cliente_produto = $id_cliente_produto<br>";
				echo "id_produto: $id_produto<br>";
			
				
			
			
			
			}
		
		
		
		
		
		}else if ($op == "cria_contratos"){
		
		
			$sSQL = "SELECT cp.id_cliente, cp.id_cliente_produto, cp.id_produto, cn.tipo_conta FROM cbtb_cliente_produto cp, cntb_conta cn WHERE cp.excluido = false AND cp.id_cliente_produto = cn.id_cliente_produto";
			$produtos = $this->bd->obtemRegistros($sSQL);
			
			
			
			
			for ($i=0;count($produtos);$i++){
			
				$id_cliente_produto = $produto[$i]["id_cliente_produto"];
				$id_produto = $produto[$i]["id_produto"];
				$id_cobranca = 3;
				$tipo_produto = $produtos[$i]["tipo_conta"];
				
				
				$sSQL  = "INSERT INTO cbtb_contrato (id_cliente_produto, id_produto, data_contratacao, valor_contrato, id_cobranca, status, tipo_produto, ";
				$sSQL .= "valor_produto, num_emails, quota_por_conta, tx_instalacao, valor_comodato, carencia) VALUES ( ";
				$sSQL .= "$id_cliente_produto, $id_produto, '2006-10-23', '0.00',$id_cobranca, 'A', '$tipo_produto', '0.00', '1', '50000', '0.00', '0.00', '20' ";
				$this->bd->consulta($sSQL);
				
				echo "NUM: $i<br>ICP: $id_cliente_produto<BR>IP: $id_produto<br>TIPO: $tipo_produto<br>";
				
				
			}
			
			echo "TODOS OS CLIENTES CADASTRADOS: TOTAL: ."$i+1."<br>";
			
			
			
			
		
		
		}
	
	
	
	
	
	
	
	
	}
	
	
	
	
	
	
	

public function __destruct() {
      	parent::__destruct();
}

}



?>
