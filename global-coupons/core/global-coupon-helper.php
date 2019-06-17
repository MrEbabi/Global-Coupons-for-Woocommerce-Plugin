<?php

//function for settings page to edit front-end
function global_coupons_default_settings()
{
    //check if options are created
    $isCreated = get_option('global_coupons');
    
    if($isCreated) return;
    
    //if not, add options
    add_option('global_coupons', array(
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
    'th_bg' =>  '#4CAF50',
    'first_order'   =>  'Discount For First Order',
    'number_of_orders'  =>  'Required number of orders',
    'amount_of_orders'  =>  'Total amount of orders',
    'special_for_you'   =>  'Special Discount For You',
    'number_of_reviews' =>  'Required number of reviews',
    'date_interval' =>  'Available Between',
    'no_coupons_found' =>  'No Global Coupons Found',
    ));
}

//function for settings page to edit my account tab names
function global_coupons_menu_tab_names()
{
    //check if options are created
    $isCreated = get_option('global_coupons_menu');
    
    if($isCreated) return;
    
    //if not, add options
    add_option('global_coupons_menu', array(
    'dashboard_1'   =>  __( 'Dashboard', 'woocommerce' ),
    'coupons_2' =>  __( 'Coupons', 'woocommerce' ),
    'orders_3'  =>  __( 'Orders', 'woocommerce' ),
    'address_4' =>  __( 'Addresses', 'woocommerce' ),
    'account_5' =>  __( 'Account Details', 'woocommerce' ),
    'logout_6'  =>  __( 'Logout', 'woocommerce' ),
    ));
}

//function to get all global coupons
function global_coupons_get_all_global_coupons() 
{
    $args = array(
        //get all coupons that starts with string GC
        's'                => 'GC',
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'asc',
        'post_type'        => 'shop_coupon',
        'post_status'      => 'publish',
        );
        
    $coupons = get_posts( $args );
    return $coupons;
}

//function to get all global coupons
function global_coupons_count_all_global_coupons() 
{
    $args = array(
        //get all coupons that starts with string GC
        's'                => 'GC',
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'asc',
        'post_type'        => 'shop_coupon',
        'post_status'      => 'publish',
        );
        
    $coupons = get_posts( $args );
    $result = count($coupons);
    return $result;
}

//function to get all product categories to list
function global_coupons_get_all_categories() 
{
    $args = array(
        'post_per_page' =>  -1,
        'orderby'   =>  'title',
        'order' =>  'asc',
        'post_type' =>  'product_cat',
        'post_status'   =>  'publish',
        );
    $categories = get_posts( $args );
    return $categories;
}

//function to get all products to list
function global_coupons_get_all_products()
{
    $args = array(
        'post_per_page' =>  -1,
        'orderby'   =>  'title',
        'order' =>  'asc',
        'post_type' =>  'product',
        'post_status'   =>  'publish',
        );
    $products = get_posts( $args );
    return $products;
}

//function to get all users
function global_coupons_get_all_users()
{
    $customers = get_users( array( 'fields' => array( 'ID' ) ) );
    return $customers;
}

//function to get all orders
function global_coupons_get_all_orders()
{
    $customer_orders = wc_get_orders( array(
    'limit'    => -1,
    'status'   => array('completed','pending','processing')
    ) );
    return $customer_orders;
}

//function to get all active global coupons
function global_coupons_get_all_active($coupon)
{
    $count_active = 0;
    $emails = array();
    $customers = global_coupons_get_all_users();
    
    //traverse all users and count active global coupons
    foreach($customers as $customer)
    {
        $isActive = false;
        $this_customer_id = $customer->ID;
        $user_info = get_userdata($this_customer_id);
        $customer_email = $user_info->user_email;
        
        //check if given coupon is active for this customer
        if(in_array( $customer_email , $coupon->customer_email )) 
        {
            $isActive = true;
        }
        
        //if the coupon is active for this customer, increment the counter
        if($isActive)
        {
            $count_active++;
        }
    }
    
    return $count_active;
}

//function to get all used global coupons
function global_coupons_get_all_used($coupon)
{
    $count_used = 0;
    $orders = global_coupons_get_all_orders();
    
    //traverse all users and count used global coupons
    foreach($orders as $order)
    {
        $isUsed = false;
        $order_discount = $order->discount_total;
        $order_used_coupon = $order->get_used_coupons();
        
        //check if given coupon is used 
        if($order_discount>0 && strtoupper($order_used_coupon[0]) == $coupon->post_title) 
        {
            $isUsed = true;
        }
        
        //if the coupon is active for this customer, increment the counter
        if($isUsed)
        {
            $count_used++;
        }
    }
    
    return $count_used;
}

//function to get used global coupons by a specific user
function global_coupons_get_customer_used($coupon)
{
    $args = array(
    'customer_id' => get_current_user_id(),
    );
    
    $orders = wc_get_orders( $args );   
    
    //traverse all orders of this user and check if this coupon is used
    foreach($orders as $order)
    {
        $isUsed = false;
        $order_discount = $order->discount_total;
        $order_used_coupon = $order->get_used_coupons();
        
        //check if given coupon is used 
        if($order_discount>0 && strtoupper($order_used_coupon[0]) == $coupon->post_title) 
        {
            return true;
        }
    }
    
    return $isUsed;
}

//validate email input
function global_coupons_check_email_inputs($emailToCheck)
{
    $isEmail = true;
    $emails = explode(',',$emailToCheck);
    
    foreach($emails as $email)
    {
        if(!is_email($email))
        {
            $isEmail = false;
        }
    }
    
    return $isEmail;
}

//validate date interval input
function global_coupons_check_date_inputs($dateToCheck)
{
    $isDate = true;
    
    $regularLength = 21;
    $partRegularLength = 10;
    
    if(strlen($dateToCheck) != $regularLength) $isDate = false;
    
    $dateParts = explode("-", $dateToCheck);
    
    if(strlen($dateParts[0])!=$partRegularLength) $isDate = false;
    if(strlen($dateParts[1])!=$partRegularLength) $isDate = false;
    
    $startDate = explode(".", $dateParts[0]);
    $endDate = explode('.', $dateParts[1]);
    
    if(count($startDate)!=3) $isDate = false;
    if(count($endDate)!=3) $isDate = false;
    
    if(strlen($startDate[0])!=2) $isDate = false;
    if(strlen($startDate[1])!=2) $isDate = false;
    if(strlen($startDate[2])!=4) $isDate = false;
    if(strlen($endDate[0])!=2) $isDate = false;
    if(strlen($endDate[1])!=2) $isDate = false;
    if(strlen($endDate[2])!=4) $isDate = false;
    
    if( intval($startDate[0])>31 || intval($startDate[0])<=0) $isDate = false;
    if( intval($endDate[0])>31 || intval($endDate[0])<=0) $isDate = false;
    if( intval($endDate[1])>12 || intval($endDate[1])<=0) $isDate = false;
    if( intval($startDate[1])>12 || intval($startDate[1])<=0) $isDate = false;
    if( intval($startDate[2])<1931) $isDate = false;
    
    return $isDate;
}

?>
