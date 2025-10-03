<?php

/**
 * Plugin Name: UCSC Events Functionality
 * Plugin URI: https://github.com/ucsc/ucsc-events-functionality
 * Description: Additional functionality for the events calendar.
 * Version: 1.0.1
 * Author: Rob Knight
 * Author URI: https://www.ucsc.edu
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ucsc-events-functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Main Events Calendar Functionality Class
 */
class EventsCalendarFunctionality
{

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->init();
  }

  /**
   * Initialize the plugin
   */
  private function init()
  {
    // Hook into WordPress
    add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 100);

    // Load plugin text domain for translations
    add_action('plugins_loaded', array($this, 'load_textdomain'));
  }

  /**
   * Enqueue frontend stylesheet with high priority
   */
  public function enqueue_styles()
  {
    $plugin_url = plugin_dir_url(__FILE__);
    $plugin_path = plugin_dir_path(__FILE__);
    $plugin_data = get_plugin_data(__FILE__);

    // Enqueue the main stylesheet if it exists
    if (file_exists($plugin_path . 'assets/css/ucsc-events-variables.css')) {
      wp_enqueue_style(
        'ucsc-events-functionality',
        $plugin_url . 'assets/css/ucsc-events-variables.css',
        array(), // No dependencies to ensure it loads independently
        $plugin_data['Version'],
        true
      );
    }
  }

  /**
   * Load plugin text domain for translations
   */
  public function load_textdomain()
  {
    load_plugin_textdomain(
      'ucsc-events-functionality',
      false,
      dirname(plugin_basename(__FILE__)) . '/languages/'
    );
  }
}



/**
 * Add custom CSS class to body for easier targeting
 */
function ecf_add_body_class($classes)
{
  $classes[] = 'ucsc-events-functionality-active';
  return $classes;
}
add_filter('body_class', 'ecf_add_body_class');

// Initialize the plugin
new EventsCalendarFunctionality();
