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

require_once (JPATH_SITE . '/libraries/joomla/grid/grid.php');

class DSCForkGrid extends JGrid
{
	/**
	 * @param	string	The link title
	 * @param	string	The order field for the column
	 * @param	string	The current direction
	 * @param	string	The selected ordering
	 * @param	string	An optional task override
	 */
	public static function sort( $title, $order, $direction = 'asc', $selected = 0, $form = 'document.adminForm', $task = null, $new_direction = 'asc' )
	{
		$direction = strtolower( $direction );
		$images = array( 'sort_asc.png', 'sort_desc.png' );
		$alts = array( '&#9650;', '&#9660;' );
		$index = intval( $direction == 'desc' );
		$direction = ($direction == 'desc') ? 'asc' : 'desc';

		$html = '<a href="javascript:dscgrid.gridOrdering(\'' . $order . '\',\'' . $direction . '\', ' . $form . ' );" title="' . JText::_( 'LIB_DSCFORK_CLICK_TO_SORT_BY_THIS_COLUMN' ) . '">';
		$html .= JText::_( $title );
		if ( $order == $selected )
		{
			$html .= '<img src="' . DSCFork::getURL( 'images' ) . $images[$index] . '" border="0" alt="' . $alts[$index] . '" class="dscfork-grid-sort" />';
		}
		$html .= '</a>';
		return $html;
	}

	/**
	 * @param   integer State Value
	 * @param   string Search button value
	 * @param   string Reset Button Vaue
	 * @param   string The name of the form element
	 *
	 * @return  HTML
	 */
	public static function searchform( $value = '', $search = "Search", $reset = "Reset", $class = "unstyled dscfork-flat pad-left pull-right" )
	{
		$html = '<ul class="' . $class . '">
            <li>
                <input class="search-query" type="text" name="filter" value="' . $value . '" />
            </li>
            <li>
                <button class="btn btn-primary" onclick="this.form.submit();">' . $search . '</button>
            </li>
            <li>
                <button class="btn btn-danger" onclick="dscfork.resetFormFilters(this.form);">' . $reset . '</button>
            </li>
        </ul>';

		return $html;
	}

	/**
	 * @param   integer The row index
	 * @param   integer The record id
	 * @param   boolean
	 * @param   string The name of the form element
	 *
	 * @return  string
	 */
	public static function id( $rowNum, $recId, $checkedOut = false, $name = 'cid' )
	{
		if ( $checkedOut )
		{
			return '';
		} else
		{
			return '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId . '" onclick="isChecked(this.checked);" title="' . JText::sprintf( 'JGRID_CHECKBOX_ROW_N', ($rowNum + 1) ) . '" />';
		}
	}

	/**
	 *
	 * @param $id
	 * @return unknown_type
	 */
	public static function order( $id, $image = 'filesave.png', $task = 'saveorder', $form = 'document.adminForm' )
	{
		$result = '<a href="javascript:dscfork.gridOrder(' . $id . ', -1, ' . $form . ')" >';
		$result .= '<img src="' . DSCFork::getURL( 'images' ) . 'uparrow.png" border="0" alt="' . JText::_( "LIB_DSCFORK_MOVE_UP" ) . '" />';
		$result .= '</a>';
		$result .= '<a href="javascript:dscfork.gridOrder(' . $id . ', 1, ' . $form . ')" >';
		$result .= '<img src="' . DSCFork::getURL( 'images' ) . 'downarrow.png" border="0" alt="' . JText::_( "LIB_DSCFORK_MOVE_DOWN" ) . '" />';
		$result .= '</a>';

		return $result;
	}

	/**
	 *
	 * @param $id
	 * @param $value
	 * @return unknown_type
	 */
	public static function ordering( $id, $value )
	{
		$result = '
			 <input type="text" 
			 name="ordering[' . $id . ']" 
			 size="5" 
			 value="' . $value . '" 
			 class="text_area input-tiny" 
			 style="text-align: center" 
			 />
			 ';

		return $result;
	}

