<?php
    function onestop_your_submissions() {
        $user = wp_get_current_user();
        ?><h2>Your Project Submissions <span class="little-text"><a href="https://onestopconsult.server-02.dehosting.co.uk/new-post-request/">Submit new quote</a></span></h2><?php
        $search_criteria['status'] = "active";
        $search_criteria['field_filters'][] = array('key' => '5', 'value' => $user->user_email);

        $forms = GFAPI::get_entries(1, $search_criteria);

        if (sizeof($forms) > 0) {
            foreach ($forms as $form) {
                ?>
                    <a href="https://onestopconsult.server-02.dehosting.co.uk/forms/quotes?pid=<?php echo $form['id']; ?>" class="form-submission">
                            <div class="form-address">
                                <strong>Project:</strong> <span class="form-hover"><?php echo $form[6]; ?></span>
                            </div>
                            <div class="form-created">
                                <?php echo onestop_time_ago_in_php($form['date_created']); ?>
                            </div>
                        <?php
                            $search_criteria2['status'] = "active"; 

                            $entries = GFAPI::get_entries(2, $search_criteria2);

                            $yes = 0;
                            foreach ($entries as $entry) {
                                if ($entry[2] == $form['id']) {
                                    $yes++;
                                }
                            }
                            
                            if ($yes) {
                                ?>
                                    <div class="quote-status">
                                        <strong>Responses:</strong> <?php echo $yes; ?> quote(s) found.
                                    </div>
                                <?php
                            }
                            else {
                                ?>
                                    <div class="quote-status">
                                        <strong>Responses:</strong> No quotes currently found
                                    </div>
                                <?php
                            }
                        ?>
                </a>
            <?php
            }
        }
    }

    function onestop_your_quote_view() {
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
        <span class="little-text"><a href="https://onestopconsult.server-02.dehosting.co.uk/forms">< Back to Submissions</a></span>
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
                if ($files) {
                    foreach ($files as $file) {
                        ?><a href="<?php echo $file; ?>" data-fancybox="gallery" target="_blank"><?php $a = explode("/", $file); echo end($a); ?></a><br/><?php
                    }
                }
                else {
                    echo "No attachments found.";
                }
            ?>
        </p>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                /* apply only to a input with a class of gf_readonly */
                jQuery(".gf_readonly input").attr("readonly","readonly");
            });
        </script>
        <h2 style="border-top: 1px solid black;padding-top: 20px;">Quotes Recieved</h2>
        <?php
            $search_criteria['status'] = "active";
            $search_criteria['field_filters'][] = array( 'key' => '2', 'value' => $_REQUEST['pid'] );
    
            $forms = GFAPI::get_entries(2, $search_criteria);
            if ($forms) {
                foreach ($forms as $form) {
                    $service_provider = get_user_by("id", $form['created_by']);
                    ?>
                    <div class="quote-holder">
                        <h3>Quote provided by <?php echo $service_provider->display_name; ?></h3>
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
                                else {
                                    echo "No files found.";
                                }
                            ?>
                        </p>
                    </div>
                    <div class="little-text"><a href="https://onestopconsult.server-02.dehosting.co.uk/forms">< Back to Submissions</a></div>
                    <?php
                }
            }
            else {
                ?><p>No quotes for this submission found.</p><?php
            }
    }
?>