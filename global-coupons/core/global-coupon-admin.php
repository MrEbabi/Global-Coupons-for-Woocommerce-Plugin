<?php

//adding admin menu
add_action( 'admin_menu', 'global_coupons_admin_page' );

//admin menu function
function global_coupons_admin_page() {
	add_menu_page( 'Global Coupons', 'Global Coupons', 'manage_options' , 'global-coupons-admin-page' , 'global_coupons_admin_mainmenu', '' , '53');
	add_submenu_page('global-coupons-admin-page', 'Coupon Operations', 'Coupon Operations', 'manage_options', 'global-coupons-admin-submenu-1', 'global_coupons_admin_submenu_1');
	add_submenu_page('global-coupons-admin-page', 'Preview', 'Preview', 'manage_options', 'global-coupons-admin-submenu-2', 'global_coupons_admin_submenu_2');
}

//admin panel main menu - global coupons and restrictions
function global_coupons_admin_mainmenu() {
    $coupons = global_coupons_get_all_global_coupons();
    $is_coupon_selected = false;
    
    //global coupon selection form
    $content = "<div class='kuponozellik'>";
    $content .= "<table id='admins'>";
    $content .= "<tr><th></th><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th><th>Global Operation</th></tr>";
    $content .= "<div class='forms'>";
    $content .= "<form action='' method='post' id='couponSelectionForm'>";
    $content .= "<h3> 1 - Choose <u>one</u> of the listed coupons:</h3>";
    //list all global coupons 
    foreach($coupons as $coupon)
    {
        $coupon_id = $coupon->ID;
        $coupon_name = $coupon->post_title;
        $coupon_amount = $coupon->coupon_amount;
        $coupon_description = $coupon->post_excerpt;
        $coupon_type = "";
        if($coupon->discount_type == "fixed_cart") $coupon_type = "Fixed Cart Discount";
        else $coupon_type = "Percentage Discount";
        
        if($coupon_description == "")
        {
            if($coupon_type == "Percentage Discount") $content .= "<tr><td style='width:10%'><input type='radio' name='sameRadio' value='$coupon_id'></td><td>". $coupon_name . " </td> " . "<td> ". $coupon_type . " </td> " . "<td> " . $coupon_amount . "%</td><td>Not Defined</td></tr>";
            else $content .= "<tr><td style='width:10%'><input type='radio' name='sameRadio' value='$coupon_id'></td><td>". $coupon_name . " </td> " . "<td> ". $coupon_type . " </td> " . "<td> " . get_woocommerce_currency_symbol() . $coupon_amount . "</td><td>Not Defined</td></tr>";
        }
        else
        {
            if($coupon_type == "Percentage Discount") $content .= "<tr><td style='width:10%'><input type='radio' name='sameRadio' value='$coupon_id'></td><td>". $coupon_name . " </td> " . "<td> ". $coupon_type . " </td> " . "<td> " . $coupon_amount . "%</td><td>$coupon_description</td></tr>";
            else $content .= "<tr><td style='width:10%'><input type='radio' name='sameRadio' value='$coupon_id'></td><td>". $coupon_name . " </td> " . "<td> ". $coupon_type . " </td> " . "<td> " . get_woocommerce_currency_symbol() . $coupon_amount . "</td><td>$coupon_description</td></tr>";
        }
    }
    $content .= "</table>";
    $content .= "<h3> 2 - Choose <u>one</u> of the listed restrictions:</h3>";
    $content .= "<table id='admins'>";
    $content .= "<th>First Order</th><th>Number of Orders</th><th>Amount of Orders</th><th>Special For You</th><th>Number of Reviews</th><th>Activate Date Interval</th>";
    
    //list the operations for the global coupons
    $content .= "<tr>";
    $content .= "<td style='width:10%'><input type=\"checkbox\" name=\"isFirstOrder\"></td>";
    $content .= "<td><input style='width:25%' type=\"number\" name=\"oldOrdersCount\" placeholder='5 'min='0' step='1'></td>";
    $content .= "<td><input style='width:35%' type=\"number\" name=\"oldOrdersTotalAmount\" placeholder='4500' min='0' step='1'></td>";
    $content .= "<td><input style='width:100%' type=\"text\" name=\"specialEmails\" placeholder='ex1@test.com,ex2@test.com'></td>";
    $content .= "<td><input style='width:25%' type=\"number\" name=\"oldCommentsCount\" placeholder='7' min='0' step='1'></td>";
    $content .= "<td><input style='width:100%' type=\"text\" name=\"startEndDate\" placeholder='30.06.1995-25.12.1995'></td>";
    $content .= "</tr>";
    $content .= "</table>";
    $content .= "<br>";
    
    //using nonce for coupon selection form
    $content .= wp_nonce_field('coupon_selection_form_action', 'nonce_of_couponSelectionForm');
    $content .= "<center><button class='de-button-admin de-button-anim-4' type='submit' value='Update' name='chooseCoupon' id='submitChosenCoupon'>Update</button></center>";
    $content .= "</form></div>";
    
    //verify nonce before get the inputs
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && !empty($_POST['sameRadio']) && isset($_POST['sameRadio']))
    {
        $coupon_id = $_POST['sameRadio'];
        $chosen_coupon = "Published Global Coupon: " . get_the_title($coupon_id) . " -> ";
        $is_coupon_selected = true;
    }
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && isset($_POST['oldOrdersTotalAmount']) && !empty($_POST['oldOrdersTotalAmount']) && $is_coupon_selected)
    {
        $amountOfOrders = $_POST['oldOrdersTotalAmount'];
        global_coupons_amount_of_orders($coupon_id, $amountOfOrders);
        $chosen_coupon .= "Selected Restriction: Total Amount of Orders";
        echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
        header("Refresh: 0");
    }
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && isset($_POST['oldOrdersCount']) && !empty($_POST['oldOrdersCount']) && $is_coupon_selected)
    {
        $numberOfOrders = $_POST['oldOrdersCount'];
        global_coupons_number_of_orders($coupon_id, $numberOfOrders);
        $chosen_coupon .= "Selected Restriction: Total Number of Orders";
        echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
        header("Refresh: 0");
    }
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && isset($_POST['isFirstOrder']) && !empty($_POST['isFirstOrder']) && $is_coupon_selected)
    {
        global_coupons_first_order($coupon_id);
        $chosen_coupon .= "Selected Restriction: First Order";
        echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
        header("Refresh: 0");
    }
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && isset($_POST['specialEmails']) && !empty($_POST['specialEmails']) && $is_coupon_selected)
    {
        $emailString = sanitize_text_field($_POST['specialEmails']);
        if(!global_coupons_check_email_inputs($emailString))
        {
            $chosen_coupon .= "Wrong Input.";
            echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
            header("Refresh: 0");
        }
        else
        {
            global_coupons_special_for_you($coupon_id, $emailString );
            $chosen_coupon .= "Selected Restriction: Special For You";
            echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
            header("Refresh: 0");
        }
    }
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && isset($_POST['oldCommentsCount']) && !empty($_POST['oldCommentsCount']) && $is_coupon_selected)
    {
        $howManyComments = $_POST['oldCommentsCount'];
        global_coupons_necessary_reviews($coupon_id, $howManyComments );
        $chosen_coupon .= "Selected Restriction: Total Number of Reviews";
        echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
        header("Refresh: 0");
    }
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && isset($_POST['startEndDate']) && !empty($_POST['startEndDate']) && $is_coupon_selected)
    {
        $startEndDate = sanitize_text_field($_POST['startEndDate']);
        if(!global_coupons_check_date_inputs($startEndDate))
        {
            $chosen_coupon .= "Wrong Input.";
            echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
            header("Refresh: 0");
        }
        else
        {
            global_coupons_activate_on_dates($coupon_id, $startEndDate);
            $chosen_coupon .= "Selected Restriction: Activate Date Interval";
            echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
            header("Refresh: 0");
        }
    }
    $content .= global_coupons_readme_part_1();
    $content .= "</div>";
    echo $content;
}