	/**
	 * Shows a true/false graphics
	 *
	 * @param	bool	Value
	 * @param 	string	Image for true
	 * @param 	string	Image for false
	 * @param 	string 	Text for true
	 * @param 	string	Text for false
	 * @return 	string	Html img
	 */
	public static function boolean( $bool, $true_img = null, $false_img = null, $true_text = null, $false_text = null )
	{
		$true_img = $true_img ? $true_img : 'tick.png';
		$false_img = $false_img ? $false_img : 'publish_x.png';
		$true_text = $true_text ? $true_text : 'JYES';
		$false_text = $false_text ? $false_text : 'JNO';

		return '<img src="' . DSCFork::getURL( 'images' ) . ($bool ? $true_img : $false_img) . '" border="0" alt="' . JText::_( $bool ? $true_text : $false_text ) . '" />';
	}

	public static function published( $row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix = '' )
	{
		$img = $row->published ? $imgY : $imgX;
		$task = $row->published ? 'unpublish' : 'publish';
		$alt = $row->published ? JText::_( 'LIB_DSCFORK_MOVE_PUBLISHED' ) : JText::_( 'LIB_DSCFORK_MOVE_UNPUBLISHED' );
		$action = $row->published ? JText::_( 'LIB_DSCFORK_UNPUBLISH_ITEM' ) : JText::_( 'LIB_DSCFORK_PUBLISH_ITEM' );

		$href = '
		<a href="javascript:void(0);" onclick="return dscfork.listItemTask(\'cb' . $i . '\',\'' . $prefix . $task . '\')" title="' . $action . '">
		<img src="' . DSCFork::getURL( 'images' ) . $img . '" border="0" alt="' . $alt . '" /></a>';

		return $href;
	}

	public static function enable( $enable, $i, $prefix = '', $imgY = 'tick.png', $imgX = 'publish_x.png' )
	{
		$img = $enable ? $imgY : $imgX;
		$task = $enable ? 'disable' : 'enable';
		$alt = $enable ? JText::_( 'LIB_DSCFORK_ENABLED' ) : JText::_( 'LIB_DSCFORK_DISABLED' );
		$action = $enable ? JText::_( 'LIB_DSCFORK_DISABLE_ITEM' ) : JText::_( 'LIB_DSCFORK_ENABLE_ITEM' );

		$href = '
		<a href="javascript:void(0);" onclick="return dscfork.listItemTask(\'cb' . $i . '\',\'' . $prefix . $task . '\')" title="' . $action . '">
		<img src="' . DSCFork::getURL( 'images' ) . $img . '" border="0" alt="' . $alt . '" />
		</a>';

		return $href;
	}

	public static function checkedout( &$row, $i, $identifier = 'id' )
	{
		$user = JFactory::getUser( );
		$userid = $user->get( 'id' );

		$result = false;
		if ( !isset( $row->checked_out ) )
		{
			$result = false;
		} elseif ( is_object( $row ) && method_exists( $row, 'isCheckedOut' ) )
		{
			$result = $row->isCheckedOut( $userid );
		} else
		{
			$table = JTable::getInstance( 'Content', 'JTable' );
			$table->isCheckedOut( $userid, $row->checked_out );
		}

		$checked = '';
		if ( $result )
		{
			if ( isset( $row->editor ) )
			{
				$checked = self::_checkedOut( $row );
			} else
			{
				$text = JFactory::getUser( $row->checked_out )->username;
				$date = JHTML::_( 'date', $row->checked_out_time, JText::_( 'DATE_FORMAT_LC1' ) );
				$time = JHTML::_( 'date', $row->checked_out_time, '%H:%M' );
				$hover = '<span class="editlinktip hasTip" title="' . JText::sprintf( 'LIB_DSCFORK_CHECKED_OUT_BY_NAME_ON_DATE_AT_TIME', $text, $date, $time ) . '">';
				$checked = $hover . '<img src="' . DSCFork::getURL( 'images' ) . 'checked_out.png"/></span>';
			}

		} else
		{
			$checked = JHTML::_( 'grid.id', $i, $row->$identifier );
		}

		return $checked;
	}

