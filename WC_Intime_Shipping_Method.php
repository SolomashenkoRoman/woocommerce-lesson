<?php


/**
* 
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( ! class_exists( 'WC_Intime_Shipping_Method' ) ) {
	class WC_Intime_Shipping_Method extends WC_Shipping_Method
	{
		
		 /**
         * Constructor for your shipping class
         *
         * @access public
         * @return void
         */
        public function __construct() {
            $this->id  = 'intime_shipping_method';
          
            $this->method_title = __( 'Intime Shipping Method' );
            $this->method_description = __( 'Description of your shipping method' ); //


            $this->availability = 'including';
			$this->countries = array(
			    'US', // Unites States of America
			    'CA', // Canada
			    'UA', //    Ukraine
			    );
           
            $this->init();

          

            $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
            $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Intime Shipping Method' );
        }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init() {
            // Load the settings API
            $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
            $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

            // Save settings in admin if you have any defined
            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
        }


        /**
         * Define settings field for this shipping
         * @return void 
         */
        function init_form_fields() { 

            // We will add our settings here
                $this->form_fields = array(
 
			         'enabled' => array(
			              'title' => __( 'Enable'),
			              'type' => 'checkbox',
			              'description' => __( 'Enable this shipping.'),
			              'default' => 'yes'
			              ),
			 
			         'title' => array(
			            'title' => __( 'Title'),
			              'type' => 'text',
			              'description' => __( 'Title to be display on site'),
			              'default' => __( 'Intime Shipping Method')
			              ),
			         'coast' => array(
			            'title' => __( 'Coast'),
			              'type' => 'text',
			              'description' => __( 'Coast'),
			              'default' => 10
			              ),
			 
			         );

        }

        /**
         * calculate_shipping function.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        public function calculate_shipping( $package = array() ) {
            // This is where you'll add your rates
            
        	//error_log(print_r($package, true));

        	$weight = 0;

            foreach ( $package['contents'] as $item_id => $values ) { 
               $_product = $values['data']; 
               $weight = $weight + $_product->get_weight() * $values['quantity']; 
           }

           	$country = $package["destination"]["country"]; 
            $countryZones = array(
		        'US' => 1, // Unites States of America
			    'CA' => 2, // Canada
			    'UA' => 3, //    Ukraine
		    );
 
	    	$zonePrices = array(
		        1 => 10,
		        2 => 30,
		        3 => 50,
		       
	        );

         
	    	$coast = $zonePrices[$countryZones[$country]];

            $rate = array(
                'id' => $this->id,
                'label' => $this->title,
                'cost' => $this->settings['coast'] * ($weight/10) * $coast,
                'calc_tax' => 'per_item'
            );

            // Register the rate
            $this->add_rate( $rate );

        }
	}
}