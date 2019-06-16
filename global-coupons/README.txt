=== Global Coupons for Woocommerce ===
Contributors: mrebabi
Author URI: https://github.com/MrEbabi
Tags: woocommerce, woocommerce coupons, woocommerce coupon, woocommerce coupon plugin, dynamic coupons, global coupons, global coupons for woocommerce, coupon plugin, extended coupons, coupon features
Requires at least: 3.1
Tested up to: 5.2.1
Stable tag: 1.2.1
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: global-coupons-for-woocommerce

Generate availability-restricted WooCommerce coupons and let customers to see&use coupons on My Account.

== Description ==
* PUBLISH A WOOCOMMERCE COUPON ONLY ONCE AND LET THIS COUPON TO UPDATE ITSELF AUTOMATICALLY FOR EVERY CUSTOMER DEPENDING ON THE GLOBAL COUPON RESTRICTION


* SHOW GLOBAL COUPONS TO CUSTOMERS ON MY ACCOUNT / COUPONS PAGE WITH THE COUPON RESTRICTION AND THE ACTIVENESS OF THE COUPON FOR THIS CUSTOMER


* TRACK THE USAGE AND ACTIVENESS REPORT OF THE GLOBAL COUPONS


* SEE THE ORDERS THAT GLOBAL COUPONS ARE USED


**Generate availability-restricted WooCommerce coupons and let customers to see&use coupons on My Account.**

Global Coupons are customizated WooCommerce coupons which have several restriction options and regarding to those restrictions, the coupons are available for the customers. Customers can check the published Global Coupons on the My Account - Coupons part and see if a coupon is Active or Deactive. If a coupon is Active for customer, then customer can directly apply the coupon if their cart is not empty.

**Global Coupon Restrictions for Woocommerce Coupons**

* First Order 
* Number of Orders
* Amount of Orders
* Special For You
* Number of Reviews
* Activate Date Interval

**---Global Coupons Section---**

You can edit your existing coupons from Global Coupons section to restrict them selecting the properties:

**First Order:**
When this restriction is selected, the chosen coupon will be activate for only the customers that do not have any orders. Regular Input: Checking the box.

**Number of Orders:**
This restriction is selected with #number, the chosen coupon will be activate for only the customers that have enough #number of orders. Example: 7 Orders. If a customer has 5 orders for now, customer will see this global coupon as deactive until she/he has 7 orders. Regular Input: Positive Integer

**Amount of Orders:**
This restriction is selected with #amount, the chosen coupon will be activate for only the customers that have enough #amount of orders. Example: 300 USD. If a customer has several orders with total amount of 250 USD, customer will see this global coupon as deactive until she/he has 300 USD total amount. Regular Input: Positive Integer

**Special For You:**
You may define a global coupon that can be only seen by the customers that you want. Other customer will not see this global coupon. Regular Input: test@test.com,test2@test2.com,test3@test3.com

**Number of Reviews:**
This restriction is selected with #number, the chosen coupon will be activate for only the customers that have enough #number of reviews. Example: 5 Reviews (Product or Post Comment&Rating). If a customer has 3 reviews for now, customer will see this global coupon as deactive until she/he has 5 reviews. Regular Input: Positive Integer

**Activate Date Interval:**
You may define a global coupon that will be activated between the X and Y dates. Example: 30.06.2035-25.12.2035, then customers will see this global coupon as deactive until the starting date. Regular Input: DD.MM.YYYY-DD.MM.YYYY

**---Coupon Operations Section---**

You can create Global Coupons from Coupon Operations section or remove a created one:

**Create Global Coupon:**
Coupon creation is similar to the standard Woocommerce coupon creation, except the Global Coupons are created with a prefix 'GC_' to separate them from the usual coupons.

**Remove Global Coupon:**
You may remove an existing Global Coupon from the list. Be careful when you decide to use the remove operation, since you may lose some order information after this operation.

**Extra:**
You do not have to use this section to create/remove Global Coupons, you can easily create any standard Woocommerce Coupon with a prefix 'GC_' and use it as a Global Coupon.

**---Preview Section---**

**This is how the global coupons will be shown in the user side.**

Be aware that, the Active/Deactive part is depending on the user account - in this case your account! Also note that, if the global coupon is not defined (blank comment/condition) then users will not see that coupon in the table but admin can. You can also check this preview by visiting My Account page.

**---Reports Section---**

1. Admin can check the Activation Report of Global Coupons.
2. Admin can check the Order Report of Global Coupons.

**---Settings Section---**

You can customize the all text fields, background color and text color in the table header to provide better view for your customers.

**To ask new properties or report bugs, kindly inform globalcoupons@mrebabi.com**

== Installation ==
1. Upload the entire 'global-coupons-for-woocommerce' folder to the '/wp-content/plugins/' directory or upload as a zip file then extract to the '/wp-content/plugins/'
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your admin bar to see new section: Global Coupons.
4. Checkout detailed readme parts inside submenus and enjoy the plugin!

== Frequently Asked Questions ==
= Does this plugin work with newest Wordpress and Woocommerce version and also older versions? =
Yes, the plugin is tested with Wordpress 5.2.1 and Woocommerce 3.6.4 and works fine. Yet, you are welcome to inform us about any bugs so we can fix them.

= Can we still use standard Woocommerce Coupons while using this plugin? =
Yes, the Global Coupons have a prefix (GC_) to make a difference from standard coupons, so you can create any coupon code which is not starting with "GC_" and use it as usual.

= Does this plugin support all languages? =
The plugin itself does not support other languages except English, but you can edit all text fields from Settings page to show the strings in your language.

= I need another restriction for my business. Can you improve your plugin for this special request? =
Since your need may become a need for someone else in future, of course, we will be happy to develop your request and add a new restriction to Global Coupons. Please inform us by e-mail.

== Screenshots ==
1. Global Coupon Creation (admin side)
2. Global Coupon Deletion (admin side)
3. Global Coupon Restriction (admin side)
4. Global Coupon Admin Preview (admin side)
5. Global Coupon Admin Reports (admin side)
6. Global Coupon Admin Settings (admin side)
7. Global Coupons at My Account Page (user side)
8. User Applies Global Coupon to cart (user side)
9. Applied Global Coupon to cart (user side)

== Changelog ==
**=1.2.1=**
-If no Global Coupons are defined, then show a notification in Global Coupons page (admin-side), Preview page (admin-side) and My Account - Coupons page (user-side).
-Small bug fixes.

**=1.2.0=**
-New Feature: Settings Section for Admin Panel
-Settings for customization of text fields, background color and text color in user-side.
-Responsive table for my account page.
-Small bug fixes.

**=1.1.3=**
-Despite any used global coupon cannot be used again by same user, it seems like Active. This bug is fixed.

**=1.1.2=**
-Small bug fixes
-README and Description is more detailed now.

**=1.1.1=**
-Small bug fixes

**=1.1.0=**
-New Feature: Report Section for Admin Panel
-Activation Report of Global Coupons and Order Report of Global Coupons can be seen in the Reports.

**=1.0.1=**
-Description and FAQ are re-written and whole README re-designed for a better view.
-Woocommerce activation is required to install&activate the Global Coupons for Woocommerce now.

**=1.0.0=**
-Hello World. This is the first version of the Global Coupons for Woocommerce.
-Initialized the source code.
-So it begins...