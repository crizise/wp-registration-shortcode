<?php

class Registration_Shortcode_Shortcode {
	public  function __construct() {
		$this->load_scripts();
		$this->load_shortcode();
	}

	public function load_shortcode() {
		add_shortcode( 'registration_sh', array( $this, 'shortcode_html' ) );
	}

	public function shortcode_html( $atts ) {
		ob_start();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/form.php';
		return ob_get_clean();
	}

	public function load_scripts() {
		add_action('wp_enqueue_scripts', array( $this, 'registration_sh_ajax_script' ));
	}
	
	public function registration_sh_ajax_script() {
		wp_enqueue_script( 'registration_sh_ajax', plugin_dir_url( dirname(__FILE__) ).'public/js/registration_sh_ajax.js', 'registration-shortcode', null, false, false );
	}
}