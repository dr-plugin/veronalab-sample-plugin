<?php
/**
 * Plugin Name:     Veronalab Sample Plugin
 * Plugin URI:      https://www.veronalabs.com
 * Plugin Prefix:   VSP
 * Description:     A plugin to add a CPT book and book_info table
 * Author:          Ayoob Zare
 * Author URI:      https://veronalabs.com
 * Text Domain:     vsp-text-domain
 * Domain Path:     /languages
 * Version:         1.0.0
 */

defined('ABSPATH') || exit;

use Rabbit\Application;
use Rabbit\Redirects\RedirectServiceProvider;
use Rabbit\Database\DatabaseServiceProvider;
use Rabbit\Logger\LoggerServiceProvider;
use Rabbit\Plugin;
use Rabbit\Redirects\AdminNotice;
use Rabbit\Templates\TemplatesServiceProvider;
use Rabbit\Utils\Singleton;
use League\Container\Container;
use App\Src\Vsp_Plugin_Boot;


if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require dirname(__FILE__) . '/vendor/autoload.php';
}

/**
 * Class ExamplePluginInit
 * @package ExamplePluginInit
 */
class Vsp_Plugin_Init extends Singleton
{
    /**
     * @var Container
     */
    private $application;

    /**
     * ExamplePluginInit constructor.
     */
    public function __construct()
    {
        $this->application = Application::get()->loadPlugin(__DIR__, __FILE__, 'config');
        $this->init();
    }

    public function init()
    {
        try {

            /**
             * Load service providers
             */
            //$this->application->addServiceProvider(RedirectServiceProvider::class);
            $this->application->addServiceProvider(DatabaseServiceProvider::class);
            $this->application->addServiceProvider(TemplatesServiceProvider::class);
            $this->application->addServiceProvider(LoggerServiceProvider::class);
            // Load your own service providers here...

            /**
             * Activation hooks
             */
            $this->application->onActivation(function () {
                // Create tables or something else
                $this->create_table();
                
            });

            /**
             * Deactivation hooks
             */
            $this->application->onDeactivation(function () {
                // Clear events, cache or something else
            });

            $this->application->boot(function (Plugin $plugin) {

                Vsp_Plugin_Boot::get($this->application);

            });
        } catch (Exception $e) {
            /**
             * Print the exception message to admin notice area
             */
            add_action('admin_notices', function () use ($e) {
                AdminNotice::permanent(['type' => 'error', 'message' => $e->getMessage()]);
            });

            /**
             * Log the exception to file
             */
            add_action('init', function () use ($e) {
                if ($this->application->has('logger')) {
                    $this->application->get('logger')->warning($e->getMessage());
                }
            });
        }
    }

    /**
     * @return Container
     */
    public function get_app()
    {
        return $this->application;
    }

    /**
     * create book_info table
     */
    public function create_table()
    {
        if (!$this->application->has('database')) {
            return;
        }

        $db = $this->application->get('database');
        $schema = $db->schema();

        if (!$schema->hasTable('book_info')) {
            $schema->create('book_info', function ($table) {
                $table->bigIncrements('ID');
                $table->bigInteger('post_id')->unique();
                $table->string('isbn', 50);
            });
        }else{
            //update table if need
        }

        update_option('vsp_current_dbv', $this->application->config('db_version'),  'no');
    }
}

/**
 * Returns the main instance of VspPluginInit.
 *
 * @return Vsp_Plugin_Init
 */
function vsp_plugin_init()
{
    return Vsp_Plugin_Init::get();
}

vsp_plugin_init();