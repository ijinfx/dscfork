<?php
/**
 *  com_sample
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	component
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

//check if we have the library installed
if( !class_exists( 'Sample' ) )
	exit( 'DSCFork Library required!' );

//require libray component herper
require_once JPATH_SITE . '/libraries/dscfork/library/helper/component.php';

//specific component css and javascript can be added here

