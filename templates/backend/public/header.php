<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title ?></title>
    <meta name="keywords" content="<?php echo $keywords ?>" />
    <meta name="description" content="<?php echo $description ?>" />
		<link rel="stylesheet" href="http://static.andrew.li/bootstrap/css/bootstrap.css" />
		<script type="text/javascript" src="http://static.andrew.li/js/jquery.js"></script>
		<script type="text/javascript" src="http://static.andrew.li/js/json2.js"></script>
		<script type="text/javascript" src="http://static.andrew.li/bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="http://static.andrew.li/bootstrap/js/bootstrap-confirm.js"></script>
		<!--<script type="text/javascript" src="http://static.andrew.li/hogan/hogan.js"></script>
		<script type="text/javascript" src="http://static.andrew.li/hogan/template.js"></script>
		<script type="text/javascript" src="http://static.andrew.li/hogan/compiler.js"></script>-->
  </head>
	<script type="text/javascript">
		(function ($) {
			$.fn.forceNumeric = function () {
        return this.each(function () {
					$(this).keyup(function() {
						if (!/^[0-9\.,]+$/.test($(this).val())) {
							$(this).val($(this).val().replace(/[^0-9\.,]/g, ''));
						}
					});
        });
			};
		})(jQuery);
		var libox = {
			urls: [],
			url_index: 0,
			openWindow: function(params) {
				var width = params.width && !isNaN(params.width) && Number(params.width) > 0 && Number(params.width) || 400; // width default 400
				var height = params.height && !isNaN(params.height) && Number(params.height) > 0 && Number(params.height) || 200; // height default 200
				var left = ($(window).width() - width - 2)/2;
				var top = ($(window).height() - height - 2)/2;
				var zIndex = 9999;
				var id = 'normalWindow';
				var background = '#FFFF99';
				var color = '#333';
				if (params.isTop) {
					zIndex = 10000;
					id = 'topWindow';
				}
				if (params.background) {
					background = params.background;
				}
				if (params.color) {
					color = params.color;
				}
				// generate window
				if ($('body>#'+id).length === 0) {
					$('body').append('<div id="'+id+'" style="position:fixed;z-index:'+zIndex+';border-radius: 5px;display:none"></div>');
				}
				$('body>#'+id).empty();
				$('body>#'+id).css({width: width, height: height, left: left, top: top, background: background, color:color});
				$('body>#'+id).append('<div style="position:relative;width:'+(width-10)+'px;height:'+(height-10)+'px;padding:5px;"><div id="windowContent"></div></div>');
				if (params.url) {
					$('body>#'+id+'>div>#windowContent').html('<div style="line-height:'+(height-10)+'px;text-align:center">Loading ...</div>');
					$.get(params.url, function(content){
						$('body>#'+id+'>div>#windowContent').hide().html(content).fadeIn('fast', function(){
							if (typeof params.callback == 'function')
								params.callback();
						});
					});
				}	else
					$('body>#'+id+'>div>#windowContent').html(params.msg);
				if (params.closable) {
					$('body>#'+id+'>div').append('<font style="position:absolute;display:block;line-height:14px;width:14px;text-align:center;top:0;right:0;background:#EEE;color:red;cursor:pointer;border-radius: 5px;" onclick="libox.closeWindow('+(params.isTop?1:0)+')">X</font>');
				}
				$('body>#'+id).fadeIn('fast', function(){
					if (typeof params.callback == 'function' && !params.url)
						params.callback();
				});
			},
			closeWindow: function(isTop, callback) {
				$('body>#'+(isTop?'top': 'normal')+'Window').fadeOut('fast', function(){
					if (typeof callback == 'function')
						callback();
				});
			},
			showMsg: function(params) {
				params.width = params.width && !isNaN(params.width) && Number(params.width) > 0 && Number(params.width) || 200; // width default 200
				params.height = params.height && !isNaN(params.height) && Number(params.height) > 0 && Number(params.height) || 28; // height default 30
				var background = color = '';
				if (params.type) {
					params.type = $.trim(params.type).toUpperCase();
					switch (params.type) {
						case 'OK':
							background = '#CCFFFF';
							break;
						case 'ERROR':
							background  = '#FFCCCC';
							//color = '#FFF';
							break;
					}
				}
				libox.openWindow({isTop:1, msg: '<center>'+params.msg+'</center>', width: params.width, height: params.height, background: background, color: color, callback: function() {
						setTimeout(function(){
							libox.closeWindow(1, params.callback);
						}, (params.timeout&&!isNaN(params.timeout)&&Number(params.timeout)>0&&Number(params.timeout)&&params.timeout||3)*1000);
					}});
			},
			showLoading: function() {
				libox.openWindow({isTop:1, msg: '<center>Loading...</center>', width: 120, height: 28});
			},
			hideLoading: function() {
				libox.closeWindow(1);
			},
			load: function(url, direct) {
				libox.showLoading();
				if (url == null && (direct == 0 || direct == 1 || direct == -1)) {
					if (libox.url_index == libox.urls.length - 1 && direct == 1 || libox.url_index == 0 && direct == -1) {
						return;
					}
					libox.url_index = parseInt(libox.url_index+direct);
					url = libox.urls[libox.url_index];

				}
				$.getJSON(url, function(response){
					libox.hideLoading();
					if (response.status == 0)
						libox.showMsg({msg:response.msg});
					else
						$('#libox-templates').html(response.msg);
					if (typeof direct == 'undefined') {
						libox.urls[libox.urls.length>0?++libox.url_index:0] = url;
						// remove after
						var len = libox.urls.length-libox.url_index;
						if (len > 1)
							libox.urls.splice(libox.url_index+1, len);
					}
					if (libox.url_index == 0)
						$('#prev-page').attr('disabled', true);
					else
						$('#prev-page').attr('onclick','libox.load(null,-1)').removeAttr('disabled');
					if (libox.url_index == libox.urls.length-1)
						$('#next-page').attr('disabled', true);
					else
						$('#next-page').attr('onclick','libox.load(null,1)').removeAttr('disabled');
				});
			}
		}

		// onload event
		$(document).ready(function(){
			$('#libox-main').css('height', $(window).height()-95);
			$(window).resize(function(){
				$('#libox-main').css('height', $(window).height()-95);
			});
		});
	</script>
	<style type="text/css">
		::-webkit-scrollbar {
			background:transparent;overflow:visible; width:15px;}
		::-webkit-scrollbar-thumb {
			background-color:rgba(0,0,0,0.2); border:solid #fff;}
		::-webkit-scrollbar-thumb:hover {
			background:rgba(0,0,0,0.4);}
		::-webkit-scrollbar-thumb:horizontal {
			border-width:4px 6px;min-width:40px;}
		::-webkit-scrollbar-thumb:vertical {
			border-width:6px 4px;min-height:40px;}
		::-webkit-scrollbar-track-piece{
			background-color:#fff;}
		::-webkit-scrollbar-corner {
			background:transparent;}
		::-webkit-scrollbar-thumb {
			background-color: #DDD;}
		::-webkit-scrollbar-thumb:hover {
			background-color: #999;}
		body{
			/*background: url(static/public/bg_dotted.png);*/
			margin-top:90px;
			overflow:hidden;
		}
		#libox-main{
			overflow-y:auto;
		}
		#libox-header{
			background:#2D2D2D;
			color:white;
			border-bottom: 1px solid #5D5D5D;
			padding: 0 5px;
			height: 28px;
		}
		#libox-header h3{
			float:left;
			height: 28px;
		}
		#libox-header-search{
			float:left;
			height: 28px;
		}
		#libox-header-user-controller{
			float:right;
			margin-top:6px;
		}
		.white{
			color: #FFF;
		}
		.clear{
			clear:both;
		}
		/** improve **/
		.btn,.btn:active,.btn:hover,.btn:focus,.btn.active {
			outline: none;
		}
		.ellipsis {
			overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
			display: inline-block;
		}
		.btn.loading {
			background-image: -webkit-gradient(linear, 0 0, 100% 100%,
        color-stop(.25, rgba(0, 0, 0, .10)),
        color-stop(.25, transparent),
        color-stop(.5, transparent),
        color-stop(.5, rgba(0, 0, 0, .10)),
        color-stop(.75, rgba(0, 0, 0, .10)),
        color-stop(.75, transparent),
        to(transparent));
			background-image:
        -moz-linear-gradient(-45deg,
				rgba(0, 0, 0, .10) 25%,
				transparent 25%,
				transparent 50%, rgba(0, 0, 0, .10) 50%,
				rgba(0, 0, 0, .10) 75%,
				transparent 75%, transparent
        );
			background-size: 50px 50px;
			-moz-background-size: 50px 50px;
			-webkit-background-size: 50px 50px;
			-webkit-animation: animate-stripes 2s linear infinite;
		}

		@-webkit-keyframes animate-stripes {
			from {
			background-position: 0 0;
    }
    to {
			background-position: -50px 0;
    }
		}​
	</style>
  <body>
		<div id="myModal" class="modal hide fade in" style="display:none;">
			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3>Modal Heading</h3>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<a href="javascript:void(0);" class="btn" data-dismiss="modal">取消</a>
				<a href="javascript:void(0);" class="btn btn-primary">确定</a>
			</div>
		</div>
		<div class="navbar" style="margin-bottom:0;position:fixed;width:100%;z-index:11;top:0;left:0">
			<div class="navbar-inner" style="webkit-border-radius: 0;-moz-border-radius: 0;border-radius: 0">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="#">Libox</a>
					<div class="nav-collapse">
						<ul class="nav">
							<!--<li class="active"><a href="/?module=backend">Home</a></li>-->
							<?php foreach ($modules as $m) { ?>
								<li>
									<a href="javascript:libox.load('/?module=backend&action=content&cat_id=<?php echo $m['cat_id']?>');"><?php echo $m['cat_name'];?></a>
								</li>
							<?php } ?>
							<li class="divider-vertical"></li>
						</ul>
						<form class="navbar-search pull-left" action="">
							<input type="text" class="search-query span3" placeholder="Search">
						</form>
						<ul class="nav pull-right">
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">系统 <b class="caret"></b></a>
								<ul class="dropdown-menu" style="min-width: 58px;">
									<li><a href="javascript:libox.load('/?module=backend&action=category');">分类</a></li>
									<li class="divider"></li>
									<li><a href="javascript:libox.load('/?module=backend&action=model');">模型</a></li>
									<li class="divider"></li>
									<li><a href="javascript:void(0);">基本</a></li>
									<li class="divider"></li>
									<li><a href="javascript:void(0);">备份</a></li>
									<li><a href="javascript:void(0);">还原</a></li>
								</ul>
							</li>
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user icon-white"></i> <b class="caret"></b></a>
								<ul class="dropdown-menu" style="min-width: 58px;">
									<li><a href="javascript:void(0);">改密</a></li>
									<li><a href="javascript:void(0);">注销</a></li>
								</ul>
							</li>
						</ul>
					</div><!-- /.nav-collapse -->
				</div>
			</div><!-- /navbar-inner -->
		</div>
		<ul class="breadcrumb" style="webkit-border-radius: 0;-moz-border-radius: 0;border-radius: 0;margin-bottom:0;position:fixed;width:100%;padding-left:0;padding-right:0;top:40px;left:0;z-index: 10">
			<li style="padding-left:10px;" id="libox-navigator">
			</li>
			<li class="pull-right btn-group" style="padding-right:10px">
				<button class="btn" id="prev-page" disabled><i class="icon-arrow-left"></i></button>
				<button class="btn" id="current-page" onclick="libox.load(null, 0);"><i class="icon-refresh"></i></button>
				<button class="btn" id="next-page" disabled><i class="icon-arrow-right"></i></button>
			</li>
			<li class="clear"></li>
		</ul>
		<div id="libox-main" style="background: white;">
