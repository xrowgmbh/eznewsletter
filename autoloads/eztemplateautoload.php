<?php

// Operator autoloading

$eZTemplateOperatorArray = array();

$eZTemplateOperatorArray[] = array( 'script' => 'extension/eznewsletter/autoloads/ezhelperoperators.php',
                                    'class' => 'eZHelperOperators',
                                    'operator_names' => array( 'eZDefaultHostname' ) );

$eZTemplateOperatorArray[] = array( 'script' => 'extension/eznewsletter/autoloads/eznewslettertopmenuoperator.php',
                                    'class' => 'eZNewsletterTopMenuOperator',
                                    'operator_names' => array( 'newsletter_topmenu' ) );

$eZTemplateOperatorArray[] = array( 'script' => 'extension/eznewsletter/autoloads/ezcurrentsiteaccessoperator.php',
                                    'class' => 'eZCurrentSiteaccessOperator',
                                    'operator_names' => array( 'current_siteaccess' ) );
