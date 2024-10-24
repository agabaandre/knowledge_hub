
    <div class='row-height-dialog'>
        <label class="dialog-label-text" for="height-editor" data-bind="text: res.rowHeightDialog.rowHeight">Row
            height:</label>
        <input id="height-editor" type="text" name="row-height">
    </div>
    <div class="password-dialog">
    <div>
        <span id="passwordMessage"></span>
    </div>
    <br/>
    <div>
        <label class="dialog-label-text" for="password-editor" data-bind="text: res.passwordDialog.passwordLabel">Password:</label>
    </div>
    <div>
        <input id="password-editor" type="password" name="password" class="passwordInput">
    </div>
</div>
    <div class='column-width-dialog'>
    <label class="dialog-label-text" for="width-editor" data-bind="text: res.columnWidthDialog.columnWidth">Column
        width:</label>
    <input id="width-editor" type="text" name="column-width">
</div>
    <div class='standard-width-dialog'>
    <label class="dialog-label-text" for="standard-width-editor" data-bind="text: res.standardWidthDialog.columnWidth">Standard
        column width:</label>
    <input id="standard-width-editor" type="text" name="standard-column-width">
</div>
    <div class='standard-height-dialog'>
    <label class="dialog-label-text" for="standard-height-editor" data-bind="text: res.standardHeightDialog.rowHeight">Standard
        row height:</label>
    <input id="standard-height-editor" type="text" name="standard-row-height">
</div>
    <div class='insert-cells-dialog'>
    <label data-bind="text: res.insertCellsDialog.title">Insert</label>
    <span class="horizontalseparator"></span>
    <br/>
    <input id="insert-type-editor1" type="radio" name="insert-type" value="shiftcellsright"/>
    <label for="insert-type-editor1" data-bind="text: res.insertCellsDialog.shiftcellsright">Shift cells right</label>
    <br/>
    <input id="insert-type-editor2" class="default-radio" type="radio" name="insert-type" value="shiftcellsdown"/>
    <label for="insert-type-editor2" data-bind="text: res.insertCellsDialog.shiftcellsdown">Shift cells down</label>
    <br/>
    <input id="insert-type-editor3" type="radio" name="insert-type" value="entirerow"/>
    <label for="insert-type-editor3" data-bind="text: res.insertCellsDialog.entirerow">Entire row</label>
    <br/>
    <input id="insert-type-editor4" type="radio" name="insert-type" value="entirecolumn"/>
    <label for="insert-type-editor4" data-bind="text: res.insertCellsDialog.entirecolumn">Entire column</label>
</div>
    <div class='delete-cells-dialog'>
    <label data-bind="text: res.deleteCellsDialog.title">Insert</label>
    <span class="horizontalseparator"></span>
    <br/>
    <input id="delete-type-editor1" type="radio" name="delete-type" value="shiftcellsleft"/>
    <label for="delete-type-editor1" data-bind="text: res.deleteCellsDialog.shiftcellsleft">Shift cells left</label>
    <br/>
    <input id="delete-type-editor2" class="default-radio" type="radio" name="delete-type" value="shiftcellsup"/>
    <label for="delete-type-editor2" data-bind="text: res.deleteCellsDialog.shiftcellsup">Shift cells up</label>
    <br/>
    <input id="delete-type-editor3" type="radio" name="delete-type" value="entirerow"/>
    <label for="delete-type-editor3" data-bind="text: res.deleteCellsDialog.entirerow">Entire row</label>
    <br/>
    <input id="delete-type-editor4" type="radio" name="delete-type" value="entirecolumn"/>
    <label for="delete-type-editor4" data-bind="text: res.deleteCellsDialog.entirecolumn">Entire column</label>
</div>
    <div class='group-dialog'>
    <label data-bind="text: res.groupDialog.title">Group</label>
    <span class="horizontalseparator"></span>
    <br/>
    <input id="group-type-editor1" class="default-radio" type="radio" name="group-type" value="rows"/>
    <label for="group-type-editor1" data-bind="text: res.groupDialog.rows">Rows</label>
    <br/>
    <input id="group-type-editor2" type="radio" name="group-type" value="columns"/>
    <label for="group-type-editor2" data-bind="text: res.groupDialog.columns">Columns</label>
</div>
    <div class='ungroup-dialog'>
    <label data-bind="text: res.ungroupDialog.title">UnGroup</label>
    <span class="horizontalseparator"></span>
    <br/>
    <input id="ungroup-type-editor1" class="default-radio" type="radio" name="group-type" value="rows"/>
    <label for="ungroup-type-editor1" data-bind="text: res.groupDialog.rows">Rows</label>
    <br/>
    <input id="ungroup-type-editor2" type="radio" name="group-type" value="columns"/>
    <label for="ungroup-type-editor2" data-bind="text: res.groupDialog.columns">Columns</label>
