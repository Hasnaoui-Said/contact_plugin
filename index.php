<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

<?php

/**
 * Plugin Name: contact us plugin
 * Description: this is a contact form plugin as a project in youcode.
 * Version: 1.0.1
 * Author: Said hasnaoui
 */
// Remove the admin bar from the front end
// add_filter( 'show_admin_bar', '__return_false' );


add_shortcode('said_html', function () {
    // echo "<b >this is from said</b>";
    ob_start();
    proccess_email();
    html_form_code();
    return ob_get_clean();
});

add_action('activate_contact_plugin/index.php', function () {
    global $wpdb;
    $wpdb->query("CREATE TABLE IF NOT EXISTS `wp_contact_plugin_test` ( `id` INT NOT NULL AUTO_INCREMENT , `email` TEXT NOT NULL , `name` TEXT NOT NULL , `subject` TEXT NOT NULL , `message` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
});

add_action('deactivate_contact_plugin/index.php', function () {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS `wp_contact_plugin_test`;");
});


function html_form_code()
{
?>
    <form action=" <?= esc_url($_SERVER['REQUEST_URI']) ?> " method="post">
        <p>
            Your Name (required) <br />
            <input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value=" <?= isset($_POST["cf-name"]) ? esc_attr($_POST["cf-name"]) : '' ?>" size="40" />
        </p>
        <p>
            Your Email (required) <br />
            <input type="email" name="cf-email" value="<?= isset($_POST["cf-email"]) ? esc_attr($_POST["cf-email"]) : '' ?>" size="40" />
        </p>
        <p>Subject (required) <br />
            <input type="text" name="cf-subject" pattern=".+" value="<?= isset($_POST["cf-subject"]) ? esc_attr($_POST["cf-subject"]) : '' ?> " size="40" />
        </p>
        <p>
            Your Message (required) <br />
            <textarea rows="10" cols="35" name="cf-message"><?= isset($_POST["cf-message"]) ? esc_attr($_POST["cf-message"]) : '' ?> </textarea>
        </p>
        <p><input type="submit" name="cf-submitted" value="Send" /></p>
    </form>
    <?php
}
function proccess_email()
{

    // if the submit button is clicked, send the email
    if (isset($_POST['cf-submitted'])) {
        // sanitize form values
        $name    =  $_POST["cf-name"];
        $email   = $_POST["cf-email"];
        $subject = $_POST["cf-subject"];
        $message = $_POST["cf-message"];

        // get the blog administrator's email address
        saveDataToTable($email, $name, $message, $subject); ?>
        <div class="alert alert-success" style="font-weight:bold;border:sloid black 1px;border-radius: 5px">
            Merci, nous vous rappelerons rapidement. A bientot! 
        </div>
    <?php
    }
}
function saveDataToTable($email, $name, $message, $subject)
{
    global $wpdb;
    $wpdb->query("INSERT INTO `wp_contact_plugin_test` (`id`, `email`, `name`, `subject`, `message`) VALUES (NULL, '{$email}', '{$name}', '{$subject}', '{$subject}');");
}
add_action('admin_menu', function () {

    add_menu_page(
        'List of received messages',
        'My contact form',
        'edit_posts',
        'contact_form_said',
        'list_received_emails',
        'dashicons-media-spreadsheet'
    );
});
function list_received_emails()
{
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM `wp_contact_plugin_test` ;");
    ?>

    <h5>Contact emails</h5>
    <table class="table">
        <?php if (count($results) < 1) { ?>
            <div class="alert alert-danger">
                you do not have any incoming messages yet!
            </div>
        <?php } else { ?>
            <tr>
                <th>#</th>
                <th>Email</th>
                <th>Name</th>
                <th>Sublect</th>
                <th>Message</th>
            </tr>
        <?php }
        foreach ($results as $entry) { ?>
            <tr>
                <td><?= $entry->id ?></td>
                <td><?= $entry->email ?></td>
                <td><?= $entry->name ?></td>
                <td><?= $entry->subject ?></td>
                <td><?= $entry->message ?></td>
            </tr>
        <?php } ?>

    </table>
<?php
}