//admin panel submenu1 - standard coupon operations
function global_coupons_admin_submenu_1()
{
    //coupon creation form
    $html_output = "<div class='kuponozellik'>";
    $html_output .= "<div class='forms'>";
    $html_output .= "<form action='' method='post' id='couponCreationForm'>";
    $html_output .= "<h3> 1 - Create Global Coupon:</h3>";
    echo $html_output;
    
    $_couponName = "";
    $_couponType = "";
    $_couponAmount = 0;
    
    $_content = "<table id='admins'>";
    $_content .= "<tr><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th></tr>";
    $_content .= "<tr><td><input type='text' name='newCouponName'></td><td><select name='newCouponType'><option value='1'>Fixed Cart Discount</option><option value='2'>Percentage Discount</option></td><td><input type='number' name='newCouponAmount'></td></tr>";
    $_content .= "</table>";
    $_content .= "<br><center><button class='de-button-admin de-button-anim-4' type='submit' value='Create'>Create</button></center>";
    
    //using nonce for couponCreationForm
    wp_nonce_field('coupon_creation_form_action', 'nonce_of_couponCreationForm');
    echo $_content;
    
    //verify nonce before get the inputs
    if(wp_verify_nonce($_POST['nonce_of_couponCreationForm'], 'coupon_creation_form_action') && !empty($_POST['newCouponName']) && isset($_POST['newCouponName']) && !empty($_POST['newCouponType']) && isset($_POST['newCouponType']) && !empty($_POST['newCouponAmount']) && isset($_POST['newCouponAmount']))
    {
        $_couponName = $_POST['newCouponName'];
        if(($_POST['newCouponType']) == "1") $_couponType = "fixed_cart";
        else $_couponType = "percent";
        $_couponAmount = $_POST['newCouponAmount'];
        
        //create new coupon
        $_newCoupon = array(
        	'post_title' => "GC_".$_couponName,
        	'post_content' => '',
        	'post_status' => 'publish',
        	'post_author' => 1,
        	'post_type'		=> 'shop_coupon',
        );
        $_newCouponID = wp_insert_post( $_newCoupon );
        
        //create coupon limited to use once and individual
        //secure not defined global coupons with random email restriction
        $randomaizeEmail = rand();
        $tempEmails = "noone$randomaizeEmail@noone.com";
        update_post_meta( $_newCouponID, 'discount_type', $_couponType );
        update_post_meta( $_newCouponID, 'coupon_amount', $_couponAmount );
        update_post_meta( $_newCouponID, 'usage_limit_per_user', '1' );
        update_post_meta( $_newCouponID, 'individual_use', 'yes' );
        update_post_meta( $_newCouponID, 'customer_email', $tempEmails );
        wp_update_post($_newCouponID);
        header("Refresh: 0");
    }
    
    $html_output ="</form></div><br>";
    echo $html_output;
    
    //coupon deletion form
    $coupons = global_coupons_get_all_global_coupons();
    $content = "<table id='admins'>";
    $content .= "<tr><th></th><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th></tr>";
    $content .= "<div class='forms'>";
    $content .= "<form action='' method='post' id='couponDeletionForm'>";
    $content .= "<h3> 2 - Remove Global Coupon:</h3>";
    
    //list the global coupons 
    foreach($coupons as $coupon)
    {
        $coupon_id = $coupon->ID;
        $coupon_name = $coupon->post_title;
        $coupon_amount = $coupon->coupon_amount;
        $coupon_description = $coupon->post_excerpt;
        $coupon_type = "";
        if($coupon->discount_type == "fixed_cart") $coupon_type = "Fixed Cart Discount";
        else $coupon_type = "Percentage Discount";
        
        if($coupon_type == "Percentage Discount") $content .= "<tr><td style='width:10%'><input type='radio' name='sameRadio' value='$coupon_id'></td><td>". $coupon_name . " </td> " . "<td> ". $coupon_type . " </td> " . "<td> " . $coupon_amount . "%</td></tr>";
        else $content .= "<tr><td style='width:10%'><input type='radio' name='sameRadio' value='$coupon_id'></td><td>". $coupon_name . " </td> " . "<td> ". $coupon_type . " </td> " . "<td> " . $coupon_amount . get_woocommerce_currency_symbol() . "</td></tr>";
    }
    $content .= "</table>";
    echo $content;
    
    //using nonce for couponCreationForm
    $html_output = wp_nonce_field('coupon_deletion_form_action', 'nonce_of_couponDeletionForm');
    $html_output .= "<br><center><button class='de-button-admin de-button-anim-4' type='submit' value='Remove' name='chooseCoupon' id='submitChosenCoupon'>Remove</button></center>";
    $html_output .= "</form></div>";
    echo $html_output;
    
    //verify nonce before get the inputs
    if(wp_verify_nonce($_POST['nonce_of_couponDeletionForm'], 'coupon_deletion_form_action') && !empty($_POST['sameRadio']) && isset($_POST['sameRadio']))
    {
        $coupon_id = $_POST['sameRadio'];
        wp_delete_post($coupon_id); 
        header("Refresh: 0");
    }
    echo global_coupons_readme_part_2();
    $html_output = "</div>";
    echo $html_output;
}

//admin panel submenu2 - preview for my account page
function global_coupons_admin_submenu_2()
{
    echo "<div class='preview'><h2>This is how the global coupons will be shown in the user side.</h2>";
    echo "<h2>Be aware that, the Active/Deactive part is depending on the user account - in this case your account!</h2>";
    echo "<h2>Also note that, if the global coupon is not defined (blank comment/condition) then users will not see that coupon in the table but admin can.</h2>";
    $myCouponsLink = get_permalink( get_option('woocommerce_myaccount_page_id') );
    $myCouponsLink .= 'my-global-coupons/';
    echo "<h2>You can also check this preview by visiting <a href=". $myCouponsLink ." target='_blank'>My Account</a> page.</h2><br>";
    echo "<h4><i>To ask new properties or report bugs, kindly inform <a href='mailto:globalcoupons@mrebabi.com'>globalcoupons@mrebabi.com</a></i></h4></div>";
    echo "<h1>Preview: </h1><br>";
    echo global_coupons_my_account_page();
}

?>
