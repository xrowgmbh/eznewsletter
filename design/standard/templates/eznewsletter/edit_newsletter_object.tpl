{def $newsletter=fetch( 'newsletter', 'newsletter_by_object', hash( 'contentobject_id', $objectID, 'contentobject_version', $objectVersion ) )}

{if $newsletter}
{* DESIGN: Header START *}
<div class="box-header">
    <div class="box-tc">
    <div class="box-ml">
    <div class="box-mr">
    <div class="box-tl">
    <div class="box-tr">
        <h1 class="context-title">{'Edit newsletter'|i18n( 'design/eznewsletter/edit_newsletter_object' )}</h1>
{* DESIGN: Mainline *}
    <div class="header-mainline"></div>

{* DESIGN: Header END *}
    </div>
    </div>
    </div>
    </div>
    </div>
</div>

<div class="box-ml">
    <div class="box-mr">
        <div class="box-content">
{* DESIGN: Content START *}
    <input type="hidden" name="NewsletterID" value ="{$newsletter.id}"/>

    <div class="context-attributes">

        {* Name. *}
        <div class="block float-break">
            <label>{'Name'|i18n( 'design/eznewsletter/edit_newsletter_object' )}:</label>
            <input class="box" id="newsletterName" type="text" name="NewsletterName" value="{$newsletter.name|wash}" />
        </div>

        {* Newsletter send date *}
        <div class="block float-break">
            <label>{'Newsletter send date'|i18n( 'design/eznewsletter/edit_newsletter_object' )}:</label>
            <div class="date">
                <div class="element">
                    <label>{'Year'|i18n( 'design/standard/content/datatype' )}:</label>
                    <input type="text" name="newsletter_datetime_year_{$objectID}" id="newsletter_send_date_year" size="5" value="{$newsletter.send_year|wash}" />
                </div>
                <div class="element">
                    <label>{'Month'|i18n( 'design/standard/content/datatype' )}:</label>
                    <input type="text" name="newsletter_datetime_month_{$objectID}" id="newsletter_send_date_month" size="3" value="{$newsletter.send_month|wash}" />
                </div>

                <div class="element">
                    <label>{'Day'|i18n( 'design/standard/content/datatype' )}:</label>
                    <input type="text" name="newsletter_datetime_day_{$objectID}" id="newsletter_send_date_day" size="3" value="{$newsletter.send_day|wash}" />
                </div>
                
                <div class="element">
                <img class="datepicker-icon" src={"calendar_icon.png"|ezimage} id="newsletter_datetime_cal_{$objectID}" width="24" height="28" onclick="showDatePicker( 'newsletter', '{$objectID}', 'datetime' );" style="cursor: pointer;" />
                <div id="newsletter_datetime_cal_container_{$objectID}" style="display: none; position: absolute;"></div>
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    &nbsp;
                </div>
                
            </div>
            
            

            <div class="time">
                <div class="element">
                    <label>{'Hour'|i18n( 'design/standard/content/datatype' )}:</label>
                    <input type="text" name="newsletter_datetime_hour_{$objectID}" id="newsletter_send_date_hour" size="3" value="{$newsletter.send_hour|wash}" />
                </div>

                <div class="element">
                    <label>{'Minute'|i18n( 'design/standard/content/datatype' )}:</label>
                    <input type="text" name="newsletter_datetime_minute_{$objectID}" id="newsletter_send_date_minute" size="3" value=" {$newsletter.send_minute|wash}" />
                </div>

            </div>
        </div>
        {* Newsletter category *}
        <div class="block float-break">
            <label>{'Newsletter category'|i18n( 'design/eznewsletter/edit_newsletter_object' )}:</label>
            <input class="box" type="text" name="NewsletterCategory" value="{$newsletter.category}" />
        </div>

        {* choose a format for this newsletter, available are set in type *}
        <div class="block float-break">
            <label>{'Design to use'|i18n( 'design/eznewsletter/edit_newsletter_object' )}:</label>
            {def $allowed_designs = $newsletter.newsletter_type.allowed_designs_array}
            
            <ul class="NewsletterDesignList">
            {def $design_name  = false()}
            {def $design_image = false()}
            {foreach $allowed_designs as $design}
                {set $design_name = ezini( $design, 'Description', 'newsletterdesigns.ini' )}
                {set $design_image = ezini( $design, 'PreviewImage', 'newsletterdesigns.ini' )}
                <li>
                <img alt="{$design_name|wash()}" src={$design_image|ezimage()}>
                <input type="radio" name="DesignToUse" value="{$design}" {cond( eq($design, $newsletter.design_to_use), 'checked="checked"')}>{$design_name|wash}
                </li>
            {/foreach}
            </ul>
        </div>
        {* View online preview *}
        <div class="block float-break">
            <input class="button" type="Submit" name="NewsletterPreview" value="{'Preview'|i18n( 'design/eznewsletter/edit_newsletter_object' )}" />
        </div>

        {* Send preview fields *}
        <div class="block float-break">
            <label>{'Send preview address'|i18n( 'design/eznewsletter/edit_newsletter_object' )}</label>
            <input class="box" type="text" name="NewsletterPreviewEmail" value="{$newsletter.preview_email|wash}" />
        </div>

        <div class="block float-break">
            <label>{'Send preview mobile number'|i18n( 'design/eznewsletter/edit_newsletter_object' )}</label>
            <input class="box" type="text" name="NewsletterPreviewMobile" value="{$newsletter.preview_mobile|wash}" />
        </div>
  
    </div>

{* DESIGN: Content END *}
        </div>
    </div>
</div>

<div class="controlbar">
{* DESIGN: Control bar START *}
    <div class="box-bc">
    <div class="box-ml">
    <div class="box-mr">
    <div class="box-tc">
    <div class="box-bl">
    <div class="box-br">
        <div class="block float-break">
            <input class="button" type="Submit" name="NewsletterSendPreview" value="{'Send preview'|i18n( 'design/eznewsletter/edit_newsletter_object' )}"/>
        </div>
{* DESIGN: Control bar END *}
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
</div>

<div class="break"></div>
{/if}
