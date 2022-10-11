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

/**
 * include partytown in head
 */
add_action('wp_head', function () {
  echo "<script>
  partytown = {
    // only relative
    lib: '" . str_replace(get_site_url(), '', plugin_dir_url(__FILE__)) . "public/~partytown/',
    debug: true,
    resolveUrl: function (url, location, type) {
        // nonce for filter request, set the cache of website to 8h max
        var pt_nonce = '" . wp_create_nonce('partytown_proxy') . "';
        if (type === 'script' || type === 'iframe') {
           const proxyUrl = new URL('" . get_site_url() . "/wp-json/partytown/pr/getPR');
          proxyUrl.searchParams.append('url',url.href);
          proxyUrl.searchParams.append('pt_nonce',pt_nonce);
          return proxyUrl;
        }
        return url;
      },
    };
  </script>";
  // include code partytown
  echo '<script>';
  include(dirname(__FILE__) . '/partytown.js');
  echo '</script>';
}, 1);
