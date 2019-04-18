<?php
class Quake_api {
    public $plugin_options = [];
    public $offset_date; //how far back from current time should we fetch data?
    public $current_date;    //just saves current time for easy reference elsewhere

    public function __construct() {
        $this->plugin_options = get_option('quake_plugin_options');
        $this->offset_date    = $this->get_from_date();
        $this->current_date   = date('c');
        //point 'quake_blogger_cron' to the 'query_quake_api' function - this function will execute as per the cron event
        add_action( 'quake_blogger_cron', array( $this, 'query_quake_api' ) );
    }
/*
*
*/
    public function query_quake_api() {
        //https://earthquake.usgs.gov/fdsnws/event/1/query?format=csv&updatedafter=2019-03-25T00:00:00
        $data              = array( 'format' => 'geojson', 'updatedafter' => $this->offset_date );
        $response          = wp_remote_get($this->plugin_options['api_url'], array( 'body' => $data ));
        $formatted_results = $this->format_api_results_for_post($response);
        //write_to_post($formattedResults);
        file_put_contents('/var/www/quake_blogger/api_data.json', print_r($response['body'], true));

    }
/*
 *
 */
    public function format_api_results_for_post($response) {
        $decoded_response = json_decode($response['body']);
        $post_data_arr     = ['post_title' => $this->generate_post_title($decoded_response)];
        foreach($decoded_response['features'] as $event => $key) {
            //cycle over all events, concatenate to variable and write as body afterwards
        }
        file_put_contents('/var/www/quake_blogger/post_data_array.php', print_r($post_data_arr, true));
        return $post_data_arr;
    }
/*
 * decorator
 */
    public function generate_post_title($decoded_response) {
        $postTitle = $decoded_response->metadata->title." data between $this->offset_date and $this->current_date (".$decoded_response->metadata->count." events logged)";
        return $postTitle;
    }
    public function write_to_post($response) {

    }
/*
 *
 */
    private function get_from_date () {
        $from_date = strtotime("- ".$this->plugin_options['api_frequency']." ".$this->plugin_options['api_period'], time());
        return date('c', $from_date); //reformats relative date to ISO8601 format & return
    }
}