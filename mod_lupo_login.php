<?php
/**
 * @package     LUPO
 * @copyright   Copyright (C) databauer / Stefan Bauer
 * @author      Stefan Bauer
 * @link        https://www.ludothekprogramm.ch
 * @license     License GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

require_once(dirname(__FILE__) . '/helper.php');
new ModLupoLoginHelper();
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$list            = ModLupoLoginHelper::getToys($params);

$app    = JFactory::getApplication('site');
$jinput = $app->input;

$loginlink = $jinput->get('ll', false);
if ($loginlink) {
    list ($adrnr, $password) = explode('-', $loginlink, 2);
    $lupo_login = ModLupoLoginHelper::clientLogin($adrnr, $password);
}

$do_login = $jinput->get('lupo_clientlogin', false);
if ($do_login == 'login') {
    $lupo_login = ModLupoLoginHelper::clientLogin($jinput->get('adrnr', false), $jinput->get('password', false, 'STRING'));
}
if ($do_login == 'logout') {
    ModLupoLoginHelper::clientLogout();
}

$password_sent = null;
if($jinput->get('password-reset', false)) {
	$email = $jinput->get('email', false, 'string');
	$password_sent = ModLupoLoginHelper::sendPassword($email);
}

$password_reset_enabled=ModLupoLoginHelper::hasEmails();

//load the correct session
$session      = $app->getSession();
$client       = $session->get('lupo_client');
$reservations = $session->get('lupo_reservations');

if ($client) {
    $module->title = $client->firstname . ' ' . $client->lastname;
    $toylist       = ModLupoLoginHelper::getToys($client->adrnr);
}

//load component parameter
jimport('joomla.application.component.helper');
$allow_prolongation = JComponentHelper::getParams('com_lupo')->get('lupo_prolongation_enabled', "0");

require JModuleHelper::getLayoutPath('mod_lupo_login', $params->get('layout', 'default'));
