<?php
/**
 * The admin-specific functionality of the plugin.
 *
 */

class Pushpro_Admin extends Pushpro_Basic
{
    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Script import url
     */
    const IMPORT_SCRIPT = "importScripts('https://storage.googleapis.com/push-pro-java-scripts/pushpro-sw.js');";
    const SCRIPT_SW_FILE_NAME = 'sw.js';

    /**
     *
     * Social_Parts_Admin constructor.
     * Initialize the class and set its properties.
     *
     * @param $plugin_name
     * @param $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Add a top-level menu page.
     * This method takes a capability which will be used to determine whether
     * or not a page is included in the menu.
     */
    public function register_menu_page()
    {
        add_menu_page(
            __($this->plugin_name, 'textdomain'),
            'PushPro',
            'manage_options',
            PUSHPRO_SLUG,
            [$this, 'admin_page_show'],
            plugins_url('images/menu-item-logo.png', __FILE__),
            20
        );
    }

    /**
     * Check request method
     *
     * @return bool
     */
    public function admin_page_ispost_request()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Try to make sw script and put it in root folder.
     */
    public function make_script_sw()
    {
        $importScripts = self::IMPORT_SCRIPT;
        $swName = self::SCRIPT_SW_FILE_NAME;
        if (file_exists(ABSPATH . $swName)) {
            $serviceWorkerContents = file_get_contents(ABSPATH . $swName);
        } else {
            $serviceWorkerContents = '';
        }

        $newServiceWorkerContents = $importScripts . "\n\n"
            . $serviceWorkerContents;

        if (strpos($serviceWorkerContents, $importScripts) === false) {
            if (is_writable(ABSPATH . $swName)
                || !file_exists(ABSPATH . $swName) && is_writable(ABSPATH)
            ) {
                file_put_contents(ABSPATH . $swName,
                    $newServiceWorkerContents);
            } else {
                ?>

                <p>
                    The file sw.js in the root directory of Wordpress is not
                    writable.
                    Please change its permissions and try again. Otherwise
                    replace its contents manually:
                </p>
                <pre><code><?= esc_html($newServiceWorkerContents) ?></code></pre>
                <p>
                    Also make sure that the file is accessible at
                    https://example.com/sw.js
                    (for example https://example.com/wordpress/sw.js is
                    invalid).
                </p>

                <?php
            }
        }
    }

    protected function check_push_pro_token($token)
    {
        if ($token === '') {
            return true;
        }
        if (!preg_match('/^\w+$/', $token)) {
            echo '<div class="error fade"><p>Use only alphanumeric symbols</p></div>';

            return false;
        }
        if (!(new Pushpro_Connection($token))->check()) {
            echo '<div class="error fade"><p>Wrong token</p></div>';

            return false;
        }

        return true;
    }

    /**
     * Prepare part content of admin page.
     *
     * @param $settings
     *
     * @return mixed
     */
    public function admin_page_prepare($settings)
    {
        if ($this->admin_page_ispost_request()) {
            $settings['token'] = isset ($_POST ['token']) ? sanitize_text_field($_POST ['token']) : '';
            $settings['snippet'] = (new Pushpro_Connection($settings ['token']))->snippet();
            if (!$this->check_push_pro_token($settings['token'])) {
                return $settings;
            }
            update_option('pushpro_settings', $settings);
            echo '<div class="notice notice-success is-dismissible"><p>Settings successfully updated.</p></div>';
            // service-worker.js
            $this->make_script_sw();
        }

        return $settings;
    }

    /**
     * Prepare token form.
     *
     * @param $settings
     */
    public function admin_page_tokenform($settings)
    {
        ?>
        <div>
            <form method="POST" id="pushpro-settings-form">

                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label for="token">API key</label></th>
                        <td>
                            <input class="error" type="text" name="token" id="token"
                                   value="<?php echo esc_attr($settings['token']) ?>">
                            <input type="submit" value="Save"
                                   class="button button-primary" id="submit"
                                   name="submit">
                            <p class="description">You can generate an API key in your <a
                                        href="https://portal.pushpro.io/portal/sites">domain
                                    settings</a>.</p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div id="pushpro-stats">
            <?php echo $this->admin_page_stats($settings) ?>
        </div>
        <?php
    }

