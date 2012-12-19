<?php
/*
Plugin Name: DaumView
Plugin URI: http://qnibus.com/blog/daumview-plugin/
Description: DaumView 플러그인은 다음뷰에서 제공하는 서비스를 워드프레스에서도 편리하게 사용할 수 있게 하기 위한 도구입니다. (추천박스, MY글 위젯, 추천LIVE 위젯, 랭킹 위젯, 구독 위젯 제공)
Version: 1.5
Author: qnibus
Author URI: http://qnibus.com
Author Email: andy@qnibus.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! class_exists( 'QB_Daumview' ) ) {
	class QB_Daumview {
		public $name = 'qnibus_daumview';
		public $version = '1.5';
		public $plugin_url;
		public $plugin_file;
		public $options = array();
		public $daumview_nid = 0;
		public $daum_id;
		
		/**************************************************************************
         * QB_Daumview 객체 생성
		 *
         * @return Object
         **************************************************************************/
		function __construct() {
			$this->initialize();
		}
		
		function initialize() {			
			$this->plugin_url = substr( dirname( __FILE__ ), strlen($_SERVER['DOCUMENT_ROOT']) );
			$this->plugin_file = basename(__FILE__);
			$this->options = wp_parse_args( get_option( $this->name ), array(
					'daumview_blogurl' => get_bloginfo( 'url' ),
					'daumview_position_top' => '',
					'daumview_position_bottom' => 'Y',
					'daumview_recombox_type' => 'box',
					'daumview_widget_myview_enable' => 'Y',
					'daumview_widget_subscribe_enable' => '',
					'daumview_widget_ranking_enable' => '',
					'daumview_widget_live_enable' => ''
				)
			);
			
			add_option( $this->name, $this->options );
			add_action( 'admin_menu', array( &$this, 'register_submenu_page' ) );
			add_action( 'admin_menu', array( &$this, 'register_meta_box' ) );
			add_action( 'save_post', array( &$this, 'save_meta_box' ) );
			add_filter( 'the_content', array( &$this, 'attach_into_content' ) );
			add_shortcode( 'daumview', array( &$this, 'recommend_box' ) );
			
			if ( $this->options['daumview_widget_myview_enable'] == 'Y' )
				require_once( 'widgets/daumview_myview_widget.php' );
				
			if ( $this->options['daumview_widget_subscribe_enable'] == 'Y' )
				require_once( 'widgets/daumview_subscribe_widget.php' );
				
			if ( $this->options['daumview_widget_ranking_enable'] == 'Y' )
				require_once( 'widgets/daumview_ranking_widget.php' );
				
			if ( $this->options['daumview_widget_live_enable'] == 'Y' )
				require_once( 'widgets/daumview_live_widget.php' );
				
			register_activation_hook( __FILE__, array( &$this, 'register_activate_daumview' ) );
			register_uninstall_hook( __FILE__, array( &$this, 'register_uninstall_daumview' ) );
		}


		/**************************************************************************
         * 플러그인 활성화
         *
         * @since version 1.3
         * @return NULL
         **************************************************************************/
		function register_activate_daumview() {
			if ( version_compare( PHP_VERSION, '5.0.1', '<' ) ) { 
                deactivate_plugins( basename( __FILE__ ) ); // Deactivate ourself 
                wp_die( "Sorry, but you can't run this plugin, it requires PHP 5.0.1 or higher." );
        	}
		}
		
		
		/**************************************************************************
         * 플러그인 삭제
         *
         * @since version 1.5
         * @return NULL
         **************************************************************************/
		function register_uninstall_daumview() {
			delete_option( $this->name );
		}
		
		
		/**************************************************************************
         * 숏코드
         * [daumview type=<box|smallbox|button|smallbutton>][/daumview] 사용
         *
         * @since version 1.5
         * @return string
         **************************************************************************/
		function recommend_box( $atts, $content = null ) {
			extract( shortcode_atts( array(
						'type' => 'box',
					), $atts ) );
			$content = $this->content_helper($content);
			
			if ( $xml = $this->is_post_daumview() ) {
				$daumview_box = array(
					'box' => '<embed src="http://api.v.daum.net/static/recombox1.swf?nid=' . $this->daumview_nid . '" quality="high" bgcolor="#ffffff" width="400" height="80" type="application/x-shockwave-flash"></embed>',
					'smallbox' => '<embed src="http://api.v.daum.net/static/recombox2.swf?nid=' . $this->daumview_nid . '" quality="high" bgcolor="#ffffff" width="400" height="58" type="application/x-shockwave-flash"></embed>',
					'button' => '<embed src="http://api.v.daum.net/static/recombox3.swf?nid=' . $this->daumview_nid . '" quality="high" bgcolor="#ffffff" width="67" height="80" type="application/x-shockwave-flash"></embed>',
					'smallbutton' => '<embed src="http://api.v.daum.net/static/recombox4.swf?nid=' . $this->daumview_nid . '" quality="high" bgcolor="#ffffff" width="82" height="21" type="application/x-shockwave-flash"></embed>',
				);
				$output = '<table width="100%"><tr><td align="center">' . $daumview_box[$type] . '</td></tr></table>';
					
				return $output;
			}
		}


		function delete_htmltags($content,$paragraph_tag=false,$br_tag=false){
			$content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );
			$content = preg_replace( '#<br \/>#', '', $content );
			if ( $paragraph_tag ) $content = preg_replace( '#<p>|</p>#', '', $content );
			return trim($content);
		}
	
	
		function content_helper( $content, $paragraph_tag=false, $br_tag=false ){
			return $this->delete_htmltags( do_shortcode( shortcode_unautop( $content ) ), $paragraph_tag, $br_tag );
		}


		/**************************************************************************
         * Settings > DaumView로 커스텀 메뉴 등록
         * 관리자 > 환경설정 > 다음뷰 위치에 해당 메뉴 추가
         *
         * @return NULL
         **************************************************************************/
		function register_submenu_page() {
			if ( ! current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}
			add_submenu_page( 'options-general.php', 'DaumView', 'DaumView', 'manage_options', basename(__FILE__), array( &$this, 'display_submenu_page' ) );
		}
		
		
		/**************************************************************************
         * Settings > DaumView로 관리용 서브메뉴 출력
         * daumview_admin_settings.php 파일 출력
         * 
         * @option daumview_blogurl
         * @option daumview_position_top
         * @option daumview_position_bottom
         * @option daumview_recombox_type
         * @option daumview_widget_myview_enable
         * @option daumview_widget_subscribe_enable
         * @option daumview_widget_ranking_enable
         * @option daumview_widget_live_enable
         * @return NULL
         **************************************************************************/
		function display_submenu_page() {
			$hidden_field_name = 'daumview_submit_hidden';
			if ( isset( $_POST[$hidden_field_name] ) && $_POST[$hidden_field_name] == 'Y' ) {
				$this->options['daumview_blogurl'] = esc_url( $_POST['daumview_blogurl'] );
				$this->options['daumview_position_top'] = $_POST['daumview_position_top'];
				$this->options['daumview_position_bottom'] = $_POST['daumview_position_bottom'];
				$this->options['daumview_recombox_type'] = $_POST['daumview_recombox_type'];
				$this->options['daumview_widget_myview_enable'] = $_POST['daumview_widget_myview_enable'];
				$this->options['daumview_widget_subscribe_enable'] = $_POST['daumview_widget_subscribe_enable'];
				$this->options['daumview_widget_ranking_enable'] = $_POST['daumview_widget_ranking_enable'];
				$this->options['daumview_widget_live_enable'] = $_POST['daumview_widget_live_enable'];
		
		        update_option( $this->name, $this->options );
?>
				<div class="updated"><p><strong><?php _e( 'settings saved.', 'daumview_notice_save' ); ?></strong></p></div>
<?php
			}
			require_once dirname( __FILE__ ) . '/daumview_admin_settings.php';
		}
		
		
		/**************************************************************************
         * 메타박스 등록 (All posttype)
         *
         * @return NULL
         **************************************************************************/
		function register_meta_box() {
			$post_types = get_post_types( '', 'names' );
			foreach ( $post_types as $post_type ) {
				add_meta_box( 'daumviewdiv', __( 'Send DaumView', $this->name.'_textdomain' ), array( &$this, 'display_meta_box' ), $post_type, 'side' );
			}
		}
		
		
		/**************************************************************************
         * daumview_meta_info_box.php or daumview_meta_select_box.php 출력
         *
         * @return NULL
         **************************************************************************/
		function display_meta_box() {
			global $post;
			$values = get_post( $post->ID );
			if ( $values->post_status != 'publish' ) {
				echo '<div class="daumview-error">공개로 설정되지 않은 글은 송고하실 수 없습니다.</div>';
			} else if ( ! $this->is_join_daumview( $this->get_shortlink_post_type() ) ) {
				echo '<div class="daumview-error">존재하지 않는 블로그입니다. (' . $this->get_shortlink_post_type() . ') <a href="./options-general.php?page=daumview.php">설정하기</a></div>';
				return;
			} else if ( $xml = $this->is_post_daumview() ) {
				$newsinfo = $xml->entity->news;
				require_once dirname( __FILE__ ) . '/daumview_meta_info_box.php';
			} else {
				$xml = simplexml_load_string( file_get_contents( dirname( __FILE__ ) . '/category.xml' ), null, LIBXML_NOCDATA );
				if ( is_object( $xml ) ) {
					$category = $xml->entity->category;
					wp_nonce_field( 'daumview_meta_box_nonce', 'meta_box_nonce' );
					require_once dirname( __FILE__ ) . '/daumview_meta_select_box.php';
				}				
			}
		}
		
		
		/**************************************************************************
         * 포스트 등록시 트랙백 전송
         * 
         * @param sting $post_id 편집 페이지의 포스트 아이디 값
         * @return NULL
         **************************************************************************/
		function save_meta_box( $post_id ) {
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
		    if( ! isset( $_POST['meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['meta_box_nonce'], 'daumview_meta_box_nonce' ) ) return; 
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_pages', $post_id ) ) return;
			} else {
				if ( ! current_user_can( 'edit_posts', $post_id ) ) return;
			}
			
			if ( ! $this->is_post_daumview() ) {
				$body = array(
		            'url' => $this->get_shortlink_post_type(),
		            'title' => ( isset( $_POST['daumview_post_title'] ) && strlen( trim( $_POST['daumview_post_title'] ) ) > 0 ) ? $_POST['daumview_post_title'] : $_POST['post_title'],
		            'blog_name' => get_bloginfo('name' ),
		            'excerpt' => $_POST['post_content'],
		            'img_url' => '',
		        );
				$xml = $this->get_xml_request( $_POST['daumview_category_url'], 'POST', $body );
			}
		}
		

		/**************************************************************************
         * Contents Hook
         *
         * @param sting $content Parameter to current post content
         * @return string CONTENT with DaumView
         **************************************************************************/
		function attach_into_content( $content ) {
			if ( ! $this->is_post_daumview() )
				return $content;
			
			if ( $xml = $this->is_post_daumview() ) {
				$query_url = 'nid=' . (string)$xml->entity->news->id;
			} else {
				$query_url = 'nurl=' . urlencode( $this->get_shortlink_post_type() );
			}
			
			$daumview_box = array(
				'box' => '<embed src="http://api.v.daum.net/static/recombox1.swf?' . $query_url . '" quality="high" bgcolor="#ffffff" width="400" height="80" type="application/x-shockwave-flash"></embed>',
				'smallbox' => '<embed src="http://api.v.daum.net/static/recombox2.swf?' . $query_url . '" quality="high" bgcolor="#ffffff" width="400" height="58" type="application/x-shockwave-flash"></embed>',
				'button' => '<embed src="http://api.v.daum.net/static/recombox3.swf?' . $query_url . '" quality="high" bgcolor="#ffffff" width="67" height="80" type="application/x-shockwave-flash"></embed>',
				'smallbutton' => '<embed src="http://api.v.daum.net/static/recombox4.swf?' . $query_url . '" quality="high" bgcolor="#ffffff" width="82" height="21" type="application/x-shockwave-flash"></embed>',
			);
			$daumview_content = '<table width="100%"><tr><td align="center">' . $daumview_box[empty($this->options['daumview_recombox_type']) ? 1 : $this->options['daumview_recombox_type']] . '</td></tr></table>';
			
			if ( $this->options['daumview_position_top'] == 'Y' ) {
				$content = $daumview_content . $content;
			}
			if ( $this->options['daumview_position_bottom'] == 'Y' ) {
				$content .= $daumview_content;
			}

			return $content;
		}
		
		
		/**************************************************************************
         * 포스트 Shortlink 생성
         * 
         * @since version 1.4
         * @return NULL
         **************************************************************************/
		function get_shortlink_post_type() {
			global $post;	
			if ( $post->post_type == 'post') {
				return home_url( '?p=' . $post->ID );
			} else if ( $post->post_type == 'page' ) {
				return home_url( '?page_id=' . $post->ID );
			} else if ( in_array( $post->post_type, get_post_types( array( '_builtin' => false ) ) ) ) {
				return home_url( add_query_arg( array( 'post_type' => $post->post_type, 'p' => $post->ID ), '' ) );
			} else {
				return $this->options['daumview_blogurl'];
			}
		}

		
		/**************************************************************************
         * 다음뷰 송고여부 검사
         *
         * @return Object
         **************************************************************************/
		private function is_post_daumview() {
			$xml = $this->get_xml_request( 'http://api.v.daum.net/open/news_info.xml?permalink=' . $this->get_shortlink_post_type() );
			if ( is_object( $xml ) ) {
				if ( $xml->head->code == '200' ) {
					$this->daumview_nid = $xml->entity->news->id;
					return $xml;
				}
			}
			return NULL;
		}
		
		
		/**************************************************************************
         * 다음뷰 가입여부 검사
         *
         * @return Object
         **************************************************************************/
		private function is_join_daumview( $url = NULL ) {
			$xml = $this->get_xml_request( 'http://api.v.daum.net/open/user_info.xml?blogurl=' . ( $url ? $url : urlencode( $this->options['daumview_blogurl'] ) ) );
			if ( is_object( $xml ) ) {
				if ( $xml->head->code == '200' ) {
					$this->daum_id = ltrim( strstr( (string)$xml->entity->pluslink, '=' ), '=' );
					return $xml;
				}
			}
			return NULL;
		}


		/**************************************************************************
         * HTTP 전송후 XML 반환
         *
         * @since version 1.5
         * @param sting $url Parameter to xml url
         * @return object XML
         **************************************************************************/
		private function get_xml_request( $url, $method = 'GET', $body = NULL ) {
			$args = array(
				'method' => strtoupper( $method ),
				'httpversion' => '1.1',
				'user-agent' => $this->name,
				'body' => $body,
			);
			
			if ( strtoupper($method) == 'GET' )
				$response = wp_remote_get($url, $args);
			else if ( strtoupper($method) == 'POST' ) {
				$response = wp_remote_post($url, $args);
			}
			
			if ( is_array( $response ) && $response['response']['code'] == '200' ) {
				return simplexml_load_string( $response['body'], null, LIBXML_NOCDATA );
			}
		}		
	} //End Class
} //End if class exists statement
 
if ( class_exists( 'QB_Daumview' ) ) {
    $qnibusdaumview = new QB_Daumview();
}
?>