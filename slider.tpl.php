<html><script id="tinyhippos-injected">if (window.top.require) { window.top.require("ripple/bootstrap").inject(window, document); }</script><head>
<style>
div#table { border-top: inset 3px lightgrey; width:450px;overflow-x: auto }
div#innertable { width:2000px }
div#prefs { border-top: inset 3px lightgrey; width: 450px; display: none }
div#text { overflow: auto; height: 500px }
div#centre { margin-left: auto; margin-right: auto; width: 450px; height:700px; border:0px }
span.deleted { color: red } span.added { color: blue } span.merged { -hrit-mergeid: id } a.parent { color: grey; -hrit-childid: href } a.child { color: grey; -hrit-parentid: href }h3.head {}
h3.head-italic { text-align: center; font-family: Times, serif; font-weight: bold; font-style: italic; font-size: 18px }
p.stage-italic { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; font-style: italic }
p.stage { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; }
span.speaker { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold }
br.l {}
div.sp { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px }
span.italics { font-style: italic }
span.bold { font-weight: bold; }
td {white-space: nowrap;padding:0;border:0;}
tr {border:0}
table {border:0}
td.siglum {font-weight: bold; padding-right: 3px; padding-left: 3px;}
td.siglumhidden { display: none; }
table {border-spacing: 0px 2px;}
table.inline:hover {cursor:pointer}
table.inline { display: inline-table; position: relative; }
tr.shown { display: table-row; }
tr.hidden { display: none; }
span.base { border-bottom: 1px dotted #ba0000;}
span.inserted { color: blue; position: relative }
span.left {position: relative;float:left; }
span.right {position: relative;float:right; }
span.inserted-left { position: relative;float: left; color: blue}
span.inserted-right { position: relative;float: right; color: blue}
</style>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript">
var centralDiff;
var centralSpan;
var oldCentralSpan;
function baseid( id )
{
	for ( var i=1;i<id.length;i++ )
	{
		if ( id.charAt(i)>'9'||id.charAt(i)<'0' )
			return id.substr(1,i-1);
	}
	return id.substr(1);
}
function findSpanAtOffset( obj, pos )
{
	if ( obj.is("span") && obj.attr('id') != undefined )
	{
		var spanRelOffset = obj.offset().top-$("#content").offset().top;
		if ( Math.abs(spanRelOffset-pos) < centralDiff )
		{
			centralSpan = obj;
			centralDiff = Math.abs(spanRelOffset-pos);
		}
	}
	else if ( obj.children().length>0 )
	{
		findSpanAtOffset( obj.children().first(), pos);
	}
	if ( obj.next().length>0 )
	{
		findSpanAtOffset( obj.next(), pos );
	}
}
function isText( str )
{
	for ( var i=0;i<str.length;i++ )
		if ( str.charAt(i)<'a'||str.charAt(i)>'z' )
			return false;
	return true;
}
function backgroundify( id, colour, prefix, elem )
{
	if ( id.length > 0 )
	{
		var baseId = prefix+id;
		$(elem+"[id^='"+baseId+"']").each(function(i)
		{
			var partId = prefix+baseid($(this).attr("id"));
			if ( partId.length == baseId.length )
				$(this).css("background-color",colour);
		});
	}
}
function calcMidpoint( obj, maxVScroll )
{
	var scrollPos = obj.scrollTop();
	return scrollPos+((obj.height()*scrollPos)/maxVScroll);
}
$(function() 
{
	var botmargin = ($("#centre").parent().outerHeight(true)-$("#centre").parent().outerHeight())/2;
	var cheight = $(window).height()-($("#centre").offset().top+botmargin);
	$("#centre").css("height",cheight+"px");
	$("#innertable").css("width",$("#apparatus").width()+"px");
	var rest = $("#table").outerHeight()+$("#buttons").outerHeight();
    var theight = $("#centre").height()-rest;
	$("#text").css("height",theight+"px");
	var midpoint = calcMidpoint($("#text"),$("#content").height()-$("#text").height());
	centralDiff = 4294967296;
	centralSpan = null;
	findSpanAtOffset( $("#text"), midpoint );
	if ( centralSpan != null )
	{
		var cid = baseid(centralSpan.attr("id"));
		backgroundify( cid, "pink", "v", "span" );
		backgroundify( cid, "pink", "t", "td" );
	}
	$("#text").scroll(function () 
	{ 
        centralDiff = 4294967296;
		oldCentralSpan = centralSpan;
	    centralSpan = null;
		var maxVScroll = $("#content").height()-$(this).height();
		var maxHScroll = $("#innertable").width()-$("#table").width();
		findSpanAtOffset( $("#text"), calcMidpoint($(this),maxVScroll) );
		if ( $(this).scrollTop()==0 )
		{
			$("#table").scrollLeft(0);
		}
		else if ( $(this).scrollTop()>=maxVScroll )
		{
			$("#table").scrollLeft(maxHScroll);
		}
		else
		{
			var tid = "t"+baseid(centralSpan.attr("id") );
			var left = -1;
			var right = -1;
			$("td[id^='"+tid+"']").each(function(i)
			{
				var actualTid = $(this).attr("id");
				if ( actualTid.length==tid.length
					||isText(actualTid.substr(tid.length)) ) 
				{
					var lpos = $(this).offset().left-$("#innertable").offset().left;
					right = lpos+$(this).width();
					if ( left == -1 )
						left = lpos;
				}
				// else its an invalid prefix
			});
			if ( left != -1 && right != -1 )
			{
				var pos = ((right+left)/2)-($("#table").width()/2);
				$("#table").scrollLeft(pos);
			}
		}
		if ( oldCentralSpan != null )
		{
			var oid = baseid(oldCentralSpan.attr("id"));
			backgroundify( oid, "white", "v", "span" );
			backgroundify( oid, "white", "t", "td" );
		}
		if ( centralSpan != null )
		{
			var cid = baseid(centralSpan.attr("id"));
			backgroundify( cid, "pink", "v", "span" );
			backgroundify( cid, "pink", "t", "td" );
		}
    });
});
</script>
</head>
<body>
<div id="centre" style="height: 540px;">
<div id="text" style="height: 393px;">
<div id="content">
<h3 class="head">THE TRAGEDIE OF<span id="v1" class="merged" style="background-color: rgb(255, 192, 203);"> K</span>ING<span id="v2" class="merged"> L</span>EAR.</h3>
<h3 class="head-italic">Actus Primus. Scaena Prima<span id="v3a" class="merged">.</span></h3><span id="v3b" class="merged">
</span><p class="stage-italic"><span id="v3c" class="merged">Enter Kent, Glo</span>uce<span id="v4" class="merged">ster, and </span>Edmon<span id="v5a" class="merged">d.</span></p><span id="v5b" class="merged">
</span><div class="sp"><span id="v5c" class="merged"><span class="speaker">Kent.</span>
I Thought the King had more affected the Duke of <span class="italics">Alb</span></span><span class="italics">an</span><span id="v6" class="merged"><span class="italics">y</span>, th</span>e<span id="v7" class="merged">n <span class="italics">Corn</span></span><span class="italics"><span id="v8" class="merged">w</span>a<span id="v9a" class="merged">ll.</span></span></div><span id="v9b" class="merged">
</span><div class="sp"><span class="speaker"><span id="v9c" class="merged">Glo</span>u</span><span id="v10" class="merged"><span class="speaker">.</span>
It did alwaye<span id="v11" class="merged">s seem</span>e so<span id="v12" class="merged"> to us</span>: B<span id="v13" class="merged">ut now in the division of the </span>K<span id="v14" class="merged">ingdom</span>e<span id="v15" class="merged">, it appear</span><span id="v16" class="merged">s not which of the Dukes he</span>e<span id="v17" class="merged"> val</span>ew<span id="v18" class="merged">es most, for </span><span id="v19" class="merged">qualities are so weigh</span>'<span id="v20" class="merged">d, that curiosit</span>y<span id="v21" class="merged"> in neither, can make choi</span>s<span id="v22" class="merged">e of eithers mo</span><span id="v23" class="merged">i</span>ty<span id="v24a" class="merged">.</span></span></div><span id="v24b" class="merged">
</span><div class="sp"><span id="v24c" class="merged"><span class="speaker">Kent.</span>
Is not this your </span>S<span id="v25" class="merged">on</span><span id="v26a" class="merged">, my Lord?</span></div><span id="v26b" class="merged">
</span><div class="sp"><span class="speaker"><span id="v26c" class="merged">Glo</span>u</span><span id="v27" class="merged"><span class="speaker">.</span>
His breeding</span><span id="v28" class="merged"> </span>S<span id="v29" class="merged">ir</span>,<span id="v30" class="merged"> hath b</span>i<span id="v31" class="merged">n</span><span id="v32" class="merged"> at my charge</span>.<span id="v33" class="merged"> I have so often blush</span>'d<span id="v34" class="merged"> to ack</span>n<span id="v35" class="merged">owledge him, that now I am braz'd to</span>o'<span id="v36a" class="merged">t.</span></div><span id="v36b" class="merged">
</span><div class="sp"><span id="v36c" class="merged"><span class="speaker">Kent.</span>
I cannot conceive you.</span></div><span id="v36d" class="merged">
</span><div class="sp"><span class="speaker"><span id="v36e" class="merged">Glo</span>u</span><span id="v37" class="merged"><span class="speaker">.</span>
Sir, this yo</span><span id="v38" class="merged">ng </span>F<span id="v39" class="merged">ellow</span>e<span id="v40" class="merged">s </span>m<span id="v41" class="merged">other </span>c<span id="v42" class="merged">ould</span>;<span id="v43" class="merged"> whereupon sh</span><span id="v44" class="merged">e grew round womb</span>'<span id="v45" class="merged">d, and had indeed</span>e<span id="v46" class="merged"> </span>(<span id="v47" class="merged">Sir</span>)<span id="v48" class="merged"> a </span>S<span id="v49" class="merged">on</span>ne<span id="v50" class="merged"> for her </span>C<span id="v51" class="merged">radle, e</span><span id="v52" class="merged">re she had a </span>h<span id="v53" class="merged">usband for her </span>b<span id="v54" class="merged">ed</span>. D<span id="v55" class="merged">o</span><span id="v56a" class="merged"> you smell a fault?</span></div><span id="v56b" class="merged">
</span><div class="sp"><span id="v56c" class="merged"><span class="speaker">Kent.</span>
I cannot wish the fault undone, the issue of it</span>,<span id="v57a" class="merged"> being so proper.</span></div><span id="v57b" class="merged">
</span><div class="sp"><span class="speaker"><span id="v57c" class="merged">Glo</span>u</span><span id="v58" class="merged"><span class="speaker">.</span>
But I have </span><span id="v59" class="merged">a </span>S<span id="v60" class="merged">on</span>ne, Sir,<span id="v61" class="merged"> by order of the Law, some </span>yeere<span id="v62" class="merged"> elder th</span>e<span id="v63" class="merged">n this</span>;<span id="v64" class="merged"> who</span>,<span id="v65" class="merged"> yet is no de</span>e<span id="v66" class="merged">rer in my account, tho</span>u<span id="v67" class="merged">gh this </span>K<span id="v68" class="merged">nave came som</span>thing<span id="v69" class="merged"> </span>f<span id="v70" class="merged">awc</span>i<span id="v71" class="merged">ly to the </span>w<span id="v72" class="merged">orld before he</span><span id="v73" class="merged"> was sent for</span>:<span id="v74" class="merged"> yet was his </span>M<span id="v75" class="merged">other fa</span>y<span id="v76" class="merged">r</span>e<span id="v77" class="merged">, there was good sport at his making, </span>and<span id="v78" class="merged"> the </span><span id="v79" class="merged">hor</span><span id="v80" class="merged">son must be acknowledged</span>. Doe<span id="v81" class="merged"> you know this </span>N<span id="v82" class="merged">ob</span>le Gent<span id="v83a" class="merged">leman, <span class="italics">Edmond</span>?</span></div><span id="v83b" class="merged">
</span><div class="sp"><span class="speaker">Edm</span><span id="v84" class="merged"><span class="speaker">.</span>
No</span>,<span id="v85a" class="merged"> my Lord.</span></div><span id="v85b" class="merged">
</span><div class="sp"><span class="speaker"><span id="v85c" class="merged">Glo</span>u</span><span id="v86" class="merged"><span class="speaker">.</span>
My Lord of Kent</span>:
R<span id="v87" class="merged">emember him h</span><span id="v88" class="merged">e</span>e<span id="v89" class="merged">reafter, as my </span>H<span id="v90" class="merged">onourable </span>F<span id="v91" class="merged">riend</span>.</div>
<div class="sp"><span class="speaker">Edm</span><span id="v92a" class="merged"><span class="speaker">.</span>
My services to your Lordship.</span></div><span id="v92b" class="merged">
</span><div class="sp"><span id="v92c" class="merged"><span class="speaker">Kent.</span>
I must love you, and sue to know you better.</span></div><span id="v92d" class="merged">
</span><div class="sp"><span class="speaker">Edm</span><span id="v93a" class="merged"><span class="speaker">.</span>
Sir, I shall study deserving.</span></div><span id="v93b" class="merged">
</span><div class="sp"><span class="speaker"><span id="v93c" class="merged">Glo</span>u</span><span id="v94" class="merged"><span class="speaker">.</span>
H</span><span id="v95" class="merged">e hath b</span>i<span id="v96" class="merged">n</span><span id="v97" class="merged"> out nine year</span>e<span id="v98" class="merged">s, and away h</span>e<span id="v99" class="merged"> shall again</span>e. T<span id="v100" class="merged">he King is co</span>m<span id="v101a" class="merged">ming.</span></div><span id="v101b" class="merged">
</span><p class="stage-italic"><span id="v101c" class="merged">S</span><span id="v102" class="merged">ennet</span>.<span id="v103" class="merged"> Enter </span>K<span id="v104" class="merged">ing</span><span id="v105" class="merged"> Lear,</span><span id="v106" class="merged"> Cornwall, </span>Albany,<span id="v107" class="merged"> Gon</span>e<span id="v108" class="merged">rill, Regan, Cordelia, </span>and attendant<span id="v109a" class="merged">s.</span></p><span id="v109b" class="merged">
</span><div class="sp"><span id="v109c" class="merged"><span class="speaker">Lear.</span>
Attend the Lords of France </span>&amp;<span id="v110a" class="merged"> Burgundy, Gloster.</span><br></div><span id="v110b" class="merged">
</span><div class="sp"><span class="speaker"><span id="v110c" class="merged">Glo</span>u</span><span id="v111a" class="merged"><span class="speaker">.</span>
</span><span id="v111b" class="merged">I shall, my L</span>ord.<br></div><p class="stage-italic">Exit<span id="v112a" class="merged">.</span></p><span id="v112b" class="merged">
</span><div class="sp"><span id="v112c" class="merged"><span class="speaker">Lear.</span>
Mean</span>e<span id="v113" class="merged"> time we </span>sha<span id="v114" class="merged">l</span><span id="v115" class="merged"> express</span>e<span id="v116" class="merged"> our darker purpose</span><span id="v117a" class="merged">.</span><br><span id="v117b" class="merged">
</span>Give me t<span id="v118" class="merged">he </span>M<span id="v119" class="merged">ap there</span>. K<span id="v120" class="merged">now</span>, that<span id="v121a" class="merged"> we have divided</span><br><span id="v121b" class="merged">
</span><span id="v121c" class="merged">In three</span><span id="v122" class="merged"> our </span>K<span id="v123" class="merged">ingdom</span>e<span id="v124" class="merged">: and </span>'<span id="v125" class="merged">tis our f</span>a<span id="v126a" class="merged">st intent,</span><br><span id="v126b" class="merged">
</span><span id="v126c" class="merged">To shake all </span>C<span id="v127" class="merged">ares and </span>B<span id="v128" class="merged">usines</span>se<span id="v129" class="merged"> from our </span>Ag<span id="v130a" class="merged">e,</span><br><span id="v130b" class="merged">
</span><span id="v130c" class="merged">Conf</span>err<span id="v131" class="merged">ing them on yo</span><span id="v132" class="merged">nger </span>strengths, while we<br>
Unburthen'd crawle toward d<span id="v133" class="merged">ea</span>th. Our son of <span class="italics">Cornwal,</span><br>
And you our no l<span id="v134" class="merged">es</span>se loving Sonne of <span class="italics">Albany<span id="v135a" class="merged">,</span></span><br><span id="v135b" class="merged">
</span>We have this houre a constant will to publish<br>
Our daughters severall Dowers, that future strife<br>
May be prevented now. <span id="v136" class="merged">The</span><span id="v137" class="merged"> Prince</span>s<span id="v138" class="merged">, <span class="italics">France</span> </span>&amp;<span id="v139" class="merged"> <span class="italics">Burgundy</span></span><span class="italics">,</span><br><span id="v140a" class="merged">
</span><span id="v140b" class="merged">Great </span>Ri<span id="v141" class="merged">vals in our yonge</span>st d<span id="v142" class="merged">aughter</span><span id="v143" class="merged">s </span>l<span id="v144a" class="merged">ove,</span><br><span id="v144b" class="merged">
</span><span id="v144c" class="merged">Long in our Court</span>,<span id="v145" class="merged"> have made their amorous sojourn</span>e<span id="v146a" class="merged">,</span><br><span id="v146b" class="merged">
</span><span id="v146c" class="merged">And h</span>e<span id="v147" class="merged">ere are to be answer</span>'d. T<span id="v148" class="merged">ell me my </span>d<span id="v149" class="merged">aughters</span><br>
(Since now we will divest us both of Rule,<br>
Interest of Territory, Cares of State)<br><span id="v150a" class="merged">
Which of you shall we say doth love us most,<br>
</span><span id="v150b" class="merged">That we, our largest bount</span>ie<span id="v151" class="merged"> may extend</span><br><span id="v152a" class="merged">
</span><span id="v152b" class="merged">Where </span>Nature<span id="v153" class="merged"> doth </span>with meri<span id="v154" class="merged">t challenge</span>. <span class="italics"><span id="v155" class="merged">Gon</span>e<span id="v156" class="merged">rill</span>,</span><br>
O<span id="v157" class="merged">ur eldest born</span>e<span id="v158" class="merged">, speake first</span>.<br></div><span id="v159a" class="merged">
</span><div class="sp"><span id="v159b" class="merged"><span class="speaker">Gon.</span>
</span><span id="v159c" class="merged">Sir, I love you more th</span>e<span id="v160" class="merged">n word can w</span>ei<span id="v161a" class="merged">ld the matter,</span><br><span id="v161b" class="merged">
</span><span id="v161c" class="merged">De</span>e<span id="v162" class="merged">rer then </span>e<span id="v163" class="merged">ye-sight, space, and libert</span>ie<span id="v164a" class="merged">,</span><br><span id="v164b" class="merged">
</span><span id="v164c" class="merged">Beyond what can be val</span>ew<span id="v165" class="merged">ed</span>,<span id="v166a" class="merged"> rich or rare,</span><br><span id="v166b" class="merged">
</span><span id="v166c" class="merged">No less</span>e<span id="v167" class="merged"> th</span>e<span id="v168" class="merged">n life</span>,<span id="v169" class="merged"> with grace, health, beauty, hono</span><span id="v170" class="merged">r</span>:<br><span id="v171a" class="merged">
</span><span id="v171b" class="merged">As much a</span>s<span id="v172" class="merged"> </span>C<span id="v173" class="merged">hild</span>e<span id="v174" class="merged"> e</span><span id="v175" class="merged">re lov</span>'<span id="v176" class="merged">d, or </span>F<span id="v177" class="merged">ather f</span>ou<span id="v178" class="merged">nd</span>.<br><span id="v179a" class="merged">
</span><span id="v179b" class="merged">A love that makes breath poor</span>e<span id="v180a" class="merged">, and speech unable,</span><br><span id="v180b" class="merged">
Beyond all manner of so much I love you.<br></span></div><span id="v180b" class="merged">
</span><div class="sp"><span id="v180c" class="merged"><span class="speaker">Cor.</span>
What shall <span class="italics">Cordelia</span> </span>speake? L<span id="v181" class="merged">ove</span>,<span id="v182a" class="merged"> and be silent.</span><br></div><span id="v182b" class="merged">
</span><div class="sp"><span id="v182c" class="merged"><span class="speaker">Lear.</span>
Of all these bounds</span><span id="v183" class="merged"> even from this </span>L<span id="v184" class="merged">ine</span>,<span id="v185a" class="merged"> to this,</span><br><span id="v185b" class="merged">
</span><span id="v185c" class="merged">With shad</span>owie<span id="v186" class="merged"> </span>F<span id="v187" class="merged">orrest</span>s, and with Champains rich'd <br>
With plenteous River<span id="v188" class="merged">s, and wide-skirted Mead</span>e<span id="v189a" class="merged">s</span><br><span id="v189b" class="merged">
</span><span id="v189c" class="merged">We make th</span>e<span id="v190" class="merged">e</span><span id="v191" class="merged"> Lady</span>. T<span id="v192" class="merged">o thine and <span class="italics">Alban</span></span><span class="italics">ie</span><span id="v193" class="merged"><span class="italics">s</span> </span>i<span id="v194" class="merged">ssue</span>s<br><span id="v195a" class="merged">
</span><span id="v195b" class="merged">Be this perpetua</span>l<span id="v196" class="merged">l</span>. W<span id="v197" class="merged">hat sa</span>y<span id="v198" class="merged">es our second </span>D<span id="v199a" class="merged">aughter?</span><br><span id="v199b" class="merged">
</span><span id="v199c" class="merged">Our de</span>e<span id="v200" class="merged">rest <span class="italics">Regan,</span> wife </span>of<span id="v201" class="merged"> <span class="italics">Cornwall</span></span>?<br></div><span id="v202a" class="merged">
</span><div class="sp"><span id="v202b" class="merged"><span class="speaker">Reg.</span>
</span><span id="v203" class="merged">I am made of that self</span>e<span id="v204" class="merged">-</span><span id="v205" class="merged">met</span>tle as<span id="v206" class="merged"> my </span>S<span id="v207" class="merged">ister</span><span id="v208a" class="merged">,</span><br><span id="v208b" class="merged">
</span><span id="v208c" class="merged">And prize me at her worth</span>. I<span id="v209a" class="merged">n my true heart,</span><br><span id="v209b" class="merged">
</span><span id="v209c" class="merged">I find she names my very deed</span>e<span id="v210" class="merged"> of love</span>:<br>
O<span id="v211" class="merged">n</span>e<span id="v212" class="merged">ly she</span><span id="v213" class="merged"> c</span>o<span id="v214" class="merged">me</span>s too<span id="v215" class="merged"> short,</span> t<span id="v216" class="merged">hat I profess</span>e<br>
M<span id="v217" class="merged">y self</span>e<span id="v218" class="merged"> an </span>e<span id="v219a" class="merged">nemy to all other joyes,</span><br><span id="v219b" class="merged">
Which the most precious square of sense professes,<br>
And find</span>e<span id="v220" class="merged"> I am alone felicitate</span><br>
I<span id="v221" class="merged">n your de</span>ere<span id="v222" class="merged"> </span>H<span id="v223" class="merged">ighne</span>s<span id="v224" class="merged">s</span>e<span id="v225a" class="merged"> love.</span><br></div><span id="v225b" class="merged">
</span><div class="sp"><span id="v225c" class="merged"><span class="speaker">Cor</span></span><span id="v226a" class="merged"><span class="speaker">.</span>
</span><span id="v226b" class="merged">Then poor</span>e<span id="v227" class="merged"> <span class="italics">Cord</span></span><span class="italics">elia</span>,<br>
And<span id="v228" class="merged"> yet not so, since I am sure</span> m<span id="v229" class="merged">y love's</span><br>
M<span id="v230" class="merged">ore </span>pond<span id="v231" class="merged">er</span>ous<span id="v232" class="merged"> th</span>e<span id="v233a" class="merged">n my tongue.</span><br></div><span id="v233b" class="merged">
</span><div class="sp"><span id="v233c" class="merged"><span class="speaker">Lear.</span>
To thee</span>,<span id="v234" class="merged"> and thine hereditar</span>ie<span id="v235" class="merged"> ever</span>,<br><span id="v236a" class="merged">
</span><span id="v236b" class="merged">Remain</span>e<span id="v237" class="merged"> this ample third of our fair</span>e<span id="v238" class="merged"> </span>K<span id="v239" class="merged">ingdom</span>e<span id="v240a" class="merged">,</span><br><span id="v240b" class="merged">
</span><span id="v240c" class="merged">No less</span>e<span id="v241" class="merged"> in space, validit</span>ie<span id="v242" class="merged">, and pleasure</span><br><span id="v243a" class="merged">
</span><span id="v243b" class="merged">Th</span>e<span id="v244" class="merged">n that conf</span>err<span id="v245" class="merged">'d on <span class="italics">Gon</span></span><span class="italics">e<span id="v246" class="merged">rill</span>.</span> N<span id="v247" class="merged">ow our </span>J<span id="v248a" class="merged">oy,</span><br><span id="v248b" class="merged">
</span><span id="v248c" class="merged">Although </span>our<span id="v249" class="merged"> last</span> and<span id="v250" class="merged"> least</span>; to whose y<span id="v251" class="merged">o</span>ng<span id="v252a" class="merged"> love,</span><br><span id="v252b" class="merged">
</span>The Vines of France, and Milke of Burgundie,<br>
Strive to be interest. <span id="v253" class="merged">What can you say, to </span>draw<br>
A<span id="v254" class="merged"> third, more op</span>i<span id="v255" class="merged">lent</span> t<span id="v256" class="merged">hen your Sisters?</span> Speake.<br></div><span id="v257a" class="merged">
</span><div class="sp"><span class="speaker"><span id="v257b" class="merged">Cor</span></span><span id="v258a" class="merged"><span class="speaker">.</span>
</span><span id="v258b" class="merged">Nothing</span><span id="v259a" class="merged"> my Lord.</span><br></div><span id="v259b" class="merged">
</span><div class="sp"><span id="v259c" class="merged"><span class="speaker">Lear.</span>
</span>Nothing?<br></div>

<div class="sp"><span class="speaker">Cor.</span>
Nothing.<br></div>
<div class="sp"><span class="speaker">Lear.</span>
N<span id="v260" class="merged">othing </span>will<span id="v261" class="merged"> come of nothing, speak</span>e<span id="v262" class="merged"> again</span>e<span id="v263a" class="merged">.</span><br></div><span id="v263b" class="merged">
</span><div class="sp"><span id="v263c" class="merged"><span class="speaker">Cor</span></span><span id="v264a" class="merged"><span class="speaker">.</span>
</span><span id="v264b" class="merged">Unhapp</span>ie<span id="v265" class="merged"> that I am, I cannot h</span>e<span id="v266" class="merged">ave</span><br>
M<span id="v267" class="merged">y heart into my mouth</span>.<span id="v268" class="merged"> I love your Majesty</span><br>
A<span id="v269" class="merged">ccording to my bond, no more nor less</span>e<span id="v270a" class="merged">.</span><br></div><span id="v270b" class="merged">
</span><div class="sp"><span id="v270c" class="merged"><span class="speaker">Lear.</span></span>How, how <span class="italics">Cordelia</span>? M<span id="v271a" class="merged">end your speech a little,</span><br><span id="v271b" class="merged">
</span>Lea<span id="v272" class="merged">st you may mar</span>re<span id="v273" class="merged"> your </span>F<span id="v274a" class="merged">ortunes.</span><br></div><span id="v274b" class="merged">
</span><div class="sp"><span id="v274c" class="merged"><span class="speaker">Cor</span></span><span id="v275a" class="merged"><span class="speaker">.</span>
</span><span id="v275b" class="merged">Good</span><span id="v276" class="merged"> my Lord</span>.<br><span id="v277a" class="merged">
</span><span id="v277b" class="merged">You have begot me, bred me, lov</span>'<span id="v278" class="merged">d me</span>.<br><span id="v279a" class="merged">
</span><span id="v279b" class="merged">I return</span><span id="v280" class="merged"> those duties back</span>e<span id="v281a" class="merged"> as are right fit,</span><br><span id="v281b" class="merged">
</span><span id="v281c" class="merged">Obey you, </span>L<span id="v282" class="merged">ove you, and most honour you</span>.<br><span id="v283a" class="merged">
</span><span id="v283b" class="merged">Why have my </span>S<span id="v284" class="merged">isters </span>H<span id="v285" class="merged">usbands</span><span id="v286" class="merged"> if they say</span><br>
T<span id="v287" class="merged">hey love you all?</span> <span id="v288" class="merged">Hap</span>pi<span id="v289" class="merged">ly when I shall wed</span>,<br>
T<span id="v290" class="merged">hat Lord</span>,<span id="v291" class="merged"> whose hand</span> m<span id="v292" class="merged">ust take my plight, shall ca</span><span id="v293" class="merged">r</span>ry<br>
H<span id="v294" class="merged">alf</span>e<span id="v295" class="merged"> my </span>l<span id="v296" class="merged">ove with him,</span> h<span id="v297" class="merged">alf</span>e<span id="v298" class="merged"> my </span>C<span id="v299" class="merged">are</span>,<span id="v300" class="merged"> and </span>D<span id="v301" class="merged">ut</span>ie<span id="v302" class="merged">,</span><br>
S<span id="v303" class="merged">ure I shall never</span> mar<span id="v304" class="merged">r</span><span id="v305" class="merged">y like my </span>S<span id="v306" class="merged">isters</span><span id="v307a" class="merged">.</span><br></div><span id="v307b" class="merged">
</span><div class="sp"><span id="v307c" class="merged"><span class="speaker">Lear.</span>
But goes th</span>y heart<span id="v308" class="merged"> with th</span>is<span id="v309a" class="merged">?</span><br></div><span id="v309b" class="merged">
</span><div class="sp"><span id="v309c" class="merged"><span class="speaker">Cor</span></span><span id="v310a" class="merged"><span class="speaker">.</span>
</span><span id="v310b" class="merged">I</span> my<span id="v311" class="merged"> good</span><span id="v312a" class="merged"> Lord.</span><br></div><span id="v312b" class="merged">
</span><div class="sp"><span id="v312c" class="merged"><span class="speaker">Lear.</span>
So yo</span>u<span id="v313" class="merged">ng</span>,<span id="v314" class="merged"> and so untender</span>?<br></div><span id="v315a" class="merged">
</span><div class="sp"><span id="v315b" class="merged"><span class="speaker">Cor.</span>
</span><span id="v315c" class="merged">So young</span><span id="v316" class="merged"> my Lord</span>,<span id="v317" class="merged"> and true</span>.<br></div><span id="v318a" class="merged">
</span><div class="sp"><span id="v318b" class="merged"><span class="speaker">Lear.</span>
</span>L<span id="v319" class="merged">et it be so, th</span>y<span id="v320" class="merged"> truth then </span>shall <span id="v321" class="merged">be thy dow</span>re:<br><span id="v322a" class="merged">
</span><span id="v322b" class="merged">For by the sacred radi</span>e<span id="v323" class="merged">nce of the Sun</span>ne<span id="v324a" class="merged">,</span><br><span id="v324b" class="merged">
</span><span id="v324c" class="merged">The m</span>iseri<span id="v325" class="merged">es</span><span id="v326" class="merged"> of <span class="italics">He</span></span><span class="italics">c<span id="v327" class="merged">cat</span></span><span id="v328" class="merged"> and the </span>n<span id="v329a" class="merged">ight:</span><br><span id="v329b" class="merged">
</span><span id="v329c" class="merged">By all </span><span id="v330" class="merged">operation</span><span id="v331" class="merged"> of the </span>O<span id="v332" class="merged">rb</span>e<span id="v333a" class="merged">s,</span><br><span id="v333b" class="merged">
</span><span id="v333c" class="merged">From whom</span><span id="v334" class="merged"> we do</span><span id="v335" class="merged"> exist</span><span id="v336a" class="merged"> and cease to be,</span><br><span id="v336b" class="merged">
</span><span id="v336c" class="merged">He</span>e<span id="v337" class="merged">re I desclaim</span>e<span id="v338" class="merged"> all my </span>P<span id="v339" class="merged">aterna</span>l<span id="v340a" class="merged">l care,</span><br><span id="v340b" class="merged">
</span><span id="v340c" class="merged">Propinquit</span>y<span id="v341" class="merged"> and property of blo</span>o<span id="v342a" class="merged">d,</span><br><span id="v342b" class="merged">
</span><span id="v342c" class="merged">And as a stranger to my heart and me</span>,<br><span id="v343a" class="merged">
</span><span id="v343b" class="merged">Ho</span><span id="v344" class="merged">ld thee from this for ever</span>.<span id="v345" class="merged"> </span>T<span id="v346" class="merged">he </span>b<span id="v347" class="merged">arbarous <span class="italics">Scyth</span></span><span class="italics">i<span id="v348a" class="merged">an,</span></span><br><span id="v348b" class="merged">
</span><span id="v348c" class="merged">O</span>r<span id="v349" class="merged"> he that makes his </span>g<span id="v350" class="merged">eneration</span> m<span id="v351" class="merged">esses</span><br>
T<span id="v352" class="merged">o gorge his appetite</span>, s<span id="v353" class="merged">hall </span>to my bosome<br>
B<span id="v354" class="merged">e as well neighbour'd</span><span id="v355" class="merged"> pi</span>t<span id="v356" class="merged">t</span>i<span id="v357" class="merged">ed, and rel</span>ee<span id="v358" class="merged">v</span>'d,<br><span id="v359a" class="merged">
</span><span id="v359b" class="merged">As thou my some</span><span id="v360" class="merged">time </span>D<span id="v361a" class="merged">aughter.</span><br></div><span id="v361b" class="merged">
</span><div class="sp"><span id="v361c" class="merged"><span class="speaker">Kent.</span>
Good</span>,<span id="v362a" class="merged"> my Liege.</span><br></div><span id="v362b" class="merged">
</span><div class="sp"><span id="v362c" class="merged"><span class="speaker">Lear.</span>
Peace</span><span id="v363" class="merged"> <span class="italics">Kent</span></span><span class="italics">.</span><br>
C<span id="v364" class="merged">ome not between</span>e<span id="v365" class="merged"> the Dragon </span>and<span id="v366" class="merged"> his wrath</span>,<br><span id="v367a" class="merged">
I lov'd her most, and thought to set my rest<br>
</span><span id="v367b" class="merged">On her kind</span><span id="v368" class="merged"> nursery</span>. H<span id="v369" class="merged">ence and avo</span>i<span id="v370" class="merged">d</span><span id="v371" class="merged"> my sight</span>:<br><span id="v372a" class="merged">
</span><span id="v372b" class="merged">So be my grave my peace</span>,<span id="v373" class="merged"> as here I give</span><br><span id="v374a" class="merged">
</span><span id="v374b" class="merged">Her </span>F<span id="v375" class="merged">ather</span><span id="v376" class="merged">s heart from her</span>:<span id="v377" class="merged"> call <span class="italics">France,</span> who stir</span>re<span id="v378a" class="merged">s?</span><br><span id="v378b" class="merged">
</span><span id="v378c" class="merged">Call <span class="italics">Burgundy, Cornw</span></span><span class="italics">a</span><span id="v379" class="merged"><span class="italics">ll,</span> and <span class="italics">Alban</span></span><span class="italics">ie<span id="v380a" class="merged">,</span></span><br><span id="v380b" class="merged">
</span><span id="v380c" class="merged">With my two </span>d<span id="v381" class="merged">aughters</span> D<span id="v382" class="merged">ow</span>res,<span id="v383" class="merged"> digest th</span>e<span id="v384a" class="merged"> third,</span><br><span id="v384b" class="merged">
</span><span id="v384c" class="merged">Let pride, which she ca</span><span id="v385" class="merged">l</span><span id="v386" class="merged">s plainnes</span>se<span id="v387" class="merged">, marr</span>y<span id="v388a" class="merged"> her:</span><br><span id="v388b" class="merged">
</span><span id="v388c" class="merged">I do</span>e<span id="v389" class="merged"> invest you jo</span>i<span id="v390" class="merged">ntly </span>with<span id="v391" class="merged"> my pow</span>er<span id="v392a" class="merged">,</span><br><span id="v392b" class="merged">
Preheminence, and all the large effects<br>
That troop</span>e<span id="v393" class="merged"> with Majest</span>y. O<span id="v394" class="merged">ur self</span>e<span id="v395" class="merged"> by </span>M<span id="v396" class="merged">onthly course</span>,<br><span id="v397a" class="merged">
</span><span id="v397b" class="merged">With reservation of an hundred </span>K<span id="v398a" class="merged">nights,</span><br><span id="v398b" class="merged">
</span><span id="v398c" class="merged">By you to be susta</span>in<span id="v399a" class="merged">'d, shall our abode</span><br><span id="v399b" class="merged">
</span><span id="v399c" class="merged">Make with you </span>one<span id="v400" class="merged"> due turn</span>e<span id="v401" class="merged">, on</span>e<span id="v402" class="merged">ly we shall retain</span>e<br><span id="v403a" class="merged">
</span><span id="v403b" class="merged">The name</span>,<span id="v404" class="merged"> and all th</span>'<span id="v405" class="merged">addition</span><span id="v406" class="merged"> to a King</span>: t<span id="v407" class="merged">he </span>S<span id="v408" class="merged">way,</span><br>
R<span id="v409" class="merged">even</span>new<span id="v410" class="merged"> </span>E<span id="v411a" class="merged">xecution of the rest,</span><br><span id="v411b" class="merged">
</span><span id="v411c" class="merged">Beloved </span>S<span id="v412" class="merged">on</span>ne<span id="v413" class="merged">s be yours, which to confirm</span>e<span id="v414a" class="merged">,</span><br><span id="v414b" class="merged">
</span><span id="v414c" class="merged">This Coronet part betw</span>eene<span id="v415a" class="merged"> you.</span><br></div><span id="v415b" class="merged">
</span><div class="sp"><span id="v415c" class="merged"><span class="speaker">Kent.</span>
Roya</span>l<span id="v416a" class="merged">l <span class="italics">Lear,</span></span><br><span id="v416b" class="merged">
</span><span id="v416c" class="merged">Whom I have ever hono</span><span id="v417" class="merged">r'd as </span>my<span id="v418a" class="merged"> King,</span><br><span id="v418b" class="merged">
</span><span id="v418c" class="merged">Lov</span>'<span id="v419" class="merged">d as my Father, as my Master follow</span>'<span id="v420a" class="merged">d,</span><br><span id="v420b" class="merged">
</span><span id="v420c" class="merged">As my</span><span id="v421" class="merged"> </span>great <span id="v422" class="merged">Patron</span><span id="v423" class="merged"> thought on in my pra</span>i<span id="v424a" class="merged">ers.</span><br></div><span id="v424b" class="merged">
</span><div class="sp"><span id="v424c" class="merged"><span class="speaker">Le</span></span><span id="v425a" class="merged"><span class="speaker">.</span>
</span><span id="v425b" class="merged">The </span>b<span id="v426" class="merged">ow is bent </span>&amp;<span id="v427" class="merged"> draw</span>ne,<span id="v428a" class="merged"> make from the shaft.</span><br></div><span id="v428b" class="merged">
</span><div class="sp"><span id="v428c" class="merged"><span class="speaker">Kent.</span>
Let it fall rather,</span> t<span id="v429" class="merged">hough the fork</span>e<span id="v430" class="merged"> invade</span><br>
T<span id="v431" class="merged">he region of my heart,</span> b<span id="v432" class="merged">e <span class="italics">Kent</span> unmannerly</span>,<br>
W<span id="v433" class="merged">hen <span class="italics">Lear</span> is ma</span>d, w<span id="v434" class="merged">hat w</span>ouldes<span id="v435" class="merged">t thou do</span><span id="v436" class="merged"> o</span><span id="v437" class="merged">ld man</span>?<br>
T<span id="v438" class="merged">hink'st thou that </span>d<span id="v439" class="merged">ut</span>ie s<span id="v440" class="merged">hall have dread to speak</span>e,<br>
W<span id="v441" class="merged">hen </span>p<span id="v442" class="merged">ower to </span>f<span id="v443" class="merged">lattery</span><span id="v444" class="merged"> bow</span>e<span id="v445" class="merged">s</span>?<br><span id="v446a" class="merged">
</span><span id="v446b" class="merged">To plainnes</span>se<span id="v447" class="merged"> honour</span>'<span id="v448" class="merged">s bound</span>,<br>
W<span id="v449" class="merged">hen Majesty </span>fall<span id="v450" class="merged">s to folly,</span> r<span id="v451" class="merged">eserve thy </span>stat<span id="v452" class="merged">e,</span><br>
A<span id="v453" class="merged">nd </span>in <span id="v454" class="merged">thy best consideration</span> c<span id="v455" class="merged">heck</span>e<br>
T<span id="v456" class="merged">his hideous rashnes</span>se<span id="v457" class="merged">, answer</span>e<span id="v458" class="merged"> my life</span>, m<span id="v459" class="merged">y judgement</span>:<br>
T<span id="v460" class="merged">hy yo</span><span id="v461" class="merged">ngest </span>D<span id="v462" class="merged">aughter do</span>'<span id="v463a" class="merged">s not love thee least,</span><br><span id="v463b" class="merged">
</span><span id="v463c" class="merged">Nor are those empty hearted</span>,<span id="v464" class="merged"> whose low</span><span id="v465" class="merged"> sound</span>s<br><span id="v466a" class="merged">
</span><span id="v466b" class="merged">Reverb</span>e<span id="v467" class="merged"> no hollowne</span>s<span id="v468" class="merged">s</span>e<span id="v469a" class="merged">.</span><br></div><span id="v469b" class="merged">
</span><div class="sp"><span id="v469c" class="merged"><span class="speaker">Lear.</span>
<span class="italics">Kent</span></span><span class="italics">,</span><span id="v470a" class="merged"> on thy life no more.</span><br></div><span id="v470b" class="merged">
</span><div class="sp"><span id="v470c" class="merged"><span class="speaker">Kent.</span>
My life I never held but as </span><span id="v471" class="merged">pawn</span>e<br><span id="v472a" class="merged">
</span><span id="v472b" class="merged">To wage against th</span>ine<span id="v473" class="merged"> enemies, n</span>ere<span id="v474" class="merged"> fear</span>e<span id="v475" class="merged"> to l</span>o<span id="v476" class="merged">ose it</span>,<br><span id="v477a" class="merged">
</span><span id="v477b" class="merged">Thy saf</span>e<span id="v478" class="merged">ty being</span><span id="v479a" class="merged"> motive.</span><br></div><span id="v479b" class="merged">
</span><div class="sp"><span id="v479c" class="merged"><span class="speaker">Lear.</span>
Out of my sight.<br></span></div><span id="v479d" class="merged">
</span><div class="sp"><span id="v479e" class="merged"><span class="speaker">Kent.</span>
See better <span class="italics">Lear,</span> and let me still remain</span>e<br><span id="v480a" class="merged">
</span><span id="v480b" class="merged">The true blank</span>e<span id="v481" class="merged"> of thine e</span>i<span id="v482a" class="merged">e.</span><br></div><span id="v482b" class="merged">
</span><div class="sp"><span class="speaker">K</span><span id="v483a" class="merged"><span class="speaker">ear.</span>
</span><span id="v483b" class="merged">Now by <span class="italics">Ap</span></span><span class="italics"><span id="v484" class="merged">ollo</span>,</span><br></div>
<div class="sp"><span class="speaker">L</span><span id="v485a" class="merged"><span class="speaker">ent.</span>
</span><span id="v485b" class="merged">Now by <span class="italics">A</span></span><span class="italics">p</span><span id="v486" class="merged"><span class="italics">ollo,</span> King</span><br>
T<span id="v487" class="merged">hou swear</span>.<span id="v488" class="merged">st thy </span>G<span id="v489" class="merged">ods in vain</span>e<span id="v490a" class="merged">.</span><br></div><span id="v490b" class="merged">
</span><div class="sp"><span id="v490c" class="merged"><span class="speaker">Lear.</span>
</span>O <span id="v491" class="merged">Vassal</span>l! Mis<span id="v492a" class="merged">creant.</span><br></div><span id="v492b" class="merged">
</span><div class="sp"><span class="speaker">Alb. Cor.</span>
Deare Sir forbeare.<br></div>
<div class="sp"><span id="v493" class="merged"><span class="speaker">Kent.</span>
</span>K<span id="v494" class="merged">ill thy Physi</span>tion, a<span id="v495" class="merged">nd th</span>y<span id="v496" class="merged"> </span>f<span id="v497" class="merged">ee bestow</span><br>
U<span id="v498" class="merged">pon the foul</span>e<span id="v499" class="merged"> disease,</span> r<span id="v500" class="merged">evoke th</span>y<span id="v501" class="merged"> </span>guift,<br>
O<span id="v502" class="merged">r whil</span>'<span id="v503" class="merged">st I can vent clamour</span> f<span id="v504" class="merged">rom my throat</span>e<span id="v505" class="merged">,</span><br>
I<span id="v506" class="merged">l</span>e<span id="v507" class="merged"> tell thee thou do</span><span id="v508" class="merged">st evi</span>l<span id="v509a" class="merged">l.</span><br></div><span id="v509b" class="merged">
</span><div class="sp"><span id="v509c" class="merged"><span class="speaker">Lea</span></span><span id="v510a" class="merged"><span class="speaker">.</span>
</span><span id="v510b" class="merged">Hear</span>e<span id="v511" class="merged"> me</span> recreant<span id="v512" class="merged">, on th</span>ine<span id="v513" class="merged"> alle</span><span id="v514" class="merged">g</span>e<span id="v515" class="merged">ance hear</span>e<span id="v516" class="merged"> me</span>;<br>
That<span id="v517" class="merged"> thou hast fought to make us break</span>e<span id="v518" class="merged"> our vow</span>es<span id="v519a" class="merged">,</span><br><span id="v519b" class="merged">
</span><span id="v519c" class="merged">Which we durst never yet; and with strai</span>n'<span id="v520a" class="merged">d pride,</span><br><span id="v520b" class="merged">
</span><span id="v520c" class="merged">To come betw</span>ixt<span id="v521" class="merged"> our sentence</span>s,<span id="v522" class="merged"> and our pow</span>er.<br><span id="v523a" class="merged">
</span><span id="v523b" class="merged">Which</span>,<span id="v524" class="merged"> nor our nature</span>,<span id="v525" class="merged"> nor our place can bear</span>e;<br><span id="v526a" class="merged">
</span><span id="v526b" class="merged">Our potenc</span>ie<span id="v527" class="merged"> made good, take they reward</span>.<br><span id="v528a" class="merged">
</span><span id="v528b" class="merged">F</span>iv<span id="v529" class="merged">e day</span>e<span id="v530" class="merged">s we do</span><span id="v531a" class="merged"> allot thee for provision,</span><br><span id="v531b" class="merged">
</span><span id="v531c" class="merged">To shield thee from dis</span><span id="v532" class="merged">as</span>ter<span id="v533a" class="merged">s of the world,</span><br><span id="v533b" class="merged">
</span><span id="v533c" class="merged">And on the </span>six<span id="v534" class="merged">t</span><span id="v535" class="merged"> to turn</span>e<span id="v536" class="merged"> thy hated back</span>e<br><span id="v537a" class="merged">
</span><span id="v537b" class="merged">Upon our </span>k<span id="v538" class="merged">ingdom</span>e;<span id="v539" class="merged"> if</span> on<span id="v540a" class="merged"> the tenth day following,</span><br><span id="v540b" class="merged">
</span><span id="v540c" class="merged">Thy banisht </span>t<span id="v541" class="merged">runk</span>e<span id="v542" class="merged"> be found in our </span>D<span id="v543a" class="merged">ominions,</span><br><span id="v543b" class="merged">
</span><span id="v543c" class="merged">The moment is thy death, away</span>. B<span id="v544" class="merged">y <span class="italics">Jupiter,</span></span><br>
T<span id="v545" class="merged">his shall not be revok</span>'d,<br></div><span id="v546a" class="merged">
</span><div class="sp"><span id="v546b" class="merged"><span class="speaker">Kent.</span>
</span>F<span id="v547" class="merged">are thee well </span>K<span id="v548" class="merged">ing, si</span>th<span id="v549" class="merged"> thus thou wilt appear</span>e<span id="v550a" class="merged">,</span><br><span id="v550b" class="merged">
</span><span id="v550c" class="merged">Fr</span>eedome<span id="v551" class="merged"> lives hence, and banishment is here</span>;<br><span id="v552a" class="merged">
</span><span id="v552b" class="merged">The </span>G<span id="v553" class="merged">ods to their </span>deere shel<span id="v554" class="merged">te</span>r<span id="v555" class="merged"> take the</span>e M<span id="v556" class="merged">aid</span><span id="v557a" class="merged">,</span><br><span id="v557b" class="merged">
</span><span id="v557c" class="merged">Tha</span>t justly think'st, and hast mos<span id="v558" class="merged">t rightly </span><span id="v559" class="merged">said</span>:<br><span id="v560a" class="merged">
</span><span id="v560b" class="merged">And your large speeches</span>,<span id="v561" class="merged"> may your deed</span>e<span id="v562a" class="merged">s approve,</span><br><span id="v562b" class="merged">
That good effects may spring from words of love:<br>
Thus <span class="italics">Kent</span></span><span class="italics">,</span><span id="v563" class="merged"> O Princes, bids you all ad</span>ew<span id="v564a" class="merged">,</span><br><span id="v564b" class="merged">
</span><span id="v564c" class="merged">He</span>e'l<span id="v565" class="merged"> shape his old course</span>,<span id="v566" class="merged"> in a </span>C<span id="v567" class="merged">ountr</span>y<span id="v568" class="merged"> new.</span><br></div> <p class="stage-italic">Exit.</p>
</div>
</div>
<div id="table"><div id="innertable" style="width: 56184px;">
<table id="apparatus"><tbody><tr><td class="siglum">Q2</td><td><span class="inserted">M. William Shake-speare His History, of</span></td><td>&nbsp;K<span class="inserted">ing</span></td><td>&nbsp;L<span class="inserted">ear</span></td><td>.
Enter Kent, Gloce</td><td>ster, and <span class="inserted">Bastar</span></td><td>d.
Kent.
I Thought the King had more affected the Duke of Alb<span class="inserted">ene</span></td><td>y, the</td><td>n Corn<span class="inserted">e</span></td><td>wa</td><td>ll.
Glo<span class="inserted">st</span></td><td>.
It did alwa<span class="inserted">ie</span></td><td>s seeme so</td><td>&nbsp;to us<span class="inserted">, b</span></td><td>ut now in the division of the <span class="inserted">K</span></td><td>ingdome<span class="inserted">s</span></td><td>, it appear</td><td>s not which of the Dukes he</td><td>&nbsp;val<span class="inserted">u</span></td><td>es most, for <span class="inserted">e</span></td><td>qualities are so weigh<span class="inserted">e</span></td><td>d, that curiosit<span class="inserted">y</span></td><td>&nbsp;in neither, can make&nbsp;chois</td><td>e of eithers mo<span class="inserted">it</span></td><td>i<span class="inserted">e</span></td><td>.
Kent.
Is not this your <span class="inserted">s</span></td><td>on<span class="inserted">ne</span></td><td>, my Lord?
Glo<span class="inserted">st</span></td><td>.
His breeding</td><td>&nbsp;<span class="inserted">s</span></td><td>ir</td><td>&nbsp;hath b<span class="inserted">ee</span></td><td>n<span class="inserted">e</span></td><td>&nbsp;at my charge<span class="inserted">.</span></td><td>&nbsp;I have so often blush<span class="inserted">t</span></td><td>&nbsp;to ack</td><td>owledge him, that now I am braz'd to'</td><td>t.
Kent.
I cannot conceive you.
Glo<span class="inserted">st</span></td><td>.
Sir, this yo<span class="inserted">u</span></td><td>ng <span class="inserted">f</span></td><td>ellowe</td><td>s m</td><td>other <span class="inserted">c</span></td><td>ould<span class="inserted">,</span></td><td>&nbsp;whereupon sh</td><td>e grew round womb<span class="inserted">e</span></td><td>d, and had indeed</td><td>&nbsp;</td><td>Sir</td><td>&nbsp;a <span class="inserted">s</span></td><td>onne</td><td>&nbsp;for&nbsp;her C</td><td>radle, e</td><td>re she had a h</td><td>usband for her b</td><td>ed<span class="inserted">, d</span></td><td>o</td><td>&nbsp;you smell a fault?
Kent.
I cannot wish the fault undone, the issue&nbsp;of it</td><td>&nbsp;being so proper.
Glo</td><td>.
But I have <span class="inserted">sir&nbsp;</span></td><td>a <span class="inserted">s</span></td><td>onne</td><td>&nbsp;by order of the Law, some ye<span class="inserted">are</span></td><td>&nbsp;elder the</td><td>n this<span class="inserted">,</span></td><td>&nbsp;who</td><td>&nbsp;yet is no dee</td><td>rer in my account, tho</td><td>gh this <span class="inserted">k</span></td><td>nave came som<span class="inserted">ething</span></td><td>&nbsp;<span class="inserted">s</span></td><td>awc<span class="inserted">e</span></td><td>ly to the w</td><td>orld before he</td><td>&nbsp;was sent for</td><td>&nbsp;yet was his <span class="inserted">m</span></td><td>other fa<span class="inserted">i</span></td><td>re</td><td>, there was good sport at his making, <span class="inserted">&amp;</span></td><td>&nbsp;the <span class="inserted">w</span></td><td>hor<span class="inserted">e</span></td><td>son must be acknowledged<span class="inserted">, do</span></td><td>&nbsp;you know this <span class="inserted">n</span></td><td>oble <span class="inserted">gent</span></td><td>leman, Edmond?
<span class="inserted">
Bast</span></td><td>.
No</td><td>&nbsp;my Lord.
Glo</td><td>.
My Lord of Kent<span class="inserted">,
r</span></td><td>emember him h<span class="inserted">e</span></td><td>e</td><td>reafter, as my <span class="inserted">h</span></td><td>onourable <span class="inserted">f</span></td><td>riend.
<span class="inserted">Bast</span></td><td>.
My services to your Lordship.
Kent.
I must love you, and sue to know you better.
<span class="inserted">Bast</span></td><td>.
Sir, I shall study deserving.
Glo</td><td>.
H</td><td>e hath b<span class="inserted">ee</span></td><td>n<span class="inserted">e</span></td><td>&nbsp;out nine&nbsp;yeare</td><td>s, and away h<span class="inserted">e</span></td><td>&nbsp;shall againe<span class="inserted">, t</span></td><td>he King is com</td><td>ming.
S<span class="inserted">ound a S</span></td><td>ennet<span class="inserted">.</span></td><td>&nbsp;Enter <span class="inserted">one bear</span></td><td>ing<span class="inserted"> a Coronet, then</span></td><td>&nbsp;Lear,<span class="inserted"> then the Dukes of Albany and</span></td><td>&nbsp;Cornwall, <span class="inserted">next</span></td><td>&nbsp;Gon<span class="inserted">e</span></td><td>rill, Regan, Cordelia, <span class="inserted">with follower</span></td><td>s.
Lear.
Attend the Lords of France <span class="inserted">and</span></td><td>&nbsp;Burgundy, Gloster.
Glo<span class="inserted">st</span></td><td>.
I shall, my L<span class="inserted">iege</span></td><td>.
Lear.
Meane</td><td>&nbsp;time we sha</td><td>l</td><td>&nbsp;expresse</td><td>&nbsp;our darker purpose<span class="inserted">s</span></td><td>.
<span class="inserted">T</span></td><td>he <span class="inserted">M</span></td><td>ap there<span class="inserted">; k</span></td><td>now</td><td>&nbsp;we have divided
In&nbsp;three</td><td>&nbsp;our <span class="inserted">K</span></td><td>ingdome</td><td>: and&nbsp;</td><td>tis our f<span class="inserted">ir</span></td><td>st intent,
To shake all <span class="inserted">c</span></td><td>ares and <span class="inserted">b</span></td><td>usinesse</td><td>&nbsp;from our <span class="inserted">stat</span></td><td>e,
Conf<span class="inserted">irm</span></td><td>ing them on yo<span class="inserted">u</span></td><td>nger <span class="inserted">y</span></td><td>ea<span class="inserted">r</span></td><td>es</td><td>,
</td><td>The<span class="inserted"> two great</span></td><td>&nbsp;Princes</td><td>, France <span class="inserted">and</span></td><td>&nbsp;Burgundy,</td><td>
Great Ri</td><td>vals in our yongest d</td><td>aughter</td><td>s l</td><td>ove,
Long in our Court,</td><td>&nbsp;have made their amorous sojourne</td><td>,
And h</td><td>ere are to be answer'd<span class="inserted">; t</span></td><td>ell me my d</td><td>aughters<span class="inserted">,</span></td><td>
Which of you shall we say doth love us most,
That we, our largest bount<span class="inserted">y</span></td><td>&nbsp;may extend<span class="inserted">,</span></td><td>
Where <span class="inserted">merit</span></td><td>&nbsp;doth <span class="inserted">mos</span></td><td>t challenge<span class="inserted"> it:
</span></td><td>Gon<span class="inserted">e</span></td><td>rill<span class="inserted"> o</span></td><td>ur eldest borne</td><td>, speake first.</td><td>
Gon.
Sir, I love you more the</td><td>n word can w<span class="inserted">ie</span></td><td>ld the matter,
De<span class="inserted">a</span></td><td>rer then e</td><td>ye-sight, space, and libert<span class="inserted">y</span></td><td>,
Beyond what can be val<span class="inserted">u</span></td><td>ed,</td><td>&nbsp;rich or rare,
No&nbsp;lesse</td><td>&nbsp;the</td><td>n life,</td><td>&nbsp;with grace, health, beauty, hono<span class="inserted">u</span></td><td>r<span class="inserted">,</span></td><td>
As much a<span class="inserted">s</span></td><td>&nbsp;<span class="inserted">c</span></td><td>hilde</td><td>&nbsp;e</td><td>re lov<span class="inserted">e</span></td><td>d, or <span class="inserted">f</span></td><td>ather f<span class="inserted">rie</span></td><td>nd<span class="inserted">,</span></td><td>
A love that makes breath poore</td><td>, and speech unable,
Beyond all manner of so much I love you.
Cor.
What shall Cordelia <span class="inserted">do, l</span></td><td>ove<span class="inserted">,</span></td><td>&nbsp;and be silent.
Lear.
Of all these bounds<span class="inserted">,</span></td><td>&nbsp;even from this <span class="inserted">l</span></td><td>ine</td><td>&nbsp;to this,
With shad<span class="inserted">y</span></td><td>&nbsp;<span class="inserted">f</span></td><td>orrest</td><td>s, and wide-skirted Meade</td><td>s
We make the</td><td>e</td><td>&nbsp;Lady<span class="inserted">, t</span></td><td>o thine and Albanie</td><td>s i</td><td>ssue<span class="inserted">,
</span></td><td>
Be this perpetual</td><td>l. W</td><td>hat sa<span class="inserted">i</span></td><td>es our second <span class="inserted">d</span></td><td>aughter?
Our dee</td><td>rest Regan, wife <span class="inserted">to</span></td><td>&nbsp;Cornwall<span class="inserted">, speake.</span></td><td>
Reg.
</td><td>I am made of that selfe</td><td>-<span class="inserted">same&nbsp;</span></td><td>mett<span class="inserted">all as</span></td><td>&nbsp;my <span class="inserted">S</span></td><td>ister<span class="inserted"> is</span></td><td>,
And prize me at her worth<span class="inserted"> i</span></td><td>n my true heart,
I find she names my very deed<span class="inserted">e</span></td><td>&nbsp;of love<span class="inserted">, o</span></td><td>ne</td><td>ly she<span class="inserted">e</span></td><td>&nbsp;c<span class="inserted">a</span></td><td>me</td><td>&nbsp;short,<span class="inserted">
T</span></td><td>hat I professe<span class="inserted"> m</span></td><td>y selfe</td><td>&nbsp;an e</td><td>nemy to all other joyes,
Which the most precious square of sense professes,
And finde</td><td>&nbsp;I am alone felicitate<span class="inserted"> i</span></td><td>n your deere</td><td>&nbsp;<span class="inserted">h</span></td><td>ighnes</td><td>se</td><td>&nbsp;love.
Cor</td><td>.
Then poore</td><td>&nbsp;Cordelia,<span class="inserted"> and</span></td><td>&nbsp;yet not so, since I am sure<span class="inserted">
M</span></td><td>y love's<span class="inserted"> m</span></td><td>ore <span class="inserted">rich</span></td><td>er</td><td>&nbsp;the</td><td>n my tongue.
Lear.
To thee,</td><td>&nbsp;and thine hereditar<span class="inserted">y</span></td><td>&nbsp;ever</td><td>
Remaine</td><td>&nbsp;this ample third of our&nbsp;faire</td><td>&nbsp;<span class="inserted">k</span></td><td>ingdome</td><td>,
No lesse</td><td>&nbsp;in space, validit<span class="inserted">y</span></td><td>, and pleasure</td><td>
The</td><td>n that conf<span class="inserted">irm</span></td><td>'d on Gon<span class="inserted">e</span></td><td>rill<span class="inserted">; but n</span></td><td>ow our <span class="inserted">j</span></td><td>oy,
Although our</td><td>&nbsp;last<span class="inserted">, not</span></td><td>&nbsp;least<span class="inserted"> in </span></td><td>o<span class="inserted">ur deere</span></td><td>&nbsp;love,
</td><td>What can you say, to <span class="inserted">win a</span></td><td>&nbsp;third, more op<span class="inserted">u</span></td><td>lent<span class="inserted">
T</span></td><td>hen your Sisters?</td><td>
Cor</td><td>.
Nothing</td><td>&nbsp;my Lord.
Lear.
<span class="inserted">How, n</span></td><td>othing <span class="inserted">can</span></td><td>&nbsp;come of nothing, speake</td><td>&nbsp;againe</td><td>.
Cor</td><td>.
Unhapp<span class="inserted">y</span></td><td>&nbsp;that I am, I cannot he</td><td>ave<span class="inserted"> m</span></td><td>y heart into my mouth<span class="inserted">,</span></td><td>&nbsp;I love your Majesty<span class="inserted"> a</span></td><td>ccording to my bond, no more nor lesse</td><td>.
Lear.<span class="inserted">Go too, go too, m</span></td><td>end your speech a little,
Lea</td><td>st you may marre</td><td>&nbsp;your <span class="inserted">f</span></td><td>ortunes.
Cor</td><td>.
Good</td><td>&nbsp;my&nbsp;Lord.</td><td>
You have begot me, bred me, lov<span class="inserted">e</span></td><td>d me.</td><td>
I return</td><td>&nbsp;those duties&nbsp;backe</td><td>&nbsp;as are right fit,
Obey you, <span class="inserted">l</span></td><td>ove you, and most honour you<span class="inserted">,</span></td><td>
Why have my <span class="inserted">s</span></td><td>isters <span class="inserted">h</span></td><td>usbands<span class="inserted">,</span></td><td>&nbsp;if they say<span class="inserted"> t</span></td><td>hey love you all?<span class="inserted">
</span></td><td>Hap</td><td>ly when I shall wed,<span class="inserted"> t</span></td><td>hat Lord<span class="inserted">,</span></td><td>&nbsp;whose hand<span class="inserted">
M</span></td><td>ust take my plight, shall ca<span class="inserted">r</span></td><td>r<span class="inserted">y h</span></td><td>alfe</td><td>&nbsp;my l</td><td>ove with him,<span class="inserted">
H</span></td><td>alfe</td><td>&nbsp;my <span class="inserted">c</span></td><td>are</td><td>&nbsp;and <span class="inserted">d</span></td><td>ut<span class="inserted">y</span></td><td>,<span class="inserted"> s</span></td><td>ure I shall never<span class="inserted">
Ma</span></td><td>r<span class="inserted">r</span></td><td>y like my S</td><td>isters<span class="inserted">, to love my father all</span></td><td>.
Lear.
But goes th<span class="inserted">is</span></td><td>&nbsp;with th<span class="inserted">y&nbsp;heart</span></td><td>?
Cor</td><td>.
I</td><td>&nbsp;good<span class="inserted"> my</span></td><td>&nbsp;Lord.
Lear.
So yo<span class="inserted">u</span></td><td>ng</td><td>&nbsp;and so untender?</td><td>
Cor.
So young</td><td>&nbsp;my&nbsp;Lord,</td><td>&nbsp;and&nbsp;true.</td><td>
Lear.
<span class="inserted">Well l</span></td><td>et it be so, thy</td><td>&nbsp;truth&nbsp;then </td><td>be thy dow<span class="inserted">er,</span></td><td>
For by the sacred radie</td><td>nce of the Sunne</td><td>,
The mis<span class="inserted">tr</span></td><td>es<span class="inserted">se</span></td><td>&nbsp;of Hec</td><td>cat</td><td>&nbsp;and the <span class="inserted">m</span></td><td>ight:
By all&nbsp;</td><td>operation</td><td>&nbsp;of&nbsp;the O</td><td>rbe</td><td>s,
From whom</td><td>&nbsp;we do</td><td>&nbsp;exist</td><td>&nbsp;and cease to be,
Hee</td><td>re I desclaime</td><td>&nbsp;all my <span class="inserted">p</span></td><td>aternal</td><td>l care,
Propinquity</td><td>&nbsp;and property of blo<span class="inserted">u</span></td><td>d,
And as a stranger to my heart and me,</td><td>
Ho</td><td>ld thee from this for ever<span class="inserted">,</span></td><td>&nbsp;<span class="inserted">t</span></td><td>he b</td><td>arbarous Scyth<span class="inserted">i</span></td><td>an,
Or</td><td>&nbsp;he that makes&nbsp;his g</td><td>eneration<span class="inserted">
M</span></td><td>esses<span class="inserted"> t</span></td><td>o gorge his appetite<span class="inserted">,
S</span></td><td>hall <span class="inserted">b</span></td><td>e as well neighbour'd<span class="inserted">,</span></td><td>&nbsp;pi</td><td>t<span class="inserted">ti</span></td><td>ed, and rel<span class="inserted">ee</span></td><td>v<span class="inserted">ed,</span></td><td>
As thou my some<span class="inserted">-</span></td><td>time <span class="inserted">d</span></td><td>aughter.
Kent.
Good</td><td>&nbsp;my Liege.
Lear.
Peace</td><td>&nbsp;Kent<span class="inserted">, c</span></td><td>ome not betweene</td><td>&nbsp;the Dragon and</td><td>&nbsp;his&nbsp;wrath</td><td>
I lov'd her most, and thought to set my rest
On her kind<span class="inserted">e</span></td><td>&nbsp;nursery<span class="inserted">, h</span></td><td>ence and avoi</td><td>d</td><td>&nbsp;my sight:</td><td>
So be my grave my peace</td><td>&nbsp;as here I give<span class="inserted">,</span></td><td>
Her <span class="inserted">f</span></td><td>ather</td><td>s heart from her<span class="inserted">;</span></td><td>&nbsp;call France, who stirre</td><td>s?
Call Burgundy, Cornwa</td><td>ll, and Alban<span class="inserted">y</span></td><td>,
With my two d</td><td>aughters<span class="inserted"> d</span></td><td>ow<span class="inserted">er</span></td><td>&nbsp;digest th<span class="inserted">is</span></td><td>&nbsp;third,
Let pride, which she ca</td><td>l</td><td>s plainnesse</td><td>, marry</td><td>&nbsp;her:
I do</td><td>&nbsp;invest you jo<span class="inserted">y</span></td><td>ntly <span class="inserted">in</span></td><td>&nbsp;my&nbsp;power</td><td>,
Preheminence, and all the large effects
That troope</td><td>&nbsp;with Majesty<span class="inserted">, o</span></td><td>ur selfe</td><td>&nbsp;by M</td><td>onthly course</td><td>
With reservation of an hundred K</td><td>nights,
By you to be sustain</td><td>'d, shall our abode
Make with you <span class="inserted">by</span></td><td>&nbsp;due turn<span class="inserted">es</span></td><td>, one</td><td>ly we shall retaine</td><td>
The name</td><td>&nbsp;and all th<span class="inserted">e </span></td><td>addition<span class="inserted">s</span></td><td>&nbsp;to a King<span class="inserted">,
T</span></td><td>he <span class="inserted">s</span></td><td>way,<span class="inserted"> r</span></td><td>even<span class="inserted">ue,</span></td><td>&nbsp;<span class="inserted">e</span></td><td>xecution of the rest,
Beloved <span class="inserted">s</span></td><td>onne</td><td>s be yours, which to confirme</td><td>,
This Coronet part betw<span class="inserted">ixt</span></td><td>&nbsp;you.
Kent.
Royal</td><td>l Lear,
Whom I have ever hono</td><td>r'd as my</td><td>&nbsp;King,
Lov<span class="inserted">e</span></td><td>d as my Father, as my Master follow<span class="inserted">e</span></td><td>d,
As my<span class="inserted"> great</span></td><td>&nbsp;</td><td>Patron</td><td>&nbsp;thought on in my prai</td><td>ers.
Le<span class="inserted">ar</span></td><td>.
The b</td><td>ow is bent <span class="inserted">and</span></td><td>&nbsp;drawne,</td><td>&nbsp;make from the shaft.
Kent.
Let it fall rather,<span class="inserted">
T</span></td><td>hough the forke</td><td>&nbsp;invade<span class="inserted"> t</span></td><td>he region of my heart,<span class="inserted">
B</span></td><td>e Kent unmannerly<span class="inserted">, w</span></td><td>hen Lear is ma<span class="inserted">d,
W</span></td><td>hat w<span class="inserted">il</span></td><td>t thou do</td><td>&nbsp;o</td><td>ld man<span class="inserted">, t</span></td><td>hink'st thou that d</td><td>ut<span class="inserted">y
S</span></td><td>hall have dread to speake,<span class="inserted"> w</span></td><td>hen p</td><td>ower to f</td><td>lattery</td><td>&nbsp;bowe</td><td>s<span class="inserted">,</span></td><td>
To plainnesse</td><td>&nbsp;honour</td><td>s bound<span class="inserted">, w</span></td><td>hen Majesty <span class="inserted">stoop</span></td><td>s to folly,<span class="inserted">
R</span></td><td>eserve thy <span class="inserted">doom</span></td><td>e,<span class="inserted"> a</span></td><td>nd in&nbsp;</td><td>thy best consideration<span class="inserted">
C</span></td><td>hecke<span class="inserted"> t</span></td><td>his hideous rashnesse</td><td>, answere</td><td>&nbsp;my life<span class="inserted">,
M</span></td><td>y judgement<span class="inserted">, t</span></td><td>hy yo</td><td>ngest <span class="inserted">d</span></td><td>aughter do<span class="inserted">e</span></td><td>s not love thee least,
Nor are those empty hearted,</td><td>&nbsp;whose low</td><td>&nbsp;sound</td><td>
Reverb<span class="inserted">s</span></td><td>&nbsp;no hollownes</td><td>se</td><td>.
Lear.
Kent,</td><td>&nbsp;on thy life no more.
Kent.
My life I never held but as <span class="inserted">a </span></td><td>pawne</td><td>
To wage against th<span class="inserted">y</span></td><td>&nbsp;enemies, n<span class="inserted">or</span></td><td>&nbsp;feare</td><td>&nbsp;to l</td><td>ose it,</td><td>
Thy safe</td><td>ty being<span class="inserted"> the</span></td><td>&nbsp;motive.
Lear.
Out of my sight.
Kent.
See better Lear, and let me still remaine</td><td>
The true blanke</td><td>&nbsp;of thine e<span class="inserted">i</span></td><td>e.
<span class="inserted">L</span></td><td>ear.
Now by Ap</td><td>ollo<span class="inserted">--------
K</span></td><td>ent.
Now by A<span class="inserted">p</span></td><td>ollo, King<span class="inserted"> t</span></td><td>hou swear<span class="inserted">'</span></td><td>st thy G</td><td>ods in vaine</td><td>.
Lear.
</td><td>Vassall<span class="inserted">, re</span></td><td>creant.
</td><td>Kent.
<span class="inserted">Do k</span></td><td>ill thy Physi<span class="inserted">tion
A</span></td><td>nd th<span class="inserted">e</span></td><td>&nbsp;f</td><td>ee bestow<span class="inserted"> u</span></td><td>pon the foule</td><td>&nbsp;disease,<span class="inserted">
R</span></td><td>evoke thy</td><td>&nbsp;<span class="inserted">doome, o</span></td><td>r whil<span class="inserted">'</span></td><td>st I can vent clamour<span class="inserted">
F</span></td><td>rom my throat<span class="inserted">e</span></td><td>,<span class="inserted"> i</span></td><td>le</td><td>&nbsp;tell thee thou do</td><td>st evil</td><td>l.
Lea<span class="inserted">r</span></td><td>.
Heare</td><td>&nbsp;me</td><td>, on th<span class="inserted">y</span></td><td>&nbsp;alle<span class="inserted">i</span></td><td>ge</td><td>ance heare</td><td>&nbsp;me<span class="inserted">;
Since</span></td><td>&nbsp;thou hast fought to make us breake</td><td>&nbsp;our vow</td><td>,
Which we durst never yet; and with strai<span class="inserted">e</span></td><td>d pride,
To come betw<span class="inserted">een</span></td><td>&nbsp;our sentence</td><td>&nbsp;and our power<span class="inserted">,</span></td><td>
Which,</td><td>&nbsp;nor our nature,</td><td>&nbsp;nor our place can beare<span class="inserted">,</span></td><td>
Our potenc<span class="inserted">y</span></td><td>&nbsp;made good, take they reward<span class="inserted">,</span></td><td>
F<span class="inserted">our</span></td><td>e daye</td><td>s we do</td><td>&nbsp;allot thee for provision,
To shield thee from dis<span class="inserted">e</span></td><td>as<span class="inserted">e</span></td><td>s of the world,
And on the <span class="inserted">fif</span></td><td>t</td><td>&nbsp;to&nbsp;turne</td><td>&nbsp;thy hated&nbsp;backe</td><td>
Upon our k</td><td>ingdome;</td><td>&nbsp;if on</td><td>&nbsp;the tenth day following,
Thy banisht t</td><td>runke</td><td>&nbsp;be found in&nbsp;our D</td><td>ominions,
The moment is thy death, away<span class="inserted">
B</span></td><td>y Jupiter,<span class="inserted"> t</span></td><td>his shall not be revok<span class="inserted">t,</span></td><td>
Kent.
<span class="inserted">Why f</span></td><td>are thee well <span class="inserted">K</span></td><td>ing, si<span class="inserted">nce</span></td><td>&nbsp;thus thou wilt appeare</td><td>,
Fr<span class="inserted">iendship</span></td><td>&nbsp;lives hence, and banishment is here<span class="inserted">;</span></td><td>
The G</td><td>ods to their <span class="inserted">pro</span></td><td>te<span class="inserted">ction</span></td><td>&nbsp;take the<span class="inserted"> m</span></td><td>aid</td><td>,
Tha</td><td>t rightly <span class="inserted">thinkes, and hath most justly&nbsp;</span></td><td>said<span class="inserted">,</span></td><td>
And your large speeches</td><td>&nbsp;may your deed<span class="inserted">e</span></td><td>s approve,
That good effects may spring from words of love:
Thus Kent,</td><td>&nbsp;O Princes, bids you all adew</td><td>,
Hee'l</td><td>&nbsp;shape his old course,</td><td>&nbsp;in a C</td><td>ountry</td><td>&nbsp;new.</td></tr><tr><td class="siglum">F2</td><td>THE TRAGEDIE OF</td><td>&nbsp;KING</td><td>&nbsp;LEAR.
Actus Primus. Scaena&nbsp;Prima</td><td>.
Enter Kent, Glouce</td><td>ster, and Edmon</td><td>d.
Kent.
I Thought the King had more affected the Duke of Alban</td><td>y, the</td><td>n Corn</td><td>wa</td><td>ll.
Glou</td><td>.
It did alway</td><td>s seeme</td><td>&nbsp;to&nbsp;us: B</td><td>ut now in the division of the K</td><td>ingdome</td><td>, it appear</td><td>s not which of the Dukes hee</td><td>&nbsp;val<span class="inserted">u</span></td><td>es most, for&nbsp;</td><td>qualities are so weigh'</td><td>d, that curiosity</td><td>&nbsp;in neither, can make&nbsp;chois</td><td>e of eithers mo</td><td>ity</td><td>.
Kent.
Is not this your <span class="inserted">s</span></td><td>on<span class="inserted">ne</span></td><td>, my Lord?
Glou</td><td>.
His breeding</td><td>&nbsp;S</td><td>ir,</td><td>&nbsp;hath b<span class="inserted">ee</span></td><td>n</td><td>&nbsp;at my charge.</td><td>&nbsp;I have so often blush'd</td><td>&nbsp;to ack</td><td>owledge him, that now I am braz'd too'</td><td>t.
Kent.
I cannot conceive you.
Glou</td><td>.
Sir, this yo</td><td>ng F</td><td>ellowe</td><td>s m</td><td>other c</td><td>ould;</td><td>&nbsp;whereupon sh</td><td>e grew round womb'</td><td>d, and had indeede</td><td>&nbsp;(</td><td>Sir)</td><td>&nbsp;a S</td><td>onne</td><td>&nbsp;for&nbsp;her C</td><td>radle, e</td><td>re she had a h</td><td>usband for her b</td><td>ed. D</td><td>o<span class="inserted">e</span></td><td>&nbsp;you smell a fault?
Kent.
I cannot wish the fault undone, the issue of it,</td><td>&nbsp;being so proper.
Glou</td><td>.
But I have&nbsp;</td><td>a S</td><td>onne, Sir,</td><td>&nbsp;by order of the Law, some&nbsp;yeere</td><td>&nbsp;elder the</td><td>n this;</td><td>&nbsp;who,</td><td>&nbsp;yet is no dee</td><td>rer in my account, thou</td><td>gh this K</td><td>nave came somthing</td><td>&nbsp;f</td><td>awci</td><td>ly to the w</td><td>orld before he<span class="inserted">e</span></td><td>&nbsp;was sent for:</td><td>&nbsp;yet was&nbsp;his M</td><td>other fa<span class="inserted">i</span></td><td>re</td><td>, there was good sport at his making, and</td><td>&nbsp;the <span class="inserted">w</span></td><td>hor</td><td>son must be acknowledged. Doe</td><td>&nbsp;you know this N</td><td>ob</td><td>leman, Edmond?
Edm</td><td>.
No,</td><td>&nbsp;my Lord.
Glou</td><td>.
My Lord of Kent:
R</td><td>emember him h</td><td>e</td><td>reafter, as my <span class="inserted">h</span></td><td>onourable F</td><td>riend.
Edm</td><td>.
My services to your Lordship.
Kent.
I must love you, and sue to know you better.
Edm</td><td>.
Sir, I shall study deserving.
Glou</td><td>.
H</td><td>e hath b<span class="inserted">ee</span></td><td>n</td><td>&nbsp;out nine&nbsp;yeare</td><td>s, and away he</td><td>&nbsp;shall againe. T</td><td>he King is com</td><td>ming.
S</td><td>ennet.</td><td>&nbsp;Enter K</td><td>ing</td><td>&nbsp;Lear,</td><td>&nbsp;Cornwall, Albany,</td><td>&nbsp;Gone</td><td>rill, Regan, Cordelia, and attendant</td><td>s.
Lear.
Attend the Lords of France &amp;</td><td>&nbsp;Burgundy, Gloster.
Glou</td><td>.
I shall, my Lord.Exit</td><td>.
Lear.
Meane</td><td>&nbsp;time we sha</td><td>l</td><td>&nbsp;expresse</td><td>&nbsp;our darker purpose</td><td>.
Give me t</td><td>he M</td><td>ap there. K</td><td>now, that</td><td>&nbsp;we have divided
In three<span class="inserted">,</span></td><td>&nbsp;our K</td><td>ingdome</td><td>: and '</td><td>tis our fa</td><td>st intent,
To shake all <span class="inserted">c</span></td><td>ares and <span class="inserted">b</span></td><td>usinesse</td><td>&nbsp;from our Ag</td><td>e,
Conferr</td><td>ing them on yo</td><td>nger strengths, while we
Unburthen'd crawle toward d</td><td>eath. Our son of Cornwal<span class="inserted">l,
And you our no l</span></td><td>esse loving Sonne of Albany</td><td>,
We have this houre a constant will to publish
Our <span class="inserted">Daughters severall Dowers, that future strife
May be prevented now.&nbsp;</span></td><td>The</td><td>&nbsp;Princes</td><td>, France &amp;</td><td>&nbsp;Burgundy<span class="inserted">.</span></td><td>
Great Ri</td><td>vals in our yongest d</td><td>aughter</td><td>s l</td><td>ove,
Long in our Court,</td><td>&nbsp;have made their amorous sojourne</td><td>,
And h</td><td>ere are to be answer'd. T</td><td>ell me my d</td><td>aughters
(Since now we will divest us both of Rule,
Interest of Territory, Cares of State)</td><td>
Which of you shall we say doth love us most,
That we, our largest bount<span class="inserted">y</span></td><td>&nbsp;may extend</td><td>
Where Nature</td><td>&nbsp;doth with meri</td><td>t challenge.&nbsp;</td><td>Gone</td><td>rill,
O</td><td>ur eldest borne</td><td>, speake first.</td><td>
Gon.
Sir, I love you more the</td><td>n word can wei</td><td>ld the matter,
De<span class="inserted">a</span></td><td>rer then e</td><td>ye-sight, space, and libert<span class="inserted">y</span></td><td>,
Beyond what can be val<span class="inserted">u</span></td><td>ed,</td><td>&nbsp;rich or rare,
No&nbsp;lesse</td><td>&nbsp;the</td><td>n life,</td><td>&nbsp;with grace, health, beauty, hono</td><td>r:</td><td>
As much as</td><td>&nbsp;<span class="inserted">c</span></td><td>hilde</td><td>&nbsp;e</td><td>re lov'</td><td>d, or F</td><td>ather fou</td><td>nd.</td><td>
A love that makes breath poore</td><td>, and speech unable,
Beyond all manner of so much I love you.
Cor.
What shall Cordelia speake? L</td><td>ove,</td><td>&nbsp;and be silent.
Lear.
Of all these bounds</td><td>&nbsp;even from this L</td><td>ine,</td><td>&nbsp;to this,
With shadowie</td><td>&nbsp;F</td><td>orrests, and with Champ<span class="inserted">ions rich'd 
With plenteous River</span></td><td>s, and wide-skirted Meade</td><td>s
We make the</td><td>e</td><td>&nbsp;Lady. T</td><td>o thine and Albanie</td><td>s i</td><td>ssues</td><td>
Be this perpetual</td><td>l. W</td><td>hat say</td><td>es our second D</td><td>aughter?
Our dee</td><td>rest Regan, wife of</td><td>&nbsp;Cornwall?</td><td>
Reg.
</td><td>I am made of that selfe</td><td>-</td><td>mettle as</td><td>&nbsp;my <span class="inserted">s</span></td><td>ister</td><td>,
And prize me at her worth. I</td><td>n my true heart,
I find she names my very deede</td><td>&nbsp;of love:
O</td><td>ne</td><td>ly she</td><td>&nbsp;co</td><td>mes too</td><td>&nbsp;short, t</td><td>hat I professe
M</td><td>y selfe</td><td>&nbsp;an e</td><td>nemy to all other joyes,
Which the most precious square of sense professes,
And finde</td><td>&nbsp;I am alone felicitate
I</td><td>n your deere</td><td>&nbsp;H</td><td>ighnes</td><td>se</td><td>&nbsp;love.
Cor</td><td>.
Then poore</td><td>&nbsp;Cordelia,
And</td><td>&nbsp;yet not so, since I am sure m</td><td>y love's
M</td><td>ore pond</td><td>erous</td><td>&nbsp;th<span class="inserted">a</span></td><td>n my tongue.
Lear.
To thee,</td><td>&nbsp;and thine hereditar<span class="inserted">y</span></td><td>&nbsp;ever,</td><td>
Remaine</td><td>&nbsp;this ample third of our&nbsp;faire</td><td>&nbsp;K</td><td>ingdome</td><td>,
No lesse</td><td>&nbsp;in space, validit<span class="inserted">y</span></td><td>, and pleasure</td><td>
The</td><td>n that conferr</td><td>'d on Gone</td><td>rill. N</td><td>ow our J</td><td>oy,
Although our</td><td>&nbsp;last and</td><td>&nbsp;least; to whose y</td><td>ong</td><td>&nbsp;love,
The Vines of France, and Milke of Burgundie,
Strive to be interest. </td><td>What can you say, to draw
A</td><td>&nbsp;third, more op<span class="inserted">u</span></td><td>lent t</td><td>hen your Sisters? Speake.</td><td>
Cor</td><td>.
Nothing</td><td>&nbsp;my Lord.
Lear.
Nothing?

Cor.
Nothing.
Lear.
N</td><td>othing will</td><td>&nbsp;come of nothing, speake</td><td>&nbsp;againe</td><td>.
Cor</td><td>.
Unhapp<span class="inserted">y</span></td><td>&nbsp;that I am, I cannot he</td><td>ave
M</td><td>y heart into my mouth.</td><td>&nbsp;I love your Majesty
A</td><td>ccording to my bond, no more nor lesse</td><td>.
Lear.How, how Cordelia? M</td><td>end your speech a little,
Lea</td><td>st you may marre</td><td>&nbsp;your <span class="inserted">f</span></td><td>ortunes.
Cor</td><td>.
Good</td><td>&nbsp;my&nbsp;Lord.</td><td>
You have begot me, bred me, lov'</td><td>d me.</td><td>
I return</td><td>&nbsp;those duties&nbsp;backe</td><td>&nbsp;as are right fit,
Obey you, L</td><td>ove you, and most honour you.</td><td>
Why have my S</td><td>isters <span class="inserted">h</span></td><td>usbands<span class="inserted">,</span></td><td>&nbsp;if they&nbsp;say
T</td><td>hey love you all?&nbsp;</td><td>Happi</td><td>ly when I shall wed<span class="inserted">.
T</span></td><td>hat Lord,</td><td>&nbsp;whose hand m</td><td>ust take my plight, shall ca</td><td>rry
H</td><td>alfe</td><td>&nbsp;my l</td><td>ove with him, h</td><td>alfe</td><td>&nbsp;my C</td><td>are,</td><td>&nbsp;and D</td><td>ut<span class="inserted">y</span></td><td>,
S</td><td>ure I shall never mar</td><td>r</td><td>y like my S</td><td>isters</td><td>.
Lear.
But goes thy heart</td><td>&nbsp;with this</td><td>?
Cor</td><td>.
I my</td><td>&nbsp;good</td><td>&nbsp;Lord.
Lear.
So you</td><td>ng,</td><td>&nbsp;and so untender?</td><td>
Cor.
So young</td><td>&nbsp;my&nbsp;Lord,</td><td>&nbsp;and&nbsp;true.</td><td>
Lear.
L</td><td>et it be so, thy</td><td>&nbsp;truth then shall </td><td>be thy dowre:</td><td>
For by the sacred radie</td><td>nce of the Sunne</td><td>,
The m<span class="inserted">ysteri</span></td><td>es</td><td>&nbsp;of He</td><td>cat</td><td>&nbsp;and&nbsp;the n</td><td>ight:
By all <span class="inserted">the&nbsp;</span></td><td>operation<span class="inserted">s</span></td><td>&nbsp;of&nbsp;the O</td><td>rbe</td><td>s,
From whom</td><td>&nbsp;we do<span class="inserted">e</span></td><td>&nbsp;exist</td><td>&nbsp;and cease to be,
Hee</td><td>re I desclaime</td><td>&nbsp;all my P</td><td>aternal</td><td>l care,
Propinquity</td><td>&nbsp;and property of bloo</td><td>d,
And as a stranger to my heart and me,</td><td>
Ho</td><td>ld thee from this for ever.</td><td>&nbsp;T</td><td>he b</td><td>arbarous Scythi</td><td>an,
O<span class="inserted">f</span></td><td>&nbsp;he that makes&nbsp;his g</td><td>eneration m</td><td>esses
T</td><td>o gorge his appetite, s</td><td>hall to my bosome
B</td><td>e as well neighbour'd</td><td>&nbsp;pit</td><td>ti</td><td>ed, and rele<span class="inserted">i</span></td><td>v'd,</td><td>
As thou my some</td><td>time D</td><td>aughter.
Kent.
Good</td><td>&nbsp;my Liege.
Lear.
Peace</td><td>&nbsp;Kent<span class="inserted">,
C</span></td><td>ome not betweene</td><td>&nbsp;the Dragon and</td><td>&nbsp;his wrath,</td><td>
I lov'd her most, and thought to set my rest
On her kind</td><td>&nbsp;nursery. H</td><td>ence and avo<span class="inserted">y</span></td><td>d</td><td>&nbsp;my sight:</td><td>
So be my grave my peace,</td><td>&nbsp;as here I give</td><td>
Her F</td><td>ather</td><td>s heart from her:</td><td>&nbsp;call France, who stirre</td><td>s?
Call Burgundy, Cornwa</td><td>ll, and Alban<span class="inserted">y</span></td><td>,
With my two d</td><td>aughters<span class="inserted">, D</span></td><td>owres,</td><td>&nbsp;digest the</td><td>&nbsp;third,
Let pride, which she ca<span class="inserted">l</span></td><td>l</td><td>s plainnesse</td><td>, marry</td><td>&nbsp;her:
I doe</td><td>&nbsp;invest you joi</td><td>ntly with</td><td>&nbsp;my&nbsp;power</td><td>,
Preheminence, and all the large effects
That troope</td><td>&nbsp;with Majesty. O</td><td>ur selfe</td><td>&nbsp;by M</td><td>onthly course,</td><td>
With reservation of an hundred K</td><td>nights,
By you to be sustain</td><td>'d, shall our abode
Make with you one</td><td>&nbsp;due&nbsp;turne</td><td>, one</td><td>ly we shall retaine</td><td>
The name,</td><td>&nbsp;and all th'</td><td>addition</td><td>&nbsp;to a King: t</td><td>he S</td><td>way,
R</td><td>evennew<span class="inserted">,</span></td><td>&nbsp;E</td><td>xecution of the rest,
Beloved S</td><td>onne</td><td>s be yours, which to confirme</td><td>,
This Coronet part betweene</td><td>&nbsp;you.
Kent.
Royal</td><td>l Lear,
Whom I have ever hono</td><td>r'd as my</td><td>&nbsp;King,
Lov'</td><td>d as my Father, as my Master follow'</td><td>d,
As my</td><td>&nbsp;</td><td>Patron</td><td>&nbsp;thought on in my prai</td><td>ers.
Le<span class="inserted">ar</span></td><td>.
The b</td><td>ow is bent <span class="inserted">and</span></td><td>&nbsp;drawne,</td><td>&nbsp;make from the shaft.
Kent.
Let it fall rather, t</td><td>hough the forke</td><td>&nbsp;invade
T</td><td>he region of my heart, b</td><td>e Kent unmannerly,
W</td><td>hen Lear is mad, w</td><td>hat wouldes</td><td>t thou do<span class="inserted">e</span></td><td>&nbsp;o</td><td>ld man?
T</td><td>hink'st thou that d</td><td>ut<span class="inserted">y s</span></td><td>hall have dread to speake
W</td><td>hen p</td><td>ower to f</td><td>lattery</td><td>&nbsp;bowe</td><td>s?</td><td>
To plainnesse</td><td>&nbsp;honour'</td><td>s bound,
W</td><td>hen Majesty fall</td><td>s to folly, r</td><td>eserve thy stat</td><td>e,
A</td><td>nd in&nbsp;</td><td>thy best consideration c</td><td>hecke
T</td><td>his hideous rashnesse</td><td>, answere</td><td>&nbsp;my life, m</td><td>y judgement:
T</td><td>hy yo</td><td>ngest D</td><td>aughter do'</td><td>s not love thee least,
Nor are those empty hearted,</td><td>&nbsp;whose low</td><td>&nbsp;sounds</td><td>
Reverbe</td><td>&nbsp;no hollownes</td><td>se</td><td>.
Lear.
Kent,</td><td>&nbsp;on thy life no more.
Kent.
My life I never held but as <span class="inserted">a </span></td><td>pawne</td><td>
To wage against thine</td><td>&nbsp;enemies, nere</td><td>&nbsp;feare</td><td>&nbsp;to l</td><td>ose it,</td><td>
Thy safe</td><td>ty being</td><td>&nbsp;motive.
Lear.
Out of my sight.
Kent.
See better Lear, and let me still remaine</td><td>
The true blanke</td><td>&nbsp;of thine e<span class="inserted">y</span></td><td>e.
<span class="inserted">L</span></td><td>ear.
Now by Ap</td><td>ollo<span class="inserted">.
K</span></td><td>ent.
Now by Ap</td><td>ollo, King
T</td><td>hou swear<span class="inserted">'</span></td><td>st thy <span class="inserted">g</span></td><td>ods in vaine</td><td>.
Lear.
O&nbsp;</td><td>Vassall! Mis</td><td>creant.
Alb. Cor.
Deare Sir forbeare.
</td><td>Kent.
K</td><td>ill thy Physition, a</td><td>nd thy</td><td>&nbsp;f</td><td>ee bestow
U</td><td>pon the foule</td><td>&nbsp;disease, r</td><td>evoke thy</td><td>&nbsp;gift,
O</td><td>r whil</td><td>st I can vent clamour f</td><td>rom my throat</td><td>,
I</td><td>le</td><td>&nbsp;tell thee thou do</td><td>st evil</td><td>l.
Lea<span class="inserted">r</span></td><td>.
Heare</td><td>&nbsp;me recreant</td><td>, on thine</td><td>&nbsp;alle</td><td>ge</td><td>ance heare</td><td>&nbsp;me;
That</td><td>&nbsp;thou hast fought to make us breake</td><td>&nbsp;our&nbsp;vowes</td><td>,
Which we durst never yet; and with strain'</td><td>d pride,
To come betwixt</td><td>&nbsp;our sentence,</td><td>&nbsp;and our power.</td><td>
Which,</td><td>&nbsp;nor our nature,</td><td>&nbsp;nor our place can beare;</td><td>
Our potenc<span class="inserted">y</span></td><td>&nbsp;made good, take they reward.</td><td>
Fiv</td><td>e daye</td><td>s we do<span class="inserted">e</span></td><td>&nbsp;allot thee for provision,
To shield thee from dis</td><td>aster</td><td>s of the world,
And on the six</td><td>t</td><td>&nbsp;to&nbsp;turne</td><td>&nbsp;thy hated&nbsp;backe</td><td>
Upon our k</td><td>ingdome;</td><td>&nbsp;if</td><td>&nbsp;the tenth day following,
Thy banisht t</td><td>runke</td><td>&nbsp;be found in&nbsp;our D</td><td>ominions,
The moment is thy death, away. B</td><td>y Jupiter,
T</td><td>his shall not be revok'd,</td><td>
Kent.
F</td><td>are thee well K</td><td>ing, sith</td><td>&nbsp;thus thou wilt appeare</td><td>,
Freedome</td><td>&nbsp;lives hence, and banishment is&nbsp;here;</td><td>
The <span class="inserted">g</span></td><td>ods to their de<span class="inserted">are shel</span></td><td>ter</td><td>&nbsp;take thee M</td><td>aid</td><td>,
That justly think'st, and hast mos</td><td>t rightly&nbsp;</td><td>said:</td><td>
And your large speeches,</td><td>&nbsp;may your&nbsp;deede</td><td>s approve,
That good effects may spring from words of love:
Thus Kent,</td><td>&nbsp;O Princes, bids you all ad<span class="inserted">ieu</span></td><td>,
Hee'l</td><td>&nbsp;shape his old course</td><td>&nbsp;in a C</td><td>ountr<span class="inserted">ey</span></td><td>&nbsp;new.&nbsp;Exit.</td></tr><tr><td class="siglum">Q1</td><td><span class="inserted">M. William Shake-speare His History, of</span></td><td>&nbsp;K<span class="inserted">ing</span></td><td>&nbsp;L<span class="inserted">ear</span></td><td>.
Enter Kent, Glo</td><td>ster, and <span class="inserted">Bastar</span></td><td>d.
Kent.
I Thought the King had more affected the Duke of Alban</td><td>y, the</td><td>n Corn</td><td>w<span class="inserted">e</span></td><td>ll.
Glo<span class="inserted">st</span></td><td>.
It did alwa<span class="inserted">ie</span></td><td>s seeme so</td><td>&nbsp;to us<span class="inserted">, b</span></td><td>ut now in the division of the <span class="inserted">k</span></td><td>ingdome<span class="inserted">s</span></td><td>, it appear<span class="inserted">e</span></td><td>s not which of the Dukes he</td><td>&nbsp;val<span class="inserted">u</span></td><td>es most, for <span class="inserted">e</span></td><td>qualities are so weigh<span class="inserted">e</span></td><td>d, that curiosit<span class="inserted">ie</span></td><td>&nbsp;in neither, can make&nbsp;chois</td><td>e of eithers mo<span class="inserted">yt</span></td><td>i<span class="inserted">e</span></td><td>.
Kent.
Is not this your <span class="inserted">s</span></td><td>on<span class="inserted">ne</span></td><td>, my Lord?
Glo<span class="inserted">st</span></td><td>.
His breeding</td><td>&nbsp;<span class="inserted">s</span></td><td>ir</td><td>&nbsp;hath b<span class="inserted">ee</span></td><td>n<span class="inserted">e</span></td><td>&nbsp;at my charge<span class="inserted">,</span></td><td>&nbsp;I have so often blush<span class="inserted">t</span></td><td>&nbsp;to ack</td><td>owledge him, that now I am braz'd to<span class="inserted"> i</span></td><td>t.
Kent.
I cannot conceive you.
Glo<span class="inserted">st</span></td><td>.
Sir, this yo<span class="inserted">u</span></td><td>ng <span class="inserted">f</span></td><td>ellowe</td><td>s m</td><td>other <span class="inserted">C</span></td><td>ould<span class="inserted">,</span></td><td>&nbsp;whereupon sh<span class="inserted">e</span></td><td>e grew round womb<span class="inserted">e</span></td><td>d, and had indeed</td><td>&nbsp;</td><td>Sir</td><td>&nbsp;a <span class="inserted">s</span></td><td>onne</td><td>&nbsp;for her <span class="inserted">c</span></td><td>radle, e</td><td>re she had a h</td><td>usband for her b</td><td>ed<span class="inserted">, d</span></td><td>o<span class="inserted">e</span></td><td>&nbsp;you smell a fault?
Kent.
I cannot wish the fault undone, the issue&nbsp;of it</td><td>&nbsp;being so proper.
Glo<span class="inserted">st</span></td><td>.
But I have <span class="inserted">sir&nbsp;</span></td><td>a <span class="inserted">s</span></td><td>onne</td><td>&nbsp;by order of the Law, some ye<span class="inserted">are</span></td><td>&nbsp;elder the</td><td>n this<span class="inserted">,</span></td><td>&nbsp;who</td><td>&nbsp;yet is no dee</td><td>rer in my account, thou</td><td>gh this <span class="inserted">k</span></td><td>nave came som<span class="inserted">ething</span></td><td>&nbsp;f</td><td>awc<span class="inserted">e</span></td><td>ly to the w</td><td>orld before he<span class="inserted">e</span></td><td>&nbsp;was sent for<span class="inserted">,</span></td><td>&nbsp;yet was his <span class="inserted">m</span></td><td>other fa<span class="inserted">i</span></td><td>re</td><td>, there was good sport at his making, <span class="inserted">&amp;</span></td><td>&nbsp;the <span class="inserted">w</span></td><td>hor<span class="inserted">e</span></td><td>son must be acknowledged<span class="inserted">, do</span></td><td>&nbsp;you know this <span class="inserted">n</span></td><td>oble <span class="inserted">gent</span></td><td>leman, Edmond?
<span class="inserted">
Bast</span></td><td>.
No</td><td>&nbsp;my Lord.
Glo<span class="inserted">st</span></td><td>.
My Lord of Kent<span class="inserted">, r</span></td><td>emember him h</td><td>e</td><td>reafter, as my <span class="inserted">h</span></td><td>onourable <span class="inserted">f</span></td><td>riend.
<span class="inserted">Bast</span></td><td>.
My services to your Lordship.
Kent.
I must love you, and sue to know you better.
<span class="inserted">Bast</span></td><td>.
Sir, I shall study deserving.
Glo<span class="inserted">st</span></td><td>.
H<span class="inserted">e</span></td><td>e hath b<span class="inserted">ee</span></td><td>n<span class="inserted">e</span></td><td>&nbsp;out nine&nbsp;yeare</td><td>s, and away he<span class="inserted">e</span></td><td>&nbsp;shall againe<span class="inserted">, t</span></td><td>he King is com</td><td>ming.
S<span class="inserted">ound a S</span></td><td>ennet<span class="inserted">,</span></td><td>&nbsp;Enter <span class="inserted">one bear</span></td><td>ing<span class="inserted"> a Coronet, then</span></td><td>&nbsp;Lear,<span class="inserted"> then the Dukes of Albany, and</span></td><td>&nbsp;Cornwall, <span class="inserted">next</span></td><td>&nbsp;Gon<span class="inserted">o</span></td><td>rill, Regan, Cordelia, <span class="inserted">with follower</span></td><td>s.
Lear.
Attend the Lords of France <span class="inserted">and</span></td><td>&nbsp;Burgundy, Gloster.
Glo<span class="inserted">st</span></td><td>.
I shall, my L<span class="inserted">iege</span></td><td>.
Lear.
Meane</td><td>&nbsp;time we <span class="inserted">wil</span></td><td>l</td><td>&nbsp;expresse</td><td>&nbsp;our darker purpose<span class="inserted">s</span></td><td>.
<span class="inserted">T</span></td><td>he <span class="inserted">m</span></td><td>ap there<span class="inserted">; k</span></td><td>now</td><td>&nbsp;we have divided
In&nbsp;three</td><td>&nbsp;our <span class="inserted">k</span></td><td>ingdome</td><td>: and&nbsp;</td><td>tis our f<span class="inserted">ir</span></td><td>st intent,
To shake all <span class="inserted">c</span></td><td>ares and <span class="inserted">b</span></td><td>usines</td><td>&nbsp;from our <span class="inserted">stat</span></td><td>e,
Conf<span class="inserted">irm</span></td><td>ing them on yo<span class="inserted">u</span></td><td>nger <span class="inserted">y</span></td><td>ea<span class="inserted">r</span></td><td>es</td><td>,
</td><td>The<span class="inserted"> two great</span></td><td>&nbsp;Princes</td><td>, France <span class="inserted">and</span></td><td>&nbsp;Burgundy,</td><td>
Great <span class="inserted">ry</span></td><td>vals in our yongest d</td><td>aughter</td><td>s l</td><td>ove,
Long in our Court</td><td>&nbsp;have made their amorous sojourne</td><td>,
And h</td><td>ere are to be answer<span class="inserted">d; t</span></td><td>ell me my d</td><td>aughters<span class="inserted">,</span></td><td>
Which of you shall we say doth love us most,
That we, our largest bountie</td><td>&nbsp;may extend<span class="inserted">,</span></td><td>
Where <span class="inserted">merit</span></td><td>&nbsp;doth <span class="inserted">mos</span></td><td>t challenge<span class="inserted"> it:
</span></td><td>Gon<span class="inserted">o</span></td><td>rill<span class="inserted"> o</span></td><td>ur eldest borne</td><td>, speake first<span class="inserted">?</span></td><td>
Gon.
Sir, I love you more the</td><td>n word can w<span class="inserted">ie</span></td><td>ld the matter,
De<span class="inserted">a</span></td><td>rer then e</td><td>ye-sight, space, and libertie</td><td>,
Beyond what can be val<span class="inserted">u</span></td><td>ed</td><td>&nbsp;rich or rare,
No&nbsp;lesse</td><td>&nbsp;the</td><td>n life<span class="inserted">;</span></td><td>&nbsp;with grace, health, beauty, hono<span class="inserted">u</span></td><td>r<span class="inserted">,</span></td><td>
As much a</td><td>&nbsp;<span class="inserted">c</span></td><td>hilde</td><td>&nbsp;e</td><td>re lov<span class="inserted">e</span></td><td>d, or <span class="inserted">f</span></td><td>ather f<span class="inserted">rie</span></td><td>nd<span class="inserted">,</span></td><td>
A love that makes breath poore</td><td>, and speech unable,
Beyond all manner of so much I love you.
Cor.
What shall Cordelia <span class="inserted">doe, l</span></td><td>ove</td><td>&nbsp;and be silent.
Lear.
Of all these bounds<span class="inserted">,</span></td><td>&nbsp;even from this <span class="inserted">l</span></td><td>ine</td><td>&nbsp;to this,
With shad<span class="inserted">y</span></td><td>&nbsp;<span class="inserted">f</span></td><td>orrest</td><td>s, and wide-skirted Meade</td><td>s
We make the</td><td>e</td><td>&nbsp;Lady<span class="inserted">, t</span></td><td>o thine and Albanie</td><td>s i</td><td>ssue<span class="inserted">,
</span></td><td>
Be this perpetual</td><td>l<span class="inserted">, w</span></td><td>hat sa<span class="inserted">i</span></td><td>es our second <span class="inserted">d</span></td><td>aughter?
Our dee</td><td>rest Regan, wife <span class="inserted">to</span></td><td>&nbsp;Cornwall<span class="inserted">, speake?</span></td><td>
Reg.
<span class="inserted">Sir&nbsp;</span></td><td>I am made of that selfe</td><td>-<span class="inserted">same&nbsp;</span></td><td>mett<span class="inserted">all that</span></td><td>&nbsp;my <span class="inserted">s</span></td><td>ister<span class="inserted"> is</span></td><td>,
And prize me at her worth<span class="inserted"> i</span></td><td>n my true heart,
I find she names my very deed</td><td>&nbsp;of love<span class="inserted">, o</span></td><td>ne</td><td>ly she<span class="inserted">e</span></td><td>&nbsp;c<span class="inserted">a</span></td><td>me</td><td>&nbsp;short,<span class="inserted">
T</span></td><td>hat I professe<span class="inserted"> m</span></td><td>y selfe</td><td>&nbsp;an e</td><td>nemy to all other joyes,
Which the most precious square of sense professes,
And find</td><td>&nbsp;I am alone felicitate<span class="inserted">, i</span></td><td>n your deere</td><td>&nbsp;<span class="inserted">h</span></td><td>ighne</td><td>s</td><td>&nbsp;love.
Cor<span class="inserted">d</span></td><td>.
Then poore</td><td>&nbsp;Cord<span class="inserted">.,&nbsp;&amp;</span></td><td>&nbsp;yet not so, since I am sure<span class="inserted">
M</span></td><td>y love's<span class="inserted"> m</span></td><td>ore <span class="inserted">rich</span></td><td>er</td><td>&nbsp;the</td><td>n my tongue.
Lear.
To thee</td><td>&nbsp;and thine hereditarie</td><td>&nbsp;ever</td><td>
Remaine</td><td>&nbsp;this ample third of our&nbsp;faire</td><td>&nbsp;<span class="inserted">k</span></td><td>ingdome</td><td>,
No lesse</td><td>&nbsp;in space, validit<span class="inserted">y</span></td><td>, and pleasure<span class="inserted">,</span></td><td>
The</td><td>n that conf<span class="inserted">irm</span></td><td>'d on Gon<span class="inserted">o</span></td><td>rill<span class="inserted">, but n</span></td><td>ow our <span class="inserted">j</span></td><td>oy,
Although <span class="inserted">the</span></td><td>&nbsp;last<span class="inserted">, not</span></td><td>&nbsp;least<span class="inserted"> in </span></td><td>o<span class="inserted">ur deere</span></td><td>&nbsp;love,
</td><td>What can you say, to <span class="inserted">win a</span></td><td>&nbsp;third, more op<span class="inserted">u</span></td><td>lent<span class="inserted">
T</span></td><td>hen your Sisters?</td><td>
Cor<span class="inserted">d</span></td><td>.
Nothing</td><td>&nbsp;my Lord.
Lear.
<span class="inserted">How, n</span></td><td>othing <span class="inserted">can</span></td><td>&nbsp;come of nothing, speake</td><td>&nbsp;againe</td><td>.
Cor<span class="inserted">d</span></td><td>.
Unhapp<span class="inserted">y</span></td><td>&nbsp;that I am, I cannot he</td><td>ave<span class="inserted"> m</span></td><td>y heart into my mouth<span class="inserted">,</span></td><td>&nbsp;I love your Majesty<span class="inserted"> a</span></td><td>ccording to my bond, no more nor lesse</td><td>.
Lear.<span class="inserted">Goe too, goe too, m</span></td><td>end your speech a little,
Lea</td><td>st you may mar</td><td>&nbsp;your <span class="inserted">f</span></td><td>ortunes.
Cor<span class="inserted">d</span></td><td>.
Good</td><td>&nbsp;my Lord<span class="inserted">,</span></td><td>
You have begot me, bred me, lov<span class="inserted">e</span></td><td>d me<span class="inserted">,</span></td><td>
I return<span class="inserted">e</span></td><td>&nbsp;those duties&nbsp;backe</td><td>&nbsp;as are right fit,
Obey you, <span class="inserted">l</span></td><td>ove you, and most honour you<span class="inserted">,</span></td><td>
Why have my <span class="inserted">s</span></td><td>isters <span class="inserted">h</span></td><td>usbands<span class="inserted">,</span></td><td>&nbsp;if they say<span class="inserted"> t</span></td><td>hey love you all?<span class="inserted">
</span></td><td>Happ<span class="inserted">e</span></td><td>ly when I shall wed,<span class="inserted"> t</span></td><td>hat Lord</td><td>&nbsp;whose hand<span class="inserted">
M</span></td><td>ust take my plight, shall ca</td><td>r<span class="inserted">y h</span></td><td>alfe</td><td>&nbsp;my l</td><td>ove with him,<span class="inserted">
H</span></td><td>alfe</td><td>&nbsp;my <span class="inserted">c</span></td><td>are</td><td>&nbsp;and <span class="inserted">d</span></td><td>ut<span class="inserted">y</span></td><td>,<span class="inserted"> s</span></td><td>ure I shall never<span class="inserted">
Ma</span></td><td>r</td><td>y like my <span class="inserted">s</span></td><td>isters<span class="inserted">, to love my father all</span></td><td>.
Lear.
But goes th<span class="inserted">is</span></td><td>&nbsp;with th<span class="inserted">y&nbsp;heart</span></td><td>?
Cor<span class="inserted">d</span></td><td>.
I</td><td>&nbsp;good<span class="inserted"> my</span></td><td>&nbsp;Lord.
Lear.
So yo</td><td>ng</td><td>&nbsp;and so untender<span class="inserted">.</span></td><td>
Cor.
So young</td><td>&nbsp;my Lord</td><td>&nbsp;and&nbsp;true.</td><td>
Lear.
<span class="inserted">Well l</span></td><td>et it be so, thy</td><td>&nbsp;truth&nbsp;then </td><td>be thy dow<span class="inserted">er,</span></td><td>
For by the sacred radie</td><td>nce of the Sunne</td><td>,
The mis<span class="inserted">tr</span></td><td>es<span class="inserted">se</span></td><td>&nbsp;of Hec</td><td>cat</td><td>&nbsp;and the <span class="inserted">m</span></td><td>ight:
By all&nbsp;</td><td>operation</td><td>&nbsp;of the <span class="inserted">o</span></td><td>rb</td><td>s,
From whom<span class="inserted">e</span></td><td>&nbsp;we do<span class="inserted">e</span></td><td>&nbsp;exist</td><td>&nbsp;and cease to be,
Hee</td><td>re I desclaime</td><td>&nbsp;all my <span class="inserted">p</span></td><td>aternal</td><td>l care,
Propinquit<span class="inserted">ie</span></td><td>&nbsp;and property of blo<span class="inserted">u</span></td><td>d,
And as a stranger to my heart and me</td><td>
Ho<span class="inserted">u</span></td><td>ld thee from this for ever.</td><td>&nbsp;<span class="inserted">t</span></td><td>he b</td><td>arbarous Scyth<span class="inserted">y</span></td><td>an,
Or</td><td>&nbsp;he that makes&nbsp;his g</td><td>eneration<span class="inserted">
M</span></td><td>esses<span class="inserted"> t</span></td><td>o gorge his appetite<span class="inserted">
S</span></td><td>hall <span class="inserted">b</span></td><td>e as well neighbour'd<span class="inserted">,</span></td><td>&nbsp;pi</td><td>t<span class="inserted">ty</span></td><td>ed, and rel<span class="inserted">ie</span></td><td>v<span class="inserted">ed</span></td><td>
As thou my some</td><td>time <span class="inserted">d</span></td><td>aughter.
Kent.
Good</td><td>&nbsp;my Liege.
Lear.
Peace</td><td>&nbsp;Kent<span class="inserted">, c</span></td><td>ome not between</td><td>&nbsp;the Dragon <span class="inserted">&amp;</span></td><td>&nbsp;his&nbsp;wrath</td><td>
I lov'd her most, and thought to set my rest
On her kind<span class="inserted">e</span></td><td>&nbsp;nursery<span class="inserted">, h</span></td><td>ence and avoi</td><td>d<span class="inserted">e</span></td><td>&nbsp;my sight<span class="inserted">?</span></td><td>
So be my grave my peace</td><td>&nbsp;as here I give<span class="inserted">,</span></td><td>
Her <span class="inserted">f</span></td><td>ather</td><td>s heart from her<span class="inserted">,</span></td><td>&nbsp;call France, who stirre</td><td>s?
Call Burgundy, Cornw<span class="inserted">e</span></td><td>ll, and Alban<span class="inserted">y</span></td><td>,
With my two d</td><td>aughters<span class="inserted"> d</span></td><td>ow<span class="inserted">er</span></td><td>&nbsp;digest th<span class="inserted">is</span></td><td>&nbsp;third,
Let pride, which she ca</td><td>l</td><td>s plainnes</td><td>, marr<span class="inserted">ie</span></td><td>&nbsp;her:
I doe</td><td>&nbsp;invest you jo<span class="inserted">y</span></td><td>ntly <span class="inserted">in</span></td><td>&nbsp;my pow<span class="inserted">re</span></td><td>,
Preheminence, and all the large effects
That troope</td><td>&nbsp;with Majest<span class="inserted">ie, o</span></td><td>ur selfe</td><td>&nbsp;by M</td><td>onthly course</td><td>
With reservation of an hundred <span class="inserted">k</span></td><td>nights,
By you to be susta<span class="inserted">ny</span></td><td>'d, shall our abode
Make with you <span class="inserted">by</span></td><td>&nbsp;due turn<span class="inserted">es</span></td><td>, one</td><td>ly we shall retaine</td><td>
The name</td><td>&nbsp;and all th<span class="inserted">e </span></td><td>addition<span class="inserted">s</span></td><td>&nbsp;to a King<span class="inserted">,
T</span></td><td>he <span class="inserted">s</span></td><td>way,<span class="inserted"> r</span></td><td>even<span class="inserted">ue,</span></td><td>&nbsp;<span class="inserted">e</span></td><td>xecution of the rest,
Beloved <span class="inserted">s</span></td><td>onne</td><td>s be yours, which to confirme</td><td>,
This Coronet part betw<span class="inserted">ixt</span></td><td>&nbsp;you.
Kent.
Royal</td><td>l Lear,
Whom I have ever hono</td><td>r'd as my</td><td>&nbsp;King,
Lov<span class="inserted">e</span></td><td>d as my Father, as my Master follow<span class="inserted">e</span></td><td>d,
As my<span class="inserted"> great</span></td><td>&nbsp;</td><td>Patron</td><td>&nbsp;thought on in my pra<span class="inserted">y</span></td><td>ers.
Le<span class="inserted">ar</span></td><td>.
The b</td><td>ow is bent &amp;</td><td>&nbsp;draw<span class="inserted">en</span></td><td>&nbsp;make from the shaft.
Kent.
Let it fall rather,<span class="inserted">
T</span></td><td>hough the forke</td><td>&nbsp;invade<span class="inserted"> t</span></td><td>he region of my heart,<span class="inserted">
B</span></td><td>e Kent unmannerly<span class="inserted"> w</span></td><td>hen Lear is ma<span class="inserted">n,
W</span></td><td>hat w<span class="inserted">il</span></td><td>t thou do<span class="inserted">e</span></td><td>&nbsp;o<span class="inserted">u</span></td><td>ld man<span class="inserted">, t</span></td><td>hink'st thou that d</td><td>utie<span class="inserted">
S</span></td><td>hall have dread to speake,<span class="inserted"> w</span></td><td>hen p</td><td>ower to f</td><td>lattery<span class="inserted">ie</span></td><td>&nbsp;bowe</td><td>s<span class="inserted">,</span></td><td>
To plainnes</td><td>&nbsp;honour</td><td>s bound<span class="inserted"> w</span></td><td>hen Majesty <span class="inserted">stoop</span></td><td>s to folly,<span class="inserted">
R</span></td><td>eserve thy <span class="inserted">doom</span></td><td>e,<span class="inserted"> a</span></td><td>nd in&nbsp;</td><td>thy best consideration<span class="inserted">
C</span></td><td>hecke<span class="inserted"> t</span></td><td>his hideous rashnes</td><td>, answere</td><td>&nbsp;my life<span class="inserted">
M</span></td><td>y judgement<span class="inserted">, t</span></td><td>hy yo</td><td>ngest <span class="inserted">d</span></td><td>aughter do<span class="inserted">e</span></td><td>s not love thee least,
Nor are those empty hearted</td><td>&nbsp;whose low<span class="inserted">,</span></td><td>&nbsp;sound</td><td>
Reverb<span class="inserted">s</span></td><td>&nbsp;no hollowne</td><td>s</td><td>.
Lear.
Kent</td><td>&nbsp;on thy life no more.
Kent.
My life I never held but as <span class="inserted">a </span></td><td>pawne</td><td>
To wage against th<span class="inserted">y</span></td><td>&nbsp;enemies, n<span class="inserted">or</span></td><td>&nbsp;feare</td><td>&nbsp;to l</td><td>ose it</td><td>
Thy saf</td><td>ty being<span class="inserted"> the</span></td><td>&nbsp;motive.
Lear.
Out of my sight.
Kent.
See better Lear, and let me still remaine</td><td>
The true blanke</td><td>&nbsp;of thine e<span class="inserted">y</span></td><td>e.
<span class="inserted">L</span></td><td>ear.
Now by Ap<span class="inserted">p</span></td><td>ollo<span class="inserted">,
K</span></td><td>ent.
Now by Ap<span class="inserted">p</span></td><td>ollo, King<span class="inserted"> t</span></td><td>hou swear<span class="inserted">'</span></td><td>st thy G</td><td>ods in vaine</td><td>.
Lear.
</td><td>Vassall<span class="inserted">, re</span></td><td>creant.
</td><td>Kent.
<span class="inserted">Doe, k</span></td><td>ill thy Physi<span class="inserted">cion
A</span></td><td>nd th<span class="inserted">e</span></td><td>&nbsp;f</td><td>ee bestow<span class="inserted"> u</span></td><td>pon the foule</td><td>&nbsp;disease,<span class="inserted">
R</span></td><td>evoke thy</td><td>&nbsp;<span class="inserted">doome, o</span></td><td>r whil</td><td>st I can vent clamour<span class="inserted">
F</span></td><td>rom my throat</td><td>,<span class="inserted"> i</span></td><td>le</td><td>&nbsp;tell thee thou do</td><td>st evil</td><td>l.
Lea<span class="inserted">r</span></td><td>.
Heare</td><td>&nbsp;me</td><td>, on th<span class="inserted">y</span></td><td>&nbsp;alle</td><td>ge</td><td>ance heare</td><td>&nbsp;me<span class="inserted">?
Since</span></td><td>&nbsp;thou hast fought to make us breake</td><td>&nbsp;our vow</td><td>,
Which we durst never yet; and with strai<span class="inserted">e</span></td><td>d pride,
To come betw<span class="inserted">eene</span></td><td>&nbsp;our sentence</td><td>&nbsp;and our pow<span class="inserted">re,</span></td><td>
Which</td><td>&nbsp;nor our nature</td><td>&nbsp;nor our place can beare<span class="inserted">,</span></td><td>
Our potenc<span class="inserted">y</span></td><td>&nbsp;made good, take they reward<span class="inserted">,</span></td><td>
F<span class="inserted">our</span></td><td>e daye</td><td>s we do<span class="inserted">e</span></td><td>&nbsp;allot thee for provision,
To shield thee from dis<span class="inserted">e</span></td><td>as<span class="inserted">e</span></td><td>s of the world,
And on the <span class="inserted">fif</span></td><td>t</td><td>&nbsp;to&nbsp;turne</td><td>&nbsp;thy hated&nbsp;backe</td><td>
Upon our k</td><td>ingdome<span class="inserted">,</span></td><td>&nbsp;if on</td><td>&nbsp;the tenth day following,
Thy banisht t</td><td>runke</td><td>&nbsp;be found in our <span class="inserted">d</span></td><td>ominions,
The moment is thy death, away<span class="inserted">, b</span></td><td>y Jupiter,
T</td><td>his shall not be revok<span class="inserted">t.</span></td><td>
Kent.
<span class="inserted">Why f</span></td><td>are thee well <span class="inserted">k</span></td><td>ing, si<span class="inserted">nce</span></td><td>&nbsp;thus thou wilt appeare</td><td>,
Fr<span class="inserted">iendship</span></td><td>&nbsp;lives hence, and banishment is here<span class="inserted">,</span></td><td>
The G</td><td>ods to their <span class="inserted">pro</span></td><td>te<span class="inserted">ction</span></td><td>&nbsp;take the<span class="inserted"> m</span></td><td>aid<span class="inserted">e</span></td><td>,
Tha</td><td>t rightly <span class="inserted">thinkes, and hath most justly&nbsp;</span></td><td>said<span class="inserted">,</span></td><td>
And your large speeches</td><td>&nbsp;may your deed<span class="inserted">e</span></td><td>s approve,
That good effects may spring from words of love:
Thus Kent</td><td>&nbsp;O Princes, bids you all adew</td><td>,
Hee<span class="inserted">le</span></td><td>&nbsp;shape his old course</td><td>&nbsp;in a <span class="inserted">c</span></td><td>ountr<span class="inserted">ie</span></td><td>&nbsp;new.</td></tr><tr><td class="siglum">F4</td><td>THE TRAGEDIE OF</td><td>&nbsp;KING</td><td>&nbsp;LEAR
Actus Primus. Scena&nbsp;Prima</td><td>.
Enter Kent, Glouce</td><td>ster, and Edmon</td><td>d.
Kent.
I Thought the King had more affected the Duke of Alban</td><td>y, th<span class="inserted">a</span></td><td>n Corn</td><td>wa</td><td>ll.
Glou</td><td>.
It did alway</td><td>s seem</td><td>&nbsp;to&nbsp;us: B</td><td>ut now in the division of the K</td><td>ingdom</td><td>, it appear</td><td>s not which of the Dukes hee</td><td>&nbsp;val<span class="inserted">u</span></td><td>es most, for&nbsp;</td><td>qualities are so weigh'</td><td>d, that curiosity</td><td>&nbsp;in neither, can make choi<span class="inserted">c</span></td><td>e of eithers mo</td><td>i<span class="inserted">ety</span></td><td>.
Kent.
Is not this your <span class="inserted">s</span></td><td>on</td><td>, my Lord?
Glou</td><td>.
His breeding<span class="inserted">,</span></td><td>&nbsp;S</td><td>ir,</td><td>&nbsp;hath b<span class="inserted">ee</span></td><td>n</td><td>&nbsp;at my charge.</td><td>&nbsp;I have so often blush'd</td><td>&nbsp;to ack</td><td>owledge him, that now I am braz'd to'</td><td>t.
Kent.
I cannot conceive you.
Glou</td><td>.
Sir, this yo<span class="inserted">u</span></td><td>ng F</td><td>ellow</td><td>s m</td><td>other c</td><td>ould;</td><td>&nbsp;whereupon sh</td><td>e grew round womb'</td><td>d, and had indeed</td><td>&nbsp;(</td><td>Sir)</td><td>&nbsp;a S</td><td>on</td><td>&nbsp;for&nbsp;her C</td><td>radle, e<span class="inserted">'</span></td><td>re she had a <span class="inserted">H</span></td><td>usband for her b</td><td>ed. D</td><td>o</td><td>&nbsp;you smell a fault?
Kent.
I cannot wish the fault undone, the issue of it,</td><td>&nbsp;being so proper.
Glou</td><td>.
But I have&nbsp;</td><td>a S</td><td>on, Sir,</td><td>&nbsp;by order of the Law, some <span class="inserted">Year</span></td><td>&nbsp;elder th<span class="inserted">a</span></td><td>n this;</td><td>&nbsp;who,</td><td>&nbsp;yet is no de<span class="inserted">a</span></td><td>rer in my account, thou</td><td>gh this K</td><td>nave came som<span class="inserted">ewhat</span></td><td>&nbsp;f</td><td>awci</td><td>ly to the <span class="inserted">W</span></td><td>orld before he</td><td>&nbsp;was sent for:</td><td>&nbsp;yet was&nbsp;his M</td><td>other fa<span class="inserted">i</span></td><td>r</td><td>, there was good sport at his making, and</td><td>&nbsp;the <span class="inserted">w</span></td><td>hor</td><td>son must be acknowledged. Do</td><td>&nbsp;you know this N</td><td>ob</td><td>leman, Edmond?
Edm</td><td>.
No,</td><td>&nbsp;my Lord.
Glou</td><td>.
My Lord of Kent:
R</td><td>emember him h</td><td>e</td><td>reafter, as my <span class="inserted">h</span></td><td>onourable F</td><td>riend.
Edm</td><td>.
My services to your Lordship.
Kent.
I must love you, and sue to know you better.
Edm</td><td>.
Sir, I shall study deserving.
Glou</td><td>.
H</td><td>e hath b<span class="inserted">ee</span></td><td>n</td><td>&nbsp;out nine year</td><td>s, and away he</td><td>&nbsp;shall again. T</td><td>he King is co</td><td>ming.
S</td><td>ennet.</td><td>&nbsp;Enter K</td><td>ing</td><td>&nbsp;Lear,</td><td>&nbsp;Cornwall, Albany,</td><td>&nbsp;Gone</td><td>rill, Regan, Cordelia, and <span class="inserted">Attendant</span></td><td>s.
Lear.
Attend the Lords of France &amp;</td><td>&nbsp;Burgundy, Gloster.
Glou</td><td>.
I shall, my Lord.Exit</td><td>.
Lear.
Mean</td><td>&nbsp;time we sha</td><td>l<span class="inserted">l</span></td><td>&nbsp;express</td><td>&nbsp;our darker purpose</td><td>.
Give me t</td><td>he M</td><td>ap there. K</td><td>now, that</td><td>&nbsp;we have divided
In&nbsp;three</td><td>&nbsp;our K</td><td>ingdom</td><td>: and '</td><td>tis our fa</td><td>st intent,
To shake all <span class="inserted">c</span></td><td>ares and <span class="inserted">b</span></td><td>usinesse</td><td>&nbsp;from our Ag</td><td>e,
Conferr</td><td>ing them on yo<span class="inserted">u</span></td><td>nger strengths, while we
Unburthen'd crawl toward d</td><td>eath. Our son of Cornwal<span class="inserted">l,
And you our no l</span></td><td>esse loving Son of Albany</td><td>,
We have this hour a constant will to publish
Our <span class="inserted">Daughter<span class="inserted">'s several Dowers, that future strife
May be prevented now.&nbsp;</span></span></td><td>The</td><td>&nbsp;Prince</td><td>, France &amp;</td><td>&nbsp;Burgundy,</td><td>
Great Ri</td><td>vals in our yongest <span class="inserted">D</span></td><td>aughter<span class="inserted">'</span></td><td>s <span class="inserted">l</span></td><td>ove,
Long in our Court,</td><td>&nbsp;have made their amorous sojourn</td><td>,
And h</td><td>ere are to be answer'd. T</td><td>ell me my <span class="inserted">D</span></td><td>aughters
(Since now we will divest us both of Rule,
Interest of Territory, Cares of State)</td><td>
Which of you shall we say doth love us most,
That we, our largest bount<span class="inserted">y</span></td><td>&nbsp;may extend</td><td>
Where <span class="inserted">nature</span></td><td>&nbsp;doth with meri</td><td>t challenge.&nbsp;</td><td>Gone</td><td>rill,
O</td><td>ur eldest borne</td><td>, speake first.</td><td>
Gon.
Sir, I love you more th<span class="inserted">a</span></td><td>n word can w<span class="inserted">ie</span></td><td>ld the matter,
De<span class="inserted">a</span></td><td>rer then <span class="inserted">E</span></td><td>ye-sight, space, and libert<span class="inserted">y</span></td><td>,
Beyond what can be val<span class="inserted">u</span></td><td>ed,</td><td>&nbsp;rich or rare,
No&nbsp;lesse</td><td>&nbsp;th<span class="inserted">a</span></td><td>n life,</td><td>&nbsp;with grace, health, beauty, hono<span class="inserted">u</span></td><td>r:</td><td>
As much as</td><td>&nbsp;C</td><td>hild</td><td>&nbsp;e<span class="inserted">'</span></td><td>re lov'</td><td>d, or F</td><td>ather fou</td><td>nd.</td><td>
A love that makes breath poor</td><td>, and speech unable,
Beyond all manner of so much I love you.
Cor.
What shall Cordelia speak? L</td><td>ove,</td><td>&nbsp;and be silent.
Lear.
Of all these bounds</td><td>&nbsp;even from this L</td><td>ine,</td><td>&nbsp;to this,
With shadow<span class="inserted">y</span></td><td>&nbsp;F</td><td>orrests, and with Champ<span class="inserted">ions rich'd 
With plenteous River</span></td><td>s, and wide-skirted Mead</td><td>s
We make th</td><td>e<span class="inserted">e</span></td><td>&nbsp;Lady. T</td><td>o thine and Alban<span class="inserted">y'</span></td><td>s <span class="inserted">I</span></td><td>ssues</td><td>
Be this perpetua</td><td>l. W</td><td>hat say</td><td>es our second D</td><td>aughter?
Our de<span class="inserted">a</span></td><td>rest Regan, wife of</td><td>&nbsp;Cornwall?</td><td>
Reg.
</td><td>I am made of that self</td><td>-</td><td>met<span class="inserted">al as</span></td><td>&nbsp;my <span class="inserted">S</span></td><td>ister</td><td>,
And prize me at her worth. I</td><td>n my true heart,
I find she names my very deed</td><td>&nbsp;of love:
O</td><td>n</td><td>ly she</td><td>&nbsp;co</td><td>mes too</td><td>&nbsp;short, t</td><td>hat I profess
M</td><td>y self</td><td>&nbsp;an <span class="inserted">E</span></td><td>nemy to all other joyes,
Which the most precious square of sense professes,
And find</td><td>&nbsp;I am alone felicitate
I</td><td>n your de<span class="inserted">ar</span></td><td>&nbsp;H</td><td>ighnes</td><td>s<span class="inserted">e</span></td><td>&nbsp;love.
Cor</td><td>.
Then poor</td><td>&nbsp;Cordelia,
And</td><td>&nbsp;yet not so, since I am sure m</td><td>y love's
M</td><td>ore pond</td><td>erous</td><td>&nbsp;th<span class="inserted">a</span></td><td>n my tongue.
Lear.
To thee,</td><td>&nbsp;and thine hereditar<span class="inserted">y</span></td><td>&nbsp;ever,</td><td>
Remain</td><td>&nbsp;this ample third of our fair</td><td>&nbsp;K</td><td>ingdom</td><td>,
No less</td><td>&nbsp;in space, validit<span class="inserted">y</span></td><td>, and pleasure</td><td>
Th<span class="inserted">a</span></td><td>n that conferr</td><td>'d on Gone</td><td>rill. N</td><td>ow our J</td><td>oy,
Although our</td><td>&nbsp;last and</td><td>&nbsp;least; to whose y</td><td>o<span class="inserted">ung</span></td><td>&nbsp;love,
The Vines of France, and Milke of Burgund<span class="inserted">y,
Strive to be interest. </span></td><td>What can you say, to draw
A</td><td>&nbsp;third, more op<span class="inserted">u</span></td><td>lent t</td><td>hen your Sisters? <span class="inserted">speak.</span></td><td>
Cor</td><td>.
Nothing<span class="inserted">,</span></td><td>&nbsp;my Lord.
Lear.
Nothing?
Cor.
Nothing.
Lear.
N</td><td>othing will</td><td>&nbsp;come of nothing,&nbsp;speak</td><td>&nbsp;again</td><td>.
Cor</td><td>.
Unhapp<span class="inserted">y</span></td><td>&nbsp;that I am, I cannot h</td><td>ave
M</td><td>y heart into my mouth.</td><td>&nbsp;I love your Majesty
A</td><td>ccording to my bond, no more nor less</td><td>.
Lear.How, how Cordelia? M</td><td>end your speech a little,
<span class="inserted">
La</span></td><td>st you may marr</td><td>&nbsp;your <span class="inserted">f</span></td><td>ortunes.
Cor</td><td>.
Good<span class="inserted">,</span></td><td>&nbsp;my&nbsp;Lord.</td><td>
You have begot me, bred me, lov'</td><td>d me.</td><td>
I return</td><td>&nbsp;those duties back</td><td>&nbsp;as are right fit,
Obey you, <span class="inserted">l</span></td><td>ove you, and most honour you.</td><td>
Why have my S</td><td>isters <span class="inserted">h</span></td><td>usbands<span class="inserted">,</span></td><td>&nbsp;if they&nbsp;say
T</td><td>hey love you all?&nbsp;</td><td>Happi</td><td>ly when I shall wed,
T</td><td>hat Lord,</td><td>&nbsp;whose hand m</td><td>ust take my plight, shall ca</td><td>rry
H</td><td>alf</td><td>&nbsp;my l</td><td>ove with him, h</td><td>alf</td><td>&nbsp;my C</td><td>are,</td><td>&nbsp;and D</td><td>ut<span class="inserted">y</span></td><td>,
S</td><td>ure I shall never mar</td><td>r</td><td>y like my S</td><td>isters</td><td>.
Lear.
But goes thy heart</td><td>&nbsp;with this</td><td>?
Cor</td><td>.
I my</td><td>&nbsp;good</td><td>&nbsp;Lord.
Lear.
So you</td><td>ng,</td><td>&nbsp;and so untender?</td><td>
Cor.
So young<span class="inserted">,</span></td><td>&nbsp;my&nbsp;Lord,</td><td>&nbsp;and true<span class="inserted">?</span></td><td>
Lear.
L</td><td>et it be so, th<span class="inserted">e</span></td><td>&nbsp;truth then shall </td><td>be thy dowre:</td><td>
For by the sacred radi<span class="inserted">a</span></td><td>nce of the Sun</td><td>,
The m<span class="inserted">ysteri</span></td><td>es</td><td>&nbsp;of He</td><td>cat<span class="inserted">e,</span></td><td>&nbsp;and&nbsp;the n</td><td>ight:
By all <span class="inserted">the&nbsp;</span></td><td>operation<span class="inserted">s</span></td><td>&nbsp;of&nbsp;the O</td><td>rb</td><td>s,
From whom</td><td>&nbsp;we do</td><td>&nbsp;exist<span class="inserted">,</span></td><td>&nbsp;and cease to be,
He</td><td>re I desclaim</td><td>&nbsp;all my P</td><td>aterna</td><td>l care,
Propinquity</td><td>&nbsp;and property of bloo</td><td>d,
And as a stranger to my heart and me,</td><td>
Ho</td><td>ld thee from this for ever.</td><td>&nbsp;T</td><td>he <span class="inserted">B</span></td><td>arbarous Scythi</td><td>an,
Or</td><td>&nbsp;he that makes his <span class="inserted">G</span></td><td>eneration <span class="inserted">M</span></td><td>esses
T</td><td>o gorge his appetite, s</td><td>hall to my bosom
B</td><td>e as well neighbour'd<span class="inserted">,</span></td><td>&nbsp;pi</td><td>ti</td><td>ed, and rel<span class="inserted">ie</span></td><td>v'd,</td><td>
As thou my some</td><td>time D</td><td>aughter.
Kent.
Good</td><td>&nbsp;my Liege.
Lear.
Peace<span class="inserted">,</span></td><td>&nbsp;Kent.
C</td><td>ome not between</td><td>&nbsp;the Dragon and</td><td>&nbsp;his wrath,</td><td>
I lov'd her most, and thought to set my rest
On her kind</td><td>&nbsp;nursery. H</td><td>ence and avoi</td><td>d</td><td>&nbsp;my sight:</td><td>
So be my grave my peace,</td><td>&nbsp;as here I give</td><td>
Her F</td><td>ather<span class="inserted">'</span></td><td>s heart from her:</td><td>&nbsp;call France, who stir</td><td>s?
Call Burgundy, Cornwa</td><td>ll, and Alban<span class="inserted">y</span></td><td>,
With my two <span class="inserted">D</span></td><td>aughters D</td><td>owres,</td><td>&nbsp;digest the</td><td>&nbsp;third,
Let pride, which she ca</td><td>l<span class="inserted">l</span></td><td>s plainness</td><td>, marry</td><td>&nbsp;her:
I do</td><td>&nbsp;invest you joi</td><td>ntly with</td><td>&nbsp;my&nbsp;power</td><td>,
Preheminence, and all the large effects
That troop</td><td>&nbsp;with Majesty. O</td><td>ur self</td><td>&nbsp;by <span class="inserted">m</span></td><td>onthly course,</td><td>
With reservation of an hundred K</td><td>nights,
By you to be sustain</td><td>'d, shall our abode
Make with you <span class="inserted">by</span></td><td>&nbsp;due turn<span class="inserted">e</span></td><td>, on</td><td>ly we shall retain</td><td>
The name,</td><td>&nbsp;and all th'</td><td>addition</td><td>&nbsp;to a King: t</td><td>he S</td><td>way,
R</td><td>even<span class="inserted">ue,</span></td><td>&nbsp;E</td><td>xecution of the rest,
Beloved S</td><td>on</td><td>s be yours, which to confirm</td><td>,
This Coronet part between</td><td>&nbsp;you.
Kent.
Roya</td><td>l Lear,
Whom I have ever hono<span class="inserted">u</span></td><td>r'd as <span class="inserted">a</span></td><td>&nbsp;King,
Lov'</td><td>d as my Father, as my Master follow'</td><td>d,
As my</td><td>&nbsp;</td><td>Patron<span class="inserted">,</span></td><td>&nbsp;thought on in my pra<span class="inserted">y</span></td><td>ers.
Le<span class="inserted">ar</span></td><td>.
The <span class="inserted">b</span></td><td>ow is bent <span class="inserted">and</span></td><td>&nbsp;drawn,</td><td>&nbsp;make from the shaft.
Kent.
Let it fall rather, t</td><td>hough the fork</td><td>&nbsp;invade
T</td><td>he region of my heart, b</td><td>e Kent unmannerly,
W</td><td>hen Lear is mad, w</td><td>hat wouldes</td><td>t thou do<span class="inserted">,</span></td><td>&nbsp;o</td><td>ld man?
T</td><td>hink'st thou that <span class="inserted">D</span></td><td>ut<span class="inserted">y s</span></td><td>hall have dread to speak,
W</td><td>hen <span class="inserted">P</span></td><td>ower to <span class="inserted">F</span></td><td>lattery</td><td>&nbsp;bow</td><td>s?</td><td>
To plainness</td><td>&nbsp;honour'</td><td>s bound,
W</td><td>hen Majesty fall</td><td>s to folly, r</td><td>eserve thy stat</td><td>e,
A</td><td>nd&nbsp;</td><td>thy best consideration<span class="inserted">, c</span></td><td>heck
T</td><td>his hideous rashness</td><td>, answer</td><td>&nbsp;my life, m</td><td>y judgement:
T</td><td>hy yo<span class="inserted">u</span></td><td>ngest D</td><td>aughter do'</td><td>s not love thee least,
Nor are those empty hearted,</td><td>&nbsp;whose low</td><td>&nbsp;sounds</td><td>
Reverb</td><td>&nbsp;no hollownes</td><td>s</td><td>.
Lear.
Kent,</td><td>&nbsp;on thy life no more.
Kent.
My life I never held but as <span class="inserted">a </span></td><td>pawn</td><td>
To wage against thine</td><td>&nbsp;enemies, ne<span class="inserted">'er</span></td><td>&nbsp;fear</td><td>&nbsp;to l</td><td>ose it,</td><td>
Thy safe</td><td>ty being</td><td>&nbsp;motive.
Lear.
Out of my sight.
Kent.
See better Lear, and let me still remain</td><td>
The true blank</td><td>&nbsp;of thine e<span class="inserted">y</span></td><td>e.
<span class="inserted">L</span></td><td>ear.
Now by Ap</td><td>ollo<span class="inserted">.
K</span></td><td>ent.
Now by Ap</td><td>ollo, King<span class="inserted">,
T</span></td><td>hou swear<span class="inserted">'</span></td><td>st thy <span class="inserted">g</span></td><td>ods in vain</td><td>.
Lear.
O&nbsp;</td><td>Vassal! Mis</td><td>creant.
Alb. Cor.
Dear Sir<span class="inserted">, forbear.
</span></td><td>Kent.
K</td><td>ill thy Physi<span class="inserted">cian, a</span></td><td>nd th<span class="inserted">ey</span></td><td>&nbsp;<span class="inserted">F</span></td><td>ee bestow
U</td><td>pon the foul</td><td>&nbsp;disease, r</td><td>evoke th<span class="inserted">e</span></td><td>&nbsp;gift,
O</td><td>r whil<span class="inserted">'</span></td><td>st I can vent clamour f</td><td>rom my throat</td><td>,
I<span class="inserted">'</span></td><td>l<span class="inserted">l</span></td><td>&nbsp;tell thee thou do<span class="inserted">'</span></td><td>st evi</td><td>l.
Lea<span class="inserted">r</span></td><td>.
Hear</td><td>&nbsp;me <span class="inserted">Recreant</span></td><td>, on thine</td><td>&nbsp;alle</td><td>g<span class="inserted">i</span></td><td>ance hear</td><td>&nbsp;me;
That</td><td>&nbsp;thou hast fought to make us&nbsp;break</td><td>&nbsp;our vows</td><td>,
Which we durst never yet; and with strain'</td><td>d pride,
To come betwixt</td><td>&nbsp;our sentence</td><td>&nbsp;and our power.</td><td>
Which,</td><td>&nbsp;nor our nature,</td><td>&nbsp;nor our place can&nbsp;bear;</td><td>
Our potenc<span class="inserted">y</span></td><td>&nbsp;made good, take they reward.</td><td>
Fiv</td><td>e day</td><td>s we do</td><td>&nbsp;allot thee for provision,
To shield thee from dis</td><td>aster</td><td>s of the world,
And on the six</td><td>t<span class="inserted">h</span></td><td>&nbsp;to turn</td><td>&nbsp;thy hated back</td><td>
Upon our <span class="inserted">K</span></td><td>ingdom;</td><td>&nbsp;if on</td><td>&nbsp;the tenth day following,
Thy banisht <span class="inserted">T</span></td><td>runk</td><td>&nbsp;be found in&nbsp;our D</td><td>ominions,
The moment is thy death, away. B</td><td>y Jupiter,
T</td><td>his shall not be revok'd<span class="inserted">.</span></td><td>
Kent.
F</td><td>are thee well K</td><td>ing, sith</td><td>&nbsp;thus thou wilt appear</td><td>,
Freedom</td><td>&nbsp;lives hence, and banishment is&nbsp;here;</td><td>
The <span class="inserted">g</span></td><td>ods to their de<span class="inserted">ar shel</span></td><td>ter</td><td>&nbsp;take thee<span class="inserted">, M</span></td><td>aid</td><td>,
That justly think<span class="inserted">s, and hast mos</span></td><td>t rightly&nbsp;</td><td>said:</td><td>
And your large speeches</td><td>&nbsp;may your deed</td><td>s approve,
That good effects may spring from words of love:
Thus Kent,</td><td>&nbsp;O Princes, bids you all ad<span class="inserted">ieu</span></td><td>,
He<span class="inserted">'ll</span></td><td>&nbsp;shape his old course</td><td>&nbsp;in a C</td><td>ountr<span class="inserted">ey</span></td><td>&nbsp;new. Exit</td></tr><tr><td class="siglum">F3</td><td>THE TRAGEDIE OF</td><td>&nbsp;KING</td><td>&nbsp;LEAR
Actus Primus. Scena&nbsp;Prima</td><td>.
Enter Kent, Glouce</td><td>ster, and Edmon</td><td>d.
Kent.
I Thought the King had more affected the Duke of Alban</td><td>y, the</td><td>n Corn</td><td>wa</td><td>ll.
Glou</td><td>.
It did alwaye</td><td>s seem</td><td>&nbsp;to&nbsp;us: B</td><td>ut now in the division of the K</td><td>ingdome</td><td>, it appear</td><td>s not which of the Dukes hee</td><td>&nbsp;val<span class="inserted">u</span></td><td>es most, for&nbsp;</td><td>qualities are so weigh'</td><td>d, that curiosity</td><td>&nbsp;in neither, can make&nbsp;chois</td><td>e of eithers mo</td><td>ity</td><td>.
Kent.
Is not this your <span class="inserted">s</span></td><td>on</td><td>, my Lord?
Glou</td><td>.
His breeding<span class="inserted">,</span></td><td>&nbsp;S</td><td>ir,</td><td>&nbsp;hath b<span class="inserted">ee</span></td><td>n</td><td>&nbsp;at my charge.</td><td>&nbsp;I have so often blush'd</td><td>&nbsp;to ack</td><td>owledge him, that now I am braz'd to'</td><td>t.
Kent.
I cannot conceive you.
Glou</td><td>.
Sir, this yo<span class="inserted">u</span></td><td>ng F</td><td>ellowe</td><td>s <span class="inserted">M</span></td><td>other c</td><td>ould;</td><td>&nbsp;whereupon sh</td><td>e grew round womb'</td><td>d, and had indeed</td><td>&nbsp;(</td><td>Sir)</td><td>&nbsp;a S</td><td>onne</td><td>&nbsp;for&nbsp;her C</td><td>radle, e<span class="inserted">'</span></td><td>re she had a <span class="inserted">H</span></td><td>usband for her <span class="inserted">B</span></td><td>ed. D</td><td>o</td><td>&nbsp;you smell a fault?
Kent.
I cannot wish the fault undone, the issue of it,</td><td>&nbsp;being so proper.
Glou</td><td>.
But I have&nbsp;</td><td>a S</td><td>onne, Sir,</td><td>&nbsp;by order of the Law, some&nbsp;yeere</td><td>&nbsp;elder the</td><td>n this;</td><td>&nbsp;who,</td><td>&nbsp;yet is no dee</td><td>rer in my account, thou</td><td>gh this K</td><td>nave came somthing</td><td>&nbsp;f</td><td>awci</td><td>ly to the w</td><td>orld before he</td><td>&nbsp;was sent for:</td><td>&nbsp;yet was&nbsp;his M</td><td>other fa<span class="inserted">i</span></td><td>r</td><td>, there was good sport at his making, and</td><td>&nbsp;the <span class="inserted">w</span></td><td>hor</td><td>son must be acknowledged. Do</td><td>&nbsp;you know this N</td><td>ob</td><td>leman, Edmond?
Edm</td><td>.
No,</td><td>&nbsp;my Lord.
Glou</td><td>.
My Lord of Kent:
R</td><td>emember him h</td><td>e<span class="inserted">e</span></td><td>reafter, as my <span class="inserted">h</span></td><td>onourable F</td><td>riend</td><td>.
My services to your Lordship.
Kent.
I must love you, and sue to know you better.
Edm</td><td>.
Sir, I shall study deserving.
Glou</td><td>.
H</td><td>e hath b<span class="inserted">ee</span></td><td>n</td><td>&nbsp;out nine&nbsp;yeare</td><td>s, and away he</td><td>&nbsp;shall againe. T</td><td>he King is com</td><td>ming.
S</td><td>ennet.</td><td>&nbsp;Enter K</td><td>ing</td><td>&nbsp;Lear,</td><td>&nbsp;Cornwall, Albany,</td><td>&nbsp;Gone</td><td>rill, Regan, Cordelia, and attendant</td><td>s.
Lear.
Attend the Lords of France &amp;</td><td>&nbsp;Burgundy, Gloster.
Glou</td><td>.
I shall, my Lord.Exit</td><td>.
Lear.
Mean</td><td>&nbsp;time we sha</td><td>l</td><td>&nbsp;express</td><td>&nbsp;our darker purpose</td><td>.
Give me t</td><td>he M</td><td>ap there. K</td><td>now, that</td><td>&nbsp;we have divided
In three<span class="inserted">,</span></td><td>&nbsp;our K</td><td>ingdome</td><td>: and '</td><td>tis our fa</td><td>st intent,
To shake all <span class="inserted">c</span></td><td>ares and <span class="inserted">b</span></td><td>usiness</td><td>&nbsp;from our Ag</td><td>e,
Conferr</td><td>ing them on yo<span class="inserted">u</span></td><td>nger strengths, while we
Unburthen'd crawl toward d</td><td>eath. Our son of Cornwal<span class="inserted">l,
And you our no l</span></td><td>ess loving <span class="inserted">son of Albany</span></td><td>,
We have this hour a constant will to publish
Our <span class="inserted">Daughter<span class="inserted">'s severall Dowers, that future strife
May be prevented now.&nbsp;</span></span></td><td>The</td><td>&nbsp;Prince</td><td>, France &amp;</td><td>&nbsp;Burgundy<span class="inserted">.</span></td><td>
Great Ri</td><td>vals in our yonge<span class="inserted">r D</span></td><td>aughter<span class="inserted">'</span></td><td>s <span class="inserted">L</span></td><td>ove,
Long in our Court,</td><td>&nbsp;have made their amorous sojourn</td><td>,
And h</td><td>ere are to be answer'd. T</td><td>ell me my <span class="inserted">D</span></td><td>aughters
(Since now we will divest us both of Rule,
Interest of Territory, Cares of State)</td><td>
Which of you shall we say doth love us most,
That we, our largest bount<span class="inserted">y</span></td><td>&nbsp;may extend</td><td>
Where Nature</td><td>&nbsp;doth with meri</td><td>t challenge.&nbsp;</td><td>Gone</td><td>rill,
O</td><td>ur eldest born</td><td>, speake first.</td><td>
Gon.
Sir, I love you more the</td><td>n word can wei</td><td>ld the matter,
Dee</td><td>rer then e</td><td>ye-sight, space, and libert<span class="inserted">y</span></td><td>,
Beyond what can be val<span class="inserted">u</span></td><td>ed,</td><td>&nbsp;rich or rare,
No less</td><td>&nbsp;the</td><td>n life,</td><td>&nbsp;with grace, health, beauty, hono<span class="inserted">u</span></td><td>r:</td><td>
As much as</td><td>&nbsp;C</td><td>hild</td><td>&nbsp;e<span class="inserted">'</span></td><td>re lov'</td><td>d, or F</td><td>ather fou</td><td>nd.</td><td>
A love that makes breath poor</td><td>, and speech unable,
Beyond all manner of so much I love you.
Cor.
What shall Cordelia speake? L</td><td>ove,</td><td>&nbsp;and be silent.
Lear.
Of all these bounds</td><td>&nbsp;even from this L</td><td>ine,</td><td>&nbsp;to this,
With shadow<span class="inserted">y</span></td><td>&nbsp;F</td><td>orrests, and with Champ<span class="inserted">ions rich'd 
With plenteous River</span></td><td>s, and wide-skirted Mead</td><td>s
We make th</td><td>e</td><td>&nbsp;Lady. T</td><td>o thine and Albanie<span class="inserted">'</span></td><td>s i</td><td>ssues</td><td>
Be this perpetua</td><td>l. W</td><td>hat say</td><td>es our second D</td><td>aughter?
Our dee</td><td>rest Regan, wife of</td><td>&nbsp;Cornwall?</td><td>
Reg.
</td><td>I am made of that self</td><td>-</td><td>met<span class="inserted">al as</span></td><td>&nbsp;my <span class="inserted">s</span></td><td>ister</td><td>,
And prize me at her worth. I</td><td>n my true heart,
I find she names my very deed</td><td>&nbsp;of love:
O</td><td>n</td><td>ly she</td><td>&nbsp;co</td><td>mes too</td><td>&nbsp;short, t</td><td>hat I profess
M</td><td>y self</td><td>&nbsp;an e</td><td>nemy to all other joyes,
Which the most precious square of sense professes,
And find</td><td>&nbsp;I am alone felicitate
I</td><td>n your de<span class="inserted">ar</span></td><td>&nbsp;H</td><td>ighnes</td><td>s</td><td>&nbsp;love.
Cor</td><td>.
Then poor</td><td>&nbsp;Cordelia,
And</td><td>&nbsp;yet not so, since I am sure m</td><td>y love's
M</td><td>ore pond</td><td>erous</td><td>&nbsp;th<span class="inserted">a</span></td><td>n my tongue.
Lear.
To thee,</td><td>&nbsp;and thine hereditar<span class="inserted">y</span></td><td>&nbsp;ever,</td><td>
Remaine</td><td>&nbsp;this ample third of our fair</td><td>&nbsp;K</td><td>ingdome</td><td>,
No less</td><td>&nbsp;in space, validit<span class="inserted">y</span></td><td>, and pleasure</td><td>
The</td><td>n that conferr</td><td>'d on Gone</td><td>rill. N</td><td>ow our J</td><td>oy,
Although our</td><td>&nbsp;last and</td><td>&nbsp;least; to whose y</td><td>o<span class="inserted">ung</span></td><td>&nbsp;love,
The Vines of France, and Milke of Burgund<span class="inserted">y,
Strive to be interest. </span></td><td>What can you say, to draw
A</td><td>&nbsp;third, more op<span class="inserted">u</span></td><td>lent t</td><td>hen your Sisters? Speak.</td><td>
Cor</td><td>.
Nothing</td><td>&nbsp;my Lord.
Lear.
Nothing?

Cor.
Nothing.
Lear.
N</td><td>othing will</td><td>&nbsp;come of nothing, speake</td><td>&nbsp;again</td><td>.
Cor</td><td>.
Unhapp<span class="inserted">y</span></td><td>&nbsp;that I am, I cannot h</td><td>ave
M</td><td>y heart into my mouth.</td><td>&nbsp;I love your Majesty
A</td><td>ccording to my bond, no more nor less</td><td>.
Lear.How, how Cordelia? M</td><td>end your speech a little,
Le</td><td>st you may marre</td><td>&nbsp;your <span class="inserted">f</span></td><td>ortunes.
Cor</td><td>.
Good</td><td>&nbsp;my Lord<span class="inserted">,</span></td><td>
You have begot me, bred me, lov'</td><td>d me.</td><td>
I return</td><td>&nbsp;those duties back</td><td>&nbsp;as are right fit,
Obey you, L</td><td>ove you, and most honour you.</td><td>
Why have my S</td><td>isters <span class="inserted">h</span></td><td>usbands<span class="inserted">,</span></td><td>&nbsp;if they&nbsp;say
T</td><td>hey love you all?&nbsp;</td><td>Happi</td><td>ly when I shall wed<span class="inserted">.
T</span></td><td>hat Lord,</td><td>&nbsp;whose hand m</td><td>ust take my plight, shall ca</td><td>rry
H</td><td>alf</td><td>&nbsp;my <span class="inserted">L</span></td><td>ove with him, h</td><td>alf</td><td>&nbsp;my C</td><td>are,</td><td>&nbsp;and D</td><td>ut<span class="inserted">y</span></td><td>,
S</td><td>ure I shall never mar</td><td>r</td><td>y like my S</td><td>isters</td><td>.
Lear.
But goes thy heart</td><td>&nbsp;with this</td><td>?
Cor</td><td>.
I my</td><td>&nbsp;good</td><td>&nbsp;Lord.
Lear.
So you</td><td>ng,</td><td>&nbsp;and so untender?</td><td>
Cor.
So young</td><td>&nbsp;my&nbsp;Lord,</td><td>&nbsp;and&nbsp;true.</td><td>
Lear.
L</td><td>et it be so, th<span class="inserted">e</span></td><td>&nbsp;truth then shall </td><td>be thy dowre:</td><td>
For by the sacred radi<span class="inserted">a</span></td><td>nce of the Sun</td><td>,
The m<span class="inserted">ysteri</span></td><td>es</td><td>&nbsp;of He</td><td>cat<span class="inserted">e,</span></td><td>&nbsp;and&nbsp;the n</td><td>ight:
By all&nbsp;</td><td>operation<span class="inserted">s</span></td><td>&nbsp;of&nbsp;the O</td><td>rbe</td><td>s,
From whom</td><td>&nbsp;we do</td><td>&nbsp;exist</td><td>&nbsp;and cease to be,
He</td><td>re I desclaim</td><td>&nbsp;all my P</td><td>aterna</td><td>l care,
Propinquity</td><td>&nbsp;and property of blo<span class="inserted">u</span></td><td>d,
And as a stranger to my heart and me,</td><td>
Ho</td><td>ld thee from this for ever.</td><td>&nbsp;T</td><td>he b</td><td>arbarous Scythi</td><td>an,
O<span class="inserted">f</span></td><td>&nbsp;he that makes his <span class="inserted">G</span></td><td>eneration <span class="inserted">M</span></td><td>esses
T</td><td>o gorge his appetite, s</td><td>hall to my bosome
B</td><td>e as well neighbour'd<span class="inserted">,</span></td><td>&nbsp;pi</td><td>ti</td><td>ed, and rel<span class="inserted">ie</span></td><td>v'd,</td><td>
As thou my some</td><td>time D</td><td>aughter.
Kent.
Good</td><td>&nbsp;my Liege.
Lear.
Peace</td><td>&nbsp;Kent.
C</td><td>ome not between</td><td>&nbsp;the Dragon and</td><td>&nbsp;his wrath,</td><td>
I lov'd her most, and thought to set my rest
On her kind</td><td>&nbsp;nursery. H</td><td>ence and avoi</td><td>d</td><td>&nbsp;my sight:</td><td>
So be my grave my peace,</td><td>&nbsp;as here I give</td><td>
Her F</td><td>ather<span class="inserted">'</span></td><td>s heart from her:</td><td>&nbsp;call France, who stir</td><td>s?
Call Burgundy, Cornwa</td><td>ll, and Alban<span class="inserted">y</span></td><td>,
With my two <span class="inserted">D</span></td><td>aughters D</td><td>owres,</td><td>&nbsp;digest the</td><td>&nbsp;third,
Let pride, which she ca</td><td>l<span class="inserted">l</span></td><td>s plainness</td><td>, marry</td><td>&nbsp;her:
I do</td><td>&nbsp;invest you joi</td><td>ntly with</td><td>&nbsp;my&nbsp;power</td><td>,
Preheminence, and all the large effects
That troop</td><td>&nbsp;with Majesty. O</td><td>ur self</td><td>&nbsp;by M</td><td>onthly course,</td><td>
With reservation of an hundred K</td><td>nights,
By you to be sustain</td><td>'d, shall our abode
Make with you <span class="inserted">by</span></td><td>&nbsp;due turn</td><td>, on</td><td>ly we shall retain</td><td>
The name,</td><td>&nbsp;and all th'</td><td>addition</td><td>&nbsp;to a King: t</td><td>he S</td><td>way,
R</td><td>even<span class="inserted">ue,</span></td><td>&nbsp;E</td><td>xecution of the rest,
Beloved S</td><td>onne</td><td>s be yours, which to confirm</td><td>,
This Coronet part between</td><td>&nbsp;you.
Kent.
Roya</td><td>l Lear,
Whom I have ever hono<span class="inserted">u</span></td><td>r'd as my</td><td>&nbsp;King,
Lov'</td><td>d as my Father, as my Master follow'</td><td>d,
As my</td><td>&nbsp;</td><td>Patron</td><td>&nbsp;thought on in my pra<span class="inserted">y</span></td><td>ers.
Le<span class="inserted">ar</span></td><td>.
The <span class="inserted">B</span></td><td>ow is bent <span class="inserted">and</span></td><td>&nbsp;drawn,</td><td>&nbsp;make from the shaft.
Kent.
Let it fall rather, t</td><td>hough the fork</td><td>&nbsp;invade
T</td><td>he region of my heart, b</td><td>e Kent unmannerly,
W</td><td>hen Lear is mad, w</td><td>hat wouldes</td><td>t thou do</td><td>&nbsp;o</td><td>ld man?
T</td><td>hink'st thou that d</td><td>ut<span class="inserted">y s</span></td><td>hall have dread to speak
W</td><td>hen p</td><td>ower to f</td><td>lattery</td><td>&nbsp;bowe</td><td>s?</td><td>
To plainness</td><td>&nbsp;honour'</td><td>s bound,
W</td><td>hen Majesty fall</td><td>s to folly, r</td><td>eserve thy stat</td><td>e,
A</td><td>nd&nbsp;</td><td>thy best consideration c</td><td>heck
T</td><td>his hideous rashness</td><td>, answer</td><td>&nbsp;my life, m</td><td>y judgement:
T</td><td>hy yo<span class="inserted">u</span></td><td>ngest D</td><td>aughter do'</td><td>s not love thee least,
Nor are those empty hearted,</td><td>&nbsp;whose low</td><td>&nbsp;sounds</td><td>
Reverbe</td><td>&nbsp;no hollownes</td><td>s</td><td>.
Lear.
Kent,</td><td>&nbsp;on thy life no more.
Kent.
My life I never held but as <span class="inserted">a </span></td><td>pawn</td><td>
To wage against thine</td><td>&nbsp;enemies, ne<span class="inserted">'re</span></td><td>&nbsp;fear</td><td>&nbsp;to l</td><td>ose it,</td><td>
Thy safe</td><td>ty being</td><td>&nbsp;motive.
Lear.
Out of my sight.
Kent.
See better Lear, and let me still remain</td><td>
The true blank</td><td>&nbsp;of thine e<span class="inserted">y</span></td><td>e.
<span class="inserted">L</span></td><td>ear.
Now by Ap</td><td>ollo<span class="inserted">.
K</span></td><td>ent.
Now by Ap</td><td>ollo, King
T</td><td>hou swear<span class="inserted">'</span></td><td>st thy <span class="inserted">g</span></td><td>ods in vaine</td><td>.
Lear.
O&nbsp;</td><td>Vassal! Mis</td><td>creant.
Alb. Cor.
Dear Sir forbear.
</td><td>Kent.
K</td><td>ill thy Physi<span class="inserted">cian, a</span></td><td>nd thy</td><td>&nbsp;f</td><td>ee bestow
U</td><td>pon the foul</td><td>&nbsp;disease, r</td><td>evoke thy</td><td>&nbsp;gift,
O</td><td>r whil</td><td>st I can vent clamour f</td><td>rom my throat</td><td>,
I<span class="inserted">'</span></td><td>le</td><td>&nbsp;tell thee thou do<span class="inserted">'</span></td><td>st evi</td><td>l.
Lea<span class="inserted">r</span></td><td>.
Hear</td><td>&nbsp;me recreant</td><td>, on thine</td><td>&nbsp;alle</td><td>ge</td><td>ance hear</td><td>&nbsp;me;
That</td><td>&nbsp;thou hast fought to make us&nbsp;break</td><td>&nbsp;our&nbsp;vowes</td><td>,
Which we durst never yet; and with strain'</td><td>d pride,
To come betwixt</td><td>&nbsp;our sentence,</td><td>&nbsp;and our power.</td><td>
Which,</td><td>&nbsp;nor our nature,</td><td>&nbsp;nor our place can&nbsp;bear;</td><td>
Our potenc<span class="inserted">y</span></td><td>&nbsp;made good, take they reward.</td><td>
Fiv</td><td>e daye</td><td>s we do</td><td>&nbsp;allot thee for provision,
To shield thee from dis</td><td>aster</td><td>s of the world,
And on the six</td><td>t</td><td>&nbsp;to&nbsp;turne</td><td>&nbsp;thy hated back</td><td>
Upon our k</td><td>ingdome;</td><td>&nbsp;if</td><td>&nbsp;the tenth day following,
Thy banisht t</td><td>runk</td><td>&nbsp;be found in&nbsp;our D</td><td>ominions,
The moment is thy death, away. B</td><td>y Jupiter,
T</td><td>his shall not be revok'd<span class="inserted">.</span></td><td>
Kent.
F</td><td>are thee well K</td><td>ing, sith</td><td>&nbsp;thus thou wilt appear</td><td>,
Freedome</td><td>&nbsp;lives hence, and banishment is&nbsp;here;</td><td>
The <span class="inserted">g</span></td><td>ods to their de<span class="inserted">ar shel</span></td><td>ter</td><td>&nbsp;take thee M</td><td>aid</td><td>,
That justly think'st, and hast mos</td><td>t rightly&nbsp;</td><td>said:</td><td>
And your large speeches,</td><td>&nbsp;may your&nbsp;deede</td><td>s approve,
That good effects may spring from words of love:
Thus Kent,</td><td>&nbsp;O Princes, bids you all ad<span class="inserted">ieu</span></td><td>,
He<span class="inserted">'ll</span></td><td>&nbsp;shape his old course</td><td>&nbsp;in a C</td><td>ountr<span class="inserted">ey</span></td><td>&nbsp;new.&nbsp;Exit.</td></tr><tr><td class="siglum">F1</td><td>THE TRAGEDIE OF</td><td id="t1" style="background-color: rgb(255, 192, 203);">&nbsp;KING</td><td id="t2">&nbsp;LEAR.
Actus Primus. Scaena&nbsp;Prima</td><td id="t3">.
Enter Kent, Glouce</td><td id="t4">ster, and Edmon</td><td id="t5">d.
Kent.
I Thought the King had more affected the Duke of Alban</td><td id="t6">y, the</td><td id="t7">n Corn</td><td id="t8">wa</td><td id="t9">ll.
Glou</td><td id="t10">.
It did alwaye</td><td id="t11">s seeme so</td><td id="t12">&nbsp;to&nbsp;us: B</td><td id="t13">ut now in the division of the K</td><td id="t14">ingdome</td><td id="t15">, it appear</td><td id="t16">s not which of the Dukes hee</td><td id="t17">&nbsp;valew</td><td id="t18">es most, for&nbsp;</td><td id="t19">qualities are so weigh'</td><td id="t20">d, that curiosity</td><td id="t21">&nbsp;in neither, can make&nbsp;chois</td><td id="t22">e of eithers mo</td><td id="t23">ity</td><td id="t24">.
Kent.
Is not this your S</td><td id="t25">on</td><td id="t26">, my Lord?
Glou</td><td id="t27">.
His breeding</td><td id="t28">&nbsp;S</td><td id="t29">ir,</td><td id="t30">&nbsp;hath bi</td><td id="t31">n</td><td id="t32">&nbsp;at my charge.</td><td id="t33">&nbsp;I have so often blush'd</td><td id="t34">&nbsp;to ackn</td><td id="t35">owledge him, that now I am braz'd too'</td><td id="t36">t.
Kent.
I cannot conceive you.
Glou</td><td id="t37">.
Sir, this yo</td><td id="t38">ng F</td><td id="t39">ellowe</td><td id="t40">s m</td><td id="t41">other c</td><td id="t42">ould;</td><td id="t43">&nbsp;whereupon sh</td><td id="t44">e grew round womb'</td><td id="t45">d, and had indeede</td><td id="t46">&nbsp;(</td><td id="t47">Sir)</td><td id="t48">&nbsp;a S</td><td id="t49">onne</td><td id="t50">&nbsp;for&nbsp;her C</td><td id="t51">radle, e</td><td id="t52">re she had a h</td><td id="t53">usband for her b</td><td id="t54">ed. D</td><td id="t55">o</td><td id="t56">&nbsp;you smell a fault?
Kent.
I cannot wish the fault undone, the issue of it,</td><td id="t57">&nbsp;being so proper.
Glou</td><td id="t58">.
But I have&nbsp;</td><td id="t59">a S</td><td id="t60">onne, Sir,</td><td id="t61">&nbsp;by order of the Law, some&nbsp;yeere</td><td id="t62">&nbsp;elder the</td><td id="t63">n this;</td><td id="t64">&nbsp;who,</td><td id="t65">&nbsp;yet is no dee</td><td id="t66">rer in my account, thou</td><td id="t67">gh this K</td><td id="t68">nave came somthing</td><td id="t69">&nbsp;f</td><td id="t70">awci</td><td id="t71">ly to the w</td><td id="t72">orld before he</td><td id="t73">&nbsp;was sent for:</td><td id="t74">&nbsp;yet was&nbsp;his M</td><td id="t75">other fay</td><td id="t76">re</td><td id="t77">, there was good sport at his making, and</td><td id="t78">&nbsp;the </td><td id="t79">hor</td><td id="t80">son must be acknowledged. Doe</td><td id="t81">&nbsp;you know this N</td><td id="t82">oble Gent</td><td id="t83">leman, Edmond?
Edm</td><td id="t84">.
No,</td><td id="t85">&nbsp;my Lord.
Glou</td><td id="t86">.
My Lord of Kent:
R</td><td id="t87">emember him h</td><td id="t88">ee</td><td id="t89">reafter, as my H</td><td id="t90">onourable F</td><td id="t91">riend.
Edm</td><td id="t92">.
My services to your Lordship.
Kent.
I must love you, and sue to know you better.
Edm</td><td id="t93">.
Sir, I shall study deserving.
Glou</td><td id="t94">.
H</td><td id="t95">e hath bi</td><td id="t96">n</td><td id="t97">&nbsp;out nine&nbsp;yeare</td><td id="t98">s, and away he</td><td id="t99">&nbsp;shall againe. T</td><td id="t100">he King is com</td><td id="t101">ming.
S</td><td id="t102">ennet.</td><td id="t103">&nbsp;Enter K</td><td id="t104">ing</td><td id="t105">&nbsp;Lear,</td><td id="t106">&nbsp;Cornwall, Albany,</td><td id="t107">&nbsp;Gone</td><td id="t108">rill, Regan, Cordelia, and attendant</td><td id="t109">s.
Lear.
Attend the Lords of France &amp;</td><td id="t110">&nbsp;Burgundy, Gloster.
Glou</td><td id="t111">.
I shall, my Lord.Exit</td><td id="t112">.
Lear.
Meane</td><td id="t113">&nbsp;time we sha</td><td id="t114">l</td><td id="t115">&nbsp;expresse</td><td id="t116">&nbsp;our darker purpose</td><td id="t117">.
Give me t</td><td id="t118">he M</td><td id="t119">ap there. K</td><td id="t120">now, that</td><td id="t121">&nbsp;we have divided
In&nbsp;three</td><td id="t122">&nbsp;our K</td><td id="t123">ingdome</td><td id="t124">: and '</td><td id="t125">tis our fa</td><td id="t126">st intent,
To shake all C</td><td id="t127">ares and B</td><td id="t128">usinesse</td><td id="t129">&nbsp;from our Ag</td><td id="t130">e,
Conferr</td><td id="t131">ing them on yo</td><td id="t132">nger strengths, while we
Unburthen'd crawle toward d</td><td id="t133">eath. Our son of Cornwal,
And you our no l</td><td id="t134">esse loving Sonne of Albany</td><td id="t135">,
We have this houre a constant will to publish
Our daughters severall Dowers, that future strife
May be prevented now.&nbsp;</td><td id="t136">The</td><td id="t137">&nbsp;Princes</td><td id="t138">, France &amp;</td><td id="t139">&nbsp;Burgundy,</td><td id="t140">
Great Ri</td><td id="t141">vals in our yongest d</td><td id="t142">aughter</td><td id="t143">s l</td><td id="t144">ove,
Long in our Court,</td><td id="t145">&nbsp;have made their amorous sojourne</td><td id="t146">,
And he</td><td id="t147">ere are to be answer'd. T</td><td id="t148">ell me my d</td><td id="t149">aughters
(Since now we will divest us both of Rule,
Interest of Territory, Cares of State)</td><td id="t150">
Which of you shall we say doth love us most,
That we, our largest bountie</td><td id="t151">&nbsp;may extend</td><td id="t152">
Where Nature</td><td id="t153">&nbsp;doth with meri</td><td id="t154">t challenge.&nbsp;</td><td id="t155">Gone</td><td id="t156">rill,
O</td><td id="t157">ur eldest borne</td><td id="t158">, speake first.</td><td id="t159">
Gon.
Sir, I love you more the</td><td id="t160">n word can wei</td><td id="t161">ld the matter,
Dee</td><td id="t162">rer then e</td><td id="t163">ye-sight, space, and libertie</td><td id="t164">,
Beyond what can be valew</td><td id="t165">ed,</td><td id="t166">&nbsp;rich or rare,
No&nbsp;lesse</td><td id="t167">&nbsp;the</td><td id="t168">n life,</td><td id="t169">&nbsp;with grace, health, beauty, hono</td><td id="t170">r:</td><td id="t171">
As much as</td><td id="t172">&nbsp;C</td><td id="t173">hilde</td><td id="t174">&nbsp;e</td><td id="t175">re lov'</td><td id="t176">d, or F</td><td id="t177">ather fou</td><td id="t178">nd.</td><td id="t179">
A love that makes breath poore</td><td id="t180">, and speech unable,
Beyond all manner of so much I love you.
Cor.
What shall Cordelia speake? L</td><td id="t181">ove,</td><td id="t182">&nbsp;and be silent.
Lear.
Of all these bounds</td><td id="t183">&nbsp;even from this L</td><td id="t184">ine,</td><td id="t185">&nbsp;to this,
With shadowie</td><td id="t186">&nbsp;F</td><td id="t187">orrests, and with Champains rich'd 
With plenteous River</td><td id="t188">s, and wide-skirted Meade</td><td id="t189">s
We make the</td><td id="t190">e</td><td id="t191">&nbsp;Lady. T</td><td id="t192">o thine and Albanie</td><td id="t193">s i</td><td id="t194">ssues</td><td id="t195">
Be this perpetual</td><td id="t196">l. W</td><td id="t197">hat say</td><td id="t198">es our second D</td><td id="t199">aughter?
Our dee</td><td id="t200">rest Regan, wife of</td><td id="t201">&nbsp;Cornwall?</td><td id="t202">
Reg.
</td><td id="t203">I am made of that selfe</td><td id="t204">-</td><td id="t205">mettle as</td><td id="t206">&nbsp;my S</td><td id="t207">ister</td><td id="t208">,
And prize me at her worth. I</td><td id="t209">n my true heart,
I find she names my very deede</td><td id="t210">&nbsp;of love:
O</td><td id="t211">ne</td><td id="t212">ly she</td><td id="t213">&nbsp;co</td><td id="t214">mes too</td><td id="t215">&nbsp;short, t</td><td id="t216">hat I professe
M</td><td id="t217">y selfe</td><td id="t218">&nbsp;an e</td><td id="t219">nemy to all other joyes,
Which the most precious square of sense professes,
And finde</td><td id="t220">&nbsp;I am alone felicitate
I</td><td id="t221">n your deere</td><td id="t222">&nbsp;H</td><td id="t223">ighnes</td><td id="t224">se</td><td id="t225">&nbsp;love.
Cor</td><td id="t226">.
Then poore</td><td id="t227">&nbsp;Cordelia,
And</td><td id="t228">&nbsp;yet not so, since I am sure m</td><td id="t229">y love's
M</td><td id="t230">ore pond</td><td id="t231">erous</td><td id="t232">&nbsp;the</td><td id="t233">n my tongue.
Lear.
To thee,</td><td id="t234">&nbsp;and thine hereditarie</td><td id="t235">&nbsp;ever,</td><td id="t236">
Remaine</td><td id="t237">&nbsp;this ample third of our&nbsp;faire</td><td id="t238">&nbsp;K</td><td id="t239">ingdome</td><td id="t240">,
No lesse</td><td id="t241">&nbsp;in space, validitie</td><td id="t242">, and pleasure</td><td id="t243">
The</td><td id="t244">n that conferr</td><td id="t245">'d on Gone</td><td id="t246">rill. N</td><td id="t247">ow our J</td><td id="t248">oy,
Although our</td><td id="t249">&nbsp;last and</td><td id="t250">&nbsp;least; to whose y</td><td id="t251">ong</td><td id="t252">&nbsp;love,
The Vines of France, and Milke of Burgundie,
Strive to be interest. </td><td id="t253">What can you say, to draw
A</td><td id="t254">&nbsp;third, more opi</td><td id="t255">lent t</td><td id="t256">hen your Sisters? Speake.</td><td id="t257">
Cor</td><td id="t258">.
Nothing</td><td id="t259">&nbsp;my Lord.
Lear.
Nothing?

Cor.
Nothing.
Lear.
N</td><td id="t260">othing will</td><td id="t261">&nbsp;come of nothing, speake</td><td id="t262">&nbsp;againe</td><td id="t263">.
Cor</td><td id="t264">.
Unhappie</td><td id="t265">&nbsp;that I am, I cannot he</td><td id="t266">ave
M</td><td id="t267">y heart into my mouth.</td><td id="t268">&nbsp;I love your Majesty
A</td><td id="t269">ccording to my bond, no more nor lesse</td><td id="t270">.
Lear.How, how Cordelia? M</td><td id="t271">end your speech a little,
Lea</td><td id="t272">st you may marre</td><td id="t273">&nbsp;your F</td><td id="t274">ortunes.
Cor</td><td id="t275">.
Good</td><td id="t276">&nbsp;my&nbsp;Lord.</td><td id="t277">
You have begot me, bred me, lov'</td><td id="t278">d me.</td><td id="t279">
I return</td><td id="t280">&nbsp;those duties&nbsp;backe</td><td id="t281">&nbsp;as are right fit,
Obey you, L</td><td id="t282">ove you, and most honour you.</td><td id="t283">
Why have my S</td><td id="t284">isters H</td><td id="t285">usbands</td><td id="t286">&nbsp;if they&nbsp;say
T</td><td id="t287">hey love you all?&nbsp;</td><td id="t288">Happi</td><td id="t289">ly when I shall wed,
T</td><td id="t290">hat Lord,</td><td id="t291">&nbsp;whose hand m</td><td id="t292">ust take my plight, shall ca</td><td id="t293">rry
H</td><td id="t294">alfe</td><td id="t295">&nbsp;my l</td><td id="t296">ove with him, h</td><td id="t297">alfe</td><td id="t298">&nbsp;my C</td><td id="t299">are,</td><td id="t300">&nbsp;and D</td><td id="t301">utie</td><td id="t302">,
S</td><td id="t303">ure I shall never mar</td><td id="t304">r</td><td id="t305">y like my S</td><td id="t306">isters</td><td id="t307">.
Lear.
But goes thy heart</td><td id="t308">&nbsp;with this</td><td id="t309">?
Cor</td><td id="t310">.
I my</td><td id="t311">&nbsp;good</td><td id="t312">&nbsp;Lord.
Lear.
So you</td><td id="t313">ng,</td><td id="t314">&nbsp;and so untender?</td><td id="t315">
Cor.
So young</td><td id="t316">&nbsp;my&nbsp;Lord,</td><td id="t317">&nbsp;and&nbsp;true.</td><td id="t318">
Lear.
L</td><td id="t319">et it be so, thy</td><td id="t320">&nbsp;truth then shall </td><td id="t321">be thy dowre:</td><td id="t322">
For by the sacred radie</td><td id="t323">nce of the Sunne</td><td id="t324">,
The miseri</td><td id="t325">es</td><td id="t326">&nbsp;of Hec</td><td id="t327">cat</td><td id="t328">&nbsp;and&nbsp;the n</td><td id="t329">ight:
By all&nbsp;</td><td id="t330">operation</td><td id="t331">&nbsp;of&nbsp;the O</td><td id="t332">rbe</td><td id="t333">s,
From whom</td><td id="t334">&nbsp;we do</td><td id="t335">&nbsp;exist</td><td id="t336">&nbsp;and cease to be,
Hee</td><td id="t337">re I desclaime</td><td id="t338">&nbsp;all my P</td><td id="t339">aternal</td><td id="t340">l care,
Propinquity</td><td id="t341">&nbsp;and property of bloo</td><td id="t342">d,
And as a stranger to my heart and me,</td><td id="t343">
Ho</td><td id="t344">ld thee from this for ever.</td><td id="t345">&nbsp;T</td><td id="t346">he b</td><td id="t347">arbarous Scythi</td><td id="t348">an,
Or</td><td id="t349">&nbsp;he that makes&nbsp;his g</td><td id="t350">eneration m</td><td id="t351">esses
T</td><td id="t352">o gorge his appetite, s</td><td id="t353">hall to my bosome
B</td><td id="t354">e as well neighbour'd</td><td id="t355">&nbsp;pit</td><td id="t356">ti</td><td id="t357">ed, and relee</td><td id="t358">v'd,</td><td id="t359">
As thou my some</td><td id="t360">time D</td><td id="t361">aughter.
Kent.
Good,</td><td id="t362">&nbsp;my Liege.
Lear.
Peace</td><td id="t363">&nbsp;Kent.
C</td><td id="t364">ome not betweene</td><td id="t365">&nbsp;the Dragon and</td><td id="t366">&nbsp;his wrath,</td><td id="t367">
I lov'd her most, and thought to set my rest
On her kind</td><td id="t368">&nbsp;nursery. H</td><td id="t369">ence and avoi</td><td id="t370">d</td><td id="t371">&nbsp;my sight:</td><td id="t372">
So be my grave my peace,</td><td id="t373">&nbsp;as here I give</td><td id="t374">
Her F</td><td id="t375">ather</td><td id="t376">s heart from her:</td><td id="t377">&nbsp;call France, who stirre</td><td id="t378">s?
Call Burgundy, Cornwa</td><td id="t379">ll, and Albanie</td><td id="t380">,
With my two d</td><td id="t381">aughters D</td><td id="t382">owres,</td><td id="t383">&nbsp;digest the</td><td id="t384">&nbsp;third,
Let pride, which she ca</td><td id="t385">l</td><td id="t386">s plainnesse</td><td id="t387">, marry</td><td id="t388">&nbsp;her:
I doe</td><td id="t389">&nbsp;invest you joi</td><td id="t390">ntly with</td><td id="t391">&nbsp;my&nbsp;power</td><td id="t392">,
Preheminence, and all the large effects
That troope</td><td id="t393">&nbsp;with Majesty. O</td><td id="t394">ur selfe</td><td id="t395">&nbsp;by M</td><td id="t396">onthly course,</td><td id="t397">
With reservation of an hundred K</td><td id="t398">nights,
By you to be sustain</td><td id="t399">'d, shall our abode
Make with you one</td><td id="t400">&nbsp;due&nbsp;turne</td><td id="t401">, one</td><td id="t402">ly we shall retaine</td><td id="t403">
The name,</td><td id="t404">&nbsp;and all th'</td><td id="t405">addition</td><td id="t406">&nbsp;to a King: t</td><td id="t407">he S</td><td id="t408">way,
R</td><td id="t409">evennew</td><td id="t410">&nbsp;E</td><td id="t411">xecution of the rest,
Beloved S</td><td id="t412">onne</td><td id="t413">s be yours, which to confirme</td><td id="t414">,
This Coronet part betweene</td><td id="t415">&nbsp;you.
Kent.
Royal</td><td id="t416">l Lear,
Whom I have ever hono</td><td id="t417">r'd as my</td><td id="t418">&nbsp;King,
Lov'</td><td id="t419">d as my Father, as my Master follow'</td><td id="t420">d,
As my</td><td id="t421">&nbsp;great </td><td id="t422">Patron</td><td id="t423">&nbsp;thought on in my prai</td><td id="t424">ers.
Le</td><td id="t425">.
The b</td><td id="t426">ow is bent &amp;</td><td id="t427">&nbsp;drawne,</td><td id="t428">&nbsp;make from the shaft.
Kent.
Let it fall rather, t</td><td id="t429">hough the forke</td><td id="t430">&nbsp;invade
T</td><td id="t431">he region of my heart, b</td><td id="t432">e Kent unmannerly,
W</td><td id="t433">hen Lear is mad, w</td><td id="t434">hat wouldes</td><td id="t435">t thou do</td><td id="t436">&nbsp;o</td><td id="t437">ld man?
T</td><td id="t438">hink'st thou that d</td><td id="t439">utie s</td><td id="t440">hall have dread to speake,
W</td><td id="t441">hen p</td><td id="t442">ower to f</td><td id="t443">lattery</td><td id="t444">&nbsp;bowe</td><td id="t445">s?</td><td id="t446">
To plainnesse</td><td id="t447">&nbsp;honour'</td><td id="t448">s bound,
W</td><td id="t449">hen Majesty fall</td><td id="t450">s to folly, r</td><td id="t451">eserve thy stat</td><td id="t452">e,
A</td><td id="t453">nd in&nbsp;</td><td id="t454">thy best consideration c</td><td id="t455">hecke
T</td><td id="t456">his hideous rashnesse</td><td id="t457">, answere</td><td id="t458">&nbsp;my life, m</td><td id="t459">y judgement:
T</td><td id="t460">hy yo</td><td id="t461">ngest D</td><td id="t462">aughter do'</td><td id="t463">s not love thee least,
Nor are those empty hearted,</td><td id="t464">&nbsp;whose low</td><td id="t465">&nbsp;sounds</td><td id="t466">
Reverbe</td><td id="t467">&nbsp;no hollownes</td><td id="t468">se</td><td id="t469">.
Lear.
Kent,</td><td id="t470">&nbsp;on thy life no more.
Kent.
My life I never held but as </td><td id="t471">pawne</td><td id="t472">
To wage against thine</td><td id="t473">&nbsp;enemies, nere</td><td id="t474">&nbsp;feare</td><td id="t475">&nbsp;to lo</td><td id="t476">ose it,</td><td id="t477">
Thy safe</td><td id="t478">ty being</td><td id="t479">&nbsp;motive.
Lear.
Out of my sight.
Kent.
See better Lear, and let me still remaine</td><td id="t480">
The true blanke</td><td id="t481">&nbsp;of thine ei</td><td id="t482">e.
K</td><td id="t483">ear.
Now by Ap</td><td id="t484">ollo,
L</td><td id="t485">ent.
Now by Ap</td><td id="t486">ollo, King
T</td><td id="t487">hou swear.</td><td id="t488">st thy G</td><td id="t489">ods in vaine</td><td id="t490">.
Lear.
O&nbsp;</td><td id="t491">Vassall! Mis</td><td id="t492">creant.
Alb. Cor.
Deare Sir forbeare.
</td><td id="t493">Kent.
K</td><td id="t494">ill thy Physition, a</td><td id="t495">nd thy</td><td id="t496">&nbsp;f</td><td id="t497">ee bestow
U</td><td id="t498">pon the foule</td><td id="t499">&nbsp;disease, r</td><td id="t500">evoke thy</td><td id="t501">&nbsp;guift,
O</td><td id="t502">r whil'</td><td id="t503">st I can vent clamour f</td><td id="t504">rom my throate</td><td id="t505">,
I</td><td id="t506">le</td><td id="t507">&nbsp;tell thee thou do</td><td id="t508">st evil</td><td id="t509">l.
Lea</td><td id="t510">.
Heare</td><td id="t511">&nbsp;me recreant</td><td id="t512">, on thine</td><td id="t513">&nbsp;alle</td><td id="t514">ge</td><td id="t515">ance heare</td><td id="t516">&nbsp;me;
That</td><td id="t517">&nbsp;thou hast fought to make us breake</td><td id="t518">&nbsp;our&nbsp;vowes</td><td id="t519">,
Which we durst never yet; and with strain'</td><td id="t520">d pride,
To come betwixt</td><td id="t521">&nbsp;our sentences,</td><td id="t522">&nbsp;and our power.</td><td id="t523">
Which,</td><td id="t524">&nbsp;nor our nature,</td><td id="t525">&nbsp;nor our place can beare;</td><td id="t526">
Our potencie</td><td id="t527">&nbsp;made good, take they reward.</td><td id="t528">
Fiv</td><td id="t529">e daye</td><td id="t530">s we do</td><td id="t531">&nbsp;allot thee for provision,
To shield thee from dis</td><td id="t532">aster</td><td id="t533">s of the world,
And on the six</td><td id="t534">t</td><td id="t535">&nbsp;to&nbsp;turne</td><td id="t536">&nbsp;thy hated&nbsp;backe</td><td id="t537">
Upon our k</td><td id="t538">ingdome;</td><td id="t539">&nbsp;if on</td><td id="t540">&nbsp;the tenth day following,
Thy banisht t</td><td id="t541">runke</td><td id="t542">&nbsp;be found in&nbsp;our D</td><td id="t543">ominions,
The moment is thy death, away. B</td><td id="t544">y Jupiter,
T</td><td id="t545">his shall not be revok'd,</td><td id="t546">
Kent.
F</td><td id="t547">are thee well K</td><td id="t548">ing, sith</td><td id="t549">&nbsp;thus thou wilt appeare</td><td id="t550">,
Freedome</td><td id="t551">&nbsp;lives hence, and banishment is&nbsp;here;</td><td id="t552">
The G</td><td id="t553">ods to their deere shel</td><td id="t554">ter</td><td id="t555">&nbsp;take thee M</td><td id="t556">aid</td><td id="t557">,
That justly think'st, and hast mos</td><td id="t558">t rightly&nbsp;</td><td id="t559">said:</td><td id="t560">
And your large speeches,</td><td id="t561">&nbsp;may your&nbsp;deede</td><td id="t562">s approve,
That good effects may spring from words of love:
Thus Kent,</td><td id="t563">&nbsp;O Princes, bids you all adew</td><td id="t564">,
Hee'l</td><td id="t565">&nbsp;shape his old course,</td><td id="t566">&nbsp;in a C</td><td id="t567">ountry</td><td id="t568">&nbsp;new.&nbsp;Exit.</td></tr></tbody></table>
</div></div>
<div id="prefs"><form name="default" id="default" method="post" action="/tests/table/"><p><span>hide merged</span>
<input type="checkbox" name="HIDE_MERGED"><span>&nbsp;&nbsp;whole words</span>
<input type="checkbox" name="WHOLE_WORDS"><span>&nbsp;&nbsp;compact</span>
<input type="checkbox" name="COMPACT"><input type="submit" onclick="presubmit()"></p>
<p>length:<select name="LENGTH"><option selected="selected">100</option>
<option>150</option>
<option>200</option>
<option>250</option>
</select>
&nbsp;&nbsp;start offset:<select name="OFFSET"><option selected="selected">0</option>
<option>100</option>
<option>200</option>
<option>300</option>
<option>400</option>
<option>500</option>
<option>600</option>
<option>700</option>
</select>
<span>&nbsp;&nbsp;some versions</span>
<input type="checkbox" name="SOME_VERSIONS" onclick="toggleVersionSelector(this)"><select disabled="" onchange="checkmark(this)" id="selector"><optgroup label="Base"><option value="/Base/F1">F1</option>
<option value="/Base/F2">F2</option>
<option value="/Base/F3">F3</option>
<option value="/Base/F4">F4</option>
<option value="/Base/Q1">Q1</option>
<option value="/Base/Q2">Q2</option>
</optgroup>
</select>
<input type="hidden" id="versions" name="SELECTED_VERSIONS"></p>
<input type="hidden" name="DOC_ID" id="DOC_ID" value="english/shakespeare/kinglear/act1/scene1"></form>
</div>
<div id="buttons">
  <button name="prefs">prefs</button>
  <button name="table">table</button>
  <button name="none">none</button>
</div>
</div>
<script>
$("button[name='prefs']").click(function() 
{
	var bheight = $("#buttons").height();
	var cheight=$("#centre").height();	
	if ( $("#table").is(":visible") )
	{
		$("#table").hide();
    }
	$("#prefs").animate(
		{height: 'show'},
		{
          duration: "slow",
		  step: function(now, fx) 
	      {
			  var th = cheight-(now+bheight);
			  $("#text").css("height",th+"px");
		  }
    });
});
$("button[name='table']").click(function() 
{
	var bheight = $("#buttons").height();
	var cheight=$("#centre").height();	
	if ( $("#prefs").is(":visible") )
	{
		$("#prefs").hide();
    }
	$("#table").animate(
		{height: 'show'},
		{
          duration: "slow",
		  complete: function()
		  {
			var th = cheight-($("#table").outerHeight()+bheight);
			$("#text").css("height",th+"px");
	      },
		  step: function(now, fx) 
	      {
			  var th = cheight-(now+bheight);
			  $("#text").css("height",th+"px");
		  }
    });
});
$("button[name='none']").click(function() 
{
	var bheight = $("#buttons").height();
	var cheight=$("#centre").height();	
	if ( $("#prefs").is(":visible") )
	{
		$("#prefs").hide();
    }
	if ( $("#table").is(":visible") )
	{
		$("#table").hide();
    }
	var th = cheight-bheight;
	$("#text").css("height",th+"px");
});
</script>


</body></html>