<?php
// $Id: common.php,v 1.38 2003/09/28 01:06:44 okazu Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

if (!defined("XOOPS_MAINFILE_INCLUDED")) {
	exit();
} else {
	// ############## Activate error handler ##############
	include_once XOOPS_ROOT_PATH . '/class/errorhandler.php';
	$xoopsErrorHandler =& XoopsErrorHandler::getInstance();
	// Turn on error handler by default (until config value obtained from DB)
	$xoopsErrorHandler->activate(true);

	define("XOOPS_SIDEBLOCK_LEFT",0);
	define("XOOPS_SIDEBLOCK_RIGHT",1);
	define("XOOPS_SIDEBLOCK_BOTH",2);
	define("XOOPS_CENTERBLOCK_LEFT",3);
	define("XOOPS_CENTERBLOCK_RIGHT",4);
	define("XOOPS_CENTERBLOCK_CENTER",5);
	define("XOOPS_CENTERBLOCK_ALL",6);
	define("XOOPS_BLOCK_INVISIBLE",0);
	define("XOOPS_BLOCK_VISIBLE",1);
	define("XOOPS_MATCH_START",0);
	define("XOOPS_MATCH_END",1);
	define("XOOPS_MATCH_EQUAL",2);
	define("XOOPS_MATCH_CONTAIN",3);
	define("SMARTY_DIR", XOOPS_ROOT_PATH."/class/smarty/");
	define("XOOPS_CACHE_PATH", XOOPS_ROOT_PATH."/cache");
	define("XOOPS_UPLOAD_PATH", XOOPS_ROOT_PATH."/uploads");
	define("XOOPS_THEME_PATH", XOOPS_ROOT_PATH."/themes");
	define("XOOPS_COMPILE_PATH", XOOPS_ROOT_PATH."/templates_c");
	define("XOOPS_THEME_URL", XOOPS_URL."/themes");
	define("XOOPS_UPLOAD_URL", XOOPS_URL."/uploads");
	include_once XOOPS_ROOT_PATH.'/class/logger.php';
	$xoopsLogger =& XoopsLogger::instance();
	$xoopsLogger->startTime();
	if (!defined('XOOPS_XMLRPC')) {
		define('XOOPS_DB_CHKREF', 1);
	} else {
		define('XOOPS_DB_CHKREF', 0);
	}

	// ############## Include common functions file ##############
	include_once XOOPS_ROOT_PATH.'/include/functions.php';

    // #################### Connect to DB ##################
	require_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';
	if ($_SERVER['REQUEST_METHOD'] != 'POST' || !xoops_refcheck(XOOPS_DB_CHKREF)) {
		define('XOOPS_DB_PROXY', 1);
	}
	$xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();

	// ################# Include required files ##############
	require_once XOOPS_ROOT_PATH.'/kernel/object.php';
	require_once XOOPS_ROOT_PATH.'/kernel/handlerregistry.php';
	require_once XOOPS_ROOT_PATH.'/class/criteria.php';

	// #################### Include text sanitizer ##################
	include_once XOOPS_ROOT_PATH."/class/module.textsanitizer.php";

	// ################# Load Config Settings ##############
	$config_handler =& xoops_gethandler('config');
	$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);

	// #################### Error reporting settings ##################
	error_reporting(0);

	if ($xoopsConfig['debug_mode'] == 1) {
		error_reporting(E_ALL);
	} else {
		// Turn off error handler
		$xoopsErrorHandler->activate(false);
	}

	if ($xoopsConfig['enable_badips'] == 1 && isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != '') {
		foreach ($xoopsConfig['bad_ips'] as $bi) {
			if (!empty($bi) && preg_match("/".$bi."/", $_SERVER['REMOTE_ADDR'])) {
				exit();
			}
		}
	}
	unset($bi);
	unset($bad_ips);
	unset($xoopsConfig['badips']);

	// ################# Include version info file ##############
	include_once XOOPS_ROOT_PATH."/include/version.php";

	// for older versions...will be DEPRECATED!
	$xoopsConfig['xoops_url'] = XOOPS_URL;
	$xoopsConfig['root_path'] = XOOPS_ROOT_PATH."/";


	// #################### Include site-wide lang file ##################
	if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php") ) {
		include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php";
	} else {
		include_once XOOPS_ROOT_PATH."/language/english/global.php";
	}

	// ################ Include page-specific lang file ################
	if ( isset($xoopsOption['pagetype']) ) {
		if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/".$xoopsOption['pagetype'].".php") ) {
			include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/".$xoopsOption['pagetype'].".php";
		} else {
			include_once XOOPS_ROOT_PATH."/language/english/".$xoopsOption['pagetype'].".php";
		}
	}

	if ( !defined("XOOPS_USE_MULTIBYTES") ) {
		define("XOOPS_USE_MULTIBYTES",0);
	}

	// ############## Login a user with a valid session ##############
	$xoopsUser = '';
	$xoopsUserIsAdmin = false;
	$member_handler = xoops_gethandler('member');
	$sess_handler = xoops_gethandler('session');
	if ($xoopsConfig['use_ssl'] && isset($_POST[$xoopsConfig['sslpost_name']]) && $_POST[$xoopsConfig['sslpost_name']] != '') {
		session_id($_POST[$xoopsConfig['sslpost_name']]);
	} elseif ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '') {
		if (isset($HTTP_COOKIE_VARS[$xoopsConfig['session_name']])) {
			session_id($HTTP_COOKIE_VARS[$xoopsConfig['session_name']]);
		} else {
			// no custom session cookie set, destroy session if any
			$HTTP_SESSION_VARS = array();
			//session_destroy();
		}
		if (function_exists('session_cache_expire')) {
			session_cache_expire($xoopsConfig['session_expire']);
		}
	}
	session_set_save_handler(array(&$sess_handler, 'open'), array(&$sess_handler, 'close'), array(&$sess_handler, 'read'), array(&$sess_handler, 'write'), array(&$sess_handler, 'destroy'), array(&$sess_handler, 'gc'));
	session_start();

	if (!empty($HTTP_SESSION_VARS['xoopsUserId'])) {
		$xoopsUser =& $member_handler->getUser($HTTP_SESSION_VARS['xoopsUserId']);
		if (!is_object($xoopsUser)) {
			$xoopsUser = '';
			$HTTP_SESSION_VARS = array();
		} else {
			if ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '') {
				setcookie($xoopsConfig['session_name'], session_id(), time()+(60*$xoopsConfig['session_expire']), '/',  '', 0);
			}
			$xoopsUser->setGroups($HTTP_SESSION_VARS['xoopsUserGroups']);
			$xoopsUserIsAdmin = $xoopsUser->isAdmin();
		}
	}
	if ( isset( $_POST['xoops_theme_select'] ) && in_array( $_POST['xoops_theme_select'], $xoopsConfig['theme_set_allowed'] ) ) {
		$xoopsConfig['theme_set'] = $_POST['xoops_theme_select'];
		$_SESSION['xoopsUserTheme'] = $_POST['xoops_theme_select'];
	} elseif (isset($_SESSION['xoopsUserTheme']) && in_array($_SESSION['xoopsUserTheme'], $xoopsConfig['theme_set_allowed'])) {
		$xoopsConfig['theme_set'] = $_SESSION['xoopsUserTheme'];
	}

	if ($xoopsConfig['closesite'] == 1) {
		$allowed = false;
		if (is_object($xoopsUser)) {
			foreach ($xoopsUser->getGroups() as $group) {
				if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
					$allowed = true;
					break;
				}
			}
		} elseif (!empty($_POST['xoops_login'])) {
			include_once XOOPS_ROOT_PATH.'/include/checklogin.php';
			exit();
		}
		if (!$allowed) {
			include_once XOOPS_ROOT_PATH.'/class/template.php';
			$xoopsTpl = new XoopsTpl();
			$xoopsTpl->assign(array('sitename' => $xoopsConfig['sitename'], 'xoops_themecss' => xoops_getcss(), 'xoops_imageurl' => XOOPS_THEME_URL.'/'.$xoopsConfig['theme_set'].'/', 'lang_login' => _LOGIN, 'lang_username' => _USERNAME, 'lang_password' => _PASSWORD, 'lang_siteclosemsg' => $xoopsConfig['closesite_text']));
			$xoopsTpl->xoops_setCaching(1);
			$xoopsTpl->display('db:system_siteclosed.html');
			exit();
		}
		unset($allowed, $group);
	}

	$xoopsRequestUri = @xoops_getenv('REQUEST_URI');
	if (!$xoopsRequestUri) {
		$xoopsRequestUri = (!$sn = xoops_getenv('SCRIPT_NAME')) ? getenv('REQUEST_URI') : $sn;
	}
	if (file_exists('./xoops_version.php')) {
		$url_arr = explode('/', str_replace(str_replace('https://', 'http://', XOOPS_URL.'/modules/'), '', 'http://'.$_SERVER['HTTP_HOST'].$xoopsRequestUri));
		$module_handler = xoops_gethandler('module');
		$xoopsModule = $module_handler->getByDirname($url_arr[0]);
		unset($url_arr);
		if (!$xoopsModule || !$xoopsModule->getVar('isactive')) {
			include_once XOOPS_ROOT_PATH."/header.php";
			echo "<h4>"._MODULENOEXIST."</h4>";
			include_once XOOPS_ROOT_PATH."/footer.php";
			exit();
		} 
		$moduleperm_handler = xoops_gethandler('groupperm');
		if ($xoopsUser) {
			if (!$moduleperm_handler->checkRight('module_read', $xoopsModule->getVar('mid'), $xoopsUser->getGroups())) {
				redirect_header(XOOPS_URL."/user.php",1,_NOPERM);
				exit();
			}
		} else {
			if (!$moduleperm_handler->checkRight('module_read', $xoopsModule->getVar('mid'), XOOPS_GROUP_ANONYMOUS)) {
				redirect_header(XOOPS_URL."/user.php",1,_NOPERM);
				exit();
			}
		}
		if ( file_exists(XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar('dirname')."/language/".$xoopsConfig['language']."/main.php") ) {
			include_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar('dirname')."/language/".$xoopsConfig['language']."/main.php";
		} else {
			if ( file_exists(XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar('dirname')."/language/english/main.php") ) {
				include_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar('dirname')."/language/english/main.php";
			}
		}
		if ($xoopsModule->getVar('hasconfig') == 1 || $xoopsModule->getVar('hascomments') == 1 || $xoopsModule->getVar( 'hasnotification' ) == 1) {
			$xoopsModuleConfig =& $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
		}
	}
}
?>
</content>
<task_progress>
- [x] Analyze all PHP files for compatibility issues
- [x] Fix include/common.php (remove set_magic_quotes_runtime)
- [ ] Convert `var` declarations to `public`/`private`/`protected` in all core class files
- [ ] Replace `each()` with `foreach` loops in class/snoopy.php and class/mail/phpmailer/class.smtp.php
- [ ] Replace `strftime`/`gmstrftime` with modern alternatives
- [ ] Fix `get_class()` lowercase comparisons
- [ ] Fix dynamic properties and variable variables issues
- [ ] Fix module files (modules/)
- [ ] Verify all changes
</task_progress>
</execute_file>Now let me fix the core class files systematically. I'll fix all `var` declarations and other PHP 8.2+ issues.

