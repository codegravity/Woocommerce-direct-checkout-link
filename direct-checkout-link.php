<?php
if ( !defined( 'ABSPATH' ) ) exit;

// --------- direck link to checkout -----------  //
//  A direct marketing link emailed to the the client, which opens the checkout page with a preset product and client info pre-filled.
//  https://www.your-domain.se/kassan/?add-to-cart=9522&direct_checkout=1&id=6945490&epost=client@client-domain.se.se&clientFirstName=Kalle&clientLastName=Svensson&clientPhone=0123456789
//   Add to theme functions.php file.
  

if(isset($_GET['direct_checkout'])) {

		add_filter( 'woocommerce_is_sold_individually', '__return_true' );  // Restrict checkout to single product in link.
    
		add_filter( 'woocommerce_checkout_fields', 'cg_direct_checkout_fields' ); // Fill the checkout form with the link data.
		function cg_direct_checkout_fields( $fields ) {

			if(isset($_GET['id'])) {      // client id is an id in google script-based admin system client ID in this case, not the wordpress or Woocommerce id.
				$gooID = $_GET['id'];
         /* option 1   via comments field  */
        $fields['order']['order_comments']['default'] = $gooID;   // Add the client ID to the WooCommerce checkout page comments field.
		    $fields['order']['order_comments']['type'] = 'hidden';    // Hide the comments field
			  $fields['order']['order_comments']['label'] = '';         //  Remove the label for the hidden comments field.
        /* Option 2 via custom field is implmented after the standard fields are filled. */
			}		
      
			if(isset($_GET['epost'])) {
				$gooEpost = $_GET['epost'];
        $fields['billing']['billing_email']['default'] = $gooEpost;  // Add the client epost to the checkout form.
			}
      
			if(isset($_GET['clientFirstName'])) {
				$gooFirstName = $_GET['clientFirstName'];
        $fields['billing']['billing_first_name']['default'] = $gooFirstName;  // Add the client first name to the checkout form.
			}
      
			if(isset($_GET['clientLastName'])) {
				$gooLastName = $_GET['clientLastName'];
        $fields['billing']['billing_last_name']['default'] = $gooLastName;  Add the client last name to the checkout form.
			}
      
			if(isset($_GET['clientPhone'])) {
				$gooPhone = $_GET['clientPhone'];
        $fields['billing']['billing_phone']['default'] = $gooPhone; // Add the clients telephone to the checkout form.
			}
   	
		  return $fields;		
		}

		/*   ---- Option:  copy mobile number from telephone field to Swish  with jQuery
		add_action( 'wp_enqueue_scripts', 'cg_add_mobil_2_swish' );
		function cg_add_mobil_2_swish() {
    		wp_register_script('cg_load_swish', get_stylesheet_directory_uri() . '/js/cg_load_swish.js', false, null, true);
    		wp_enqueue_script('cg_load_swish'); 
		}
		*/	
	/* --- Option 2 client id: Add client id field to checkout. Custom fields are supported by the Swish-WooCommerce plugin, 
         but other gateways have varying levels of support for custom fields.  --- */	
         
		add_action('woocommerce_after_order_notes', 'clientid_checkout_field');
		function clientid_checkout_field($checkout)
		{
		  echo '<div id="clientid_checkout_field">';
		  woocommerce_form_field('clientid_field', array(
			//'type' => 'text',  **** choose visible or hidden field ****
			 'type' => 'hidden', 
			'class' => array(
			  'cg-clientid-field form-row-wide'
			) ,
		//	'label' => __('Befintlig kund id') ,  **** choose visible or hidden field ****
			'required' => true,
			'readonly'=>'readonly',
			'default' => ($_GET['id'])
		  ) , $checkout->get_value('clientid_field'));
		  echo '</div>';
		}
	/* --- END add cliend id field to checkout --- */	
}else{	
	// ----------- standard direct product to checkout (no cart) ------------ //
	add_filter ('woocommerce_add_to_cart_redirect', 'redirect_to_checkout');
	function redirect_to_checkout() {
    	global $woocommerce;	
		$checkout_url = '/kassan/';	
		return $checkout_url;		
	}
}

/***  Add the custin field for cliend ID to the order meta. ***/
add_action( 'woocommerce_checkout_update_order_meta', 'cg_clientid_field_update_order_meta' );  
function cg_clientid_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['clientid_field'] ) ) {
        update_post_meta( $order_id, 'clientid_field', sanitize_text_field( $_POST['clientid_field'] ) );
    }
}
/*** Display kund id on the orders page ****/
add_action( 'woocommerce_admin_order_data_after_billing_address', 'cg_clientid_checkout_field_display_admin_order_meta', 10, 1 );
function cg_clientid_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Kund ID').':</strong> <br/>' . get_post_meta( $order->get_id(), 'clientid_field', true ) . '</p>';
}

/*** Allow only 1 element in the cart.  ***/
add_filter( 'woocommerce_add_cart_item_data', function ( $cart_item_data ) {
	global $woocommerce;
	$woocommerce->cart->empty_cart();
	return $cart_item_data;
} );

