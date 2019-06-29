<?php

//review (comment) count
function global_coupons_necessary_reviews($couponid, $commentCount)
{
    $customers = global_coupons_get_all_users();
    $emails = array();
    
    //traverse all users and count their reviews
    foreach($customers as $customer)
    {
        $this_customer_id = $customer->ID;
        $args = array(
            'user_id' => $this_customer_id,
        );
        $comments = get_comments($args);
        $haveEnoughComments = false;
        $user_info = get_userdata($this_customer_id);
        $customer_email = $user_info->user_email;
        
        if(count($comments)>=$commentCount) $haveEnoughComments = true;
        
        //if the user have enough reviews, add her/his e-mail to the coupon
        if($haveEnoughComments)
        {
            array_push($emails, $customer_email);
        }
    }
    update_post_meta( $couponid, 'customer_email', $emails );
    
    $excerpt_text = "At least $commentCount product review(s)";
    $update_post = array(
        'ID' => $couponid,
        'post_excerpt' => $excerpt_text,
        );
    wp_update_post($update_post);
}

//special for you
function global_coupons_special_for_you($couponid, $couponemails)
{
    $emails = explode(",", $couponemails);
    update_post_meta( $couponid, 'customer_email', $emails );
    $update_post = array(
        'ID' => $couponid,
        'post_excerpt' => 'Special Discount For You',
        );
    wp_update_post($update_post);
}

//first order
function global_coupons_first_order($couponid)
{
    $customers = global_coupons_get_all_users();
    $emails = array();
    
    //traverse all users to see whether they have buy something or not
    foreach($customers as $customer)
    {
        $this_customer_id = $customer->ID;
        $customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $this_customer_id,
            'post_type'   => wc_get_order_types(),
            //only for on-hold, processing and completed orders
            'post_status' => array('wc-on-hold','wc-processing','wc-completed'),
        ) );
            
        $isThisFirstOrder = false;
        $user_info = get_userdata($this_customer_id);
        $customer_email = $user_info->user_email;
        
        if(count($customer_orders)==0) $isThisFirstOrder = true;
        
        //if they do not have any orders, add their e-mails to the given coupon
        if($isThisFirstOrder)
        {
            array_push($emails, $customer_email);
        }
    }
    update_post_meta( $couponid, 'customer_email', $emails );
    $update_post = array(
        'ID' => $couponid,
        'post_excerpt' => 'Discount For First Order',
        );
    wp_update_post($update_post);
}

//activate on the given date interval
function global_coupons_activate_on_dates($couponid, $dates)
{
    $isTodayInTheInterval = false;
    $separateDates = explode("-", $dates);
    $separateStart = explode(".", $separateDates[0]);
    $separateEnd = explode(".", $separateDates[1]);
    $today = getdate();
    
    $remainingToStart = ( (365*($today[year]-$separateStart[2])) + (30*($today[mon]-$separateStart[1])) + 1*($today[mday]-$separateStart[0]) );
    $remainingToEnd = ( (365*($separateEnd[2]-$today[year])) + (30*($separateEnd[1]-$today[mon])) + (1*($separateEnd[0]-$today[mday])) );
    
    if($remainingToStart>=0 && $remainingToEnd>=0) $isTodayInTheInterval = true;
    
    $excerpt_text = "Available: ";
    $excerpt_text .= "$separateStart[0].$separateStart[1].$separateStart[2] - ";
    $excerpt_text .= "$separateEnd[0].$separateEnd[1].$separateEnd[2]";
    
    if($isTodayInTheInterval)
    {
        update_post_meta( $couponid, 'customer_email', "" );
        update_post_meta( $couponid, 'individual_use', 'yes' );
        $update_post = array(
            'ID' => $couponid,
            'post_excerpt' => $excerpt_text,
            );
        wp_update_post($update_post);
    }
    else
    {
        $admin_email = get_option('admin_email');
        update_post_meta( $couponid, 'customer_email', $admin_email );
        update_post_meta( $couponid, 'individual_use', 'yes' );
        $update_post = array(
            'ID' => $couponid,
            'post_excerpt' => $excerpt_text,
            );
        wp_update_post($update_post);
    }
}

