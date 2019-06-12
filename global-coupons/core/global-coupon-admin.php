<?php

//adding admin menu
add_action( 'admin_menu', 'global_coupons_admin_page' );

//admin menu function
function global_coupons_admin_page() {
	add_menu_page( 'Global Coupons', 'Global Coupons', 'manage_options' , 'global-coupons-admin-page' , 'global_coupons_admin_mainmenu', 'dashicons-star-filled' , '53');
	add_submenu_page('global-coupons-admin-page', 'Coupon Operations', 'Coupon Operations', 'manage_options', 'global-coupons-admin-submenu-1', 'global_coupons_admin_submenu_1');
	add_submenu_page('global-coupons-admin-page', 'Preview', 'Preview', 'manage_options', 'global-coupons-admin-submenu-2', 'global_coupons_admin_submenu_2');
	add_submenu_page('global-coupons-admin-page', 'Reports', 'Reports', 'manage_options', 'global-coupons-admin-submenu-3', 'global_coupons_admin_submenu_3');
	add_submenu_page('global-coupons-admin-page', 'Settings', 'Settings', 'manage_options', 'global-coupons-admin-submenu-4', 'global_coupons_admin_submenu_4');
}

//admin panel main menu - global coupons and restrictions
function global_coupons_admin_mainmenu() {
    $coupons = global_coupons_get_all_global_coupons();
    $is_coupon_selected = false;
    $coupon_ids = array ();
    $content = "<h1>Global Coupons</h1>";
    
    //global coupon selection form
    $content .= "<div class='kuponozellik'>";
    $content .= "<table id='admins'>";
    $content .= "<tr><th></th><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th><th>Global Operation</th></tr>";
    $content .= "<div class='forms'>";
    $content .= "<form action='' method='post' id='couponSelectionForm'>";
    $content .= "<h3> 1 - Choose <u>one</u> of the listed coupons:</h3>";
    //list all global coupons 
    foreach($coupons as $coupon)
    {
        $coupon_id = $coupon->ID;
        array_push($coupon_ids, $coupon_id);
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
        $coupon_id = sanitize_text_field($_POST['sameRadio']);
        if(in_array($coupon_id, $coupon_ids))
        {
            $chosen_coupon = "Published Global Coupon: " . get_the_title($coupon_id) . " -> ";
            $is_coupon_selected = true;   
        }
    }
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && isset($_POST['oldOrdersTotalAmount']) && !empty($_POST['oldOrdersTotalAmount']) && $is_coupon_selected)
    {
        $amountOfOrders = sanitize_text_field($_POST['oldOrdersTotalAmount']);
        $amountOfOrders = absint($amountOfOrders);
        global_coupons_amount_of_orders($coupon_id, $amountOfOrders);
        $chosen_coupon .= "Selected Restriction: Total Amount of Orders";
        echo "<script type='text/javascript'>alert('".$chosen_coupon."')</script>";
        header("Refresh: 0");
    }
    if(wp_verify_nonce($_POST['nonce_of_couponSelectionForm'], 'coupon_selection_form_action') && isset($_POST['oldOrdersCount']) && !empty($_POST['oldOrdersCount']) && $is_coupon_selected)
    {
        $numberOfOrders = sanitize_text_field($_POST['oldOrdersCount']);
        $numberOfOrders = absint($numberOfOrders);
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
        $howManyComments = sanitize_text_field($_POST['oldCommentsCount']);
        $howManyComments = absint($howManyComments);
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
    $html_output = "<h1>Coupon Operations</h1>";
    //coupon creation form
    $html_output .= "<div class='kuponozellik'>";
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
        $_couponName = sanitize_text_field($_POST['newCouponName']);
        $_couponTypeN = sanitize_text_field($_POST['newCouponType']);
        if( $_couponTypeN == "1") $_couponType = "fixed_cart";
        else if( $_couponTypeN == "2") $_couponType = "percent";
        else return;  
        $_couponAmount = sanitize_text_field($_POST['newCouponAmount']);
        $_couponAmount = absint($_couponAmount);
        
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
        $tempEmail = "noone$randomaizeEmail@noone.com";
        update_post_meta( $_newCouponID, 'discount_type', $_couponType );
        update_post_meta( $_newCouponID, 'coupon_amount', $_couponAmount );
        update_post_meta( $_newCouponID, 'usage_limit_per_user', '1' );
        update_post_meta( $_newCouponID, 'individual_use', 'yes' );
        update_post_meta( $_newCouponID, 'customer_email', $tempEmail );
        wp_update_post($_newCouponID);
        header("Refresh: 0");
    }
    
    $html_output ="</form></div><br>";
    echo $html_output;
    
    //coupon deletion form
    $coupons = global_coupons_get_all_global_coupons();
    $coupon_ids = array ();
    $content = "<table id='admins'>";
    $content .= "<tr><th></th><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th></tr>";
    $content .= "<div class='forms'>";
    $content .= "<form action='' method='post' id='couponDeletionForm'>";
    $content .= "<h3> 2 - Remove Global Coupon:</h3>";
    
    //list the global coupons 
    foreach($coupons as $coupon)
    {
        $coupon_id = $coupon->ID;
        array_push($coupon_ids, $coupon_id);
        $coupon_name = $coupon->post_title;
        $coupon_amount = $coupon->coupon_amount;
        $coupon_description = $coupon->post_excerpt;
        $coupon_type = "";
        if($coupon->discount_type == "fixed_cart") $coupon_type = "Fixed Cart Discount";
        else $coupon_type = "Percentage Discount";
        
        if($coupon_type == "Percentage Discount") $content .= "<tr><td style='width:10%'><input type='radio' name='sameRadio' value='". esc_html($coupon_id)."'></td><td>". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($coupon_type) . " </td> " . "<td> " . esc_html($coupon_amount) . "%</td></tr>";
        else $content .= "<tr><td style='width:10%'><input type='radio' name='sameRadio' value='". esc_html($coupon_id)."'></td><td>". esc_html($coupon_name) ." </td> " . "<td> ". esc_html($coupon_type) . " </td> " . "<td> " . esc_html($coupon_amount) . get_woocommerce_currency_symbol() . "</td></tr>";
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
        $coupon_id = sanitize_text_field($_POST['sameRadio']);
        if(in_array($coupon_id, $coupon_ids))
        {
            wp_delete_post($coupon_id);    
        }
        header("Refresh: 0");
    }
    echo global_coupons_readme_part_2();
    $html_output = "</div>";
    echo $html_output;
}

