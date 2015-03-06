<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library/helper
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class DSCForkHelperUser extends DSCForkHelper
{
	/**
	 *
	 * @param $string
	 * @return unknown_type
	 */
	public static function usernameExists( $string )
	{
		// TODO Make this use ->load()

		$success = false;

		$db = JFactory::getDBO( );
		$query = $db->getQuery( true );
		$query->select( $db->quoteName( '*' ) );
		$query->from( $db->quoteName( '#__users' ) );
		$query->where( '1' );
		$query->where( $db->quoteName( 'username' ) . '=' . $db->quote( $string ) );
		$query->setLimit( '1' );

		$db->setQuery( $query );

		$result = $db->loadObject( );
		if ( $result )
		{
			$success = $result;
		}
		return $success;
	}

	/**
	 *
	 * @param $string
	 * @return unknown_type
	 */
	public static function emailExists( $string, $table = 'users' )
	{
		switch($table)
		{
			case  'users':
			default:
				$table = '#__users';
		}

		$success = false;

		$db = JFactory::getDBO( );
		$query = $db->getQuery( true );
		$query->select( $db->quoteName( '*' ) );
		$query->from( $db->quoteName( $table ) );
		$query->where( '1' );
		$query->where( $db->quoteName( 'email' ) . '=' . $db->quote( $string ) );
		$query->setLimit( '1' );

		$db->setQuery( $query );
		$result = $db->loadObject( );
		if ( $result )
		{
			$success = true;
		}
		return $result;
	}

	/**
	 * Returns yes/no
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	public static function createNewUser( $details, $guest = false )
	{
		$success = false;
		// Get required system objects
		$user = clone(JFactory::getUser( ));
		$config = JFactory::getConfig( );
		$authorize = JFactory::getACL( );

		$usersConfig = JComponentHelper::getParams( 'com_users' );

		// Bind the post array to the user object
		if ( !$user->bind( $details ) )
		{
			$this->setError( $user->getError( ) );
			return false;
		}

		if ( empty( $user->password ) )
		{
			jimport( 'joomla.user.helper' );
			$user->password = JUserHelper::genRandomPassword( );
		}

		// Set some initial user values
		$user->set( 'id', 0 );
		$user->set( 'usertype', '' );

		$newUsertype = $usersConfig->get( 'new_usertype', '2' );

		// Joomla! 1.6+ code here
		$user->set( 'usertype', 'deprecated' );
		$user->set( 'groups', array( $newUsertype ) );

		$date = JFactory::getDate( );
		$user->set( 'registerDate', $date->toMySQL( ) );

		// we disable useractivation for auto-created users
		$useractivation = (!empty( $this->useractivation )) ? '1' : '0';
		if ( $useractivation == '1' )
		{
			jimport( 'joomla.user.helper' );
			$user->set( 'activation', md5( JUserHelper::genRandomPassword( ) ) );
			$user->set( 'block', '0' );
		}

		// If there was an error with registration, set the message and display form
		if ( !$user->save( ) )
		{
			$msg->message = $user->getError( );
			return $success;
		}

		$app = DSCFork::getApp( );
		if ( !$app->get( 'disable_guest_signup_email' ) )
		{
			// Send registration confirmation mail
			self::sendMail( $user, $details, $useractivation, $guest );
		}

		return $user;
	}

	/**
	 * Returns yes/no
	 * @param array [username] & [password]
	 * @param mixed Boolean
	 *
	 * @return array
	 */
	public static function login( $credentials, $remember = '', $return = '' )
	{
		$app = JFactory::getApplication( );

		if ( strpos( $return, 'http' ) !== false && strpos( $return, JURI::base( ) ) !== 0 )
		{
			$return = '';
		}

		// $credentials = array();
		
		// $credentials = $app->input->post->get('username', '', 'USERNAME');
		// $credentials = $app->input->post->getString('passwd');
	
		$options = array( );
		$options['remember'] = $remember;
		$options['return'] = $return;

		//preform the login action
		$success = $app->login( $credentials, $options );

		if ( $return )
		{
			$app->redirect( $return );
		}

		return $success;
	}

	/**
	 * Returns yes/no
	 * @param mixed Boolean
	 * @return array
	 */
	public static function logout( $return = '' )
	{
		$mainframe = JFactory::getApplication( );

		//preform the logout action//check to see if user has a joomla account
		//if so register with joomla userid
		//else create joomla account
		$success = $mainframe->logout( );

		if ( strpos( $return, 'http' ) !== false && strpos( $return, JURI::base( ) ) !== 0 )
		{
			$return = '';
		}

		if ( $return )
		{
			$mainframe->redirect( $return );
		}

		return $success;
	}

	/**
	 * Unblocks a user
	 *
	 * @param int $user_id
	 * @param int $unblock
	 * @return boolean
	 */
	public static function unblockUser( $user_id, $unblock = 1 )
	{
		$user = JFactory::getUser( (int)$user_id );

		if ( $user->get( 'id' ) )
		{
			$user->set( 'block', !$unblock );

			if ( !$user->save( ) )
			{
				return false;
			}

			return true;
		} else
		{
			return false;
		}
	}

	/**
	 * Returns yes/no
	 * @param object
	 * @param mixed Boolean
	 * @return array
	 */
	private static function sendMail( &$user, $details, $useractivation, $guest = false )
	{
		$com = DSCFork::getApp( );
		$com_name = strtoupper( 'com_' . $com->getName( ) );

		$lang = JFactory::getLanguage( );
		$lang->load( 'lib_dscfork', JPATH_ADMINISTRATOR );

		$mainframe = JFactory::getApplication( );

		$db = JFactory::getDBO( );

		$name = $user->get( 'name' );
		$email = $user->get( 'email' );
		$username = $user->get( 'username' );
		$activation = $user->get( 'activation' );
		$password = $details['password2'];
		// using the original generated pword for the email

		$usersConfig = JComponentHelper::getParams( 'com_users' );
		// $useractivation = $usersConfig->get( 'useractivation' );
		$sitename = $mainframe->getCfg( 'sitename' );
		$mailfrom = $mainframe->getCfg( 'mailfrom' );
		$fromname = $mainframe->getCfg( 'fromname' );
		$siteURL = JURI::base( );

		$subject = sprintf( JText::_( $com_name . '_ACCOUNT_DETAILS_FOR' ), $name, $sitename );
		$subject = html_entity_decode( $subject, ENT_QUOTES );

		if ( $useractivation == 1 )
		{
			$message = sprintf( JText::_( $com_name . '_EMAIL_MESSAGE_ACTIVATION' ), $sitename, $siteURL, $username, $password, $activation );
		} else
		{
			$message = sprintf( JText::_( $com_name . '_EMAIL_MESSAGE' ), $sitename, $siteURL, $username, $password );
		}

		if ( $guest )
		{
			$message = sprintf( JText::_( $com_name . '_EMAIL_MESSAGE_GUEST' ), $sitename, $siteURL, $username, $password );
		}

		$message = html_entity_decode( $message, ENT_QUOTES );

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' . ' FROM #__users' . ' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList( );

		// Send email to user
		if ( !$mailfrom || !$fromname )
		{
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		$success = self::doMail( $mailfrom, $fromname, $email, $subject, $message );

		return $success;
	}

	/**
	 *
	 * @return unknown_type
	 */
	private static function doMail( $from, $fromname, $recipient, $subject, $body, $actions = NULL, $mode = NULL, $cc = NULL, $bcc = NULL, $attachment = NULL, $replyto = NULL, $replytoname = NULL )
	{
		$success = false;

		$message = JFactory::getMailer( );
		$message->addRecipient( $recipient );
		$message->setSubject( $subject );

		// check user mail format type, default html
		$message->IsHTML( true );
		$body = htmlspecialchars_decode( $body );
		$message->setBody( nl2br( $body ) );

		$sender = array( $from, $fromname );
		$message->setSender( $sender );

		$sent = $message->send( );
		if ( $sent == '1' )
		{
			$success = true;
		}
		return $success;

	}

	/**
	 * Updates the core __users table
	 * setting the email address = $email
	 */
	public static function updateUserEmail( $userid, $email )
	{
		$user = JFactory::getUser( $userid );
		$user->set( 'email', $email );

		if ( !$user->save( ) )
		{
			$this->setError( $user->getError( ) );
			return false;
		}
		return true;
	}

	/**
	 * Gets the next auto-inc id in the __users table
	 */
	public static function getLastUserId( )
	{
		$database = JFactory::getDBO( );
		$query = "
            SELECT 
                MAX(id) as id
            FROM 
                #__users
            ";
		$database->setQuery( $query );
		$result = $database->loadObject( );

		return $result->id;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $default
	 * @param unknown_type $fieldname
	 * @return Ambigous <mixed, boolean>
	 */
	public static function getACLSelectList( $default = '', $fieldname = 'core_user_new_gid' )
	{
		$db = JFactory::getDbo( );
		$query = 'SELECT CONCAT( REPEAT(\'..\', COUNT(parent.id) - 1), node.title) as text, node.id as value' . ' FROM #__usergroups AS node, #__usergroups AS parent' . ' WHERE node.lft BETWEEN parent.lft AND parent.rgt' . ' GROUP BY node.id' . ' ORDER BY node.lft';
		$db->setQuery( $query );
		$gtree = $db->loadObjectList( );

		$object = new JObject( );
		$object->value = '';
		$object->text = JText::_( "No Change" );

		foreach ( $gtree as $key => $item )
		{
			if ( $item->value == '29' || $item->value == '30' )
			{
				unset( $gtree[$key] );
			}
		}
		array_unshift( $gtree, $object );
		return JHTML::_( 'select.genericlist', $gtree, $fieldname, 'size="1"', 'value', 'text', $default );

	}

	/**
	 * Verifies that the string is in a proper e-mail address format.
	 *
	 * @static
	 * @param string $email String to be verified.
	 * @return boolean True if string has the correct format; false otherwise.
	 * @since 1.5
	 */
	public static function isEmailAddress( $email )
	{
		// Split the email into a local and domain
		$atIndex = strrpos( $email, "@" );
		$domain = substr( $email, $atIndex + 1 );
		$local = substr( $email, 0, $atIndex );

		// Check Length of domain
		$domainLen = strlen( $domain );
		if ( $domainLen < 1 || $domainLen > 255 )
		{
			return false;
		}

		// Check the local address
		// We're a bit more conservative about what constitutes a "legal" address, that is, A-Za-z0-9!#$%&\'*+/=?^_`{|}~-
		$allowed = 'A-Za-z0-9!#&*+=?_-';
		$regex = "/^[$allowed][\.$allowed]{0,63}$/";
		if ( !preg_match( $regex, $local ) )
		{
			return false;
		}

		// No problem if the domain looks like an IP address, ish
		$regex = '/^[0-9\.]+$/';
		if ( preg_match( $regex, $domain ) )
		{
			return true;
		}

		// Check Lengths
		$localLen = strlen( $local );
		if ( $localLen < 1 || $localLen > 64 )
		{
			return false;
		}

		// Check the domain
		$domain_array = explode( ".", rtrim( $domain, '.' ) );
		if ( count( $domain_array ) == 1 )
		{
			return false;
		}

		$regex = '/^[A-Za-z0-9-]{0,63}$/';
		foreach ( $domain_array as $domain )
		{

			// Must be something
			if ( !$domain )
			{
				return false;
			}

			// Check for invalid characters
			if ( !preg_match( $regex, $domain ) )
			{
				return false;
			}

			// Check for a dash at the beginning of the domain
			if ( strpos( $domain, '-' ) === 0 )
			{
				return false;
			}

			// Check for a dash at the end of the domain
			$length = strlen( $domain ) - 1;
			if ( strpos( $domain, '-', $length ) === $length )
			{
				return false;
			}

		}

		return true;
	}

	/**
	 * Returns url for user login view in Joomla!
	 *
	 * @param unknown_type $return	Return string
	 *
	 * @return Correct url
	 */
	public static function getUserLoginUrl( $return = '' )
	{
		$result = "index.php?option=com_users&view=login";
		if ( !empty( $return ) )
		{
			$result .= '&return=' . base64_encode( $return );
		}
		return $result;
	}

}
