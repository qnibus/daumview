<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>DaumView Settings</h2>
	<h3>General Settings</h3>
	<hr />
	<form action="options-general.php?page=<?php echo $this->plugin_file; ?>" method="post">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
<?php if ( ! $this->is_join_daumview() ) { ?>
	<input type="hidden" name="daumview_blogurl" value="<?php echo $this->options['daumview_blogurl']; ?>" />
	<input type="hidden" name="daumview_position_top" value="<?php echo $this->options['daumview_position_top']; ?>" />
	<input type="hidden" name="daumview_position_bottom" value="<?php echo $this->options['daumview_position_bottom']; ?>" />
	<input type="hidden" name="daumview_recombox_type" value="<?php echo $this->options['daumview_recombox_type']; ?>" />
	<input type="hidden" name="daumview_widget_myview_enable" value="<?php echo $this->options['daumview_widget_myview_enable']; ?>" />
	<input type="hidden" name="daumview_widget_subscribe_enable" value="<?php echo $this->options['daumview_widget_subscribe_enable']; ?>" />
	<input type="hidden" name="daumview_widget_ranking_enable" value="<?php echo $this->options['daumview_widget_ranking_enable']; ?>" />
	<input type="hidden" name="daumview_widget_live_enable" value="<?php echo $this->options['daumview_widget_live_enable']; ?>" />
<?php } ?>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="blogname">블로그 주소</label></th>
			<td>
				<fieldset><legend class="hidden">블로그 주소</legend>
				<label for="daumview_blogurl">
					<input name="daumview_blogurl" type="text" id="daumview_blogurl" value="<?php echo $this->options['daumview_blogurl']; ?>" class="regular-text">
					<?php if ( $this->is_join_daumview() ) { ?>
					<a href="http://v.daum.net/my/<?php echo $this->daum_id; ?>" target="_blank" class="button button-big">MY view 보기</a>
					<?php } ?>
				</label>
				</fieldset>
				<p class="description">플러그인 활성화시 사이트의 기본 주소를 자동으로 입력합니다.<br />만약, 하단의 옵션 메뉴가 보이지 않는 경우 블로그 주소가 다음뷰에 기입한 블로그 주소와 일치하는지 다시 한번 확인해주세요.<br />다음뷰에 가입하지 않으셨으면 <a href="http://v.daum.net/user/join" target="_blank">DaumView 가입</a>을 해주세요.</p>
			</td>
		</tr>
