<?

class VAAjax extends VirtexAdminWeb {

	public function __construct() {
		parent::__construct();
	
		$adm = $this->admLogin->obtemAdmin();
		$this->tpl->atribui("admin",$adm);	

		$this->arquivoTemplate = "ajax-teste.html";

	}
	
	
	public function processa($op=null) {
		header("Pragma: no-cache");
		/**
		 * Lista de produtos disponíveis de um determinado tipo
		 */
		if( $op == "lista_produtos" ) {
			$this->arquivoTemplate = "ajax-lista.html";

			$tipo = @$_REQUEST['tipo'];

			if( $tipo ) {
				$sSQL  = "SELECT ";
				$sSQL .= "   * ";
				$sSQL .= "FROM ";
				$sSQL .= "   prtb_produto ";
				$sSQL .= "WHERE ";
				$sSQL .= "   disponivel is true ";
				$sSQL .= "   AND tipo = '$tipo'";
				
				$lista = $this->bd->obtemRegistros($sSQL);
				
				$this->tpl->atribui('lista',$lista);
				
			}
		} else if( $op == "endereco_nas" ) {
			/**
			 * Verifica se um determinado endereço pertence ao NAS especificado
			 */
			
			$registros = array();
			
			$endereco = @$_REQUEST["endereco"];
			$id_nas   = @$_REQUEST["id_nas"];
			
			if( $endereco && $id_nas ) {

				$vaC = new VAClientes();
				$nas = $vaC->obtemNAS($id_nas);
				
				if( @$nas["id_nas"] ) {
				
					//echo "NAS: " . $nas["tipo_nas"] . "<br>\n";
					$sSQL = "SELECT rede FROM cftb_nas_rede WHERE (rede >> '$endereco' or rede = '$endereco') AND id_nas = '$id_nas'";
					$_rede = $this->bd->obtemUnicoRegistro($sSQL);
					$rede = @$_rede["rede"];
				
					if( $nas["tipo_nas"] == "I" ) {
						// Nas tipo ip, procurar rede
						if( $rede ) {
							$registros[] = array( "endereco" => $rede, "id_nas" => $id_nas );
						}
						
					} else {
						// Nas tipo pppoe. Identificar se o endereço existe
						if( $rede ) {
							$registros[] = array( "endereco" => $endereco, "id_nas" => $id_nas );
						}
					}
				} else {
					// echo "NAS INVALIDO";	
				}
				
				
				
			}
			
			$this->tpl->atribui("registros",@$registros);
			
			// Arquivo genérico utilizado para retorno
			$this->arquivoTemplate = "ajax-generic.xml";
			
			header("Content-type: text/xml");
		
		} else if( $op == "teste" ) {
			$this->arquivoTemplate = "ajax-teste.html";
		}
	}

	public function __destruct() {
			parent::__destruct();
	}

}



?>
