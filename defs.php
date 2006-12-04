<?

if(!defined('_DEFS_PHP')) {
	define('_DEFS_PHP',true);


/**
 * Path para os aplicativos
 */

ini_set('include_path',ini_get('include_path').':/mosman/virtex/cfrontend/framework:/usr/local/share/pear:/usr/local/share/smarty');

/**
 * Definições
 */

define('PATH_LIB','./lib');
define('PATH_ETC','./etc');
define('PATH_TMP','./tmp');


/**
 * Carrega dinamicamente os arquivos de acordo com o nome da classe
 * Verifica de acordo com algumas notações.
 */

function __autoload($class_name) {
  $pear_dirs = array("/usr/local/share/pear","/usr/share/pear");

   $possibilidades = array();
   $possibilidades[] = PATH_LIB . "/" . $class_name . ".class.php";
   $possibilidades[] = $class_name . "class.php";
   $possibilidades[] = $class_name . ".php";
   //$possibilidades[] = $class_name . "/" . $class_name . ".php";
   $possibilidades[] = str_replace("_","/",$class_name) . ".php";

   $encontrado = 0;
   for($i=0;$i<count($possibilidades);$i++) {
       if( @include_once($possibilidades[$i]) ) {
         $encontrado = 1;
         break;
       }
   }
   if( !$encontrado ) {
     die("Classe nao encontrada: $class_name \n");
   }
}

//session_start();

}

?>
