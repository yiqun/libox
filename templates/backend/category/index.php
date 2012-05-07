<style type="text/css">
	#libox-templates #form-list{
		display:none;
	}
	#libox-templates .form-horizontal .control-label{
		width: 80px;
	}
	#libox-templates .form-horizontal .controls{
		margin-left: 100px;
	}
	.btn-group + .btn-group{
		margin-left:0;
	}
</style>
<script type="text/javascript">
	var categoryNewElement;
	$(document).ready(function(){
		$('#libox-navigator').html('系统 / 分类');
		$('.delButton').confirmDialog({
			message: '<strong>确定要删除吗?</strong>',
			confirmButton: '确定',
			cancelButton: '取消'
		});
		$('.cat_name, .description').css('cursor', 'pointer').click(function(){
			// get model_id
			var cat_id = $(this).parent().parent().children('.cat_id').text();
			var elm_id = $(this).attr('class');
			$('#myModal .modal-header > h3').text('修改');
			$('#myModal .modal-body').html('<input class="input-xlarge" id="input-value" style="width:518px;">');
			$('#input-value').val($(this).text());
			$('#myModal .modal-footer>.btn.btn-primary').attr('onclick', '$(this).prev().click();updateCatAttr('+cat_id+', "'+elm_id+'", $(\'#input-value\').val());');
		});
		$('.category_id[rel="tooltip"][name="thumb_id"]').tooltip({placement:'right'});
		categoryNewElement = $('.category_id[rel="newCat"]').parent().clone();
	});
	function updateCategoryAttr(cat_id, attr_name, attr_value, callback) {
	libox.showLoading();
		$.ajax({
			url: '/?module=backend&action=category&trick=updateCategoryAttr',
			type: 'post',
			data: {cat_id:cat_id, attr_name: attr_name, attr_value: attr_value},
			error:function(){},
			success: function(response) {
				libox.hideLoading();
				if (response.status == 0) {
					libox.showMsg({msg: response.msg});
				} else {

				}
				if ('function' == typeof callback) {
					callback();
				}
			}
		});
	}
	function addCategory(){
		var sort = $('.category_id[rel="newCat"] [name="sort"]').val();
		var cat_name = $.trim($('.category_id[rel="newCat"] [name="cat_name"]').val());
		var model_id = $('.category_id[rel="newCat"] [name="model_id"]').val();
		var description = $.trim($('.category_id[rel="newCat"] [name="description"]').val());
		var url = $('.category_id[rel="newCat"] [name="url"]').val();
		var status = 0;
		var parent_id = <?php echo $parent_id?>;
		if (cat_name == '') {
			libox.showMsg({msg: 'Invalid category name'});
			$('.category_id[rel="newCat"] [name="cat_name"]').focus();
			return false;
		}
		$('.category_id[rel="newCat"] *').attr('disabled', true);
		libox.showLoading();
		$.ajax({
			url: '/?module=backend&action=category&trick=addCategory',
			type: 'post',
			data: {sort:sort, cat_name:cat_name, parent_id:parent_id, model_id: model_id, description: description, url:url, parent_id:parent_id, status: status},
			error: function(){},
			success: function(response){
				libox.hideLoading();
				if (response.status != 1) {
					libox.showMsg({msg: response.msg});
					$('.category_id[rel="newCat"] *:not([name="cat_id"])').removeAttr('disabled');
				} else {
					$('.category_id[rel="newCat"] [name="cat_id"]').val(response.msg).attr('disabled', true);
					$('.category_id[rel="newCat"] [name="model_id"]').after(
					' <div class="span1" name="thumb_id_'+response.msg+'" style="float:none;display:inline-block;margin-left:0;width:40px;position:relative;top:11px;" rel="tooltip" data-original-title="No image">'+
						'<a href="javascript:void(0);" onclick="$(\'#uploader_handler\').val(\'#thumb_id_'+response.msg+'\');$(\'#uploader\').click();" class="thumbnail" style="height:18px;width:30px;background:#EEE">'+
						'</a>'+
						'</div>'
				);
					$('.category_id[rel="newCat"] [name="description"]').removeAttr('style');
					var addButton = $('.category_id[rel="newCat"] button:last');
					addButton.before(
					' <button class="btn" onclick="libox.load(\'/?module=backend&action=category&trick=index&parent_id='+response.msg+'\')">子分类</button>'+
						' <div class="btn-group" style="width:95px;float:none;display:inline-block;position:relative;top:11px;">'+
						'<button name="status" onclick="updateCategoryAttr('+response.msg+', \'status\', 1, function(){$(\'.category_id[rel=\\\''+response.msg+'\\\'] [name=\\\'status\\\']:first\').addClass(\'btn-success\').next().removeClass(\'btn-danger\');});" class="btn">开启</button>'+
						'<button name="status" onclick="updateCategoryAttr('+response.msg+', \'status\', 0, function(){$(\'.category_id[rel=\\\''+response.msg+'\\\'] [name=\\\'status\\\']:last\').addClass(\'btn-danger\').prev().removeClass(\'btn-success\');});" class="btn btn-danger">禁用</button>'+
						'</div> '+
						'<a class="btn btn-danger delButton" href="javascript:removeCategory('+response.msg+');">删除</a>'
				);
					addButton.remove();
					$('.category_id[rel="newCat"] *:not([name="cat_id"])').removeAttr('disabled');
					$('.category_id[rel="newCat"]').attr('rel', response.msg);
					$('.category_id[rel="'+response.msg+'"]').parent().after(categoryNewElement);
					categoryNewElement = $('.category_id[rel="newCat"]').parent().clone();
				}
			}
		});
	}
	function removeCategory(cat_id) {
		if (!cat_id || isNaN(cat_id) || Number(cat_id) < 1) {
			libox.showMsg({msg:'Invalid category id'});
			return;
		}
		libox.showLoading();
		$.ajax({
			url: '/?module=backend&action=category&trick=removeCategory',
			type:'post',
			data: {cat_id: cat_id},
			error: function(){},
			success: function(response){
				libox.hideLoading();
				if (response.status == 0) {
					libox.showMsg({msg: response.msg});
				} else {
					$('.category_id[rel='+cat_id+']').parent().fadeOut('fast', function(){$(this).remove()});
				}
			}
		});
	}