</div>
    <div class='find-dialog'>
        <label for="find-what-editor1" data-bind="text: res.findDialog.findwhat">Find what:</label>
        <input class="findwhat-input" id="find-what-editor1" type="text" name="find-what"/>
        <br/>
        <table>
            <tr class="row1">
                <td class="column1 column-hidden">
                    <label for="find-what-editor" data-bind="text: res.findDialog.within">Within:</label>
                </td>
                <td class="column2 column-hidden">
                    <select class="select" name="within">
                        <option selected="selected">Worksheet</option>
                        <option>Workbook</option>
                    </select>
                </td>
                <td class="column3 column-hidden">
                    <input type="checkbox" id="match-case-editor" class="matchcase-editor"/>
                </td>
                <td class="column4 column-hidden">
                    <label for="match-case-editor" data-bind="text: res.findDialog.matchcase">Match case</label>
                </td>
                <td rowspan="3" class="column5">
                    <button name="option" role="button" aria-disabled="false" type="button">
                        <span></span>
                    </button>
                </td>
            </tr>
            <tr class="row2 row-hidden">
                <td class="column1 column-hidden">
                    <label for="search-editor" data-bind="text: res.findDialog.search">Search:</label>
                </td>
                <td class="column2 column-hidden">
                    <select class="select" name="search">
                        <option selected="selected">By Rows</option>
                        <option>By Columns</option>
                    </select>
                </td>
                <td class="column3 column-hidden">
                    <input type="checkbox" id="match-exactly-editor" class="matchexactly-editor"/>
                </td>
                <td class="column4 column-hidden">
                    <label for="match-exactly-editor" data-bind="text: res.findDialog.matchexactly">Match exactly</label>
                </td>
            </tr>
            <tr class="row3 row-hidden">
                <td class="column1 column-hidden">
                    <label for="look-in-editor" data-bind="text: res.findDialog.lookin">Look in:</label>
                </td>
                <td class="column2 column-hidden">
                    <select class="select" name="lookin">
                        <option selected="selected">Values</option>
                        <option>Formulas</option>
                    </select>
                </td>
                <td class="column3 column-hidden">
                    <input type="checkbox" id="use-wildcards-editor" class="usewildcards-editor"/>
                </td>
                <td class="column4 column-hidden">
                    <label for="use-wildcards-editor" data-bind="text: res.findDialog.usewildcards">Use wildcards</label>
                </td>
            </tr>
        </table>
        <div align="right" class="buttons">
            <button class="findall-button" role="button" aria-disabled="false" type="button">
                    <span data-bind="text: res.findDialog.findall">Find All
                    </span>
            </button>
            <button class="findnext-button" role="button" aria-disabled="false" type="button">
                    <span data-bind="text: res.findDialog.findnext">Find Next
                    </span>
            </button>
            <button class="close-button" role="button" aria-disabled="false" type="button">
                    <span data-bind="text: res.close">Close
                    </span>
            </button>
        </div>
        <div class="findall-grid-container">
            <table class="findall-grid"></table>
            <label class="findall-description-label"></label>
        </div>
    </div>
    <div class="goto-dialog">
        <label for="goto-list" data-bind="text: res.gotoDialog.goto">Go to:</label>
        <br/>
        <select class="goto-list" id="goto-list" size="15"></select>
        <br/>
        <label for="goto-value" data-bind="text: res.gotoDialog.reference">Reference:</label>
        <br/>
        <input type="text" id="goto-value" class="goto-value-input"/>
    </div>
    <div class="name-manager-dialog">
        <button class="new-button" role="button" aria-disabled="false" type="button">
                <span data-bind="text: res.nameManagerDialog.newName">New...
                </span>
        </button>
        <button class="edit-button" role="button" aria-disabled="false" type="button">
                <span data-bind="text: res.nameManagerDialog.edit">Edit...
                </span>
        </button>
        <button class="delete-button" role="button" aria-disabled="false" type="button">
                <span data-bind="text: res.nameManagerDialog.deleteName">Delete
                </span>
        </button>
        <div align="right" class="filter-area">
            <label for="filter-select" data-bind="text: res.nameManagerDialog.filterWith.title">Filter with:</label>
            <select id="filter-select" class="filter-select"></select>
        </div>
        <div class="names-grid-container">
            <table class="names-grid" style="width: 605px; "></table>
        </div>
    </div>
    <div class="new-name-dialog">
        <table>
            <tr class="can-hidden-area">
                <td>
                    <label class="text-label" for="name-input" data-bind="text: res.newNameDialog.name">Name:</label>
                </td>
                <td colspan="2">
                    <input type="text" id="name-input" class="name-input"/>
                </td>
            </tr>
            <tr class="can-hidden-area">
                <td>
                    <label class="text-label" for="scope-select"
                           data-bind="text: res.newNameDialog.scope.title">Scope:</label>
                </td>
                <td>
                    <select id="scope-select" class="scope-select">
                    </select>
                </td>
            </tr>
            <tr>
                <td class="can-hidden-area">
                    <label class="text-label" for="referTo-input" data-bind="text: res.newNameDialog.referTo">Refer
                        to:</label>
                </td>
                <td>
                    <div class="rangeSelectContainer">
                        <input type="text" id="referTo-input" class="referTo"/>
                        <div class="rangeSelectButton">
                            <span></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="can-hidden-area">
                    <label class="text-label" for="comment-input"
                           data-bind="text: res.newNameDialog.comment">Comment:</label>
                </td>
                <td>
                    <textarea id="comment-input" class="comment-input"></textarea>
                </td>
            </tr>
        </table>
    </div>
    <div class="tag-dialog">
        <div>
            <table>
                <tr>
                    <td>
                        <label class="text-label" data-bind="text: res.tagDialog.tag">Tag:</label>
                    </td>
                    <td>
                        <textarea id="tag-input" class="tag-input"> </textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="insert-function-dialog">
        <table>
            <tr>
                <td>
                    <label for="function-category" data-bind="text: res.insertFunctionDialog.functionCategory"></label>
                </td>
                <td>
                    <label for="function-list" data-bind="text: res.insertFunctionDialog.functionList"></label>
                </td>
            </tr>
            <tr>
                <td>
                    <select id="function-category" class="function-category-select" size="9">
                    </select>
                </td>
                <td>
                    <select id="function-list" class="function-list-select" size="9">
                    </select>
                </td>
            </tr>
        </table>
        <label class="function-name"></label>
        <!--<br/>-->
        <label class="function-description"></label>
        <!--<br/>-->
        <label for="formula" data-bind="text: res.insertFunctionDialog.formula"></label>
        <input type="text" id="formula" class="formula"/>
    </div>
    <div class="button-celltype-dialog">
        <fieldset>
            <legend data-bind="text: res.buttonCellTypeDialog.marginGroup"></legend>
            <table>
                <tr>
                    <td class="first-column">
                        <label for="margin-left" data-bind="text: res.buttonCellTypeDialog.left"></label>
                    </td>
                    <td>
                        <input type="text" id="margin-left" class="margin-left"/>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="margin-top" data-bind="text: res.buttonCellTypeDialog.top"></label>
                    </td>
                    <td>
                        <input type="text" id="margin-top" class="margin-top"/>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="margin-right" data-bind="text: res.buttonCellTypeDialog.right"></label>
                    </td>
                    <td>
                        <input type="text" id="margin-right" class="margin-right"/>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="margin-bottom" data-bind="text: res.buttonCellTypeDialog.bottom"></label>
                    </td>
                    <td>
                        <input type="text" id="Text3" class="margin-bottom"/>
                    </td>
                </tr>
            </table>
        </fieldset>

        <fieldset>
            <legend data-bind="text: res.buttonCellTypeDialog.other"></legend>
            <table>
                <tr>
                    <td class="first-column">
                        <label for="button-backcolor" data-bind="text: res.buttonCellTypeDialog.backcolor"></label>
                    </td>
                    <td>
                        <div id="button-backcolor-color-frame" class="color-frame float-left">
                                <span>
                                    <span id="button-backcolor-color-span" class="color-span"></span>
                                </span>
                            <div class="button-backcolor-color-picker">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="button-text" data-bind="text: res.buttonCellTypeDialog.text"></label>
                    </td>
                    <td>
                        <input type="text" id="button-text" class="button-text"/>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <div class="checkbox-celltype-dialog">
        <fieldset>
            <legend data-bind="text: res.checkBoxCellTypeDialog.textGroup"></legend>
            <table>
                <tr>
                    <td class="first-column">
                        <label for="text-true" data-bind="text: res.checkBoxCellTypeDialog.true"></label>
                    </td>
                    <td>
                        <input type="text" id="text-true" class="text-true"/>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="text-indeterminate" data-bind="text: res.checkBoxCellTypeDialog.indeterminate"></label>
                    </td>
                    <td>
                        <input type="text" id="text-indeterminate" class="text-indeterminate"/>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="text-false" data-bind="text: res.checkBoxCellTypeDialog.false"></label>
                    </td>
                    <td>
                        <input type="text" id="text-false" class="text-false"/>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="text-align" data-bind="text: res.checkBoxCellTypeDialog.align"></label>
                    </td>
                    <td>
                        <select id="text-align" class="text-align"/>
                    </td>
                </tr>
            </table>
        </fieldset>
        <fieldset>
            <legend data-bind="text: res.checkBoxCellTypeDialog.other"></legend>
            <label for="caption" data-bind="text: res.checkBoxCellTypeDialog.caption"></label>
            <input type="text" id="caption" class="caption"/>
            <br/>
            <input type="checkbox" id="isThreeState" class="isThreeState" checked="checked">
            <label for="isThreeState" data-bind="text: res.checkBoxCellTypeDialog.isThreeState"></label>
        </fieldset>
    </div>
    <div class="combobox-celltype-dialog">
        <label for="editor-valuetype" class="combox-column1"
               data-bind="text: res.comboBoxCellTypeDialog.editorValueTypes"></label>
        <select id="editor-valuetype" class="editor-valuetype"/>
        <br/>
        <div>
            <label data-bind="text:res.comboBoxCellTypeDialog.itemHeight + ':'" class="combox-column1">Items Height:</label>
            <input placeholder="22" class="combo-item-height"/>
            <input type="checkbox" id="combox-editable"/>
            <label data-bind="text:res.comboBoxCellTypeDialog.editable" for="combox-editable">Editable</label>
        </div>
        <table>
            <tr>
                <td>
                    <fieldset>
                        <legend data-bind="text: res.comboBoxCellTypeDialog.items"></legend>
                        <select class="items" size="10"/>
                    </fieldset>
                </td>
                <td>
                    <fieldset>
                        <legend data-bind="text: res.comboBoxCellTypeDialog.itemProperties"></legend>
                        <table>
                            <tr>
                                <td class="first-column">
                                    <label for="text" data-bind="text: res.comboBoxCellTypeDialog.text"></label>
                                </td>
                                <td>
                                    <input type="text" id="text" class="text"/>
                                </td>
                            </tr>
                            <tr>
                                <td class="first-column">
                                    <label for="value" data-bind="text: res.comboBoxCellTypeDialog.value"></label>
                                </td>
                                <td>
                                    <input type="text" id="value" class="value"/>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>

        <button class="add-button" role="button" aria-disabled="false" type="button">
            <span data-bind="text: res.comboBoxCellTypeDialog.add"></span>
        </button>
        <button class="remove-button" role="button" aria-disabled="false" type="button">
            <span data-bind="text: res.comboBoxCellTypeDialog.remove"></span>
        </button>
    </div>
    <div class="hyperLink-celltype-dialog">
        <fieldset>
            <legend data-bind="text: res.hyperLinkCellTypeDialog.color"></legend>
            <table>
                <tr>
                    <td>
                        <label for="link" data-bind="text: res.hyperLinkCellTypeDialog.link"></label>
                    </td>
                    <td>
                        <div id="link" class="link-color-frame">
                                <span>
                                    <span class="link-color-span"></span>
                                </span>
                            <div class="link-color-picker">
                            </div>

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="visitedlink" data-bind="text: res.hyperLinkCellTypeDialog.visitedlink"></label>
                    </td>
                    <td>
                        <div id="visitedlink" class="visitedlink-color-frame">
                                <span>
                                    <span class="visitedlink-color-span"></span>
                                </span>
                            <div class="visitedlink-color-picker">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </fieldset>
        <fieldset>
            <legend data-bind="text: res.hyperLinkCellTypeDialog.other"></legend>
            <table>
                <tr>
                    <td>
                        <label for="hyperLink-text" data-bind="text: res.hyperLinkCellTypeDialog.text"></label>
                    </td>
                    <td>
                        <input type="text" id="hyperLink-text" class="text"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="linktooltip" data-bind="text: res.hyperLinkCellTypeDialog.linktooltip"></label>
                    </td>
                    <td>
                        <input type="text" id="linktooltip" class="linktooltip"/>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <div class='header-cells-dialog'>
        <div class="button-set">
            <div class="gcui-ribbon-list">
                <button title="Merge" class="merge" name="merge" data-bind="attr: { title: res.headerCellsDialog.merge }">
                </button>
                <button title="Unmerge" class="unmerge" name="unmerge"
                        data-bind="attr: { title: res.headerCellsDialog.unmerge }"></button>
            </div>
            <span class="gcui-ribbon-separator"></span>
            <div class="gcui-ribbon-list">
                <button title="Insert Rows" class="insertrows" name="insertrows"
                        data-bind="attr: { title: res.headerCellsDialog.insertRows }"></button>
                <button title="Add Rows" class="addrows" name="addrows"
                        data-bind="attr: { title: res.headerCellsDialog.addRows }"></button>
                <button title="Delete Rows" class="deleterows" name="deleterows"
                        data-bind="attr: { title: res.headerCellsDialog.deleteRows }"></button>
            </div>
            <span class="gcui-ribbon-separator"></span>
            <div class="gcui-ribbon-list">
                <button title="Insert Columns" class="insertcolumns" name="insertcolumns"
                        data-bind="attr: { title: res.headerCellsDialog.insertColumns }"></button>
                <button title="Add Columns" class="addcolumns" name="addcolumns"
                        data-bind="attr: { title: res.headerCellsDialog.addColumns }"></button>
                <button title="Delete Columns" class="deletecolumns" name="deletecolumns"
                        data-bind="attr: { title: res.headerCellsDialog.deleteColumns }"></button>
            </div>
            <span class="gcui-ribbon-separator"></span>
            <div class="gcui-ribbon-list">
                <button title="Clear" class="clear" name="clear"
                        data-bind="attr: { title: res.headerCellsDialog.clear }"></button>
            </div>
        </div>
        <div class="header-tab">
            <ul>
                <li><a href="#rowHeaderTab" data-bind="text: res.headerCellsDialog.rowHeader"></a></li>
                <li><a href="#columnHeaderTab" data-bind="text: res.headerCellsDialog.columnHeader"></a></li>
            </ul>
            <div id="rowHeaderTab">
            </div>
            <div id="columnHeaderTab">
            </div>
        </div>
        <div class="property-grid-container">
            <table class="property-grid">
                <tr>
                    <td class="first-column">
                        <label for="backColor" data-bind="text: res.headerCellsDialog.backColor"></label>
                    </td>
                    <td>
                        <div id="backColor" class="backColor-colorframe">
                                <span>
                                    <span class="backColor-colorspan"></span>
                                </span>
                            <div class="backColor-colorpicker">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="borderBottom" data-bind="text: res.headerCellsDialog.borderBottom"></label>
                    </td>
                    <td>
                        <div id="borderBottom" class="borderBottom-borderframe">
                                <span>
                                    <Input type="text" readonly="readonly" class="borderBottom-borderspan"></Input>
                                </span>
                            <div class="borderBottom-borderpicker">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="borderLeft" data-bind="text: res.headerCellsDialog.borderLeft"></label>
                    </td>
                    <td>
                        <div id="borderLeft" class="borderLeft-borderframe">
                                <span>
                                    <Input type="text" readonly="readonly" class="borderLeft-borderspan"></Input>
                                </span>
                            <div class="borderLeft-borderpicker">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="borderRight" data-bind="text: res.headerCellsDialog.borderRight"></label>
                    </td>
                    <td>
                        <div id="borderRight" class="borderRight-borderframe">
                                <span>
                                    <Input type="text" readonly="readonly" class="borderRight-borderspan"></Input>
                                </span>
                            <div class="borderRight-borderpicker">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="borderTop" data-bind="text: res.headerCellsDialog.borderTop"></label>
                    </td>
                    <td>
                        <div id="borderTop" class="borderTop-borderframe">
                                <span>
                                    <Input type="text" readonly="readonly" class="borderTop-borderspan"></Input>
                                </span>
                            <div class="borderTop-borderpicker">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                    <label for="diagonalUp" data-bind="text: res.headerCellsDialog.diagonalUp"></label>
                </td>
                <td>
                    <div id="diagonalUp" class="diagonalUp-borderframe">
                                <span>
                                    <Input type="text" readonly="readonly" class="diagonalUp-borderspan"></Input>
                                </span>
                        <div class="diagonalUp-borderpicker">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="first-column">
                    <label for="diagonalDown" data-bind="text: res.headerCellsDialog.diagonalDown"></label>
                </td>
                <td>
                    <div id="diagonalDown" class="diagonalDown-borderframe">
                                <span>
                                    <Input type="text" readonly="readonly" class="diagonalDown-borderspan"></Input>
                                </span>
                        <div class="diagonalDown-borderpicker">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="first-column">
                        <label for="font" data-bind="text: res.headerCellsDialog.font"></label>
                    </td>
                    <td>
                        <input type="text" id="font" class="font"/>
                        <button class="font-popup" role="button" aria-disabled="false" type="button">
                            <span data-bind="text: res.headerCellsDialog.popUp"></span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="foreColor" data-bind="text: res.headerCellsDialog.foreColor"></label>
                    </td>
                    <td>
                        <div id="foreColor" class="foreColor-colorframe">
                                <span>
                                    <span class="foreColor-colorspan"></span>
                                </span>
                            <div class="foreColor-colorpicker">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="formatter" data-bind="text: res.headerCellsDialog.formatter"></label>
                    </td>
                    <td>
                        <input type="text" id="formatter" class="formatter">
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="hAlign" data-bind="text: res.headerCellsDialog.hAlign"></label>
                    </td>
                    <td>
                        <select class="hAlign" id="hAlign"></select>
                    </td>
                </tr>
                <tr class="height-row">
                    <td class="first-column">
                        <label for="height" data-bind="text: res.headerCellsDialog.height"></label>
                    </td>
                    <td>
                        <input type="text" id="height" class="height">
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="locked" data-bind="text: res.headerCellsDialog.locked"></label>
                    </td>
                    <td>
                        <input type="checkbox" id="locked" class="locked">
                </td>
            </tr>
            <tr>
                <td class="first-column">
                    <label for="verticalText" data-bind="text: res.headerCellsDialog.verticalText"></label>
                </td>
                <td>
                    <input type="checkbox" id="verticalText" class="verticalText">
                    </td>
                </tr>
                <tr class="resizable-row">
                    <td class="first-column">
                        <label for="resizable" data-bind="text: res.headerCellsDialog.resizable"></label>
                    </td>
                    <td>
                        <input type="checkbox" id="resizable" class="resizable">
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="shrinkToFit" data-bind="text: res.headerCellsDialog.shrinkToFit"></label>
                    </td>
                    <td>
                        <input type="checkbox" id="shrinkToFit" class="shrinkToFit">
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="textIndent" data-bind="text: res.headerCellsDialog.textIndent"></label>
                    </td>
                    <td>
                        <input type="text" id="textIndent" class="textIndent">
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="vAlign" data-bind="text: res.headerCellsDialog.vAlign"></label>
                    </td>
                    <td>
                        <select class="vAlign" id="vAlign"></select>
                    </td>
                </tr>
                <tr class="value-row">
                    <td class="first-column">
                        <label for="value" data-bind="text: res.headerCellsDialog.value"></label>
                    </td>
                    <td>
                        <input type="text" id="Text1" class="value">
                    </td>
                </tr>
                <tr class="visible-row">
                    <td class="first-column">
                        <label for="visible" data-bind="text: res.headerCellsDialog.visible"></label>
                    </td>
                    <td>
                        <input type="checkbox" id="visible" class="visible">
                    </td>
                </tr>
                <tr class="width-row">
                    <td class="first-column">
                        <label for="width" data-bind="text: res.headerCellsDialog.width"></label>
                    </td>
                    <td>
                        <input type="text" id="width" class="width">
                    </td>
                </tr>
                <tr>
                    <td class="first-column">
                        <label for="wordWrap" data-bind="text: res.headerCellsDialog.wordWrap"></label>
                    </td>
                    <td>
                        <input type="checkbox" id="wordWrap" class="wordWrap">
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="font-dialog">
        <div class="font-picker"></div>
    </div>
    <div class="cellValueRuleFormat-dialog">
        <label class="description"></label>
        <br/>
        <div class="rangeSelectContainer textBox-cell1-container">
            <input type="text" class="textBox-cell1"/>
            <div class="rangeSelectButton" data-name="textBox-cell1">
                <span></span>
            </div>
        </div>
        <label class="label-and" data-bind="text: res.conditionalFormatting.common.and"></label>
        <div class="rangeSelectContainer textBox-cell2-container">
            <input type="text" class="textBox-cell2"/>
            <div class="rangeSelectButton" data-name="textBox-cell2">
                <span></span>
            </div>
        </div>
        <label data-bind="text: res.conditionalFormatting.common.with"></label>
        <select class="withStyle">
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill_DarkRedText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.yellowFill_DrakYellowText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.greenFill_DarkGreenText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redBorder"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.customFormat"></option>
        </select>
    </div>
    <div class="textRuleFormat-dialog">
        <label class="description" data-bind="text: res.conditionalFormatting.textThatCotains.description"></label>
        <br/>
        <div class="rangeSelectContainer">
            <input type="text" class="textBox-cell1"/>
            <div class="rangeSelectButton" data-name="textBox-cell1">
                <span></span>
            </div>
        </div>
        <label data-bind="text: res.conditionalFormatting.common.with"></label>
        <select class="withStyle">
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill_DarkRedText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.yellowFill_DrakYellowText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.greenFill_DarkGreenText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redBorder"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.customFormat"></option>
        </select>
    </div>
    <div class="dateOccurringFormat-dialog">
        <label class="description" data-bind="text: res.conditionalFormatting.dateOccurringFormat.description"></label>
        <br/>
        <select class="date">
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.yesterday"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.today"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.tomorrow"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.last7days"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.lastweek"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.thisweek"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.nextweek"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.lastmonth"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.thismonth"></option>
            <option data-bind="text: res.conditionalFormatting.dateOccurringFormat.date.nextmonth"></option>
        </select>
        <label data-bind="text: res.conditionalFormatting.common.with"></label>
        <select class="withStyle">
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill_DarkRedText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.yellowFill_DrakYellowText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.greenFill_DarkGreenText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redBorder"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.customFormat"></option>
        </select>
    </div>
    <div class="duplicateValuesFormat-dialog">
        <label class="description" data-bind="text: res.conditionalFormatting.duplicateValuesFormat.description"></label>
        <br/>
        <select class="type">
            <option data-bind="text: res.conditionalFormatting.duplicateValuesFormat.type.duplicate"></option>
            <option data-bind="text: res.conditionalFormatting.duplicateValuesFormat.type.unique"></option>
        </select>
        <label data-bind="text: res.conditionalFormatting.duplicateValuesFormat.valueswith"></label>
        <select class="withStyle">
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill_DarkRedText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.yellowFill_DrakYellowText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.greenFill_DarkGreenText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redBorder"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.customFormat"></option>
        </select>
    </div>
    <div class="top10RuleFormat-dialog">
        <label class="description"></label>
        <br/>
        <input value="10" class="itemCounts"/>
        <label data-bind="text: res.conditionalFormatting.common.with"></label>
        <select class="withStyle">
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill_DarkRedText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.yellowFill_DrakYellowText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.greenFill_DarkGreenText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redBorder"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.customFormat"></option>
        </select>
    </div>
    <div class="averageRuleFormat-dialog">
        <label class="description"></label>
        <br/>
        <label class="with" data-bind="text: res.conditionalFormatting.common.selectedRangeWith"></label>
        <select class="withStyle">
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill_DarkRedText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.yellowFill_DrakYellowText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.greenFill_DarkGreenText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.lightRedFill"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redText"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.redBorder"></option>
            <option data-bind="text: res.conditionalFormatting.withStyle.customFormat"></option>
        </select>
    </div>
    <div class="newFormattingRule-dialog">
        <label data-bind="text: res.conditionalFormatting.newFormattingRule.description1"></label>
        <br/>
        <select size="6" class="ruleType">
            <option data-bind="text: res.conditionalFormatting.newFormattingRule.ruleType.formatOnValue"></option>
            <option data-bind="text: res.conditionalFormatting.newFormattingRule.ruleType.formatContain"></option>
            <option data-bind="text: res.conditionalFormatting.newFormattingRule.ruleType.formatRankedValue"></option>
            <option data-bind="text: res.conditionalFormatting.newFormattingRule.ruleType.formatAbove"></option>
            <option data-bind="text: res.conditionalFormatting.newFormattingRule.ruleType.formatUnique"></option>
            <option data-bind="text: res.conditionalFormatting.newFormattingRule.ruleType.useFormula"></option>
        </select>
        <br/>
        <label class="borderContainer-description"
               data-bind="text: res.conditionalFormatting.newFormattingRule.description2"></label>
        <div class="borderContainer">
            <div class="formatOnValue">
                <div class="format-on-value-description">
                    <label class="description"
                           data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.description"></label>
                </div>
                <div class="format-style-container">
                    <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.formatStyle"></label>
                    <select class="formatStyle">
                        <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.formatStyleSelector.color2"></option>
                        <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.formatStyleSelector.color3"></option>
                        <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.formatStyleSelector.dataBar"></option>
                        <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.formatStyleSelector.iconSets"></option>
                    </select>
                </div>
                <div class="color23">
                    <table cellspacing="3px">
                        <tr>
                            <td></td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.min"></label>
                            </td>
                            <td>
                                <label class="color3"
                                       data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color3.mid"></label>
                            </td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.max"></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.type"></label>
                            </td>
                            <td>
                                <select class="minSelector">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.minSelector.lowest"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                </select>
                            </td>
                            <td>
                                <select class="midSelector color3">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                </select>
                            </td>
                            <td>
                                <select class="maxSelector">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.maxSelector.highest"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.value"></label>
                            </td>
                            <td>
                                <div class="rangeSelectContainer">
                                    <input type="text" class="minValue format-rule-value"/>
                                    <div class="rangeSelectButton minValueButton" data-name="minValue">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="rangeSelectContainer color3">
                                    <input type="text" class="midValue color3 format-rule-value"/>
                                    <div class="rangeSelectButton midValueButton" data-name="midValue">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="rangeSelectContainer">
                                    <input type="text" class="maxValue format-rule-value"/>
                                    <div class="rangeSelectButton maxValueButton" data-name="maxValue">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.color"></label>
                            </td>
                            <td>
                                <div class="min-color-frame format-rule-color-frame">
                                        <span>
                                            <span class="min-color-span"></span>
                                        </span>
                                    <div class="min-color-picker">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="mid-color-frame color3 format-rule-color-frame">
                                        <span>
                                            <span class="mid-color-span"></span>
                                        </span>
                                    <div class="mid-color-picker">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="max-color-frame format-rule-color-frame">
                                        <span>
                                            <span class="max-color-span"></span>
                                        </span>
                                    <div class="max-color-picker">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="preview-description"
                                       data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.preview"></label>
                            </td>
                            <td colspan="3" class="preview-td">
                                <div class="previewOfColor23"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="dataBar">
                    <input type="checkbox" class="showBarOnly"/>
                    <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.showBarOnly"></label>
                    <table cellspacing="3px">
                        <tr>
                            <td></td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.min"></label>
                            </td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.max"></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.type"></label>
                            </td>
                            <td>
                                <select class="minSelector2">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.minSelector.lowest"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.auto"></option>
                                </select>
                            </td>
                            <td>
                                <select class="maxSelector2">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.maxSelector.highest"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.auto"></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.color2.value"></label>
                            </td>
                            <td>
                                <div class="rangeSelectContainer">
                                    <input type="text" class="minValue2 format-rule-value2"/>
                                    <div class="rangeSelectButton minValue2Button" data-name="minValue2">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="rangeSelectContainer">
                                    <input type="text" class="maxValue2 format-rule-value2"/>
                                    <div class="rangeSelectButton maxValue2Button" data-name="maxValue2">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="databar-description2-container">
                        <label class="description"
                               data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.description2"></label>
                    </div>
                    <table cellspacing="3px">
                        <tr>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.fill"></label>
                            </td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.color"></label>
                            </td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.border"></label>
                            </td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.color"></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select class="fillSelector">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.fillSelector.solidFill"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.fillSelector.gradientFill"></option>
                                </select>
                            </td>
                            <td>
                                <div class="barcolor1-frame">
                                        <span>
                                            <span class="barcolor1-span"></span>
                                        </span>
                                    <div class="barcolor1-picker">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="borderSelector">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.borderSelector.noBorder"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.borderSelector.solidBorder"></option>
                                </select>
                            </td>
                            <td>
                                <div class="barcolor2-frame">
                                        <span>
                                            <span class="barcolor2-span"></span>
                                        </span>
                                    <div class="barcolor2-picker">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <button class="negativeBtn" role="button" aria-disabled="false" type="button">
                        <span data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeBtn"></span>
                    </button>
                    <div align="right" class="inlineBlock">
                        <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.barDirection"></label>
                        <select class="barDirectionSelector">
                            <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.barDirectionSelector.l2r"></option>
                            <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.barDirectionSelector.r2l"></option>
                        </select>
                    </div>
                    <div align="right">
                        <label class="boldFont previewText"
                               data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.preview"></label>
                        <div class="previewContainer-dataBar">
                            <div class="previewOfDatabar">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="iconSets">
                    <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.iconStyle"></label>
                    <!--<select class=""></select>-->
                    <div class="iconStyle-frame iconStyle">
                            <span>
                                <span class="icon-button-container">
                                    <span class="iconStyleIcon iconSetsIcons horizontal-icon-set-item"></span>
                                    <span class="iconStyleIcon iconSetsIcons horizontal-icon-set-item"></span>
                                    <span class="iconStyleIcon iconSetsIcons horizontal-icon-set-item"></span>
                                    <span class="iconStyleIcon iconSetsIcons horizontal-icon-set-item"></span>
                                    <span class="iconStyleIcon iconSetsIcons horizontal-icon-set-item"></span>
                                </span>
                            </span>
                        <div id="icon-sets-popup-dialog">
                            <ul id="icon-sets-popup-menu-dialog"
                                class="ui-helper-reset ui-helper-clearfix ui-corner-all gcui-ribbon-dropdown">
                                <li class="ui-corner-all submenu-title">
                                    <span data-bind="text: res.conditionalFormat.directional">Directional</span>
                                </li>
                                <li class="ui-corner-all">
                                    <div class="horizontal-2-small-iconset-button-container">
                                        <button name="3-arrows-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item up-arrow-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-arrow-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item down-arrow-red"></span>
                                                </span>
                                        </button>
                                        <button name="3-arrows-gray-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item up-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item down-arrow-gray"></span>
                                                </span>
                                        </button>
                                        <button name="3-triangles-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item up-triangle-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item minus-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item down-triangle-red"></span>
                                                </span>
                                        </button>
                                        <button name="4-arrows-gray-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item up-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-up-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-down-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item down-arrow-gray"></span>
                                                </span>
                                        </button>

                                        <button name="4-arrows-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item up-arrow-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-up-arrow-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-down-arrow-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item down-arrow-red"></span>
                                                </span>
                                        </button>
                                        <button name="5-arrows-gray-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item up-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-up-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-down-arrow-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item down-arrow-gray"></span>
                                                </span>
                                        </button>
                                        <button name="5-arrows-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item up-arrow-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-up-arrow-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-arrow-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item right-down-arrow-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item down-arrow-red"></span>
                                                </span>
                                        </button>
                                    </div>
                                </li>
                                <li class="ui-corner-all submenu-title">
                                    <span data-bind="text: res.conditionalFormat.shapes">Shapes</span>
                                </li>
                                <li class="ui-corner-all">
                                    <div class="horizontal-2-small-iconset-button-container">
                                        <button name="3-traffic-lights-unrimmed-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-red"></span>
                                                </span>
                                        </button>
                                        <button name="3-traffic-lights-rimmed-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-rimmed-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-rimmed-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-rimmed-red"></span>
                                                </span>
                                        </button>
                                        <button name="3-signs-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item up-triangle-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item down-rhombus-red"></span>
                                                </span>
                                        </button>
                                        <button name="4-traffic-lights-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-red"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item traffic-light-black"></span>
                                                </span>
                                        </button>
                                        <button name="red-to-black-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item ball-red"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item ball-pink"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item ball-gray"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item ball-black"></span>
                                                </span>
                                        </button>
                                    </div>
                                </li>
                                <li class="ui-corner-all submenu-title">
                                    <span data-bind="text: res.conditionalFormat.indicators">Indicators</span>
                                </li>
                                <li class="ui-corner-all">
                                    <div class="horizontal-2-small-iconset-button-container">
                                        <button name="3-symbols-circled-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item check-circled-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item notice-circled-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item close-circled-red"></span>
                                                </span>
                                        </button>
                                        <button name="3-symbols-uncircled-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item check-uncircled-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item notice-uncircled-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item close-uncircled-red"></span>
                                                </span>
                                        </button>
                                        <button name="3-flags-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item flag-green"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item flag-yellow"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item flag-red"></span>
                                                </span>
                                        </button>
                                    </div>
                                </li>
                                <li class="ui-corner-all submenu-title">
                                    <span data-bind="text: res.conditionalFormat.ratings">Ratings</span>
                                </li>
                                <li class="ui-corner-all">
                                    <div class="horizontal-2-small-iconset-button-container">
                                        <button name="3-stars-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item star-solid"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item star-half"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item star-hollow"></span>
                                                </span>
                                        </button>
                                        <button name="4-ratings-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-3"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-2"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-1"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-0"></span>
                                                </span>
                                        </button>
                                        <button name="5-quarters-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item quarters-4"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item quarters-3"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item quarters-2"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item quarters-1"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item quarters-0"></span>
                                                </span>
                                        </button>
                                        <button name="5-ratings-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-4"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-3"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-2"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-1"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item rating-0"></span>
                                                </span>
                                        </button>
                                        <button name="5-boxes-icon-set"
                                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                                <span class="icon-button-container">
                                                    <span class="iconSetsIcons horizontal-icon-set-item box-4"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item box-3"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item box-2"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item box-1"></span>
                                                    <span class="iconSetsIcons horizontal-icon-set-item box-0"></span>
                                                </span>
                                        </button>
                                    </div>
                                </li>
                                <li class="ui-corner-all">
                                    <div class="gcui-ribbon-listseparator"></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <input type="checkbox" class="showIconOnly"/>
                    <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.showIconOnly"></label>
                    <input type="checkbox" class="reverseIconOrder"/>
                    <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.reverseIconOrder"></label>
                    <div class="iconset-display-description">
                        <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.display"></label>
                    </div>
                    <table cellspacing="3px">
                        <tr>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.icon"
                                       class="iconset-title-label"></label>
                            </td>
                            <td></td>
                            <td></td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.value"
                                       class="iconset-title-label"></label>
                            </td>
                            <td>
                                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.type"
                                       class="iconset-title-label"></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <div class="icon1-frame icon-frame">
                                <span class="iconWrapper">
                                <span class="icon1 iconSetsIcons"></span>
                                    <span class="iconText"></span>
                                </span>
                                <div class="icons-popup-dialog">
                                    <span class="icon-wrapper">
                                        <span class="iconSetsIcons no-cell-icon" name="noIcons,0" data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.noCellIcon">No Cell Icon</span>
                                    </span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-green" name="3-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-yellow" name="3-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-red" name="3-arrows-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-gray" name="3-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-gray" name="3-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-gray" name="3-arrows-gray-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-green" name="3-triangles-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons minus-yellow" name="3-triangles-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-triangle-red" name="3-triangles-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-gray" name="4-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-gray" name="4-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-yellow" name="4-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-yellow" name="4-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-green" name="3-traffic-lights-unrimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-yellow" name="3-traffic-lights-unrimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-red" name="3-traffic-lights-unrimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-green" name="3-traffic-lights-rimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-yellow" name="3-traffic-lights-rimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-red" name="3-traffic-lights-rimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-yellow" name="3-signs-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-rhombus-red" name="3-signs-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-black" name="4-traffic-lights-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-red" name="red-to-black-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-pink" name="red-to-black-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-gray" name="red-to-black-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-black" name="red-to-black-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-circled-green" name="3-symbols-circled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-circled-yellow" name="3-symbols-circled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-circled-red" name="3-symbols-circled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-uncircled-green" name="3-symbols-uncircled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-uncircled-yellow" name="3-symbols-uncircled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-uncircled-red" name="3-symbols-uncircled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-green" name="3-flags-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-yellow" name="3-flags-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-red" name="3-flags-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-solid" name="3-stars-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-half" name="3-stars-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-hollow" name="3-stars-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-4" name="5-quarters-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-3" name="5-quarters-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-2" name="5-quarters-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-1" name="5-quarters-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-0" name="5-quarters-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-4" name="5-ratings-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-3" name="5-ratings-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-2" name="5-ratings-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-1" name="5-ratings-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-0" name="5-ratings-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-4" name="5-boxes-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-3" name="5-boxes-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-2" name="5-boxes-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-1" name="5-boxes-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-0" name="5-boxes-icon-set,0"></span></span>
                                </div>
                            </div>
                            </td>
                            <td style="width: 140px">
                                <label class="des1_iconSet"
                                       data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.description1"></label>
                            </td>
                            <td>
                                <select class="operator1">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.operator.largeOrEqu"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.operator.large"></option>
                                </select>
                            </td>
                            <td>
                                <div class="rangeSelectContainer">
                                    <input type="text" class="value1"/>
                                    <div class="rangeSelectButton value1Button" data-name="value1">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="value1Selector">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <div class="icon2-frame icon-frame">
                                <span class="iconWrapper">
                                <span class="icon2 iconSetsIcons"></span>
                                    <span class="iconText"></span>
                                </span>
                                <div class="icons-popup-dialog">
                                    <span class="icon-wrapper"><span class="iconSetsIcons no-cell-icon"
                                                                     name="noIcons,0" data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.noCellIcon">No Cell Icon</span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-green" name="3-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-yellow" name="3-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-red" name="3-arrows-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-gray" name="3-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-gray" name="3-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-gray" name="3-arrows-gray-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-green" name="3-triangles-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons minus-yellow" name="3-triangles-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-triangle-red" name="3-triangles-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-gray" name="4-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-gray" name="4-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-yellow" name="4-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-yellow" name="4-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-green" name="3-traffic-lights-unrimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-yellow" name="3-traffic-lights-unrimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-red" name="3-traffic-lights-unrimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-green" name="3-traffic-lights-rimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-yellow" name="3-traffic-lights-rimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-red" name="3-traffic-lights-rimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-yellow" name="3-signs-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-rhombus-red" name="3-signs-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-black" name="4-traffic-lights-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-red" name="red-to-black-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-pink" name="red-to-black-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-gray" name="red-to-black-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-black" name="red-to-black-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-circled-green" name="3-symbols-circled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-circled-yellow" name="3-symbols-circled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-circled-red" name="3-symbols-circled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-uncircled-green" name="3-symbols-uncircled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-uncircled-yellow" name="3-symbols-uncircled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-uncircled-red" name="3-symbols-uncircled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-green" name="3-flags-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-yellow" name="3-flags-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-red" name="3-flags-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-solid" name="3-stars-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-half" name="3-stars-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-hollow" name="3-stars-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-4" name="5-quarters-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-3" name="5-quarters-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-2" name="5-quarters-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-1" name="5-quarters-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-0" name="5-quarters-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-4" name="5-ratings-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-3" name="5-ratings-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-2" name="5-ratings-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-1" name="5-ratings-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-0" name="5-ratings-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-4" name="5-boxes-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-3" name="5-boxes-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-2" name="5-boxes-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-1" name="5-boxes-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-0" name="5-boxes-icon-set,0"></span></span>
                                </div>
                            </div>
                            </td>
                            <td>
                                <label class="des2_iconSet"></label>
                            </td>
                            <td>
                                <select class="operator2">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.operator.largeOrEqu"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.operator.large"></option>
                                </select>
                            </td>
                            <td>
                                <div class="rangeSelectContainer">
                                    <input type="text" class="value2"/>
                                    <div class="rangeSelectButton value2Button" data-name="value2">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="value2Selector">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="iconSet-dataRow3">
                            <td>
                            <div class="icon3-frame icon-frame">
                                <span class="iconWrapper">
                                <span class="icon3 iconSetsIcons"></span>
                                    <span class="iconText"></span>
                                </span>
                                <div class="icons-popup-dialog">
                                    <span class="icon-wrapper"><span class="iconSetsIcons no-cell-icon"
                                                                     name="noIcons,0" data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.noCellIcon">No Cell Icon</span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-green" name="3-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-yellow" name="3-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-red" name="3-arrows-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-gray" name="3-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-gray" name="3-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-gray" name="3-arrows-gray-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-green" name="3-triangles-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons minus-yellow" name="3-triangles-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-triangle-red" name="3-triangles-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-gray" name="4-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-gray" name="4-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-yellow" name="4-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-yellow" name="4-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-green" name="3-traffic-lights-unrimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-yellow" name="3-traffic-lights-unrimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-red" name="3-traffic-lights-unrimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-green" name="3-traffic-lights-rimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-yellow" name="3-traffic-lights-rimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-red" name="3-traffic-lights-rimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-yellow" name="3-signs-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-rhombus-red" name="3-signs-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-black" name="4-traffic-lights-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-red" name="red-to-black-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-pink" name="red-to-black-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-gray" name="red-to-black-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-black" name="red-to-black-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-circled-green" name="3-symbols-circled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-circled-yellow" name="3-symbols-circled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-circled-red" name="3-symbols-circled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-uncircled-green" name="3-symbols-uncircled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-uncircled-yellow" name="3-symbols-uncircled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-uncircled-red" name="3-symbols-uncircled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-green" name="3-flags-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-yellow" name="3-flags-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-red" name="3-flags-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-solid" name="3-stars-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-half" name="3-stars-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-hollow" name="3-stars-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-4" name="5-quarters-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-3" name="5-quarters-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-2" name="5-quarters-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-1" name="5-quarters-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-0" name="5-quarters-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-4" name="5-ratings-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-3" name="5-ratings-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-2" name="5-ratings-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-1" name="5-ratings-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-0" name="5-ratings-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-4" name="5-boxes-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-3" name="5-boxes-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-2" name="5-boxes-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-1" name="5-boxes-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-0" name="5-boxes-icon-set,0"></span></span>
                                </div>
                            </div>
                            </td>
                            <td>
                                <label class="des3_iconSet"></label>
                            </td>
                            <td class="row3-mayHiddenParts">
                                <select class="operator3">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.operator.largeOrEqu"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.operator.large"></option>
                                </select>
                            </td>
                            <td class="row3-mayHiddenParts">
                                <div class="rangeSelectContainer">
                                    <input type="text" class="value3"/>
                                    <div class="rangeSelectButton value3Button" data-name="value3">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                            <td class="row3-mayHiddenParts">
                                <select class="value3Selector">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="iconSet-dataRow4">
                            <td>
                            <div class="icon4-frame icon-frame">
                                <span class="iconWrapper">
                                <span class="icon4 iconSetsIcons"></span>
                                    <span class="iconText"></span>
                                </span>
                                <div class="icons-popup-dialog">
                                    <span class="icon-wrapper"><span class="iconSetsIcons no-cell-icon"
                                                                     name="noIcons,0"  data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.noCellIcon">No Cell Icon</span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-green" name="3-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-yellow" name="3-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-red" name="3-arrows-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-gray" name="3-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-gray" name="3-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-gray" name="3-arrows-gray-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-green" name="3-triangles-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons minus-yellow" name="3-triangles-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-triangle-red" name="3-triangles-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-gray" name="4-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-gray" name="4-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-yellow" name="4-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-yellow" name="4-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-green" name="3-traffic-lights-unrimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-yellow" name="3-traffic-lights-unrimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-red" name="3-traffic-lights-unrimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-green" name="3-traffic-lights-rimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-yellow" name="3-traffic-lights-rimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-red" name="3-traffic-lights-rimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-yellow" name="3-signs-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-rhombus-red" name="3-signs-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-black" name="4-traffic-lights-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-red" name="red-to-black-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-pink" name="red-to-black-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-gray" name="red-to-black-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-black" name="red-to-black-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-circled-green" name="3-symbols-circled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-circled-yellow" name="3-symbols-circled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-circled-red" name="3-symbols-circled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-uncircled-green" name="3-symbols-uncircled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-uncircled-yellow" name="3-symbols-uncircled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-uncircled-red" name="3-symbols-uncircled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-green" name="3-flags-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-yellow" name="3-flags-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-red" name="3-flags-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-solid" name="3-stars-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-half" name="3-stars-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-hollow" name="3-stars-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-4" name="5-quarters-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-3" name="5-quarters-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-2" name="5-quarters-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-1" name="5-quarters-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-0" name="5-quarters-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-4" name="5-ratings-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-3" name="5-ratings-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-2" name="5-ratings-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-1" name="5-ratings-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-0" name="5-ratings-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-4" name="5-boxes-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-3" name="5-boxes-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-2" name="5-boxes-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-1" name="5-boxes-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-0" name="5-boxes-icon-set,0"></span></span>
                                </div>
                            </div>
                            </td>
                            <td>
                                <label class="des4_iconSet"></label>
                            </td>
                            <td class="row4-mayHiddenParts">
                                <select class="operator4">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.operator.largeOrEqu"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.operator.large"></option>
                                </select>
                            </td>
                            <td class="row4-mayHiddenParts">
                                <div class="rangeSelectContainer">
                                    <input type="text" class="value4"/>
                                    <div class="rangeSelectButton value4Button" data-name="value4">
                                        <span></span>
                                    </div>
                                </div>
                            </td>
                            <td class="row4-mayHiddenParts">
                                <select class="value4Selector">
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.num"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percent"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.formula"></option>
                                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.commonSelector.percentile"></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="iconSet-dataRow5">
                            <td>
                            <div class="icon5-frame icon-frame">
                                <span class="iconWrapper">
                                <span class="icon5 iconSetsIcons"></span>
                                    <span class="iconText"></span>
                                </span>
                                <div class="icons-popup-dialog">
                                    <span class="icon-wrapper"><span class="iconSetsIcons no-cell-icon"
                                                                     name="noIcons,0"  data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.iconSets.noCellIcon">No Cell Icon</span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-green" name="3-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-yellow" name="3-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-red" name="3-arrows-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-arrow-gray" name="3-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-arrow-gray" name="3-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-arrow-gray" name="3-arrows-gray-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-green" name="3-triangles-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons minus-yellow" name="3-triangles-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-triangle-red" name="3-triangles-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-gray" name="4-arrows-gray-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-gray" name="4-arrows-gray-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-up-arrow-yellow" name="4-arrows-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons right-down-arrow-yellow" name="4-arrows-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-green" name="3-traffic-lights-unrimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-yellow" name="3-traffic-lights-unrimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-red" name="3-traffic-lights-unrimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-green" name="3-traffic-lights-rimmed-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-yellow" name="3-traffic-lights-rimmed-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-rimmed-red" name="3-traffic-lights-rimmed-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons up-triangle-yellow" name="3-signs-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons down-rhombus-red" name="3-signs-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons traffic-light-black" name="4-traffic-lights-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-red" name="red-to-black-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-pink" name="red-to-black-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-gray" name="red-to-black-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons ball-black" name="red-to-black-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-circled-green" name="3-symbols-circled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-circled-yellow" name="3-symbols-circled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-circled-red" name="3-symbols-circled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons check-uncircled-green" name="3-symbols-uncircled-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons notice-uncircled-yellow" name="3-symbols-uncircled-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons close-uncircled-red" name="3-symbols-uncircled-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-green" name="3-flags-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-yellow" name="3-flags-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons flag-red" name="3-flags-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-solid" name="3-stars-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-half" name="3-stars-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons star-hollow" name="3-stars-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-4" name="5-quarters-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-3" name="5-quarters-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-2" name="5-quarters-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-1" name="5-quarters-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons quarters-0" name="5-quarters-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-4" name="5-ratings-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-3" name="5-ratings-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-2" name="5-ratings-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-1" name="5-ratings-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons rating-0" name="5-ratings-icon-set,0"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-4" name="5-boxes-icon-set,4"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-3" name="5-boxes-icon-set,3"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-2" name="5-boxes-icon-set,2"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-1" name="5-boxes-icon-set,1"></span></span>
                                    <span class="icon-wrapper"><span class="iconSetsIcons box-0" name="5-boxes-icon-set,0"></span></span>
                                </div>
                            </div>
                            </td>
                            <td>
                                <label class="des5_iconSet"></label>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="formatContain">
                <label class="description"
                       data-bind="text: res.conditionalFormatting.newFormattingRule.formatContain.description"></label>
                <br/>
                <select class="formatContain-type">
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatContain.type.cellValue"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatContain.type.specificText"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatContain.type.dateOccurring"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatContain.type.blanks"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatContain.type.noBlanks"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatContain.type.errors"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatContain.type.noErrors"></option>
                </select>
                <select class="operator">
                </select>
                <div class="rangeSelectContainer textBox-cell1-container">
                    <input type="text" class="textBox-cell1"/>
                    <div class="rangeSelectButton" data-name="textBox-cell1">
                        <span></span>
                    </div>
                </div>
                <label class="label-and" data-bind="text: res.conditionalFormatting.common.and"></label>
                <div class="rangeSelectContainer textBox-cell2-container">
                    <input type="text" class="textBox-cell2"/>
                    <div class="rangeSelectButton" data-name="textBox-cell2">
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="formatRankedValue">
                <label class="description"
                       data-bind="text: res.conditionalFormatting.newFormattingRule.formatRankedValue.description"></label>
                <br/>
                <select class="formatRankedValue-type">
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatRankedValue.type.top"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatRankedValue.type.bottom"></option>
                </select>
                <input value="10" class="itemCounts"/>
            </div>
            <div class="formatAbove">
                <label class="description"
                       data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.description"></label>
                <br/>
                <select class="formatAbove-type">
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.above"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.below"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.equalOrAbove"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.equalOrBelow"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.std1Above"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.std1Below"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.std2Above"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.std2Below"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.std3Above"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.type.std3Below"></option>
                </select>
                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatAbove.description2"></label>
            </div>
            <div class="formatUnique">
                <label class="description"
                       data-bind="text: res.conditionalFormatting.newFormattingRule.formatUnique.description"></label>
                <br/>
                <select class="formatUnique-type">
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatUnique.type.duplicate"></option>
                    <option data-bind="text: res.conditionalFormatting.newFormattingRule.formatUnique.type.unique"></option>
                </select>
                <label data-bind="text: res.conditionalFormatting.newFormattingRule.formatUnique.description2"></label>
            </div>
            <div class="useFormula">
                <div>
                    <label class="description"
                           data-bind="text: res.conditionalFormatting.newFormattingRule.useFormula.description"></label>
                </div>
                <div>
                    <div class="rangeSelectContainer textBox-cell1-container">
                        <input type="text" class="formula-input"/>
                        <div class="rangeSelectButton" data-name="formula-input">
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="format-rule-sperator-line"></div>
            <div class="preview">
                <label class="preview-description"
                       data-bind="text: res.conditionalFormatting.newFormattingRule.preview.description"></label>
                <div class="previewContainer-common" align="center">
                    <div class="preview-common">
                    </div>
                </div>
                <button class="format-button" role="button" aria-disabled="false" type="button">
                    <span data-bind="text: res.conditionalFormatting.newFormattingRule.preview.buttonText"></span>
                </button>
            </div>
        </div>
    </div>
    <div class="negativeValueAndAxisSettings-dialog">
        <fieldset>
            <legend data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group1.title"></legend>
            <input class="default-radio" type="radio" name="fill-color" id="fill-color-editor1" value="fillColor"/>
            <label class="vertical-top" for="fill-color-editor1"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group1.fillColor"></label>
            <div class="fillcolor-frame">
                    <span>
                        <span class="fillcolor-span"></span>
                    </span>
                <div class="fillcolor-picker">
                </div>
            </div>
            <br/>
            <input type="radio" name="fill-color" id="fill-color-editor2" value="apply"/>
            <label class="vertical-top" for="fill-color-editor2"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group1.apply"></label>
        </fieldset>
        <fieldset class="borderColor">
            <legend data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group2.title"></legend>
            <input type="radio" name="border-color" id="border-color-editor1" value="borderColor"/>
            <label class="vertical-top" for="border-color-editor1"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group2.borderColor"></label>
            <div class="bordercolor-frame">
                    <span>
                        <span class="bordercolor-span"></span>
                    </span>
                <div class="bordercolor-picker">
                </div>
            </div>
            <br/>
            <input class="default-radio" type="radio" name="border-color" id="border-color-editor2" value="apply"/>
            <label class="vertical-top" for="border-color-editor2"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group2.apply"></label>
        </fieldset>
        <fieldset>
            <legend data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group3.title"></legend>
            <label style="line-height: 25px"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group3.description"></label>
            <br/>
            <input class="default-radio" type="radio" name="axis-positon" id="axis-positon-editor1" value="auto"/>
            <label class="vertical-top" for="axis-positon-editor1"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group3.radio.auto"></label>
            <br/>
            <input type="radio" name="axis-positon" id="axis-positon-editor2" value="cell"/>
            <label class="vertical-top" for="axis-positon-editor2"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group3.radio.cell"></label>
            <br/>
            <input type="radio" name="axis-positon" id="axis-positon-editor3" value="none"/>
            <label class="vertical-top" for="axis-positon-editor3"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group3.radio.none"></label>
            <br/>
            <label class="vertical-top"
                   data-bind="text: res.conditionalFormatting.newFormattingRule.formatOnValue.dataBar.negativeDialog.group3.axisColor"></label>
            <div class="axiscolor-frame">
                    <span>
                        <span class="axiscolor-span"></span>
                    </span>
                <div class="axiscolor-picker">
                </div>
            </div>
        </fieldset>
    </div>
    <div class="format-rules-manager-dialog">
        <!--<span>Show formatting rules for: </span>
        <select>
                <option>Current Selection</option>
            <option>This Worksheet</option>
            </select>-->
        <div class="format-rules-description">
            <span data-bind="text: res.formattingRulesManagerDialog.rulesScopeLabel">Formatting rules for this worksheet:</span>
        </div>
        <div class="format-rules-view-container">
            <div class="format-rules-button-set">
                <button id="new-rule">
                    <span class="add-format-rule format-icon"></span>
                    <span data-bind="text: res.formattingRulesManagerDialog.newRule">New Rule...</span>
                </button>
                <button id="edit-rule">
                    <span class="edit-format-rule format-icon"></span>
                    <span data-bind="text: res.formattingRulesManagerDialog.editRule">Edit Rule...</span>
                </button>
                <button id="delete-rule">
                    <span class="delete-format-rule format-icon"></span>
                    <span data-bind="text: res.formattingRulesManagerDialog.deleteRule">Delete Rule</span>
                </button>
                <button id="format-rule-move-up" class="format-rule-button">
                    <span class="move-up-format-rule format-icon"></span>
                </button>
                <button id="format-rule-move-down" class="format-rule-button">
                    <span class="move-down-format-rule format-icon"></span>
                </button>
            </div>

            <div class="grid-border">
                <div class="format-rules-grid-head-wrapper">
                    <table class="format-rules-grid-header" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="rule-column">
                                <a data-bind="text: res.formattingRulesManagerDialog.gridTitleRule">Rule (applied in order
                                    shown)</a>
                            </th>
                            <th class="format-column">
                                <a data-bind="text: res.formattingRulesManagerDialog.gridTitleFormat">Format</a>
                            </th>
                            <th class="applies-to-column">
                                <a data-bind="text: res.formattingRulesManagerDialog.gridTitleAppliesTo">Applies to</a>
                            </th>
                            <th class="stop-if-true-column">
                                <a data-bind="text: res.formattingRulesManagerDialog.gridTitleStopIfTrue">Stop If True</a>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="format-rules-grid-scroll-wrapper">
                    <table class="format-rules-grid-body" cellspacing="0">
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="save-as-dialog">
        <div style="margin:auto; width:220px;">
            <span class="option-title" data-bind="text: res.saveAsDialog.fileNameLabel">File Name:</span>
            <br />
            <input class="option-input" id="saveAsFileName" autofocus style="margin-left: 16px;margin-top:4px;" /><span id="saveAsFileExtension"></span>
        </div>
    </div>
    <div class="range-select-dialog">
        <div id="rangeSelect" class="rangeSelectWrapper rangeSelect"></div>
    </div>

