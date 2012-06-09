{* Subscription template *}<div class="context-block">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{"Add Subscription"|i18n( 'design/eznewsletter/user_addsubscription' )}</h1>

<div class="header-mainline"></div>

</div></div></div></div></div></div>

<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<div class="break"></div>

<table>
<tr>
<td>{'Please select a list you want to add:'|i18n( 'design/eznewsletter/user_addsubscription' )}
</td>
</tr>
<tr>
<td>
{if count($additionalLists)|lt(1)}
{'No subscriptions available'|i18n( 'design/eznewsletter/user_addsubscription' )}
{else}
    <select name="AddSubscriptionID[]" class="halfbox" multiple="multiple" size="{count($additionalLists)|inc(1)}"
{if is_set($newUser)}
disabled="disabled"
{/if}
    >
    {foreach $additionalLists as $list}
	<option value="{$list.id}">{$list.name|wash}</option>
    {/foreach}
    </select>
{/if}
</td>
</tr>
</table>

<input type="Submit" name="AddSubscription" class="button" value="&nbsp; {"Update"|i18n( 'design/eznewsletter/user_addsubscription' )}&nbsp;"
{if is_set($newUser)}
disabled="disabled"
{/if}
><br>&nbsp;<br>

<div class="break"></div><div class="break"></div><div class="break"></div>
</div></div></div></div></div></div>

</div>
