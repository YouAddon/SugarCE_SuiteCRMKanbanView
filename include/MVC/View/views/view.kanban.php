<?php

/**
 * Created by Caro Team (carodev.com).
 * User: Jacky (jacky@youaddon.com).
 * Year: 2017
 * File: view.kanban.php
 */

require_once('include/MVC/View/views/view.list.php');

/**
 * Class ViewKanban
 * @property SugarBean $bean
 */
class ViewKanban extends ViewList
{
    public function __construct()
    {
        parent::__construct();
    }

    public function display()
    {
        parent::display();
    }

    public function process()
    {
        $this->changeColumn();
        parent::process();
    }

    public function listViewPrepare()
    {
        $module = $GLOBALS['module'];

        $metadataFile = $this->getMetaDataFile();

        if (!file_exists($metadataFile))
            sugar_die($GLOBALS['app_strings']['LBL_NO_ACTION']);

        require($metadataFile);

        $this->listViewDefs = $listViewDefs;

        if (!empty($this->bean->object_name) && isset($_REQUEST[$module . '2_' . strtoupper($this->bean->object_name) . '_offset'])) {//if you click the pagination button, it will populate the search criteria here
            if (!empty($_REQUEST['current_query_by_page'])) {//The code support multi browser tabs pagination
                $blockVariables = array('mass', 'uid', 'massupdate', 'delete', 'merge', 'selectCount', 'request_data', 'current_query_by_page', $module . '2_' . strtoupper($this->bean->object_name) . '_ORDER_BY');
                if (isset($_REQUEST['lvso'])) {
                    $blockVariables[] = 'lvso';
                }
                $current_query_by_page = json_decode(html_entity_decode($_REQUEST['current_query_by_page']), true);
                foreach ($current_query_by_page as $search_key => $search_value) {
                    if ($search_key != $module . '2_' . strtoupper($this->bean->object_name) . '_offset' && !in_array($search_key, $blockVariables)) {
                        if (!is_array($search_value)) {
                            $_REQUEST[$search_key] = securexss($search_value);
                        } else {
                            foreach ($search_value as $key => &$val) {
                                $val = securexss($val);
                            }
                            $_REQUEST[$search_key] = $search_value;
                        }
                    }
                }
            }
        }

        if (!empty($_REQUEST['saved_search_select'])) {
            if ($_REQUEST['saved_search_select'] == '_none' || !empty($_REQUEST['button'])) {
                $_SESSION['LastSavedView'][$_REQUEST['module']] = '';
                unset($_REQUEST['saved_search_select']);
                unset($_REQUEST['saved_search_select_name']);

                //use the current search module, or the current module to clear out layout changes
                if (!empty($_REQUEST['search_module']) || !empty($_REQUEST['module'])) {
                    $mod = !empty($_REQUEST['search_module']) ? $_REQUEST['search_module'] : $_REQUEST['module'];
                    global $current_user;
                    //Reset the current display columns to default.
                    $current_user->setPreference('ListViewDisplayColumns', array(), 0, $mod);
                }
            } else if (empty($_REQUEST['button']) && (empty($_REQUEST['clear_query']) || $_REQUEST['clear_query'] != 'true')) {
                $this->saved_search = loadBean('SavedSearch');
                $this->saved_search->retrieveSavedSearch($_REQUEST['saved_search_select']);
                $this->saved_search->populateRequest();
            } elseif (!empty($_REQUEST['button'])) { // click the search button, after retrieving from saved_search
                $_SESSION['LastSavedView'][$_REQUEST['module']] = '';
                unset($_REQUEST['saved_search_select']);
                unset($_REQUEST['saved_search_select_name']);
            }
        }

//        $this->storeQuery = new StoreQuery();
//        if (!isset($_REQUEST['query'])) {
//            $this->storeQuery->loadQuery($this->module);
//            $this->storeQuery->populateRequest();
//        } else {
//            $this->storeQuery->saveFromRequest($this->module);
//        }

        $this->seed = $this->bean;

        $displayColumns = array();
        if (!empty($_REQUEST['displayColumns'])) {
            foreach (explode('|', $_REQUEST['displayColumns']) as $num => $col) {
                if (!empty($this->listViewDefs[$module][$col]))
                    $displayColumns[$col] = $this->listViewDefs[$module][$col];
            }
        } else {
            foreach ($this->listViewDefs[$module] as $col => $this->params) {
                if (!empty($this->params['default']) && $this->params['default'])
                    $displayColumns[$col] = $this->params;
            }
        }

        $this->params = array('massupdate' => true);
        if (!empty($_REQUEST['orderBy'])) {
            $this->params['orderBy'] = $_REQUEST['orderBy'];
            $this->params['overrideOrder'] = true;
            if (!empty($_REQUEST['sortOrder'])) $this->params['sortOrder'] = $_REQUEST['sortOrder'];
        }

        $this->lv->displayColumns = $displayColumns;

        $this->module = $module;

        $this->prepareSearchForm();

        if (isset($this->options['show_title']) && $this->options['show_title']) {
            $moduleName = isset($this->seed->module_dir) ? $this->seed->module_dir : $GLOBALS['mod_strings']['LBL_MODULE_NAME'];
            echo $this->getModuleTitle(true);
        }
    }

