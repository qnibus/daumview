<?php
/*
Plugin Name: DaumView
Plugin URI: http://qnibus.com/blog/daumview-plugin/
Description: DaumView 플러그인은 다음뷰에서 제공하는 서비스를 워드프레스에서도 편리하게 사용할 수 있게 하기 위한 도구입니다. (추천박스, MY글 위젯, 추천LIVE 위젯, 랭킹 위젯, 구독 위젯 제공)
Version: 1.4
Author: qnibus
Author URI: http://qnibus.com
Author Email: andy@qnibus.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! class_exists( 'QB_Daumview' ) ) {
	class QB_Daumview {
		public $name = 'qnibus_daumview';
		public $version = '1.3';
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
			add_action( "admin_menu", array( &$this, 'register_submenu_page' ) );
			add_action( "admin_menu", array( &$this, 'register_meta_box' ) );
			add_action( "save_post", array( &$this, 'save_meta_box' ) );
			add_filter( "the_content", array( &$this, 'attach_into_content' ) );
			
			if ( $this->options['daumview_widget_myview_enable'] == 'Y' )
				require_once( 'widgets/daumview_myview_widget.php' );
				
			if ( $this->options['daumview_widget_subscribe_enable'] == 'Y' )
				require_once( 'widgets/daumview_subscribe_widget.php' );
				
			if ( $this->options['daumview_widget_ranking_enable'] == 'Y' )
				require_once( 'widgets/daumview_ranking_widget.php' );
				
			if ( $this->options['daumview_widget_live_enable'] == 'Y' )
				require_once( 'widgets/daumview_live_widget.php' );
				
			register_activation_hook( __FILE__, array(&$this, 'register_activate_daumview' ));
		}


		/**************************************************************************
         * 플러그인 활성화
         *
         * @version 1.3
         * @return NULL
         **************************************************************************/
		function register_activate_daumview() {
			if ( version_compare( PHP_VERSION, '5.0.1', '<' ) ) { 
                deactivate_plugins( basename( __FILE__ ) ); // Deactivate ourself 
                wp_die( "Sorry, but you can't run this plugin, it requires PHP 5.0.1 or higher." );
        	}
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
			require_once dirname( __FILE__ ) . "/daumview_admin_settings.php";
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
			if ( ! $this->is_join_daumview( $this->get_shortlink_post_type() ) ) {
				echo '<div class="daumview-error">존재하지 않는 블로그입니다. (' . $this->get_shortlink_post_type() . ') <a href="./options-general.php?page=daumview.php">설정하기</a></div>';
			} else if ( $this->is_post_daumview() ) {
				$xml = $this->get_xml_url( "http://api.v.daum.net/open/news_info.xml?permalink=" . urlencode( $this->get_shortlink_post_type() ) );
				if ( is_object( $xml ) ) {
					$newsinfo = $xml->entity->news;
					require_once dirname( __FILE__ ) . '/daumview_meta_info_box.php';
				}
			} else {
				global $post;
				$xml = simplexml_load_string( file_get_contents( dirname( __FILE__ ) . '/category.xml' ), null, LIBXML_NOCDATA );
				$category = $xml->entity->category;
				$values = get_post_custom( $post->ID );
				wp_nonce_field( 'daumview_meta_box_nonce', 'meta_box_nonce' );
				require_once dirname( __FILE__ ) . "/daumview_meta_select_box.php";
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
				$fields = array(
		            'url' => rawurlencode( $this->get_shortlink_post_type() ),
		            'title' => rawurlencode( stripslashes( strip_tags( $_POST['post_title'] ) ) ),
		            'blog_name' => rawurlencode( stripslashes( strip_tags( get_bloginfo('name' ) ) ) ),
		            'excerpt' => rawurlencode( stripslashes( strip_tags( $_POST['content'] ) ) ),
		            'img_url' => rawurlencode( '' ),
		        );
				$xml = $this->post_xml_url( $_POST["daumview_category_url"], $fields );
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
						
			$xml = $this->get_xml_url( "http://api.v.daum.net/open/news_info.xml?permalink=" . urlencode( $this->get_shortlink_post_type() ) );
			if ( is_object( $xml ) ) {
				$query_url = 'nid=' . (string)$xml->entity->news->id;
			} else {
				$query_url = 'nurl=' . urlencode( $this->get_shortlink_post_type() );
			}
			
			$daumview_box = 
				array(
					'box' => '<embed src="http://api.v.daum.net/static/recombox1.swf?' . $query_url . '" quality="high" bgcolor="#ffffff" width="400" height="80" type="application/x-shockwave-flash"></embed>',
					'thinbox' => '<embed src="http://api.v.daum.net/static/recombox2.swf?' . $query_url . '" quality="high" bgcolor="#ffffff" width="400" height="58" type="application/x-shockwave-flash"></embed>',
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
         * @version 1.4
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
         * @return Boolean
         **************************************************************************/
		private function is_post_daumview() {			
			if ( $this->daumview_nid != 0 )
				return true;

			$xml = $this->get_xml_url( "http://api.v.daum.net/open/news_info.xml?permalink=" . urlencode( $this->get_shortlink_post_type() ) );
			if ( is_object( $xml ) ) {
				if ( $xml->head->code == "200" ) {
					$this->daumview_nid = $xml->entity->news->id;
					return true;
				}
			}
				
			return false;
		}
		
		
		/**************************************************************************
         * 다음뷰 가입여부 검사
         *
         * @return Boolean
         **************************************************************************/
		private function is_join_daumview( $url = NULL ) {
			$xml = $this->get_xml_url( "http://api.view.daum.net/open/user_info.xml?blogurl=" . ( $url ? $url : urlencode( $this->options['daumview_blogurl'] ) ) );
			if ( is_object( $xml ) ) {
				$this->daum_id = ltrim( strstr( (string)$xml->entity->pluslink, '=' ), '=' );
				if ( $xml->head->code == "200" ) {
					return true;
				}
				return false;
			}
		}

		
		/**************************************************************************
         * GET 전송후 XML 반환
         *
         * @param sting $url Parameter to xml url
         * @return object XML
         **************************************************************************/
		private function get_xml_url( $url ) {
			if ( function_exists( 'curl_init' ) ) {
			    $ch = curl_init();
			    curl_setopt( $ch, CURLOPT_URL, $url );
			    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			    curl_setopt( $ch, CURLOPT_REFERER, get_bloginfo('url') );
			    $result = curl_exec( $ch );
			    curl_close( $ch );
			    return simplexml_load_string( $result, null, LIBXML_NOCDATA );
			} else {
				$fsockurl = parse_url( $url );				
				if ( ( $fsock = fsockopen( $fsockurl['host'], 80, $errno, $errstr, 5 ) ) ) {
					$query  = "GET " . $fsockurl['path'] . ( $fsockurl['query'] ? '?'.$fsockurl['query'] : '' ) . " HTTP/1.0\r\n";
					$query .= "Host: " . $fsockurl['host'] . "\r\n";
					$query .= "Connection: close\r\n\r\n";
					
					$header = '';
					$body   = '';
					
					fputs( $fsock, $query );
					do {
						$header .= fgets ( $fsock, 128 );
					} while ( strpos ( $header, "\r\n\r\n" ) === false );

					while ( ! feof ( $fsock ) ) {
						$body .= fgets ( $fsock, 128 );
					}
					return simplexml_load_string( $body, null, LIBXML_NOCDATA );
				}
			}
		}
		  		
  		
  		/**************************************************************************
         * POST 전송후 XML 반환
         *
         * @param sting $url Parameter to xml url
         * @param sting $fields Parameter to postbody
         * @return object XML
         **************************************************************************/
  		private function post_xml_url( $url, $fields ) {
			foreach ( $fields as $key => $value ) {
				$fields_string .= $key . '=' . $value . '&';
			}
			rtrim( $fields_string, '&' );
			
			if ( function_exists( 'curl_init' ) ) {
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $url );
				curl_setopt( $ch, CURLOPT_POST, count($fields) );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				$result = curl_exec( $ch );
				curl_close( $ch );
				return simplexml_load_string( $result, null, LIBXML_NOCDATA );
			} else {
				$fsockurl = parse_url( $url );	
				if ( ($fsock = fsockopen( $fsockurl['host'], 80, $errno, $errstr, 5 ) ) ) {
					$query  = "POST " . $fsockurl['path'] . " HTTP/1.1\r\n";
					$query .= "Host: " . $fsockurl['host'] . "\r\n";
					$query .= "User-Agent: " . $this->name . "\r\n";
					$query .= "Content-Type: application/x-www-form-urlencoded;charset=UTF-8\r\n";
					$query .= "Content-Length: " . strlen($fields_string) . "\r\n";
					$query .= "Connection: close\r\n\r\n";
					$query .= $fields_string;
					
					$header = '';
					$body   = '';
					
					fputs( $fsock, $query );
					do {
						$header .= fgets ( $fsock, 128 );
					} while ( strpos ( $header, "\r\n\r\n" ) === false );
			
					while ( ! feof ( $fsock ) ) {
						$body .= fgets ( $fsock, 128 );
					}
					return simplexml_load_string( $body, null, LIBXML_NOCDATA );
				}
			}
		}
	} //End Class
} //End if class exists statement
 
if ( class_exists( 'QB_Daumview' ) ) {
    $qnibusdaumview = new QB_Daumview();
}
?>