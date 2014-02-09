<?php
/*
Class Name: daumview_subscribe_widget of Extended WP_Widget
Widget URL: http://widgetbank.daum.net/widget/view/739
*/

if ( class_exists( "WP_Widget" ) ) {
	class QB_Subscribe_Widget extends WP_Widget {
	    function QB_Subscribe_Widget() {
			$widget_ops = array( 'description' => 'DaumView 플러그인의 구독 위젯을 출력해줍니다.' );
			$control_ops = array( 'width' => 250, 'height' => 300 );
			parent::WP_Widget( false, $name='DaumView 구독위젯', $widget_ops, $control_ops );
	    }
	
		/* Displays the Widget in the front-end */
	    function widget( $args, $instance ){
			extract($args);
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'DaumView 구독위젯' : esc_html( $instance['title'] ) );
			$daumID = empty( $instance['daumID'] ) ? '' : $instance['daumID'];
			$subscribeToColor = empty( $instance['subscribeToColor'] ) ? '' : $instance['subscribeToColor'];
			$subscribeToReceiveColor = empty( $instance['subscribeToReceiveColor'] ) ? '' : $instance['subscribeToReceiveColor'];
	
			echo $before_widget;
	
			if ( $title )
				echo $before_title . $title . $after_title;
				
			if ( $daumID ) { ?>
			
			<script src="http://widgetprovider.daum.net/view?url=http://widgetcfs1.daum.net/xml/30/widget/2010/03/22/10/20/4ba6c5c0be6c3.xml&up_bgcolor1=<?php echo $subscribeToReceiveColor; ?>&up_DAUM_WIDGETBANK_LOGINID=<?php echo $daumID; ?>&up_bgcolor2=<?php echo $subscribeToColor; ?>&width=132&height=152&widgetId=739&scrap=1" type="text/javascript"></script>
		<?php
			}
			echo $after_widget;
		}
	
		/*Saves the settings. */
	    function update( $new_instance, $old_instance ){
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['daumID'] = strip_tags( $new_instance['daumID'] );
			$instance['subscribeToColor'] = $new_instance['subscribeToColor'];
			$instance['subscribeToReceiveColor'] = $new_instance['subscribeToReceiveColor'];
	
			return $instance;
		}
	
		/*Creates the form for the widget in the back-end. */
	    function form( $instance ){
			//Defaults
			$instance = wp_parse_args( (array) $instance, array( 'title'=>'DaumView 구독위젯', 'daumID'=>'', 'subscribeToColor'=>'0', 'subscribeToReceiveColor'=>'1' ) );
	
			$title = esc_attr( $instance['title'] );
			$daumID = $instance['daumID'];
			$subscribeToColor = esc_textarea( $instance['subscribeToColor'] );
			$subscribeToReceiveColor = esc_textarea( $instance['subscribeToReceiveColor'] );
			
			# Title
			echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
			# Daum ID
			echo '<p><label for="' . $this->get_field_id('daumID') . '">' . 'Daum ID:' . '</label><input class="widefat" id="' . $this->get_field_id('daumID') . '" name="' . $this->get_field_name('daumID') . '" type="text" value="' . $daumID . '" /></p>';
			# 구독받는 색상
			echo '<p><label for="' . $this->get_field_id('subscribeToColor') . '">' . '구독받는 색상:' . '</label><select id="' . $this->get_field_id('subscribeToColor') . '" name="' . $this->get_field_name('subscribeToColor') . '"><option value="0" ' . ( $subscribeToColor == 0 ? 'selected="selected"' : '' ) . '>하늘색</option><option value="1" ' . ( $subscribeToColor == 1 ? 'selected="selected"' : '' ) . '>연두색</option><option value="2" ' . ( $subscribeToColor == 2 ? 'selected="selected"' : '' ) . '>분홍색</option></select></p>';
			# 구독하는 색상
			echo '<p><label for="' . $this->get_field_id('subscribeToReceiveColor') . '">' . '구독받는 색상:' . '</label><select id="' . $this->get_field_id('subscribeToReceiveColor') . '" name="' . $this->get_field_name('subscribeToReceiveColor') . '"><option value="0" ' . ( $subscribeToReceiveColor == 0 ? 'selected="selected"' : '' ) . '>하늘색</option><option value="1" ' . ( $subscribeToReceiveColor == 1 ? 'selected="selected"' : '' ) . '>연두색</option><option value="2" ' . ($subscribeToReceiveColor == 2 ? 'selected="selected"' : '' ) . '>분홍색</option></select></p>';
		}
	}
	add_action( 'widgets_init', create_function( '', 'return register_widget("QB_Subscribe_Widget");' ) );
}
?>