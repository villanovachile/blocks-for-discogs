<?php
/**
 * Plugin Name:       Discogs Block
 * Description:       Displays a collection from discogs.com
 * Requires at least: 6.0
 * Requires PHP:      7.0
 * Version:           1.0a
 * Author:            Daniel Rodriguez
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       discogsblock
 *
 * @package           discogs-block
 */

 
 // Register drdb-discogs-block.js
 function drdb_discogs_block_register_scripts() {
    wp_register_script(
   'drdb_script',
   plugin_dir_url( __FILE__ ) . 'drdb-discogs-block.js', array(),
   '1.0.0',
   true
   ); 
}


// initializes registration function for ccnj.js
add_action( 'init', 'drdb_discogs_block_register_scripts' );

    function drdb_render_releases() {
        wp_enqueue_script( 'jquery' );
        wp_localize_script( 'drdb_script', 'discogs_fetch',
        array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_script( 'drdb_script' );
        return '
        <div id="drdb-discogs-block-parent" class="drdb-discogs-block-parent">
        </div>
        ';
    }

	function drdb_discogs_block_init() {
		register_block_type_from_metadata( __DIR__ , array(
			'render_callback' => 'drdb_render_releases'
		) );
		
	}

    add_action( 'init', 'drdb_discogs_block_init' );

    add_action('wp_ajax_drdb_discogs_fetch', 'drdb_discogs_fetch');
    add_action('wp_ajax_nopriv_drdb_discogs_fetch', 'drdb_discogs_fetch');

    function drdb_discogs_fetch(){
        $options = get_option( 'drdb_discogs_block_options' );
        if (isset($options['token'])) {
        $token = $options['token'];
        } else {
            $token = '';
        }
        if (isset($options['username'])) {
            $username = $options['username'];
            } else {
                $username = '';
            }
        if(isset($_REQUEST)){
            $page = $_REQUEST['page'];
            $limit = $_REQUEST['limit'];
    
            
    
            $response = wp_remote_get(
                esc_url_raw( 'https://api.discogs.com/users/' . $username .'/collection/folders/0/releases?page='.$page.'&per_page='.$limit ),
                array(
                    'headers' => array(
                        'referer' => home_url(),
                        'Authorization' => 'Discogs token='.$token
                    )
                )
            );
            
        if ( is_wp_error($response) ) {
            return false;
        }

            $body = wp_remote_retrieve_body( $response );
           $data = json_decode( $body );
    
           wp_send_json_success( $data );
        
    
        }
        die();
    }


add_action ('admin_menu', 'drdb_add_settings_menu');

    function drdb_add_settings_menu() {
            add_menu_page ('Discogs Block Settings', 'Discogs Bock', 'manage_options', 'drdb_discogs_block', 'drdb_discogs_block_option_page', 'dashicons-admin-site', 99);
    }

    // Create the option page
function drdb_discogs_block_option_page() {
    ?>
    <div class="wrap">
        <h2> Discogs Block</h2>
        <form action="options.php" method="post">
            <?php 
            settings_fields ('drdb_discogs_block_options');
            do_settings_sections ('drdb_discogs_block');
            submit_button ('Save Changes', 'primary');
            ?>

        </form>
    <?php   
}
?>
<?php

add_action('admin_init', 'drdb_load_menu');

function drdb_load_menu() {    

    add_settings_section( 
        'drdb_discogs_block_main', 
        'Settings',
        'drdb_discogs_block_section_text', 
        'drdb_discogs_block' 
    );

	register_setting ('drdb_discogs_block_options', 'drdb_discogs_block_options', 
		array (
			'type' => 'string',
			'sanitize_callback' => 'drdb_discogs_block_validate_token',
			'default' => ''
		)
	);
    add_settings_field( 'drdb_discogs_block_token',
        'Your Discogs API Token',
        'drdb_discogs_block_settings_token',
        'drdb_discogs_block',
        'drdb_discogs_block_main'
    );

	register_setting ('drdb_discogs_block_options', 'drdb_discogs_block_options', 
		array (
			'type' => 'string',
			'sanitize_callback' => 'drdb_discogs_block_validate_username',
			'default' => ''
		)
	);

	add_settings_field( 'drdb_discogs_block_username',
	'Your Discogs Username',
	'drdb_discogs_block_settings_username',
	'drdb_discogs_block',
	'drdb_discogs_block_main'
	);

    function drdb_discogs_block_section_text() {
        echo '<p>Use shortcode: <b>[drdb]</b></p>';
    }

    function drdb_discogs_block_settings_token() {
        $options = get_option( 'drdb_discogs_block_options' );
        if (isset($options['token'])) {
        $token = $options['token'];
        } else {
            $token = '';
        }
   
        echo "<input id='token' name='drdb_discogs_block_options[token]'
        type='text' value='" . esc_attr( $token ) . "' />";

    }

    function drdb_discogs_block_settings_username() {
        $options = get_option( 'drdb_discogs_block_options' );
        if (isset($options['username'])) {
        $username = $options['username'];
        } else {
            $username = '';
        }
        
        echo "<input id='username' name='drdb_discogs_block_options[username]'
        type='text' value='" . esc_attr( $username ) . "' />";

    }

    function drdb_discogs_block_validate_token( $input ) {
		
        $input['token'] = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            $input['token'] );
		return $input;

    }

    function drdb_discogs_block_validate_username( $input ) {
		
        $input['username'] = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            $input['username'] );
			return $input;
    }
	
}


