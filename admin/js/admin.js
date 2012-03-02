function toggleVisibility( targetID )
{
	if (document.getElementById(targetID).style.display == "none")
	{
		document.getElementById(targetID).style.display = "block";
	}
	else
	{
		document.getElementById(targetID).style.display = "none";
	}
}

function setVisible( targetID, visible )
{
	if (visible) {
		document.getElementById(targetID).style.display = "block";
	} else {
		document.getElementById(targetID).style.display = "none";
	}
}

function checkAll( formName, selfName, elementName)
{
	var i;
	var self = document.forms[formName][selfName];
	var elements = document.forms[formName][elementName];

	if (elements != null) {
		if (self.checked) {
			self.checked = true;
			if (elements.length > 1) {
				for (i=0; i<elements.length; i++) {
					elements[i].checked = true;
				}
			} else {
				elements.checked = true;
			}
		} else {
			self.checked = false;
			if (elements.length > 1) {
				for (i=0; i<elements.length; i++) {
					elements[i].checked = false;
				}
			} else {
				elements.checked = false;
			}
		}
	}
}

function checkAllTicked(formName, selfName, checkAllName) {
	var checkbox = document.forms[formName][selfName];
	var checkAll = document.forms[formName][checkAllName];
	var len = checkbox.length;
	var all_checked = true;

	if (len > 1) {
		for (i=0;i<len;i++) {
			if (!checkbox[i].checked) {
				all_checked = false;
			}
		}
	} else {
		if (!checkbox.checked) {
			all_checked = false;
		}
	}

	if (all_checked) {
		checkAll.checked = true;
	} else {
		checkAll.checked = false;
	}
}

function toggleEnabled( formName, enabled ) {
	var a = toggleEnabled.arguments;
	for (i=2; i<a.length; i++) {
		if (!enabled) {
			document.forms[formName][a[i]].value = "";
		}
		document.forms[formName][a[i]].disabled = !enabled;
	}
}

function toggleEnabledCheckbox( formName, enabled ) {
	var a = toggleEnabledCheckbox.arguments;
	for (i=2; i<a.length; i++) {
		if (!enabled) {
			document.forms[formName][a[i]].checked = false;
		}
		document.forms[formName][a[i]].disabled = !enabled;
	}
}

function getCategoryId(targetID, id) {
	var targetField = document.getElementById(targetID);
	if (targetField != null) {
		targetField.value = id;
	}
}
