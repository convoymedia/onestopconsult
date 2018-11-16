<?php
    /*
    Plugin Name: OneStop Consult Portal
    Version: 0.3
    Author: Convoymedia
    Author URI: http://www.convoymedia.com/
    */

    include( plugin_dir_path( __FILE__ ) . 'company/submissions.php');
    include( plugin_dir_path( __FILE__ ) . 'service/quotes.php');

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

    // Add custom fields to user profiles

    function onestop_extra_user_profile_fields( $user ) { 
        ?>
            <h3><?php _e("One Stop Portal Information", "blank"); ?></h3>
            <p class="description">
                Please type in the services provided by this user. If more than one service is provided then please seperate each service with a comma.<br/>
                Please also ensure that the user role is set to Service otherwise they will not get notified of any projects to quote on.
            </p>
            <table class="form-table">
            <tr>
                <th><label for="services"><?php _e("Services"); ?></label></th>
                <td>
                    <input type="text" name="services" id="services" value="<?php echo esc_attr( get_the_author_meta( 'services', $user->ID ) ); ?>" class="regular-text" /><br />
                    <span class="description"><?php _e("Please enter provided services."); ?></span>
                </td>
            </tr>
            </table>
        <?php 
    }
    
    add_action( 'show_user_profile', 'onestop_extra_user_profile_fields' );
    add_action( 'edit_user_profile', 'onestop_extra_user_profile_fields' );
    

    function onestop_save_extra_user_profile_fields( $user_id ) {
        if ( !current_user_can( 'edit_user', $user_id ) ) { 
            return false; 
        }
        update_user_meta( $user_id, 'services', $_POST['services'] );
    }

    add_action( 'personal_options_update', 'onestop_save_extra_user_profile_fields' );
    add_action( 'edit_user_profile_update', 'onestop_save_extra_user_profile_fields' );

    // add log in and log out to the main menu

    function onestop_add_loginout_link( $items, $args ) {
        if (is_user_logged_in() && $args->theme_location == 'primary') {
            $items .= '<li><a href="https://onestopconsult.server-02.dehosting.co.uk/forms/">Quotes</a></li>';
            $items .= '<li><a href="'. wp_logout_url() .'">Log Out</a></li>';
        }
        elseif (!is_user_logged_in() && $args->theme_location == 'primary') {
            $items .= '<li><a href="https://onestopconsult.server-02.dehosting.co.uk/log-in/">Log In</a></li>';
        }
        return $items;
    }

    add_filter( 'wp_nav_menu_items', 'onestop_add_loginout_link', 10, 2 );

    function onestop_forms_shortcode($atts) {
        ?>
        <style>
            .form-submission {
                position:relative;
                display:block;
                background: #f3f4f7 !important;
                padding:10px;
                margin-bottom:20px
            }

            .form-created {
                position: absolute;
                top: 10px;
                right: 10px;
            }

            .done {
                border-left:5px solid green;
            }

            .notdone {
                border-left:5px solid orange;
            }

            .late {
                border-left:5px solid red;
            }

            .little-text {
                font-family: "Ropa Sans";
                line-height: 25px;
                font-weight: 400;
                font-style: normal;
                color: #666666;
                font-size: 17px;
            }
            a.form-submission:hover {
                color: #666666 !important;
            }

            a.form-submission:hover .form-hover{
                color:#8cc63f !important;
            }
        </style>
        <?php
        if( is_user_logged_in() ) {
            $user = wp_get_current_user();
            $roles = ( array ) $user->roles;
            if (in_array("service", $roles) || in_array("company", $roles)) {
                // company page
                if (in_array("company", $roles)) {
                    onestop_your_submissions();
                }
                else {
                    onestop_your_quotes();
                }
            }
            else {
                return 'You must be logged in to proceed. Click <a href="https://onestopconsult.server-02.dehosting.co.uk/log-in/">here</a> to login.';
            }
        }
    }

    add_shortcode( 'onestop-forms', 'onestop_forms_shortcode' );

    function onestop_quotes_shortcode() {
        ?>
        <style>
            .form-submission {
                position:relative;
                display:block;
                background: #f3f4f7 !important;
                padding:10px;
                margin-bottom:20px
            }

            .form-created {
                position: absolute;
                top: 10px;
                right: 10px;
            }

            .done {
                border-left:5px solid green;
            }

            .notdone {
                border-left:5px solid orange;
            }

            .late {
                border-left:5px solid red;
            }

            .little-text {
                font-family: "Ropa Sans";
                line-height: 25px;
                font-weight: 400;
                font-style: normal;
                color: #666666;
                font-size: 17px;
            }

            
            a.form-submission:hover {
                color: #666666 !important;
            }

            a.form-submission:hover .form-hover{
                color:#8cc63f !important;
            }
        </style>
        <?php
        $user = wp_get_current_user();
        $roles = ( array ) $user->roles;
        if (in_array("service", $roles) || in_array("company", $roles)) {
            // company page
            if (in_array("company", $roles)) {
                onestop_your_quote_view();
            }
            else {
                one_stop_quote_reply();
            }
        }
        else {
            return 'You must be logged in to proceed. Click <a href="https://onestopconsult.server-02.dehosting.co.uk/log-in/">here</a> to login.';
        }
    }

    add_shortcode('onestop-quotes', 'onestop_quotes_shortcode');

    function onestop_time_ago_in_php($timestamp){
        date_default_timezone_set("Asia/Kolkata");         
        $time_ago        = strtotime($timestamp);
        $current_time    = time();
        $time_difference = $current_time - $time_ago;
        $seconds         = $time_difference;
        
        $minutes = round($seconds / 60); // value 60 is seconds  
        $hours   = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec  
        $days    = round($seconds / 86400); //86400 = 24 * 60 * 60;  
        $weeks   = round($seconds / 604800); // 7*24*60*60;  
        $months  = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60  
        $years   = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60
                        
        if ($seconds <= 60){
            return "Just Now";
        } 
        else if ($minutes <= 60){
            if ($minutes == 1){
                return "one minute ago";
            } 
            else {
                return "$minutes minutes ago";
            }
        } 
        else if ($hours <= 24){
            if ($hours == 1){
                return "an hour ago";
            } 
            else {
                return "$hours hrs ago";
            }
        } 
        else if ($days <= 7){
            if ($days == 1){
                return "yesterday";
            } 
            else {
                return "$days days ago";
            }
        } 
        else if ($weeks <= 4.3){
            if ($weeks == 1){
                return "a week ago";
            } else {
                return "$weeks weeks ago";
            }
        } 
        else if ($months <= 12){
            if ($months == 1){
                return "a month ago";
            } else {
                return "$months months ago";
            }
        } 
        else {
            if ($years == 1){
                return "one year ago";
            } else {
                return "$years years ago";
            }
        }
    }
?>