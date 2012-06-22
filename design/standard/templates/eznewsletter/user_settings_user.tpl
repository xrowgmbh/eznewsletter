<h1>{'Manage Subscriptions'|i18n( 'ezxnewsletter' )}</h1>

<form method="post" action={concat('/newsletter/user_settings/',$hash)|ezurl}>

<input type="hidden" name="original_email" value="{$userData.email}" />

<h5 class="no-top-border">{'Your settings'|i18n( 'ezxnewsletter' )}</h5>

{if is_set($warning)}
    <div class="message-warning">
        <h2>{$warning|wash}</h2>
    </div>
{/if}
{*
<label>{'Firstname'|i18n( 'ezxnewsletter' )}:</label>
<input class="halfbox" id="firstname" type="text" name="FirstName" value="{$userData.firstname|wash}" />

<label>{'Name'|i18n( 'ezxnewsletter' )}:</label>
<input class="halfbox" id="username" type="text" name="Name" value="{$userData.name|wash}" />

<label>{'Mobile'|i18n( 'ezxnewsletter' )}:</label>
<input class="halfbox" id="mobile" type="text" name="Mobile" value="{$userData.mobile|wash}" />
*}
<label>{'Email'|i18n( 'ezxnewsletter' )}:</label>
<input class="halfbox" id="email" type="text" name="Email" value="{$userData.email|wash}" />

{if $userData.password}

        <label>{"Password"|i18n( 'ezxnewsletter' )}:</label>
        <input name="Password1" size="25" type="password" value="password" />
        <input name="Password2" size="25" type="password" value="password" />

{/if}


    <input class="button" type="submit" name="UpdateSubscriptions" value="{'Update'|i18n( 'ezxnewsletter' )}" title="{'Update settings.'|i18n( 'ezxnewsletter' )}" />

<h5>{'Your subscriptions'|i18n( 'ezxnewsletter' )}</h5>


<table class="list">
<tr>
    <th>{'Name'|i18n('ezxnewsletter')}</th>
    <th>{'Subscribed'|i18n( 'ezxnewsletter' )}</th>
</tr>
{if count($subscriptionList)|lt(1)}
<tr><td></td><td>{'No subscriptions available'|i18n( 'ezxnewsletter' )}</td></tr>
{else}
    {foreach $subscriptionList as $subscription
             sequence array( bglight, bgdark ) as $seq}
        <tr class="{$seq}">
        <td>{$subscription.subscription_list.name|wash}</td>
        <td>
	    <input style="border: 0px none ;" name="Status_{$subscription.id}" value="1" type="checkbox" 
	    {if $removedStatusList|contains($subscription.status)}{else}{'checked="checked"'}{/if}
	    {if $allowedStatusList|contains($subscription.status)}{else}{'disabled="disabled"'}{/if}>
        </td>

        </tr>
    {/foreach}
{/if}
</table>

    <input class="button" type="submit" name="UpdateSubscriptions" value="{'Update'|i18n( 'ezxnewsletter' )}" title="{'Update settings.'|i18n( 'ezxnewsletter' )}" />

</form>
<p></p>
<p></p>
<p>
<a href={"services/newsletter"|ezurl}>{'Back to the newsletter overview'|i18n( 'ezxnewsletter' )}</a> 
</p>
