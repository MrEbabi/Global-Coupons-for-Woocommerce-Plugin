<?php
/*
Plugin Name: Global Coupons for Woocommerce
Description: Generate availability-restricted WooCommerce coupons that can be seen by customers from My Account page.
Version: 1.0.0
Author: Mr.Ebabi
Author URI: http://mrebabi.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: global-coupons
*/

if(!defined('ABSPATH'))
{
    die;
}

defined('ABSPATH') or die('You shall not pass!');

if(!function_exists('add_action'))
{
    echo "You shall not pass!";
    exit;
}

function global_coupons_settings_link( $links ) 
{
    if(!is_admin()) exit();

	$links[] = '<a href="' .
		admin_url( 'admin.php?page=global-coupons-admin-page' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}

//css for admin panel
function global_coupons_admin_css() 
{
    wp_register_style('global-coupons-admin', plugins_url('css/global-coupons.css',__FILE__ ));
    wp_enqueue_style('global-coupons-admin');
}

add_action( 'admin_init','global_coupons_admin_css');

//css for user interface
function global_coupons_user_css()
{
    wp_enqueue_style('global-coupons-user', plugins_url('css/global-coupons.css',__FILE__ ));
}

add_action('wp_enqueue_scripts','global_coupons_user_css');


if(!class_exists('GlobalCoupons'))
{
    class GlobalCoupons
    {
        function __construct()
        {
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'global_coupons_settings_link');
            require_once(dirname(__FILE__) . '/core/global-coupon-helper.php');
            require_once(dirname(__FILE__) . '/core/global-coupon-operations.php');
            require_once(dirname(__FILE__) . '/core/global-coupon-readme.php');
            require_once(dirname(__FILE__) . '/core/global-coupon-admin.php');
            require_once(dirname(__FILE__) . '/core/global-coupon-my-account.php');
        }
    }
}

if(class_exists('GlobalCoupons'))
{
    $globalCoupons = new GlobalCoupons();
}

register_activation_hook( __FILE__, array($globalCoupons, '__construct'));
