<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later *
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register( 'JToolBar', JPATH_PLATFORM . '/cms/toolbar/toolbar.php' );

class DSCForkMenu
{
	public $_name = array( );
	public $_menu;

	function __construct( $name = 'submenu' )
	{
		$this->_option = JFactory::getApplication( )->input->getCmd( 'option' );
		$this->_name = $name;

		$this->_menu = JToolBar::getInstance( $name );

		// Try to load initial values for the menu with a config file
		$initialpath = 'media/' . $this->_option . '/menus';

		$admin = JFactory::getApplication( )->isAdmin( );
		if ( $admin )
		{
			$path = '../' . $initialpath . '/admin';
		} else
		{
			$path = $initialpath . '/site';
		}

		$xmlfile = $path . '/' . "$name.xml";

		// Does the file exist?
		if ( file_exists( $xmlfile ) )
		{

			// the NULL and TRUE, make it so we can load a file
			$xml = new SimpleXMLElement( $xmlfile, NULL, TRUE );

			// Parse the file
			if ( $xml )
			{
				$items = array( );
				foreach ( $xml->children() as $child )
				{
					$name = $url = NULL;

					// If we have both a URL and name, add a new link
					if ( !empty( $child->name ) && !empty( $child->url ) )
					{
						$object = new JObject( );
						$object->name = JText::_( $child->name );
						$object->url = ($admin) ? $child->url : JRoute::_( $child->url );
						$object->url_raw = $child->url;
						$object->active = false;
						$items[] = $object;
					}
				}

				// find an exact URL match
				$uri = JURI::getInstance( );
				$uri_string = "index.php" . $uri->toString( array( 'query' ) );
				$exact_match = false;
				foreach ( $items as $item )
				{
					if ( $item->url_raw == $uri_string )
					{
						$exact_match = $item->url_raw;
					}
				}

				$view = JFactory::getApplication( )->input->getCmd( 'view' );

				// if no exact match, then match on view
				foreach ( $items as $item )
				{
					parse_str( $item->url_raw, $urlvars );
					$active = (strtolower( $view ) == strtolower( $urlvars['view'] ));
					if ( $exact_match == $item->url_raw || (empty( $exact_match ) && $active) )
					{
						$item->active = true;
					}
					$this->_menu->appendButton( $item->name, $item->url, $item->active );
				}

			}
		}
	}

	/**
	 *
	 * @param string $name
	 * @return mixed
	 *
	 * Returns a reference to a DSCForkMenu object or false if submenus have been disabled by an admin
	 */
	public static function getInstance( $name = 'submenu' )
	{
		static $instances;

		if ( !isset( $instances ) )
		{
			$instances = array( );
		}

		if ( empty( $instances[$name] ) )
		{
			$instances[$name] = new DSCForkMenu( $name );
		}

		return $instances[$name];
	}

	/**
	 *
	 * @param $name
	 * @param $link
	 * @param $active
	 * @return unknown_type
	 */
	function addEntry( $title, $link = '', $active = false )
	{
		$this->_menu->appendButton( $title, $link, $active );
	}

	/**
	 * Displays the menu according to view.
	 *
	 * @return unknown_type
	 */
	public function display( $layout = 'submenu', $hidemainmenu = '', $type = '' )
	{
		jimport( 'joomla.application.component.view' );

		// TODO This should be passed as an argument
		$hide = JFactory::getApplication( )->input->getInt( 'hidemainmenu' );

		$items = $this->_menu->getItems( );
		$name = $this->_name;

		// Load the named template, if there are links to display.
		if ( !empty( $items ) )
		{
			$base = JFactory::getApplication( )->isAdmin( ) ? JPATH_ADMINISTRATOR : JPATH_SITE;
			$app = DSCFork::getApp( );
			$template = JFactory::getApplication( )->getTemplate( );

			$lib_path = JPATH_SITE . '/libraries/dscfork/component/view/dashboard';
			$com_template_path = $base . '/components/com_' . $app->getName( ) . '/views/dashboard/tmpl';
			$template_path = $base . '/templates/' . $template . '/html/com_' . $app->getName( ) . '/dashboard';

			$view = new DSCForkView( array( 'name' => 'dashboard', 'template_path' => $lib_path ) );

			$view->set( 'items', $items );
			$view->set( 'name', $name );
			$view->set( 'layout', $layout );
			$view->set( 'hide', $hide );
			$view->setLayout( $layout );
			$view->addTemplatePath( $com_template_path );
			$view->addTemplatePath( $template_path );
			$view->display( );

		}
	}

	public static function treerecurse( $id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1, $pre = null, $spacer = null )
	{
		if ( @$children[$id] && $level <= $maxlevel )
		{
			foreach ( $children[$id] as $v )
			{
				$id = $v->id;

				if ( $type == 1 )
				{
					if ( is_null( $pre ) )
					{
						$pre = '<sup>|_</sup>&#160;';
					}
					if ( is_null( $spacer ) )
					{
						$spacer = '.&#160;&#160;&#160;&#160;&#160;&#160;';
					}
				} else
				{
					if ( is_null( $pre ) )
					{
						$pre = '- ';
					}
					if ( is_null( $spacer ) )
					{
						$spacer = '&#160;&#160;';
					}
				}

				if ( $v->parent_id == 0 )
				{
					$txt = $v->title;
				} else
				{
					$txt = $pre . $v->title;
				}
				$pt = $v->parent_id;
				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count( @$children[$id] );
				$list = DSCForkMenu::treerecurse( $id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type, $pre, $spacer );
			}
		}
		return $list;
	}

}
