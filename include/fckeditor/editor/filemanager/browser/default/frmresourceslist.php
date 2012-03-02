<?
	require_once('../../../../inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!--
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2006 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: frmresourceslist.html
 * 	This page shows all resources available in a folder in the File Browser.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="browser.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript">

var oListManager = new Object() ;

oListManager.Clear = function()
{
	document.body.innerHTML = '' ;
}

oListManager.GetFolderRowHtml = function( folderName, folderPath )
{
	// Build the link to view the folder.
	var sLink = '<a href="#" onclick="OpenFolder(\'' + folderPath.replace( /'/g, '\\\'') + '\');return false;">' ;

	return '<tr>' +
			'<td width="16">' +
				sLink +
				'<img alt="" src="images/Folder.gif" width="16" height="16" border="0"></a>' +
			'</td><td nowrap colspan="2">&nbsp;' +
				sLink + 
				folderName + 
				'</a>' +
		'</td></tr>' ;
}

oListManager.GetFileRowHtml = function( fileName, fileUrl, fileSize, deleteLink )
{
	// Build the link to view the folder.
	var sLink = '<a href="#" onclick="OpenFile(\'' + fileUrl.replace( /'/g, '\\\'') + '\');return false;">' ;

	// Get the file icon.
	var sIcon = oIcons.GetIcon( fileName ) ;

	return '<tr>' +
			'<td width="16">' +
				sLink + 
				'<img alt="" src="images/icons/' + sIcon + '.gif" width="16" height="16" border="0"></a>' +
			'</td><td>&nbsp;' +
				sLink + 
				fileName + 
				'</a>' +
			'</td><td align="right" nowrap>&nbsp;' +
				fileSize + 
				' KB' +
			'</td><td align="right" nowrap>&nbsp;' +
				deleteLink + 
		'</td></tr>' ;
}

function OpenFolder( folderPath )
{
	// Load the resources list for this folder.
	window.parent.frames['frmFolders'].LoadFolders( folderPath ) ;
}

function OpenFile( fileUrl )
{
	/* Added by S3 Group */
	var filepath;
	var oEditor		= window.parent.oEditor ;
	var FCKConfig	= oEditor.FCKConfig ;
	var path = "<? echo $_FCK_SETTINGS['absolute_path']; ?>";
	
	if (FCKConfig.AbsolutePath){
		filepath = path+fileUrl;
	}else{
		filepath = fileUrl;
	}
	/***/

	window.top.opener.SetUrl( filepath ) ;
	window.top.close() ;
	window.top.opener.focus() ;
}

function LoadResources( resourceType, folderPath )
{
	oListManager.Clear() ;
	oConnector.ResourceType = resourceType ;
	oConnector.CurrentFolder = folderPath
	oConnector.SendCommand( 'GetFoldersAndFiles', null, GetFoldersAndFilesCallBack ) ;
}

function Refresh()
{
	LoadResources( oConnector.ResourceType, oConnector.CurrentFolder ) ;
}

function GetFoldersAndFilesCallBack( fckXml )
{
	if ( oConnector.CheckError( fckXml ) != 0 )
		return ;

//	var dTimer = new Date() ;

	// Get the current folder path.
	var oNode = fckXml.SelectSingleNode( 'Connector/CurrentFolder' ) ;
	var sCurrentFolderPath	= oNode.attributes.getNamedItem('path').value ;
	var sCurrentFolderUrl	= oNode.attributes.getNamedItem('url').value ;
	
	var sHTML = '<table id="tableFiles" cellspacing="1" cellpadding="0" width="100%" border="0">' ;


	// Add the Folders.	
	var oNodes = fckXml.SelectNodes( 'Connector/Folders/Folder' ) ;
	for ( var i = 0 ; i < oNodes.length ; i++ )
	{
		var sFolderName = oNodes[i].attributes.getNamedItem('name').value ;
		sHTML += oListManager.GetFolderRowHtml( sFolderName, sCurrentFolderPath + sFolderName + "/" ) ;
	}

	// Add the Files.	
	var oNodes = fckXml.SelectNodes( 'Connector/Files/File' ) ;
	for ( var i = 0 ; i < oNodes.length ; i++ )
	{
		var sFileName = oNodes[i].attributes.getNamedItem('name').value ;
		var sFileSize = oNodes[i].attributes.getNamedItem('size').value ;
		//var dLink = '<a href="#" title="Delete file" onclick="DeleteFile(\'' + escape(sFileName) + '\');return false;">delete</a>' ;
		//here we can do it using our own delete function or build a connector to do it
		var dLink = '<a href="javascript:openWin(\'confirm_delete.php?filename='+sCurrentFolderUrl+sFileName+'\',\'\',\'scrollbars=no,width=300,height=150\')">delete</a>';
		sHTML += oListManager.GetFileRowHtml( sFileName, sCurrentFolderUrl + sFileName, sFileSize, dLink) ;
	}

	sHTML += '</table>' ;

	document.body.innerHTML = sHTML ;

//	window.top.document.title = 'Finished processing in ' + ( ( ( new Date() ) - dTimer ) / 1000 ) + ' seconds' ;
}

window.onload = function()
{
	window.top.IsLoadedResourcesList = true ;
}

		function openWin(theURL,winName,features) {
			newwindow = window.open(theURL,winName,features);
			if (window.focus) { newwindow.focus(); }	
		}

	</script>
</head>
<body class="FileArea" bottommargin="10" leftmargin="10" topmargin="10" rightmargin="10">
</body>
</html>
