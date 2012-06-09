<?php /*

[MailAccountSettings]
AccountList[]
AccountList[]=TestAccount

[TestAccount]
ServerName=mail.example.com
ServerPort=143
LoginName=username
Password=password
#Protocol can be either pop3 or imap
Protocol=imap
Flags[]=notls
# List of optional flags you can to add to the connection
# see Reference at http://php.net/manual/en/function.imap-open.php

[EmailSettings]
SubscribeEmail=subscribe@example.com
UnsubscribeEmail=unsubscribe@example.com

*/ ?>
