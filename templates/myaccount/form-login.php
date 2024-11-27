<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/user-registration/myaccount/form-login.php.
 *
 * HOWEVER, on occasion UserRegistration will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.wpuserregistration.com/docs/how-to-edit-user-registration-template-files-such-as-login-form/
 * @package UserRegistration/Templates
 * @version 1.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$form_template  = get_option( 'user_registration_login_options_form_template', 'default' );
$template_class = '';

if ( 'bordered' === $form_template ) {
	$template_class = 'ur-frontend-form--bordered';

} elseif ( 'flat' === $form_template ) {
	$template_class = 'ur-frontend-form--flat';

} elseif ( 'rounded' === $form_template ) {
	$template_class = 'ur-frontend-form--rounded';

} elseif ( 'rounded_edge' === $form_template ) {
	$template_class = 'ur-frontend-form--rounded ur-frontend-form--rounded-edge';
}

$labels       = array(
	'username'           => get_option( 'user_registration_label_username_or_email', __( 'Username or Email Address', 'user-registration' ) ),
	'password'           => get_option( 'user_registration_label_password', __( 'Password', 'user-registration' ) ),
	'remember_me'        => get_option( 'user_registration_label_remember_me', __( 'Remember Me', 'user-registration' ) ),
	'login'              => get_option( 'user_registration_label_login', __( 'Login', 'user-registration' ) ),
	'lost_your_password' => get_option( 'user_registration_label_lost_your_password', __( 'Lost your password?', 'user-registration' ) ),
);
$placeholders = array(
	'username' => get_option( 'user_registration_placeholder_username_or_email', '' ),
	'password' => get_option( 'user_registration_placeholder_password', '' ),
);
$hide_labels  = ur_option_checked( 'user_registration_login_options_hide_labels', false );

$enable_ajax = ur_option_checked( 'ur_login_ajax_submission', false );

$enable_field_icon = ur_option_checked( 'user_registration_pro_general_setting_login_form', false );

$login_title = ur_option_checked( 'user_registration_login_title', false );

$is_passwordless_enabled = ! ur_is_passwordless_login_enabled() || ! isset( $_GET['pl'] ) || ! ur_string_to_bool( $_GET['pl'] ); // phpcs:ignore;

?>

<?php
/**
 * Filter to modify the notice content before rendering of user registration login form.
 *
 * @param function Print notice function.
 * @return function.
 */
// apply_filters( 'user_registration_login_form_before_notice', ur_print_notices() );
?>

<?php
/**
 * Action to fire before the rendering of customer login form.
 */
do_action( 'user_registration_before_customer_login_form' );
?>

