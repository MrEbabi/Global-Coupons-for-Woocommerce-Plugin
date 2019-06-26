<?php

//user side - my account coupons page
function global_coupons_my_account_page() {
    $anyGC = global_coupons_count_all_global_coupons();
    $frontend_settings = get_option('global_coupons');
    if($anyGC > 0)
    {
        $coupons = global_coupons_get_all_global_coupons();
        $content = "<table id='customers'>";
        $content .= "<tr style='background-color:".$frontend_settings['th_bg']."; color:".$frontend_settings['th_text']."'><th>".$frontend_settings['coupon_code']."</th><th>".$frontend_settings['coupon_type']."</th><th>".$frontend_settings['coupon_amount']."</th><th>".$frontend_settings['coupon_restriction']."</th><th>".$frontend_settings['coupon_status']."</th><th>".$frontend_settings['coupon_apply']."</th><th>".$frontend_settings['you_have']."</th></tr>";
        foreach($coupons as $coupon)
        {
            $coupon_id = $coupon->ID;
            $coupon_name = $coupon->post_title;
            $coupon_amount = $coupon->coupon_amount;
            $coupon_description = $coupon->post_excerpt;
            $coupon_type = "";
            $coupon_condition = "";
            $coupon_condition_for_user = " - ";
            $coupon_activeness = "";
            $color_activeness = "";
            
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
                    $coupon_apply_text = $frontend_settings['empty_cart'];
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
            if($coupon_description!="" && $coupon_description=="Discount For First Order")
            {
                global_coupons_first_order($coupon_id);
                $coupon_condition = $frontend_settings['first_order'];
                $count = global_coupons_get_number_of_orders();
                $coupon_condition_for_user = $frontend_settings['currently_orders']. ": " .$count;
            }
            elseif($coupon_description != "" && strpos($coupon_description, 'At least') !== false)
            {
                $howMany = substr($coupon_description, 9);
                $strArray = explode('p', $howMany);
                $howMany = intval($strArray[0]); 
                global_coupons_necessary_reviews($coupon_id, $howMany ); 
                $coupon_condition = $frontend_settings['number_of_reviews'] . ": " . $howMany;
                $count = global_coupons_get_number_of_reviews();
                $coupon_condition_for_user = $frontend_settings['currently_reviews']. ": " .$count;
            }
            elseif($coupon_description != "" && strpos($coupon_description, 'Available: ') !== false)
            {
                $dateInterval = substr($coupon_description, 11);
                $dateInterval = str_replace(' ', '', $dateInterval);
                global_coupons_activate_on_dates($coupon_id, $dateInterval);
                $coupon_condition = $frontend_settings['date_interval'] . ": " . $dateInterval;
            }
            elseif($coupon_description != "" && strpos($coupon_description, 'Required number of orders: ') !== false)
            {
                $requiredNumberOfOrders = substr($coupon_description, 27);
                global_coupons_number_of_orders($coupon_id, $requiredNumberOfOrders);
                $coupon_condition = $frontend_settings['number_of_orders'] . ": " . $requiredNumberOfOrders;
                $count = global_coupons_get_number_of_orders();
                $coupon_condition_for_user = $frontend_settings['currently_orders']. ": " .$count;
            }
            elseif($coupon_description != "" && strpos($coupon_description, 'Total amount of orders: ') !== false)
            {
                $requiredAmountOfOrders = substr($coupon_description, 24);
                global_coupons_amount_of_orders($coupon_id, $requiredAmountOfOrders);
                $coupon_condition = $frontend_settings['amount_of_orders'] . ": " . get_woocommerce_currency_symbol() . $requiredAmountOfOrders;
                $count = global_coupons_get_amount_of_orders();
                $coupon_condition_for_user = $frontend_settings['currently_amount']. ": " .get_woocommerce_currency_symbol() . $count;
                
                //add currency symbol to the start of coupon description without changing the original excerpt
                $coupon_description = "Total amount of orders: " . get_woocommerce_currency_symbol() . $requiredAmountOfOrders ;
            }
            elseif($coupon_description != "" && strpos($coupon_description, 'Special Discount For You') !== false)
            {
                $coupon_condition = "Special Discount For You";
            }
            
            //check activeness for this specific user
            $isActive = false;
            $this_customer_id = get_current_user_id();
            $user = get_current_user();
            $user_info = get_userdata($this_customer_id);
            $customer_email = $user_info->user_email;
            if($coupon->customer_email != "")
            {
                if(in_array( $customer_email , $coupon->customer_email ) && ( !global_coupons_get_customer_used($coupon) )) 
                {
                    $isActive = true;
                    $coupon_activeness = $frontend_settings['active'];
                    $color_activeness = "green";
                }
                elseif(global_coupons_get_customer_used($coupon))
                {
                    $coupon_activeness = $frontend_settings['used'];
                    $color_activeness = "blue";
                }
                else
                {
                    $coupon_activeness = $frontend_settings['deactive'];
                    $color_activeness = "red";
                }
            }
            elseif(strpos($coupon_description, 'Available: ') !== false)
            {
                $isActive = true;
                $coupon_activeness = $frontend_settings['active'];
                $color_activeness = "green";
            }
            if($coupon_description!="" || current_user_can('administrator'))
            {
                if($isActive)
                {
                    if($coupon_type == "Percentage Discount") $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($frontend_settings['percentage']) . " </td> " . "<td> " . esc_html($coupon_amount) . "%</td>" . "<td> " . esc_html($coupon_condition) ."</td>" . "<td style='color:".esc_html($color_activeness)."'>".esc_html($coupon_activeness)."</td><td><form action='' method='post'><button class='de-button de-button-anim-1' type='submit' value='".esc_html($coupon_apply_text)."' name='appliedCoupon'>".esc_html($coupon_apply_text)."</button></form></td><td>".esc_html($coupon_condition_for_user)."</td></tr>";
                    else $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($frontend_settings['fixed_cart']) . " </td> " . "<td>" . get_woocommerce_currency_symbol() . esc_html($coupon_amount) . "</td>" . "<td> " . esc_html($coupon_condition) ."</td>" . "<td style='color:".esc_html($color_activeness)."'>".esc_html($coupon_activeness)."</td><td><form action='' method='post'><button class='de-button de-button-anim-1' type='submit' value='".esc_html($coupon_apply_text)."' name='appliedCoupon'>".esc_html($coupon_apply_text)."</button></form></td><td>".esc_html($coupon_condition_for_user)."</td></tr>"; 
                    if($_SERVER['REQUEST_METHOD'] === 'POST' && $coupon_apply_text != "preview" && $coupon_apply_text != $frontend_settings['empty_cart'])
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
                elseif(!($coupon_description == 'Special Discount For You'))
                {
                    $coupon_apply_text = "Disabled";
                    if($coupon_type == "Percentage Discount") $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($frontend_settings['percentage']) . " </td> " . "<td> " . esc_html($coupon_amount) . "%</td>" . "<td> " . esc_html($coupon_condition) . "</td>" . "<td style='color:".esc_html($color_activeness)."'>".esc_html($coupon_activeness)."</td><td><button class='de-button de-button-anim-11' type='submit' value='".esc_html($coupon_apply_text)."' name=''>".esc_html($coupon_apply_text)."</button></td><td>".esc_html($coupon_condition_for_user)."</td></tr>";
                    else $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($frontend_settings['fixed_cart']) . " </td> " . "<td> " . get_woocommerce_currency_symbol() . esc_html($coupon_amount) . "</td>" . "<td> " . esc_html($coupon_condition) . "</td>" . "<td style='color:".esc_html($color_activeness)."'>".esc_html($coupon_activeness)."</td><td><button class='de-button de-button-anim-11' type='submit' value='".esc_html($coupon_apply_text)."' name=''>".esc_html($coupon_apply_text)."</button></td><td>".esc_html($coupon_condition_for_user)."</td></tr>";
                }
            }
        }
        $content .= "</table>";
        return $content;
    }
    else
    {
        $content = "<center>".esc_html($frontend_settings['no_coupons_found'])."</center>";
        return $content;
    }
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
    global_coupons_menu_tab_names();
    $menu_names = get_option("global_coupons_menu");
 	$menuOrder = array(
 	    'dashboard'          => $menu_names['dashboard_1'],
 	    'my-global-coupons' => $menu_names['coupons_2'],
 		'orders'             => $menu_names['orders_3'],
 		'edit-address'       => $menu_names['address_4'],
 		'edit-account'    	=> $menu_names['account_5'],
 		'customer-logout'    => $menu_names['logout_6'],
 	);
 	return $menuOrder;
 }
add_filter ( 'woocommerce_account_menu_items', 'global_coupons_my_account_menu_order' );

?>
