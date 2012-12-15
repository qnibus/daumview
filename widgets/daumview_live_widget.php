<?php
/*
Class Name: daumview_live_widget of Extended WP_Widget
Widget URL: http://widgetbank.daum.net/widget/view/395
*/

if ( class_exists( "WP_Widget" ) ) {
	class QB_Live_Widget extends WP_Widget {
	    function QB_Live_Widget() {
			$widget_ops = array( 'description' => 'DaumView 플러그인의 추천LIVE 위젯을 출력해줍니다.' );
			$control_ops = array( 'width' => 250, 'height' => 300 );
			parent::WP_Widget( false, $name='DaumView 추천 LIVE', $widget_ops, $control_ops );
	    }
	
		/* Displays the Widget in the front-end */
	    function widget( $args, $instance ) {
			extract($args);
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'DaumView 추천 LIVE' : esc_html( $instance['title'] ) );
	
			echo $before_widget;
	
			if ( $title )
				echo $before_title . $title . $after_title;
				
		?>	
			<script src="http://widgetprovider.daum.net/view?url=http://widgetcfs1.daum.net/xml/2/widget/2009/05/29/10/49/4a1f3f2cd61a0.xml&width=166&height=191&widgetId=395&scrap=1" type="text/javascript"></script>
		<?php
			echo $after_widget;
		}
	
		/*Saves the settings. */
	    function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
	
			return $instance;
		}
	
		/*Creates the form for the widget in the back-end. */
	    function form( $instance ){
			//Defaults
			$instance = wp_parse_args( (array) $instance, array( 'title'=>'DaumView 구독위젯', 'daumID'=>'', 'subscribeToColor'=>'0', 'subscribeToReceiveColor'=>'1' ) );
	
			$title = esc_attr( $instance['title'] );
			
			# Title
			echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
		}
	}
	add_action( 'widgets_init', create_function( '', 'return register_widget("QB_Live_Widget");' ) );
}
?>