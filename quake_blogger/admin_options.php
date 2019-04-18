<?php
class Quake_blogger_settings {
    private $options;
/*
 *
 */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
/*
 *
 */
    public function add_plugin_page() {
        add_options_page(
            'Quake API Settings',
            'Quake API Settings',
            'manage_options',
            'quake-api-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }
/*
 *
 */
    public function page_init() {
        register_setting(
            'my_option_group',
            'quake_plugin_options',
            array( $this, 'sanitize' )
        );
//API SETTINGS
        add_settings_section(
            'api_setting_section_id',
            'API Settings', 
            array( $this, 'print_api_section_info' ),
            'quake-api-setting-admin'
        );
////CRON SETTINGS
//        add_settings_section(
//            'cron_setting_section_id',
//            'CRON Settings',
//            array( $this, 'print_cron_section_info' ),
//            'quake-api-setting-admin'
//        );
//API FIELDS
        add_settings_field(
            'api_url', 
            'API URL',
            array( $this, 'api_url_callback' ),
            'quake-api-setting-admin',
            'api_setting_section_id' 
        );
        add_settings_field(
            'api_frequency', 
            'API Frequency',
            array( $this, 'api_frequency_callback' ),
            'quake-api-setting-admin',
            'api_setting_section_id' 
        );
        add_settings_field(
            'api_period',
            'API Period',
            array( $this, 'api_period_callback' ),
            'quake-api-setting-admin',
            'api_setting_section_id'
        );
    }
/*
*
*/
    public function create_admin_page() {
        $this->options = get_option( 'quake_plugin_options' );
        ?>
        <div class="wrap">
            <h1>Quake API Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'my_option_group' );
                do_settings_sections( 'quake-api-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
/*
*
*/
    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['api_url'] ) )
            $new_input['api_url'] = sanitize_text_field( $input['api_url'] );

        if( isset( $input['api_frequency'] ) )
            $new_input['api_frequency'] = absint( $input['api_frequency'] );

        if( isset( $input['api_period'] ) )
            $new_input['api_period'] = sanitize_text_field( $input['api_period'] );

        return $new_input;
    }
/*
 * 
 */
    public function print_api_section_info() {
        print 'API Frequency and Period will be used to set how far back from now to fetch data:';
    }
/*
 *
 */
//    public function print_cron_section_info() {
//        print 'Set how often to check API for new data:';
//    }
/*
* 
*/
    public function api_url_callback() {
        printf(
            '<input type="text" id="api_url" name="quake_plugin_options[api_url]" value="%s" />',
            isset( $this->options['api_url'] ) ? esc_attr( $this->options['api_url']) : ''
        );
    }
/*
 * 
 */
    public function api_frequency_callback() {
        printf(
            '<input type="number" min="1" max="60" id="api_frequency" name="quake_plugin_options[api_frequency]" value="%s" />',
            isset( $this->options['api_frequency'] ) ? esc_attr( $this->options['api_frequency']) : ''
        );
    }
/*
 * Dropdown to allow period selection...
 */
    public function api_period_callback() {
	$options = $this->options['api_period'];
	$items   = array("Years", "Months", "Days", "Hours", "Minutes");
	echo "<select id='api_period' name='quake_plugin_options[api_period]'>";
	foreach($items as $item) {
		$selected = ($this->options['api_period']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
    }
}

if( is_admin() )
    $my_settings_page = new Quake_blogger_settings();