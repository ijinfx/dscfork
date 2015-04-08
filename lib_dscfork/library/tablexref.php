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

class DSCForkTableXref extends DSCForkTable
{
	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function store( $updateNulls = false )
	{
		$dispatcher = JDispatcher::getInstance( );
		$before = $dispatcher->trigger( 'onBeforeStore' . $this->get( '_suffix' ), array( $this ) );
		if( in_array( false, $before, true ) )
		{
			return false;
		}

		// check if a record exists with these key values
		$already = clone $this;
		$keynames = $this->getKeyNames( );
		foreach( $keynames as $key => $value )
		{
			$keynames[ $key ] = $this->$key;
		}

		if( $already->load( $keynames ) )
		{
			$ret = $this->updateObject( $updateNulls );
		} else
		{
			$ret = $this->insertObject( );
		}

		if( !$ret )
		{
			$this->setError( get_class( $this ) . '::store failed - ' . $this->getError( ) );
			$return = false;
		} else
		{
			$return = true;
		}

		if( $return )
		{
			$dispatcher = JDispatcher::getInstance( );
			$dispatcher->trigger( 'onAfterStore' . $this->get( '_suffix' ), array( $this ) );
		}
		return $return;
	}

	/**
	 * (non-PHPdoc)
	 * @see sample/admin/tables/DSCForkTable#delete($oid)
	 */
	function delete( $oid = '' )
	{
		if( empty( $oid ) )
		{
			// if empty, use the values of the current keys
			$keynames = $this->getKeyNames( );
			foreach( $keynames as $key => $value )
			{
				$oid[ $key ] = $this->$key;
			}
			if( empty( $oid ) )
			{
				// if still empty, fail
				$this->setError( JText::_( "LIB_DSCFORK_CANNOT_LOAD_WITH_EMPTY_KEY" ) );
				return false;
			}
		}
		$oid = (array)$oid;

		$dispatcher = JDispatcher::getInstance( );
		$before = $dispatcher->trigger( 'onBeforeDelete' . $this->get( '_suffix' ), array( $this, $oid ) );
		if( in_array( false, $before, true ) )
		{
			return false;
		}

		$db = $this->getDBO( );

		// initialize the query
		$query = new DSCForkQuery( );
		$query->delete( );
		$query->from( $this->getTableName( ) );

		foreach( $oid as $key => $value )
		{
			// Check that $key is field in table
			if( !in_array( $key, array_keys( $this->getProperties( ) ) ) )
			{
				$this->setError( JText::sprintf( 'LIB_DSCFORK_CLASS_DOES_NOT_HAVE_THE_FIELD_KEY', get_class( $this ), $key ) );
				return false;
			}
			// add the key=>value pair to the query
			$value = $db->Quote( $db->escape( trim( strtolower( $value ) ) ) );
			$query->where( $key . ' = ' . $value );
		}

		$db->setQuery( (string)$query );

		if( $db->execute( ) )
		{
			$dispatcher = JDispatcher::getInstance( );
			$dispatcher->trigger( 'onAfterDelete' . $this->get( '_suffix' ), array( $this, $oid ) );
			return true;
		} else
		{
			$this->setError( $db->getErrorMsg( ) );
			return false;
		}
	}

	/**
	 * Inserts a row into a table based on an objects properties
	 *
	 * @access	public
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	function insertObject( )
	{
		$table = $this->getTableName( );
		$fmtsql = 'INSERT INTO ' . $this->_db->quoteName( $table ) . ' ( %s ) VALUES ( %s ) ';
		$fields = array( );
		foreach( get_object_vars( $this ) as $k => $v )
		{
			if( is_array( $v ) or is_object( $v ) or $v === NULL )
			{
				continue;
			}
			if( $k[ 0 ] == '_' )
			{
				// internal field
				continue;
			}
			$fields[ ] = $this->_db->quoteName( $k );
			//$values[ ] = $this->_db->isQuoted( $k ) ? $this->_db->Quote( $v ) : (int)$v;
			// method isQuoted is depreciated so we cast the values to int for now
			// TODO: find a better way?
			$values[ ] = (int)$v;
		}
		$this->_db->setQuery( sprintf( $fmtsql, implode( ",", $fields ), implode( ",", $values ) ) );
		if( !$this->_db->execute( ) )
		{
			$this->setError( $this->_db->getErrorMsg( ) );
			return false;
		}
		return true;
	}

	/**
	 * Updates an existing role
	 *
	 * @access public
	 * @param [type] $updateNulls
	 */
	function updateObject( $updateNulls = true )
	{
		$table = $this->getTableName( );
		$fmtsql = 'UPDATE ' . $this->_db->quoteName( $table ) . ' SET %s WHERE %s';
		$tmp = array( );
		$where = array( );
		foreach( get_object_vars( $this ) as $k => $v )
		{
			if( is_array( $v ) or is_object( $v ) or $k[ 0 ] == '_' )
			{
				// internal or NA field
				continue;
			}

			if( in_array( $k, $this->getKeyNames( ) ) )
			{
				// Allow PKs to be updated
				// TODO Use query builder
				// ->where()
				$where[ ] = $k . '=' . $this->_db->Quote( $v );
			}

			if( $v === null )
			{
				if( $updateNulls )
				{
					$val = 'NULL';
				} else
				{
					continue;
				}
			} else
			{
				//$val = $this->_db->isQuoted( $k ) ? $this->_db->Quote( $v ) : (int)$v;
				
				// method isQuoted is depreciated so we cast the values to int for now
				// TODO: find a better way?
				$val = (int)$v;
			}
			$tmp[ ] = $this->_db->quoteName( $k ) . '=' . $val;
		}
		$this->_db->setQuery( sprintf( $fmtsql, implode( ",", $tmp ), implode( " AND ", $where ) ) );
		if( !$this->_db->execute( ) )
		{
			$this->setError( $this->_db->getErrorMsg( ) );
			return false;
		}
		return true;
	}

}
