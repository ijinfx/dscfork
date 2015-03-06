<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dscfork/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage library/template
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later *
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class DSCForkTemplateBase
{

	public $name = 'template';
	public $layout;

	// access to the helper classes
	public $API;
	public $bootstrap;
	public $less;

	public $page_suffix;
	public $pageclass = '';
	public $doc;
	public $footerScripts = array( );

	function __construct( $template )
	{
		$this->API = new DSCForkTemplateAPI( $template );
		$this->name = $this->API->templateName( );
		$this->bootstrap = new DSCForkTemplateBootstrap( $this );
		$this->layout = $this->API->get( 'layout', 'default.php' );
		$this->pageclass = $this->API->get( 'pageclass', '' );
	}

	function prepareDoc( )
	{
		$this->doc = JFactory::getDocument( );
		$this->doc->setGenerator( $this->API->get( 'generator', 'KlaraOnTheWeb' ) );
		$this->API->addFavicon( );
	}

	function returnLayout( )
	{
		if ( JFile::exists( $this->API->getTemplateLayoutPath( $this->layout ) ) )
		{
			return $this->API->getTemplateLayoutPath( $this->layout );
		}
	}

	function prepareFooterScripts( )
	{

	}

}
