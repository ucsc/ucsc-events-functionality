<?php

/**
 * Plugin Name: UCSC Events Functionality
 * Plugin URI: https://github.com/ucsc/ucsc-events-functionality
 * Description: Additional functionality for the events calendar.
 * Version: 1.1.0
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
    // Load plugin text domain for translations
    add_action('plugins_loaded', array($this, 'load_textdomain'));

    add_action('init', function () {
      register_block_bindings_source('ucsc-events-functionality/post-id', [
        'label' => __('Post ID', 'ucsc-events-functionality'),
        'get_value_callback' => function ($source_args, $block_instance) {
          return get_the_ID();
        },
        'uses_context' => ['postId'], // Important for query loops
      ]);
    });

    // Add custom column to users list for events count
    add_filter('manage_users_columns', array($this, 'add_events_column'));
    add_action('manage_users_custom_column', array($this, 'populate_events_column'), 10, 3);

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

  /**
   * Add Events column to users list
   *
   * @param array $columns Existing columns
   * @return array Modified columns
   */
  public function add_events_column($columns)
  {
    // Insert the Events column before the Posts column
    $new_columns = array();
    foreach ($columns as $key => $value) {
      if ($key === 'posts') {
        $new_columns['tribe_events'] = __('Events', 'ucsc-events-functionality');
      }
      $new_columns[$key] = $value;
    }
    return $new_columns;
  }

  /**
   * Populate Events column with user's event count
   *
   * @param string $output      Custom column output
   * @param string $column_name Name of the column
   * @param int    $user_id     ID of the user
   * @return string Column content
   */
  public function populate_events_column($output, $column_name, $user_id)
  {
    if ($column_name === 'tribe_events') {
      $count = count_user_posts($user_id, 'tribe_events', true);

      if ($count > 0) {
        // Create a link to filter events by this user
        $output = sprintf(
          '<a href="%s">%d</a>',
          admin_url('edit.php?post_type=tribe_events&author=' . $user_id),
          $count
        );
      } else {
        $output = '0';
      }
    }
    return $output;
  }
}


// Initialize the plugin
new EventsCalendarFunctionality();