<write_to_file>
<path>class/template.php</path>
<content><?php
// $Id: template.php,v 1.20 2003/07/08 12:38:08 okazu Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if (!defined('SMARTY_DIR')) {
	exit();
}
/**
 * Base class: Smarty template engine
 */
require_once SMARTY_DIR.'Smarty.class.php';

// PHP 8.2 compatibility: allow dynamic properties for Smarty
#[\AllowDynamicProperties]
class XoopsTpl extends Smarty
{

	/**
	 * Allow update of template files from the themes/ directory?
     * This should be set to false on an active site to increase performance
	 */
	public $_canUpdateFromFile = false;

	/**
	 * Constructor
	 **/
	function XoopsTpl()
	{
		global $xoopsConfig;
		$this->Smarty();
		$this->compile_id = null;
		if ($xoopsConfig['theme_fromfile'] == 1) {
			$this->_canUpdateFromFile = true;
			$this->compile_check = true;
		} else {
			$this->_canUpdateFromFile = false;
			$this->compile_check = false;
		}
		$this->left_delimiter =  '<{';
		$this->right_delimiter =  '}>';
		$this->template_dir = XOOPS_THEME_PATH;
		$this->cache_dir = XOOPS_CACHE_PATH;
		$this->compile_dir = XOOPS_COMPILE_PATH;
		$this->plugins_dir = array(XOOPS_ROOT_PATH.'/class/smarty/plugins');
		$this->default_template_handler_func = 'xoops_template_create';
		
		// Added by goghs on 11-26 to deal with safe mode
// Safe mode was removed in PHP 5.4
	$this->use_sub_dirs = false;
		//} else {
		//	$this->use_sub_dirs = true;
		//}
		// END

		$this->assign(array('xoops_url' => XOOPS_URL, 'xoops_rootpath' => XOOPS_ROOT_PATH, 'xoops_langcode' => _LANGCODE, 'xoops_charset' => _CHARSET, 'xoops_version' => XOOPS_VERSION, 'xoops_upload_url' => XOOPS_UPLOAD_URL));
	}