<div class="ur-frontend-form login <?php echo esc_attr( $template_class ); ?>" id="ur-frontend-form">

	<form class="user-registration-form user-registration-form-login login" method="post">
		<div class="ur-form-row">
			<div class="ur-form-grid">
				<?php
				if ( $login_title ) {
					$login_title_label =
					/**
					 * Filter to modify the login title.
					 *
					 * @param array $labels['login].
					 * @return array.
					 */
					apply_filters( 'ur_login_title', $labels['login'] );
					/* translators: %s - Login Title. */
					echo wp_kses_post( sprintf( __( '<span class="user-registration-login-title"> %s </span>', 'user-registration' ), $login_title_label ) );
				}
				?>
					<?php
					/**
					 * Action to fire at the start of rendering the login form.
					 */
					do_action( 'user_registration_login_form_start' );
					?>
					<p class="user-registration-form-row user-registration-form-row--wide form-row form-row-wide">
						<?php
						if ( ! $hide_labels ) {
							printf( '<label for="username">%s <span class="required">*</span></label>', esc_html( $labels['username'] ) );
						}
						?>
						<span class="input-wrapper">
						<input placeholder="<?php echo esc_attr( $placeholders['username'] ); ?>" type="text" class="user-registration-Input user-registration-Input--text input-text" name="username" id="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( sanitize_text_field( $_POST['username'] ) ) ) : ''; // phpcs:ignore ?>" style="<?php echo $enable_field_icon ? "padding-left: 32px !important" : '' ?>"/>
						<span class="ur-login-error-message">
							<?php
							apply_filters( 'user_registration_login_form_before_notice', ur_print_notices() );

							?>
						</span>
						<?php if (  $enable_field_icon ) { ?>
						<span class="ur-icon ur-icon-user-new">
						<svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
  <path d="M2.33537 2.83301H11.6687C12.3104 2.83301 12.8354 3.35801 12.8354 3.99967V10.9997C12.8354 11.6413 12.3104 12.1663 11.6687 12.1663H2.33537C1.6937 12.1663 1.1687 11.6413 1.1687 10.9997V3.99967C1.1687 3.35801 1.6937 2.83301 2.33537 2.83301Z" stroke="#858585" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M12.8356 4L7.00228 8.08333L1.16895 4" stroke="#858585" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
						</span>
						<?php } ?>
						</span>
					</p>
					<?php if ( $is_passwordless_enabled ) : ?>
					<p class="user-registration-form-row user-registration-form-row--wide form-row form-row-wide<?php echo ( ur_option_checked( 'user_registration_login_option_hide_show_password', false ) ) ? ' hide_show_password' : ''; ?>">
						<?php
						if ( ! $hide_labels ) {
							printf( '<label for="password">%s <span class="required">*</span></label>', esc_html( $labels['password'] ) );
						}
						?>
						<span class="input-wrapper">
						<span class="password-input-group">
						<input placeholder="<?php echo esc_attr( $placeholders['password'] ); ?>" class="user-registration-Input user-registration-Input--text input-text" type="password" name="password" id="password" style="<?php echo $enable_field_icon ? 'padding-left: 32px !important' : ''; ?>" />

						<?php
						if ( ur_option_checked( 'user_registration_login_option_hide_show_password', false ) ) {
							?>
						<a href="javaScript:void(0)" class="password_preview dashicons dashicons-hidden" title="<?php echo esc_attr__( 'Show password', 'user-registration' ); ?>"></a>
						</span>
							<?php
						}
						?>
						<?php if ( $enable_field_icon ) { ?>
						<span class="ur-icon ur-icon-password-new">
						<svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
  <path d="M11.0833 6.91797H2.91667C2.27233 6.91797 1.75 7.4403 1.75 8.08464V12.168C1.75 12.8123 2.27233 13.3346 2.91667 13.3346H11.0833C11.7277 13.3346 12.25 12.8123 12.25 12.168V8.08464C12.25 7.4403 11.7277 6.91797 11.0833 6.91797Z" stroke="#858585" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M4.08289 6.91772V4.58439C4.08289 3.81084 4.39018 3.06898 4.93716 2.522C5.48414 1.97502 6.226 1.66772 6.99955 1.66772C7.7731 1.66772 8.51497 1.97502 9.06195 2.522C9.60893 3.06898 9.91622 3.81084 9.91622 4.58439V6.91772" stroke="#858585" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
						</span>
						<?php } ?>
						</span>
					</p>
					<?php endif; ?>
					<?php
					if ( ! empty( $recaptcha_node ) ) {
						echo '<div id="ur-recaptcha-node"> ' . $recaptcha_node . '</div>';  //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>

					<?php
					/**
					 * Action to fire after rendering of user registration login form.
					 */
					do_action( 'user_registration_login_form' );
					?>

					<p class="form-row">
						<?php wp_nonce_field( 'user-registration-login', 'user-registration-login-nonce' ); ?>
						<div>
						<div class="user-registration-login-extra-section">
							<div class="ur-remember-me-section">
								<?php
								$remember_me_enabled = ur_option_checked( 'user_registration_login_options_remember_me', true );

								if ( $remember_me_enabled && $is_passwordless_enabled ) {
									?>
									<label class="user-registration-form__label user-registration-form__label-for-checkbox inline">
										<input class="user-registration-form__input user-registration-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php echo esc_html( $labels['remember_me'] ); ?></span>
									</label>
									<?php
								}
								?>
							</div>
							<div class="ur-lost-password-section">
								<?php
								$lost_password_enabled = ur_option_checked( 'user_registration_login_options_lost_password', true );

								if ( $lost_password_enabled && $is_passwordless_enabled ) {
									?>
									<p class="user-registration-LostPassword lost_password">
										<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php echo esc_html( $labels['lost_your_password'] ); ?></a>
									</p>
									<?php
								}
								?>
							</div>

						</div>
						<?php
						/**
						 * Action to fire before rendering of submit button for user registration login form.
						 */

						do_action( 'user_registration_login_form_before_submit_button' );
						?>
							<?php if ( $enable_ajax ) { ?>
							<input type="submit" class="user-registration-Button button ur-submit-button" id="user_registration_ajax_login_submit" name="login" value="<?php echo esc_html( $labels['login'] ); ?>" />
							<span></span>
							<?php } else { ?>
							<input type="submit" class="user-registration-Button button " name="login" value="<?php echo esc_html( $labels['login'] ); ?>" />
							<?php } ?>
						</div>
						<input type="hidden" name="redirect" value="<?php echo isset( $redirect ) ? esc_attr( $redirect ) : esc_attr( the_permalink() ); ?>" />

					</p>

					<?php
					$users_can_register = ur_option_checked( 'users_can_register', true );

					if ( $users_can_register ) {
						$url_options = get_option( 'user_registration_general_setting_registration_url_options' );

						if ( ! empty( $url_options ) ) {
							$url_pattern = "/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}(\\.[a-zA-Z0-9()]{1,6})?\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/";
							if ( ! filter_var( $url_options, FILTER_VALIDATE_URL ) || ! preg_match( $url_pattern, $url_options ) ) {
								$url_options = home_url( $url_options );
							}
							echo '<p class="user-registration-register register">';
							$label = get_option( 'user_registration_general_setting_registration_label' );

							if ( ! empty( $label ) ) {
								?>
								<a href="<?php echo esc_url( $url_options ); ?>"> <?php echo esc_html( get_option( 'user_registration_general_setting_registration_label' ) ); ?>
									</a>
								<?php
							} else {
								update_option( 'user_registration_general_setting_registration_label', __( 'Not a member yet? Register now.', 'user-registration' ) );
								?>
									<a href="<?php echo esc_url( $url_options ); ?>"> <?php echo esc_html( get_option( 'user_registration_general_setting_registration_label' ) ); ?>
									</a>
								<?php
							}
							echo '</p>';
						}
					}
					?>
					<?php
					/**
					 * Action to fire at the end of rendering the user registration form.
					 */
					do_action( 'user_registration_login_form_end' );
					?>
			</div>
		</div>
	</form>

</div>

<?php
/**
 * Action to fire after the rendering of user registration login form.
 */
do_action( 'user_registration_after_login_form' ); ?>