//admin panel submenu2 - preview for my account page
function global_coupons_admin_submenu_2()
{
    $content = "<h1>Preview</h1>";
    $content .= "<div class='preview'><h2>This is how the global coupons will be shown in the user side.</h2>";
    $content .= "<h2>Be aware that, the Active/Deactive part is depending on the user account - in this case your account!</h2>";
    $content .= "<h2>Also note that, if the global coupon is not defined (blank comment/condition) then users will not see that coupon in the table but admin can.</h2>";
    
    $myCouponsLink = get_permalink( get_option('woocommerce_myaccount_page_id') );
    $myCouponsLink .= "my-global-coupons/";
    
    $content .= "<h2>You can also check this preview by visiting <a href='". esc_html($myCouponsLink) ."' target='_blank'>My Account</a> page.</h2><br>";
    $content .= "<h4><i>To ask new properties or report bugs, kindly inform <a href='mailto:globalcoupons@mrebabi.com'>globalcoupons@mrebabi.com</a></i></h4></div>";
    $content .= "<div style='margin-left: 20%; width:60%'><br>";
    echo $content;
    echo global_coupons_my_account_page();
    echo "</div>";
}

//admin panel submenu3 - reports for global coupons
function global_coupons_admin_submenu_3()
{
    echo "<h1>Reports</h1>";
	echo global_coupons_reports();
}

