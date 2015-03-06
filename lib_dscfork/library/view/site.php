<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dscfork/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage library/view
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later *
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.filter.filteroutput' );
jimport( 'joomla.application.component.view' );

class DSCForkViewSite extends DSCForkView
{
	/**
	 * The valid task set by the controller
	 * @var str
	 */
	protected $_doTask;

	/**
	 * First displays the submenu, then displays the output
	 * but only if a valid _doTask is set in the view object
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	public function display( $tpl = null )
	{
		// display() will return null if 'doTask' is not set by the controller
		// This prevents unauthorized access by bypassing the controllers
		if ( empty( $this->_doTask ) )
		{
			return null;
		}

		$this->getLayoutVars( $tpl );

		$hidemainmenu = JFactory::getApplication( )->input->getInt( 'hidemainmenu' );
		if ( !$hidemainmenu && empty( $this->hidemenu ) )
		{
			$this->displaySubmenu( );
			//TODO: make it a sidemenu
		}

		$app = DSCFork::getApp( )->getnName( );

		$config = $app::getInstance( );
		if ( $config->get( 'include_site_css', '1' ) )
		{
			JHTML::_( 'stylesheet', 'site.css', 'media/' . $this->_option . '/css/' );
		}

		parent::display( $tpl );
	}

	/**
	 * Displays a submenu if there is one and if hidemainmenu is not set
	 *
	 * @param $selected
	 * @return unknown_type
	 */
	public function displaySubmenu( $selected = '' )
	{
		$input = JFactory::getApplication( )->input;
		if ( !$input->getInt( 'hidemainmenu' ) && empty( $this->hidemenu ) )
		{
			jimport( 'joomla.html.toolbar' );
			require_once (JPATH_ADMINISTRATOR . '/includes/toolbar.php');
			$view = strtolower( $input->getCmd( 'view' ) );

			$menu = DSCForkMenu::getInstance( );
		}
	}

}
