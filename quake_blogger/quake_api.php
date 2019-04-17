<?php

class Quake_api {
    public function __construct() {
        $this->api_url         = 'https://earthquake.usgs.gov/fdsnws/event/1/query';
        //point 'quake_blogger_cron' to the 'check_quake_api' function - this function will execute as per the cron event
        add_action( 'quake_blogger_cron', array( $this, 'check_quake_api' ) );
    }
/*
*
*/
    public function check_quake_api() {
        //https://earthquake.usgs.gov/fdsnws/event/1/query?format=csv&updatedafter=2019-03-25T00:00:00
          $fromDate = $this->get_from_date();
//        $data = array( 'format' => 'geojson', 'updatedafter' => $fromDate );
//        $response = wp_remote_post( $this->api_url, array( 'data' => $data ));
        file_put_contents('/var/www/quake_blogger/crontab.txt', $fromDate, FILE_APPEND);
    }
/*
 *
 */
    private function get_from_date () {
        return $datetime = date('c', time());
    }
}