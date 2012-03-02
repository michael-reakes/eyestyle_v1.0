$(document).ready(function() {
	// Dropdown
	$("#nav li:has(.dropdown)").each(function() {
		$(this).hover(
			function() { $(this).addClass("hover"); },
			function() { $(this).removeClass("hover"); }
		);
	});

	// Payment method
	$("input[name=payment_method]").bind("click", onSelectPaymentMethod);

	function onSelectPaymentMethod() {
		var val = $("input[name=payment_method]:checked").val();
		var target = $("#payment-cc");
		(val == "cc") ? target.fadeIn() : target.hide();
	}

	onSelectPaymentMethod();

	// Category product image rollover
	$(".productlist li img").each(function() {
		var srcOver = $(this).attr("rel");
		var src = $(this).attr("src");
		if (srcOver != '')
		{
			$(this).hover(
				function() { $(this).attr("src", srcOver); },
				function() { $(this).attr("src", src); }
			);
		}
	});
});