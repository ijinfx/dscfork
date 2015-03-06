<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library/model
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class DSCForkModelElement extends DSCForkModel
{
	public $cache_enabled = false;

	var $title_key = 'title';
	var $select_title_constant = 'LIB_DSCFORK_SELECT_ITEM';
	var $select_constant = 'LIB_DSCFORK_SELECT';
	var $clear_constant = 'LIB_DSCFORK_CLEAR_SELECTION';

	public function __construct( $config = array() )
	{
		parent::__construct( $config );

		if ( !empty( $this->option ) )
		{
			$option = $this->option;
		} else
		{
			$r = null;

			if ( !preg_match( '/(.*)Model/i', get_class( $this ), $r ) )
			{
				JError::raiseError( 500, JText::_( 'JLIB_APPLICATION_ERROR_MODEL_GET_NAME' ) );
			}

			$option = 'com_' . strtolower( $r[1] );
		}

		$lang = JFactory::getLanguage( );
		$lang->load( $option );
		$lang->load( $option, JPATH_ADMINISTRATOR );
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function fetchElement( $name, $value = '', $control_name = '', $js_extra = '', $fieldName = '' )
	{
		$doc = JFactory::getDocument( );

		if ( empty( $fieldName ) )
		{
			$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;
		}

		if ( $value )
		{
			$table = $this->getTable( );
			$table->load( $value );
			$title_key = $this->title_key;
			$title = $table->$title_key;
		} else
		{
			$title = JText::_( $this->select_title_constant );
		}

		$close_window = "window.parent.SqueezeBox.close();";

		$js = "dscfork.select" . $this->getName( ) . " = function(id, title, object) {
                        document.getElementById(object + '_id').value = id;
                        document.getElementById(object + '_name').value = title;
                        document.getElementById(object + '_name_hidden').value = title;
        $close_window
        $js_extra
                   }";
		$doc->addScriptDeclaration( $js );

		if ( !empty( $this->option ) )
		{
			$option = $this->option;
		} else
		{
			$r = null;

			if ( !preg_match( '/(.*)Model/i', get_class( $this ), $r ) )
			{
				JError::raiseError( 500, JText::_( 'JLIB_APPLICATION_ERROR_MODEL_GET_NAME' ) );
			}

			$option = 'com_' . strtolower( $r[1] );
		}
		$link = 'index.php?option=' . $option . '&view=' . $this->getName( ) . '&tmpl=component&object=' . $name;

		JHTML::_( 'behavior.modal', 'a.modal' );
		$html = "\n" . '<input type="text" id="' . $name . '_name" value="' . htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) . '" disabled="disabled" />';
		$html .= '<a class="modal btn btn-primary" style="color : white; margin-left : 2px;" title="' . JText::_( $this->select_title_constant ) . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">' . JText::_( $this->select_constant ) . '</a>' . "\n";
		$html .= "\n" . '<input type="hidden" id="' . $name . '_id" name="' . $fieldName . '" value="' . $value . '" />';
		$html .= "\n" . '<input type="hidden" id="' . $name . '_name_hidden" name="' . $name . '_name_hidden" value="' . htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) . '" />';

		return $html;
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function clearElement( $name, $value = '', $control_name = '' )
	{
		$doc = JFactory::getDocument( );
		$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;

		$js = "
            dscfork.reset" . $this->getName( ) . " = function(id, title, object) {
                document.getElementById(object + '_id').value = id;
                document.getElementById(object + '_name').value = title;
            }";
		$doc->addScriptDeclaration( $js );

		$html = '
                    <a href="javascript:void(0);" style="color : white;" class="btn btn-danger" onclick="dscfork.reset' . $this->getName( ) . '( \'' . $value . '\', \'' . JText::_( $this->select_title_constant ) . '\', \'' . $name . '\' )">' . JText::_( $this->clear_constant ) . '
                    </a>
            ';

		return $html;
	}

}
