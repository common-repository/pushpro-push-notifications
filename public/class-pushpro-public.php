<?php

class Pushpro_Public extends Pushpro_Basic
{
    const LIBRARY_SRC = 'https://storage.googleapis.com/push-pro-java-scripts/pushpro-lib.js';

    protected $plugin_name;

    protected $version;

    /**
     * Pushpro_Public constructor.
     * @param $plugin_name
     * @param $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Add snippet to head
     */
    public function pushpro_add_wp_head()
    {
        $pushpro_settings = $this->pushpro_get_settings();
        $apiKey = $pushpro_settings ['token'];
        if (!$apiKey) {
            return;
        }

        if (!isset($pushpro_settings['snippet'])) {
            $pushpro_settings['snippet'] = (new Pushpro_Connection($apiKey))->snippet();
            update_option('pushpro_settings', $pushpro_settings);
        }
        $snippet = $pushpro_settings['snippet'];
        ?>
        <script>
            window.__pushpro = <?php echo wp_json_encode($snippet); ?>
        </script>
        <script src="<?php echo self::LIBRARY_SRC ?>"></script>
        <?php
    }
}