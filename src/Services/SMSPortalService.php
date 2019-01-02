<?php

namespace Bishopm\Churchnet\Services;

use Bishopm\Churchnet\Services\SMSInterface;

class SMSPortalService implements SMSInterface
{

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->url = 'https://rest.smsportal.com';
        $ch = curl_init( );
        $headers = array(
            'Content-Type:application/json',
            'Authorization:Basic '. base64_encode("$this->username:$this->password")
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt ( $ch, CURLOPT_URL, $this->url . '/Authentication' );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        // Allow cUrl functions 20 seconds to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Wait 10 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        $data = json_decode(curl_exec( $ch ));
        $this->token = $data->token;
        curl_close( $ch );
    }
    
    public function send_message ($messages) {
        $ch = curl_init( );
        $headers = array(
            'Content-Type:application/json',
            'Authorization:Bearer ' . $this->token
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt ( $ch, CURLOPT_URL, $this->url . '/bulkmessages' );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode(array('Messages'=>$messages)) );
        // Allow cUrl functions 20 seconds to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Wait 10 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        $output = array();
        $output['server_response'] = curl_exec( $ch );
        $curl_info = curl_getinfo( $ch );
        $output['http_status'] = $curl_info[ 'http_code' ];
        curl_close( $ch );
        if ($output['http_status'] != 201) {
          return "Error sending.  HTTP status " . $output['http_status'] . " Response was " .$output['server_response'];
        } else {
          return "Response " . $output['server_response'];
          // Use json_decode($output['server_response']) to work with the response further
        }
    }

    public function get_credits () {
        $ch = curl_init( );
        $headers = array(
            'Content-Type:application/json',
            'Authorization:Bearer ' . $this->token
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt ( $ch, CURLOPT_URL, $this->url . '/Balance' );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        // Allow cUrl functions 20 seconds to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Wait 10 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        $output = json_decode(curl_exec( $ch ));
        curl_close( $ch );
        return $output->balance;
    }

    
}
