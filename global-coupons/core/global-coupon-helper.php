<?php

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
function global_couponsget_all_products()
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