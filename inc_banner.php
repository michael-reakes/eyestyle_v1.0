<div id="banner">
	<script language="JavaScript1.2" type="text/javascript">

	/***********************************************
	* Fade-in image slideshow script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
	* This notice MUST stay intact for legal use
	* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
	***********************************************/
	
	var slideshow_width='775px' //SET IMAGE WIDTH
	var slideshow_height='200px' //SET IMAGE HEIGHT
	var pause=5000 //SET PAUSE BETWEEN SLIDE (3000=3 seconds)
	
	var fadeimages=new Array()
	//SET 1) IMAGE PATHS, 2) optional link, 3), optional link target:
	<?php
		$counter = 0;
		if ($_SESSION['gender'] == 'men') {
			for ($i=1; $i<=8; $i++) {
				$feature_product = new dbo('feature_products',$i);
				if (!empty($feature_product->banner)) {
					print 'fadeimages['.$counter.']=["'.$_ROOT.$feature_product->banner.'","'.url::linkProduct($feature_product->product_id).'",""];';
				}
				$counter++;
			}
		} else {
			for ($i=9; $i<=16; $i++) {
				$feature_product = new dbo('feature_products',$i);
				if (!empty($feature_product->banner)) {
					print 'fadeimages['.$counter.']=["'.$_ROOT.$feature_product->banner.'","'.url::linkProduct($feature_product->product_id).'",""];';
				}
				$counter++;
			}
		}
	?>
		
	////NO need to edit beyond here/////////////
	
	var preloadedimages=new Array();
	for (p=0;p<fadeimages.length;p++){
	preloadedimages[p]=new Image();
	preloadedimages[p].src=fadeimages[p] != null ? fadeimages[p][0] : "";
	}
	
	var ie4=document.all
	var dom=document.getElementById
	
	if (ie4||dom)
	document.write('<div style="position:relative;z-index:0;width:'+slideshow_width+';height:'+slideshow_height+';overflow:hidden"><div id="canvas0" style="position:absolute;width:'+slideshow_width+';height:'+slideshow_height+';top:0;left:0;filter:alpha(opacity=10);opacity:10"></div><div id="canvas1" style="position:absolute;width:'+slideshow_width+';height:'+slideshow_height+';top:0;left:0;filter:alpha(opacity=10);opacity:10;visibility: hidden"></div></div>')
	else
	document.write('<img name="defaultslide" src="'+fadeimages[0][0]+'">')
	
	var curpos=10
	var degree=10
	var curcanvas="canvas0"
	var curimageindex=0
	var nextimageindex=1
	
	function fadepic(){
	if (curpos<100){
	curpos+=5
	if (tempobj.filters)
	tempobj.filters.alpha.opacity=curpos
	else if (tempobj.style.opacity)
	tempobj.style.opacity=curpos/101
	}
	else{
	clearInterval(dropslide)
	nextcanvas=(curcanvas=="canvas0")? "canvas0" : "canvas1"
	tempobj=ie4? eval("document.all."+nextcanvas) : document.getElementById(nextcanvas)
	tempobj.innerHTML=insertimage(nextimageindex)
	nextimageindex=(nextimageindex<fadeimages.length-1)? nextimageindex+1 : 0
	var tempobj2=ie4? eval("document.all."+nextcanvas) : document.getElementById(nextcanvas)
	tempobj2.style.visibility="hidden"
	setTimeout("rotateimage()",pause)
	}
	}
	
	function insertimage(i){
	var tempcontainer=fadeimages[i][1]!=""? '<a href="'+fadeimages[i][1]+'" target="'+fadeimages[i][2]+'">' : ""
	tempcontainer+='<img src="'+fadeimages[i][0]+'" border="0">'
	tempcontainer=fadeimages[i][1]!=""? tempcontainer+'</a>' : tempcontainer
	return tempcontainer
	}
	
	function rotateimage(){
	if (ie4||dom){
	resetit(curcanvas)
	var crossobj=tempobj=ie4? eval("document.all."+curcanvas) : document.getElementById(curcanvas)
	crossobj.style.zIndex++
	tempobj.style.visibility="visible"
	var temp='setInterval("fadepic()",50)'
	dropslide=eval(temp)
	curcanvas=(curcanvas=="canvas0")? "canvas1" : "canvas0"
	}
	else
	document.images.defaultslide.src=fadeimages[curimageindex][0]
	curimageindex=(curimageindex<fadeimages.length-1)? curimageindex+1 : 0
	}
	
	function resetit(what){
	curpos=5
	var crossobj=ie4? eval("document.all."+what) : document.getElementById(what)
	if (crossobj.filters)
	crossobj.filters.alpha.opacity=curpos
	else if (crossobj.style.opacity)
	crossobj.style.opacity=curpos/101
	}
	
	function startit(){
	var crossobj=ie4? eval("document.all."+curcanvas) : document.getElementById(curcanvas)
	crossobj.innerHTML=insertimage(curimageindex)
	rotateimage()
	}
	
	if (ie4||dom)
	window.onload=startit
	else
	setInterval("rotateimage()",pause)
	</script>
</div>