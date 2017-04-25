

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( ! class_exists( 'WC_Delivery_Shipping_Method' ) ) {
    class WC_Delivery_Shipping_Method extends WC_Shipping_Method
    {
    	/**
         * Constructor for your shipping class
         *
         * @access public
         * @return void
         */
        public function __construct() {
            $this->id                 = 'delivery_shipping_method'; 
            $this->method_title       = __( 'Delivery');  
            $this->method_description = __( 'Custom Shipping Method for Delivery'); 


            $this->availability = 'including';
			$this->countries = array(
			    'UA', //    Ukraine
			    );

            $this->init();

            $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
            $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Delivery' );
        }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init() {
            // Load the settings API
            $this->init_form_fields(); 
            $this->init_settings(); 

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
			              'default' => __( 'Delivery')
			              ),
			         'coast' => array(
			            'title' => __( 'Coast'),
			              'type' => 'text',
			              'description' => __( 'Coast'),
			              'default' => 1000
			              ),
			 
			         );

        }

        /**
         * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        public function calculate_shipping( $package = array() ) {
            

            $country = $package["destination"]["country"];

            error_log(print_r($package["destination"], true));
        	//error_log(print_r($package, true));

        	$weight = 0;

            foreach ( $package['contents'] as $item_id => $values ) { 
               $_product = $values['data']; 
               $weight = $weight + $_product->get_weight() * $values['quantity']; 
           }

           error_log(print_r($weight, true));

            // We will add the cost, rate and logics in here
             $rate = array(
                'id' => $this->id,
                'label' => $this->title,
                'cost' => $this->settings['coast'] * $weight,
                'calc_tax' => 'per_item'
            );

            // Register the rate
            $this->add_rate( $rate );

           
            
        }
    }
}