</script>
<form class="form-horizontal" onsubmit="return false;">
	<fieldset>
		<?php
		foreach ($categories as $cat){
			?>
			<div class="control-group">
				<div class="controls category_id" rel="<?php echo $cat['cat_id'];?>" style="margin-left:5px">
					<input type="text" name="cat_id" rel="tooltip" data-original-title="ID" class="input-mini span1" style="width:40px;text-align:center;" value="<?php echo $cat['cat_id'];?>" disabled />
					<input type="text" name="sort" onblur="updateCategoryAttr(<?php echo $cat['cat_id'];?>, 'sort', $(this).val())" rel="tooltip" data-original-title="排序" class="input-mini span1" style="text-align:center;" value="<?php echo $cat['sort'];?>" placeholder="排序" />
					<input type="text" name="cat_name" onblur="updateCategoryAttr(<?php echo $cat['cat_id'];?>, 'cat_name', $(this).val())" rel="tooltip" data-original-title="名称" class="input-mini span2" value="<?php echo $cat['cat_name'];?>" placeholder="名称" />
					<select class="span1" onchange="updateCategoryAttr(<?php echo $cat['cat_id'];?>, 'model_id', $(this).val())" name="model_id" rel="tooltip" data-original-title="模型" >
						<?php foreach ($models as $m) { ?>
							<option value="<?php echo $m['model_id']; ?>" <?php if ($m['model_id'] == $cat['model_id']) echo ' selected'; ?>><?php echo $m['model_name']; ?></option>
						<?php } ?>
					</select>
					<div class="span1" name="thumb_id_<?php echo $cat['cat_id'];?>" style="float:none;display:inline-block;margin-left:0;width:40px;position:relative;top:11px;" rel="tooltip" data-original-title="No image">
						<a href="javascript:void(0);" onclick="$('#uploader_handler').val('#thumb_id_<?php echo $cat['cat_id'];?>');$('#uploader').click();" class="thumbnail" style="height:18px;width:30px;background:#EEE">
						</a>
					</div>
					<input type="text" onblur="updateCategoryAttr(<?php echo $cat['cat_id'];?>, 'description', $(this).val())" name="description" rel="tooltip" data-original-title="描述" class="input-mini span5" value="<?php echo $cat['description'];?>" placeholder="描述" />
					<div class="input-prepend">
						<span class="add-on">www</span><input class="span3" onblur="updateCategoryAttr(<?php echo $cat['cat_id'];?>, 'url', $(this).val())" name="url" rel="tooltip" data-original-title="链接" size="16" type="text" value="<?php echo $cat['url'];?>" placeholder="链接" />
					</div>
					<button class="btn" onclick="libox.load('/?module=backend&action=category&trick=index&parent_id=<?php echo $cat['cat_id'];?>')">子分类</button>
					<div class="btn-group" style="width:95px;float:none;display:inline-block;position:relative;top:11px;">
						<button name="status" onclick="updateCategoryAttr(<?php echo $cat['cat_id'];?>, 'status', 1, function(){$('.category_id[rel=\'<?php echo $cat['cat_id'];?>\'] [name=\'status\']:first').addClass('btn-success').next().removeClass('btn-danger');});" class="btn<?php if($cat['status']){?> btn-success<?php }?>">开启</button>
						<button name="status" onclick="updateCategoryAttr(<?php echo $cat['cat_id'];?>, 'status', 0, function(){$('.category_id[rel=\'<?php echo $cat['cat_id'];?>\'] [name=\'status\']:last').addClass('btn-danger').prev().removeClass('btn-success');});" class="btn<?php if(!$cat['status']){?> btn-danger<?php }?>">禁用</button>
					</div>
					<a class="btn btn-danger delButton" href="javascript:removeCategory(<?php echo $cat['cat_id'];?>);">删除</a>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="control-group">
			<div class="controls category_id" rel="newCat" style="margin-left:5px">
				<input type="text" rel="tooltip" data-original-title="ID" class="input-mini span1" style="width:40px;text-align:center;" value="" placeholder="ID" name="cat_id" disabled />
				<input type="text" name="sort" rel="tooltip" data-original-title="排序" class="input-mini span1" style="text-align:center;" value="0" />
				<input type="text" name="cat_name" rel="tooltip" data-original-title="名称" class="input-mini span2" value="" placeholder="名称" />
				<select class="span1" name="model_id" rel="tooltip" data-original-title="模型">
					<?php foreach ($models as $m) { ?>
						<option value="<?php echo $m['model_id']; ?>" <?php if ($m['model_id'] == $parent_model_id) echo ' selected'; ?>><?php echo $m['model_name']; ?></option>
					<?php } ?>
				</select>
				<input type="text" name="description" rel="tooltip" data-original-title="描述" class="input-mini span5" style="width:414px" value="" placeholder="描述" />
				<div class="input-prepend">
					<span class="add-on">www</span><input class="span3" name="url" rel="tooltip" data-original-title="链接" size="16" type="text" placeholder="链接" />
				</div>
				<button class="btn" onclick="addCategory()"><i class="icon-plus-sign"></i> 提交</button>
			</div>
		</div>
	</fieldset>
</form>
<!--<form action="/?module=upload&action=upload&trick=single_upload" method="post" enctype="multipart/form-data" target="hide_uploader">
	<input type="file" name="upload_file" id="uploader" onchange="$(this).closest('form').submit();" />
	<input type="hidden" name="uploader_handler" />
</form>
<iframe src="" style="display: none;" id="hide_uploader" name="hide_uploader"></iframe>-->