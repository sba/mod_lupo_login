<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_lupo_categories
 *
 * @copyright   Copyright (C) databauer / Stefan Bauer 
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

require_once( dirname(__FILE__).'/helper.php' );
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$list            = ModLupoLoginHelper::getToys($params);

$jinput = JFactory::getApplication()->input;
$do_login = $jinput->get('lupo_clientlogin', false);
if($do_login=='login'){
	$lupo_login = ModLupoLoginHelper::clientLogin($jinput->get('adrnr', false), $jinput->get('password', false));
}
if($do_login=='logout'){
	ModLupoLoginHelper::clientLogout();
}

$session = JFactory::getSession();
$client = $session->get('lupo_client');

if($client){
	$module->title = $client->firstname . ' ' . $client->lastname;
	$toylist = ModLupoLoginHelper::getToys($client->adrnr);
}

require JModuleHelper::getLayoutPath('mod_lupo_login', $params->get('layout', 'default'));
