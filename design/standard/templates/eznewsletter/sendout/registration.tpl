{set-block variable=$subject scope=root}{'Newsletter subscription verification'|i18n( 'design/eznewsletter/sendout/registration' )}{/set-block}

{'Hi %name

Thank you for subscribing to the list %listName. To activate your subscription, visit this link: %link .

To edit your settings, visit : %settingsLink'|i18n( 'design/eznewsletter/sendout/registration',,hash( '%name', $subscription.name,
                                               '%listName', $subscriptionList.name,
                                               '%settingsLink', concat( 'http://', $hostname, concat( '/newsletter/user_settings/', cond( $userData, $userData.hash, 'f' ) )|ezurl(no) ),
                                               '%link', concat( 'http://', $hostname, concat( '/newsletter/subscription_activate/', $subscription.hash )|ezurl(no) ) ) )}
