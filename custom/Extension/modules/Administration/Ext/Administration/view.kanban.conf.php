<?php
/**
 * Created by Caro Team (carodev.com).
 * User: Jacky (jacky@youaddon.com).
 * Year: 2017
 * File: view.kanban.conf.php
 */

$admin_option_defs = array();

$admin_option_defs['Administration']['<section key>'] = array(
    //Icon name. Available icons are located in ./themes/default/images
    'Administration',

    //Link name label
    'LBL_LINK_KANBAN_CONFIG_NAME',

    //Link description label
    'LBL_LINK_KANBAN_CONFIG_DESCRIPTION',

    //Link URL
    './index.php?module=Administration&action=KanbanConf',
);

$admin_group_header[] = array(
    //Section header label
    'LBL_KANBAN_CONFIG_SECTION_HEADER',

    //$other_text parameter for get_form_header()
    '',

    //$show_help parameter for get_form_header()
    false,

    //Section links
    $admin_option_defs,

    //Section description label
    'LBL_KANBAN_CONFIG_SECTION_DESCRIPTION'
);