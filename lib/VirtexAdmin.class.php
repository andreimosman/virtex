<?

require_once("MWebApp.class.php");
require_once("MDatabase.class.php");

class VirtexAdmin extends MWebApp {

	protected $bd;

	public function VirtexAdmin() {
	   parent::MWebApp("etc/virtex.ini",'template/default');
	   
	   if( @$this->cfg->config["DB"]["dsn"] ) {
	      // Instanciar BD;
	      
	      $this->bd = new MDatabase($this->cfg->config["DB"]["dsn"]);
	      
	      
	      if( $this->bd->obtemErro() != MDATABASE_OK ) {
	      
	         echo "ERRO: " . $this->bd->obtemMensagemErro() . "<br>\n";
	      
	      }
	      
	      
	      
	      
	      /**
	      $options = array(
	      	               "debug" => 0,
	      	               "portability" => DB_PORTABILITY_ALL
	      	              );
	      
	      $this->bd =& DB::connect($this->cfg->config["DB"]["dsn"],$options);
	      
	      if( PEAR::isError($this->bd) ) {
	         echo "ERRO: " . $this->bd->getMessage();
	      }
	      
	      */
	      
	      
	      
	   }
	}
	
	
	public function processa($op=null) {
	   // Não faz nada por hora.
	}


}



?>
