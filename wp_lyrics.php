<?php
/*
Plugin Name: WP-Lyrics
Plugin URI: http://wordpress.org/extend/plugins/wplyrics/
Description: Simple plugin to hide the lyrics of the songs you publish (they will be between [lyrics] and [/lyrics]). Allows you to customize the replacement text.
Author: Adrian Moreno
Version: 0.4.1
Author URI:  http://bloqnum.com
*/ 

/*  Copyright 2008  Adrian Moreno  (email : adrian.moreno at bloqnum.com)

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

if (!class_exists('wp_lyrics')) {
    class wp_lyrics	{
		
		/**
		* @var string   The name the options are saved under in the database.
		*/
		var $adminOptionsName = "wp_lyrics_options";
				
		/**
		* PHP 4 Compatible Constructor
		*/
		function wp_lyrics(){$this->__construct();}
		
		/**
		* PHP 5 Constructor
		*/		
		function __construct(){

		//COMENTADO!
		//add_action("admin_menu", "admin_print_scripts");		
		//add_action("init", "wp_print_scripts");
		add_action("init", array(&$this, 'add_scripts'));
		add_filter('the_content', array(&$this, 'hidelyrics'));
		$this->add_options();
		}
		
		
		/**
		* Called by the filter the_content
		* Filters the post content to hide the lyrics
		*/
		function hidelyrics($content = '') {
			
			$options = $this->load_options();
			
			$pre = "<h5 class='wplyricstitle'>";
			$pre .= $options['title'];
			$pre .= " <a class='wplyricslink' title='";
			$pre .= $options['more'];
			$pre .= "' onclick='open_lyrics(this);' style='cursor:pointer;display:none;' >";
			$pre .= $options['symbol1'];
			$pre .= "</a></h5><div class='wplyrics'>";
			
			$post = "</div>";
			
			$content = preg_replace("/\[lyrics\]/", $pre , $content);
			$content = preg_replace("/\[\/lyrics\]/", $post , $content);
			
			return $content;
		}
		
		function add_options(){
			add_option('wp_lyrics_title', 'Lyrics');	
			add_option('wp_lyrics_more', 'Show the lyrics...');	
			add_option('wp_lyrics_less', 'Hide the lyrics...');	
			add_option('wp_lyrics_symbol1', '[+]');	
			add_option('wp_lyrics_symbol2', '[-]');				
		}
		
		function load_options() {
		    $wp_lyrics_title = get_option('wp_lyrics_title');
		    $wp_lyrics_more = get_option('wp_lyrics_more');
		    $wp_lyrics_less = get_option('wp_lyrics_less');
   		    $wp_lyrics_symbol1 = get_option('wp_lyrics_symbol1');
   		    $wp_lyrics_symbol2 = get_option('wp_lyrics_symbol2');

			
			$options = array(
				'title' => $wp_lyrics_title,
				'more' => $wp_lyrics_more,
				'less' => $wp_lyrics_less,
				'symbol1' => $wp_lyrics_symbol1,
				'symbol2' => $wp_lyrics_symbol2				
			);
			return $options;
		}
		 
		
		
		/**
		* Tells WordPress to load the scripts
		*/
		function add_scripts(){			
			$options = $this->load_options();
			
			wp_enqueue_script('wp_lyrics_script', get_bloginfo('wpurl').'/wp-content/plugins/wplyrics/wplyrics.js', array("jquery") , 0.1); 
			wp_localize_script( 'wp_lyrics_script', 'WPLyricsSettings', array(
			  	'title' => $options['title'],
                'more' => $options['more'],
                'less' => $options['less'],
                'symbol1' => $options['symbol1'],
                'symbol2' => $options['symbol2']
			));
			
		}
		

    }
}

//instantiate the class
if (class_exists('wp_lyrics')) {
	$wp_lyrics = new wp_lyrics();
	add_action('admin_menu', 'add_admin_pages');

}
		
		/*
		Admin Pages
		*/
		
		function add_admin_pages(){
				add_options_page('WP Lyrics Options', 'WP Lyrics', 8, 'wp_lyrics', 'output_admin_page');
		}


		/**
		* Outputs the HTML for the admin page.
		*/
		function output_admin_page(){
	    // variables for the field and option names 
	    $updating = 'N';
	    $wp_lyrics_title = get_option('wp_lyrics_title');
	    $wp_lyrics_more = get_option('wp_lyrics_more');
	    $wp_lyrics_less = get_option('wp_lyrics_less');
	    $wp_lyrics_symbol1 = get_option('wp_lyrics_symbol1');
	    $wp_lyrics_symbol2 = get_option('wp_lyrics_symbol2');
	    

	    // Read in existing option value from database
	    $opt_val = get_option( $opt_name );
	
	    // See if the user has posted us some information
	    // If they did, this hidden field will be set to 'Y'
	    if( $_POST[ $updating ] == 'Y' ) {
	        // Read their posted value
	        $wp_lyrics_title_val = $_POST['wp_lyrics_title'];
	        $wp_lyrics_more_val = $_POST['wp_lyrics_more'];
	        $wp_lyrics_less_val = $_POST['wp_lyrics_less'];
	        $wp_lyrics_symbol1_val = $_POST['wp_lyrics_symbol1'];
	        $wp_lyrics_symbol2_val = $_POST['wp_lyrics_symbol2'];

	
	        // Save the posted value in the database
	        update_option( 'wp_lyrics_title', $wp_lyrics_title_val );
	        update_option( 'wp_lyrics_more', $wp_lyrics_more_val );
	        update_option( 'wp_lyrics_less', $wp_lyrics_less_val );
	        update_option( 'wp_lyrics_symbol1', $wp_lyrics_symbol1_val );
	        update_option( 'wp_lyrics_symbol2', $wp_lyrics_symbol2_val );

	
	        // Put an options updated message on the screen
			?>
			<div class="updated"><p><strong><?php _e('Options saved.', 'wp_lyrics' );?></strong></p></div>
			<?php } ?>
			
			<div class="wrap">
				<h2>Modify WP-Lyrics Options</h2>
				<p>Here you can customize the texts used by the plugin.</p>

				<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="<?php echo $updating; ?>" value="Y">
				
				<p><?php _e("Default title:", 'wp_lyrics' ); ?> 
				<input type="text" name="wp_lyrics_title" value="<?php echo $wp_lyrics_title; ?>" size="20">
				</p>
				
				<p><?php _e("View more text:", 'wp_lyrics' ); ?> 
				<input type="text" name="wp_lyrics_more" value="<?php echo $wp_lyrics_more; ?>" size="20">
				</p>								

				<p><?php _e("View less text:", 'wp_lyrics' ); ?> 
				<input type="text" name="wp_lyrics_less" value="<?php echo $wp_lyrics_less; ?>" size="20">
				</p>			
				
				<p><?php _e("Clickable 'View more' link text:", 'wp_lyrics' ); ?> 
				<input type="text" name="wp_lyrics_symbol1" value="<?php echo $wp_lyrics_symbol1; ?>" size="4">
				</p>
				
				<p><?php _e("Clickable 'View less' link text:", 'wp_lyrics' ); ?> 
				<input type="text" name="wp_lyrics_symbol2" value="<?php echo $wp_lyrics_symbol2; ?>" size="4">
				</p>									
				
				<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Update Options', 'wp_lyrics' ) ?>" />
				</p>
				
				</form>

			</div>
			<?php
		} 
?>