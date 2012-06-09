<?php /*

[CronjobSettings]
ExtensionDirectories[]=eznewsletter
Scripts[]=send_newsletter.php
Scripts[]=check_bounce.php
Scripts[]=recurring_newsletter.php
Scripts[]=conditional_send.php
Scripts[]=build_list.php
Scripts[]=cluster_send.php
Scripts[]=email_subscribe.php
Scripts[]=send_sms.php

[CronjobPart-send_newsletter]
Scripts[]
Scripts[]=build_list.php
Scripts[]=send_newsletter.php
Scripts[]=recurring_newsletter.php

[CronjobPart-check_bounce]
Scripts[]
Scripts[]=check_bounce.php

[CronjobPart-recurring_newsletter]
Scripts[]
Scripts[]=recurring_newsletter.php

[CronjobPart-conditional_send]
Scripts[]
Scripts[]=conditional_send.php

[CronjobPart-build_list]
Scripts[]
Scripts[]=build_list.php

[CronjobPart-cluster_send]
Scripts[]
Scripts[]=build_list.php
Scripts[]=cluster_send.php
Scripts[]=recurring_newsletter.php

[CronjobPart-email_subscribe]
Scripts[]
Scripts[]=email_subscribe.php

[CronjobPart-send_sms]
Scripts[]
Scripts[]=send_sms.php

*/ ?>
