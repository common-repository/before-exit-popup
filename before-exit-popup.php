<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 Plugin Name: Before Exit Popup
 Description: Show relative posts before closing the browser tab in popup.
 Plugin URI: https://wordpress.org/plugins/before-exit-popup
 Version: 1.0
 Author: Webgensis
 Author URI: http://www.webgensis.com
 Text Domain: before-exit-popup
 */

/*  Copyright 2016-2017	webgensis  (email : info@webgensis.com)

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

// Version of the plugin
define('WEBGENSIS_EXIT_POPUP_CURRENT_VERSION', '1.0' );

//our plugin name to be used at multiple places
define( 'WEBGENSIS_EXIT_POPUP_PLUGIN_NAME', "Before Exit Popup" );

//We'll key on the slug, set it here so it can be used in various places
define( 'WEBGENSIS_EXIT_POPUP_PLUGIN_SLUG', plugin_basename( __FILE__ ) );

//plugin admin scripts
function webgensis_exit_popup_admin_style() {
        wp_register_style( 'webgensis_exit_popup_admin_css', plugin_dir_url( __FILE__ ) . 'inc/admin/css/webgensis-exit-popup-admin.css',1.0,true );
        wp_enqueue_style( 'webgensis_exit_popup_admin_css' );

        wp_register_script( 'webgensis_exit_popup_admin_js', plugin_dir_url( __FILE__ ) . 'inc/admin/js/webgensis-exit-popup-admin.js',1.0,true );
        wp_enqueue_script( 'webgensis_exit_popup_admin_js' );
}
add_action( 'admin_enqueue_scripts', 'webgensis_exit_popup_admin_style' );

//registering scripts and style
function webgensis_exit_popup_scripts() {
    wp_enqueue_style( 'webgensis_exit_popup_style', plugin_dir_url( __FILE__ ) . 'inc/css/before-exit-popup.css',1.0,true );
    wp_enqueue_script( 'webgensis_exit_popup_popup_script', plugin_dir_url( __FILE__ ) . 'inc/js/before-exit-popup.js',1.0, true );
}
add_action( 'wp_footer', 'webgensis_exit_popup_scripts' ); 


//Create an admin menu
add_action( 'admin_menu', 'webgensis_register_settings' );

//function for admin menu page and required options
function webgensis_register_settings()
{
	//adding page unser wordpress settings
    add_options_page( WEBGENSIS_EXIT_POPUP_PLUGIN_NAME.'-Settings', 'Exit Popup Settings', 'manage_options', WEBGENSIS_EXIT_POPUP_PLUGIN_SLUG, 'webgensis_exit_popup_settings_page' );

    /***********************
    Add all required options
    ***********************/
    //option to select between related posts or selected posts
    add_option('webgensis_exit_popup_select_option', '0', '', 'yes' );
    //option to select limit of relative posts
	add_option('webgensis_exit_popup_relative_posts_limit', '4', '', 'yes' );
	//option to select between related posts or selected posts
    add_option('webgensis_exit_popup_selected_posts', '', '', 'yes' );

    /************************
    Register options settings 
    ************************/
    //creating setting group "webgensis_exit_popup_options_group" 
	register_setting( 'webgensis_exit_popup_options_group', 'webgensis_exit_popup_select_option');
	//creating setting group "webgensis_exit_popup_options_group" 
	register_setting( 'webgensis_exit_popup_options_group', 'webgensis_exit_popup_relative_posts_limit');
	//creating setting group "webgensis_exit_popup_options_group" 
	register_setting( 'webgensis_exit_popup_options_group', 'webgensis_exit_popup_selected_posts');
}

//This is our plugins settings page
function webgensis_exit_popup_settings_page()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting
            settings_fields('webgensis_exit_popup_options_group');
            ?>
			<table class="widefat fixed" cellspacing="0">
			<tr>
				<td>
					<label for="webgensis_exit_popup_select_option">Select option:</label>
				</td>
				<td>
					<input type="radio" id="webgensis_exit_popup_select_option" name="webgensis_exit_popup_select_option" value="0" onclick="show1();" <?php if (get_option('webgensis_exit_popup_select_option')==0) {
						echo "checked";
					} ?>> Related posts<br>

  					<input type="radio" id="webgensis_exit_popup_select_option" name="webgensis_exit_popup_select_option" onclick="show2();" value="1" <?php if (get_option('webgensis_exit_popup_select_option')==1) {
						echo "checked";
					} ?>> Selected posts<br>
					
				</td>
			</tr>
			<tr id="relative_posts">
				<td>
					<label for="webgensis_exit_popup_relative_posts_limit">Enter the number of relative posts which you want to show.</label>
				</td>
				<td>
					<input type="number" id="webgensis_exit_popup_relative_posts_limit" name="webgensis_exit_popup_relative_posts_limit" value="<?php echo get_option('webgensis_exit_popup_relative_posts_limit'); ?>" min=1 max=20 required/>
				</td>
			</tr>
			<tr id="selected_posts">
				<td>
					<label for="webgensis_exit_popup_selected_posts">Enter the comma (,) seperated IDs of the posts which you want to show.</label>
					<p>* Enter Ids like - 12,144,25,655,488</p>
				</td>
				<td>
					<input type="text" id="webgensis_exit_popup_selected_posts" name="webgensis_exit_popup_selected_posts" value="<?php echo get_option('webgensis_exit_popup_selected_posts'); ?>" />
				</td>
			</tr>
			</table>
			<?php  submit_button(); ?>
        </form>
    </div>
    <?php
}

