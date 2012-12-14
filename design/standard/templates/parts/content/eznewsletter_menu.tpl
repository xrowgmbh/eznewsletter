{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h4>{'Newslettermenu'|i18n( 'design/eznewsletter/parts/eznewsletter_menu' )}</h4>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<ul>
    <li><div><a href={'newsletter/list_type'|ezurl()}>{'Newslettertypes'|i18n( 'design/eznewsletter/parts/eznewsletter_menu' )}</a></div></li>
    <li><div><a href={'newsletter/list_subscriptions'|ezurl()}>{'Subscription lists'|i18n( 'design/eznewsletter/parts/eznewsletter_menu' )}</a></div></li>
    <li><div><a href={'newsletter/subscription_search'|ezurl()}>{'Search subscriber'|i18n( 'design/eznewsletter/parts/eznewsletter_menu' )}</a></div></li>
    <li><div><a href={'newsletter/list_bounce/all'|ezurl()}>{'Bounce list'|i18n( 'design/eznewsletter/parts/eznewsletter_menu' )}</a></div></li>
    <li><div><a href={'newsletter/bounce_search'|ezurl()}>{'Search bounce'|i18n( 'design/eznewsletter/parts/eznewsletter_menu' )}</a></div></li>
</ul>

{* DESIGN: Content END *}</div></div></div></div></div></div>

{* This is the border placed to the left for draging width, js will handle disabling the one above and enabling this *}
<div id="widthcontrol-handler" class="hide">
<div class="widthcontrol-grippy"></div>
</div>

{if is_set($module_result.newsletter_menu)}
    {include uri=$module_result.newsletter_menu}
{/if}
