<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * A DSCForkRecaptchaResponse is returned from recaptcha_check_answer()
 */
class DSCForkRecaptchaResponse extends JObject
{
	var $is_valid;
	var $error;
}
