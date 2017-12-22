<?php

class Registration_Shortcode_Notification {
	
	const PREFIX = 'registration_confirmation_';
	
	public function __construct(){
		add_action( 'user_register', array($this, 'send_user_registration_complete') );
		add_action( 'user_register', array($this, 'send_admin_new_registration') );
	}

	private static function get_wbdb(){
		return $GLOBALS['wpdb'];
	}

	public static function send_user_registration_complete($user_id) {
		$user_info = get_userdata($user_id);
		$to = $user_info->user_email;
		$subject = "Registration at American Russian-speaking IT community";
		$message = "<h4>Welcome to the American Russian-Speaking IT Community,</h4>
		<p>By becoming a member, you will receive:</p>
		<ul>
		<li>free passes to our future events</li>
		<li>access to premier member website areas</li>
		<li>monthly newsletter</li>
		</ul>
		<p>If you have any questions please email us at: <a href='mailto:info@arsitc.com'>info@arsitc.com</a>.</p>
		<p>Thanks again. You've made a great decision.</p>
		<p><a href='". get_option('siteurl') ."' style='text-transform:uppercase'>". str_replace(array( 'http://', 'https://' ), '', get_option('siteurl')) ."</a></p>";
		$headers = self::mail_headers();

		wp_mail($to, $subject, $message, $headers);
	}

	public static function send_admin_new_registration($user_id){
		$user_info = get_userdata($user_id);
		$to = get_option('admin_email');
		$subject = "New User Registration";
		$message = '<h2>New user has been registred at: '. get_option("blogname") .':</h2>
		<ul>
		<li>Email: '. $user_info->user_email .'</li>
		<li>Nickname: '. $user_info->user_login . '</li>
		</ul>';
		$headers = self::mail_headers();
		
		wp_mail($to, $subject, $message, $headers);	
	}
	
	public function send_confirmation_email( $fields = array()){
		
		$token = md5(random_bytes(2));
		$to = $fields['email'];
		$subject = 'Complete Registration at ARSITC website';
		$message = "<h4>Thank you!</h4>
		<p>Please <a href=" . home_url() .'/join-us/?nocache=1&token=%s' . " >complete</a> registation.</p>";
		$headers = self::mail_headers();
		
		self::save_user_fields($fields, $token);

		$confirmation_email = wp_mail($to, $subject, sprintf($message, $token), $headers);

		if ($confirmation_email):
			wp_send_json_success('Success! Please, check your email for complete registation.');
		else: 
			wp_send_json_success('Email with confirmation link was not sent. Please try again later.');
		endif;
	}

	private function save_user_fields($fields, $token) {
		$prev_data = self::get_unconfirmed_users() ?: array();
		$data = array();
		$data[$token] = $fields;
		#need sanitize fields
		update_option(self::PREFIX .'data', array_merge($prev_data, $data));
	}

	private static function mail_headers(){
		$headers = 'MIME-Version: 1.0' . "\r\n".
		'Content-type: text/html; charset=iso-8859-1' . "\r\n".
		'From:  ARSITC Notification Center < info@arsitc.com >' . " \r\n" .
		'Reply-To: info@arsitc.com'."\r\n" . 
		'Bcc: a.kolomitsev@gmail.com' . "\r\n".
		'X-Mailer: PHP/' . phpversion();

		return $headers;
	}

	public static function get_confirmed_user($token) {
		
		$data = self::get_user_fields($token);
		return $data;
	}

	private static function get_user_fields($token){
		$wpdb = self::get_wbdb();

		$option_name = self::PREFIX .'data';
		$data = $wpdb->get_var($wpdb->prepare( 
			"
			SELECT option_value 
			FROM $wpdb->options 
			WHERE option_name = %s
			",
			$option_name
		) );
		$data = unserialize($data);
		if (array_key_exists($token ,$data)) {
			$user_fields = $data[$token];
			return $user_fields;
		}
	}
	public static function get_unconfirmed_users(){
		$wpdb = self::get_wbdb();

		$option_name = self::PREFIX .'data';
		$data = $wpdb->get_var($wpdb->prepare( 
			"
			SELECT option_value 
			FROM $wpdb->options 
			WHERE option_name = %s
			",
			$option_name
		) );
		$users = unserialize($data);

		return $users;
		
	}
	public static function remove_confirmed_user($token){
		$users = self::get_unconfirmed_users();
		if ($users[$token]):
			unset($users[$token]);
			return $users;
		endif;
	}

	public static function update_unconfirmed_users($token){
		$unconfirmed_users = serialize(self::remove_confirmed_user($token));

		$option_name = self::PREFIX .'data';
		$wpdb = self::get_wbdb();
		$update = $wpdb->update(
			$wpdb->options,
			array(
				'option_value' => $unconfirmed_users
			),
			array(
				'option_name' => $option_name
			),
			array(
				'%s'
			),
			array(
				'%s'
			)
		);

		return $update;
	}

	
}