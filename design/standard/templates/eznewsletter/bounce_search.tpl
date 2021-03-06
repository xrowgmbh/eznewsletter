{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* eZNewsletter - bounce search *}
{def $base_uri='newsletter/bounce_search'}


<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'Bounce search'|i18n( 'design/eznewsletter/bounce_search' )}</h2>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<form name="bounce_search" method="post" action={$base_uri|ezurl} class="inline">

<div class="context-attributes">

    {* Name. *}
        <div class="block">
        <label>{"Enter newsletter or email address:"|i18n( 'design/eznewsletter/bounce_search' )}</label>
        <input class="halfbox" id="searchString" type="text" name="searchString" value="{$searchString|wash}" title="{'Search text.'|i18n( 'design/eznewsletter/bounce_search' )}" />
    </div>

    {* Edit *}
    <div class="block">
        <input class="button" type="submit" name="SearchButton" value="{'Search'|i18n( 'design/admin/rss/edit_import' )}" title="{'Search bounce.'|i18n( 'design/eznewsletter/bounce_search' )}" />
    </div>

    </div>

    </div>
    <div class="break"></div>

</div>


    <div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
    </div>


{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'Bounce list'|i18n( 'design/eznewsletter/bounce_search' )}</h2>
{* DESIGN: Subline *}<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN:  START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
<div class="context-attributes">

{* Bounce search table. *}
<div class="overflow-table">

<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection'|i18n( 'design/eznewsletter/bounce_search' )}" title="{'Invert selection'|i18n( 'design/eznewsletter/bounce_search' )}" onclick="ezjs_toggleCheckboxes( document.bounce_search, 'BounceIDArray[]' ); return false;" /></th>
    <th class="tight">{'ID'|i18n('eznewslettert')}</th>
    <th>{'Newsletter'|i18n( 'design/eznewsletter/bounce_search' )}</th>
    <th>{'Email'|i18n( 'design/eznewsletter/bounce_search' )}</th>
    <th></th>
</tr>

{foreach $bounceSearch as $bounce
         sequence array( bglight, bgdark ) as $seq}
<tr class="{$seq}">
    <td><input type="checkbox" name="BounceIDArray[]" value="{$bounce.id}" title="{'Select bounce for removal'|i18n( 'design/eznewsletter/bounce_search' )}" /></td>
    <td class="number" align="right">{$bounce.id}</td>
    <td>{$bounce.name|wash}</td>
    <td>{$bounce.address|wash}</td>
    </td>
</tr>
{/foreach}
</table>

</div>

{* DESIGN: Table END *}</div></div></div>

{* DESIGN: Control bar START *}
<div class="controlbar">
<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">
    <input class="button" type="submit" name="RemoveBounceButton" value="{'Remove selected'|i18n( 'design/eznewsletter/bounce_search' )}" title="{'Remove selected bounce.'|i18n( 'design/eznewsletter/bounce_search' )}">
</form>
</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

{* DESIGN: Content END *}</div></div></div>

