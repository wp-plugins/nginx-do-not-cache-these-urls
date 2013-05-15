<?php
/*
Plugin Name: Nginx - do not cache these URLs
Plugin URI: http://aimbox.com/p/WordPress_Nginx_do_not_cache_these_URLs/2
Description: This plugin forces Nginx not to cache the specified URLs. Regular expressions are supported for advanced users.
Version: 1.0
Author: Aimbox
Author URI: http://aimbox.com
*/

function nginx_x_accel_create_menu()
{
    add_options_page('Nginx - do not cache these URLs', 'Nginx - exclude URLs', 'manage_options', 'nginx_x_accel_expires', 'nginx_x_accel_settings_page');
}

function nginx_x_accel_register_settings()
{
    register_setting( 'nginx_exclude_urls-group', 'nginx_exclude_urls' );
}

function nginx_x_accel_settings_page()
{
    ?>
    <div class="wrap">
        <h2>Nginx - do not cache these URLs</h2>

        <form method="post" action="options.php">
            <?php wp_nonce_field('update-options'); ?>
            <?php settings_fields( 'nginx_exclude_urls-group' ); ?>

            <table class="form-table">

                <tr valign="top">
                    <th scope="row">List of URLs to be exluded:</th>
                    <td><textarea name="nginx_exclude_urls" class="large-text code" rows="5"><?php echo get_option('nginx_exclude_urls'); ?></textarea></td>
                </tr>

                </tr>

            </table>

            <input type="hidden" name="action" value="update" />

            <?php submit_button(); ?>

        </form>
    </div>
    <?php
}

function nginx_x_accel_send_headers()
{
    $regexp = get_option('nginx_exclude_urls');
    $regexpArray = explode("\n", $regexp);

	if(!is_array($regexpArray)) return;

    foreach($regexpArray AS $regexp)
    {
        if(!empty($regexp) && preg_match(('/'.preg_quote(trim($regexp), '/').'/si'), $_SERVER['REQUEST_URI']))
		{
            header("X-Accel-Expires: 0");
            return;
        }
    }
}

function nginx_x_accel_activate()
{
    add_option('nginx_exclude_urls', "wp-admin\nwp-login.php");
}

register_activation_hook(__FILE__, 'nginx_x_accel_activate');

add_action('admin_menu', 'nginx_x_accel_create_menu');
add_action('admin_init', 'nginx_x_accel_register_settings');

add_action( 'send_headers', 'nginx_x_accel_send_headers' );

?>