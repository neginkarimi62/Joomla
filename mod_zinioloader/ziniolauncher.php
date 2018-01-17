<?php
//defined( '_JEXEC' ) or die( 'Restricted access' );
define("JOOMLA_MODULEROOT", 2);  //it can be used with getPath to point to Modules folder
define("JOOMLA_ROOT", 3);        //it can be used with getPath to point to Joomla folder
define("WEB_ROOT", 4);           //it can be used with getPath to point to Web Server folder
/**
 * Returns the top level directory
 */

function getPath($filename, $level) {
    if ($level < 1) {
        return $filename;
    }
    return getPath(dirname($filename), --$level);
}

//To find Joomla Root from a module you need to set the $level value to 3
define('JPATH_BASE', getPath(__FILE__, JOOMLA_ROOT));
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

require_once (JPATH_BASE . DS . 'configuration.php' );
require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php' );
require_once (JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'factory.php' );
$mainframe = & JFactory::getApplication('site');
$mainframe->initialise();
$user = JFactory::getUser();

$stAPI = filter_input(INPUT_GET, 'api_url', FILTER_SANITIZE_URL);
$UserSeq = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);
$stToken = filter_input(INPUT_GET, 'library_token', FILTER_SANITIZE_STRING);
$stLibId = filter_input(INPUT_GET, 'library_id', FILTER_SANITIZE_STRING);

$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select($db->quoteName(array('email', 'userpwd')));
$query->from($db->quoteName('#__user_catcher'));
$query->where('id=' . $UserSeq);
$db->setQuery($query);
$result = $db->loadObject();

$user_detail = array(
    'password' => '0G4MGRR5JC',
    'email' => $result->email
    );

$stParams = "cmd=p_login&lib_id=" . $stLibId . "&token=" . $stToken . "&email=" . $user_detail['email'] . "&pwd=" . $user_detail['password'];

// Check if we have curl or not
if (function_exists('curl_init')) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $stAPI);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $stParams);

    $arRtn = explode("\t", curl_exec($curl));
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    switch ($httpcode) {
        case 200: {
            if ($arRtn[0] == 'OK') {
                header("Location: " . $arRtn[1]);
            } else {
                echo "error exception: " . $arRtn[0] . " " . $arRtn[1];
                }
                break;
                }
        default: {
            print_r($httpcode);
            }
        }

    } else {
        echo "No curl detected!";
        // not installed cUrl
    }
?>
