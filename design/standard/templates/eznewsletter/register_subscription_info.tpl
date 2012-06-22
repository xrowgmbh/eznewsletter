<h1>{'Thank you for registering'|i18n('ezxnewsletter')}</h1>

<p>{'Thank you for registering to %name.'|i18n( 'ezxnewsletter', '', hash( '%name', $subscriptionList.name ) )|wash}</p>

<a href={ezini( 'SiteSettings', 'DefaultPage' )|ezurl}>{'Continue'|i18n( 'ezxnewsletter' )}</a>

<p></p>
<p></p>
<p>
<a href={"services/newsletter"|ezurl}>{'Back to the newsletter overview'|i18n( 'ezxnewsletter' )}</a> 
</p>