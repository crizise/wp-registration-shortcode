<form method="post" class="" id="registration_sh">
	<div class="register-form bg-w-form rlp-form">
		<div class="row">
			<div class="col-md-6">
				<label class="control-label form-label">
					<?php esc_html_e( 'NAME', 'registration-shortcode' ); ?> 
					<span class="required">*</span>
				</label>
				<input type="text" placeholder="" class="form-control form-input" value="<?php echo !empty( $_POST['username'] ) ? esc_attr( $_POST['username'] ) : '' ?>" name="username" required>
				<label class="error username"></label>
			</div>
			<div class="col-md-6">
				<label class="control-label form-label">
					<?php esc_html_e( 'email', 'registration-shortcode' ); ?> 
					<span class="required">*</span>
				</label>
				<input type="email" placeholder="" class="form-control form-input" value="<?php echo !empty( $_POST['email'] ) ?  esc_attr( $_POST['email'] ) : '' ?>" name="email" required>
				<label class="error email"></label>
			</div>
			<div class="col-md-6">
				<label class="control-label form-label">
					<?php esc_html_e( 'password', 'registration-shortcode' ); ?> 
					<span class="required">*</span>
				</label>
				<input type="password" placeholder="" class="form-control form-input" name="password" required>
				<label class="error password"></label>
			</div>
			<div class="col-md-6">
				<label class="control-label form-label">
					<?php esc_html_e( 'confirm password', 'registration-shortcode' ); ?> 
					<span class="required">*</span>
				</label>
				<input type="password" placeholder="" class="form-control form-input" name="repassword" required>
				<label class="error repassword"></label>
			</div>			
		</div>
		<div class="row register-submit">
			<div class="col-md-5">
				<div class="register-submit">
					<?php wp_nonce_field( 'registration_sh_ajax', 'registration_nonce' ); ?>
					<input type="submit" id="register" class="btn btn-register btn-green" name="submit" value="<?php esc_html_e( 'Create account', 'registration-shortcode' ); ?>"/>
				</div>
			</div>
			<div class="col-md-1">
				<p>or</p>
			</div>
			<div class="col-md-5">
				<div class="linkedin-shortcode text-center">
					<?php echo do_shortcode( '[wpli_login_link]' ); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p class="form-success" style="color: #86bc42;font-size: 16px;font-style: italic;"></p>
			</div>
		</div>
	</form>
	
	<?php
