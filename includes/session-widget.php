<?php 

class cr3ativ_session extends WP_Widget {

	// constructor
	function cr3ativ_session() {
        parent::WP_Widget(false, $name = __('Session Loop', 'cr3at_conf') );
    }

	// widget form creation
	function form($instance) { 
// Check values
 if( $instance) { 
     $title = esc_attr($instance['title']); 
     $sessionspeakers = esc_attr($instance['sessionspeakers']);
     $sessionlocation = esc_attr($instance['sessionlocation']);
} else { 
     $title = ''; 
     $sessionspeakers = '';
     $sessionlocation = '';
} 
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'cr3at_conf'); ?></label>
<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" style="float:right; width:56%;" />
</p>
<p>
<label for="<?php echo $this->get_field_id('sessionspeakers'); ?>"><?php _e('Show speakers for the sessions?', 'cr3at_conf'); ?></label>
<input id="<?php echo $this->get_field_id('sessionspeakers'); ?>" name="<?php echo $this->get_field_name('sessionspeakers'); ?>" type="checkbox" value="1" <?php checked( '1', $sessionspeakers ); ?> style="float:right; margin-right:6px;" />
</p>
<p>
<label for="<?php echo $this->get_field_id('sessionlocation'); ?>"><?php _e('Show location for the sessions?', 'cr3at_conf'); ?></label>
<input id="<?php echo $this->get_field_id('sessionlocation'); ?>" name="<?php echo $this->get_field_name('sessionlocation'); ?>" type="checkbox" value="1" <?php checked( '1', $sessionlocation ); ?> style="float:right; margin-right:6px;" />
</p>


<?php }
	// widget update
	function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['sessionspeakers'] = strip_tags($new_instance['sessionspeakers']);
      $instance['sessionlocation'] = strip_tags($new_instance['sessionlocation']);
     return $instance;
}

	// widget display
	function widget($args, $instance) {
   extract( $args );
   // these are the widget options
   $title = apply_filters('widget_title', $instance['title']);
   $sessionspeakers = $instance['sessionspeakers'];
   $sessionlocation = $instance['sessionlocation'];
   echo $before_widget;

      
        global $post;
        add_filter('posts_orderby','cr3ativoderby2');
        $args = array(
		'post_type' => 'cr3ativconference',
                'posts_per_page' => 99999999,
                'meta_key' => 'cr3ativconfmeetingdate',

                'meta_query' => array(
                    array(
                'key' => 'cr3ativconfmeetingdate',
                ),
                    array(
                'key' => 'cr3ativ_confstarttime',
                ),
                ),
                );
        remove_filter('posts_orderby','cr3ativoderby2');
   
    query_posts($args);  
          
        $sessiondate = '';
         
   
   // Check if title is set
   if ( $title ) {
      echo $before_title . $title . $after_title;
   }	
   
   // Display the widget
    ?> 
		<?php if (have_posts($args)) : while (have_posts()) : the_post(); 

        $cr3ativconfmeetingdate = get_post_meta($post->ID, 'cr3ativconfmeetingdate', $single = true); 
        $confstarttime = get_post_meta($post->ID, 'cr3ativ_confstarttime', $single = true);
        $confendtime = get_post_meta($post->ID, 'cr3ativ_confendtime', $single = true); 
        $conflocation = get_post_meta($post->ID, 'cr3ativ_conflocation', $single = true); 
        
        ?>
    
     <div class="sessionwidget">
         <?php $dateformat = get_option('date_format'); ?>
         
            <?php $dateformat = get_option('date_format'); ?>
            
            <?php if ($sessiondate != (date($dateformat, $cr3ativconfmeetingdate))){ ?>
                
            <h1 class="conference_date"><?php echo date($dateformat, $cr3ativconfmeetingdate); ?></h1>
 
            <h2 class="meeting_date"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'cr3at_conf' ); ?>  <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            
            <?php } else { ?>
            
            <h2 class="meeting_date"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'cr3at_conf' ); ?>  <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            
            <?php } ?>
         
            <?php $dateformat = get_option('date_format'); ?>
            
            <?php $sessiondate = date($dateformat, $cr3ativconfmeetingdate); ?>
            
            <!-- Start of conference time -->
            <div class="conference-time">
                <?php if ($confstarttime != ('')){ ?>
                <?php echo ($confstarttime); ?>
                <?php } ?>
                <?php if ($confendtime != ('')){ ?>
                &nbsp;-&nbsp;
                <?php echo ($confendtime); ?>
                <?php } ?>
            </div><!-- End of conference time -->
         
         <?php if( $sessionlocation == '1' ) { ?>
            
            <!-- Start of conference location -->
            <div class="conference-location">
                <?php if ($conflocation != ('')){ ?>
                <?php echo stripslashes($conflocation); ?> 
                <?php } ?>
            </div><!-- End of conference location -->
         
         <?php } ?>
         
         <?php if( $sessionspeakers == '1' ) { ?>
            
            <!-- Start of speaker list -->
            <div class="speaker_list">
            <?php
	         $cr3ativ_confspeakers = get_post_meta($post->ID, 'cr3ativ_confspeaker', $single = true); 
	        ?>    
            <?php
	        if ( $cr3ativ_confspeakers ) { 
				
	        	foreach ( $cr3ativ_confspeakers as $cr3ativ_confspeaker ) :
	        	
	        		$speaker = get_post($cr3ativ_confspeaker);
                    echo'<div class="speaker_list_wrapper">';
	        		echo get_the_post_thumbnail($speaker->ID).'<a href="'. $speaker->guid .'">'. $speaker->post_title .'</a></div>'; 
				
				endforeach; 
				
			} ?>
            </div><!-- End of speaker list -->
         
         <?php } ?>

        </div>

        <?php endwhile; ?>

        <?php else: ?> 
        <p><?php _e( 'There are no posts to display. Try using the search.', 'cr3at_conf' ); ?></p> 

        <?php endif; wp_reset_query(); ?>
  
<?php     
   
   echo $after_widget;
}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("cr3ativ_session");'));

?>