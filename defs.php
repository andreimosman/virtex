<?

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
 * Requerimentos
 * Aqui são incluidos todos os arquivos de classes que chamam os templates (templates/default)
 */
require_once(PATH_LIB . '/status.defs.php');
require_once(PATH_LIB . '/redes.class.php');
require_once(PATH_LIB . '/Spool.class.php');
require_once(PATH_LIB . '/AdminLogin.class.php');
require_once(PATH_LIB . '/VirtexAdmin.class.php');
require_once(PATH_LIB . '/VAGrafico.class.php');
require_once(PATH_LIB . '/VALogin.class.php');
require_once(PATH_LIB . '/VAHome.class.php');
require_once(PATH_LIB . '/VAClientes.class.php');
require_once(PATH_LIB . '/VACobranca.class.php');
require_once(PATH_LIB . '/VASuporte.class.php');
require_once(PATH_LIB . '/VAConfiguracao.class.php');
require_once(PATH_LIB . '/VAAdministrador.class.php');
require_once(PATH_LIB . '/VARelatorio.class.php');
require_once(PATH_LIB . '/VAInterface_cliente.class.php');
require_once(PATH_LIB . '/VAInterface_cliente_home.class.php');
require_once(PATH_LIB . '/spool_hospedagem.class.php');
require_once(PATH_LIB . '/VABackup.class.php');


//function __autoload($class_name) {
//   require_once PATH_LIB . "/" . $class_name . '.class.php';
//}

session_start();

?>