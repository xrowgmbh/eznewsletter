ALTER TABLE eznewsletter CHANGE template_to_use design_to_use VARCHAR( 255 ) DEFAULT '';
ALTER TABLE `ezsubscription_list` ADD `allowed_siteaccesses` longtext NOT NULL AFTER `status` ;
ALTER TABLE `eznewslettertype` ADD `allowed_siteaccesses` longtext NOT NULL AFTER `Posttext` ;
UPDATE ezsite_data SET value = '1.6.0' WHERE name = 'eznewsletter-version';
UPDATE ezsite_data SET value = '1' WHERE name = 'eznewsletter-release';