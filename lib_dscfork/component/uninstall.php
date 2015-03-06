<?php defined( '_JEXEC' ) or die( 'Restricted access' );
// The following two lines must be defined in the component install.php file prior to including this file
//$thisextension = strtolower( "com_whatever" );
//$thisextensionname = substr ( $thisextension, 4 );

JLoader::import( 'dscfork.library.installer', JPATH_SITE . '/libraries' );
$dscforkinstaller = new dscforkInstaller();
$dscforkinstaller->thisextension = $thisextension;
$dscforkinstaller->manifest = !empty($this->manifest) ? $this->manifest : $dscforkinstaller->getComponentManifestFile($thisextension);

//TODO: LOAD DSCFORK LANGUAGE?
// load the component language file
$language = JFactory::getLanguage();
$language->load( $thisextension );

$status = new JObject();
$status->modules = array();
$status->plugins = array();
$status->templates = array();

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* // TEMPLATES UNINSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$templates = $dscforkinstaller->getElementByPath('templates');
if ( (is_a($templates, 'JSimpleXMLElement') || is_a( $templates, 'JXMLElement')) && !empty( $templates ) && count($templates->children())) {

    foreach ($templates->children() as $template)
    {
        $mname		= $dscforkinstaller->getAttribute('template', $template);
        $mpublish	= $dscforkinstaller->getAttribute('publish', $template);
        $mclient	= JApplicationHelper::getClientInfo($dscforkinstaller->getAttribute('client', $template), true);

        $package    = array();
        $package['type'] = 'template';
        $package['group'] = '';
        $package['element'] = $mname;
        $package['client'] = $dscforkinstaller->getAttribute('client', $template);
        
        /*
         * fire the dscforkInstaller with the foldername and folder entryType
        */
        $dscforkInstaller = new dscforkInstaller();
        $result = $dscforkInstaller->uninstallExtension($pathToFolder, 'folder');

        // track the message and status of installation from dscforkInstaller
        if ($result)
        {
            $alt = JText::_( "LIB_DSCFORK_UNINSTALLED" );
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
 * MODULE UNINSTALLATION SECTION
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
                
        $package    = array();
        $package['type'] = 'module';
        $package['group'] = '';
        $package['element'] = str_replace('modules/', '', $mname);
        $package['client'] = $dscforkinstaller->getAttribute('client', $module);
                
        /*
         * fire the dscforkInstaller
         */
        $dscforkInstaller = new dscforkInstaller();
        $result = $dscforkInstaller->uninstallExtension($package);
        
        // track the message and status of installation from dscforkInstaller
        if ($result) 
        {
            $alt = JText::_( "LIB_DSCFORK_UNINSTALLED" );
            $mstatus = "<img src='" . DSCFork::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        } 
            else 
        {
            $alt = JText::_( "LIB_DSCFORK_FAILED" );
            $error = $dscforkInstaller->getError();
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

        $package    = array();
        $package['type'] = 'plugin';
        $package['group'] = $pgroup;
        $package['element'] = $name;
        $package['client'] = '';
        
        /*
         * fire the dscforkInstaller
         */
        $dscforkInstaller = new dscforkInstaller();
        $result = $dscforkInstaller->uninstallExtension($package);
        
        // track the message and status of installation from dscforkInstaller
        if ($result) 
        {
            $alt = JText::_( "LIB_DSCFORK_UNINSTALLED" );
            $pstatus = "<img src='" . DSCFork::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />"; 
        } 
            else 
        {
            $alt = JText::_( "LIB_DSCFORK_FAILED" );
            $error = $dscforkInstaller->getError();
            $pstatus = "<img src='" . DSCFork::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' /> ";
            $pstatus .= " - ".$error;   
        }

        $status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'status'=>$pstatus);
    }
}


/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
 $rows = 0;
?>

<h2><?php echo JText::_('LIB_DSCFORK_UNINSTALLATION_RESULTS'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('LIB_DSCFORK_EXTENSION'); ?></th>
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
			<td class="key" colspan="2"><?php echo JText::_('LIB_DSCFORK_COMPONENT'); ?></td>
			<td><center><strong><?php echo JText::_('LIB_DSCFORK_REMOVED'); ?></strong></center></td>
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
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td class="key"><center><?php echo $plugin['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>