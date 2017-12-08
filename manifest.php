<?php
/**
 * Created YouAddOn Team.
 * Website www.youaddon.com
 * User: jacky@youaddon.com
 * Mail: support@youaddon.com
 * Skype: youaddon
 * File manifest.php.
 */

$manifest = array(
    array(
        'acceptable_sugar_versions' => array(),
    ),
    array(
        'acceptable_sugar_flavors' => array(
            'CE',
            'PRO',
            'ENT',
        ),
    ),
    'readme' => 'See ReadMe.txt',
    'key' => 'UA',
    'author' => 'youaddon',
    'description' => 'Kanban View for all modules',
    'icon' => '',
    'is_uninstallable' => false,
    'name' => 'Ua_KanbanPro',
    'published_date' => '2017-01-10 00:00:00',
    'type' => 'module',
    'version' => '1.0',
    'remove_tables' => 'prompt',
);

$installdefs = array(
    'copy' => array(
        array(
            'from' => '<basepath>/custom',
            'to' => 'custom',
        ),
        array(
            'from' => '<basepath>/include',
            'to' => 'include',
        ),
    ),
);