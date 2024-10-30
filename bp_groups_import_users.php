<?php
/*
Plugin Name: BP Groups Import Users
Plugin URI: http://www.Vibethemes.com
Description: A simple WordPress plugin to modify buddypress groups
Version: 1.1
Author: Vibethemes
Author URI: http://www.vibethemes.com
Text Domain: bp-giu
License: GPL2
*/
/*
Copyright 2014  VibeThemes  (email : vibethemes@gmail.com)

BP GROUPS IMPORT USERS program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

BP GROUPS IMPORT USERS program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with BP GROUPS IMPORT USERS program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


include_once 'includes/bp_group_import_users_class.php';

add_action('plugins_loaded','bp_group_import_users_translations');
function bp_group_import_users_translations(){

    $locale = apply_filters("plugin_locale", get_locale(), 'bp-giu');
    $lang_dir = dirname( __FILE__ ) . '/languages/';
    $mofile        = sprintf( '%1$s-%2$s.mo', 'bp-giu', $locale );
    $mofile_local  = $lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

    if ( file_exists( $mofile_global ) ) {
        load_textdomain( 'bp-giu', $mofile_global );
    } else {
        load_textdomain( 'bp-giu', $mofile_local );
    }  
}

if(class_exists('bp_group_import_users_class')){

    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('bp_group_import_users_class', 'activate'));
    register_deactivation_hook(__FILE__, array('bp_group_import_users_class', 'deactivate'));

    // instantiate the plugin class
 	$init = bp_group_import_users_class::instance_bp_group_import_users_class();
}
