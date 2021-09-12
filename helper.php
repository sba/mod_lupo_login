<?php
/**
 * @package     LUPO
 * @copyright   Copyright (C) databauer / Stefan Bauer
 * @author      Stefan Bauer
 * @link        https://www.ludothekprogramm.ch
 * @license     License GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_lupo_login
 *
 */
class ModLupoLoginHelper {
	function __construct() {
		if (!class_exists('LupoModelLupoClient')) {
			JLoader::import('lupo', JPATH_BASE . '/components/com_lupo/models');
		}
	}

	/**
	 * do client login and save client vars to session
	 *
	 * @param $adrnr
	 * @param $password
	 *
	 * @return bool true if login successful
	 */
	public static function clientLogin($adrnr, $password) {
		$model       = new LupoModelLupoClient();
		$clientLogin = $model->clientLogin($adrnr, $password);

		$app = JFactory::getApplication();
		$app->redirect(JURI::current() . ($clientLogin ? '' : '?loginError'));
	}

	/**
	 * kills client login-session
	 */
	public static function clientLogout() {
		$model = new LupoModelLupoClient();
		$model->clientLogout();
	}

	/**
	 * send password and username to email
	 */
	public static function sendPassword($email) {
		$model = new LupoModelLupoClient();
		$res   = $model->getClient($email);

		if ($res) {
			$mailer = JFactory::getMailer();
			$config = JFactory::getConfig();

			$sent = false;
			if ($mailer->ValidateAddress($email)) {
				$subject = JText::_('MOD_LUPO_LOGIN_RESET_EMAIL_SUBJECT');
				$subject = sprintf($subject, $config->get('sitename'));
				$body    = JText::_('MOD_LUPO_LOGIN_ADRNR') . ': ' . $res->adrnr . "\n";
				$body    .= JText::_('MOD_LUPO_LOGIN_PASSWORD') . ': ' . $res->username . "\n\n";

				$module        = JModuleHelper::getModule('mod_lupo_loginlink');
				$params        = new JRegistry($module->params);
				$loginlink_url = $params->get('loginlink_url');

				$body .= JURI::base() . $loginlink_url . "?ll=" . $res->adrnr . "-" . $res->username . "\n";
				$mailer->setSubject($subject);
				$mailer->setBody($body);

				$sender = [
					$config->get('mailfrom'),
					$config->get('fromname'),
				];
				$mailer->setSender($sender);
				$mailer->addRecipient($res->email);
				$sent = $mailer->Send();
			}

			return $sent ? 'mail_sent' : 'mail_error';
		} else {
			return "not_found";
		}
	}

	/**
	 * Retrieve list of borrowed toys
	 *
	 * @param $adrnr
	 *
	 * @return  mixed
	 */
	public static function getToys($adrnr) {
		$model   = new LupoModelLupoClient();
		$toylist = $model->getClientToys($adrnr);

		return $toylist;
	}

	/**
	 * Checks if at least one mail ist in database stored
	 * if yes, we assume that lupo uploads the mails (from 2021.2.9)
	 *
	 * @return bool
	 */
	public static function hasEmails() {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__lupo_clients')
			->where('NOT ISNULL(email)');
		$db->setQuery($query);
		$numRows = $db->loadObject();

		return $numRows !== null;
	}
}
