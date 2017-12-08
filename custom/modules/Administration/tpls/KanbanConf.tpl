{literal}
    <style>
        .kanbanconf tr td {
            padding: 5px 0;
        }
    </style>
{/literal}

<h3>Current Config</h3>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom: 1px solid #666666">
    <tr style="border-bottom: 1px solid #dddddd">
        <td><strong>Module</strong></td>
        <td><strong>Field</strong></td>
        <td><strong>Limit</strong></td>
        <td><strong>Columns</strong></td>
    </tr>

    {foreach from=$CONFIG item=C key=M}
        <tr>
            <td>{$M}</td>
            <td>{$C.field}</td>
            <td>{$C.limit}</td>
            <td>
                {foreach from=$C.views item=column}
                    <strong>{$column}</strong> |
                {/foreach}
            </td>
        </tr>
    {/foreach}
</table>

<form action="index.php" method="post">
    <input type="hidden" name="module" value="Administration">
    <input type="hidden" name="action" value="KanbanConf">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="kanbanconf">
        <tr><td colspan='100'><h2>{$MOD.LBL_LINK_KANBAN_CONFIG_NAME}</h2></td></tr>
        <tr><td colspan='100'>{$MOD.LBL_KANBAN_CONFIG_SECTION_DESCRIPTION}</td></tr>
        <tr><td><br></td></tr>

        <tr>
            <td colspan='100'>
                <table border="0" cellspacing="1" cellpadding="1" class="actionsContainer">
                    <tr>
                        <td>
                            <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button primary" onclick="SUGAR.saveConfigureTabs();this.form.action.value='SaveTabs'; " type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >
                            <input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="this.form.action.value='index'; this.form.module.value='Administration';" type="submit" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
                        </td>
                    </tr>
                </table>

                <div class='add_table' style='margin-bottom:5px'>
                    <table id="KanbanConf" class="themeSettings edit view" style='margin-bottom:0px;' border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="20%" scope="row">{$MOD.LBL_KB_CHOOSE_MODULE}: <span class="required">*</span></td>
                            <td width="30%">
                                <select name="kbmodule" onchange="loadFields(this)">
                                    <option></option>
                                    {html_options options=$MODULES}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%" scope="row">Items per page: </td>
                            <td width="30%"><input type="text" name="kblimit" value=""></td>
                        </tr>
                        <tr>
                            <td width="20%" scope="row">{$MOD.LBL_KB_CHOOSE_FIELD}: <span class="required">*</span></td>
                            <td width="30%">
                                <select name="kbfield" id="chooseFieldKB" onchange="loadOptions(this)"></select>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%" scope="row">{$MOD.LBL_KB_CHOOSE_OPTIONS}: <span class="required">*</span></td>
                            <td width="30%">
                                <select name="kboptions[]" id="chooseOptionsKB" multiple="multiple"></select>
                            </td>
                        </tr>
                    </table>
                </div>

                <table border="0" cellspacing="1" cellpadding="1" class="actionsContainer">
                    <tr>
                        <td>
                            <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" class="button primary" onclick="this.form.module.value='Administration';this.form.action.value='KanbanConf';" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >
                            <input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" class="button" onclick="this.form.action.value='index'; this.form.module.value='Administration';" type="submit" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

{literal}
<script>
    function loadFields(obj) {
        var module = $(obj).val();
        $.get('index.php?to_pdf=1&module=Administration&action=KanbanConf&load=' + module, function (data) {
            $('#chooseFieldKB').html(data.options);
            $('input[name=kblimit]').val(data.limit);
            $('#chooseFieldKB').change();
        });
    }

    function loadOptions(obj) {
        var list = $('#chooseFieldKB option:selected').attr('list');
        var m = $('select[name=kbmodule]').val();
        $('#chooseOptionsKB').load('index.php?to_pdf=1&module=Administration&action=KanbanConf&list=' + list + '&mconf=' + m);
    }
</script>
{/literal}