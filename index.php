<?php

namespace WPPerformance\partytown;

/**
 * Plugin Name:       WP Performance Partytown
 * Description:       partytown in your WordPress
 * Update URI:        wp-performance-partytown
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Version:           0.0.1
 * Author:            Faramaz Patrick <infos@goodmotion.fr>
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-performance-partytown
 *
 * @package           wp-performance
 */

require_once(dirname(__FILE__) . '/inc/route.php');

if (!defined('PR_DEBUG')) {
  define('PR_DEBUG', false);
}

if (!defined('PR_PROXY')) {
  define('PR_PROXY', false);
}

if (!defined('PR_PROXY_KEY')) {
  define('PR_PROXY_KEY', 'EbEUt6AxCz4FgF8');
}

/**
 * include partytown in head
 */
add_action('wp_head', function () {
  echo "<script>
  partytown = {
    // only relative
    lib: '" . str_replace(get_site_url(), '', plugin_dir_url(__FILE__)) . "public/~partytown/',
    debug: " .  (PR_DEBUG ? 'true' : 'false') . ",
    " . (PR_PROXY ? "resolveUrl: function (url, location, type) {
        // nonce for filter request, set the cache of website to 8h max
        var pt_nonce = '" . (is_user_logged_in() ? PR_PROXY_KEY : wp_create_nonce('partytown_proxy')) . "';
        if (type === 'script' || type === 'iframe') {
           const proxyUrl = new URL('" . get_site_url() . "/wp-json/partytown/pr/getPR');
          proxyUrl.searchParams.append('url',url.href);
          proxyUrl.searchParams.append('pt_nonce',pt_nonce);
          return proxyUrl;
        }
        return url;
      }," : null) . "};
  </script>";
  // include code partytown
  echo '<script>';
  include(dirname(__FILE__) . '/partytown.js');
  echo '</script>';
}, 1);
