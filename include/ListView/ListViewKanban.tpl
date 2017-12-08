<link rel="stylesheet" href="include/javascript/jqwidgets450/jqwidgets/styles/jqx.base.css" type="text/css"/>
<link rel="stylesheet" href="include/javascript/jqwidgets450/jqwidgets/styles/jqx.energyblue.css" type="text/css"/>
<style>
    {literal}
        #MassAssign_SecurityGroups, #select_actions_disabled_ {
            display: none;
        }

        .jqx-kanban-item-color-status {
            width: 100%;
            height: 25px;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            position:relative;
            margin-top:0px;
            top: 0px;
        }
        .jqx-kanban-item {
            padding-top: 0px;
        }
        .jqx-kanban-item-text {
            padding-top: 6px;
        }
        .jqx-kanban-item-avatar {
            top: 2px;
        }
        .jqx-kanban-template-icon {
            position: absolute;
            right: 3px;
            top:12px;
        }
        .jqx-kanban-column {
            width: 220px !important;
        }
    {/literal}
</style>

<script type="text/javascript" src="include/javascript/jqwidgets450/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="include/javascript/jqwidgets450/jqwidgets/jqxsortable.js"></script>
<script type="text/javascript" src="include/javascript/jqwidgets450/jqwidgets/jqxkanban.js"></script>
<script type="text/javascript" src="include/javascript/jqwidgets450/jqwidgets/jqxdata.js"></script>

{$multiSelectData}

<div style="overflow: visible;" id="mainSplitter">
    {if $NO_CONFIG}
        <div>
            you do not yet config kanban views. Please config <a href="index.php?module=Administration&action=KanbanConf">here</a>
        </div>
    {else}
        <script type="text/javascript">
            var kbwidth = {$KBWIDTH};
            var columns = {$COLUMNS};
            var localData = {$SOURCE};
            var ldResource = {$RESOURCE};
        </script>

        <div id="kanban" style="height: 440px"></div>

        {literal}
            <script type="text/javascript">
                $(document).ready(function () {
                    var fields = [
                        {name: "className", map: "module", type: "string"},
                        {name: "id", type: "string"},
                        {name: "status", map: "state", type: "string"},
                        {name: "text", map: "label", type: "string"},
                        {name: "tags", type: "string"},
                        {name: "color", map: "hex", type: "string"},
                        {name: "resourceId", type: "string"}
                    ];

                    var source = {
                        localData: localData,
                        dataType: "array",
                        dataFields: fields
                    };

                    var dataAdapter = new $.jqx.dataAdapter(source);

                    var resourcesAdapterFunc = function () {
                        var resourcesSource =
                            {
                                localData: ldResource,
                                dataType: "array",
                                dataFields: [
                                    {name: "id", type: "string"},
                                    {name: "name", type: "string"},
                                    {name: "image", type: "string"},
                                    {name: "common", type: "boolean"}
                                ]
                            };
                        var resourcesDataAdapter = new $.jqx.dataAdapter(resourcesSource);
                        return resourcesDataAdapter;
                    };

                    $('#kanban').jqxKanban({
                        template: "<div class='jqx-kanban-item' id=''>"
                        + "<div class='jqx-kanban-item-color-status'></div>"
                        + "<div class='jqx-kanban-item-avatar'></div>"
                        + "<div class='jqx-kanban-item-text'></div>"
                        + "<div style='display: none;' class='jqx-kanban-item-footer'></div>"
                        + "</div>",
                        resources: resourcesAdapterFunc(),
                        source: dataAdapter,
                        columns: columns,
                        itemRenderer: function(element, item, resource) {
                            $(element).find(".jqx-kanban-item-color-status").html("" +
                                "<span style='line-height: 23px; margin-left: 5px; color:white;'>" + item.tags + "</span>"
                            );
                        },
                        width: kbwidth,
                        height: 440
                    });

                    $('#kanban').on('itemAttrClicked', function (event) {
                        var args = event.args;
                        var itemId = args.itemId;
                        var item = args.item;
                        var attribute = args.attribute;

                        if (attribute == 'text') {
                            location.href = 'index.php?module=' + item.className + '&action=DetailView&record=' + itemId;
                        }
                    });

                    $('#kanban').on('itemMoved', function (event) {
                        var args = event.args;
                        var itemId = args.itemId;
                        //var oldParentId = args.oldParentId;
                        //var newParentId = args.newParentId;
                        var itemData = args.itemData;
                        var oldColumn = args.oldColumn;
                        var newColumn = args.newColumn;

                        $('#kanban_' + itemId).css('opacity', 0.2);

                        $.post('index.php', {
                            module: itemData.className,
                            action: 'Kanban',
                            item_id: itemId,
                            new_value: newColumn.dataField,
                            to_pdf: 1
                        }, function (data) {
                            $('#kanban_' + itemId).css('opacity', 1);
                        });
                    });
                });
            </script>
        {/literal}
    {/if}
</div>
<div class="clear"></div>

{include file='include/ListView/ListViewPagination.tpl'}