<?php
    function onestop_your_quotes() {
        ?><h2>Your Quotes</h2><?php
        $project_form = GFAPI::get_form(1);
        $services_count = count($project_form['fields'][7]['choices']);

        $services = explode(";", get_user_meta(get_current_user_id(), "services", true));

        $search_criteria['status'] = "active";
        $search_criteria['field_filters'][] = array('key' => '8', 'operator' => 'in', 'value' => $services);

        $forms = GFAPI::get_entries(1, $search_criteria);
        
        if (sizeof($forms) > 0) {
            $current_user = wp_get_current_user();
            foreach ($forms as $form) {
                $search_criteria2['status'] = "active";
                $search_criteria2['field_filters'][] = array( 'key' => '2', 'value' => $form['id'] );
                $search_criteria2['field_filters'][] = array( 'key' => 'created_by', 'value' => $current_user->ID );
        
                $entry = GFAPI::get_entries(2, $search_criteria2);
                ?>
                    <a href="https://onestopconsult.server-02.dehosting.co.uk/forms/quotes?pid=<?php echo $form['id']; ?>" class="form-submission <?php if (count($entry) > 0) { echo "done"; } else { echo "notdone"; } ?> ">
                        <div class="form-address">
                            <strong>Project:</strong> <span class="form-hover"><?php echo $form[6]; ?></span>
                        </div>
                        <div class="form-created">
                            <?php echo onestop_time_ago_in_php($form['date_created']); ?>
                        </div>
                    <?php
                        if (empty($form[11])) {
                            ?>
                                <div class="quote-status">
                                    Not yet quoted on.
                                </div>
                            <?php
                        }
                        else {
                        ?>
                            <div class="quote-status">
                                No quotes currently found
                            </div>
                        <?php
                        }
                    ?>
                    </a>
            <?php
            }
        }
    }

    function one_stop_quote_reply() {
        $entry = GFAPI::get_entry( $_REQUEST['pid'] );
        //print_r($entry);
        ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.2/dist/jquery.fancybox.min.css" />
        <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.2/dist/jquery.fancybox.min.js"></script>
        <style>
            .project-title {
                text-transform: capitalize;
            }

            .quote-holder {
                padding-bottom:20px;
                margin-bottom:20px;
                border-bottom:1px solid #282a2d;
            }
        </style>
        <span class="little-text"><a href="https://onestopconsult.server-02.dehosting.co.uk/forms">< Back to Quotes</a></span>
        <h2 class="project-title"><?php echo $entry['6'] ?></h2>
        <p>
            <strong>Created By: </strong> <?php echo $entry['1.3'] . " " .$entry['1.6'] ?><br/>
            <strong>Posted : </strong> <?php echo onestop_time_ago_in_php($entry['date_created']); ?><br/>
            <strong>Approx Start Date: </strong> <?php echo $entry[7]; ?><br/>
            <strong>Services : </strong> 
            <?php
                $project_form = GFAPI::get_form(1);
                $services_count = count($project_form['fields'][7]['choices']);

                for ( $i = 0 ; $i < $services_count ; $i++ ) {
                    if (!empty($entry['8.' . ($i+1)])) {
                        $services[] = $entry['8.' . ($i+1)];
                    }
                }
                echo implode(", ", $services);
            ?>
        </p>
        <p><?php echo $entry[9]; ?></p>
        <p>
            <strong>Attached Files</strong><br/>
            <?php 
                $files = json_decode($entry[12], true);
                foreach ($files as $file) {
                    ?><a href="<?php echo $file; ?>" data-fancybox="gallery" target="_blank"><?php $a = explode("/", $file); echo end($a); ?></a><br/><?php
                }
            ?>
        </p>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                /* apply only to a input with a class of gf_readonly */
                jQuery(".gf_readonly input").attr("readonly","readonly");
            });
        </script>
        <?php
            $current_user = wp_get_current_user();
            $search_criteria2['status'] = "active";
            $search_criteria2['field_filters'][] = array( 'key' => '2', 'value' => $_REQUEST['pid'] );
            $search_criteria2['field_filters'][] = array( 'key' => 'created_by', 'value' => $current_user->ID );
    
            $forms = GFAPI::get_entries(2, $search_criteria2);
            if ($forms) {
                foreach ($forms as $form) {
            ?>
                <h2 style="border-top: 1px solid black;padding-top: 20px;">Quote for Project</h2>
                <div class="quote-holder">
                    <strong>Submitted: </strong><?php echo onestop_time_ago_in_php($form['date_created']); ?><br/>
                    <p><?php echo $form[3] ?></p>
                    <p>
                        <strong>Attached Files</strong><br/>
                        <?php 
                            $files = json_decode($form[4], true);
                            if ($files) {
                                foreach ($files as $file) {
                                    ?><a href="<?php echo $file; ?>" data-fancybox="gallery" target="_blank"><?php $a = explode("/", $file); echo end($a); ?></a><br/><?php
                                }
                            }
                        ?>
                    </p>
                </div>
                <span class="little-text"><a href="https://onestopconsult.server-02.dehosting.co.uk/forms">< Back to Quotes</a></span>
            <?php
                }
            }
            else {
        ?>
            <h2 style="border-top: 1px solid black;padding-top: 20px;">Quote for Project</h2>
            <?php
            echo do_shortcode('[gravityform id="2" title="false" description="false"]');
            ?>
            <span class="little-text"><a href="https://onestopconsult.server-02.dehosting.co.uk/forms">< Back to Quotes</a></span>
        <?php
        }
}
?>