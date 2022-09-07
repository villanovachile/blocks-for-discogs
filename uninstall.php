<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_name = 'drbfd_blocks_for_discogs_options';
 
delete_option($option_name);