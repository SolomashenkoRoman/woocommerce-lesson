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

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

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



}

