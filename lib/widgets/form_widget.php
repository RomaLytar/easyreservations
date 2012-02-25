<?php
/**
 * Foo_Widget Class
 */
class easyReservations_form_widget extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::WP_Widget( /* Base ID */'easyReservations_form_widget', /* Name */'easyReservations Widget', array( 'description' => 'easyReservations form and calendar widget' ) );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$calendar = esc_attr( $instance[ 'calendar' ] );
		$calendar_style = esc_attr( $instance[ 'calendar_style' ] );
		$calendar_price = esc_attr( $instance[ 'calendar_price' ] );
		$calendar_width = esc_attr( $instance[ 'calendar_width' ] );
		$calendar_height = esc_attr( $instance[ 'calendar_height' ] );
		$calendar_room = esc_attr( $instance[ 'calendar_room' ] );
		$form_url = esc_attr( $instance[ 'form_url' ] );
		$form_button = esc_attr( $instance[ 'form_button' ] );
		$form_editor = esc_attr( $instance[ 'form_editor' ] );
		
		if($calendar_price == "on") $showPrice = 1;
		else $showPrice = 0;
		
		if($calendar_width == 0 OR empty($calendar_width)) $calendar_width = 180;
		$theForm = stripslashes($form_editor);

		preg_match_all(' /\[.*\]/U', $theForm, $matches);
		$mergearray=array_merge($matches[0], array());
		$edgeoneremove=str_replace('[', '', $mergearray);
		$edgetworemoves=str_replace(']', '', $edgeoneremove);
		$customPrices = 0;
		$form_date = 0;

		foreach($edgetworemoves as $fields){
			$field=array_values(array_filter(preg_split('/("[^"]*"|\'[^\']*\'|\s+)/', str_replace("\\", "", $fields), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE), 'trim'));
			if($field[0]=="date-from"){
				$theForm=str_replace('['.$fields.']', '<input id="easy-widget-datepicker-from" type="text" name="from" value="'.date("d.m.Y", time()).'">', $theForm); $form_date++;
			} elseif($field[0]=="date-to"){
				$theForm=str_replace('['.$fields.']', '<input id="easy-widget-datepicker-to"  type="text" name="to" value="'.date("d.m.Y", time()+172800).'">', $theForm); $form_date++;
			} elseif($field[0]=="nights"){
				if(isset($field[1])) $number=$field[1]; else $number=31;
				$theForm=preg_replace('/\['.$fields.'\]/', '<select name="nights">'.easyReservations_num_options(1,$number).'</select>', $theForm);
			} elseif($field[0]=="persons"){
				if($field[1]=="Select"){
					if(isset($field[2])) $number=$field[2]; else $number = 6;
					$theForm=preg_replace('/\['.$fields.'\]/', '<select name="persons">'.easyReservations_num_options(1,$number).'</select>', $theForm);
				} elseif($field[1]=="text"){
					$theForm=preg_replace('/\['.$fields.'\]/', '<input id="easy-widget-persons" name="persons" type="text" size="70px">', $theForm);
				}
			} elseif($field[0]=="childs"){
				if($field[1]=="Select"){
					if(isset($field[2])) $number=$field[2]; else $number = 6;
					$theForm=preg_replace('/\['.$fields.'\]/', '<select name="childs">'.easyReservations_num_options(0,$number).'</select>', $theForm);
				} elseif($field[1]=="text"){
					$theForm=preg_replace('/\['.$fields.'\]/', '<input name="childs" type="text" size="70px">', $theForm);
				}
			} elseif($field[0]=="thename"){
				$theForm=preg_replace('/\['.$fields.'\]/', '<input type="text" id="easy-widget-thename" name="thename">', $theForm);
			}  elseif($field[0]=="email"){
				$theForm=preg_replace('/\['.$fields.'\]/', '<input id="easy-widget-email"  type="text" name="email">', $theForm);
			} elseif($field[0]=="country"){
				$theForm=str_replace('['.$fields.']', '<select id="easy-widget-country" name="country">'.easyReservations_country_select('').'</select>', $theForm);
			}  elseif($field[0]=="message"){
				$theForm=preg_replace('/\['.$fields.'\]/', '<textarea type="text" name="message" style="width:200px; height: 100px;"></textarea>', $theForm);
			} elseif($field[0]=="rooms"){		
				if($calendar == true) $calendar_action = 'document.widget_formular.room.value=this.value;easyRes_sendReq_widget_Calendar();'; else $calendar_action = '';
				$theForm=str_replace('['.$fields.']', '<select name="room" id="form_room" onchange="'.$calendar_action.'">'.reservations_get_room_options().'</select>', $theForm);
			} elseif($field[0]=="offers"){
				if($calendar == true AND $showPrice == 1) $calendar_action = 'document.widget_formular.offer.value=this.value;easyRes_sendReq_widget_Calendar();'; else $calendar_action = '';
				$theForm=preg_replace('/\['.$fields.'\]/', '<select name="offer" id="form_offer" onchange="'.$calendar_action.'"><option value="0" select="selected">'. __( 'None' , 'easyReservations' ).'</option>'.reservations_get_offer_options().'</select>', $theForm);
			}
		}

		echo $before_widget;
		if($title) echo $before_title . $title . $after_title;
		if($calendar == "on"){?>
			<input type="hidden" id="urlWidgetCalendar" value="<?php echo RESERVATIONS_JS_DIR; ?>/ajax/send_calendar.php">
			<form name="widget_formular" id="widget_formular">
				<input type="hidden" name="room" onChange="easyRes_sendReq_widget_Calendar()" value="<?php echo $calendar_room; ?>">
				<input type="hidden" name="offer" onChange="easyRes_sendReq_widget_Calendar()" value="0">
				<input type="hidden" name="date" onChange="easyRes_sendReq_widget_Calendar()" value="0">
				<input type="hidden" name="size" value="<?php echo $calendar_width.','.$calendar_height.','.$showPrice.','.$calendar_style; ?>">
			</form>
			<div id="show_widget_calendar"></div>
			<script>
				easyRes_sendReq_widget_Calendar();
			</script><?php 

		}  if(isset($form_url) AND !empty($form_url)){ ?>
		<form method="post" action="<?php echo $form_url; ?>" name="easy_widget_form" id="easy_widget_form">
			<?php
		} if($form_date > 0){?>
			<script>
				jQuery(document).ready(function() {
					jQuery("#easy-widget-datepicker-from").datepicker( { dateFormat: 'dd.mm.yy', style: 'font-size:1em' });
					jQuery("#easy-widget-datepicker-to").datepicker( { dateFormat: 'dd.mm.yy' });
				});
			</script><?php
		}
		echo htmlspecialchars_decode($theForm);
			
		if(isset($form_url) AND !empty($form_url)){ ?>
			<input type="submit" class="easybutton" value="<?php echo $form_button; ?>">
		</form>
			<?php
		}
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['calendar'] = strip_tags($new_instance['calendar']);
		$instance['calendar_style'] = strip_tags($new_instance['calendar_style']);
		$instance['calendar_width'] = strip_tags($new_instance['calendar_width']);
		$instance['calendar_height'] = strip_tags($new_instance['calendar_height']);
		$instance['calendar_price'] = strip_tags($new_instance['calendar_price']);
		$instance['calendar_room'] = strip_tags($new_instance['calendar_room']);
		$instance['form_url'] = strip_tags($new_instance['form_url']);
		$instance['form_editor'] = $new_instance['form_editor'];
		$instance['form_button'] = strip_tags($new_instance['form_button']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$calendar_height = esc_attr( $instance[ 'calendar_height' ] );
			$calendar_style = esc_attr( $instance[ 'calendar_style' ] );
			$calendar_width = esc_attr( $instance[ 'calendar_width' ] );
			$calendar_room = esc_attr( $instance[ 'calendar_room' ] );
			$form_url = esc_attr( $instance[ 'form_url' ] );
			$form_button = esc_attr( $instance[ 'form_button' ] );
			$form_editor = esc_attr( $instance[ 'form_editor' ] );
		}
		else {
			$title = __( 'New title', 'text_domain' );
			$calendar_width = 180;
			$calendar_height = 140;
			$calendar_style = 1;
			$form_url = __( 'type in URL to a form', 'text_domain' );
			$form_button = __( 'Reser now!', 'text_domain' );
			$form_editor = '[date-from] - [date-to]<br>
<b><u>Room:</u></b> [rooms]<br>
<b><u>Offer:</u></b> [offers]<br>';
		} //<?php checked( (bool) $instance['calendar_room'], true );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'easyReservations'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('calendar'); ?>"><?php _e('Show calendar:', 'easyReservations'); ?>
			<input id="<?php echo $this->get_field_id('calendar'); ?>" <?php checked( (bool) $instance['calendar'], true ); ?> name="<?php echo $this->get_field_name('calendar'); ?>" type="checkbox" /></label> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('calendar_style'); ?>"><?php _e('Calendar style:', 'easyReservations'); ?>
			<select id="<?php echo $this->get_field_id('calendar_style'); ?>" name="<?php echo $this->get_field_name('calendar_style'); ?>" ><option value="1" <?php selected( $calendar_style, 1 ); ?>>simple</option><option value="2" <?php selected( $calendar_style, 2 ); ?>>modern</option></select></label> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('calendar_room'); ?>"><?php _e('Default room:', 'easyReservations'); ?>
			<select id="<?php echo $this->get_field_id('calendar_room'); ?>" name="<?php echo $this->get_field_name('calendar_room'); ?>"><?php echo reservations_get_room_options($calendar_room); ?></select></label> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('calendar_width'); ?>"><?php _e('Calendar width:', 'easyReservations'); ?></label> 
			<input class="widefat" style="width:40px" id="<?php echo $this->get_field_id('calendar_width'); ?>" name="<?php echo $this->get_field_name('calendar_width'); ?>" type="text" value="<?php echo $calendar_width; ?>" /> px
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('calendar_height'); ?>"><?php _e('Calendar height:', 'easyReservations'); ?></label> 
			<input class="widefat" style="width:40px" id="<?php echo $this->get_field_id('calendar_height'); ?>" name="<?php echo $this->get_field_name('calendar_height'); ?>" type="text" value="<?php echo $calendar_height; ?>" /> px
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('calendar_price'); ?>"><?php _e('Show price in calendar:', 'easyReservations'); ?>
			<input id="<?php echo $this->get_field_id('calendar_price'); ?>" <?php checked( (bool) $instance['calendar_price'], true ); ?> name="<?php echo $this->get_field_name('calendar_price'); ?>" type="checkbox" /></label> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('form_editor'); ?>"><?php _e('Edit form:', 'easyReservations'); ?><br>
			<textarea id="<?php echo $this->get_field_id('form_editor'); ?>" name="<?php echo $this->get_field_name('form_editor'); ?>" cols="34" rows="10"><?php echo $form_editor; ?></textarea></label> 
		</p>
		<code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[date-from]';">[date-from]</code>, <code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[date-to]';">[date-to]</code>, <code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[persons Select]';">[persons *]</code>, <code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[childs Select]';">[childs *]</code>, <code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[offers]';">[offers]</code>, <code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[rooms]';">[rooms]</code>, <code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[country]';">[country]</code>, <code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[email]';">[email]</code>, <code style="cursor:pointer" onclick="document.getElementById('<?php echo $this->get_field_id('form_editor'); ?>').value += '[thename]';">[thename]</code>
		<p>
			<label for="<?php echo $this->get_field_id('form_url'); ?>"><?php _e('Form URL:', 'easyReservations'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('form_url'); ?>" name="<?php echo $this->get_field_name('form_url'); ?>" type="text" value="<?php echo $form_url; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('form_button'); ?>"><?php _e('Submit button:', 'easyReservations'); ?>
			<input class="widefat" style="width:80px" id="<?php echo $this->get_field_id('form_button'); ?>" name="<?php echo $this->get_field_name('form_button'); ?>" type="text" value="<?php echo $form_button; ?>" /></label> 
		</p>

		<?php 
	}

} // class Foo_Widget
// register Foo_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget("easyReservations_form_widget");' ) );
 ?>