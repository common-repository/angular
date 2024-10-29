<?php
/*
Plugin Name: Angular
Plugin URI: http://www.getangular.com
Description: Easily embed angular applications on a wordpress site
Version: 0.1
Author: Adam Abrons
Author URI: http://www.getangular.com
*/

require( dirname(__FILE__) . '/simple_html_dom.php');

add_action('wp_head', 'angular_add_javascript');
add_action('wp_head', 'angular_prettify');
add_action('admin_menu', 'angular_admin');
add_filter('the_content', 'angularize');

function angularize($content) {
  $html = str_get_html($content);
  $id = 0;
  foreach($html->find('.angular') as $angular) {
    $angular_inner = $angular->innertext;
    $replacement = '<div id="angularTab-'.$id.'" ng-init="$window.jQuery(\'#angularTab-'.$id.'\').tabs();$window.prettyPrint();">';
    $replacement = $replacement . '<ul>';
    $replacement = $replacement . '<li><a href="#angular-'.$id.'">' . "With&nbsp;&lt;angular/&gt;" . "</a></li>";
    $replacement = $replacement . '<li><a href="#angularOff-'.$id.'">' . "Without&nbsp;&lt;angular/&gt;" . "</a></li>";
    $replacement = $replacement . '<li><a href="#angularSrc-'.$id.'">' . "HTML&nbsp;Source" . "</a></li>";
    $replacement = $replacement . '</ul>';
    $replacement = $replacement . '<div id="angular-'.$id.'">' . $angular_inner . "</div>";
    $replacement = $replacement . '<div id="angularOff-'.$id.'" ng-non-bindable="">' . $angular_inner . "</div>";
    $replacement = $replacement . '<div id="angularSrc-'.$id.'"><pre class="prettyprint" ng-non-bindable="">' . htmlspecialchars($angular) . "</pre></div>";
    $replacement = $replacement . '</div>';
  
    $angular->innertext = $replacement;
    $id = $id + 1;
  }
  echo $html;
}


function angular_add_javascript() {
  $angular_library_option = get_option('angular_library');
  $angular_database_option = get_option('angular_database');

  $angular_development_mode = false;
  $angular_host = $angular_development_mode ? "getangular.dev:3000" : "getangular.com";

  $angular_subdomain = $angular_library_option ? $angular_library_option . '.' : '';
  $angular_suffix = $angular_database_option ? '&database=' . $angular_database_option : '';

  $angular_js_file = $angular_development_mode ? 'angular-bootstrap.js' : 'angular-1.0a.js';
  $angular_script_url = 'http://' . $angular_subdomain . $angular_host . '/' . $angular_js_file . '#autoSubmit=false' . $angular_suffix;
  echo '<script type="text/javascript" src="' . $angular_script_url .'"></script>' . "\n";
}

function angular_prettify() {
  echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') .'/wp-content/plugins/angular/prettify.js" ></script>' . "\n";  
}

function angular_options() {
  if ( isset( $_POST['angular_library'] ) ) {
    update_option( 'angular_library', $_POST['angular_library'] );
  }
  if ( isset( $_POST['angular_database'] ) ) {
    update_option( 'angular_database', $_POST['angular_database'] );
  }
  if ( !empty($_POST['submit'] ) ) : ?>
  <div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
  <?php endif; ?>

  <div class="wrap">
  <h2>Angular Configuration</h2>
  <div class="narrow">
  <form action="" method="post" id="angular-config" style="margin: auto; width: 400px; ">
  <h3><label for="angular_library">Angular Library Name</label></h3>
  <p>
  <input id="angular_library" name="angular_library" type="text" size="64" maxlength="64" value="<?php echo get_option('angular_library'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" />

  <h3><label for="angular_database">Angular Database Name</label></h3>
  <p>
  <input id="angular_database" name="angular_database" type="text" size="64" maxlength="64" value="<?php echo get_option('angular_database'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" />

  <p>
  <input type="submit" name="submit"/>
  </p>
 <?php
}

function angular_admin() {
  add_submenu_page('plugins.php', 'Angular Configuration', 'Angular Configuration', 'administrator', 'angular-config', 'angular_options');
}
?>