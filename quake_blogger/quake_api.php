<?php
class Quake_api {
    public $plugin_options = [];
    public $offset_date;  //how far back from current time should we fetch data?
    public $current_date; //just saves current time for easy reference elsewhere

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
        $data     = array( 'format' => 'geojson', 'updatedafter' => $this->offset_date );
        $response = wp_remote_get($this->plugin_options['api_url'], array( 'body' => $data ));

        $this->format_api_results_and_post($response);
    }
/*
 *
 */
    public function format_api_results_and_post($response) {
        $decoded_response = json_decode($response['body']);
	    $event_details    = '';
        $event_array      = [];
        foreach($decoded_response->features as $key => $event) {
	        $event_details .= '<li>'.$event->properties->title.'</li>';
            array_push($event_array, $event_details);
        }
        //we only want the first 3 items as excerpt
	    $event_excerpt  = '<ul>'.implode(' ', array_slice($event_array, 0, 3)).'</ul>';
	    $event_details .= "<ul>$event_details</ul>\n";

        $post_data_arr  = ['post_title'   => $this->generate_post_title($decoded_response),
                           'post_content' => $event_details,
                           'post_excerpt' => $event_excerpt,
                           'post_author'  => 1,
                           'post_status'  => 'publish'];
	    wp_insert_post($post_data_arr);

        return $post_data_arr;
    }
/*
 * decorators - returns in the following format: '2019-04-18 (10:20:25) - 2019-04-18 (22:20:25) (411 events logged)'
 */
    public function generate_post_title($decoded_response) {
        $offset_date  = $this->format_date_time($this->offset_date);
        $current_date = $this->format_date_time($this->current_date);
        $postTitle    = $decoded_response->metadata->title.": $offset_date - $current_date (".$decoded_response->metadata->count." events logged)";

        return $postTitle;
    }

    public function format_date_time($date) {
        $dateTime = explode('T', $date);
        $time     = explode('+', $dateTime[1]);

        return $dateTime[0]." (".$time[0].")";
    }
/*
 *
 */
    private function get_from_date () {
        $from_date = strtotime("- ".$this->plugin_options['api_frequency']." ".$this->plugin_options['api_period'], time());

        return date('c', $from_date); //reformats relative date to ISO8601 format & return
    }
}