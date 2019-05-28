<?php

//user side - my account coupons page
function global_coupons_my_account_page() {
    $coupons = global_coupons_get_all_global_coupons();
    $content = "<table id='customers'>";
    $content .= "<tr><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th><th>Comment/Condition</th><th>Status</th><th>Apply Coupon</th></tr>";
    foreach($coupons as $coupon)
    {
        $coupon_id = $coupon->ID;
        $coupon_name = $coupon->post_title;
        $coupon_amount = $coupon->coupon_amount;
        $coupon_description = $coupon->post_excerpt;
        $coupon_type = "";
        if($coupon->discount_type == "fixed_cart") $coupon_type = "Fixed Cart Discount";
        else $coupon_type = "Percentage Discount";
        
        //checking if the current page is admin panel or my account page
        $isPreview = false;
        global $wp;
        $getSlug = add_query_arg( array(), $wp->request );
        $slugArray = explode('/', $getSlug);
        if($slugArray[1] != "my-global-coupons") $isPreview = true;
        
        //wc cart must be used only in my account page
        if(!$isPreview)
        {
            //apply coupon button text
            if( WC()->cart->get_cart_contents_count() == 0 )
            {
                $coupon_apply_text = "Empty Cart";
            }
            else
            {
                $coupon_apply_text = $coupon_name;
            }
        }
        //if it is admin panel, show preview in the button text
        else
        {
            $coupon_apply_text = "preview";
        }
        
        //update all predefined global coupons
        if($coupon_description!="" && $coupon_description=="Discount For First Order") global_coupons_first_order($coupon_id);
        elseif($coupon_description != "" && strpos($coupon_description, 'At least') !== false)
        {
            $howMany = substr($coupon_description, 9);
            $strArray = explode('p', $howMany);
            $howMany = intval($strArray[0]); 
            global_coupons_necessary_reviews($coupon_id, $howMany ); 
        }
        elseif($coupon_description != "" && strpos($coupon_description, 'Available: ') !== false)
        {
            $dateInterval = substr($coupon_description, 11);
            $dateInterval = str_replace(' ', '', $dateInterval);
            global_coupons_activate_on_dates($coupon_id, $dateInterval);
        }
        elseif($coupon_description != "" && strpos($coupon_description, 'Required number of orders: ') !== false)
        {
            $requiredNumberOfOrders = substr($coupon_description, 27);
            global_coupons_number_of_orders($coupon_id, $requiredNumberOfOrders);
        }
        elseif($coupon_description != "" && strpos($coupon_description, 'Total amount of orders: ') !== false)
        {
            $requiredAmountOfOrders = substr($coupon_description, 24);
            global_coupons_amount_of_orders($coupon_id, $requiredAmountOfOrders);
            
            //add currency symbol to the start of coupon description without changing the original excerpt
            $coupon_description = "Total amount of orders: " . get_woocommerce_currency_symbol() . $requiredAmountOfOrders ;
        }
        
        //check activeness for this specific user
        $isActive = false;
        $this_customer_id = get_current_user_id();
        $user = get_current_user();
        $user_info = get_userdata($this_customer_id);
        $customer_email = $user_info->user_email;
        if($coupon->customer_email != "")
        {
            if(in_array( $customer_email , $coupon->customer_email )) 
            {
                $isActive = true;
            }
        }
        else
        {
            $isActive = true;
        }
        if($coupon_description!="" || current_user_can('administrator'))
        {
            if($isActive)
            {
                if($coupon_type == "Percentage Discount") $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($coupon_type) . " </td> " . "<td> " . esc_html($coupon_amount) . "%</td>" . "<td> " . esc_html($coupon_description) ."</td>" . "<td style='color:green'>Active</td><td><form action='' method='post'><button class='de-button de-button-anim-1' type='submit' value='".esc_html($coupon_apply_text)."' name='appliedCoupon'>".esc_html($coupon_apply_text)."</button></form></td>";
                else $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($coupon_type) . " </td> " . "<td> " . get_woocommerce_currency_symbol() . esc_html($coupon_amount) . "</td>" . "<td> " . esc_html($coupon_description) ."</td>" . "<td style='color:green'>Active</td><td><form action='' method='post'><button class='de-button de-button-anim-1' type='submit' value='".esc_html($coupon_apply_text)."' name='appliedCoupon'>".esc_html($coupon_apply_text)."</button></form></td>"; 
                if($_SERVER['REQUEST_METHOD'] === 'POST' && $coupon_apply_text != "preview" && $coupon_apply_text != "Empty Cart")
                {
                    $applyCode = $_POST['appliedCoupon'];
                    if(!WC()->cart->get_applied_coupons())
                    {
                        WC()->cart->add_discount( $applyCode );
                        $cartURL = WC()->cart->get_cart_url();
                        echo '<script type="text/javascript">window.location = "'.$cartURL.'"</script>';
                    }
                }
            }
            elseif(!($coupon_description == 'Special Discount For You')){
                $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($coupon_type) . " </td> " . "<td> " . get_woocommerce_currency_symbol() . esc_html($coupon_amount) . "</td>" . "<td> " . esc_html($coupon_description) . "</td>" . "<td style='color:red'>Deactive</td>";
            }
        }
    }
    $content .= "</table>";
    return $content;
}

//shortcode for my account page
add_shortcode('my-account-global-coupons', 'global_coupons_my_account_page');

//my global coupons/coupons page for my account - start
function global_coupons_tab_endpoint() {
    add_rewrite_endpoint( 'my-global-coupons', EP_ROOT | EP_PAGES );
}
  
add_action( 'init', 'global_coupons_tab_endpoint' );
  
function global_coupons_tab_query_vars( $vars ) {
    $vars[] = 'my-global-coupons';
    return $vars;
}
  
add_filter( 'query_vars', 'global_coupons_tab_query_vars', 0 );
  
function global_coupons_tab_my_account( $items ) {
    $items['my-global-coupons'] = 'Coupons';
    return $items;
}
  
add_filter( 'woocommerce_account_menu_items', 'global_coupons_tab_my_account' );
  
function global_coupons_tab_content() {
    echo do_shortcode('[my-account-global-coupons]');
}

function global_coupons_tab_rewrite_rules() {
    flush_rewrite_rules();
}

add_action( 'wp_loaded', 'global_coupons_tab_rewrite_rules' );

add_action( 'woocommerce_account_my-global-coupons_endpoint', 'global_coupons_tab_content' );

//my global coupons/coupons page for my account - end

//my account page re-order tabs
function global_coupons_my_account_menu_order() {
 	$menuOrder = array(
 	    'dashboard'          => __( 'Dashboard', 'woocommerce' ),
 	    'my-global-coupons' => __( 'Coupons', 'woocommerce' ),
 		'orders'             => __( 'Orders', 'woocommerce' ),
 		'edit-address'       => __( 'Addresses', 'woocommerce' ),
 		'edit-account'    	=> __( 'Account Details', 'woocommerce' ),
 		'customer-logout'    => __( 'Logout', 'woocommerce' ),
 	);
 	return $menuOrder;
 }
add_filter ( 'woocommerce_account_menu_items', 'global_coupons_my_account_menu_order' );

?>
