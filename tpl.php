<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>[<?php print $aResult['path'];?>]</title>
		<link rel="stylesheet" href="<?php $this->getRessourceLink('bstyle.css'); ?>" >
		<script type="text/javascript" src="<?php $this->getRessourceLink('bsorttable.js'); ?>"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	</head>

	<body>
		<div class="container">
			<h1 class="text-center">[ <?php print $aResult['path'];?> ]</h1>
			<p><?php if($aResult['thumb']){?><img width=140 src="<?php print $aResult['thumb'];?>"><?php }?></p>
			<p><a href="<?php print $aResult['uppath']['href'];?>">up (..)</a></p>
			<table class="table table-striped sortable">
				<thead>
					<tr>
						<th>-</th>
						<th>Filename</th>
						<th>Type</th>
						<th>Size <small>(bytes)</small></th>
						<th>Date Modified</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($aResult['list'] as $item){?>
					<tr>
						<td><?php if($item['thumb']){?><img width=40 src="<?php print $item['thumb'];?>"><?php }?></td>
						<td class="item-name"><a href="<?php print $item['href'];?>"><?php print $item['name'];?></a></td>
						<td class="item-stype"><?php print $item['stype'];?></a></td>
						<td class="item-size"><?php print number_format($item['size']);?></a></td>
						<td class="item-mtime"><?php print $item['mtime'];?></a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</body>
</html>