<?
require_once('inc_header.php');
?>
		<div id="content">
			<?=html_message::show()?>
			<?php
				$banners = array();
				$featurebanners_list = new dbo_list('preference', "where `name` LIKE 'featurebanner_image_%' AND `value` != ''");
				foreach($featurebanners_list->get_all() as $banner) {
					$pref = new dbo('preference');
					$url = $pref->load(str_replace('image', 'url', $banner->name)) ? $pref->value : '';
					$banners[] = array('image' => $banner->value, 'url' => $url);
				}
				if (count($banners)):
					shuffle($banners);
			?>
			<ul class="banners">
				<?php foreach($banners as $banner):
						if ($banner['url'] != ''): 
				?>
				<li><a href="<?=substr($banner['url'], 0, 7) == 'http://' ? $banner['url'] : 'http://'.$banner['url']?>"><img src="<?=$banner['image']?>" alt="<?=basename($banner['image'])?>" width="980" height="500" /></a></li>
				<?php	else: ?>
				<li><img src="<?=$banner['image']?>" alt="<?=basename($banner['image'])?>" width="980" height="500" /></li>
				<?php	endif;
					endforeach;
				?>
			</ul>
			<?php endif; ?>
			<script type="text/javascript">
				var Banner = function(target)
				{
					this.target = target;
					this.items = this.target.find("li");
					this.items.hide();
					this.numItems = this.items.length;
					this.nextItem();
				}
				Banner.prototype = 
				{
					target: null,
					items: null,
					current: 0,
					currentItem: null,
					numItems: 0,
					itemDelay: 5000,
					fadeSpeed: 2000,
					nextItem: function()
					{
						var controller = this;
						this.currentItem = this.items.filter(":eq(" + this.current + ")");
						this.currentItem.fadeIn(this.fadeSpeed, function() { controller.onItemShow(); });
					},
					onItemShow: function()
					{
						var controller = this;
						this.current++;
						if (this.current == this.numItems) this.current = 0;
						setTimeout(function() 
							{
								controller.currentItem.fadeOut(this.fadeSpeed);
								controller.nextItem(); 
							}, 
							this.itemDelay
						);
					}
				};
				$(document).ready(function() {
					$("ul.banners").each(function() { new Banner($(this)); });
				});
			</script>
		</div>
<?
require_once('inc_footer.php');
?>