	/**
	 * Set the directory for templates
     * 
     * @param   string  $dirname    Directory path without a trailing slash
	 **/
	function xoops_setTemplateDir($dirname)
	{
		$this->template_dir = $dirname;
	}

	/**
	 * Get the active template directory
	 * 
	 * @return  string
	 **/
	function xoops_getTemplateDir()
	{
		return $this->template_dir;
	}

	/**
	 * Set debugging mode
	 * 
	 * @param   boolean     $flag
	 **/
	function xoops_setDebugging($flag=false)
	{
		$this->debugging = is_bool($flag) ? $flag : false;
	}

	/**
	 * Set caching
	 * 
	 * @param   integer     $num
	 **/
	function xoops_setCaching($num=0)
	{
		$this->caching = (int)$num;
	}

	/**
	 * Set cache lifetime
	 * 
	 * @param   integer     $num    Cache lifetime
	 **/
	function xoops_setCacheTime($num=0)
	{
		$num = (int)$num;
		if ($num <= 0) {
			$this->caching = 0;
		} else {
			$this->cache_lifetime = $num;
		}
	}

	/**
	 * Set directory for compiled template files
	 * 
	 * @param   string  $dirname    Full directory path without a trailing slash
	 **/
	function xoops_setCompileDir($dirname)
	{
		$this->compile_dir = $dirname;
	}

