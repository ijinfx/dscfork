<?php defined( '_JEXEC' ) or die( 'Restricted access' );
// The following two lines must be defined in the component install.php file prior to including this file
//$thisextension = strtolower( "com_whatever" );
//$thisextensionname = substr ( $thisextension, 4 );

JLoader::import( 'dscfork.library.installer', JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' );
$dscforkinstaller = new dscforkInstaller();
$dscforkinstaller->thisextension = $thisextension;
$dscforkinstaller->manifest = $this->manifest;
$dscforkinstaller->runInstallSQL();
$dscforkinstaller->fixAdminMenu( $thisextension );

//TODO: LOAD DSCFORK LANGUAGE?
// load the component language file
$language = JFactory::getLanguage();
$language->load( $thisextension );

$status = new JObject();
$status->modules = array();
$status->plugins = array();
$status->templates = array();
$status->libraries = array();

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
* // LIBRARIES INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
//$libraries = $dscforkinstaller->getElementByPath('libraries'); // TODO This isn't ready yet.  Finish this!  :-)  refs #16
$libraries = array();
if ( (is_a($libraries, 'JSimpleXMLElement') || is_a( $libraries, 'JXMLElement')) && !empty( $libraries ) && count($libraries->children())) {

    foreach ($libraries->children() as $library)
    {
        $name		= $dscforkinstaller->getAttribute('library', $library);
        $publish	= $dscforkinstaller->getAttribute('publish', $library);
        $client	    = JApplicationHelper::getClientInfo($dscforkinstaller->getAttribute('client', $library), true);

        // Set the installation path
        if (!empty ($name)) {
            $this->parent->setPath('extension_root', $client->path.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$name);
        } else {
            $this->parent->abort(JText::_('LIB_DSCFORK_LIBRARY').' '.JText::_('LIB_DSCFORK_INSTALL').': '.JText::_('LIB_DSCFORK_INSTALL_LIBRARY_FILE_MISSING'));
            return false;
        }

        /*
         * fire the dscforkInstaller with the foldername and folder entryType
        */
        $pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$name;
        $dscforkInstaller = new dscforkInstaller();
        if (!empty($publish) && $publish == "true") {
            $dscforkInstaller->set( '_publishExtension', true );
        }
        $result = $dscforkInstaller->installExtension($pathToFolder, 'folder');

        // track the message and status of installation from dscforkInstaller
        if ($result)
        {
            $alt = JText::_( "Installed" );
            $status = "<img src='" . DSCFork::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        } else {
            $alt = JText::_( "Failed" );
            $error = $dscforkInstaller->getError();
            $status = "<img src='" . DSCFork::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
            $status .= " - ".$error;
        }

        $status->libraries[] = array('name'=>$name,'client'=>$client->name, 'status'=>$status );
    }
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * // TEMPLATES INSTALLATION SECTION 
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$templates = $dscforkinstaller->getElementByPath('templates');
if ( (is_a($templates, 'JSimpleXMLElement') || is_a( $templates, 'JXMLElement')) && !empty( $templates ) && count($templates->children())) {

	foreach ($templates->children() as $template)
	{
		$mname		= $dscforkinstaller->getAttribute('template', $template);
		$mpublish	= $dscforkinstaller->getAttribute('publish', $template);
		$mclient	= JApplicationHelper::getClientInfo($dscforkinstaller->getAttribute('client', $template), true);
		
		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$mname);
		} else {
			$this->parent->abort(JText::_('LIB_DSCFORK_TEMPLATE').' '.JText::_('LIB_DSCFORK_INSTALL').': '.JText::_('LIB_DSCFORK_INSTALL_TEMPLATE_FILE_MISSING'));
			return false;
		}
		
		/*
		 * fire the dscforkInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$mname;
		$dscforkInstaller = new dscforkInstaller();
		if (!empty($mpublish) && $mpublish == "true") {
			$dscforkInstaller->set( '_publishExtension', true );
		}
		$result = $dscforkInstaller->installExtension($pathToFolder, 'folder');
		
		// track the message and status of installation from dscforkInstaller
		if ($result) 
		{
			$alt = JText::_( "LIB_DSCFORK_INSTALLED" );
			$mstatus = "<img src='" . DSCFork::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
		} else {
			$alt = JText::_( "LIB_DSCFORK_FAILED" );
			$error = $dscforkInstaller->getError();
			$mstatus = "<img src='" . DSCFork::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
			$mstatus .= " - ".$error;
		}
		
		$status->templates[] = array('name'=>$mname,'client'=>$mclient->name, 'status'=>$mstatus );
	}
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * MODULE INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$modules = $dscforkinstaller->getElementByPath('modules');
if ( (is_a($modules, 'JSimpleXMLElement') || is_a( $modules, 'JXMLElement')) && !empty( $modules ) && count($modules->children())) {

	foreach ($modules->children() as $module)
	{
		$mname		= $dscforkinstaller->getAttribute('module', $module);
		$mpublish	= $dscforkinstaller->getAttribute('publish', $module);
		$mposition	= $dscforkinstaller->getAttribute('position', $module);
		$mclient	= JApplicationHelper::getClientInfo($dscforkinstaller->getAttribute('client', $module), true);
		
		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$mname);
		} else {
			$this->parent->abort(JText::_('LIB_DSCFORK_MODULE').' '.JText::_('LIB_DSCFORK_INSTALL').': '.JText::_('LIB_DSCFORK_INSTALL_MODULE_FILE_MISSING'));
			return false;
		}
		
		/*
		 * fire the dscforkiInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$mname;
		$dscforkinstaller = new dscforkInstaller();
		if (!empty($mpublish) && $mpublish == 'true') {
			$dscforkinstaller->set( '_publishExtension', true );
		}
		$result = $dscforkinstaller->installExtension($pathToFolder, 'folder', $mname);
//		$mname		= $dscforkinstaller->getModuleName( $mname );
		
		// track the message and status of installation from dscforkInstaller
		if ($result) 
		{
			// set the position of the module if it is a new install and if position value exists in manifest
			if (!empty($mposition))
			{
				$db = JFactory::getDBO();
                $q = "UPDATE #__modules SET `position` = '{$mposition}' WHERE `module` = '{$result['element']}' AND `position` = '';";
                $db->setQuery($q);
				$db->query();
			}

			$alt = JText::_( "LIB_DSCFORK_INSTALLED" );
			$mstatus = "<img src='" . DSCFork::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
		} else {
			$alt = JText::_( "LIB_DSCFORK_FAILED" );
			$error = $dscforkinstaller->getError();
			$mstatus = "<img src='" . DSCFork::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
			$mstatus .= " - ".$error;
		}
		
		$status->modules[] = array('name'=>$mname,'client'=>$mclient->name, 'status'=>$mstatus );
	}
}


/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * PLUGIN INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$plugins = $dscforkinstaller->getElementByPath('plugins');
if ( (is_a($plugins, 'JSimpleXMLElement') || is_a( $plugins, 'JXMLElement')) && !empty( $plugins ) && count($plugins->children())) {

	foreach ($plugins->children() as $plugin)
	{
		$pname		= $dscforkinstaller->getAttribute('plugin', $plugin);
		$ppublish	= $dscforkinstaller->getAttribute('publish', $plugin);
		$pgroup		= $dscforkinstaller->getAttribute('group', $plugin);
		$name		= $dscforkinstaller->getAttribute('element', $plugin);
		
		// Set the installation path
		if (!empty($pname) && !empty($pgroup)) {
			$this->parent->setPath('extension_root', JPATH_ROOT.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$pgroup);
		} else {
			$this->parent->abort(JText::_('LIB_DSCFORK_PLUGIN').' '.JText::_('LIB_DSCFORK_INSTALL').': '.JText::_('LIB_DSCFORK_INSTALL_PLUGIN_FILE_MISSING'));
			return false;
		}
		
		/*
		 * fire the dscforkiInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$pname;
		$dscforkinstaller = new dscforkInstaller();
		if (!empty($ppublish) && $ppublish == 'true') {
			$dscforkinstaller->set( '_publishExtension', true );
		}
		$result = $dscforkinstaller->installExtension($pathToFolder, 'folder', $name);
		
		// track the message and status of installation from dscforkInstaller
		if ($result) {
			$alt = JText::_( "LIB_DSCFORK_INSTALLED" );
			$pstatus = "<img src='" . DSCFork::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";	
		} else {
			$alt = JText::_( "LIB_DSCFORK_FAILED" );
			$error = $dscforkinstaller->getError();
			$pstatus = "<img src='" . DSCFork::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' /> ";
			$pstatus .= " - ".$error;	
		}

		$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'status'=>$pstatus);
	}
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * SETUP DEFAULTS
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

// None

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$rows = 0;
?>

<h2><?php echo JText::_('LIB_DSCFORK_INSTALLATION_RESULTS'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th colspan="2"><?php echo JText::_('LIB_DSCFORK_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('LIB_DSCFORK_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo JText::_( $thisextension ); ?></td>
			<td class="key"><center><?php $alt = JText::_('LIB_DSCFORK_INSTALLED'); echo "<img src='" . DSCFork::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />"; ?></center></td>
		</tr>
<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('LIB_DSCFORK_MODULE'); ?></th>
			<th><?php echo JText::_('LIB_DSCFORK_CLIENT'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td class="key"><center><?php echo $module['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('LIB_DSCFORK_PLUGIN'); ?></th>
			<th><?php echo JText::_('LIB_DSCFORK_GROUP'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $plugin['name']; ?></td>
			<td class="key"><?php echo $plugin['group']; ?></td>
			<td class="key"><center><?php echo $plugin['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->templates)) : ?>
		<tr>
			<th><?php echo JText::_('LIB_DSCFORK_TEMPLATE'); ?></th>
			<th><?php echo JText::_('LIB_DSCFORK_CLIENT'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->templates as $template) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $template['name']; ?></td>
			<td class="key"><?php echo $template['client']; ?></td>
			<td class="key"><center><?php echo $template['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>