	public static function pagetooltip( $key, $title = 'Tip', $id = 'page_tooltip', $app = null )
	{
		$href = '';

		$constant = 'page_tooltip_' . $key;
		$app = JFactory::getApplication( )->input->getCmd( 'option' );
		$defines = DSCFork::getApp( $app );
		$disabled = $defines->get( $constant . "_disabled", '0' );

		$full_constant = strtoupper( $app . "_" . $constant );
		$lang = JFactory::getLanguage( );
		if ( $lang->hasKey( $full_constant ) && !$disabled )
		{
			$option = strtolower( $app );
			$view = strtolower( JFactory::getApplication( )->input->getCmd( 'view' ) );
			$task = "page_tooltip_disable";
			$url = JRoute::_( "index.php?option={$option}&controller={$view}&view={$view}&task={$task}&key={$key}" );
			$link = "<a href='{$url}'>" . JText::_( "LIB_DSCFORK_HIDE_THIS" ) . "</a>";

			$href = '
				<fieldset class="' . $id . '">
					<legend class="' . $id . '">' . JText::_( $title ) . '</legend>
					' . JText::_( $full_constant ) . '
					<span class="' . $id . '" style="float: right;">' . $link . '</span>
				</fieldset>
			';
		}

		return $href;
	}

	public static function checkoutnotice( $row, $title = 'Item', $lock_task = 'edit' )
	{
		if ( !isset( $row->checked_out ) )
		{
			return null;
		}

		if ( JFactory::getUser( )->id == @$row->checked_out )
		{
			$html = "
			<div class='note'>
				" . JText::_( 'LIB_DSCFORK_ITEM_CHECKED_OUT_BY_YOU', $title ) . "
				<button onclick='document.getElementById(\"task\").value=\"release\"; this.form.submit();'>" . JText::sprintf( 'LIB_DSCFORK_RELEASE_ITEM', $title ) . "</button>
			</div>
			";
		} elseif ( !empty( $row->checked_out ) )
		{
			$html = "
			<div class='note'>
				" . sprintf( JText::sprintf( 'LIB_DSCFORK_ITEM_CHECKED_OUT_BY_SOMEONE', $title ), JFactory::getUser( @$row->checked_out )->username ) . "
			</div>
			";
		} else
		{
			$html = "
			<div class='note'>
				" . JText::sprintf( 'LIB_DSCFORK_ITEM_CHECKED_OUT_BY_NOBODY', $title ) . "
				<button onclick='document.getElementById(\"task\").value=\"$lock_task\"; this.form.submit();'>" . JText::_( 'LIB_DSCFORK_LOCK_ITEM', $title ) . "</button>
			</div>
			";
		}

		return $html;
	}

	protected static function _checkedOut( &$row, $overlib = 1 )
	{
		$hover = '';
		if ( $overlib )
		{
			$text = addslashes( htmlspecialchars( $row->editor ) );

			$date = JHTML::_( 'date', $row->checked_out_time, JText::_( 'DATE_FORMAT_LC1' ) );
			$time = JHTML::_( 'date', $row->checked_out_time, '%H:%M' );

			$hover = '<span class="editlinktip hasTip" title="' . JText::_( 'LIB_DSCFORK_ITEM_CHECKED_OUT' ) . '::' . $text . '<br />' . $date . '<br />' . $time . '">';
		}
		$checked = $hover . '<img src="' . DSCFork::getURL( 'images' ) . 'checked_out.png"/></span>';

		return $checked;
	}

	public static function required( $text = '', $css_suffix = '' )
	{
		$css_class = 'dscfork-required';
		if ( strlen( $css_suffix ) )
			$css_class .= $css_suffix;

		$txt = 'LIB_DSCFORK_REQUIRED';
		if ( strlen( $text ) )
			$txt = $text;

		$html = '<div class="' . $css_class . '" title="' . JText::_( $txt ) . '"></div>';
		return $html;
	}

}