//function to provide related posts args
function webgensis_exit_popup_related_posts( $post_id, $related_count, $args = array() ) {
	$args = wp_parse_args( (array) $args, array(
			'orderby' => 'rand',
			'return'  => 'query', // Valid values are: 'query' (WP_Query object), 'array' (the arguments array)
		) );
	if (get_option('webgensis_exit_popup_select_option')==1) {
		$selected_posts=get_option('webgensis_exit_popup_selected_posts');
		$selected_posts_array=explode(",",$selected_posts);
		$str='';
		foreach ($selected_posts_array as $key => $value) {
		    if (preg_match('/^[0-9]+$/', $selected_posts_array[$key])) 
		    {
		    	//we will count the string to match if array IDs are all numbers 
		        $str=$str."Y";
		    }
		}

		if(count(array_unique($selected_posts_array))==count($selected_posts_array)  && (count($selected_posts_array)==strlen($str)))
		{
		    //FURTHER CODE HERE WHEN ALL ELEMENTS UNIQUE AND VALID NUMBERS
		    $related_args = array(
			'posts_per_page' => count($selected_posts_array),
			'post_status'    => 'publish',
			'post__in'		 => $selected_posts_array,
			'orderby'        => $args['orderby']
			);
			if ( $args['return'] == 'query' ) {
				return new WP_Query( $related_args );
			} else {
				return $related_args;
			}
		}
		else
		{
		    //FURTHER CODE HERE WHEN NOT UNIQUE OR NOT A VALID NUMBERS
		    $related_args = array(
			'post_type'      => get_post_type( $post_id ),
			'posts_per_page' => $related_count,
			'post_status'    => 'publish',
			'post__not_in'   => array( $post_id ),
			'orderby'        => $args['orderby'],
			'tax_query'      => array()
			);

			$post       = get_post( $post_id );
			$taxonomies = get_object_taxonomies( $post, 'names' );

			foreach ( $taxonomies as $taxonomy ) {
				$terms = get_the_terms( $post_id, $taxonomy );
				if ( empty( $terms ) ) {
					continue;
				}
				$term_list                   = wp_list_pluck( $terms, 'slug' );
				$related_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $term_list
				);
			}

			if ( count( $related_args['tax_query'] ) > 1 ) {
				$related_args['tax_query']['relation'] = 'OR';
			}

			if ( $args['return'] == 'query' ) {
				return new WP_Query( $related_args );
			} else {
				return $related_args;
			}
		}

	}else{
		$related_args = array(
			'post_type'      => get_post_type( $post_id ),
			'posts_per_page' => $related_count,
			'post_status'    => 'publish',
			'post__not_in'   => array( $post_id ),
			'orderby'        => $args['orderby'],
			'tax_query'      => array()
		);

		$post       = get_post( $post_id );
		$taxonomies = get_object_taxonomies( $post, 'names' );

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			if ( empty( $terms ) ) {
				continue;
			}
			$term_list                   = wp_list_pluck( $terms, 'slug' );
			$related_args['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term_list
			);
		}

		if ( count( $related_args['tax_query'] ) > 1 ) {
			$related_args['tax_query']['relation'] = 'OR';
		}

		if ( $args['return'] == 'query' ) {
			return new WP_Query( $related_args );
		} else {
			return $related_args;
		}
	}

}

//function to get relative posts and show in popup
function webgensis_exit_popup_ouput(){
	?>
	<!-- modal content -->
	<div id="exit-popup-modal" class="exit-popup-modal" style="display:none;">
    	<div class="inner_outer">
       <div class="exit-popup-close" id="exit-popup-close">X</div>
     	 <div class="exit-popup">
        <div class="exit-popup-inner">
        
		<?php
		$max_posts=get_option( 'webgensis_exit_popup_relative_posts_limit' );
		$related = webgensis_exit_popup_related_posts( get_the_ID(), $max_posts);
		if ( $related->have_posts() ){
		?><ul><?php 
			 while ( $related->have_posts() ): $related->the_post(); 
		?>
        		<li>
       	 		<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
       	 			<?php
       	 			if (has_post_thumbnail()) {
       	 			 	the_post_thumbnail();
       	 			}else{
       	 				echo '<img src="'.plugin_dir_url( __FILE__ ) . 'inc/img/placeholder.jpg"';
       	 			}
       	 			?>
       	 			<?php the_title('<h3>','</h3>'); ?>
       	 		</a>
       	 		
        		</li>   
		<?php 
			endwhile;
		?></ul><?php
		}//if ends here
		wp_reset_postdata();
		?>
        </div>
        </div>
      </div>  
	</div>
	<?php
}
add_action( 'wp_footer', 'webgensis_exit_popup_ouput' );