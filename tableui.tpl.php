<link rel="stylesheet" href="/sites/all/libraries/ext-4.1.1a/resources/css/ext-all.css">
<link rel="stylesheet" href="/sites/all/libraries/ext-4.1.1a/resources/css/ext-all-gray.css">
<?php 
$modulePrefix = arg(0);
if (property_exists($user,'data')){
 $fullscreen = $user->data['fullscreen'];
} else {
 $fullscreen = false;
}
?>
<div id="metadata"
 <?php if (austese_access('edit metadata', $project)): ?>
  data-editable="true"
 <?php endif; ?>
 <?php if ($fullscreen):?>
 data-fullscreen="<?php print $fullscreen; ?>"
 <?php endif; ?>
 data-moduleprefix="<?php print $modulePrefix; ?>"
 data-modulepath="<?php print drupal_get_path('module', 'collation'); ?>"
 data-baseurl="http://<?php print $_SERVER['SERVER_NAME']; ?>"
 <?php if ($project):?>
  data-project="<?php print $project; ?>"
 <?php endif; ?>
>
</div>
<div id="uiplaceholder"></div>
<script type="text/javascript">
var leftScrollPos,rightScrollPos;
var scrolledDiff;
var scrolledSpan;
function getOffsetTopByElem( elem )
{
	var offset = 0;
	while ( elem != null )
	{
		offset += elem.offsetTop;
		elem = elem.offsetParent;
	}
	return offset;
}
function getElementHeight( elem )
{
	if ( elem.height )
		return elem.height;
	else
		return elem.offsetHeight;
}

function getHeight( elem, inclBorder )
{
	var borderHeight = getBorderValue(elem,"border-top-width")
			+getBorderValue(elem,"border-bottom-width");
	if ( elem.clientHeight )
		return (inclBorder)?borderHeight+elem.clientHeight
			:elem.clientHeight;
	else
		return (inclBorder)?elem.offsetHeight
			:elem.offsetHeight-borderHeight;
}
function getOffsetTop( id )
{
	var elem = document.getElementById(id);
	return getOffsetTopForElem( elem );
}
function getOffsetTopForElem( elem )
{
	var offset = 0;
	while ( elem != null )
	{
		offset += elem.offsetTop;
		elem = elem.offsetParent;
	}
	return offset;
}
function synchroScroll(scrolledDiv,staticDiv)
{
	
	// 2. find the most central span in the scrolled div
	scrolledDiff = 4294967296;
	scrolledSpan = null;
	var scrolledDivTop = getOffsetTopByElem( scrolledDiv );
	var staticDivTop = getOffsetTopByElem( staticDiv );
	var centre = getElementHeight(scrolledDiv)/2
		+scrolledDiv.scrollTop;
	findSpanAtOffset( scrolledDiv, centre, scrolledDivTop );
	// 3. find the corresponding span on the other side
	if ( scrolledSpan != null )
	{
		var staticId = scrolledSpan.getAttribute("id");
		if ( staticId.charAt(0)=='a' )
			staticId = "d"+staticId.substring(1);
		else
			staticId = "a"+staticId.substring(1);
		var staticSpan = document.getElementById( staticId );
		if ( staticSpan != null )
		{
			// 4. compute relative topOffset of scrolledSpan
			var scrolledTopOffset = scrolledSpan.offsetTop
				-scrolledDivTop;
			// 5. compute relative topOffset of staticSpan
			var staticTopOffset = staticSpan.offsetTop-staticDivTop;
			// 6. scroll the static div level with scrolledSpan
			var top = staticTopOffset-getElementHeight(staticDiv)/2;
			if ( top < 0 )
				staticDiv.scrollTop = 0;
			else
				staticDiv.scrollTop = top;
		}
	}
}
function findSpanAtOffset( elem, pos, divOffset )
{
	if ( elem.nodeName == "SPAN"
		&& elem.getAttribute('id') != null )
	{
		var idAttr = elem.getAttribute('id');
		var spanRelOffset = elem.offsetTop-divOffset;
		if ( Math.abs(spanRelOffset-pos) < scrolledDiff )
		{
			scrolledSpan = elem;
			scrolledDiff = Math.abs(spanRelOffset-pos);
		}
	}
	else if ( elem.firstChild != null )
		findSpanAtOffset( elem.firstChild, pos, divOffset );
	if ( elem.nextSibling != null )
		findSpanAtOffset( elem.nextSibling, pos, divOffset );
}
</script>
