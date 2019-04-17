<?php

class Quake_api {

 // $frequency expects format: ['interval' => 1, 'unit' => 'months', 'tense' => '-']
    public function __construct($frequency) {
        $this->api_url         = 'https://earthquake.usgs.gov/fdsnws/event/1/query?';
        //these should be passed as params
        $this->frequency       = $frequency;
        //point 'quake_blogger_cron' to the 'check_quake_api' function - this function will execute as per the cron event
        add_action( 'quake_blogger_cron', array( $this, 'check_quake_api' ) );
    }
/*
*
*/
    public function check_quake_api() {
        //https://earthquake.usgs.gov/fdsnws/event/1/query?format=csv&updatedafter=2019-03-25T00:00:00
          $fromDate = $this->get_from_date();
          $data = array( 'format' => 'geojson', 'updatedafter' => $fromDate );
        $response = wp_remote_get( $this->api_url, array( 'body' => $data ));
        file_put_contents('/var/www/quake_blogger/api_data.json', print_r($response['body'], true));
    }
/*
 *
 */
    private function get_from_date () {
        $fromDate = strtotime($this->frequency['tense']." ".$this->frequency['interval']." ".$this->frequency['unit'], time());
        return date('c', $fromDate); //reformats relative date to ISO8601 format & return
    }
}