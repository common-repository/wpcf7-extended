<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function cf7e_add_shortcode_back() {
	if(function_exists('wpcf7_add_form_tag')) {
		wpcf7_add_form_tag( 'cf7e_back', 'cf7e_back_button_form_tag_handler' );
	} else {
		wpcf7_add_shortcode( 'cf7e_back', 'cf7e_back_button_form_tag_handler' );
	}
}

function cf7e_back_button_form_tag_handler( $tag ) {
	if ( version_compare(WPCF7_VERSION, "4.6", ">=") ) {
		$tag = new WPCF7_FormTag( $tag );
	} else {
		$tag = new WPCF7_Shortcode( $tag );
	}

	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class ) . " cf7e-back cf7e-btn cf7e-hide";
	$atts['id'] = $tag->get_option( 'id', 'id', true );
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
	$submit_button = $tag->has_option( 'submit_button' );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) )
		$value = __( 'Back', 'wpcf7-extended' );

	$atts['type'] = 'button';
	$atts['value'] = $value;

	$atts = wpcf7_format_atts( $atts );

	if( $submit_button ){
		$html = sprintf( '<button %1$s >'.$value.'</button>', $atts );	
	}else{
		$html = sprintf( '<input %1$s />', $atts );	
	}

	return $html;
}


/* Tag generator */

if(WPCF7_VERSION >= "4.2.0") {

	if ( is_admin() ) {
		add_action( 'admin_init', 'cf7e_add_tag_generator_back', 99 );
	}

	function cf7e_add_tag_generator_back() {
		if(!class_exists('WPCF7_TagGenerator')) {
			return false;
		}
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'cf7e_back', __( 'CF7E Back', 'wpcf7-extended' ),
			'cf7e_tg_pane_back', array( 'nameless' => 1 ) );
	}

	function cf7e_tg_pane_back( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );

		?>
		<div class="control-box">
			<fieldset>
				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row"><label
								for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Label', 'contact-form-7' ) ); ?></label>
						</th>
						<td><input type="text" name="values" class="oneline"
						           id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"/></td>
					</tr>

					<tr>
						<th scope="row"><label
								for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label>
						</th>
						<td><input type="text" name="id" class="idvalue oneline option"
						           id="<?php echo esc_attr( $args['content'] . '-id' ); ?>"/></td>
					</tr>

					<tr>
						<th scope="row"><label
								for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label>
						</th>
						<td><input type="text" name="class" class="classvalue oneline option"
						           id="<?php echo esc_attr( $args['content'] . '-class' ); ?>"/></td>
					</tr>

					<tr>
						<th scope="row"></th>
						<td>
							<label><input type="checkbox" name="submit_button" class="option" /> <?php echo esc_html( __( 'Tags submit is <button>', 'wpcf7-extended' ) ); ?></label><br />
						</td>
					</tr>

					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="cf7e_back" class="tag code" readonly="readonly" onfocus="this.select()"/>

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag"
				       value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>"/>
			</div>
		</div>
		<?php
	}

} else {

	add_action( 'admin_init', 'cf7e_add_tag_generator_back', 99 );

	function cf7e_add_tag_generator_back() {
		if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
			return;

		wpcf7_add_tag_generator( 'cf7e_back', __( 'Back button', 'wpcf7-extended' ),
			'wpcf7-tg-pane-back', 'cf7e_tg_pane_back', array( 'nameless' => 1 ) );
	}

	function cf7e_tg_pane_back( $contact_form ) {
	?>
	<div id="wpcf7-tg-pane-back" class="hidden"><?php echo esc_html( __( 'Support Contact Form 7 version 4.2.0 or higher', 'wpcf7-extended' ) ); ?></div>
	<?php
	}

}
?>