    /**
     * Try to get site stats.
     *
     * @param $settings
     * @return string
     */
    public function admin_page_stats($settings)
    {
        $stats = (new Pushpro_Connection($settings['token']))->stats();
        if (!$stats) {
            return '';
        }
        $statsTable = '<div id="pushpro-pager" >
                            <span class="dashicons dashicons-arrow-left-alt2 prev"></span>
                            <span class="pagedisplay" data-pager-output-filtered="{startRow:input} &ndash; {endRow} / {filteredRows} of {totalRows} total rows"></span>
                            <span class="dashicons dashicons-arrow-right-alt2 next"></span>
                        </div>
                        <table id="pushpro-stats-table" class="widefat fixed">
                            <thead>
                                <tr>
                                <th><b>TITLE</b></th>
                                <th><b>DELIVERED</b></th>
                                <th><b>OPENED</b></th>
                                <th><b>CTR</b></th>
                                </tr>
                            </thead>
                            <tbody>';
        foreach ($stats as $task) {
            $received = $task->message_received;
            $clicked = $task->message_clicked;
            $ctr = (int)$received !== 0 ? ($clicked / $received * 100) : 0;
            $statsTable .= "<tr>
                                <td>$task->task_title</td>
                                <td>$received</td>
                                <td>$clicked</td>
                                <td>$ctr%</td>
                            </tr>";
        }
        $statsTable .= '</tbody></table>';

        return $statsTable;
    }

    /**
     * Admin page init.
     */
    public function admin_page_show()
    {
        $settings
            = $this->admin_page_prepare($this->pushpro_get_settings());
        ?>
        <div class="wrap">
            <h1>PushPro for WordPress</h1>
            <div id="push-pro-notifications">
            </div>
            <div class="welcome-panel">
                <div class="welcome-panel-column">
                    <img id="pushpro-logo" src="<?php echo plugins_url('images/full-logo.png', __FILE__) ?>"
                         alt="Pushpro logo">

                </div>
                <div class="welcome-panel-column">
                    <h3 style="margin-top:0px">Small Messages.
                        Big Impact.</h3>
                    <p>Send powerful Push Notifications from your current website,
                        reaching all your visitors directly on their mobile device.</p>
                    <p>No native app needed, just a 5 minute installation.</p>
                    <p>Ready to take PushPro for a spin? Get started for free now.</p>
                    <?php if (!isset($settings['token']) || !$settings['token']): ?>
                        <div class="pushpro-links">
                            <a class="button button-primary"
                               href="https://portal.pushpro.io"
                               target="_blank">Login</a>
                            <a class="button button-primary"
                               href="https://portal.pushpro.io"
                               target="_blank">Sign Up</a>
                            <a class="button button-primary"
                               href="https://www.pushpro.io/docs"
                               target="_blank">Documentation</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $this->admin_page_tokenform($settings);
            ?>
        </div>
        <?php
    }

    public function pushpro_admin_script()
    {
        wp_enqueue_script('pushpro_admin_script', plugin_dir_url(__FILE__) . 'js/pushpro-admin.js');
    }

    public function table_sorter_admin_script()
    {
        wp_enqueue_script('table_sorter_admin_script', plugin_dir_url(__FILE__) . 'js/jquery.tablesorter.js');
    }

    public function table_sorter_pager_admin_script()
    {
        wp_enqueue_script('table_sorter_pager_admin_script',
            plugin_dir_url(__FILE__) . 'js/jquery.tablesorter.pager.js');
    }

    function pushpro_admin_style()
    {
        wp_enqueue_style('pushpro_admin_style', plugin_dir_url(__FILE__) . 'css/pushpro-admin.css');
    }
}
