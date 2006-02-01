<?



/**
 * Definições
 */

define('PATH_LIB','./lib');
define('PATH_ETC','./etc');




/**
 * Requerimentos
 * Aqui são incluidos todos os arquivos de classes que chamam os templates (templates/default)
 */
require_once(PATH_LIB . '/VirtexAdmin.class.php');
require_once(PATH_LIB . '/VAHome.class.php');
require_once(PATH_LIB . '/VAClientes.class.php');
require_once(PATH_LIB . '/VACobranca.class.php');
require_once(PATH_LIB . '/VASuporte.class.php');
require_once(PATH_LIB . '/VAConfiguracao.class.php');
require_once(PATH_LIB . '/VAAdministrador.class.php');
require_once(PATH_LIB . '/VARelatorio.class.php');

?>
