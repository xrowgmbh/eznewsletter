{*<div id="maincontent">*}
<div id="fix">
<div id="maincontent-design">
<!-- Maincontent START -->

{* Content window. *}
{*<div class="context-block">*}

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">
{$object.version_name|wash}&nbsp;[{$object.content_class.name|wash}]</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

<div class="box-ml"><div class="box-mr">

<div class="context-information">
<p class="modified">&nbsp;</p>
<p class="full-screen">
<a href={concat("newsletter/previewfull/",$object.contentobject_id, "/", $object.version )|ezurl} target="_blank"><img src={"images/window_fullscreen.png"|ezdesign} /></a>
</p>
<div class="break"></div>
</div>

{* Content preview in content window. *}
<div class="mainobject-window">

    <iframe src={concat("newsletter/previewfull/",$object.contentobject_id, "/", $object.version )|ezurl} width="100%" height="800">
    Your browser does not support iframes. Please see this <a href={concat("newsletter/previewfull/",$object.id )|ezurl}>link</a> instead.
</iframe>

</div>


</div></div>

{* Buttonbar for content window. *}
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
    <input class="button" type="button" onClick="javascript:window.location.href = document.referrer; window.reload();" name="BackButton" value="{'Back'|i18n( 'design/eznewsletter/newsletter_preview' )}" title="{'Return to last view.'|i18n( 'design/eznewsletter/newsletter_preview' )}" />
    <form action={'newsletter/copy'|ezurl()} method="post" style="display: inline;">
        <input type="hidden" value="{$object.contentobject_id}" name="ObjectID"/>
        <input class="button" type="submit" title="{'Copy this item to the same location'|i18n( 'design/eznewsletter/newsletter_preview' )}" value="Copy" name="CopyButton"/>
    </form>
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
<!-- Maincontent END -->
</div>
</div>

