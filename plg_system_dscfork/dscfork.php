<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	plg_system_dscfork
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemDscFork extends JPlugin
{
	function onAfterInitialise( )
	{
		// Import Joomla! classes
		jimport( 'joomla.application.component.controller' );
		jimport( 'joomla.application.component.model' );
		jimport( 'joomla.application.component.view' );

		//TODO: Add the compatibilty (ie.DSCForkTableBase) in the future release of joomla 4?
		// Load the Base classes
		JLoader::register( 'DSCForkTable', JPATH_SITE . '/libraries/dscfork/library/table.php' );
		JLoader::register( 'DSCForkController', JPATH_SITE . '/libraries/dscfork/library/controller.php' );
		JLoader::register( 'DSCForkModel', JPATH_SITE . '/libraries/dscfork/library/model.php' );
		JLoader::register( 'DSCForkView', JPATH_SITE . '/libraries/dscfork/library/view.php' );

		if( !class_exists( 'DSCFork' ) )
		{
			if( !JFile::exists( JPATH_SITE . '/libraries/dscfork/dscfork.php' ) )
			{
				return false;
			}
			require_once JPATH_SITE . '/libraries/dscfork/dscfork.php';
		}

		$language = JFactory::getLanguage();
        $language -> load('lib_dscfork', JPATH_ROOT, '', true);
		
		return DSCFork::loadLibrary( );
	}

}
