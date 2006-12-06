<?

	/**
	 * VABEUpdate.class.php
	 *
	 * Sistema do atualização automática do VA.
	 *
	 */

	class VABEUpdate extends VirtexAdminBackend {
		protected $sep;

		/**
		 * Construtor
		 */
		public function __construct() {
			parent::__construct();
			$this->initVars();

			// Opções de linha de comando
			$this->_shortopts = "ABC:";
			$this->getopt();
			$this->bd->preparaReverso();
			
		}
		
		protected function initVars() {
			$this->sep = ":::";
		}
		
		public function executa() {
			$xml = new MXMLUtils();
		
			for($i=0;$i<count($this->options);$i++) {
				switch($this->options[$i][0]) {
				
					// Gera a informação do banco de dados local
					// Usado geralmente para publicação da nossa estrutura.
					case 'B':
						//echo $this->writeDatabaseInfoText($this->loadLocalDatabaseInfo());
						echo $xml->a2x($this->bd->obtemEstrutura(),"database");
						
						break;
					
					// Compara o banco de dados local com o arquivo especificado.
					// Usado geralmente nos procedimentos pré-atualização
					// -A pega o script padrao
					// -C especifica o script
					case 'A':
						$arquivo = "upd/vabd.xml";
					case 'C':

						$texto = "";
						if(!@$arquivo) {
							$arquivo = $this->options[$i][1];
						}
						$erro = 0;
						
						//echo "Arquivo: $arquivo\n";

						$fd = @fopen($arquivo,"r");
						if( !$fd ) {
							// Erro ao abrir o arquivo
							$erro = 2;
						} else {
							$texto = fread($fd,filesize($arquivo));
							fclose($fd);
						}
						
						//echo "ERRO: $erro \n";
						//echo "TEXTO: $texto \n";
						
						if( !$erro ) {
							$file_info  = $xml->x2a($texto,"database");
							unset($texto);
							$local_info = $this->bd->obtemEstrutura();
							
							$script = $this->bd->scriptModificacao($local_info,$file_info);
							unset($local_info,$file_info);
							
							echo $this->bd->script2text($script);
							//$this->compareDatabaseInfo($local_info,$file_info);
							
						}
				}
			}
			return;
		}
	}
	
?>