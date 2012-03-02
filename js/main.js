function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function mark(face,field_color,text_color){
	if (document.documentElement){//if browser is IE5+ or NS6+
		face.style.backgroundColor=field_color;
		face.style.color=text_color;
	}
}

function openWin(theURL,winName,features) {
	var newwindow = window.open(theURL,winName,features);
	if (window.focus) { newwindow.focus(); }
}

function toggleVisibility(divName) {
	var div = document.getElementById(divName);
	if (div.style.display == "none") {
		div.style.display = "block";
	} else {
		div.style.display = "none";
	}
}

var currentThumbnail = "";
function showProductImage(root, imagePath, thumbnailName) {
	var mainImage = document.getElementById("img_main_product");
	mainImage.src = root + imagePath;
	if (currentThumbnail != "") {
		currentThumbnailCell = document.getElementById(currentThumbnail);
		if (currentThumbnailCell != undefined) {
			currentThumbnailCell.className = "image";	
		}
	}
	
	var thumbnailCell = document.getElementById(thumbnailName + "_cell");
	if (thumbnailCell != undefined) {
		thumbnailCell.className = "image";
	}	
	currentThumbnail = thumbnailName + "_cell";
}

function showDesignersList(gender) {
	var womensList = document.getElementById('womens_list');
	var mensList = document.getElementById('mens_list');
	switch (gender) {
		case 1: //show mens
			womensList.style.display = 'none';
			if (mensList.style.display == 'block') {
				mensList.style.display = 'none';
			}
			else {
				mensList.style.display = 'block';
			}
			break;
		case 2: //show womens
			mensList.style.display = 'none';
			if (womensList.style.display == 'block') {
				womensList.style.display = 'none';
			}
			else {
				womensList.style.display = 'block';
			}
			break;
	}
}


function showCCDetails(showFlag) {
	var ccDetails = document.getElementById('ccDetails');
	ccDetails.style.display = showFlag ? 'block' : 'none';
	var generalButton = document.getElementById('generalButton');
	generalButton.style.display = 'block';
	var paypalButton = document.getElementById('paypalButton');
	paypalButton.style.display = 'none';
}

// added by victor
/**********/

function showPayPal() {
	var payment_cc = document.getElementById('payment-cc');
	payment_cc.style.display = 'none';
	var generalButton = document.getElementById('generalButton');
	generalButton.style.display = 'none';
	var paypalButton = document.getElementById('paypalButton');
	paypalButton.style.display = 'block';
}

/**********/

function openWin(theURL,winName,features) {
	newwindow = window.open(theURL,winName,features);
	if (window.focus) { newwindow.focus(); }	
}

function goToBrand(url) {
	window.location = url;
}
