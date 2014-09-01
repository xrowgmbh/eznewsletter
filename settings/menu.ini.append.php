<?php /*

[NavigationPart]
Part[eznewsletter]=Newsletter
Part[eznewsletterList]=Listmanagement
Part[eznewsletterSearch]=Search
Part[eznewsletterBounce]=Bouncemanagement
Part[eznewsletter_setup]=Newsletter setup

[TopAdminMenu]
# Activate the newsletter tab here to show the tab
Tabs[]=eznewsletter
#Tabs[]=eznewsletter_setup
# Older layout tabs
#Tabs[]=eznewsletterList
#Tabs[]=eznewsletterSearch
#Tabs[]=eznewsletterBounce

[Topmenu_eznewsletter_setup]
NavigationPartIdentifier=eznewsletter_setup
Name=Newsletter setup
Tooltip=eZ Newsletter setup
URL[]
URL[default]=newsletter_setup/general
Enabled[]
Enabled[default]=true
Enabled[browse]=true
Enabled[edit]=false
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true

[Topmenu_eznewsletter]
NavigationPartIdentifier=eznewsletter
Name=Newsletter
Tooltip=Newsletter menu
URL[]
URL[default]=newsletter/list_type
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true

[Topmenu_eznewsletterList]
NavigationPartIdentifier=eznewsletterList
Name=List management
Tooltip=Newsletter lists menu
URL[]
URL[default]=newsletter/list_subscriptions
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true

[Topmenu_eznewsletterSearch]
NavigationPartIdentifier=eznewsletterSearch
Name=Search subscriber
Tooltip=Subscriber search
URL[]
URL[default]=newsletter/subscription_search
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true

[Topmenu_eznewsletterBounce]
NavigationPartIdentifier=eznewsletterBounce
Name=Bounce management
Tooltip=Newsletter bounces menu
URL[]
URL[default]=newsletter/list_bounce/all
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true

*/ ?>
