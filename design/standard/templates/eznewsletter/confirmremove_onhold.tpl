{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* eZNewsletter - confirm remove bounce entry *}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Confirm on hold entry removal'|i18n( 'design/eznewsletter/confirmremove_onhold' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="message-confirmation">

{section show=$delete_result|count|eq(1)}
    <h2>{'Are you sure you want to remove the on hold entry?'|i18n( 'design/eznewsletter/confirmremove_onhold' )}</h2>
{section-else}
    <h2>{'Are you sure you want to remove the on hold entries?'|i18n( 'design/eznewsletter/confirmremove_onhold' )}</h2>
{/section}

<p>{'The following entries on hold will be removed:'|i18n( 'design/eznewsletter/confirmremove_onhold' )}:</p>

<ul>
{foreach $delete_result as $item}
    <li>{$item.newsletter.name|wash}: {$item.id|wash} - {$item.user_data.email|wash}</li>
{/foreach}
</ul>

<p><b>{'Warning'|i18n( 'design/eznewsletter/confirmremove_onhold' )}:</b></p>
<p>{'Do not proceed unless you are sure.'|i18n( 'design/eznewsletter/confirmremove_onhold' )}</p>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">

<form action={concat( $module.functions.list_bounce.uri, '/onhold/' )|ezurl} method="post" name="BounceEntryRemove">
    <input class="button" type="submit" name="ConfirmRemoveOnHoldEntryButton" value="{'OK'|i18n( 'design/eznewsletter/confirmremove_onhold' )}" />
    <input class="button" type="submit" name="CancelButton" value="{'Cancel'|i18n( 'design/eznewsletter/confirmremove_onhold' )}" />
</form>

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

</div>