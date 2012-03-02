<?php
	if (!isset($_GET['filename'])){
		exit;	
	}
	$filename = $_GET['filename'];
?>
<html>
<head>
<script language="Javascript" type="text/javascript">
	function createRequestObject() {
		var ro;
		var browser = navigator.appName;
		if(browser == "Microsoft Internet Explorer"){
			ro = new ActiveXObject("Microsoft.XMLHTTP");
		}else{
			ro = new XMLHttpRequest();
		}
		return ro;
	}
	var http = createRequestObject();
	function delete_file() {
		http.open('get','actions/delete.php?filename=<?=$filename?>');
		http.onreadystatechange = handleResponse;
		http.send(null);
	}
	function handleResponse() {
		if(http.readyState == 4){
			var response = http.responseText;
			if (response == 'true'){
				window.opener.top.location.reload();
				document.getElementById('message').innerHTML='Delete succes. <input type="button" value="Close" onclick="javascript:window.close()" />';
				window.close();
			}else{
				document.getElementById('information').innerHTML = "Delete is uncessful "+response;
				document.getElementById('message').innerHTML='Delete unsuccesful. <input type="button" value="Close" onclick="javascript:window.close()" />';
			}
		}
	}
</script></head>

</head>
<body bgcolor="#F1F1E3" style="font-size:12px">
<div id="information" name="information" style="background-color:yellow"></div>
<div id="message" name="message">
	Are you sure you want to delete the following file: <br />
	<?=$filename?> <br />
	<input type="button" value="yes" onclick="javascript:delete_file()"/>
	<input type="button" value="no" onclick="javascript:window.close()" />
</div>
</body>
</html>