<ul>
<?php foreach ($result as $r){?>
<li>
	<?php foreach($r as $c){?>
	<a><?php echo $c;?></a>
	<?php }?>
</li>
<?php } ?>
</ul>