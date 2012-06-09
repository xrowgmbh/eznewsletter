{set-block variable=$subject scope=root}{'Newsletter subscription activation'|i18n( 'design/eznewsletter/sendout/subscription' )} - {$subscriptionList.name}{/set-block}

{'Hello,

Thank you for subscribing to %listName.'|i18n( 'design/eznewsletter/sendout/subscription',,hash( '%name', $subscription.name,
                                               '%listName', $subscriptionList.name ) )}
