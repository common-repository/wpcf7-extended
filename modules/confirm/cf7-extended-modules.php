<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	require_once CF7E_PLUGIN_DIR . '/modules/confirm/cf7-extended-add-confirm.php';
	require_once CF7E_PLUGIN_DIR . '/modules/confirm/cf7-extended-add-back.php';
	require_once CF7E_PLUGIN_DIR . '/modules/confirm/cf7-extended-add-thanks.php';

	/* Shortcode handler */
	add_action( 'wpcf7_init', 'cf7e_add_shortcode_confirm' );
	add_action( 'wpcf7_init', 'cf7e_add_shortcode_back' );
	add_action( 'wpcf7_init', 'cf7e_add_shortcode_thanks' );

	add_action( 'init', 'cf7e_extended_modules_confirm_init', 10 );
	function cf7e_extended_modules_confirm_init() {		

		if ( isset( $_POST['_cf7e'] ) && sanitize_text_field($_POST["_cf7e"])  == 'confirm' ) {
			remove_filter( 'wpcf7_refill_response', 'wpcf7_quiz_ajax_refill' );
			remove_filter( 'wpcf7_feedback_response', 'wpcf7_quiz_ajax_refill' );
		}

		remove_filter( 'wpcf7_validate_captchar', 'wpcf7_captcha_validation_filter', 10 );
		add_filter( 'wpcf7_validate_captchar', 'cf7e_captcha_validation_filter', 10, 9 );

		add_action( 'wp_head', 'cf7e_enqueue_styles_inline', 10, 9999 );
		add_action( 'wp_footer', 'cf7e_enqueue_scripts_inline', 10, 9999 );

		do_action( 'cf7e_confirm');
	}

	add_action( 'cf7e_confirm', 'cf7e_confirm_func', 10, 1 );
	function cf7e_confirm_func(){
		if ( isset( $_POST['_cf7e'] ) ){
			if ( sanitize_text_field($_POST["_cf7e"]) == 'confirm' ) {
				add_filter( "wpcf7_skip_mail", '__return_true', 10, 9 );
				add_filter( "wpcf7_ajax_json_echo", "cf7e_ajax_json_echo", 10, 9 );
			}else if( sanitize_text_field($_POST["_cf7e"]) == 'confirm_send' ){
				add_filter( "wpcf7_ajax_json_echo", "cf7e_ajax_json_echo_send", 10, 9 );
			}
		}	
	}

	function cf7e_ajax_json_echo( $items, $result ) {
		if($items['status'] == 'mail_sent' ) {
			$items["message"]  = "";
			$items["mailSent"] = false;
			$items["status"] = 'cf7e_confirm';
			unset( $items['captcha'] );
		}
		return $items;
	}

	function cf7e_ajax_json_echo_send( $items, $result ) {
		return $items;
	}

	function cf7e_captcha_validation_filter( $result, $tag ) {
		$type = $tag->type;
		$name = $tag->name;

		$captchac = '_wpcf7_captcha_challenge_' . $name;

		$prefix = isset( $_POST[$captchac] ) ? sanitize_text_field( $_POST[$captchac] ) : '';
		$response = isset( $_POST[$name] ) ? sanitize_text_field( $_POST[$name] ) : '';
		$response = wpcf7_canonicalize( $response );

		if ( 0 === strlen( $prefix )
		or ! wpcf7_check_captcha( $prefix, $response ) ) {
			$result->invalidate( $tag, wpcf7_get_message( 'captcha_not_match' ) );
		}

		if ( 0 !== strlen( $prefix ) || sanitize_text_field($_POST["_cf7e"]) == "confirm_send" ) {
			wpcf7_remove_captcha( $prefix );
		}

		return $result;
	}

	function cf7e_enqueue_styles_inline(){
	?>
		<style>
			.cf7e-hide, p input[name=_cf7e][value=confirm] ~ .cf7e-back, p input[name=_cf7e][value=confirm] ~ .wpcf7-submit{
				display: none !important;
			}
			form[data-status=custom-cf7e-confirm] .cf7e-confirm{
				display: none !important;
			}
			form[data-status=custom-cf7e-confirm] .wpcf7-response-output{ display: none !important; }
			.custom-cf7e-confirm input.wpcf7-form-control:not(.cf7e-btn, .wpcf7-submit),
			.custom-cf7e-confirm input.wpcf7-form-control:not(.cf7e-btn, .wpcf7-submit):focus,
			.custom-cf7e-confirm textarea,
			.custom-cf7e-confirm textarea:focus{
				border: none;
				outline: none;
    			outline-offset: unset;
			}
			form[data-status=custom-cf7e-confirm] .cf7e-back, form[data-status=custom-cf7e-confirm] .wpcf7-submit{
				display: block !important;
			}

			.custom-cf7e-confirm input:-webkit-autofill,
			.custom-cf7e-confirm input:-webkit-autofill:hover, 
			.custom-cf7e-confirm input:-webkit-autofill:focus,
			.custom-cf7e-confirm textarea:-webkit-autofill,
			.custom-cf7e-confirm textarea:-webkit-autofill:hover,
			.custom-cf7e-confirm textarea:-webkit-autofill:focus{
			 	border: none;
			  	-webkit-text-fill-color: #000;
			  	-webkit-box-shadow: 0 0 0px 1000px #fff inset;
			  	transition: background-color 5000s ease-in-out 0s;
			}
		</style>
	<?php
	}

	function cf7e_enqueue_scripts_inline(){
	?>
		<script>
			if( document.querySelector('.cf7e-confirm') == null && document.querySelector('.wpcf7-submit') != null ){
				document.querySelector('.wpcf7-submit').classList.remove('cf7e-hide');
			}
			
			document.addEventListener( 'wpcf7submit', function( event ) {
				switch ( event.detail.status ) {
					case 'cf7e_confirm':
						cf7e_confirm(event.detail.unitTag);
						break;
					case 'mail_sent':
						cf7e_confirm_send(event.detail.unitTag);
						break;
				}
			}, false );

			var cf7e_confirm = function(unit_tag) {

				var this_form = window.document.getElementById(unit_tag);
				var all_field = this_form.querySelectorAll('input[type=text], input[type=email], textarea, input[type=url], input[type=tel], input[type=number], input[type=date], input[type=radio], input[type=checkbox]');
				var field_disabled = this_form.querySelectorAll('select, input[type=radio], input[type=checkbox], input[type=range]');

				/* To Confirm */
				all_field.forEach( x=> x.setAttribute("readonly",true));
				field_disabled.forEach((x) => {
					if( x.nodeName.toLowerCase() == 'input' ){						
						if( x.checked ){
							var e = document.createElement('input');
							var papa = x.parentElement;
							e.value = x.value;
							e.setAttribute('name' , x.getAttribute('name'));
							e.setAttribute('type' , 'hidden');
							papa.appendChild(e);
						}
					}else if( x.nodeName.toLowerCase() == 'select' && x.multiple ){
						var e = document.createElement('input');
						var papa = x.parentElement;
						var all_v = x.querySelectorAll('option:checked');
						e.value = Array.from(all_v).map(el => el.value);
						e.setAttribute('name' , x.getAttribute('name'));
						e.setAttribute('type' , 'hidden');
						papa.appendChild(e);
					}else{
						var e = document.createElement('input');
						var papa = x.parentElement;
						e.value = x.value;
						e.setAttribute('name' , x.getAttribute('name'));
						e.setAttribute('type' , 'hidden');
						papa.appendChild(e);
					}
					

					x.setAttribute("disabled",true);
				});
				this_form.querySelectorAll('input[type=file]').forEach((x) => {
					var e = document.createElement('input');
					var papa = x.parentElement;

					var fullPath = x.value;
					if (fullPath) {
					    var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
					    var filename = fullPath.substring(startIndex);
					    if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
					        filename = filename.substring(1);
					    }
					    e.value  = filename;
					}else{
						e.value = fullPath;	
					}					
					e.setAttribute('name' , x.getAttribute('name') + '_cf7e');
					e.setAttribute('type' , 'text');
					e.setAttribute("disabled",true);
					e.classList.add('wpcf7-form-control');
					papa.appendChild(e);

					x.setAttribute("readonly",true);
					x.classList.add('wpcf7-response-output');
				});

				this_form.querySelector('input[name=_cf7e]').setAttribute('value', 'confirm_send');
				
				cf7e_scroll_form(this_form);

				/* To Back */
				this_form.querySelector('.cf7e-back').addEventListener('click', function( event ){
					all_field.forEach( x=> x.removeAttribute("readonly"));
					field_disabled.forEach((x) => {
						x.removeAttribute("disabled");
						var papa = x.parentElement;
						if( papa.querySelector('input[type=hidden]') != null ){
							papa.querySelector('input[type=hidden]').remove();	
						}
					});
					this_form.querySelectorAll('input[type=file]').forEach((x) => {
						x.removeAttribute("readonly");
						x.classList.remove('wpcf7-response-output');
						var papa = x.parentElement;
						papa.querySelector('input[type=text]').remove();
					});

					this_form.querySelector('input[name=_cf7e]').setAttribute('value', 'confirm');
					this_form.querySelector('form.wpcf7-form').classList.remove('custom-cf7e-confirm');
					this_form.querySelector('form.wpcf7-form').setAttribute('data-status', 'init');
					this_form.querySelector('form.wpcf7-form').classList.add('init');
					cf7e_scroll_form(this_form);
				});
			}

			var cf7e_confirm_send = function(unit_tag){
				var this_form = window.document.getElementById(unit_tag);
				if( this_form.querySelector('input[name=cf7e_thanks]') ){
					window.location.href = this_form.querySelector('input[name=cf7e_thanks]').value;
				}
			}

			var cf7e_scroll_form = function(unit_tag) {
				let anchor;				
				if( window.document.querySelector('input[name=_cf7e_anchorID]') != null ){
					anchor = window.document.getElementById( window.document.querySelector('input[name=_cf7e_anchorID]').value );
				}else{
					anchor = unit_tag;
				}
				let position = cf7e_offset_element(anchor).top - 100;
				
				if( cf7e_check_ie() == false ){
					window.scrollTo({ top: position, behavior: 'smooth' });	
				}else{
					jQuery("html, body").animate({scrollTop:position}, 1000, "swing");	
				}
			}

			var cf7e_check_ie = function() {
			    var ua = window.navigator.userAgent;
			    var msie = ua.indexOf("MSIE ");
			    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)){
			        return true;
			    }
			    return false;
			}

			var cf7e_offset_element = function (el){
			    var rect = el.getBoundingClientRect(),
			    scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
			    scrollTop = window.pageYOffset || document.documentElement.scrollTop;
			    return { top: rect.top + scrollTop, left: rect.left + scrollLeft }
			}

		</script>
	<?php
	}
?>