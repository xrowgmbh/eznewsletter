{set-block variable=$subject scope=root}{'Newsletter subscription deactivation'|i18n( 'design/eznewsletter/sendout/unscubscription' )} - {$subscriptionList.name}{/set-block}

{'Hello,

Your subscription to %listName was deactivated.

Thank you for using this service.'|i18n( 'design/eznewsletter/sendout/unscubscription',,hash( '%name', $subscription.name,
                                               '%listName', $subscriptionList.name ) )}
