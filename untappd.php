<?php
/*
Plugin Name: Untappd
Plugin URI: http://untappd.com/
Description: Untappd lets you keep track of the beers you drink
Version: 1.1.0
Author: Jason Austin
Author URI: http://jasonawesome.com/
*/

function showuntappd_admin_menu()
{
	add_options_page('Untappd Options', 'Untappd', 8, __FILE__, 'showuntappd_admin');
}

add_action('admin_menu', 'showuntappd_admin_menu');

function widget_untappd()
{
    require_once(ABSPATH.'wp-includes/rss-functions.php');

    $wp_untappd_rss = get_option('untappd_rss');
    $wp_untappd_title = (get_option('untappd_title') == '') ? "Beers I'm Drinking" : get_option('untappd_title');
    $wp_untappd_userid = get_option('untappd_userid');
    
	echo wptexturize('<div id="widget_untappd">');
	echo wptexturize('<div id="widget_untappd_header">' . $wp_untappd_title . '</div>');

	echo wptexturize('<div id="widget_untappd_beer_list">');
	
	if ($wp_untappd_rss != '') {
    	$rss = fetch_rss($wp_untappd_rss);
    	
    	if (count($rss->items) != 0) {
        	$limit = 5;
        	foreach ($rss->items as $item) {
        	    if ($limit == 0) {
        	        break;
        	    }
        		echo wptexturize('<div class="widget_untappd_beer"><a target="_blank" href="' . $item['link'] . '">' . $item['title'] . '</a></div>');
        		$limit++;
        	}
    	} else {
    	    echo wptexturize('<div class="widget_untappd_beer">What?!?  No beers yet?!?<br /><br /></div>');
    	}
	} else {
	    echo wptexturize('<div class="widget_untappd_beer">Widget not setup yet<br /><br /></div>');
	}

	echo wptexturize('</div>');
	if ($wp_untappd_userid != '') {
	    echo wptexturize('<div id="widget_untappd_follow"><a target="_blank" href="http://untappd.com/user/' . $wp_untappd_userid . '">Follow me on Untappd!</a></div>');
	} else {
	    echo wptexturize('<div id="widget_untappd_follow"><a target="_blank" href="http://untappd.com/">Drink Socially at Untappd!</a></div>');
	}
	echo wptexturize('</div>');
	echo wptexturize('<div id="widget_untappd_logo"><a target="_blank" href="http://untappd.com"><span></span></a></div>');
}

// Add some CSS
function widget_untappd_css() {
  
	$css = '
		#widget_untappd { 
			background-image: url(' . WP_PLUGIN_URL . '/untappd/images/untappd_bg.png);
			-moz-border-radius: 8px;
			border-radius: 8px;
			-webkit-border-radius: 8px;
			border: 1px solid #CC6600;
			margin-top: 10px;
		}
		#widget_untappd_header {
			color: #FFFFFF;
			font-size: 20px;
			text-align: center;
			padding: 4px;
			line-height: 25px;
		}
		#widget_untappd_beer_list {
		    background-color: #F9F0AC;
		    margin: 0px;
		    padding: 0px;
		}
		.widget_untappd_beer {
			padding: 4px 4px 4px 40px;
			background-image: url(' . WP_PLUGIN_URL . '/untappd/images/badge-beer-default.png);
			background-repeat: no-repeat;
			background-position: 4px 4px;
		}
		.widget_untappd_beer a {
			color: #CC6600;
		}
		#widget_untappd_follow {
			line-height: 25px;
			padding: 4px;
			text-align: center;
		}
		#widget_untappd_follow a {
			color: #FFFFFF;
		}
		#widget_untappd_logo a span {
			width: 93px;
			height: 40px;
			background-image: url(' . WP_PLUGIN_URL . '/untappd/images/untappd.png);
			display: block;
			margin: 0 auto;
		}
		#widget_untappd_logo {
			padding: 10px 0px;
		}
	';
	
	echo '<style type="text/css">' . $css . '</style>';
	
}

// Adds CSS to head
add_action('wp_head', 'widget_untappd_css');

function widget_untappd_init()
{
	$widget_ops = array(
		'class'       => 'widget_untappd', 
		'description' => __('Untappd Widget')
    );
    
	wp_register_sidebar_widget('untappd', __('Untappd'), 'widget_untappd', $widget_ops);
}

add_action('init', 'widget_untappd_init');

function showuntappd_admin()
{
	if ($_POST["untappd_action"]) {
		update_option('untappd_rss', $_POST['untappd_rss']);
		update_option('untappd_userid', stripslashes($_POST['untappd_userid']));
		update_option('untappd_title', stripslashes($_POST['untappd_title']));

		$message = "<div><p>Untappd RSS Options successfully updated.</p></div><br/>";
	}
?>

	<div class="wrap">
		<h2>Untappd Widget</h2>
	    <?php if ($strMessage <> "") { print $strMessage; } ?>
	    
		<form method="post" action="">
			<p>
			<label for="untappd_rss">Your Untappd RSS link *:</label> 
			<input name="untappd_rss" type="text" id="untappd_rss" value="<?php echo get_option('untappd_rss'); ?>" size="50" />
			</p>
			<p>
			<label for="untappd_userid">Your Untappd User ID:</label>
			<input name="untappd_userid" type="text" id="untappd_userid" value="<?php echo get_option('untappd_userid'); ?>" size="15" />
			</p>
			<p>
			<label for="untappd_title">Widget Title:</label>
			<input name="untappd_title" type="text" id="untappd_title" value="<?php echo get_option('untappd_title'); ?>" size="25" />
			</p>
			<p class="submit">
				<input type="submit" name="untappd_action" value="Update Options" /> 
			</p>
			<b>* To find your RSS link:</b>
			<div>
				- Login to <a href="http://www.untappd.com/">Untappd.com</a><br />
				- After you have checked in at least once, on the right side of your dashboard you should have a section called "Top 6 beers"<br />
				- Click on the <b>See Your Distinct Beers</b> link in that section<br />
				- The link to <b>RSS Feed</b> should be to the right under the <b>Your Beer History List</b> header<br />
				- Copy the link and paste it above.<br />
			</div>
		</form>
	</div>

<?php 
}