//admin panel submenu4 - settings for global coupons front-end
function global_coupons_admin_submenu_4()
{
    $frontend_settings = get_option('global_coupons');
    
    $inputForCode = $frontend_settings['coupon_code'];
    $inputForType = $frontend_settings['coupon_type'];
    $inputForAmount = $frontend_settings['coupon_amount'];
    $inputForComment = $frontend_settings['coupon_restriction'];
    $inputForStatus = $frontend_settings['coupon_status'];
    $inputForApply = $frontend_settings['coupon_apply'];
    $inputForFixed = $frontend_settings['fixed_cart'];
    $inputForPerc = $frontend_settings['percentage'];
    $inputForAct = $frontend_settings['active'];
    $inputForDeact = $frontend_settings['deactive'];
    $inputForEmpty = $frontend_settings['empty_cart'];
    $inputForText = $frontend_settings['th_text'];
    $inputForThBg = $frontend_settings['th_bg'];
    $inputForFirst = $frontend_settings['first_order'];
    $inputForNumberOrders = $frontend_settings['number_of_orders'];
    $inputForAmountOrders = $frontend_settings['amount_of_orders'];
    $inputForSpecial = $frontend_settings['special_for_you'];
    $inputForReviews = $frontend_settings['number_of_reviews'];
    $inputForDates = $frontend_settings['date_interval'];

    $content .= "<h1>Settings</h1>";
    $content .= "<div class='settingsfirst'>";
    $content .= "<table id='settings'>";
    $content .= "<h3><center>Default Text</center></h3>";
    $content .= "<tr><th>Coupon Code</th></tr>";
    $content .= "<tr><th>Coupon Type</th></tr>";
    $content .= "<tr><th>Coupon Amount</th></tr>";
    $content .= "<tr><th>Comment/Condition</th></tr>";
    $content .= "<tr><th>Status</th></tr>";
    $content .= "<tr><th>Apply Coupon</th></tr>";
    $content .= "</table></div>";
    
    $content .= "<div class='settingssecond'>";
    $content .= "<form action='' method='post' id='submitSettingsForm'>";
    $content .= "<table id='settings'>";
    $content .= "<h3><center>Your Customization</center></h3>";
    $content .= "<tr><th><input type='text' name='couponCodeInput' placeholder='".esc_html($inputForCode)."'></th></tr>";
    $content .= "<tr><th><input type='text' name='couponTypeInput' placeholder='".esc_html($inputForType)."'></th></tr>";
    $content .= "<tr><th><input type='text' name='couponAmountInput' placeholder='".esc_html($inputForAmount)."'></th></tr>";
    $content .= "<tr><th><input type='text' name='couponCommentInput' placeholder='".esc_html($inputForComment)."'></th></tr>";
    $content .= "<tr><th><input type='text' name='couponStatusInput' placeholder='".esc_html($inputForStatus)."'></th></tr>";
    $content .= "<tr><th><input type='text' name='couponApplyInput' placeholder='".esc_html($inputForApply)."'></th></tr>";
    $content .= "</table>";    
    
    $content .= wp_nonce_field('save_settings_form', 'nonce_of_saveSettingsForm');
    $content .= "<br><center><button class='de-button-admin de-button-anim-4' type='submit' name='getSettings' id='submitSettingsForm'>Save</button></form></div>";
    
    $content .= "<div class='vertical-line'></div>";
    
    if(wp_verify_nonce($_POST['nonce_of_saveSettingsForm'], 'save_settings_form'))
    {
        if( isset($_POST['getSettings'] ))
        {
            if( isset($_POST['couponCodeInput']) && !empty($_POST['couponCodeInput']) ) $inputForCode = sanitize_text_field($_POST['couponCodeInput']);
            if( isset($_POST['couponTypeInput']) && !empty($_POST['couponTypeInput']) ) $inputForType = sanitize_text_field($_POST['couponTypeInput']);
            if( isset($_POST['couponAmountInput']) && !empty($_POST['couponAmountInput']) ) $inputForAmount = sanitize_text_field($_POST['couponAmountInput']);
            if( isset($_POST['couponCommentInput']) && !empty($_POST['couponCommentInput']) ) $inputForComment = sanitize_text_field($_POST['couponCommentInput']);
            if( isset($_POST['couponStatusInput']) && !empty($_POST['couponStatusInput']) ) $inputForStatus = sanitize_text_field($_POST['couponStatusInput']);
            if( isset($_POST['couponApplyInput']) && !empty($_POST['couponApplyInput']) ) $inputForApply = sanitize_text_field($_POST['couponApplyInput']);
            
            $newOptions = array(
                'coupon_code'   =>  $inputForCode,
                'coupon_type'   =>  $inputForType,
                'coupon_amount' =>  $inputForAmount,
                'coupon_restriction'    =>  $inputForComment,
                'coupon_status' =>  $inputForStatus,
                'coupon_apply'  =>  $inputForApply,
                'fixed_cart'    =>  $inputForFixed,
                'percentage'    =>  $inputForPerc,
                'active'    =>  $inputForAct,
                'deactive'  =>  $inputForDeact,
                'empty_cart'    =>  $inputForEmpty,
                'th_text'  =>  $inputForText,
                'th_bg' =>  $inputForThBg,
                'first_order'   =>  $inputForFirst,
                'number_of_orders'  =>  $inputForNumberOrders,
                'amount_of_orders'  =>  $inputForAmountOrders,
                'special_for_you'   =>  $inputForSpecial,
                'number_of_reviews' =>  $inputForReviews,
                'date_interval' =>  $inputForDates,
                );
            
            update_option('global_coupons', $newOptions);
            header("Refresh:0");
        }
    }
    
    $content .= "<div class='settingssecond'>";
    $content .= "<table id='settings'>";
    $content .= "<h3><center>Default Text</center></h3>";
    $content .= "<tr><th>Fixed Cart Discount</th></tr>";
    $content .= "<tr><th>Percentage Discount</th></tr>";
    $content .= "<tr><th>Active</th></tr>";
    $content .= "<tr><th>Deactive</th></tr>";
    $content .= "<tr><th>Empty Cart</th></tr>";
    $content .= "<tr><th>Table Header Background Color</th></tr>";
    $content .= "<tr><th>Table Header Text Color</th></tr>";
    $content .= "</table></div>";
    
    $content .= "<div class='settingssecond'>";
    $content .= "<table id='settings'>";
    $content .= "<h3><center>Your Customization</center></h3>";
    $content .= "<form action='' method='post' id='submitSettingsSecForm'>";
    $content .= "<tr><th><input type='text' name='fixedCartInput' placeholder='".$inputForFixed."'></th></tr>";
    $content .= "<tr><th><input type='text' name='percentageInput' placeholder='".$inputForPerc."'></th></tr>";
    $content .= "<tr><th><input type='text' name='activeInput' placeholder='".$inputForAct."'></th></tr>";
    $content .= "<tr><th><input type='text' name='deactiveInput' placeholder='".$inputForDeact."'></th></tr>";
    $content .= "<tr><th><input type='text' name='emptyInput' placeholder='".$inputForEmpty."'></th></tr>";
    $content .= "<tr><th><input type='text' name='thBgInput' placeholder='".$inputForThBg."'></th></tr>";
    $content .= "<tr><th><input type='text' name='textInput' placeholder='".$inputForText."'></th></tr></table>";
    
    $content .= wp_nonce_field('save_settings_sec_form', 'nonce_of_saveSettingsSecForm');
    $content .= "<br><center><button class='de-button-admin de-button-anim-4' type='submit' name='getSecSettings' id='submitSettingsSecForm'>Save</button>";
    $content .= "<br><br><br><center><button class='de-button-admin de-button-anim-4' type='submit' name='resetSecSettings' id='submitSettingsSecForm'>Reset All</button></form></div>";
    
    $content .= "<div class='vertical-line'></div>";
    
    if(wp_verify_nonce($_POST['nonce_of_saveSettingsSecForm'], 'save_settings_sec_form'))
    {
        if( isset($_POST['getSecSettings'] ))
        {
            if( isset($_POST['fixedCartInput']) && !empty($_POST['fixedCartInput']) ) $inputForFixed = sanitize_text_field($_POST['fixedCartInput']);
            if( isset($_POST['percentageInput']) && !empty($_POST['percentageInput']) ) $inputForPerc = sanitize_text_field($_POST['percentageInput']);
            if( isset($_POST['activeInput']) && !empty($_POST['activeInput']) ) $inputForAct = sanitize_text_field($_POST['activeInput']);
            if( isset($_POST['deactiveInput']) && !empty($_POST['deactiveInput']) ) $inputForDeact = sanitize_text_field($_POST['deactiveInput']);
            if( isset($_POST['emptyInput']) && !empty($_POST['emptyInput']) ) $inputForEmpty = sanitize_text_field($_POST['emptyInput']);
            if( isset($_POST['thBgInput']) && !empty($_POST['thBgInput']) ) $inputForThBg = sanitize_text_field($_POST['thBgInput']);
            if( isset($_POST['textInput']) && !empty($_POST['textInput']) ) $inputForText = sanitize_text_field($_POST['textInput']);
            
            $newOptions = array(
                'coupon_code'   =>  $inputForCode,
                'coupon_type'   =>  $inputForType,
                'coupon_amount' =>  $inputForAmount,
                'coupon_restriction'    =>  $inputForComment,
                'coupon_status' =>  $inputForStatus,
                'coupon_apply'  =>  $inputForApply,
                'fixed_cart'    =>  $inputForFixed,
                'percentage'    =>  $inputForPerc,
                'active'    =>  $inputForAct,
                'deactive'  =>  $inputForDeact,
                'empty_cart'    =>  $inputForEmpty,
                'th_text'  =>  $inputForText,
                'th_bg' =>  $inputForThBg,
                'first_order'   =>  $inputForFirst,
                'number_of_orders'  =>  $inputForNumberOrders,
                'amount_of_orders'  =>  $inputForAmountOrders,
                'special_for_you'   =>  $inputForSpecial,
                'number_of_reviews' =>  $inputForReviews,
                'date_interval' =>  $inputForDates,
                );
            
            update_option('global_coupons', $newOptions);
            header("Refresh:0");
        }
        if( isset($_POST['resetSecSettings'] ))
        {
            $defaultOptions = array(
                'coupon_code'   =>  'Coupon Code',
                'coupon_type'   =>  'Coupon Type',
                'coupon_amount' =>  'Coupon Amount',
                'coupon_restriction'    =>  'Comment/Condition',
                'coupon_status' =>  'Status',
                'coupon_apply'  =>  'Apply Coupon',
                'fixed_cart'    =>  'Fixed Cart Discount',
                'percentage'    =>  'Percentage Cart Discount',
                'active'    =>  'Active',
                'deactive'  =>  'Deactive',
                'empty_cart'    =>  'Empty Cart',
                'th_text'  =>  'white',
                'th_bg' =>  '#333333',
                'first_order'   =>  'Discount For First Order',
                'number_of_orders'  =>  'Required number of orders',
                'amount_of_orders'  =>  'Total amount of orders',
                'special_for_you'   =>  'Special Discount For You',
                'number_of_reviews' =>  'Required number of reviews',
                'date_interval' =>  'Available Between'
                );
            
            update_option('global_coupons', $defaultOptions);
            header("Refresh:0");
        }
    }
    
    $content .= "<div class='settingssecond'>";
    $content .= "<table id='settings'>";
    $content .= "<h3><center>Default Text</center></h3>";
    $content .= "<tr><th>Discount For First Order</th></tr>";
    $content .= "<tr><th>Required number of orders</th></tr>";
    $content .= "<tr><th>Total amount of orders</th></tr>";
    $content .= "<tr><th>Special Discount For You</th></tr>";
    $content .= "<tr><th>Required number of reviews</th></tr>";
    $content .= "<tr><th>Available Between</th></tr>";
    $content .= "</table></div>";
    
    $content .= "<div class='settingssecond'>";
    $content .= "<table id='settings'>";
    $content .= "<h3><center>Your Customization</center></h3>";
    $content .= "<form action='' method='post' id='submitSettingsThirdForm'>";
    $content .= "<tr><th><input type='text' name='firstOrderInput' placeholder='".$inputForFirst."'></th></tr>";
    $content .= "<tr><th><input type='text' name='numberOrdersInput' placeholder='".$inputForNumberOrders."'></th></tr>";
    $content .= "<tr><th><input type='text' name='amountOrdersInput' placeholder='".$inputForAmountOrders."'></th></tr>";
    $content .= "<tr><th><input type='text' name='specialForYouInput' placeholder='".$inputForSpecial."'></th></tr>";
    $content .= "<tr><th><input type='text' name='reviewInput' placeholder='".$inputForReviews."'></th></tr>";
    $content .= "<tr><th><input type='text' name='datesInput' placeholder='".$inputForDates."'></th></tr></table>";
    
    $content .= wp_nonce_field('save_settings_third_form', 'nonce_of_saveSettingsThirdForm');
    $content .= "<br><center><button class='de-button-admin de-button-anim-4' type='submit' name='getThirdSettings' id='submitSettingsThirdForm'>Save</button></form></div>";
    
    if(wp_verify_nonce($_POST['nonce_of_saveSettingsThirdForm'], 'save_settings_third_form'))
    {
        if( isset($_POST['getThirdSettings'] ))
        {
            if( isset($_POST['firstOrderInput']) && !empty($_POST['firstOrderInput']) ) $inputForFirst = sanitize_text_field($_POST['firstOrderInput']);
            if( isset($_POST['numberOrdersInput']) && !empty($_POST['numberOrdersInput']) ) $inputForNumberOrders = sanitize_text_field($_POST['numberOrdersInput']);
            if( isset($_POST['amountOrdersInput']) && !empty($_POST['amountOrdersInput']) ) $inputForAmountOrders = sanitize_text_field($_POST['amountOrdersInput']);
            if( isset($_POST['specialForYouInput']) && !empty($_POST['specialForYouInput']) ) $inputForSpecial = sanitize_text_field($_POST['specialForYouInput']);
            if( isset($_POST['reviewInput']) && !empty($_POST['reviewInput']) ) $inputForReviews = sanitize_text_field($_POST['reviewInput']);
            if( isset($_POST['datesInput']) && !empty($_POST['datesInput']) ) $inputForDates = sanitize_text_field($_POST['datesInput']);
            
            
            $newOptions = array(
                'coupon_code'   =>  $inputForCode,
                'coupon_type'   =>  $inputForType,
                'coupon_amount' =>  $inputForAmount,
                'coupon_restriction'    =>  $inputForComment,
                'coupon_status' =>  $inputForStatus,
                'coupon_apply'  =>  $inputForApply,
                'fixed_cart'    =>  $inputForFixed,
                'percentage'    =>  $inputForPerc,
                'active'    =>  $inputForAct,
                'deactive'  =>  $inputForDeact,
                'empty_cart'    =>  $inputForEmpty,
                'th_text'  =>  $inputForText,
                'th_bg' =>  $inputForThBg,
                'first_order'   =>  $inputForFirst,
                'number_of_orders'  =>  $inputForNumberOrders,
                'amount_of_orders'  =>  $inputForAmountOrders,
                'special_for_you'   =>  $inputForSpecial,
                'number_of_reviews' =>  $inputForReviews,
                'date_interval' =>  $inputForDates,
                );
            
            update_option('global_coupons', $newOptions);
            header("Refresh:0");
        }
    }
    
    echo $content;
}

?>
