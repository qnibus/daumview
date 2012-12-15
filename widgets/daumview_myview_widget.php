<?php
/*
Class Name: daumview_myview_widget of Extended WP_Widget
Widget URL: http://v.daum.net/my?exp=widget
*/

if ( class_exists( "WP_Widget" ) ) {
	class QB_Myview_Widget extends WP_Widget {
	    function QB_Myview_Widget() {
			$widget_ops = array( 'description' => 'DaumView 플러그인의 MY글 위젯을 출력해줍니다.' );
			$control_ops = array( 'width' => 250, 'height' => 300 );
			parent::WP_Widget( false, $name='DaumView MY글위젯', $widget_ops, $control_ops );
	    }
	
		/* Displays the Widget in the front-end */
	    function widget( $args, $instance ){
			extract($args);
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'DaumView MY글위젯' : esc_html( $instance['title'] ) );
			$daumID = empty( $instance['daumID'] ) ? '' : $instance['daumID'];
			$skin = empty( $instance['skin'] ) ? '' : $instance['skin'];
			$initType = empty( $instance['initType'] ) ? '' : $instance['initType'];
			$pageSize = empty( $instance['pageSize'] ) ? '' : $instance['pageSize'];
			$isFooter = empty( $instance['isFooter'] ) ? '' : $instance['isFooter'];
	
			echo $before_widget;
	
			if ( $title )
				echo $before_title . $title . $after_title;
				
			if ( $daumID ) {
				$height = 273 + ( ($pageSize - 7) * 26 ); ?>				

			<iframe src="http://api.v.daum.net/iframe/my_widget?skin=<?php echo $skin; ?>&page_size=<?php echo $pageSize; ?>&init_type=<?php echo $initType; ?>&is_footer=<?php echo $isFooter; ?>&daumid=<?php echo $daumID; ?>" width="100%" height="<?php echo $height; ?>" frameborder="0" scrolling="no" allowtransparency="true"></iframe>

		<?php
			}
			echo $after_widget;
		}
	
		/*Saves the settings. */
	    function update( $new_instance, $old_instance ){
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['daumID'] = strip_tags( $new_instance['daumID'] );
			$instance['skin'] = strip_tags( $new_instance['skin'] );
			$instance['initType'] = strip_tags( $new_instance['initType'] );
			$instance['pageSize'] = strip_tags( $new_instance['pageSize'] );
			$instance['isFooter'] = strip_tags( $new_instance['isFooter'] );
	
			return $instance;
		}
	
		/*Creates the form for the widget in the back-end. */
	    function form( $instance ){
			//Defaults
			$instance = wp_parse_args( (array) $instance, array( 'title'=>'DaumView 구독위젯', 'daumID'=>'', 'subscribeToColor'=>'0', 'subscribeToReceiveColor'=>'1' ) );
	
			$title = esc_attr( $instance['title'] );
			$daumID = $instance['daumID'];
			$skin = esc_textarea( $instance['skin'] );
			$initType = esc_textarea( $instance['initType'] );
			$pageSize = esc_textarea( $instance['pageSize'] );
			$isFooter = esc_textarea( $instance['isFooter'] );
			
			# Title
			echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
			# Daum ID
			echo '<p><label for="' . $this->get_field_id('daumID') . '">' . 'Daum ID:' . '</label><input class="widefat" id="' . $this->get_field_id('daumID') . '" name="' . $this->get_field_name('daumID') . '" type="text" value="' . $daumID . '" /></p>';
			# Skin
			echo '<p><label for="' . $this->get_field_id('skin') . '">' . '위젯 색상:' . '</label><select id="' . $this->get_field_id('skin') . '" name="'. $this->get_field_name('skin') . '"><option value="1" ' . ( $skin == 1 ? 'selected="selected"' : '' ) . '>기본형(파란색)</option><option value="2" ' . ( $skin == 2 ? 'selected="selected"' : '' ) . '>회색</option><option value="3" ' . ( $skin == 3 ? 'selected="selected"' : '' ) . '>청록색</option><option value="4" ' . ( $skin == 4 ? 'selected="selected"' : '' ) . '>갈색</option><option value="5" ' . ( $skin == 5 ? 'selected="selected"' : '' ) . '>검정색</option></select></p>';
			# Init type
			echo '<p><label for="' . $this->get_field_id('initType') . '">' . '기본 화면 설정:' . '</label><select id="' . $this->get_field_id('initType') . '" name="'. $this->get_field_name('initType') . '"><option value="" ' . ( $initType == '' ? 'selected="selected"' : '' ) . '>내 최신 글</option><option value="recommend" ' . ( $initType == 'recommend' ? 'selected="selected"' : '' ) . '>내가 추천한 글</option><option value="point" ' . ( $initType == 'point' ? 'selected="selected"' : '' ) . '>내 인기 글</option></select></p>';
			# Page size
			echo '<p><label for="' . $this->get_field_id('pageSize') . '">' . '노출글 갯수:' . '</label><select id="' . $this->get_field_id('pageSize') . '" name="'. $this->get_field_name('pageSize') . '">';
			for ( $i = 7; $i < 16; $i++ ) {
				echo '<option value="'.$i.'" ' . ( $pageSize == $i ? 'selected="selected"' : '' ) . '>' . $i . '개</option>';
			}
			echo '</select></p>';
			# Is footer
			echo '<p><label for="' . $this->get_field_id('isFooter') . '">' . '열린편집자 표시:' . '</label><input type="radio" id="' . $this->get_field_id('isFooter') . '" name="' . $this->get_field_name('isFooter') . '" value="1" ' . ( $isFooter == 1 ? 'checked="checked"' : '' ) . '/> 표시함 <input type="radio" id="' . $this->get_field_id('isFooter') . '-1" name="' . $this->get_field_name('isFooter') . '" value="0" ' . ($isFooter == 0 ? 'checked="checked"' : '') . '/> 표시안함</p>';
		}
	}
	add_action( 'widgets_init', create_function( '', 'return register_widget("QB_Myview_Widget");' ) );
}
?>