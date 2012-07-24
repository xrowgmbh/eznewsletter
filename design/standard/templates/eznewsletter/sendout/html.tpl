{*set-block variable=$emailSender scope=root}{ezini('MailAccountSettings','BounceAddress','bounce.ini')}{/set-block*}
{*set-block variable=$emailSenderName scope=root}{ezini('MailAccountSettings','BounceName','bounce.ini')}{/set-block*}
{def $host_path = eZDefaultHostname()}
{def $sys_path  = ezsys('sitedir')}
{set-block variable=$subject scope=root}{$contentobject.name|wash(string)} - [name]{/set-block}
<html>
<body>
<h1>{$contentobject.name|wash}</h1>

<h2>{'Systeminformation'|i18n( 'extension/eznewsletter' )}</h2>
<p>
{'Hostname'|i18n( 'extension/eznewsletter' )}: {$host_path}<br />
{'Path'|i18n( 'extension/eznewsletter' )}: {$sys_path}
<p>
{foreach $contentobject.contentobject_attributes as $attribute}
<h2>{$attribute.contentclass_attribute.name|wash}</h2>
{attribute_view_gui attribute=$attribute}
{/foreach}

<hr />
<p>Use the following link to <a href={'/newsletteraddons/unregister_subscription/[userhash]'|ezurl()} title="{'Cancel newsletter'|i18n('extension/eznewsletter')}">{'cancel'|i18n('extension/eznewsletter')}</a> your subscription to this newsletter.</p>
</body>
</html>