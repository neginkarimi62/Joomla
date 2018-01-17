<?php
/**
 * @package Joomla.Site
 * @subpackage mod_zinioloader
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( dirname(__FILE__).'/helper.php' );
 
$rbdigitalLink = modZinioConnector::connectToZinio($params);
$rbdigitalDescription = modZinioConnector::getDescription($params);
require( JModuleHelper::getLayoutPath( 'mod_zinioloader' ) );
?>