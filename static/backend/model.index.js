function addNewModel() {
	var model_name = $.trim($('#newModel #model_name').val());
	var alias_name = $.trim($('#newModel #alias_name').val());
	var description = $.trim($('#newModel #description').val());
	var status = $('#newModel #status').val();
	if (model_name == '')
		return libox.showMsg({
			msg:'Invalid model name'
		});
	if (alias_name == '')
		return libox.showMsg({
			msg:'Invalid alias name'
		});
	libox.showLoading();
	$.ajax({
		url: '/?module=backend&action=model&trick=addModel',
		type: 'post',
		data: {
			model_name: model_name, 
			alias_name: alias_name, 
			description: description, 
			status: status
		},
		error: function(){},
		success: function(response){
			libox.hideLoading();
			if (response.status == 1) {
				model_id = response.msg;
				// remove new form
				$('#newModel').remove();
				$('#addNewButton').before('<tr rel="model_'+model_id+'">'+
					'<td class="model_id">'+model_id+'</td>'+
					'<td><a href="#myModal" class="model_name" data-toggle="modal">'+model_name+'</a></td>'+
					'<td><a href="#myModal" class="alias_name" data-toggle="modal">'+alias_name+'</a></td>'+
					'<td><a href="#myModal" class="description" data-toggle="modal">'+description+'</a></td>'+
					'<td><div class="btn-group" style="width:108px;margin:0 auto">'+
					'<button onclick="$(this).next().removeClass(\'btn-danger\');$(this).addClass(\'btn-success\');updateModelAttr('+model_id+', \'status\', 1);" class="btn'+(status == 1?' btn-success':'')+'">可用</button>'+
					'<button onclick="$(this).prev().removeClass(\'btn-success\');$(this).addClass(\'btn-danger\');updateModelAttr('+model_id+', \'status\', 0);" class="btn'+(status == 0?' btn-danger':'')+'">不可用</button>'+
					'</div></td>'+
					'<td><div class="btn-group" style="width:150px;margin:0 auto">'+
					'<button class="btn btn-primary" onclick="libox.load(\'/?module=backend&action=model&trick=fields&model_id='+model_id+'\')"><i class="icon-th-list icon-white"></i>字段管理</button>'+
					'<a href="javascript:removeModel('+model_id+')" class="btn btn-danger delButton"><i class="icon-trash icon-white"></i>删除</button>'+
					'</div></td>'+
					'</tr>');
				$('#model-list [rel="model_'+model_id+'"] .delButton').confirmDialog({
					message: '<strong>确定要删除吗?</strong>',
					confirmButton: '确定',
					cancelButton: '取消'
				});
				$('#addNewButton button').removeAttr('disabled');
			} else {
				libox.showMsg({
					msg:response.msg
					});
			}
		}
	});
}
function removeModel(model_id) {
	$('tr[rel="model_'+model_id+'"] *').attr('disabled', true);
	$('tr[rel="model_'+model_id+'"] .delButton').addClass('loading');
	$.ajax({
		url: '/?module=backend&action=model&trick=removeModel',
		type: 'post',
		data: {
			model_id: model_id
		},
		error: function(){},
		success: function(response) {
			if (response.status == 1) {
				$('tr[rel="model_'+model_id+'"]').fadeOut('fast', function(){
					$(this).remove();
				});
			} else {
				$('tr[rel="model_'+model_id+'"] *').removeAttr('disabled');
				$('tr[rel="model_'+model_id+'"] .delButton').removeClass('loading');
				libox.showMsg({
					msg: response.msg
					});
			}
		}
	})
}
function removeNewModel() {
	$('#newModel').fadeOut('fast', function(){
		$(this).remove();
		$('.addNewModel').removeAttr('disabled');
	});
}
function updateModelAttr(model_id, attr_name, attr_value) {
	$('[rel="model_'+model_id+'"] *').attr('disabled', true);
	$('[rel="model_'+model_id+'"] .delButton').attr('href', 'javascript:void(0);');
	libox.showLoading();
	$.ajax({
		url: '/?module=backend&action=model&trick=updateModelAttr',
		type: 'post',
		data: {
			model_id: model_id, 
			attr_name: attr_name, 
			attr_value: attr_value
		},
		error: function(){},
		success: function(response) {
			libox.hideLoading();
			if (response.status != 1) {
				libox.showMsg({
					msg:response.msg
					});
			}
			$('[rel="model_'+model_id+'"] .'+attr_name).text(attr_value);
			$('[rel="model_'+model_id+'"] *').removeAttr('disabled');
			$('[rel="model_'+model_id+'"] .delButton').attr('href', 'javascript:removeModel('+model_id+');');
		}
	});
}