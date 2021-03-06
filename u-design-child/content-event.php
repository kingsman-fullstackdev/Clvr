<?php
/**
 * The template for displaying posts in the Venue post format
 *
 * @package WordPress
 * @subpackage U-design
 * @since Ven
 */
?>
<?php
	$screen_date = isset($_GET['s_date']) ? trim($_GET['s_date']) : '';

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<header class="entry-header">

		<?php
			global $post;
			if ( is_single() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
			endif;
		?>
	</header><!-- .entry-header -->
<?php       udesign_single_post_entry_before(); ?>
                 <div class="entry">
<?php                  udesign_single_post_entry_top();
                       if( $udesign_options['display_post_image_in_single_post'] == 'yes' ) display_post_image_fn( $post->ID, false );

                       $event = em_get_event($post->ID);

                       //PRINT SHOW_TIMES

                       //get all showdates
                       $show_dates = get_event_show_dates($post->ID);
                       //print_r($show_dates);
                       if(in_array(date("m/d/Y",time()),$show_dates))
							$date_index = date("Y-m-d", time());
                       else
                       		$date_index = date("Y-m-d",strtotime($show_dates[0]));

                       //echo $date_index;
                       //$show_times = get_event_show_times($post->ID, $date_index);

                       $show_times = get_event_show_times_withlink($post->ID, $date_index);
                       //print content
                       $movie_info_array = get_field("movie");
						//my_var_dump($movie_info_array);
						$movie = $movie_info_array;
					   if(is_array($movie_info_array))
					   		$movie = $movie_info_array[0];

					   //print movie info

					   /* if(!empty($movie))
					   		$image= wp_get_attachment_image_src(get_post_thumbnail_id($movie->ID),array(258,271)); */
					   if($movie)
					   		$image = get_movie_image($movie);
?>
		<div class="movie_thumb">
		 <img src="<?php echo DENVER_ROOT.$image;?>" width="332" height="180"/>
		</div>
		<div class="movie_showtime">

		<h4>Date of Show</h4>

		<select class="buy_ticket">
			<?php
		 		if($show_dates) // means event has multiple dates not in sequence
				{
					foreach($show_dates as $show_date)
					{
					//	echo $subfields['show_date'];
						$date_info =  DateTime::createFromFormat('m/d/Y', trim($show_date));
					//	my_var_dump($date_info);
						$date_info = $date_info->format("D M. j, Y");
						//echo $date_info;
						$date_value= date("Y-m-d",strtotime($show_date));
						if($date_value == $date_index)
							$selected= "selected";
						else
							$selected= "";

 						echo "<option {$selected} value='{$date_value}'>{$date_info}</option>";
					}
				}else{
					for($i_date = strtotime($event->event_start_date); $i_date<=strtotime($event->event_end_date); $i_date+=86400)
					{
						$date_info = date("D M. j, Y", $i_date);
						$date_value = date("d/m/Y", $i_date);
						echo "<option value='{$date_value}'>{$date_info}</option>";
					}

				}
			?>


		</select>
		<p>Click on showtimes to buy tickets<br/>
		<strong>Show Times:
		<span id='showtimes'>
		<?php
			if($show_times)
			{
				//print_r($show_times);
				echo implode(" | ", $show_times);
			}else{

				echo date("H:i:sA",strtotime($event->event_start_time));
			}
		?>
		</span>
		 </strong><br/>
		</p>
		<span class="yellow"><?php echo get_field("duration",$movie->ID). " Minute Running Time";?></span>
		</div>
		<div class="clear"></div>
<?php
			//prints single event info generated by events setting...
			// the_content(__('<p class="serif">Read the rest of this entry &raquo;</p>', 'udesign'));

			//get movie info....
			$movie_content = apply_filters('the_content_movie', $movie);
			echo $movie_content;
?>
			<!--  Hot ticket submig -->
			<?php // echo hot_ticket_box(); ?>
<?php
						wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

<?php                       //    udesign_single_post_entry_bottom(); ?>
			    </div>
<?php                       udesign_single_post_entry_after(); ?>

</article><!-- #post-## -->
<script type="text/javascript">
//buy_rticket
jQuery(document).ready(function(){
	jQuery('.buy_ticket').change(function(obj){
		  var option = jQuery('option:selected',jQuery(this));
		  console.log(option);
		  var value = jQuery(this).val();
		  console.log(value);
		  var controller = '<?php echo CHILD_DIR ."/";?>'+"ajax/ajaxcontroller.php";
		//  var url = window.location.href.split('?')[0];
		// location.href=url+"?s_date="+value;
		console.log(controller);
		//return;
		  jQuery.post(controller,{action:"get_event_showtimes",event_id: <?php echo $post->ID; ?> ,date_index: value }, function(data){
			  jQuery('#showtimes').html(data);
			 });
	});
});

</script>