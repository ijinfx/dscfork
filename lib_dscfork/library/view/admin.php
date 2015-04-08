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

/*Use this file to include admin only specific code*/

class DSCForkViewAdmin extends DSCForkView
{
	/**
	 * Displays a layout file
	 *
	 * @param unknown_type $tpl
	 * @return unknown_type
	 */
	public function display( $tpl = null )
	{
		//JHTML::_( 'stylesheet', 'admin.css', 'media/' . $this->_option . '/css/' );

		$this->getLayoutVars( $tpl );

		$this->displayTitle( $this->get( 'title' ) );
		
		$hidemainmenu = JFactory::getApplication()->input->getInt('hidemainmenu');

		if ( !$hidemainmenu && empty( $this->hidemenu ) )
		{
			DSCForkMenu::getInstance( )->display( );
		}

		jimport( 'joomla.application.module.helper' );
		$modules = JModuleHelper::getModules( $this->_name . "_left" );
		if ( $modules && !$hidemainmenu && empty( $this->hidemenu ) || !empty( $this->leftMenu ) && empty( $this->hidemenu ) )
		{
			$this->displayWithLeftMenu( $tpl = null, $this->leftMenu );

		} else
		{
			parent::display( $tpl );
		}
	}

	/**
	 * Displays text as the title of the page
	 *
	 * @param $text
	 * @return unknown_type
	 */
	public function displayTitle( $text = '' )
	{
		$layout = $this->getLayout( );
		switch(strtolower($layout))
		{
			case "footer":
				break;
			case "default":
			default:
				$app = DSCFork::getApp( );

				$title = $text ? JText::_( $text ) : JText::_( ucfirst( JFactory::getApplication( )->input->getCmd( 'view' ) ) );
				JToolBarHelper::title( $title, $app->getName( ) );
				break;
		}
	}

	/**
	 * Displays a layout file with room for a left menu bar
	 * @param $tpl
	 * @return unknown_type
	 */
	public function displayWithLeftMenu( $tpl = null, $menuname )
	{
		// TODO This is an ugly, quick hack - fix it
		// TODO: Check Ad Agency for inspiration
		echo "<table width='100%'>";
		echo "<tr>";
		echo "<td style='width: 180px; padding-right: 5px; vertical-align: top;' >";

		DSCFork::load( 'DSCForkMenu', 'library.menu' );
		if ( $menu = DSCForkMenu::getInstance( $menuname ) )
		{
			$menu->display( 'leftmenu' );
		}

		$modules = JModuleHelper::getModules( $this->_name . "_left" );
		$document = JFactory::getDocument( );
		$renderer = $document->loadRenderer( 'module' );
		$attribs = array( );
		$attribs['style'] = 'xhtml';
		foreach ( @$modules as $mod )
		{
			echo $renderer->render( $mod, $attribs );
		}

		echo "</td>";
		echo "<td style='vertical-align: top;' >";
		parent::display( $tpl );
		echo "</td>";
		echo "</tr>";
		echo "</table>";
	}

	/**
	 * Basic commands for displaying a list
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function _default( $tpl = '' )
	{
		if ( empty( $this->hidemenu ) )
		{
			// add toolbar buttons
			$this->_defaultToolbar( );
		}

		parent::_default( $tpl );
	}

	/**
	 * Basic methods for displaying an item from a list
	 * @param $tpl
	 * @return unknown_type
	 */
	function _form( $tpl = '' )
	{
		if ( empty( $this->hidemenu ) )
		{
			$model = $this->getModel( );

			// get the data
			$table = $model->getTable( );
			$table->load( (int)$model->getId( ) );

			// set toolbar
			$layout = $this->getLayout( );
			$isNew = ($table->id < 1);
			$view = JFactory::getApplication( )->input->getCmd( 'view' );
			$view = ucwords( strtolower( $view ) );
			switch(strtolower($layout))
			{
				case "view":
					$title = JText::sprintf( 'LIB_DSCFORK_TITLE_VIEW', $view );
					$this->set( "title", $title );
					$this->_viewToolbar( $isNew );
					break;
				case "form":
				default:
					$title = JText::sprintf( 'LIB_DSCFORK_TITLE_EDIT', $view );
					$this->set( "title", $title );
					$this->_formToolbar( $isNew );
					break;
			}
		}

		parent::_form( $tpl );
	}

	/**
	 * The default toolbar for a list
	 * @return unknown_type
	 */
	function _defaultToolbar( )
	{
		JToolBarHelper::editList( );
		JToolBarHelper::deleteList( JText::_( 'LIB_DSCFORK_VALID_DELETE_ITEMS' ) );
		JToolBarHelper::addnew( );
	}

	/**
	 * The default toolbar for editing an item
	 * @param $isNew
	 * @return unknown_type
	 */
	function _formToolbar( $isNew = null )
	{
		$divider = false;
		$surrounding = (!empty( $this->surrounding )) ? $this->surrounding : array( );
		if ( !empty( $surrounding['prev'] ) )
		{
			$divider = true;
			JToolBarHelper::custom( 'saveprev', "previous", "previous", JText::_( 'LIB_DSCFORK_SAVE_AND_PREV' ), false );
		}
		if ( !empty( $surrounding['next'] ) )
		{
			$divider = true;
			JToolBarHelper::custom( 'savenext', "next", "next", JText::_( 'LIB_DSCFORK_SAVE_AND_NEXT' ), false );
		}
		if ( $divider )
		{
			JToolBarHelper::divider( );
		}

		JToolBarHelper::custom( 'savenew', "save-new", "save-new", JText::_( 'LIB_DSCFORK_SAVE_AND_NEW' ), false );
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply( 'apply' );

		if ( $isNew )
		{
			JToolBarHelper::cancel( );
		} else
		{
			JToolBarHelper::cancel( 'close', JText::_( 'LIB_DSCFORK_CLOSE' ) );
		}
	}

	/**
	 * The default toolbar for viewing an item
	 * @param $isNew
	 * @return unknown_type
	 */
	function _viewToolbar( $isNew = null )
	{
		$divider = false;
		$surrounding = (!empty( $this->surrounding )) ? $this->surrounding : array( );
		if ( !empty( $surrounding['prev'] ) )
		{
			$divider = true;
			JToolBarHelper::custom( 'prev', "prev", "prev", JText::_( 'LIB_DSCFORK_PREV' ), false );
		}
		if ( !empty( $surrounding['next'] ) )
		{
			$divider = true;
			JToolBarHelper::custom( 'next', "next", "next", JText::_( 'LIB_DSCFORK_NEXT' ), false );
		}
		if ( $divider )
		{
			JToolBarHelper::divider( );
		}

		JToolBarHelper::cancel( 'close', JText::_( 'LIB_DSCFORK_CLOSE' ) );
	}

}
