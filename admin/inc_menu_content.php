<?php $page_list = new dbo_list('page'); ?>
<div class="panel">
	<div class="title">Content Management</div>
	<div class="subtitle">Eyestyle Pages</div>
	<div class="list">
		<?php foreach($page_list->get_all() as $page): ?>
		<a href="content_content.php?id=<?=$page->page_id?>"><?=$page->title?></a>
		<?php endforeach; ?>
	</div>
	
</div>
