<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function cf7e_add_shortcode_confirm() {
	if(function_exists('wpcf7_add_form_tag')) {
		wpcf7_add_form_tag( 'cf7e_confirm', 'cf7e_confirm_button_form_tag_handler' );
	} else {
		wpcf7_add_shortcode( 'cf7e_confirm', 'cf7e_confirm_button_form_tag_handler' );
	}
}

function cf7e_confirm_button_form_tag_handler( $tag ) {
	if ( version_compare(WPCF7_VERSION, "4.6", ">=") ) {
		$tag = new WPCF7_FormTag( $tag );
	} else {
		$tag = new WPCF7_Shortcode( $tag );
	}

	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();
	
	$atts['class'] = $tag->get_class_option( $class ) . " cf7e-confirm cf7e-btn";
	$atts['id'] = $tag->get_option( 'id', 'id', true );
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
	$submit_button = $tag->has_option( 'submit_button' );	

	if ( !isset( $tag->values[0] ) || $tag->values[0] == '' ){
		$atts['value'] = __( 'Confirm', 'wpcf7-extended' );
	}else{
		$atts['value'] = $tag->values[0];
	}
	$value = $atts['value'];

	$atts['type'] = 'submit';

	$atts = wpcf7_format_atts( $atts );

	if( $submit_button ){
		$html = sprintf( '<button %1$s >'.$value.'</button>', $atts );	
	}else{
		$html = sprintf( '<input %1$s />', $atts );	
	}

	if( $tag->has_option( 'anchorID' ) ){
		$html = '<input type="hidden" name="_cf7e_anchorID" value="'. strval($tag->get_option( 'anchorID' )[0]) .'">' . $html;
	}

	$html = '<input type="hidden" name="_cf7e" value="confirm">' . $html;

	return $html;
}


/* Tag generator */

if(WPCF7_VERSION >= "4.2.0") {

	if ( is_admin() ) {
		add_action( 'admin_init', 'cf7e_add_tag_generator_confirm', 99 );
	}

	function cf7e_add_tag_generator_confirm() {
		if(!class_exists('WPCF7_TagGenerator')) {
			return false;
		}
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'cf7e_confirm', __( 'CF7E Confirm', 'wpcf7-extended' ),
			'cf7e_tg_pane_confirm', array( 'nameless' => 1 ) );
	}


	function cf7e_tg_pane_confirm( $contact_form, $args = '' ) {
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
						<td>
							<input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"/>			
						</td>
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

					<tr>
						<th scope="row"><label
								for="<?php echo esc_attr( $args['content'] . '-anchorID' ); ?>"><?php echo esc_html( __( 'Anchor ID', 'wpcf7-extended' ) ); ?></label>
						</th>
						<td><input type="text" name="anchorID" class="anchorID oneline option"
						           id="<?php echo esc_attr( $args['content'] . '-anchorID' ); ?>"/>
						           <br><i><?php echo __( 'Scroll the screen to the desired ID.<br>Default will scroll the screen to the form ID', 'wpcf7-extended' ); ?></i></td>
					</tr>

					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="cf7e_confirm" class="tag code" readonly="readonly" onfocus="this.select()"/>

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag"
				       value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>"/>
			</div>
		</div>
		<?php
	}


} else {
	add_action( 'admin_init', 'cf7e_add_tag_generator_confirm', 55 );

	function cf7e_add_tag_generator_confirm() {
		if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
			return;
		wpcf7_add_tag_generator( 'cf7e_confirm', __( 'Confirm button', 'wpcf7-extended' ),
			'wpcf7-tg-pane-confirm', 'cf7e_tg_pane_confirm', array( 'nameless' => 1 ) );
	}

	function cf7e_tg_pane_confirm( $contact_form ) {
	?>
	<div id="wpcf7-tg-pane-confirm" class="hidden"><?php echo esc_html( __( 'Support Contact Form 7 version 4.2.0 or higher', 'wpcf7-extended' ) ); ?></div>
	<?php
	}

}
?>