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

class DSCForkViewCSV extends DSCForkView
{

	protected $csvHeader = true;
	protected $csvFilename = null;

	function __construct( $config = array() )
	{
		parent::__construct( $config );

		if ( empty( $this->csvFilename ) )
		{
			$view = JFactory::getApplication( )->input->getCmd( 'view' );

			$this->csvFilename = strtolower( $view );
		}

	}

	/**
	 * Displays a layout file
	 *
	 * @param unknown_type $tpl
	 * @return unknown_type
	 */
	public function display( $tpl = null )
	{
		// Load the model
		$model = $this->getModel( );

		$items = $model->getList( );
		$this->assignRef( 'items', $items );

		$document = JFactory::getDocument( );
		$document->setMimeEncoding( 'text/csv' );

		$app = JFactory::getApplication( );

		$app->setHeader( 'Pragma', 'public' );
		$app->setHeader( 'Expires', '0' );
		$app->setHeader( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0' );
		$app->setHeader( 'Cache-Control', 'public', false );
		$app->setHeader( 'Content-Description', 'File Transfer' );
		$app->setHeader( 'Content-Disposition', 'attachment; filename=' . $this->csvFilename );

		JError::setErrorHandling( E_ALL, 'ignore' );
		if ( is_null( $tpl ) )
			$tpl = 'csv';
		$result = $this->loadTemplate( $tpl );
		JError::setErrorHandling( E_WARNING, 'callback' );

		if ( $result instanceof JException )
		{
			// Default CSV behaviour in case the template isn't there!
			if ( empty( $items ) )
				return;

			if ( $this->csvHeader )
			{
				$item = array_pop( $items );
				$keys = get_object_vars( $item );
				$items[] = $item;
				reset( $items );

				$csv = array( );
				foreach ( $keys as $k => $v )
				{
					$csv[] = '"' . str_replace( '"', '""', $k ) . '"';
				}
				echo implode( ",", $csv ) . "\r\n";
			}

			foreach ( $items as $item )
			{
				$csv = array( );
				$keys = get_object_vars( $item );
				foreach ( $item as $k => $v )
				{
					$csv[] = '"' . str_replace( '"', '""', $v ) . '"';
				}
				echo implode( ",", $csv ) . "\r\n";
			}
			return;
		}
	}

}