//required number of orders
function global_coupons_number_of_orders($couponid, $number)
{
    $customers = global_coupons_get_all_users();
    $emails = array();
    
    //traverse all users to see whether they have buy something or not
    foreach($customers as $customer)
    {
        $this_customer_id = $customer->ID;
        $customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $this_customer_id,
            'post_type'   => wc_get_order_types(),
            //only for processing and completed orders
            'post_status' => array('wc-processing','wc-completed'),
        ) );
            
        $haveEnoughOrders = false;
        $user_info = get_userdata($this_customer_id);
        $customer_email = $user_info->user_email;
        
        if(count($customer_orders)>=$number) $haveEnoughOrders = true;
        
        //if customers have enough orders, add their e-mails to the given coupon
        if($haveEnoughOrders)
        {
            array_push($emails, $customer_email);
        }
    }
    
    $excerpt_text = "Required number of orders: ";
    $excerpt_text .= $number;
    
    update_post_meta( $couponid, 'customer_email', $emails );
    $update_post = array(
        'ID' => $couponid,
        'post_excerpt' => $excerpt_text,
        );
    wp_update_post($update_post);
}

//required amount of orders
function global_coupons_amount_of_orders($couponid, $amount)
{
    $customers = global_coupons_get_all_users();
    $emails = array();
    
    //traverse all users to see whether they have buy something or not
    foreach($customers as $customer)
    {
        $this_customer_id = $customer->ID;
        $customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $this_customer_id,
            'post_type'   => wc_get_order_types(),
            //only for processing and completed orders
            'post_status' => array('wc-processing','wc-completed'),
        ) );
        
        //get amount of each order
        $totalAmount = 0;
        foreach($customer_orders as $customer_order)
        {
            $order_id = $customer_order->ID;
            $customer_order = wc_get_order( $order_id );
            $totalAmount += $customer_order->get_total();
        }
        $haveEnoughAmount = false;
        $user_info = get_userdata($this_customer_id);
        $customer_email = $user_info->user_email;
        
        if($totalAmount>=$amount) $haveEnoughAmount = true;
        
        //if customers have enough orders, add their e-mails to the given coupon
        if($haveEnoughAmount)
        {
            array_push($emails, $customer_email);
        }
    }
    
    $excerpt_text = "Total amount of orders: ";
    $excerpt_text .= $amount;
    
    update_post_meta( $couponid, 'customer_email', $emails );
    $update_post = array(
        'ID' => $couponid,
        'post_excerpt' => $excerpt_text,
        );
    wp_update_post($update_post);
}

//required years of membership
function global_coupons_years_of_membership($couponid, $years)
{
    $customers = global_coupons_get_all_users();
    $emails = array();
    
    //traverse all users to see if they satisfy required years of membership
    foreach($customers as $customer)
    {
        $difference = 0;
        $this_customer_id = $customer->ID;
        $user_info = get_userdata($this_customer_id);
        $register_date = $user_info->user_registered;
        $customer_email = $user_info->user_email;
        
        $register = date( "d m Y", strtotime( $register_date ) );
        
        $today = date( "d m Y" );
        
        $register_day = substr($register, 0, 2);
        $register_mon = substr($register, 3, 2);
        $register_year = substr($register, 6, 4);
        
        
        $today_day = substr($today, 0, 2);
        $today_mon = substr($today, 3, 2);
        $today_year = substr($today, 6, 4);
       
        
        if(( $register_mon < $today_mon ) || ( $register_mon == $today_mon && $register_day <= $today_day ) )
        {
           $difference = $today_year - $register_year;
        }
        else
        {
            $difference = $today_year - $register_year - 1;
        }
        if($difference >= $years)
        {
            array_push($emails, $customer_email);
        }
    }
    
    $excerpt_text = "Required years of membership: ";
    $excerpt_text .= $years;
    
    update_post_meta( $couponid, 'customer_email', $emails );
    $update_post = array(
        'ID' => $couponid,
        'post_excerpt' => $excerpt_text,
        );
    wp_update_post($update_post);
}

?>
