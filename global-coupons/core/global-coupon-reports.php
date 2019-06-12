<?php

function global_coupons_reports()
{
    $coupons = global_coupons_get_all_global_coupons();
    $users = global_coupons_get_all_users();
    $orders = global_coupons_get_all_orders();
    
    $content = "<div class='reports-active'>";
    $content .= "<h1>Activation Report of Global Coupons</h1>";
    $content .= "<table id='admins'>";
    $content .= "<tr><th>Coupon Code</th><th>Coupon Type</th><th>Coupon Amount</th><th>Global Operation</th><th>Total Activations</th><th>Used Amount</th><th>Live Activations</th></tr>";
    
    foreach($coupons as $coupon)
    {
        $coupon_id = $coupon->ID;
        $coupon_name = $coupon->post_title;
        $coupon_amount = $coupon->coupon_amount;
        $coupon_description = $coupon->post_excerpt;
        $coupon_type = "";
        if($coupon->discount_type == "fixed_cart") $coupon_type = "Fixed Cart Discount";
        else $coupon_type = "Percentage Discount";
    
        $total_used = global_coupons_get_all_used($coupon);
        $live_activations = global_coupons_get_all_active($coupon);
        $total_activations = $total_used + $live_activations;
        
        if($coupon_type == "Percentage Discount") $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($coupon_type) . " </td> " . "<td> " . esc_html($coupon_amount) . "%</td>" . "<td> " . esc_html($coupon_description) ."</td>" . "<td>" . esc_html($total_activations) . "</td><td>" . esc_html($total_used) . "</td><td>" . esc_html($live_activations) . "</td>";
        else $content .= "<tr><td> ". esc_html($coupon_name) . " </td> " . "<td> ". esc_html($coupon_type) . " </td> " . "<td> " . get_woocommerce_currency_symbol() . esc_html($coupon_amount) . "</td>" . "<td> " . esc_html($coupon_description) ."</td>" . "<td>" . esc_html($total_activations) . "</td><td>" . esc_html($total_used) . "</td><td>" . esc_html($live_activations) . "</td>"; 
    }
    
    $content .= "</table>";
    $content .= "</div><div class='reports-orders'>";
    $content .= "<h1>Order Report of Global Coupons</h1>";
    $content .= "<table id='admins'>";
    $content .= "<tr><th>Order</th><th>Order Date</th><th>Order Status</th><th>Total</th><th>Discount</th><th>Used Global Coupon</th></tr>";
    
    foreach($orders as $order)
    {
        $order_id = $order->ID;
        $order_date = $order->date_created;
        $order_status = $order->status;
        $order_total = $order->total+$order->discount_total;
        $order_discount = $order->discount_total;
        $order_used_coupon_arr = $order->get_used_coupons();
        $order_used_coupon = $order_used_coupon_arr[0];

        if($order_discount>0 && (substr($order_used_coupon, 0, 3) == 'gc_')) $content .= "<tr><td>". esc_html($order_id) ."</td><td>". esc_html(substr($order_date, 0, 10)) ."</td><td>". esc_html(ucfirst($order_status)) ."</td><td>". get_woocommerce_currency_symbol() . " " . esc_html($order_total) ."</td><td>". get_woocommerce_currency_symbol() . " " . esc_html($order_discount) ."</td><td>". esc_html(strtoupper($order_used_coupon)) ."</td></tr>";
    }
    
    $content .= "</table></div>";
    return $content;
}

?>
