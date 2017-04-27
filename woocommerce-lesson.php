<?php
/**
 * Plugin Name: Woocommerce Lesson
 * Plugin URI: https://github.com/SolomashenkoRoman/woocommerce-lesson
 * Description: Woocommerce Lesson
 * Version: 1.0.0
 * Author: WooCommerce
 * Author URI: http://woocommerce.com/
 * Developer: Solomashenko Roman
 * Developer URI: https://www.linkedin.com/in/solomashenkoroman/
 * Text Domain: woocommerce-lesson
 * Domain Path: /languages
 *
 * Copyright: © 2009-2015 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}



if (! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    function activation(){
        deactivate_plugins('woocommerce-lesson/woocommerce-lesson.php');
        wp_die('Error wordpress');
    }
    register_activation_hook( __FILE__, 'activation' );
}





if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {




    add_action( 'plugins_loaded', 'wc_les_my_init', 0 );

    function wc_les_my_init() {
        require_once dirname(__FILE__) . '/My_WC_Integration.php';
    }

    add_filter( 'woocommerce_integrations', 'add_woocommerce_integrations' );
    function add_woocommerce_integrations( $integrations ) {
        $integrations[] = 'My_WC_Integration';
        return $integrations;
    }


    add_action( 'woocommerce_api_callback', 'callback_handler' );
    function callback_handler(){
        error_log('Run callback');
    }

    // Include our Gateway Class and Register Payment Gateway with WooCommerce
    add_action( 'plugins_loaded', 'wc_les_liqpay_init', 0 );

    function wc_les_liqpay_init() {
        require_once dirname(__FILE__) . '/WC_LiqPay_Payment_Gateway.php';
    }

    add_filter( 'woocommerce_payment_gateways', 'add_liqpay_gateway_class' );
    function add_liqpay_gateway_class( $methods ) {
        //error_log(print_r($methods, true));
        $methods[] = 'WC_LiqPay_Payment_Gateway';
        return $methods;
    }


    // Add custom action links
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_les_liqpay_action_links' );
    function wc_les_liqpay_action_links( $links ) {
        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">'
            . __( 'Settings') . '</a>',
        );

        // Merge our new link with the default ones
        return array_merge( $plugin_links, $links );
    }









    function intime_validate_order( $posted )   {

        $packages = WC()->shipping->get_packages();

        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

    

        if( is_array( $chosen_methods ) && in_array( 'intime_shipping_method', $chosen_methods ) ) {

            foreach ( $packages as $i => $package ) {

                if ( $chosen_methods[ $i ] != "intime_shipping_method" ) {

                    continue;

                }

                //$WC_Delivery_Shipping_Method = new WC_Delivery_Shipping_Method();
                //$weightLimit = (int) $WC_Delivery_Shipping_Method->settings['weight'];

                $weightLimit = 100;
                $weight = 0;

                foreach ( $package['contents'] as $item_id => $values )
                {
                    $_product = $values['data'];
                    $weight = $weight + $_product->get_weight() * $values['quantity'];
                }

                $weight = wc_get_weight( $weight, 'kg' );

                if( $weight > $weightLimit ) {

                    $message = sprintf( __( 'Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'tutsplus' ), $weight, $weightLimit, $TutsPlus_Shipping_Method->title );

                    $messageType = "error";

                    if( ! wc_has_notice( $message, $messageType ) ) {

                        wc_add_notice( $message, $messageType );

                    }
                }
            }
        }
    }

    add_action( 'woocommerce_review_order_before_cart_contents', 'intime_validate_order' , 10 );
    add_action( 'woocommerce_after_checkout_validation', 'intime_validate_order' , 10 );

    // Создайте функцию для размещения своего класса
    function intime_shipping_method_init() {
        require_once dirname(__FILE__) . '/WC_Intime_Shipping_Method.php';
    }

    add_action( 'woocommerce_shipping_init', 'intime_shipping_method_init' );


    function add_intime_shipping_method( $methods ) {
        $methods['intime_shipping_method'] = 'WC_Intime_Shipping_Method';
        return $methods;
    }
    add_filter( 'woocommerce_shipping_methods', 'add_intime_shipping_method' );





    // Создайте функцию для размещения своего класса
    function your_shipping_method_init() {
        require_once dirname(__FILE__) . '/WC_Your_Shipping_Method.php';
    }

    add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );


    function add_your_shipping_method( $methods ) {
        $methods['your_shipping_method'] = 'WC_Your_Shipping_Method';
        return $methods;
    }
    add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );



     // Создайте функцию для размещения своего класса
    function delivery_shipping_method_init() {
        require_once dirname(__FILE__) . '/WC_Delivery_Shipping_Method.php';
    }

    add_action( 'woocommerce_shipping_init', 'delivery_shipping_method_init' );


    function add_delivery_shipping_method( $methods ) {
        $methods['delivery_shipping_method'] = 'WC_Delivery_Shipping_Method';
        return $methods;
    }
    add_filter( 'woocommerce_shipping_methods', 'add_delivery_shipping_method' );



     function delivery_validate_order( $posted )   {

        $packages = WC()->shipping->get_packages();

        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

        error_log(print_r($chosen_methods, true));

        if( is_array( $chosen_methods ) && in_array( 'delivery_shipping_method', $chosen_methods ) ) {

            foreach ( $packages as $i => $package ) {

                if ( $chosen_methods[ $i ] != "delivery_shipping_method" ) {

                    continue;

                }

                //$WC_Delivery_Shipping_Method = new WC_Delivery_Shipping_Method();
                //$weightLimit = (int) $WC_Delivery_Shipping_Method->settings['weight'];

                $weightLimit = 100;
                $weight = 0;

                foreach ( $package['contents'] as $item_id => $values )
                {
                    $_product = $values['data'];
                    $weight = $weight + $_product->get_weight() * $values['quantity'];
                }

                $weight = wc_get_weight( $weight, 'kg' );

                if( $weight > $weightLimit ) {

                    $message = sprintf( __( 'Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'tutsplus' ), $weight, $weightLimit, $TutsPlus_Shipping_Method->title );

                    $messageType = "error";

                    if( ! wc_has_notice( $message, $messageType ) ) {

                        wc_add_notice( $message, $messageType );

                    }
                }
            }
        }
    }

    add_action( 'woocommerce_review_order_before_cart_contents', 'delivery_validate_order' , 10 );
    add_action( 'woocommerce_after_checkout_validation', 'delivery_validate_order' , 10 );



    /*// Создайте функцию для размещения своего класса
    function tutsplus_shipping_method() {
        require_once dirname(__FILE__) . '/TutsPlus_Shipping_Method.php';
    }

    add_action( 'woocommerce_shipping_init', 'tutsplus_shipping_method' );


    function add_tutsplus_shipping_method( $methods ) {

        $methods['tutsplus_shipping_method'] = 'TutsPlus_Shipping_Method';

        error_log(print_r($methods, true));

        return $methods;
    }
    add_filter( 'woocommerce_shipping_methods', 'add_tutsplus_shipping_method' );*/

    // Создайте функцию для размещения своего класса
    function novaya_pochta_shipping_method_init() {
        require_once dirname(__FILE__) . '/WC_Novaya_Pochta_Shipping_Method.php';
    }

    add_action( 'woocommerce_shipping_init', 'novaya_pochta_shipping_method_init' );


    function add_novaya_pochta_shipping_method( $methods ) {
        $methods['novaya_pochta_shipping_method'] = 'WC_Novaya_Pochta_Shipping_Method';
        return $methods;
    }
    add_filter( 'woocommerce_shipping_methods', 'add_novaya_pochta_shipping_method' );


    function tutsplus_validate_order( $posted )   {

        $packages = WC()->shipping->get_packages();

        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

        if( is_array( $chosen_methods ) && in_array( 'tutsplus', $chosen_methods ) ) {

            foreach ( $packages as $i => $package ) {

                if ( $chosen_methods[ $i ] != "tutsplus" ) {

                    continue;

                }

                $TutsPlus_Shipping_Method = new WC_Novaya_Pochta_Shipping_Method();
                $weightLimit = (int) $TutsPlus_Shipping_Method->settings['weight'];
                $weight = 0;

                foreach ( $package['contents'] as $item_id => $values )
                {
                    $_product = $values['data'];
                    $weight = $weight + $_product->get_weight() * $values['quantity'];
                }

                $weight = wc_get_weight( $weight, 'kg' );

                if( $weight > $weightLimit ) {

                    $message = sprintf( __( 'Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'tutsplus' ), $weight, $weightLimit, $TutsPlus_Shipping_Method->title );

                    $messageType = "error";

                    if( ! wc_has_notice( $message, $messageType ) ) {

                        wc_add_notice( $message, $messageType );

                    }
                }
            }
        }
    }

    add_action( 'woocommerce_review_order_before_cart_contents', 'tutsplus_validate_order' , 10 );
    add_action( 'woocommerce_after_checkout_validation', 'tutsplus_validate_order' , 10 );



add_filter( 'woocommerce_get_sections_products', 'wcslider_add_section' );
function wcslider_add_section( $sections ) {
    $sections['wcslider'] = __( 'WC Slider', 'text-domain' );
    return $sections;
}
add_filter( 'woocommerce_get_settings_products', 'wcslider_all_settings', 20, 2 );
function wcslider_all_settings( $settings, $current_section ) {
    /**
     * Check the current section is what we want
     **/
    if ( $current_section == 'wcslider' ) {
        $settings_slider = array();
        // Add Title to the Settings
        $settings_slider[] = array( 'name' => __( 'WC Slider Settings', 'text-domain' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure WC Slider', 'text-domain' ), 'id' => 'wcslider' );
        // Add first checkbox option
        $settings_slider[] = array(
            'name'     => __( 'Auto-insert into single product page', 'text-domain' ),
            'desc_tip' => __( 'This will automatically insert your slider into the single product page', 'text-domain' ),
            'id'       => 'wcslider_auto_insert',
            'type'     => 'checkbox',
            'css'      => 'min-width:300px;',
            'desc'     => __( 'Enable Auto-Insert', 'text-domain' ),
        );
        // Add second text field option
        $settings_slider[] = array(
            'name'     => __( 'Slider Title', 'text-domain' ),
            'desc_tip' => __( 'This will add a title to your slider', 'text-domain' ),
            'id'       => 'wcslider_title',
            'type'     => 'text',
            'desc'     => __( 'Any title you want can be added to your slider with this option!', 'text-domain' ),
        );
        
        $settings_slider[] = array( 'type' => 'sectionend', 'id' => 'wcslider' );
        return $settings_slider;
    
    /**
     * If not, return the standard settings
     **/
    } else {
        return $settings;
    }
}


} else {

    add_action( 'admin_notices', 'maybe_display_admin_notices'  );
        function maybe_display_admin_notices () {
                echo '<div class="error fade"><p>Error woocommerce</p></div>' . "\n";
            }
      
}
