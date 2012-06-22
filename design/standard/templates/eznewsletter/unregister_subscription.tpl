<h1>{'Remove subscription'|i18n('newsletteraddons')}</h1>

<form name="remove" method="post" action={concat("newsletteraddons/unregister_subscription/", $UserHash )|ezurl}>

<p>{'Are you sure you want to remove the subscription for the newsletter "%name"?'|i18n( 'eznewsletteraddons', '', hash( '%name', $NewsletterItem.newsletter.newsletter_type.name ) )|wash}</p>

<input class="button" name="OKButton" type="submit" value="{'Yes'|i18n( 'eznewsletteraddons' )}" />
<input class="button" name="CancelButton" type="submit" value="{'No'|i18n( 'eznewsletteraddons' )}" />
</form>