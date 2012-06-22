DROP TABLE IF EXISTS ezsubscription_group;
CREATE TABLE ezsubscription_group (
  id int(11) NOT NULL auto_increment,
  status int(11) NOT NULL default '0',
  created int(11) NOT NULL default '0',
  creator_id int(11) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  description longtext NOT NULL,
  subscriptionlistid_list longtext NOT NULL,
  PRIMARY KEY  (id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS ezsubscription_list;
CREATE TABLE ezsubscription_list (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  url_type int(11) NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  description longtext NOT NULL,
  allow_anonymous int(11) NOT NULL default '0',
  login_steps int(11) NOT NULL default '1',
  require_password int(11) NOT NULL default '1',
  auto_confirm_registered int(11) NOT NULL default '1',
  auto_approve_registered int(11) NOT NULL default '0',
  created int(11) NOT NULL default '0',
  creator_id int(11) NOT NULL default '0',
  related_object_id_1 int(11) default '0',
  related_object_id_2 int(11) default '0',
  related_object_id_3 int(11) default '0',
  status int(11) NOT NULL default '0',
  allowed_siteaccesses longtext NOT NULL,
  PRIMARY KEY  (id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS ez_newsletter_subscription;
CREATE TABLE ez_newsletter_subscription (
  newsletter_id int(11) NOT NULL default '0',
  status int(11) NOT NULL default '0',
  subscription_id int(11) NOT NULL default '0',
  PRIMARY KEY  (newsletter_id,status,subscription_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS eznewslettertype;
CREATE TABLE eznewslettertype (
  id int(11) NOT NULL auto_increment,
  name varchar(255) default NULL,
  subscriptionlist_list_id int(11) default '0',
  contentclass_list varchar(255) default '',
  inbox_id int(11) default '0',
  sender_address varchar(255) default '',
  description longtext NOT NULL,
  defaultsubscriptionlist_id int(11) default '0',
  allowed_output_formats varchar(255) default '',
  allowed_designs varchar(255) default NULL,
  digest_settings int(11) default '0',
  related_object_id_1 int(11) default '0',
  related_object_id_2 int(11) default '0',
  related_object_id_3 int(11) default '0',
  article_pool_object_id int(11) default '0',
  status int(11) NOT NULL default '0',
  created int(11) NOT NULL default '0',
  send_date_modifier int(11) NOT NULL default '0',
  creator_id int(11) NOT NULL default '0',
  personalise int(11) NOT NULL default '1',
  Pretext longtext NOT NULL,
  Posttext longtext NOT NULL,
  allowed_siteaccesses longtext NOT NULL,
  PRIMARY KEY  (id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS ezsendnewsletteritem;
CREATE TABLE ezsendnewsletteritem (
  id int(11) NOT NULL auto_increment,
  newsletter_id int(11) NOT NULL default '0',
  subscription_id int(11) NOT NULL default '0',
  send_status int(11) NOT NULL default '0',
  send_ts int(11) NOT NULL default '0',
  hash varchar(32) default '',
  bounce_id int(11) NOT NULL default '0',
  object_read_ids longtext,
  object_print_ids longtext,
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS ezsubscription;
CREATE TABLE ezsubscription (
  id int(11) NOT NULL auto_increment,
  version_status int(11) NOT NULL default '0',
  subscriptionlist_id int(11) default '0',
  email varchar(255) default '',
  hash varchar(255) default '',
  status int(11) default '0',
  vip int(11) default '0',
  last_active int(11) NOT NULL default '0',
  output_format varchar(255) default '',
  creator_id int(11) NOT NULL default '0',
  created int(11) NOT NULL default '0',
  confirmed int(11) NOT NULL default '0',
  approved int(11) NOT NULL default '0',
  removed int(11) NOT NULL default '0',
  user_id int(11) default '0',
  bounce_count int(11) default '0',
  contentobject_id int(11) default NULL,
  PRIMARY KEY  (id,version_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS ez_bouncedata;
CREATE TABLE ez_bouncedata (
  id int(11) NOT NULL auto_increment,
  newslettersenditem_id int(11) NOT NULL default '0',
  address varchar(255) default '',
  bounce_count int(11) NOT NULL default '0',
  bounce_type int(11) NOT NULL default '0',
  bounce_arrived int(11) NOT NULL default '0',
  bounce_message text NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `eznewsletter`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `eznewsletter` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  `hash` varchar(255) collate utf8_unicode_ci default NULL,
  `output_format` varchar(255) collate utf8_unicode_ci default '',
  `design_to_use` varchar(255) collate utf8_unicode_ci default '',
  `send_date` int(11) NOT NULL default '0',
  `send_status` int(11) NOT NULL default '0',
  `contentobject_id` int(11) NOT NULL default '0',
  `contentobject_version` int(11) NOT NULL default '0',
  `newslettertype_id` int(11) NOT NULL default '0',
  `category` varchar(255) collate utf8_unicode_ci default '',
  `preview_email` varchar(255) collate utf8_unicode_ci default '',
  `recurrence_type` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `recurrence_value` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `recurrence_condition` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `recurrence_last_sent` int(11) NOT NULL default '0',
  `object_relations` longtext collate utf8_unicode_ci,
  `status` int(11) NOT NULL default '0',
  `created` int(11) NOT NULL default '0',
  `creator_id` int(11) NOT NULL default '0',
  `pretext` longtext collate utf8_unicode_ci NOT NULL,
  `posttext` longtext collate utf8_unicode_ci NOT NULL,
  `preview_mobile` varchar(255) collate utf8_unicode_ci default '',
  PRIMARY KEY  (`id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS ezsubscriptionuserdata;
CREATE TABLE ezsubscriptionuserdata (
  id int(11) NOT NULL auto_increment,
  email varchar(255) default '',
  firstname varchar(255) default '',
  name varchar(255) default '',
  password varchar(255) default '',
  hash varchar(255) default '',
  mobile varchar(255) default '',
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS ezrobinsonlist;
CREATE TABLE ezrobinsonlist (
  id int(11) NOT NULL auto_increment,
  value varchar(255) NOT NULL default '',
  type int(11) NOT NULL default '0',
  global int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ezsubscription ADD INDEX(subscriptionlist_id),ADD INDEX(status),ADD INDEX(version_status),ADD INDEX(email),ADD INDEX(user_id);
ALTER TABLE ezsubscriptionuserdata ADD INDEX(id),ADD INDEX(email);
ALTER TABLE ezsendnewsletteritem ADD INDEX(newsletter_id),ADD INDEX(subscription_id);
ALTER TABLE ezrobinsonlist ADD INDEX(value),ADD INDEX(type),ADD INDEX(global);

INSERT INTO `ezsite_data` VALUES ('eznewsletter-version', '1.6.0');
INSERT INTO `ezsite_data` VALUES ('eznewsletter-release', '1');
