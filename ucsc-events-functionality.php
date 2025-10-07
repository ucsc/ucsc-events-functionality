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


// Include custom templates
add_filter('theme_page_templates', 'ecf_register_plugin_templates');
add_filter('template_include', 'ecf_load_plugin_templates', 99);

/**
 * Register plugin templates so they appear in the admin "Template" dropdown.
 */
function ecf_register_plugin_templates($templates) {
    // Only add templates if we're in the admin area
    if (is_admin()) {
        $new_templates = array(
            'template-venues.php' => __('Venues Template', 'ucsc-events-functionality'),
            'template-organizer.php' => __('Organizers Template', 'ucsc-events-functionality'),
        );

        $templates = array_merge($templates, $new_templates);
    }

    return $templates;
}

/**
 * Load selected plugin template when assigned to a page.
 */
function ecf_load_plugin_templates($template) {
    if (is_singular('page')) {
        $selected_template = get_post_meta(get_the_ID(), '_wp_page_template', true);

        // Only process if a custom template is selected and it's one of our plugin templates
        if (!empty($selected_template) && $selected_template !== 'default') {
            // Define our plugin templates
            $plugin_templates = array(
                'template-venues.php',
                'template-organizer.php'
            );

            // Check if the selected template is one of our plugin templates
            if (in_array($selected_template, $plugin_templates)) {
                // Path to the template inside the plugin
                $plugin_template = plugin_dir_path(__FILE__) . 'templates/' . $selected_template;

                // Verify the file exists before returning it
                if (file_exists($plugin_template) && is_readable($plugin_template)) {
                    return $plugin_template;
                } else {
                    // Log error for debugging (only if WP_DEBUG is enabled)
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('UCSC Events Functionality: Template file not found or not readable: ' . $plugin_template);
                        error_log('UCSC Events Functionality: File exists check: ' . (file_exists($plugin_template) ? 'true' : 'false'));
                        error_log('UCSC Events Functionality: File readable check: ' . (is_readable($plugin_template) ? 'true' : 'false'));
                    }
                }
            }
        }
    }

    return $template;
}