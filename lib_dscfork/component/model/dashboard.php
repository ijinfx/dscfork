<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	component/model
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Sample::load( 'SampleModelBase', 'models._base' );

class SampleModelDashboard extends SampleModelBase
{
	function getTable( )
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATORR . 'com_sample' . DIRECTORY_SEPARATOR . 'tables' );
		$table = JTable::getInstance( 'Config', 'SampleTable' );
		return $table;
	}

}
