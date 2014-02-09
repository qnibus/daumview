<?php
/*
Class Name: daumview_ranking_widget of Extended WP_Widget
Widget URL: http://widgetbank.daum.net/widget/view/434
*/

if ( class_exists( "WP_Widget" ) ) {
	class QB_Ranking_Widget extends WP_Widget {
	    function QB_Ranking_Widget() {
			$widget_ops = array( 'description' => 'DaumView 플러그인의 랭킹 위젯을 출력해줍니다.' );
			$control_ops = array( 'width' => 250, 'height' => 300 );
			parent::WP_Widget( false, $name='DaumView 랭킹위젯', $widget_ops, $control_ops );
	    }
	
		/* Displays the Widget in the front-end */
	    function widget( $args, $instance ){
			extract($args);
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'DaumView 랭킹위젯' : esc_html( $instance['title'] ) );
			$blogUrl = empty( $instance['blogUrl'] ) ? '' : esc_url( $instance['blogUrl'] );
			$displayType = empty( $instance['displayType'] ) ? '' : $instance['displayType'];
	
			echo $before_widget;
	
			if ( $title )
				echo $before_title . $title . $after_title;
				
			if ( $blogUrl ) { ?>
			
			<script src="http://widgetprovider.daum.net/view?url=http://widgetcfs1.daum.net/xml/15/widget/2009/07/22/17/20/4a66cbbacfa56.xml&up_DAUM_WIDGETBANK_BLOG_URL=<?php echo $blogUrl; ?>&up_init_pan=<?php echo $displayType; ?>&&width=166&height=155&widgetId=434&scrap=1" type="text/javascript"></script>
		<?php
			}
			echo $after_widget;
		}
	
		/*Saves the settings. */
	    function update( $new_instance, $old_instance ){
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['blogUrl'] = esc_url( $new_instance['blogUrl'] );
			$instance['displayType'] = $new_instance['displayType'];
	
			return $instance;
		}
	
		/*Creates the form for the widget in the back-end. */
	    function form( $instance ){
			//Defaults
			$instance = wp_parse_args( (array) $instance, array( 'title'=>'DaumView 구독위젯', 'daumID'=>'', 'subscribeToColor'=>'0', 'subscribeToReceiveColor'=>'1' ) );
	
			$title = esc_attr( $instance['title'] );
			$blogUrl = esc_url( $instance['blogUrl'] );
			$displayType = esc_textarea( $instance['subscribeToColor'] );
			
			# Title
			echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
			# 블로그 주소
			echo '<p><label for="' . $this->get_field_id('blogUrl') . '">' . '블로그주소:' . '</label><input class="widefat" id="' . $this->get_field_id('blogUrl') . '" name="' . $this->get_field_name('blogUrl') . '" type="text" value="' . $blogUrl . '" /></p>';
			# 기본 선택판
			echo '<p><label for="' . $this->get_field_id('displayType') . '">' . '기본선택판:' . '</label><select id="' . $this->get_field_id('displayType') . '" name="'. $this->get_field_name('displayType') .'"><option value="0" ' . ($displayType == 0 ? 'selected="selected"' : '') . '>전체순위</option><option value="1" ' . ( $displayType == 1 ? 'selected="selected"' : '' ).'>전체순위그래프</option><option value="2" ' . ( $displayType == 2 ? 'selected="selected"' : '' ) . '>오늘 순위</option></select></p>';
		}
	}
	add_action( 'widgets_init', create_function( '', 'return register_widget("QB_Ranking_Widget");' ) );
}
?>