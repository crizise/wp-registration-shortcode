<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      0.0.2
 * @package    registration-shortcode
 * @subpackage registration-shortcode/includes
 */
/**
 * The core plugin class.
 *
 * @since      0.0.2
 * @package    registration-shortcode
 * @subpackage registration-shortcode/includes
 * @author     Alex K <a.kolomitsev@gmail.com>
 */

class Registration_Shortcode {
	
	protected $version;
	protected $plugin_name;
	public $notification;
	public $new_user;
	public function __construct() {
		
		if ( defined( 'WP_REGISTRATION_SHORTCODE_VERSION' ) ) {
			$this->version = WP_REGISTRATION_SHORTCODE_VERSION;
		} else {
			$this->version = '0.0.2';
		}
		$this->plugin_name = 'registration-shortcode';
		$this->create_shortcode();
		$this->listen_registration_sh_ajax();
		$this->run_notifications();
		$this->listen_confirmation();
		$this->complete_registration();
	}

	public function create_shortcode() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-registration-shortcode-shortcode.php';
		$shortcode = new Registration_Shortcode_Shortcode();
	}

	public function listen_confirmation() {
		if (isset($_GET['token'])):
			return Registration_Shortcode_Notification::get_confirmed_user($_GET['token']);
		endif;
	}
	public function listen_registration_sh_ajax() {
		add_action('wp_ajax_registration_sh_ajax_handler', array($this, 'registration_sh_ajax_handler'));
		add_action('wp_ajax_nopriv_registration_sh_ajax_handler', array($this, 'registration_sh_ajax_handler'));
	}

	public function run_notifications() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-registration-shortcode-notification.php';
		$notification = new Registration_Shortcode_Notification();
	}

	public function registration_sh_ajax_handler() {

		if (isset($_POST) && !empty($_POST['data'])):
			$valid_nonce = $this->check_nonce( $_POST['registration_nonce'], 'registration_sh_ajax' );			
			$valid_fields = $this->validate_fields($_POST['data']);			

			if (!empty($valid_fields)) :
				Registration_Shortcode_Notification::send_confirmation_email($valid_fields);
			endif;

		endif;
		wp_die();
	}

	public function complete_registration() {
		add_action('init', array($this, 'register_new_user'));
	}

	public function register_new_user() {
		$user_fields = $this->listen_confirmation();

		if (isset($user_fields) && !empty($user_fields)):

			$user_data = array(
				'user_login'    =>   $user_fields['username'],
				'user_email'    =>   $user_fields['email'],
				'user_pass'     =>   $user_fields['password'],
			);
			$user_id = wp_insert_user($user_data);
			if( is_numeric($user_id) ) :
				//Registration_Shortcode_Notification::send_user_registration_complete($user_id);
				//Registration_Shortcode_Notification::send_admin_new_registration($user_id);


				if (isset($_GET['token'])) :
					Registration_Shortcode_Notification::update_unconfirmed_users($_GET['token']);
				endif;

				$secure_cookie = is_ssl() ? true : false;
				wp_set_auth_cookie( $user_id, true, $secure_cookie );
				wp_safe_redirect( home_url( '/' ) );
				exit;
			endif;

		endif;
	}

	private function check_nonce($nonce = '', $action_name = '') {
		if ( !wp_verify_nonce( $_POST['registration_nonce'], 'registration_sh_ajax' ) ) :
			$response = wp_send_json_error(
				new WP_Error( '401', 'Not authorized.' )
			);
		endif;
	}

	private function validate_fields($data = array()) {

		$errors = new WP_Error;
		$fields = [];
		for ( $i=0; $i < count($data); $i++ ) { 
			$fields[$data[$i]['name']] = $data[$i]['value'];
		}

		if( username_exists($fields['username']) ):
			$errors->add( 'username', 'This username already exist.' );
		endif;

		if( !validate_username($fields['username']) ):
			$errors->add( 'username', 'Username you entered is not valid.' );
		endif;

		if( !is_email($fields['email']) ):
			$errors->add( 'email', 'Email is not valid.' );
		endif;

		if( email_exists($fields['email']) ):
			$errors->add( 'email', 'This email already exist.' );
		endif;

		if( 6 > strlen( $fields['password']) ):
			$errors->add( 'password', 'Password must contain at least 6 symbols' );
		endif;

		if( $fields['password'] != $fields['repassword'] ):
			$errors->add( 'repassword', 'Password fields must be the same' );
		endif;		

		if ( !empty($errors->get_error_messages()) ): 
			wp_send_json_error($errors);
		else:
			return $fields;
		endif;
	}	
}