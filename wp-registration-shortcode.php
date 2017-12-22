<?php 

/**
 *
 * Plugin Name: Registration Shortcode
 * Description: Allow to use registration form anywhere at website using simple shortcode [registration]. Plugin make registration flow easier for user and safest for website owner using registration email confirmation.
 * Version: 0.0.2
 * Author: Alex.K
 * Author URI: https://goo.gl/Cw35nH
 * 
 * @link              
 * @since             0.0.2
 * @package           registration-shortcode
 * 
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

define('WP_REGISTRATION_SHORTCODE_VERSION', '0.0.2');

require plugin_dir_path( __FILE__ ) . 'includes/class-registration-shortcode.php';

function run_registration_shortcode() {
	$plugin = new Registration_Shortcode();
	$plugin->create_shortcode();
}
run_registration_shortcode();