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



add_action( 'init', 'drdb_discogs_block_register_scripts' );
add_action( 'init', 'drdb_add_shortcode' );
 
function drdb_add_shortcode() {
    add_shortcode( 'discogs-block', 'drdb_render_releases' );
}

function drdb_shortcode_style_func() {
    wp_enqueue_style( 'shortcode_styles', plugin_dir_url( __FILE__ ) . '/build/style-index.css');
  }
  add_action( 'init', 'drdb_shortcode_style_func' );



    function drdb_render_releases() {
        $options = get_option( 'drdb_discogs_block_options' );
        if (!isset($options['username']) && !isset($options['token']) || $options['token'] == '' && $options['username'] == '') {
            return '<div class="drdb-discogs-block-error">
            <p><b>Discogs Block</b>:<br> You must enter both a user name and token in the Discogs Block settings</p>
            </div>';
        }
        if (!isset($options['token']) || $options['token'] == ''){
            return '<div class="drdb-discogs-block-error">
            <p><b>Discogs Block</b>:<br> You must enter a token in the Discogs Block settings</p>
            </div>';
        }  else if (isset($options['token'])) {
            $token = $options['token'];
            }
        if (!isset($options['username']) || $options['username'] == '') {
            return '<div class="drdb-discogs-block-error">
            <p><b>Discogs Block</b>:<br> You must enter a Discogs.com username in the Discogs Block settings</p>
            </div>';
        } else  if (isset($options['username'])) {
            $username = $options['username'];
        }        

        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            
                $page = 1;
                $limit = 6;
            
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
                return 'false';
            }
            
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );
            
            
            $output = '<div id="drdb-discogs-block-parent" class="drdb-discogs-block-parent">
                        <div id="#drdb-discogs-container" class="drdb-discogs-container">';
            
            foreach ($data['releases'] as $item){
                $albumName = $item['basic_information']['title'];
                $artistName = $item['basic_information']['artists'][0]['name'];
                $albumCover = $item['basic_information']['thumb'];
                $releaseYear = $item['basic_information']['year'];
                $format = $item['basic_information']['formats'][0]['name'];
                $output .= '<div class="discogs-card">
                                <div class="album-title-div">
                                    <h4>' . $albumName . '</h4>
                                </div>
                                <div><img src="' . $albumCover . '"></div>
                                <h5>' . $artistName . '</h5>
                                <p>Format: ' . $format . '</p>
                                <p>Released: ' . $releaseYear . '</p>
                                </div>';
            }
            $output .= '</div></div>';

            return  $output;
        
        } 
            
        
        
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
        'sanitize_callback' => 'drdb_discogs_block_validate_username',
        'default' => ''
    )
    );

    add_settings_field( 'drdb_discogs_block_username',
    '<br>Your Discogs Username',
    'drdb_discogs_block_settings_username',
    'drdb_discogs_block',
    'drdb_discogs_block_main'
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



    function drdb_discogs_block_section_text() {
        echo '<p>Use the Discogs Block in the WordPress Block Editor. <br> If using a page builder or classic editor, use shortcode: <b>[discogs-block]</b></p>';
    }

    function drdb_discogs_block_settings_token() {
        $options = get_option( 'drdb_discogs_block_options' );
        if (isset($options['token'])) {
        $token = $options['token'];
        } else {
            $token = '';
        }
   
        echo '<input id="token" name="drdb_discogs_block_options[token]"
        type="text" value="' . esc_attr( $token ) . '" /><p>Enter a valid Discogs.com token. You can generate a new token <a href="https://www.discogs.com/settings/developers" target=_blank>here</a>.</p>';

    }

    function drdb_discogs_block_settings_username() {
        $options = get_option( 'drdb_discogs_block_options' );
        if (isset($options['username'])) {
        $username = $options['username'];
        } else {
            $username = '';
        }
        
        echo '<br><input id="username" name="drdb_discogs_block_options[username]"
        type="text" value="' . esc_attr( $username ) . '" /><p>Enter a valid Discogs.com user name. If you do not already have one, you can create a new one <a href="https://accounts.discogs.com/register" target=_blank>here</a>, and be sure to add releases to your collection.</p>';

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
