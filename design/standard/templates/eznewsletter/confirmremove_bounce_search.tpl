{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* eZNewsletter - confirm remove bounce entry *}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Confirm bounce entry removal'|i18n( 'design/eznewsletter/confirmremove_bounce' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="message-confirmation">

{section show=$delete_result|count|eq(1)}
    <h2>{'Are you sure you want to remove the bounce entry?'|i18n( 'design/eznewsletter/confirmremove_bounce' )}</h2>
{section-else}
    <h2>{'Are you sure you want to remove the bounce entries?'|i18n( 'design/eznewsletter/confirmremove_bounce' )}</h2>
{/section}


<ul>
{foreach $delete_result as $item}
{$item|attribute(show,1)}
    <li>{$item.newsletter_name|wash}: {$item.id|wash} - {$item.address|wash}</li>
{/foreach}
</ul>

<p><b>{'Warning'|i18n( 'design/eznewsletter/confirmremove_bounce' )}:</b></p>
<p>{'Do not proceeed unless you are sure.'|i18n( 'design/eznewsletter/confirmremove_bounce' )}</p>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">

<form action={$module.functions.list_bounce.uri|ezurl} method="post" name="BounceEntryRemove">
    <input class="button" type="submit" name="ConfirmRemoveBounceSearchButton" value="{'OK'|i18n( 'design/eznewsletter/confirmremove_bounce' )}" />
    <input class="button" type="submit" name="CancelBounceSearchButton" value="{'Cancel'|i18n( 'design/eznewsletter/confirmremove_bounce' )}" />
</form>

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

</div>
