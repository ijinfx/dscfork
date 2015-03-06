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

class DSCForkSocial extends JObject
{
	/**
	 * Returns a reference to the a Helper object, only creating it if it doesn't already exist
	 *
	 * @param type 		$type 	 The helper type to instantiate
	 * @param string 	$prefix	 A prefix for the helper class name. Optional.
	 * @return helper The Helper Object
	 */
	public static function getInstance( $type = '', $prefix = 'DSCForkSocial' )
	{

		static $instances;

		if ( !isset( $instances ) )
		{
			$instances = array( );
		}

		$type = preg_replace( '/[^A-Z0-9_\.-]/i', '', $type );

		$class = $prefix . ucfirst( $type );

		if ( empty( $instances[$class] ) )
		{

			if ( !class_exists( $class ) )
			{
				$path = JPATH_SITE . '/libraries/dscfork/library/social/' . strtolower( $type ) . '.php';

				JLoader::register( $class, $path );

				if ( !class_exists( $class ) )
				{
					JError::raiseWarning( 0, 'Social class ' . $class . ' not found.' );
					return false;
				}

			}

			$instance = new $class( );

			$instances[$class] = &$instance;
		}

		return $instances[$class];
	}

	public static function makeShortUrl( $url )
	{
		return $url;
	}

	//TODO: implement like button on child classes
	public function likebutton( )
	{

	}

}
