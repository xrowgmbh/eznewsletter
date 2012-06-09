{def $newsletter=fetch( newsletter, newsletter_by_object, hash( contentobject_id, $objectID, contentobject_version, $objectVersion ) )
     $recurrence_extensions = ezini( 'RecurrenceSettings', 'conditionExtensions', 'eznewsletter.ini' ) }

{if $newsletter}
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Newsletter recurrence'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>


{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<input type="hidden" name="NewsletterID" value ="{$newsletter.id}"/>

<div class="context-attributes">

    <div class="block float-break">
        <label>{'Recurrence type'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}:</label>
    </div>
    
    <div class="block float-break">

    <div class="element">
        <input type="radio" name="RecurrenceType" value="d" {cond( eq('d', $newsletter.recurrence_type), ' checked="checked"')} />{'Daily'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}
        <label>{'Weekdays'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}:</label>
        <select name="RecurrenceValue_d[]" size="7" multiple="multiple">
            <option value="1" {if eq('d', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(1), ' selected="selected"', '')} {/if}>{'Monday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="2" {if eq('d', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(2), ' selected="selected"', '')} {/if}>{'Tuesday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="3" {if eq('d', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(3), ' selected="selected"', '')} {/if}>{'Wednesday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="4" {if eq('d', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(4), ' selected="selected"', '')} {/if}>{'Thursday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="5" {if eq('d', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(5), ' selected="selected"', '')} {/if}>{'Friday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="6" {if eq('d', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(6), ' selected="selected"', '')} {/if}>{'Saturday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="0" {if eq('d', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(0), ' selected="selected"', '')} {/if}>{'Sunday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
        </select>    
    </div>
    
    <div class="element">
        <input type="radio" name="RecurrenceType" value="w" {cond( eq('w', $newsletter.recurrence_type), ' checked="checked"')} />{'Weekly'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}
        <label>{'Day of week'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}:</label>
        <select name="RecurrenceValue_w[]" size="7">
            <option value="1" {if eq('w', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(1), ' selected="selected"', '')} {/if}>{'Monday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="2" {if eq('w', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(2), ' selected="selected"', '')} {/if}>{'Tuesday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="3" {if eq('w', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(3), ' selected="selected"', '')} {/if}>{'Wednesday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="4" {if eq('w', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(4), ' selected="selected"', '')} {/if}>{'Thursday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="5" {if eq('w', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(5), ' selected="selected"', '')} {/if}>{'Friday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="6" {if eq('w', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(6), ' selected="selected"', '')} {/if}>{'Saturday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            <option value="0" {if eq('w', $newsletter.recurrence_type)} {cond( $newsletter.recurrence_value_list|contains(0), ' selected="selected"', '')} {/if}>{'Sunday'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
        </select>    
    </div>

    <div class="element">
        <input type="radio" name="RecurrenceType" value="m" {cond( eq('m', $newsletter.recurrence_type), ' checked="checked"')} />{'Monthly'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}
        <label>{'Day of month'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}:</label>
        <input type="text" name="RecurrenceValue_m[]" value="{if eq('m', $newsletter.recurrence_type)} {$newsletter.recurrence_value} {/if}" />
    </div>

    <div class="element">
        <input type="radio" name="RecurrenceType" value="" {cond( eq('', $newsletter.recurrence_type), ' checked="checked"')} />{'Deactivated'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}
        <input type="hidden" name="RecurrenceValue[]" value =""/>
    </div>

    </div>

    <div class="block">
        <label>{'Reccurence condition'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}:</label>
        {if $recurrence_extensions|count}
        <select name="RecurrenceCondition">
            <option value="">{'None'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</option>
            {foreach $recurrence_extensions as $extension}
            <option value="{$extension}">{$extension}</option>
            {/foreach}
        </select>
        {else}
        <p>{'No recurrence conditions available'|i18n( 'design/eznewsletter/edit_newsletter_recurrence' )}</p>
        {/if}
    </div>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block float-break">
    <input class="button" type="submit" name="StoreButton" value="{'Store draft'|i18n( 'design/admin/content/edit' )}" title="{'Store the contents of the draft that is being edited and continue editing. Use this button to periodically save your work while editing.'|i18n( 'design/admin/content/edit')}" />
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

<div class="break"></div>
{/if}