	/**
	 * Set the directory for cached template files
	 * 
	 * @param   string  $dirname    Full directory path without a trailing slash
	 **/
	function xoops_setCacheDir($dirname)
	{
		$this->cache_dir = $dirname;
	}

	/**
	 * Render output from template data
	 * 
	 * @param   string  $data
	 * @return  string  Rendered output  
	 **/
	function xoops_fetchFromData(&$data)
	{
		$dummyfile = XOOPS_CACHE_PATH.'/dummy_'.time();
		$fp = fopen($dummyfile, 'w');
		fwrite($fp, $data);
		fclose($fp);
		$fetched = $this->fetch('file:'.$dummyfile);
		unlink($dummyfile);
		$this->clear_compiled_tpl('file:'.$dummyfile);
		return $fetched;
	}

	/**
	 * 
	 **/
	function xoops_canUpdateFromFile()
	{
		return $this->_canUpdateFromFile;
	}
}

/**
 * Smarty default template handler function
 * 
 * @param $resource_type
 * @param $resource_name
 * @param $template_source
 * @param $template_timestamp
 * @param $smarty_obj
 * @return  bool
 **/
function xoops_template_create ($resource_type, $resource_name, &$template_source, &$template_timestamp, &$smarty_obj)
{
	if ( $resource_type == 'db' ) {
		$file_handler = xoops_gethandler('tplfile');
		$tpl = $file_handler->find('default', null, null, null, $resource_name, true);
		if (count($tpl) > 0 && is_object($tpl[0])) {
			$template_source = $tpl[0]->getSource();
			$template_timestamp = $tpl[0]->getLastModified();
			return true;
		}
	} else {
	}
	return false;
}

/**
 * function to update compiled template file in templates_c folder
 * 
 * @param   string  $tpl_id
 * @param   boolean $clear_old
 * @return  boolean
 **/
function xoops_template_touch($tpl_id, $clear_old = true)
{
	$tpl = new XoopsTpl();
	$tpl->force_compile = true;
	$tplfile_handler = xoops_gethandler('tplfile');
	$tplfile = $tplfile_handler->get($tpl_id);
	if ( is_object($tplfile) ) {
		$file = $tplfile->getVar('tpl_file');
		if ($clear_old) {
			$tpl->clear_cache('db:'.$file);
			$tpl->clear_compiled_tpl('db:'.$file);
		}
		$tpl->fetch('db:'.$file);
		return true;
	}
	return false;
}

/**
 * Clear the module cache
 * 
 * @param   int $mid    Module ID
 * @return 
 **/
function xoops_template_clear_module_cache($mid)
{
	$block_arr = XoopsBlock::getByModule($mid);
	$count = count($block_arr);
	if ($count > 0) {
		$xoopsTpl = new XoopsTpl();	
		$xoopsTpl->xoops_setCaching(2);
		for ($i = 0; $i < $count; $i++) {
			if ($block_arr[$i]->getVar('template') != '') {
				$xoopsTpl->clear_cache('db:'.$block_arr[$i]->getVar('template'));
			}
		}
	}
}
?>