{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* eZNewsletter - subscriptions list *}
{def $base_uri='newsletter/subscription_search'}

{if gt($subscriberCount,100)}
    <div class="message-warning">
            <h2>{'Search result is greater than 50, please check your search text!'|i18n( 'design/eznewsletter/subscription_search' )}</h2>
        </div>
{/if}

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'Subscription search'|i18n( 'design/eznewsletter/subscription_search' )}</h2>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<form name="subscription_search" method="post" action={$base_uri|ezurl} class="inline">

<div class="context-attributes">

    {* Name. *}
        <div class="block">
        <label>{"Enter name or email address:"|i18n( 'design/eznewsletter/subscription_search' )}</label>
        <input class="halfbox" id="searchString" type="text" name="searchString" value="{$searchString|wash}" title="{'Search text.'|i18n( 'design/eznewsletter/subscription_search' )}" />
    </div>

    {* Edit *}
    <div class="block">
        <input class="button" type="submit" name="SearchButton" value="{'Search'|i18n( 'design/admin/rss/edit_import' )}" title="{'Search subscription.'|i18n( 'design/eznewsletter/subscription_search' )}" />
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
<h2 class="context-title">{'Subscriber list'|i18n( 'design/eznewsletter/subscription_search' )}</h2>
{* DESIGN: Subline *}<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN:  START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
<div class="context-attributes">

{* Subscription list table. *}
<div class="overflow-table">

<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection'|i18n( 'design/eznewsletter/subscription_search' )}" title="{'Invert selection'|i18n( 'design/eznewsletter/subscription_search' )}" onclick="ezjs_toggleCheckboxes( document.subscription_list, 'SubscriptionIDArray[]' ); return false;" /></th>
    <th class="tight">{'ID'|i18n('eznewslettert')}</th>
    <th>{'First name'|i18n( 'design/eznewsletter/subscription_search' )}</th>
    <th>{'Last name'|i18n( 'design/eznewsletter/subscription_search' )}</th>
    <th>{'Email'|i18n( 'design/eznewsletter/subscription_search' )}</th>
    <th>{'Mobile'|i18n( 'design/eznewsletter/subscription_search' )}</th>
    
{*    <th>{'Created'|i18n( 'design/eznewsletter/subscription_search' )}</th>
    <th>{'Confirmed'|i18n( 'design/eznewsletter/subscription_search' )}</th>
    <th>{'Approved'|i18n( 'design/eznewsletter/subscription_search' )}</th>
    <th>{'Removed'|i18n( 'design/eznewsletter/subscription_search' )}</th> *}
    <th></th>
</tr>
{foreach $subscriberBounce as $subscriberB}

<tr class="{$seq}">
    <td><input type="checkbox" name="SubscriptionIDArray[]" value="{$subscriberB.id}" title="{'Select subscriber for removal'|i18n( 'design/eznewsletter/subscription_search' )}" /></td>
    <td class="number" align="right">{$subscriberB.id}</td>
    <td>{$subscriber.firstname|wash}</td>
    <td>{$subscriberB.name|wash}</td>
    <td><a href="mailto:{$subscriber.email|wash}">{$subscriber.email|wash}</a></td>
    <td>{$subscriber.mobile|wash}</td>
{*    <td>{cond( $subscriber.created|gt(0), $subscriber.created|l10n( shortdatetime ), 'n/a'|i18n( 'design/eznewsletter/subscription_search' ) )}</td>
    <td>{cond( $subscriber.confirmed|gt(0), $subscriber.confirmed|l10n( shortdatetime ), 'n/a'|i18n( 'design/eznewsletter/subscription_search' ) )}</td>
    <td>{cond( $subscriber.approved|gt(0), $subscriber.approved|l10n( shortdatetime ), 'n/a'|i18n( 'design/eznewsletter/subscription_search' ) )}</td>
    <td>{cond( $subscriber.removed|gt(0), $subscriber.removed|l10n( shortdatetime ), 'n/a'|i18n( 'design/eznewsletter/subscription_search' ) )}</td> *}
    <td>
    <a href={concat( '/newsletter/modify_subscription/', $subscriberB.hash )|ezurl}><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'design/eznewsletter/subscription_search' )}" title="{'Edit the <%newsletter_name> subscription.'|i18n( 'eznewsletter',, hash( '%newsletter_name', $subscriber.name ) )|wash}" /></a>
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
    <input class="button" type="submit" name="RemoveSubscriptionButton" value="{'Remove selected'|i18n( 'design/eznewsletter/subscription_search' )}" title="{'Remove selected subscription.'|i18n( 'design/eznewsletter/subscription_search' )}">
</form>
</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

{* DESIGN: Content END *}</div></div></div>

