<?php
/**
 * Plugin Name:       Blocks for Discogs
 * Plugin URI: https://github.com/villanovachile/blocks-for-discogs
 * Description:       Displays a collection from discogs.com
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Version:           1.0.1
 * Author:            villanovachile
 * Author URI:        https://danielr.io
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       blocksfordiscogs
 *
 * @package           blocks-for-discogs
 */

 
 function drbfd_blocks_for_discogs_register_scripts() {
    wp_register_script(
   'drbfd_script',
   plugin_dir_url( __FILE__ ) . '/assets/js/drbfd-blocks-for-discogs.js', array(),
   '1.0.0',
   true
   ); 
}

add_action( 'init', 'drbfd_blocks_for_discogs_register_scripts' );
add_action( 'init', 'drbfd_add_shortcode' );
 
function drbfd_add_shortcode() {
    add_shortcode( 'blocks-for-discogs', 'drbfd_render_releases' );
}

function drbfd_shortcode_style_func() {
    wp_enqueue_style( 'shortcode_styles', plugin_dir_url( __FILE__ ) . '/build/style-index.css');
  }

  add_action( 'init', 'drbfd_shortcode_style_func' );

    function drbfd_render_releases() {
        $options = get_option( 'drbfd_blocks_for_discogs_options' );
        if (!isset($options['username']) && !isset($options['token']) || $options['token'] == '' && $options['username'] == '') {
            return '<div class="drbfd-blocks-for-discogs-error">
            <p><b>Blocks for Discogs</b>:<br> You must enter both a user name and token in the Blocks for Discogs settings</p>
            </div>';
        }
        if (!isset($options['token']) || $options['token'] == ''){
            return '<div class="drbfd-blocks-for-discogs-error">
            <p><b>Blocks for Discogs</b>:<br> You must enter a token in the Blocks for Discogs settings</p>
            </div>';
        }  else if (isset($options['token'])) {
            $token = $options['token'];
            }
        if (!isset($options['username']) || $options['username'] == '') {
            return '<div class="drbfd-blocks-for-discogs-error">
            <p><b>Blocks for Discogs</b>:<br> You must enter a Discogs.com username in the Blocks for Discogs settings</p>
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
            
            $output = '<div id="drbfd-blocks-for-discogs-parent" class="drbfd-blocks-for-discogs-parent">
                        <div id="#drbfd-discogs-container" class="drbfd-discogs-container">';
            
            foreach ($data['releases'] as $item){
                $albumName = $item['basic_information']['title'];
                $artistName = $item['basic_information']['artists'][0]['name'];
                $albumCover = $item['basic_information']['thumb'];
                $releaseYear = $item['basic_information']['year'];
                $format = $item['basic_information']['formats'][0]['name'];
                $output .= '<div class="discogs-card">
                                <div class="album-cover-div"><img src="' . $albumCover . '"></div>
                                <div class="album-title-div">
                                        <h4>' . $albumName . '</h4>
                                    <h5>' . $artistName . '</h5>
                                </div>
                                <div class="album-release-details">
                                    <p>Format: ' . $format . '</p>
                                    <p>Released: ' . $releaseYear . '</p>
                                </div>
                            </div>';
            }
            $output .= '</div></div>';

            return  $output;
        
        } 
                   
        wp_enqueue_script( 'jquery' );
        wp_localize_script( 'drbfd_script', 'discogs_fetch',
        array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'noimage' => plugin_dir_url( __FILE__ ) . '/assets/noimage.png',
        'nonce' => wp_create_nonce('ajax-nonce')
        ) );
        wp_enqueue_script( 'drbfd_script' );
        return '
        <div id="drbfd-blocks-for-discogs-parent" class="drbfd-blocks-for-discogs-parent">
        </div>
        ';
    }

	function drbfd_blocks_for_discogs_init() {
		register_block_type_from_metadata( __DIR__ , array(
			'render_callback' => 'drbfd_render_releases'
		) );
		
	}

    add_action( 'init', 'drbfd_blocks_for_discogs_init' );
    add_action('wp_ajax_drbfd_discogs_fetch', 'drbfd_discogs_fetch');
    add_action('wp_ajax_nopriv_drbfd_discogs_fetch', 'drbfd_discogs_fetch');

    function drbfd_discogs_fetch(){
        if ( ! wp_verify_nonce( $_GET['nonce'], 'ajax-nonce' ) ) {
            return $_GET['nonce]']; 
            die ();
        }
        $options = get_option( 'drbfd_blocks_for_discogs_options' );
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
            $page = (int)$_REQUEST['page'];
            $limit = (int)$_REQUEST['limit'];

            if (!is_int($page) || $page == 0) {
                die();
            } 
            if (!is_int($limit) || $limit > 50 | $limit == 0) {
                die();
            }
                    
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

add_action ('admin_menu', 'drbfd_add_settings_menu');

    function drbfd_add_settings_menu() {
            add_menu_page ('Blocks for Discogs Settings', 'Blocks for Discogs', 
            'manage_options', 'drbfd_blocks_for_discogs', 'drbfd_blocks_for_discogs_option_page', 
            plugin_dir_url( __FILE__ ) . '/assets/icon.png', 99);
    }

function drbfd_blocks_for_discogs_option_page() {
    ?>
    <div class="wrap">
        <h2> Blocks for Discogs</h2>
        <form action="options.php" method="post">
            <?php 
            settings_fields ('drbfd_blocks_for_discogs_options');
            do_settings_sections ('drbfd_blocks_for_discogs');
            submit_button ('Save Changes', 'primary');
            ?>

        </form>
    <?php   
}
?>
<?php

function drbfd_blocks_for_discogs_settings_errors() {
    settings_errors();   
}

add_action('admin_notices', 'drbfd_blocks_for_discogs_settings_errors');

add_action('admin_init', 'drbfd_load_menu');

function drbfd_load_menu() {    

    add_settings_section( 
        'drbfd_blocks_for_discogs_main', 
        'Settings',
        'drbfd_blocks_for_discogs_section_text', 
        'drbfd_blocks_for_discogs' 
    );

    register_setting ('drbfd_blocks_for_discogs_options', 'drbfd_blocks_for_discogs_options', 
    array (
        'type' => 'string',
        'sanitize_callback' => 'drbfd_blocks_for_discogs_validate_username',
        'default' => ''
    )
    );

    add_settings_field( 'drbfd_blocks_for_discogs_username',
    '<br>Your Discogs Username',
    'drbfd_blocks_for_discogs_settings_username',
    'drbfd_blocks_for_discogs',
    'drbfd_blocks_for_discogs_main'
    );

	register_setting ('drbfd_blocks_for_discogs_options', 'drbfd_blocks_for_discogs_options', 
		array (
			'type' => 'string',
			'sanitize_callback' => 'drbfd_blocks_for_discogs_validate_token',
			'default' => ''
		)
	);
    add_settings_field( 'drbfd_blocks_for_discogs_token',
        'Your Discogs API Token',
        'drbfd_blocks_for_discogs_settings_token',
        'drbfd_blocks_for_discogs',
        'drbfd_blocks_for_discogs_main'
    );



    function drbfd_blocks_for_discogs_section_text() {
        echo '<p>Use the Blocks for Discogs in the WordPress Block Editor. <br>
         If using a page builder or classic editor, use shortcode: <b>[blocks-for-discogs]</b></p>';
    }

    function drbfd_blocks_for_discogs_settings_token() {
        $options = get_option( 'drbfd_blocks_for_discogs_options' );
        if (isset($options['token'])) {
        $token = $options['token'];
        } else {
            $token = '';
        }
   
        echo '<input id="token" name="drbfd_blocks_for_discogs_options[token]"
        type="text" value="' . esc_attr( $token ) . '" required/>
        <p>Enter a valid Discogs.com token. You can generate a new token 
        <a href="https://www.discogs.com/settings/developers" target=_blank>here</a>.</p>';

    }

    function drbfd_blocks_for_discogs_settings_username() {
        $options = get_option( 'drbfd_blocks_for_discogs_options' );
        if (isset($options['username'])) {
        $username = $options['username'];
        } else {
            $username = '';
        }
        
        echo '<br><input id="username" name="drbfd_blocks_for_discogs_options[username]"
        type="text" value="' . esc_attr( $username ) . '" required/>
        <p>Enter a valid Discogs.com user name. If you do not already have one, 
        you can create a new one <a href="https://accounts.discogs.com/register" target=_blank>here</a>, 
        and be sure to add releases to your collection.</p>';

    }

    function drbfd_blocks_for_discogs_validate_token( $input ) {
		
        $input['token'] = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            $input['token'] );
		return $input;

    }

    function drbfd_blocks_for_discogs_validate_username( $input ) {
		
        $input['username'] = preg_replace(
            '/[^A-Za-z0-9._-]/',
            '',
            $input['username'] );
			return $input;
    }
	
}
