<?php
    /*
    Plugin Name: OneStop Consult Portal
    Version: 0.3
    Author: Convoymedia
    Author URI: http://www.convoymedia.com/
    */

    // Create the two user types

    $result = add_role( 'company', __('Company' ), array(
            'read' => true, // true allows this capability
            'edit_posts' => false, // Allows user to edit their own posts
            'edit_pages' => false, // Allows user to edit pages
            'edit_others_posts' => false, // Allows user to edit others posts not just their own
            'create_posts' => false, // Allows user to create new posts
            'manage_categories' => false, // Allows user to manage post categories
            'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
        ));

    $result = add_role( 'service', __('Service' ), array(
        'read' => true, // true allows this capability
        'edit_posts' => false, // Allows user to edit their own posts
        'edit_pages' => false, // Allows user to edit pages
        'edit_others_posts' => false, // Allows user to edit others posts not just their own
        'create_posts' => false, // Allows user to create new posts
        'manage_categories' => false, // Allows user to manage post categories
        'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
    ));

    function onestop_forms_shortcode($atts) {
        $search_criteria['status'] = "active";
        $search_criteria['field_filters'][] = array( 'key' => 'created_by', 'value' => $current_user->ID );

        $forms = GFAPI::get_entries(1, $search_criteria);
        if (sizeof($forms) > 0) {

        }
        else {
            return "No submissions found";
        }
    }

    add_shortcode( 'onestop-forms', 'onestop_forms_shortcode' );
?>