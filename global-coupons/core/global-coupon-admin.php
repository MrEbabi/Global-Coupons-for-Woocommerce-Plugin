<?php

//adding admin menu
add_action( 'admin_menu', 'global_coupons_admin_page' );

function global_coupons_admin_page() {
	add_menu_page( 'Global Coupons', 'Global Coupons', 'manage_options' , 'global-coupons-admin-page' , 'global_coupons_admin_mainmenu', '' , '53');
	add_submenu_page('global-coupons-admin-page', 'Coupon Operations', 'Coupon Operations', 'manage_options', 'global-coupons-admin-submenu-1', 'global_coupons_admin_submenu_1');
	add_submenu_page('global-coupons-admin-page', 'Preview', 'Preview', 'manage_options', 'global-coupons-admin-submenu-2', 'global_coupons_admin_submenu_2');
}

//admin panel main menu - global coupons and restrictions
function global_coupons_admin_mainmenu() {
    $coupons = get_all_global_coupons();
    ?>
    <div class="kuponozellik">
    <?php
    $content = "<table id='admins'>";
    $content .= "<tr><th></th><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th><th>Global Operation</th></tr>";
    ?>
        <div class="forms">
        <form action="" method="post" id="couponSelectionForm">
            <h3> 1 - Choose <u>one</u> of the listed coupons:</h3>
        <?php
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
        echo $content;
        ?>
        <br>
        <center><button class="de-button-admin de-button-anim-4" type="submit" value="Update" name="chooseCoupon" id="submitChosenCoupon">Update</button></center>
        </form>
        </div>
        <?php
            if(!empty($_POST['sameRadio']) && isset($_POST['sameRadio']))
            {
                $coupon_id = $_POST['sameRadio'];
                $chosen_coupon = "<br><h3>Published Global Coupon: " . get_the_title($coupon_id) . "</h3>";
            }
            if(isset($_POST['oldOrdersTotalAmount']) && !empty($_POST['oldOrdersTotalAmount']))
            {
                $amountOfOrders = $_POST['oldOrdersTotalAmount'];
                global_coupons_amount_of_orders($coupon_id, $amountOfOrders);
                $chosen_coupon .= "<h3>Selected Restriction: Total Amount of Orders</h3>";
            }
            if(isset($_POST['oldOrdersCount']) && !empty($_POST['oldOrdersCount']))
            {
                $numberOfOrders = $_POST['oldOrdersCount'];
                global_coupons_number_of_orders($coupon_id, $numberOfOrders);
                $chosen_coupon .= "<h3>Selected Restriction: Total Number of Orders</h3>";
            }
            if(isset($_POST['isFirstOrder']) && !empty($_POST['isFirstOrder']))
            {
                global_coupons_first_order($coupon_id);
                $chosen_coupon .= "<h3>Selected Restriction: First Order</h3>";
            }
            if(isset($_POST['specialEmails']) && !empty($_POST['specialEmails']))
            {
                $emailString = $_POST['specialEmails'];
                if(!check_email_inputs($emailString))
                {
                    $chosen_coupon .= "<br>Wrong Input.";
                }
                else
                {
                    global_coupons_special_for_you($coupon_id, $emailString );
                    $chosen_coupon .= "<h3>Selected Restriction: Special For You</h3>";
                }
            }
            if(isset($_POST['oldCommentsCount']) && !empty($_POST['oldCommentsCount']))
            {
                $howManyComments = $_POST['oldCommentsCount'];
                global_coupons_necessary_reviews($coupon_id, $howManyComments );
                $chosen_coupon .= "<h3>Selected Restriction: Total Number of Reviews</h3>";
            }
            if(isset($_POST['startEndDate']) && !empty($_POST['startEndDate']))
            {
                $startEndDate = $_POST['startEndDate'];
                if(!check_date_inputs($startEndDate))
                {
                    $chosen_coupon .= "<br>Wrong Input.";
                }
                else
                {
                    global_coupons_activate_on_dates($coupon_id, $startEndDate);
                    $chosen_coupon .= "<h3>Selected Restriction: Activate Date Interval</h3>";
                }
            }
            echo $chosen_coupon;
        ?>
    </div>
    <?php
    echo readme_part_1();
}

//admin panel submenu1 - standard coupon operations
function global_coupons_admin_submenu_1()
{
    ?>
    <div class="kuponozellik">
        <div class="forms">
        <form action="" method="post" id="couponCreationForm">
        <h3> 1 - Create Global Coupon:</h3>
            <?php
            $_couponName = "";
            $_couponType = "";
            $_couponAmount = 0;
            
            $_content = "<table id='admins'>";
            
            $_content .= "<tr><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th></tr>";
            $_content .= "<tr><td><input type='text' name='newCouponName'></td><td><select name='newCouponType'><option value='1'>Fixed Cart Discount</option><option value='2'>Percentage Discount</option></td><td><input type='number' name='newCouponAmount'></td></tr>";
            $_content .= "</table>";
            $_content .= "<br><center><button class='de-button-admin de-button-anim-4' type='submit' value='Create'>Create</button></center>";
            echo $_content;
            
            //get inputs
            if(!empty($_POST['newCouponName']) && isset($_POST['newCouponName']) && !empty($_POST['newCouponType']) && isset($_POST['newCouponType']) && !empty($_POST['newCouponAmount']) && isset($_POST['newCouponAmount']))
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
            ?>
        </form>
        </div>
        <br>
        <?php
        $coupons = get_all_global_coupons();
        $content = "<table id='admins'>";
        $content .= "<tr><th></th><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th></tr>";
        ?>
            <div class="forms">
            <form action="" method="post" id="couponDeletionForm">
                <h3> 2 - Remove Global Coupon:</h3>
            <?php
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
            ?>
            <br>
            <center><button class="de-button-admin de-button-anim-4" type="submit" value="Remove" name="chooseCoupon" id="submitChosenCoupon">Remove</button></center>
            </form>
            </div>
            <?php
            if(!empty($_POST['sameRadio']) && isset($_POST['sameRadio']))
            {
                $coupon_id = $_POST['sameRadio'];
                wp_delete_post($coupon_id); 
                header("Refresh: 0");
            }
            ?>
    </div>
    <?php
    echo readme_part_2();
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
