<style type="text/css">
	#libox-templates #model-form,
	#libox-templates #form-list{
		display:none;
	}
	#libox-templates .form-horizontal .control-label{
		width: 80px;
	}
	#libox-templates .form-horizontal .controls{
		margin-left: 100px;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#libox-navigator').html('系统 / 模型');
		$('#model-list .delButton').confirmDialog({
			message: '<strong>确定要删除吗?</strong>',
			confirmButton: '确定',
			cancelButton: '取消'
		});
		// set id,name,alias_name,description
		$('#model-list .model_name, #model-list .alias_name, #model-list .description').css('cursor', 'pointer').click(function(){
			// get model_id
			var model_id = $(this).parent().parent().children('.model_id').text();
			var elm_id = $(this).attr('class');
			$('#myModal .modal-header > h3').text('修改');
			$('#myModal .modal-body').html('<input class="input-xlarge" id="input-value" style="width:518px;">');
			$('#input-value').val($(this).text());
			$('#myModal .modal-footer>.btn.btn-primary').attr('onclick', '/*$(\'#model-list [rel="model_'+model_id+'"] .'+elm_id+'\').text($(\'#input-value\').val());*/$(this).prev().click();updateModelAttr('+model_id+', "'+elm_id+'", $(\'#input-value\').val());');
		});
	});
</script>
<script type="text/javascript" src="static/backend/model.index.js"></script>
<div id="model-list">
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="5%">#</th>
				<th width="12%">名称</th>
				<th width="12%">别名</th>
				<th width="40%">描述</th>
				<th width="13%" style="text-align:center">状态</th>
				<th width="18%" style="text-align:center">管理</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($models as $m) { ?>
				<tr rel="model_<?php echo $m['model_id']; ?>">
					<td class="model_id"><?php echo $m['model_id']; ?></td>
					<td><a href="#myModal" class="model_name" data-toggle="modal"><?php echo $m['model_name']; ?></a></td>
					<td><a href="#myModal" class="alias_name" data-toggle="modal"><?php echo $m['alias_name']; ?></a></td>
					<td><a href="#myModal" class="description" data-toggle="modal"><?php echo $m['description']; ?></a></td>
					<td><div class="btn-group" style="width:108px;margin:0 auto">
							<button onclick="$(this).next().removeClass('btn-danger');$(this).addClass('btn-success');updateModelAttr(<?php echo $m['model_id'] ?>, 'status', 1);" class="btn<?php if ($m['status']) { ?> btn-success<?php } ?>">开启</button>
							<button onclick="$(this).prev().removeClass('btn-success');$(this).addClass('btn-danger');updateModelAttr(<?php echo $m['model_id'] ?>, 'status', 0);" class="btn<?php if (!$m['status']) { ?> btn-danger<?php } ?>">禁用</button>
						</div></td>
					<td><div class="btn-group" style="width:150px;margin:0 auto">
							<button class="btn btn-primary" onclick="libox.load('/?module=backend&action=model&trick=fields&model_id=<?php echo $m['model_id']; ?>')"><i class="icon-th-list icon-white"></i>字段管理</button>
							<a href="javascript:removeModel(<?php echo $m['model_id']; ?>)" class="btn btn-danger delButton"><i class="icon-trash icon-white"></i>删除</button>
						</div></td>
				</tr>
			<?php } ?>
			<tr id="addNewButton"><td colspan="6"><center><button type="button" class="btn addNewModel span3" onclick="$(this).closest('tr').before('<tr  id=\'newModel\'><td></td><td><input type=\'text\' id=\'model_name\' class=\'input-mini\' placeholder=\'名称\'></td><td><input type=\'text\' id=\'alias_name\' class=\'input-mini\' placeholder=\'别名\'></td><td><input type=\'text\' id=\'description\' class=\'input-large span5\' placeholder=\'描述\'></td><td style=\'text-align:center\'><select class=\'span2\' id=\'status\'><option value=1>可用</optoin><option value=0>不可用</option></select></td><td><button onclick=\'addNewModel()\' class=\'btn btn-primary\' style=\'margin-left:30px;\'><i class=\'icon-plus-sign icon-white\'></i> 提交</button><button onclick=\'removeNewModel();\' class=\'btn\' style=\'margin-left:10px;\'><i class=\'icon-minus-sign\'></i> 取消</button></td></tr>');$('#newModel #model_name').focus();$(this).attr('disabled', true)"><i class="icon-plus-sign"></i>&nbsp;&nbsp;新增</button></center></td></tr>
		</tbody>
	</table>
</div>