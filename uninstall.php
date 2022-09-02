<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_name = 'drdb_discogs_block_options';
 
delete_option($option_name);