<?php if ( $this->is_join_daumview() ) { ?>
		<tr valign="top">
			<th scope="row">컨텐츠내 박스의 위치</th>
			<td>
				<fieldset><legend class="hidden">컨텐츠내 박스의 위치</legend>
				<label title="daumview_position_top">
					<input type="checkbox" name="daumview_position_top" value="Y" <?php if ( $this->options['daumview_position_top'] == 'Y') { ?> checked="checked"<?php } ?>> 상단 
				</label><br />
				<label title="daumview_position_bottom">
					<input type="checkbox" name="daumview_position_bottom" value="Y" <?php if ( $this->options['daumview_position_bottom'] == 'Y') { ?> checked="checked"<?php } ?>> 하단
				</fieldset>
				<p class="description">추천박스의 위치는 체크박스의 선택에 따라 컨텐츠 상단과 하단에 배치하실 수 있습니다.</p>
			</td>
		</tr>
		<tr>
			<th scope="row">추천 박스 선택</th>
			<td>
				<fieldset><legend class="hidden">추천 박스 선택</legend>
				<label title="daumview-recombox-type-box">
					<input name="daumview_recombox_type" value="box" <?php if ( $this->options['daumview_recombox_type'] == 'box' ) { ?>checked="checked"<?php } ?>type="radio" /> 박스형<br />
					<img src="<?php echo $this->plugin_url; ?>/images/daumview_box.png" />
				</label><br />
				<label title="daumview-recombox-type-smallbox">
					<input name="daumview_recombox_type" value="smallbox" <?php if ( $this->options['daumview_recombox_type'] == 'smallbox' ) { ?>checked="checked"<?php } ?>type="radio" /> 작은 박스형<br />
					<img src="<?php echo $this->plugin_url; ?>/images/daumview_thinbox.png" />
				</label><br />
				<label title="daumview-recombox-type-button">
					<input name="daumview_recombox_type" value="button" <?php if ( $this->options['daumview_recombox_type'] == 'button' ) { ?>checked="checked"<?php } ?>type="radio" /> 버튼형<br />
					<img src="<?php echo $this->plugin_url; ?>/images/daumview_button.png" />
				</label><br />
				<label title="daumview-recombox-type-smallbutton">
					<input name="daumview_recombox_type" value="smallbutton" <?php if ( $this->options['daumview_recombox_type'] == 'smallbutton' ) { ?>checked="checked"<?php } ?>type="radio" /> 작은버튼형<br />
					<img src="<?php echo $this->plugin_url; ?>/images/daumview_smallbutton.png" />
				</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">위젯</th></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">위젯</legend>
					<label title="daumview_widget_myview_enable">
						<input type="checkbox" name="daumview_widget_myview_enable" value="Y" <?php if ( $this->options['daumview_widget_myview_enable'] == 'Y' ) { ?> checked="checked"<?php } ?>> MY글 위젯 (100% x 273)
						<!-- <iframe src='http://api.v.daum.net/iframe/my_widget?skin=1&page_size=7&init_type=recommend&is_footer=1&daumid=qnibus' width='100%' height='273' frameborder='0' scrolling='no' allowtransparency='true'></iframe> -->
					</label><br />
					<label title="daumview_widget_subscribe_enable">
						<input type="checkbox" name="daumview_widget_subscribe_enable" value="Y" <?php if ( $this->options['daumview_widget_subscribe_enable'] == 'Y' ) { ?> checked="checked"<?php } ?>> 구독위젯 (132 x 152)
						<!-- <script src="http://widgetprovider.daum.net/view?url=http://widgetcfs1.daum.net/xml/30/widget/2010/03/22/10/20/4ba6c5c0be6c3.xml&up_bgcolor1=0&up_DAUM_WIDGETBANK_LOGINID=qnibus&up_bgcolor2=1&&width=132&height=152&widgetId=739&scrap=1" type="text/javascript"></script> -->
					</label><br />
					<label title="daumview_widget_ranking_enable">
						<input type="checkbox" name="daumview_widget_ranking_enable" value="Y" <?php if ( $this->options['daumview_widget_ranking_enable'] == 'Y' ) { ?> checked="checked"<?php } ?>> 랭킹위젯 (166 x 155)
						<!-- <script src="http://widgetprovider.daum.net/view?url=http://widgetcfs1.daum.net/xml/15/widget/2009/07/22/17/20/4a66cbbacfa56.xml&up_DAUM_WIDGETBANK_BLOG_URL=http%3A%2F%2Fqnibus.com&up_init_pan=1&&width=166&height=155&widgetId=434&scrap=1" type="text/javascript"></script> -->
					</label><br />
					<label title="daumview_widget_live_enable">
						<input type="checkbox" name="daumview_widget_live_enable" value="Y" <?php if ( $this->options['daumview_widget_live_enable'] == 'Y' ) { ?> checked="checked"<?php } ?>> 추천LIVE (166 x 191)
						<!-- <script src="http://widgetprovider.daum.net/view?url=http://widgetcfs1.daum.net/xml/2/widget/2009/05/29/10/49/4a1f3f2cd61a0.xml&width=166&height=191&widgetId=395&scrap=1" type="text/javascript"></script> -->
					</label><br />
				</fieldset>
			</td>
		</tr>
		<tr>
			<th scope="row">숏코드 사용법</th>
			<td>
				<code>[daumview type="box"][/daumview]</code> 박스형<br />
				<code>[daumview type="smallbox"][/daumview]</code> 작은 박스형<br />
				<code>[daumview type="button"][/daumview]</code> 버튼형<br />
				<code>[daumview type="smallbutton"][/daumview]</code> 작은 버튼형<br />
				<p class="description">컨텐츠 작성시 원하는 위치의 위의 코드를 넣어주시면 원하는 위치에 박스가 추가됩니다.</p>
			</td>
		</tr>
<?php } ?>
	</tbody>
	</table>
	<p class="submit"><input name="Submit" class="button-primary" value="Save Changes" type="submit"></p>
	</form>
	
	<h3>Donation</h3>
	<hr />
	<p>당신의 작은 관심이 보다 유용한 플러그인과 테마를 제작하는데 큰힘이 됩니다.<br />감사합니다. ^^</p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
	">
	<input type="submit" class="button-secondary" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" value="PayPal" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<a class="button" href="https://itunes.apple.com/app/qnote-all-in-one/id556882495?mt=8" target="_blank">Qnote All-in-One</a>
	<a class="button" href="https://itunes.apple.com/app/pictok/id555935069?mt=8" target="_blank">PicTok</a>
	<a class="button" href="https://itunes.apple.com/app/fotocookie/id474472114?mt=8" target="_blank">FotoCookie</a>
	</form>
	
</div>
