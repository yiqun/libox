var field_attrs = ['alias', 'sort', 'data_type', 'length', 'type', 'extend_value', 'default', 'style_class', 'rule', 'event', 'show_on_list', 'show_on_form'];
function createOptions(arr, val) {
	var opt = '';
	for(var i in arr) {
		opt += '<option value="'+arr[i]+'"'+(arr[i]==val?' selected': '')+'>'+arr[i]+'</option>';
	}
	return opt;
}
function getSelfField(o) {
	return $(o).attr('id').substr(6).replace(/\[[^\]]+\]/g,'');
}
function checkStatus(field) {
	// compare field attr with control-group rel
	var original_attrs = $.parseJSON($('#field_'+field+'_original').attr('rel'));
	// if change show update button, else hide update button
	var change = false;
	out:
		for (var i in field_attrs) {
		switch(field_attrs[i]) {
			case 'alias':
			case 'sort':
			case 'data_type':
			case 'length':
			case 'type':
			case 'default':
			case 'style_class':
				if ((original_attrs == null || typeof original_attrs[field_attrs[i]] == 'undefined') && $('[id^="field_'+field+'"][id$="['+field_attrs[i]+']"]').val() != '' || original_attrs != null && typeof original_attrs[field_attrs[i]] != 'undefined' && original_attrs[field_attrs[i]] != $('[id^="field_'+field+'"][id$="['+field_attrs[i]+']"]').val()) {
					change = true;
					break out;
				}
				break;
			case 'extend_value':
			case 'rule':
			case 'event':
				if ((original_attrs == null || typeof original_attrs[field_attrs[i]] == 'undefined') && $('[id^="field_'+field+'"][id$="['+field_attrs[i]+']"]').text() != '' || original_attrs != null && typeof original_attrs[field_attrs[i]] != 'undefined' && original_attrs[field_attrs[i]] != $('[id^="field_'+field+'"][id$="['+field_attrs[i]+']"]').text()) {
					change = true;
					break out;
				}
				break;
			case 'show_on_list':
			case 'show_on_form':
				var val = $('[id^="field_'+field+'"][id$="['+field_attrs[i]+']"]').is(':checked')? 1: 0;
				if (original_attrs != null && typeof original_attrs[field_attrs[i]] != 'undefined' && original_attrs[field_attrs[i]] != val || val == 1) {
					change = true;
					break out;
				}
				break;
		}
	}
	if (change) {
		$('[id^="field_'+field+'"][id$="[commit]"]').removeAttr('disabled');
	} else {
		$('[id^="field_'+field+'"][id$="[commit]"]').attr('disabled', true);
	}
}
function submitField(field,isNew,isBasic) {
	// set submiting
	var data = {model_id: current_model_id,field: (isNew? $('[id^="field_'+field+'"][id$="[field]"]').val(): field), isNew: isNew, isBasic: isBasic};
	for (var i in field_attrs) {
		switch(field_attrs[i]) {
			case 'alias':
			case 'sort':
			case 'data_type':
			case 'length':
			case 'type':
			case 'default':
			case 'style_class':
				data[field_attrs[i]] = $('[id^="field_'+field+'"][id$="['+field_attrs[i]+']"]').val();
				break;
			case 'extend_value':
			case 'rule':
			case 'event':
				data[field_attrs[i]] = $('[id^="field_'+field+'"][id$="['+field_attrs[i]+']"]').text();
				break;
			case 'show_on_list':
			case 'show_on_form':
				data[field_attrs[i]] = $('[id^="field_'+field+'"][id$="['+field_attrs[i]+']"]').is(':checked')? 1: 0;
				break;
		}
	}
	$('[id^="field_'+field+'"][id$="[commit]"]').addClass('loading').attr('disabled', true).next().attr('disabled', true);
	$('#field_'+field+'_original *').attr('disabled', true);
	$.ajax({
		url: '?module=backend&action=model&trick=submitField',
		type: 'post',
		data: data,
		error: function() {
			$('#field_'+field+'_original *').removeAttr('disabled');
			$('[id^="field_'+field+'"][id$="[commit]"]').removeClass('loading').removeAttr('disabled').next().removeAttr('disabled');
		},
		success: function(result) {
			if (result.status == 1) {
				$('#field_'+field+'_original').attr('rel', JSON.stringify(data));
				if (isNew) {
					field = $('[id^="field_'+field+'"][id$="[field]"]').val();
					// update new field id
					$('[id^="field_newone"]').each(function(){
						$(this).attr('id', $(this).attr('id').replace('newone', field));
					});
					// set button
					$('#field_'+field+'_original .controls [id$="[commit]"]').attr({'onclick':'submitField("'+field+'")', 'disabled': true}).html('<i class="icon-retweet icon-white"></i>');
					var prev = $('#field_'+field+'_original .controls [id$="[del]"]').prev();
					$('#field_'+field+'_original .controls [id$="[del]"]').remove();
					prev.after(' <a id="field_'+field+'[del]" title="删除" class="btn btn-danger delButton" href="javascript:removeField(\''+field+'\')"><i class="icon-remove icon-white"></i></a>');
					// set field input to label
					$('#field_'+field+'_original > .control-label').addClass('ellipsis').removeAttr('style').html('<strong>'+field+'</strong>');
					$('[id^="field_'+field+'"][id$="[del]"]').confirmDialog({
						message: '<strong>确定要删除吗?</strong>',
						confirmButton: '确定',
						cancelButton: '取消'
					});
					$('.addNewField').removeAttr('disabled');;
				}
			} else {
				libox.showMsg({msg: result.msg});
			}
			$('#field_'+field+'_original *').removeAttr('disabled');
			$('[id^="field_'+field+'"][id$="[commit]"]').removeClass('loading').removeAttr('disabled').next().removeAttr('disabled');
		}
	});
}
function removeField(field) {
	$('[id^="field_'+field+'"][id$="[del]"]').addClass('loading').attr('href', 'javascript:void(0);').prev().attr('disabled', true);
	$('#field_'+field+'_original *').attr('disabled', true);
	$.ajax({
		url: '?module=backend&action=model&trick=removeField',
		type: 'post',
		data: {model_id: current_model_id, field: field},
		error: function() {
			$('#field_'+field+'_original *').removeAttr('disabled');
			$('[id^="field_'+field+'"][id$="[del]"]').removeClass('loading').removeAttr('disabled').prev().removeAttr('disabled');
		},
		success: function(result) {
			if (result.status == 1) {
				$('#field_'+field+'_original').fadeOut('fast', function(){$(this).remove()});
			} else {
				$('[id^="field_'+field+'"][id$="[del]"]').removeClass('loading').removeAttr('disabled').attr('href','javascript:removeField("'+field+'");');
				libox.showMsg({msg: result.msg});
			}
		}
	});
}
function addNewField(){
	$('#model-struct-list>#myTabContent>#extend>form>fieldset').append('<div class="control-group" id="field_newone_original" rel="">'+
		'<label class="control-label" style="padding:0;margin:0"><input type="text" class="input-xlarge" id="field_newone[field]" placeholder="名称" value="" style="width:80px;" /></label>'+
		'<div class="controls">'+
		'<input type="text" rel="tooltip" data-original-title="别名" class="input-xlarge span2" id="field_newone[alias]" placeholder="别名" value=""> '+
		'<input type="text" rel="tooltip" data-original-title="序号" class="input-xlarge span1" id="field_newone[sort]" placeholder="0" value="" style="text-align:center;width:40px;"> '+
		'<select rel="tooltip" data-original-title="数据类型" class="span2" id="field_newone[data_type]" onchange="if($.inArray($(this).val(), [\'CHAR\', \'VARCHAR\', \'TINYINT\', \'SMALLINT\', \'MEDIUMINT\', \'INT\', \'BIGINT\', \'FLOAT\', \'DOUBLE\', \'DECIMAL\', \'BIT\']) != -1)$(this).next().removeAttr(\'disabled\').focus();else $(this).next().attr(\'disabled\', true)">'+
		createOptions(data_types)+
		'</select> '+
		'<input type="text" rel="tooltip" data-original-title="数据长度" class="input-xlarge span1" id="field_newone[length]" placeholder="长度" value="" style="text-align:center;width:40px"> '+
		'<select rel="tooltip" data-original-title="控件类型" class="span1" id="field_newone[type]">'+
		createOptions(types)+
		'</select> '+
		'<code id="field_newone[extend_value]" rel="tooltip" data-original-title="选项,仅作用于 radio,checkbox,select,multiselect" class="span1 ellipsis" style="float:none;margin-left:0;vertical-align:middle">Wrapping|1,element|2,for|3,displaying|4,data|5,in|6,a|7,tabular|8,format|9</code> '+
		'<input type="text" rel="tooltip" data-original-title="默认值,多个值请以半角逗号分隔" class="span2" id="field_newone[default]" placeholder="默认值" value=""> '+
		'<input type="text" rel="tooltip" data-original-title="样式Class" class="input-xlarge span2" id="field_newone[style_class]" placeholder="样式Class" value=""> '+
		'<code id="field_newone[rule]" rel="tooltip" data-original-title="规则,多个值请以半角逗号分隔" class="span1 ellipsis" style="float: none; margin-left: 0px; vertical-align: middle; background: white;cursor: pointer;" data-toggle="modal" href="#myModal">required,integer,length[3-20],email</code> '+
		'<code id="field_newone[event]" rel="tooltip" data-original-title="事件,以JSON格式编写" class="span1 ellipsis" style="float: none; margin-left: 0px; vertical-align: middle; background-image: initial; background-attachment: initial; background-origin: initial; background-clip: initial; background-color: rgb(255, 255, 255); cursor: pointer; background-position: initial initial; background-repeat: initial initial; " data-toggle="modal" href="#myModal"></code> '+
		'<label rel="tooltip" data-original-title="在列表显示" class="checkbox" style="display:inline-block;vertical-align: middle">'+
		'<input type="checkbox" id="field_newone[show_on_list]" value="1">'+
		'<i class="icon-list-alt"></i>'+
		'</label> '+
		'&nbsp;<label rel="tooltip" data-original-title="在表单显示" class="checkbox" style="display:inline-block;vertical-align: middle">'+
		'<input type="checkbox" id="field_newone[show_on_form]" value="1">'+
		'<i class="icon-th-list"></i>'+
		'</label> '+
		'<button id="field_newone[commit]" class="btn btn-info" title="提交新字段" style="margin-left:10px;" onclick="submitField(\'newone\', 1);"><i class="icon-plus-sign icon-white"></i></button> '+
		'<button id="field_newone[del]" onclick="$(\'#field_newone_original\').remove();$(\'.addNewField\').removeAttr(\'disabled\');" title="删除" class="btn btn-danger"><i class="icon-minus-sign icon-white"></i></button>'+
		'</div>'+
		'</div>');
	// disabled addnew button
	$('.addNewField').attr('disabled', true);
	// initialize
	$('#field_newone_original [rel="tooltip"]').tooltip();
	$('#field_newone_original [id$="[rule]"],#field_newone_original [id$="[event]"]').css({background:'#FFF', cursor: 'pointer'}).attr({'data-toggle':'modal', 'href':'#myModal'});
	$('#field_newone_original [id$="[rule]"]').bind('click',function(){
		var field = getSelfField(this);
		$('#myModal .modal-header > h3').text('修改 ['+field+'] 规则');
		$('#myModal .modal-body').html('<textarea class="input-xlarge" id="textarea-value" rows="3" style="width:518px;resize:none;"></textarea><p>可用规则: required,integer,length[min-max],email<br />使用方法: <code>required,length[3-20]</code></p>');
		$('#textarea-value').val($(this).text());
		$('#myModal .modal-footer>.btn.btn-primary').attr('onclick', '$(\'[id^="field_'+field+'"][id$="[regexp]"]\').text($("#textarea-value").val());checkStatus("'+field+'");$(this).prev().click()');
	});
	$('#field_newone_original [id$="[event]"]').bind('click',function(){
		var field = getSelfField(this);
		$('#myModal .modal-header > h3').text('修改 ['+field+'] 事件');
		$('#myModal .modal-body').html('<textarea class="input-xlarge" id="textarea-value" rows="3" style="width:518px;resize:none;"></textarea><p>格式: <code>{click: "alert(1)", focus: "11"}</code></p>');
		$('#textarea-value').val($(this).text());
		$('#myModal .modal-footer>.btn.btn-primary').attr('onclick', '$(\'[id^="field_'+field+'"][id$="[event]"]\').text($("#textarea-value").val());checkStatus("'+field+'");$(this).prev().click()');
	});
	$('#field_newone_original [id$="[type]"]').change(function(){
		var field = getSelfField(this);
		if (-1 != $.inArray($(this).val(), ['CHECKBOX', 'RADIO', 'SELECT', 'MULTISELECT'])){
			$('[id^="field_'+field+'"][id$="[extend_value]"]').css({cursor:'pointer', 'background': '#FFF'}).attr({'data-toggle':'modal', 'href':'#myModal'})
			.removeAttr('disabled').bind('click',function(){
				$('#myModal .modal-header > h3').text('修改 ['+field+'] 选项的值');
				$('#myModal .modal-body').html('<textarea class="input-xlarge" id="textarea-value" rows="3" style="width:518px;resize:none;"></textarea><p>格式: value:text,value:text</p>');
				$('#textarea-value').val($(this).text());
				$('#myModal .modal-footer>.btn.btn-primary').attr('onclick', '$(\'[id^="field_'+field+'"][id$="[extend_value]"]\').text($("#textarea-value").val());checkStatus("'+field+'");$(this).prev().click()');
			});
		} else {
			$('[id^="field_'+field+'"][id$="[extend_value]"]').css({cursor:'default', 'background':'#F7F7F9'}).removeAttr('data-toggle').removeAttr('href')
			.attr('disabled', true);
		}
	});
}
function submitNew() {
	$('.addNewField').removeAttr('disabled');
}
