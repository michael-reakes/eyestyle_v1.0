<div class="pager">
	<div class="goto">
		Go to page:
		<form name="form_pager" method="get" action="<tag:goto_action />">
			<input name="p" type="text">
			<tag:goto_hidden_fields />
			<input name="" type="image" src="images/icon_go.gif" class="icon_btn">
		</form>
	</div>
	Page <tag:current_page /> of <tag:total_page /> &nbsp; (Total <tag:total_item />) &nbsp;
	<if:has_previous>
		<a href="<tag:previous_url />"><img src="images/icon_left_arrow_on.gif" alt="Previous" /> Previous</a>
	<else:has_previous>
		<span class="disabled"><img src="images/icon_left_arrow_off.gif" alt="Previous" /> Previous</span>
	</if:has_previous>
	| <tag:pages /> |
	<if:has_next>
		<a href="<tag:next_url />">Next <img src="images/icon_right_arrow_on.gif" alt="Next" /></a>
	<else:has_next>
		<span class="disabled">Next <img src="images/icon_right_arrow_off.gif" alt="Next" /></span>
	</if:has_next>
</div>