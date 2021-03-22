<?php

/*Add below code in your functions.php file*/

function api_that_check_pincode_aspersate($country,$state,$postcode){
    $status_st = '1';
    $data=file_get_contents('http://postalpincode.in/api/pincode/'.$postcode);
    $data=json_decode($data);
    if(isset($data->PostOffice['0'])){
        $status_st = '1';
        $api_city = $data->PostOffice['0']->Taluk;
        $api_state = strtolower( trim( $data->PostOffice['0']->State ) );
        $api_country = strtolower( trim( $data->PostOffice['0']->Country ) );
        if($api_state != $state ||  $api_country != $country){
            $status_st = '0';
        }
    }else{
        $status_st = '0';
    }
    return $status_st;
}

add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');
function my_custom_checkout_field_process() {
    if ( !empty($_POST['billing_country']) && !empty($_POST['billing_state']) && !empty($_POST['billing_postcode'])){
        $country = $_POST['billing_country'];
        $state = $_POST['billing_state'];
        $billing_pincode = trim($_POST['billing_postcode']);
        if($country=="IN"){
            $billing_state_name = strtolower( trim( WC()->countries->get_states( $country )[$state] ) );
            $billing_country_name = strtolower( trim(  WC()->countries->countries[$country] ) );
            $b_st = api_that_check_pincode_aspersate($billing_country_name,$billing_state_name,$billing_pincode);
            if($b_st == '0'){
                    wc_add_notice( "<strong>Billing Postcode / ZIP</strong> Your entered pincode is incorrect for state <strong>".WC()->countries->get_states( $country )[$state] ."</strong>","error");
            }
        }
    }
    if ( $_POST['ship_to_different_address'] == 1 && !empty($_POST['shipping_country']) && !empty($_POST['shipping_state']) && !empty($_POST['shipping_postcode'])){
        $shipping_country = $_POST['shipping_country'];
        $shipping_state = $_POST['shipping_state'];
        $shipping_postcode = trim($_POST['shipping_postcode']);
        if($shipping_country=="IN"){
            $shipping_state_name = strtolower( trim( WC()->countries->get_states( $shipping_country )[$shipping_state] ) );
            $shipping_country_name = strtolower( trim(  WC()->countries->countries[$shipping_country] ) );
           $s_st = api_that_check_pincode_aspersate($shipping_country_name,$shipping_state_name,$shipping_postcode);
            if($s_st == '0'){
                    wc_add_notice( "<strong>Shipping Postcode / ZIP</strong> Your entered pincode is incorrect for state <strong>".WC()->countries->get_states( $shipping_country )[$shipping_state] ."</strong>","error");
            }
        }
    }
}
