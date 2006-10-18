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
		
		
		
		
		
		}
	
	
	
	
	
	
	
	
	}
	
	
	
	
	
	
	

public function __destruct() {
      	parent::__destruct();
}

}



?>
