<?php /*
#[HostSettings]
# Set the default host for sent newsletters here
#defaulthost=http://yourfrontpage.com

[NewsletterAutomapping]
# Enable if attributes of the newslettertype should be mapped to the content object
autoMapping=disabled
# Define the mapping (shema: contentClass[contentClassAttribute]=newsletterTypeAttribute)
# Example if you want to map the attribute 'pretext' from the newsletterType to the attribute 'pretext' of the 'newsletter_issue' content class you do the following
#    newsletter_issue[pretext]=pretext
#newsletter_issue[pretext]=pretext
#newsletter_issue[posttext]=posttext

[NewsletterSendout]
#Select transport class for newsletter sendout. Valid values are SMTP, sendmail or File for pregeneration.
#If SMTP selected, SMTP server must be defined in siteaccess or override settings.
#
#Transport class for preview sendout
PreviewTransport=eZNewsletterSendmailTransport
#PreviewTransport=eZNewsletterFileTransport
#PreviewTransport=eZNewsletterSMTPTransport
#Transport class for newsletter sendout via cronjob
Transport=eZNewsletterSendmailTransport

[NewsletterTypeSettings]
# Class limitation prevents the use of classes as newsletter which are not in this list, if empty all classes can be used
#ClassLimitation[]
#ClassLimitation[]=newsletter_issue

[NewsletterReadcount]
# If confidentRedirect is enabled the read module will not check if the given object has been sent with the newsletter
# This is usefull when you want to count clicks on links which are not in the relation to this object but added by the template
confidentRedirect=enabled
# Possible redirectTargets are:
#  object = after readcount the user will be redirected to the view of the given object
#  newsletter = will redirect the user to the view of the newsletterobject and adds the object as a viewparameter
redirectTarget=newsletter

[RecurrenceSettings]
conditionExtensions[]

*/ ?>
