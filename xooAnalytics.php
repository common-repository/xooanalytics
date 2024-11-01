<?php
/*
Plugin Name: xooAnalytics
Plugin URI: http://xooxia.com/projects/xooanalytics-wordpress-plugin/
Description: This plugin will mark any external links (those with absolute URLs) and email links for tracking purposes with Google Analytics.  Added support to exclude your internal domain name under the Options menu.
Version: 1.4.2
Author: Xooxia
Author URI: http://xooxia.com
*/
/*  Copyright 2007  Xooxia Development  (email : xooxia@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function xooAnalytics($content){
	// first, add tracking to all absolute links and append /external/ to them
	// checking to see if the user has requested to exclude their internal domain name
	$exclude = (get_option('xooAnalytics_excludeDomain')) ? '[^'.get_option('xooAnalytics_excludeDomain').']' : '';
	$content = preg_replace('/<a href="(http|https|ftp)(\:\/\/'.$exclude.'.*?)"(.*?)>(.*?)<\\/a>/i', '<a href="$1$2" onclick="urchinTracker(\'/external/$1$2\')"$3>$4</a>', $content);
	// then, add tracking to all email links and append /mailto/ to them
	$content = preg_replace('/<a href="(mailto\:)(\w+[\w-\.]*\@\w+)((-\w+)|(\w*))(\.[a-z]{2,3})"(.*?)>(.*?)<\\/a>/i', '<a href="$1$2$3$4$5$6" onclick="urchinTracker(\'/mailto/$2$3$4$5$6\')"$7>$8</a>', $content);	
	return $content;
}
function xooAnalyticsHead() {
	if (get_option('xooAnalytics_insertCode')){
	if (get_option('xooAnalytics_googleID')){
			echo '<!-- inserted by xooAnalytics v1.4.2 -->
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-'.get_option('xooAnalytics_googleID').'";
urchinTracker();
</script>';
	}
	}
}

add_action('wp_head', 'xooAnalyticsHead');
add_filter('the_content', 'xooAnalytics', 0);
add_action('admin_menu', 'xooAnalytics_excludeOptions');

function xooAnalytics_excludeOptions() {
    add_options_page('xooAnalytics Options', 'xooAnalytics', 8, 'xooAnalyticsOptions', 'xooAnalyticsOptions_page');
}

function xooAnalyticsOptions_page() {

    $opt_name = 'xooAnalytics_excludeDomain';
	$opt_name_2 = 'xooAnalytics_insertCode';
	$opt_name_3 = 'xooAnalytics_googleID';
    $hidden_field_name = 'xoo_submit_hidden';
    $data_field_name = 'xooAnalytics_excludeDomain';
	$data_field_name_2 = 'xooAnalytics_insertCode';
	$data_field_name_3 = 'xooAnalytics_googleID';
    $opt_val = get_option( $opt_name );
	$opt_val_2 = get_option( $opt_name_2 );
	$opt_val_3 = get_option( $opt_name_3 );
	
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        $opt_val = $_POST[ $data_field_name ];
        update_option( $opt_name, $opt_val );
        $opt_val_2 = $_POST[ $data_field_name_2 ];
        update_option( $opt_name_2, $opt_val_2 );
        $opt_val_3 = $_POST[ $data_field_name_3 ];
        update_option( $opt_name_3, $opt_val_3 );

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'xoo_trans_domain' ); ?></strong></p></div>
<?php
    }
    echo '<div class="wrap">';
    echo "<h2>" . __( 'xooAnalytics Plugin Options', 'xoo_trans_domain' ) . "</h2>";
    ?>
<p>This plugin will add the Google Analytics tracking information to your external links and email links within your pages/posts.  It will add the code to links that start with:
<ul>
<li>http://</li>
<li>https://</li>
<li>ftp://</li>
</ul>
These links will have '/external/' appended to the URL within your tracking results.  You can choose to filter the content results by '/external/' and see all clicks to external links from your site.
</p>
<p><h3>Requirement:</h3>
The Google tracking code must be placed just under the opening &lt;body&gt; tag within your template.</p><hr />
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<p>You can choose to exclude your own internal domain name in the xooAnalytics plugin by adding your domain below.</p>
<p><?php _e("<strong>URL to exclude:</strong> ", 'xoo_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20"> <small>Please do not include the http://, https://, or ftp://</small><br />
<fieldset><legend>Insert Google Tracking Code?</legend>
Do you want xooAnalytics to insert your Google Code for you?  <input type="checkbox" name="<?php echo $data_field_name_2; ?>" value="1"<?php echo ($opt_val_2) ? ' CHECKED' : '' ; ?>> Yes<br />
What is your Google Analytics tracking code account number?:  <code>UA-</code><input type="text" name="<?php echo $data_field_name_3; ?>" value="<?php echo $opt_val_3; ?>" size="20">
</fieldset>
</p><hr />
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'xoo_trans_domain' ) ?>" />
</p>
</form>
</div>
<?php
}
?>
