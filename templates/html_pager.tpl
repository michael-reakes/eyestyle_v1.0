<ul class="pages">
	<if:has_previous>
		<li class="prev"><a href="<tag:previous_url />"><img src="<tag:ROOT />images/icons/arrow_left.gif" alt="&lt;" width="6" height="12" /></a></li>
	<else:has_previous>
		<li class="prev"><span class="disabled"><img src="<tag:ROOT />images/icons/arrow_left.gif" alt="&lt;" width="6" height="12" /></span></li>
	</if:has_previous>
	&nbsp;&nbsp; <tag:pages /> &nbsp;&nbsp;
	<if:has_next>
		<li class="next"><a href="<tag:next_url />"><img src="<tag:ROOT />images/icons/arrow_right.gif" alt="&gt;" width="6" height="12" /></a></li>
	<else:has_next>
		<li class="next"><span class="disabled"><img src="<tag:ROOT />images/icons/arrow_right.gif" alt="&gt;" width="6" height="12" /></span></li>
	</if:has_next>
</ul>
