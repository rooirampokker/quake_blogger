<?php
class Quake_api {
    public $plugin_options = [];
 // $frequency expects format: ['interval' => 1, 'unit' => 'months', 'tense' => '-']
    public function __construct() {
        $this->plugin_options  = get_option('quake_plugin_options');
        //point 'quake_blogger_cron' to the 'check_quake_api' function - this function will execute as per the cron event
        add_action( 'quake_blogger_cron', array( $this, 'check_quake_api' ) );
    }

    /*
    *
    */
    public function check_quake_api() {
        //https://earthquake.usgs.gov/fdsnws/event/1/query?format=csv&updatedafter=2019-03-25T00:00:00
        $fromDate = $this->get_from_date();
        $data     = array( 'format' => 'geojson', 'updatedafter' => $fromDate );
        $response = wp_remote_get($this->plugin_options['api_url'], array( 'body' => $data ));
        //This is where we want to write the response to post
        file_put_contents('/var/www/quake_blogger/api_data.json', print_r($response['body'], true));
    }
/*
 *
 */
    private function get_from_date () {
        $fromDate = strtotime("- ".$this->plugin_options['api_frequency']." ".$this->plugin_options['api_period'], time());
        return date('c', $fromDate); //reformats relative date to ISO8601 format & return
    }
}