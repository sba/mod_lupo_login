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
class ModLupoLoginHelper
{
	function __construct() {
		if (!class_exists( 'LupoModelLupoClient' )){
			JLoader::import( 'lupo', JPATH_BASE . '/components/com_lupo/models' );
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
	public static function clientLogin($adrnr, $password)
	{
		$model = new LupoModelLupoClient();
		$clientLogin = $model->clientLogin($adrnr, $password);

		$app = JFactory::getApplication();
		$app->redirect(JURI::current().($clientLogin?'':'?loginError'));
	}

	/**
	 * kills client login-session
	 */
	public static function clientLogout()
	{
		$model = new LupoModelLupoClient();
		$model->clientLogout();
		return;
	}
	
	/**
	 * Retrieve list of borrowed toys
	 * 
	 * @param $adrnr 
	 *
	 * @return  mixed
	 */
	public static function getToys($adrnr)
	{
		$model = new LupoModelLupoClient();
		$toylist = $model->getClientToys($adrnr);
		
		return $toylist;
	}
}
