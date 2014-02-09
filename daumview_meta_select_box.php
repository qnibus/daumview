<p>
	<label for="daumview-select-channel">채널선택</label>
	<select id="daumview-select-channel" name="daumview_category_url" style="width: 100%;">
		<option value="">다음뷰에 송고하지 않음</option>
	<?php foreach($category as $row) : ?>
		<optgroup style="font-style: normal; background: #aeaeae;" label="<?php echo $row->name?>">
	<?php foreach($row->list->category as $list) : ?>
	<option <?php echo $daumview_category_url == $list->trackback_url ? 'selected="selected"': ''; ?>style="padding-left: 20px; background: #fff;" value="<?php echo $list->trackback_url?>"><?php echo $list->name?></option>
	<?php endforeach; ?>
		</optgroup>
	<?php endforeach; ?>
	</select>
</p>
<p>
	<label for="daumview-post-title">포스트 제목</label>
	<input id="daumview-post-title" name="daumview_post_title" value="<?php echo $values->post_title; ?>" style="width: 98%" />
	<br /><small>포스트 제목을 변경하시면 다음뷰에 변경된 내용으로 반영됩니다.</small>
</p>
<!--
<p>
	<label for="daumview-post-content">포스트 요약정보</label>
	<textarea id="daumview-post-content" name="daumview_post_content" class="code" rows="3" style="width: 99%"><?php echo esc_textarea( strip_tags($values->post_content) ); ?></textarea>
</p>
-->


