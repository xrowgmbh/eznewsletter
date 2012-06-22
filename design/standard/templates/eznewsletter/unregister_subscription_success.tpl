<h1>{'Remove subscription'|i18n('newsletteraddons')}</h1>

<form name="remove" method="post" action={concat("newsletteraddons/unregister_subscription/", $UserHash )|ezurl}>

<p>{'Your subscription for the newsletter "%name" has been successfully removed.'|i18n( 'eznewsletteraddons', '', hash( '%name', $NewsletterItem.newsletter.newsletter_type.name ) )|wash}</p>

<input class="button" name="CancelButton" type="submit" value="{'Continue'|i18n( 'eznewsletteraddons' )}" />
</form>