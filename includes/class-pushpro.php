<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both
 * the public-facing side of the site and the admin area.
 *
 */

class Pushpro {

	/**
	 * The loader that's responsible for maintaining and registering all hooks
	 * that power the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 */
	protected $plugin_name;
	/**
	 * The current version of the plugin.
	 */
	protected $version;

	/**
	 * Pushpro constructor.
	 * Define the core functionality of the plugin.
	 * Set the plugin name and the plugin version that can be used throughout
	 * the plugin. Load the dependencies, define the locale, and set the hooks
	 * for the admin area and the public-facing side of the site.
	 */
	public function __construct() {
		if ( defined( 'PUSHPRO_VERSION' ) ) {
			$this->version = PUSHPRO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = PUSHPRO_NAME;
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 * Create an instance of the loader which will be used to register the
	 * hooks with WordPress.
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) )
		             . 'includes/class-pushpro-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) )
		             . 'includes/class-pushpro-connection.php';
		require_once plugin_dir_path( dirname( __FILE__ ) )
		             . 'includes/class-pushpro-basic.php';
		require_once plugin_dir_path( dirname( __FILE__ ) )
		             . 'admin/class-pushpro-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) )
		             . 'public/class-pushpro-public.php';
		$this->loader = new Pushpro_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the
	 * plugin.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Pushpro_Admin( $this->get_plugin_name(),
			$this->get_version() );
		$this->loader->add_action( 'admin_menu', $plugin_admin,
			'register_menu_page' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin,
            'table_sorter_admin_script' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin,
            'table_sorter_pager_admin_script' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin,
            'pushpro_admin_script' );
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'pushpro_admin_style');
    }

	/**
	 * Register all of the hooks related to the public-facing functionality of
	 * the plugin.
	 */
	private function define_public_hooks() {
		$plugin_public = new Pushpro_Public( $this->get_plugin_name(),
			$this->get_version() );
		$this->loader->add_action( 'wp_head', $plugin_public,
			'pushpro_add_wp_head' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return mixed
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

}