    public function listViewProcess()
    {
        if (!$this->headers) {
            return;
        }

        global $app_list_strings, $app_strings;

        $_REQUEST['action'] = 'Kanban';

        //$this->processSearchForm();
        //$this->lv->searchColumns = $this->searchForm->searchColumns;
        //$savedSearchName = empty($_REQUEST['saved_search_select_name']) ? '' : (' - ' . $_REQUEST['saved_search_select_name']);

        $this->lv->ss->assign('APP', $app_strings);
        $this->lv->ss->assign("SEARCH", false);

        if (empty($_REQUEST['search_form_only']) || $_REQUEST['search_form_only'] == false) {
            $no_config = true;

            // check config
            $config = array();

            $module = $this->module;
            $config[$module] = array();

            $file_path = 'custom/modules/Administration/kanban.conf.php';
            if (is_file($file_path)) {
                $config = include $file_path;
            }

            if (!empty($config[$module]) && !empty($config[$module]['views'])) {
                $no_config = false;

                $field = $config[$module]['field'];
                $field_columns = $config[$module]['views'];
                $field_options = $app_list_strings[$this->bean->field_defs[$field]['options']];

                $columns = array();

                foreach ($field_options as $v => $l) {
                    if (in_array($v, $field_columns)) {
                        $columns[] = array(
                            'text' => $l,
                            'dataField' => $v
                        );
                    }
                }

                $this->lv->ss->assign('KBWIDTH', (count($columns) * 220 + 20));
                $this->lv->ss->assign('COLUMNS', json_encode($columns));

                // setup view
                // where
                $where = $this->bean->table_name . '.' . $field . " IN ('" . implode("','", $field_columns) . "')";
                if ($this->where) {
                    $this->where .= ' AND ' . $where;
                } else {
                    $this->where = $where;
                }

                $this->params['custom_select'] = ' , ' . $this->bean->table_name . '.' . $field;

                // limit
                $limit = !empty($config[$module]['limit']) ? $config[$module]['limit'] : 10000;
                $this->lv->setup($this->seed, 'include/ListView/ListViewKanban.tpl', $this->where, $this->params, 0, $limit);

                // get data kanban
                $data = $this->lv->data['data'];

                $source = array();

                foreach ($data as $row) {
                    $source[] = array(
                        'module' => $module,
                        'id' => $row['ID'],
                        'state' => $row[strtoupper($field)],
                        'label' => $row['NAME'],
                        'tags' => $row['ASSIGNED_USER_NAME'],
                        'hex' => '#5dc3f0',
                        'resourceId' => $row['ASSIGNED_USER_ID']
                    );
                }

                $this->lv->ss->assign('SOURCE', json_encode($source));

                // resource
                $this->lv->ss->assign('RESOURCE', json_encode($this->getResources()));
            }

            $this->lv->ss->assign('NO_CONFIG', $no_config);

            $savedSearchName = empty($_REQUEST['saved_search_select_name']) ? '' : (' - ' . $_REQUEST['saved_search_select_name']);
            echo $this->lv->display();
        }
    }

    public function changeColumn()
    {
        if (!empty($_POST['item_id']) && !empty($_POST['new_value'])) {
            $file_path = 'custom/modules/Administration/kanban.conf.php';
            if (is_file($file_path)) {
                $config = include $file_path;
            }

            $module = $_POST['module'];
            $record = $_POST['item_id'];
            $field = $config[$module]['field'];
            $value = $_POST['new_value'];

            if (!empty($config[$module]) && !empty($config[$module]['views'])) {
                $focus = BeanFactory::getBean($module, $record);
                $focus->$field = $value;
                $focus->save();
            }

            sugar_cleanup(true);
        }
    }

    public function getResources()
    {
        $result = $this->bean->db->query("SELECT * FROM users WHERE deleted = 0 AND is_group = 0 AND status = 'Active'");

        $users = array();
        while ($row = $this->bean->db->fetchRow($result)) {
            $users[] = array(
                'id' => $row['id'],
                'name' => $row['user_name'],
                'image' => !empty($row['photo']) ? 'index.php?entryPoint=download&id='. $row['id'] .'_photo&type=Users' : 'include/javascript/jqwidgets450/jqwidgets/styles/images/common.png',
            );
        }

        return $users;
    }
}