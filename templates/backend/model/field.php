<style type="text/css">
	#libox-templates .form-horizontal .control-label{
		width: 80px;
	}
	#libox-templates .form-horizontal .controls{
		margin-left: 100px;
	}
</style>

<div id="model-struct-list">
	<ul id="tab" class="nav nav-tabs">
		<li class=""><a href="#basic" data-toggle="tab">基&nbsp;本</a></li>
		<li class="active"><a href="#extend" data-toggle="tab">扩&nbsp;展</a></li>
	</ul>
	<div id="myTabContent" class="tab-content">
		<div class="tab-pane fade" id="basic">
			<form class="form-horizontal" onsubmit="return false;">
				<fieldset>
					<?php
					foreach ($model_fields_basic as $mfb) {
						if ($mfb['field'] != 'content_id') {
							?>
							<div class="control-group" id="field_<?php echo $mfb['field']; ?>_original" rel="">
								<label class="control-label ellipsis" rel="tooltip" data-original-title="<?php echo $mfb['field']; ?>" for="field_<?php echo $mfb['field']; ?>[alias]"><strong><?php echo $mfb['field']; ?></strong></label>
								<div class="controls">
									<input type="text" rel="tooltip" data-original-title="别名" class="input-xlarge span2" id="field_<?php echo $mfb['field']; ?>[alias]" placeholder="别名" value="<?php echo $mfb['alias']; ?>" />
									<input type="text" rel="tooltip" data-original-title="序号" class="input-xlarge span1" id="field_<?php echo $mfb['field']; ?>[sort]" placeholder="0" value="<?php echo $mfb['sort']; ?>" style="text-align:center;width:40px;" />
									<select rel="tooltip" data-original-title="数据类型" class="span2" id="field_<?php echo $mfb['field']; ?>[data_type]" onchange="if($.inArray($(this).val(), ['CHAR', 'VARCHAR', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL', 'BIT']) != -1)$(this).next().removeAttr('disabled').focus();else $(this).next().attr('disabled', true)">
										<?php foreach ($data_types as $dt) { ?>
											<option value="<?php echo $dt; ?>"<?php if ($mfb['data_type'] == $dt) echo ' selected'; ?>><?php echo $dt; ?></option>
										<?php } ?>
									</select>
									<input type="text" rel="tooltip" data-original-title="数据长度" class="input-xlarge span1" id="field_<?php echo $mfb['field']; ?>[length]" placeholder="长度" value="<?php echo $mfb['length']; ?>" style="text-align:center;width:40px" />

									<select rel="tooltip" data-original-title="控件类型" class="span1" id="field_<?php echo $mfb['field']; ?>[type]">
										<?php foreach ($types as $dt) { ?>
											<option value="<?php echo $dt; ?>"<?php if ($mfb['type'] == $dt) echo ' selected'; ?>><?php echo $dt; ?></option>
										<?php } ?>
									</select>
									<code id="field_<?php echo $mfb['field']; ?>[extend_value]" rel="tooltip" data-original-title="选项,仅作用于 radio,checkbox,select,multiselect" class="span1 ellipsis" style="float:none;margin-left:0;vertical-align:middle"><?php echo $mfb['extend_value']; ?></code>
									<input type="text" rel="tooltip" data-original-title="默认值,多个值请以半角逗号分隔" class="span2" id="field_<?php echo $mfb['field']; ?>[default]" placeholder="默认值" value="<?php echo $mfb['default']; ?>" />
									<input type="text" rel="tooltip" data-original-title="样式Class" class="input-xlarge span2" id="field_<?php echo $mfb['field']; ?>[style_class]" placeholder="样式Class" value="<?php echo $mfb['style_class']; ?>" />
									<code id="field_<?php echo $mfb['field']; ?>[rule]" rel="tooltip" data-original-title="规则,多个值请以半角逗号分隔" class="span1 ellipsis" style="float:none;margin-left:0;vertical-align: middle"><?php echo $mfb['rule']; ?></code>
									<code id="field_<?php echo $mfb['field']; ?>[event]" rel="tooltip" data-original-title="事件,以JSON格式编写" class="span1 ellipsis" style="float:none;margin-left:0;vertical-align: middle"><?php echo $mfb['event']; ?></code>
									<label rel="tooltip" data-original-title="在列表显示" class="checkbox" style="display:inline-block;vertical-align: middle">
										<input type="checkbox" id="field_<?php echo $mfb['field']; ?>[show_on_list]" value="1" <?php if ($mfb['show_on_list']) echo ' checked'; ?> />
										<i class="icon-list-alt"></i>
									</label>
									&nbsp;<label rel="tooltip" data-original-title="在表单显示" class="checkbox" style="display:inline-block;vertical-align: middle">
										<input type="checkbox" id="field_<?php echo $mfb['field']; ?>[show_on_form]" value="1" <?php if ($mfb['show_on_form']) echo ' checked'; ?> />
										<i class="icon-th-list"></i>
									</label>
									<button id="field_<?php echo $mfb['field']; ?>[commit]" class="btn btn-info" title="提交更改" style="margin-left:10px;" disabled onclick="submitField('<?php echo $mfb['field'] ?>', 0, 1)"><i class="icon-retweet icon-white"></i></button>
								</div>
							</div>
							<?php
						}
					}
					?>
				</fieldset>
			</form>
		</div>
		<div class="tab-pane fade active in" id="extend">
			<form class="form-horizontal" onsubmit="return false;">
				<fieldset>
					<?php
					foreach ($model_fields_extend as $mfb) {
						if ($mfb['field'] != 'content_id') {
							?>
							<div class="control-group" id="field_<?php echo $mfb['field']; ?>_original" rel="">
								<label class="control-label ellipsis" rel="tooltip" data-original-title="<?php echo $mfb['field']; ?>" for="field_<?php echo $mfb['field']; ?>[alias]"><strong><?php echo $mfb['field']; ?></strong></label>
								<div class="controls">
									<input type="text" rel="tooltip" data-original-title="别名" class="input-xlarge span2" id="field_<?php echo $mfb['field']; ?>[alias]" placeholder="别名" value="<?php echo $mfb['alias']; ?>" />
									<input type="text" rel="tooltip" data-original-title="序号" class="input-xlarge span1" id="field_<?php echo $mfb['field']; ?>[sort]" placeholder="0" value="<?php echo $mfb['sort']; ?>" style="text-align:center;width:40px;" />
									<select rel="tooltip" data-original-title="数据类型" class="span2" id="field_<?php echo $mfb['field']; ?>[data_type]" onchange="if($.inArray($(this).val(), ['CHAR', 'VARCHAR', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL', 'BIT']) != -1)$(this).next().removeAttr('disabled').focus();else $(this).next().attr('disabled', true)">
										<?php foreach ($data_types as $dt) { ?>
											<option value="<?php echo $dt; ?>"<?php if ($mfb['data_type'] == $dt) echo ' selected'; ?>><?php echo $dt; ?></option>
										<?php } ?>
									</select>
									<input type="text" rel="tooltip" data-original-title="数据长度" class="input-xlarge span1" id="field_<?php echo $mfb['field']; ?>[length]" placeholder="长度" value="<?php echo $mfb['length']; ?>" style="text-align:center;width:40px" />

									<select rel="tooltip" data-original-title="控件类型" class="span1" id="field_<?php echo $mfb['field']; ?>[type]">
										<?php foreach ($types as $dt) { ?>
											<option value="<?php echo $dt; ?>"<?php if ($mfb['type'] == $dt) echo ' selected'; ?>><?php echo $dt; ?></option>
										<?php } ?>
									</select>
									<code id="field_<?php echo $mfb['field']; ?>[extend_value]" rel="tooltip" data-original-title="选项,仅作用于 radio,checkbox,select,multiselect" class="span1 ellipsis" style="float:none;margin-left:0;vertical-align:middle"><?php echo $mfb['extend_value']; ?></code>
									<input type="text" rel="tooltip" data-original-title="默认值,多个值请以半角逗号分隔" class="span2" id="field_<?php echo $mfb['field']; ?>[default]" placeholder="默认值" value="<?php echo $mfb['default']; ?>" />
									<input type="text" rel="tooltip" data-original-title="样式Class" class="input-xlarge span2" id="field_<?php echo $mfb['field']; ?>[style_class]" placeholder="样式Class" value="<?php echo $mfb['style_class']; ?>" />
									<code id="field_<?php echo $mfb['field']; ?>[rule]" rel="tooltip" data-original-title="规则,多个值请以半角逗号分隔" class="span1 ellipsis" style="float:none;margin-left:0;vertical-align: middle"><?php echo $mfb['rule']; ?></code>
									<code id="field_<?php echo $mfb['field']; ?>[event]" rel="tooltip" data-original-title="事件,以JSON格式编写" class="span1 ellipsis" style="float:none;margin-left:0;vertical-align: middle"><?php echo $mfb['event']; ?></code>
									<label rel="tooltip" data-original-title="在列表显示" class="checkbox" style="display:inline-block;vertical-align: middle">
										<input type="checkbox" id="field_<?php echo $mfb['field']; ?>[show_on_list]" value="1"<?php if ($mfb['show_on_list']) echo ' checked'; ?> />
										<i class="icon-list-alt"></i>
									</label>
									&nbsp;<label rel="tooltip" data-original-title="在表单显示" class="checkbox" style="display:inline-block;vertical-align: middle">
										<input type="checkbox" id="field_<?php echo $mfb['field']; ?>[show_on_form]" value="1" <?php if ($mfb['show_on_form']) echo ' checked'; ?> />
										<i class="icon-th-list"></i>
									</label>
									<button id="field_<?php echo $mfb['field']; ?>[commit]" class="btn btn-info" title="提交更改" style="margin-left:10px;" disabled onclick="submitField('<?php echo $mfb['field'] ?>');"><i class="icon-retweet icon-white"></i></button>
									<a id="field_<?php echo $mfb['field']; ?>[del]" title="删除" class="btn btn-danger delButton" href="javascript:removeField('<?php echo $mfb['field']; ?>')"><i class="icon-remove icon-white"></i></a>
								</div>
							</div>
							<?php
						}
					}
					?>
				</fieldset>
				<button type="button" class="btn addNewField" onclick="addNewField()"><i class="icon-plus-sign"></i>新增</button>
				<button type="button" class="btn pull-right addNewField" onclick="addNewField()"><i class="icon-plus-sign"></i>新增</button>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#libox-navigator').html('系统 / 模型');
		$('[data-parent="#accordion2"]:eq(1)').click();
		$('[rel="tooltip"]').tooltip();
		$('[id$="[sort]"],[id$="[length]"]').forceNumeric();
		$('[id$="[del]"]').confirmDialog({
			message: '<strong>确定要删除吗?</strong>',
			confirmButton: '确定',
			cancelButton: '取消'
		});
		$('[id$="[rule]"],[id$="[event]"]').css({background:'#FFF', cursor: 'pointer'}).attr({'data-toggle':'modal', 'href':'#myModal'});
		$('[id$="[rule]"]').bind('click',function(){
			var field = getSelfField(this);
			$('#myModal .modal-header > h3').text('修改 ['+field+'] 规则');
			$('#myModal .modal-body').html('<textarea class="input-xlarge" id="textarea-value" rows="3" style="width:518px;resize:none;"></textarea><p>可用规则: required,integer,length[min-max],email<br />使用方法: <code>required,length[3-20]</code></p>');
			$('#textarea-value').val($(this).text());
			$('#myModal .modal-footer>.btn.btn-primary').attr('onclick', '$(\'[id^="field_'+field+'"][id$="[regexp]"]\').text($("#textarea-value").val());checkStatus("'+field+'");$(this).prev().click()');
		});
		$('[id$="[event]"]').bind('click',function(){
			var field = getSelfField(this);
			$('#myModal .modal-header > h3').text('修改 ['+field+'] 事件');
			$('#myModal .modal-body').html('<textarea class="input-xlarge" id="textarea-value" rows="3" style="width:518px;resize:none;"></textarea><p>JSON 格式</p>');
			$('#textarea-value').val($(this).text());
			$('#myModal .modal-footer>.btn.btn-primary').attr('onclick', '$(\'[id^="field_'+field+'"][id$="[event]"]\').text($("#textarea-value").val());checkStatus("'+field+'");$(this).prev().click()');
		});
		$('[id$="[type]"]').change(function(){
			var field = getSelfField(this);
			if (-1 != $.inArray($(this).val(), ['CHECKBOX', 'RADIO', 'SELECT', 'MULTISELECT'])){
				$('[id^="field_'+field+'"][id$="[extend_value]"]').css({cursor:'pointer', 'background': '#FFF'}).attr({'data-toggle':'modal', 'href':'#myModal'})
				.removeAttr('disabled').bind('click',function(){
					$('#myModal .modal-header > h3').text('修改 ['+field+'] 选项的值');
					$('#myModal .modal-body').html('<textarea class="input-xlarge" id="textarea-value" rows="3" style="width:518px;resize:none;"></textarea><p>格式: value:text,value:text...</p>');
					$('#textarea-value').val($(this).text());
					$('#myModal .modal-footer>.btn.btn-primary').attr('onclick', '$(\'[id^="field_'+field+'"][id$="[extend_value]"]\').text($("#textarea-value").val());checkStatus("'+field+'");$(this).prev().click()');
				});
			} else {
				$('[id^="field_'+field+'"][id$="[extend_value]"]').css({cursor:'default', 'background':'#F7F7F9'}).removeAttr('data-toggle').removeAttr('href')
				.attr('disabled', true);
			}
		});
		//check change
		$('[id$="[alias]"], [id$="[sort]"], [id$="[default]"], [id$="[style_class]"]').blur(function(){
			var field = getSelfField(this);
			checkStatus(field);
		});
		$('[id$="[type]"]').change(function(){
			var field = getSelfField(this);
			checkStatus(field);
		});
		$('[id$="[show_on_list]"], [id$="[show_on_form]"]').click(function(){
			var field = getSelfField(this);
			checkStatus(field);
		});
	});
	var data_types = <?php echo json_encode($data_types); ?>;
	var types = <?php echo json_encode($types); ?>;
	var current_model_id = <?php echo $model_id; ?>;
</script>
<script type="text/javascript" src="static/backend/model.field.js"></script>