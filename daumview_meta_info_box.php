<div class="submitbox" id="submitdaumview">
	<div id="minor-publishing">
		<div id="misc-publishing-actions">
			<div class="misc-pub-section">
				송고상태: <span id="post-status-display">Published</span>
				<a href="http://v.daum.net/link/<?php echo $newsinfo->id; ?>" target="_blank" class="hide-if-no-js">View</a>
			</div><!-- .misc-pub-section -->
		
			<div class="misc-pub-section">
				송고일시: <span id="post-status-display"><?php echo $newsinfo->reg_date; ?></span>
			</div><!-- .misc-pub-section -->
			
			<div class="misc-pub-section" id="visibility">
				채널: <span id="post-visibility-display"><?php echo strtoupper($newsinfo->category_eng_name); ?></span>
				<a href="http://v.daum.net/ch/<?php echo $newsinfo->category_eng_name; ?>" target="_blank" class="hide-if-no-js">View</a>
			</div><!-- .misc-pub-section -->
			
			<div class="misc-pub-section" id="visibility">
				조회수: <span id="post-visibility-display"><?php echo strtoupper($newsinfo->read_count); ?>회</span>
			</div><!-- .misc-pub-section -->
			
			<div class="misc-pub-section" id="visibility">
				추천수: <span id="post-visibility-display"><?php echo strtoupper($newsinfo->recommend_count); ?>회</span>
			</div><!-- .misc-pub-section -->
		</div>
	</div>
	
	<div id="major-publishing-actions">
		<a href="http://v.daum.net/my/<?php echo $this->daum_id; ?>" target="_blank" class="button button-large">MY view 보기</a>
		<div class="clear"></div>
	</div>
</div><!-- .submitbox -->