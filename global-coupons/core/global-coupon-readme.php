<?php

//read me part 1 for main menu - global coupons
function readme_part_1()
{
    $content = "<div class='helps'>";
    $content .= "<li><b>You can edit your existing coupons from Global Coupons section to restrict them selecting the <u>properties</u>:</b></li>";
    $content .= "</ul>";
    $content .= "<ol><b><li>First Order:</li></b>When this restriction is selected, the chosen coupon will be activate for only the customers that do not have any orders. <u>Regular Input: Checking the box.</u>";
    $content .= "<b><li>Number of Orders:</li></b>This restriction is selected with #number, the chosen coupon will be activate for only the customers that have enough #number of orders. Example: 7 Orders. If a customer has 5 orders for now, customer will see this global coupon as deactive until she/he has 7 orders. <u>Regular Input: Positive Integer</u>";
    $content .= "<b><li>Amount of Orders:</li></b>This restriction is selected with #amount, the chosen coupon will be activate for only the customers that have enough #amount of orders. Example: 300 USD. If a customer has several orders with total amount of 250 USD, customer will see this global coupon as deactive until she/he has 300 USD total amount. <u>Regular Input: Positive Integer</u>";
    $content .= "<b><li>Special For You:</li></b>You may define a global coupon that can be only seen by the customers that you want. Other customer will not see this global coupon. <u>Regular Input: test@test.com,test2@test2.com,test3@test3.com</u>";
    $content .= "<b><li>Number of Reviews:</li></b>This restriction is selected with #number, the chosen coupon will be activate for only the customers that have enough #number of reviews. Example: 5 Reviews (Product or Post Comment&Rating). If a customer has 3 reviews for now, customer will see this global coupon as deactive until she/he has 5 reviews. <u>Regular Input: Positive Integer</u>";
    $content .= "<b><li>Activate Date Interval:</li></b>You may define a global coupon that will be activated between the X and Y dates. Example: 30.06.2035-25.12.2035, then customers will see this global coupon as deactive until the starting date. <u>Regular Input: DD.MM.YYYY-DD.MM.YYYY</u>";
    $content .= "</div>";
    return $content;
}

//read me part 2 for submenu - coupon operations
function readme_part_2()
{
    $content = "<div class='helps'>";
    $content .= "<li><b>You can create Global Coupons from Coupon Operations section or remove a created one:</b></li>";
    $content .= "</ul>";
    $content .= "<ol><b><li>Create Global Coupon:</li></b>";
    $content .= "Coupon creation is similar to the standard Woocommerce coupon creation, except the Global Coupons are created with a prefix 'GC_' to separate them from the usual coupons.";
    $content .= "<b><li>Remove Global Coupon:</li></b>";
    $content .= "You may remove an existing Global Coupon from the list. Be careful when you decide to use the remove operation, since you may lose some order information after this operation.";
    $content .= "<b><li>Extra:</li></b>";
    $content .= "You do not have to use this section to create/remove Global Coupons, you can easily create any standard Woocommerce Coupon with a prefix 'GC_' and use it as a Global Coupon. ";
    $content .= "</div>";
    return $content;
}
?>