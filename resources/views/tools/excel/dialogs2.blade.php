
    <div class="fill-dialog">
        <div class="block-box">
            <div class="groupbox">
                <label class="groupbox-label" data-bind="text: res.seriesDialog.seriesIn">Series in</label>
                <div>
                    <input id="series-rows" type="radio" name="series" />
                    <label for="series-rows" data-bind="text: res.seriesDialog.rows">Rows</label>
                </div>
                <div>
                    <input id="series-columns" type="radio" name="series" />
                    <label for="series-columns" data-bind="text: res.seriesDialog.columns">Columns</label>
                </div>
            </div>
            <div class="groupbox">
                <label class="groupbox-label" data-bind="text: res.seriesDialog.type">Type</label>
                <div>
                    <input id="type-linear" type="radio" name="type" />
                    <label for="type-linear" data-bind="text: res.seriesDialog.linear">Linear</label>
                </div>
                <div>
                    <input id="type-growth" type="radio" name="type" />
                    <label for="type-growth" data-bind="text: res.seriesDialog.growth">Growth</label>
                </div>
                <div>
                    <input id="type-date" type="radio" name="type" />
                    <label for="type-date" data-bind="text: res.seriesDialog.date">Date</label>
                </div>
                <div>
                    <input id="type-autofill" type="radio" name="type" />
                    <label for="type-autofill" data-bind="text: res.seriesDialog.autoFill">AutoFill</label>
                </div>
            </div>
            <div class="groupbox">
                <label class="groupbox-label" data-bind="text: res.seriesDialog.dateUnit">Date unit</label>
                <div>
                    <input id="date-day" type="radio" name="date" />
                    <label for="date-day" data-bind="text: res.seriesDialog.day">Day</label>
                </div>
                <div>
                    <input id="date-weekday" type="radio" name="date" />
                    <label for="date-weekday" data-bind="text: res.seriesDialog.weekday">Weekday</label>
                </div>
                <div>
                    <input id="date-month" type="radio" name="date" />
                    <label for="date-month" data-bind="text: res.seriesDialog.month">Month</label>
                </div>
                <div>
                    <input id="date-year" type="radio" name="date" />
                    <label for="date-year" data-bind="text: res.seriesDialog.year">Year</label>
                </div>
            </div>
        </div>
        <div class="clear-float"></div>
        <div class="series-trend, block-box">
            <input id="series-trend" type="checkbox" />
            <label for="series-trend" data-bind="text: res.seriesDialog.trend">Trend</label>
        </div>
        <div class="block-box">
            <label for="step-value" data-bind="text: res.seriesDialog.stepValue">Step Value</label>
            <input id="step-value" type="text" class="series-input">
            <label for="stop-value" data-bind="text: res.seriesDialog.stopValue">Stop Value</label>
            <input id="stop-value" type="text" class="series-input">
        </div>
    </div>

    <div class="sort-dialog">

        <div class="sort-button-sets">
            <button id="add-level">
                <span class="sort-add-level sort-icon"></span>
                <span data-bind="text: res.customSortDialog.addLevel">Add Level</span>
            </button>
            <button id="delete-level">
                <span class="sort-delete-level sort-icon"></span>
                <span data-bind="text: res.customSortDialog.deleteLevel">Delete Level</span>
            </button>
            <button id="copy-level">
                <span class="sort-copy-level sort-icon"></span>
                <span data-bind="text: res.customSortDialog.copyLevel">Copy Level</span>
            </button>
            <button id="move-up" class="sort-move-button">
                <span class="sort-move-up sort-icon"></span>
            </button>
            <button id="move-down" class="sort-move-button">
                <span class="sort-move-down sort-icon"></span>
            </button>
            <button id="sort-options">
                <span class="sort-options sort-icon"></span>
                <span data-bind="text: res.customSortDialog.options">Options...</span>
            </button>
        </div>
        <div class="sort-panel">
            <div>
                <label class="sort-header" data-bind="text: res.customSortDialog.sortBy">SortBy</label>
                <label class="sort-header" data-bind="text: res.customSortDialog.sortOn">SortOn</label>
                <label class="sort-header" data-bind="text: res.customSortDialog.sortOrder">SortOrder</label>
            </div>
            <div class="clear-float"></div>
            <div class="sort-infolist">
            </div>
        </div>

    </div>

    <div class="sort-option-dialog">
        <div>
            <label data-bind="text: res.customSortDialog.orientation">Orientation</label>
        </div>
        <div>
            <input id="sort-byrows" type="radio" name="sort-options" />
            <label for="sort-byrows" data-bind="text: res.customSortDialog.sortTopToBottom">Sort top to bottom</label>
        </div>
        <div>
            <input id="sort-bycols" type="radio" name="sort-options" />
            <label for="sort-bycols" data-bind="text: res.customSortDialog.sortLeftToRight">Sort left to right</label>
        </div>
    </div>

    <div class="spread-setting-dialog">
        <div class="spread-setting-tab">
            <ul>
                <li><a href="#spread-setting-general" data-bind="text: res.spreadSettingDialog.general">General</a></li>
                <li><a href="#spread-setting-scrollbar" data-bind="text: res.spreadSettingDialog.scrollBars">ScrollBars</a>
                </li>
                <li><a href="#spread-setting-tabstrip" data-bind="text: res.spreadSettingDialog.tabStrip">Tab Strip</a></li>
                <li><a href="#spread-setting-clipboard" data-bind="text: res.spreadSettingDialog.clipboard">Clipboard</a>
                </li>
            </ul>
            <div id="spread-setting-general">
                <div class="setting-block">
                    <label data-bind="text: res.spreadSettingDialog.settings">Settings</label>
                    <span class="horizontalseparator"></span>
                </div>
                <div class="column-half">
                    <div class="setting-block">
                        <input type="checkbox" id="setting-dropdrop" />
                        <label for="setting-dropdrop" data-bind="text: res.spreadSettingDialog.allowDragDrop">Allow
                            Drag and
                            Drop</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="setting-dragfill" />
                        <label for="setting-dragfill" data-bind="text: res.spreadSettingDialog.allowDragFill">Allow
                            Drag and
                            Fill</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="setting-undo" />
                        <label for="setting-undo" data-bind="text: res.spreadSettingDialog.allowUndo">Allow Undo</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="setting-dragmerge" />
                        <label for="setting-dragmerge" data-bind="text: res.spreadSettingDialog.allowDragMerge">Allow
                            Drag
                            and Merge</label>
                    </div>
                </div>
                <div class="column-half">
                    <div class="setting-block">
                        <input type="checkbox" id="setting-formula" />
                        <label for="setting-formula" data-bind="text: res.spreadSettingDialog.allowFormula">Allow User
                            to
                            Enter Formulas</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="setting-zoom" />
                        <label for="setting-zoom" data-bind="text: res.spreadSettingDialog.allowZoom">Allow Zoom</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="setting-overflow" />
                        <label for="setting-overflow" data-bind="text: res.spreadSettingDialog.allowOverflow">Allow
                            Overflow</label>
                    </div>
                </div>
            </div>
            <div id="spread-setting-scrollbar">
                <div class="setting-block">
                    <label data-bind="text: res.spreadSettingDialog.visibility">Visibility</label>
                    <span class="horizontalseparator"></span>
                </div>
                <div class="column-half">
                    <div class="setting-block">
                        <input type="checkbox" id="setting-vertical-scrollbar" />
                        <label for="setting-vertical-scrollbar" data-bind="text: res.spreadSettingDialog.verticalScrollBar">Vertical
                            ScrollBar</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="setting-scrollbar-showmax" />
                        <label for="setting-scrollbar-showmax" data-bind="text: res.spreadSettingDialog.scrollbarShowMax">ScrollBar
                            ShowMax</label>
                    </div>
                </div>
                <div class="column-half">
                    <div class="setting-block">
                        <input type="checkbox" id="setting-horizontal-scrollbar" />
                        <label for="setting-horizontal-scrollbar" data-bind="text: res.spreadSettingDialog.horizontalScrollBar">Horizontal
                            ScrollBar</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="setting-scrollbar-maxalign" />
                        <label for="setting-scrollbar-maxalign" data-bind="text: res.spreadSettingDialog.scrollbarMaxAlign">ScrollBar
                            MaxAlign</label>
                    </div>
                </div>
            </div>
            <div id="spread-setting-tabstrip">
                <div class="setting-block">
                    <label data-bind="text: res.spreadSettingDialog.settings">Setting</label>
                    <span class="horizontalseparator"></span>
                </div>
                <div class="column-half">
                    <div class="setting-block">
                        <input type="checkbox" id="setting-tab-visible" />
                        <label for="setting-tab-visible" data-bind="text: res.spreadSettingDialog.tabStripVisible">Tab
                            Strip
                            Visible</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="setting-new-tab" />
                        <label for="setting-new-tab" data-bind="text: res.spreadSettingDialog.newTabVisible">New Tab
                            Visible</label>
                    </div>
                </div>
                <div class="column-half">
                    <div class="setting-block">
                        <input type="checkbox" id="setting-tab-edit" />
                        <label for="setting-tab-edit" data-bind="text: res.spreadSettingDialog.tabStripEditable">Tab
                            Strip
                            Editable</label>
                    </div>
                </div>
                <div class="clear-float"></div>
                <div>
                    <label for="setting-tab-ratio" class="setting-tab-ratio-label" data-bind="text: res.spreadSettingDialog.tabStripRatio">Tab
                        Strip Ratio(as percentage)</label>
                    <input id="setting-tab-ratio" class="setting-tab-ratio" />
                    <label>%</label>
                </div>
            </div>
            <div id="spread-setting-clipboard">
                <div class="setting-block">
                    <label data-bind="text: res.spreadSettingDialog.settings">Settings</label>
                    <span class="horizontalseparator"></span>
                </div>
                <div class="setting-block">
                    <div class="column-half">
                        <div class="setting-block">
                            <input type="checkbox" id="setting-copyExcelStyle">
                            <label for="setting-copyExcelStyle" data-bind="text: res.spreadSettingDialog.allowCopyPasteExcelStyle">Allow
                                Copy Paste Excel
                                Style</label>
                        </div>
                    </div>
                    <div class="column-half">
                        <div class="setting-block">
                            <input type="checkbox" id="setting-copyExtendRange">
                            <label for="setting-copyExtendRange" data-bind="text: res.spreadSettingDialog.allowExtendPasteRange">Allow
                                Paste Extend
                                Range</label>
                        </div>
                    </div>
                </div>
                <div class="setting-block">
                    <div>
                        <label data-bind="text: res.spreadSettingDialog.headerOptions">Header Options:</label>
                    </div>
                    <div>
                        <div class="column-half">
                            <div class="setting-block">
                                <input type="radio" id="setting-noHeaders" name="headerOptions" value="noHeaders">
                                <label for="setting-noHeaders" data-bind="text: res.spreadSettingDialog.noHeaders">No
                                    Headers</label>
                            </div>
                            <div class="setting-block">
                                <input type="radio" id="setting-allHeaders" name="headerOptions" value="allHeaders">
                                <label for="setting-allHeaders" data-bind="text: res.spreadSettingDialog.allHeaders">All
                                    Headers</label>
                            </div>
                        </div>
                        <div class="column-half">
                            <div class="setting-block">
                                <input type="radio" id="setting-rowHeaders" name="headerOptions" value="rowHeaders">
                                <label for="setting-rowHeaders" data-bind="text: res.spreadSettingDialog.rowHeaders">Row
                                    Headers</label>
                            </div>
                            <div class="setting-block">
                                <input type="radio" id="setting-columnHeaders" name="headerOptions" value="columnHeaders">
                                <label for="setting-columnHeaders" data-bind="text: res.spreadSettingDialog.columnHeaders">Column
                                    Headers</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sheet-setting-dialog">
        <div class="sheet-setting-tab">
            <ul>
                <li><a href="#sheet-setting-general" data-bind="text: res.sheetSettingDialog.general">General</a></li>
                <li><a href="#sheet-setting-gridline" data-bind="text: res.sheetSettingDialog.gridlines">Grid Lines</a></li>
                <li><a href="#sheet-setting-calculation" data-bind="text: res.sheetSettingDialog.calculation">Calculation</a></li>
                <li><a href="#sheet-setting-headers" data-bind="text: res.sheetSettingDialog.headers">Headers</a></li>
                <li><a href="#sheet-setting-sheettab" data-bind="text: res.sheetSettingDialog.sheetTab">SheetTab</a></li>
            </ul>
            <div id="sheet-setting-general">
                <div class="general-count">
                    <div class="setting-block">
                        <div class="general-count-label" data-bind="text: res.sheetSettingDialog.columnCount">Column
                            Count:
                        </div>
                        <input id="sheet-column-count" class="general-count-value" />
                    </div>
                    <div class="setting-block">
                        <div class="general-count-label" data-bind="text: res.sheetSettingDialog.rowCount">Row Count:</div>
                        <input id="sheet-row-count" class="general-count-value" />
                    </div>
                    <div class="setting-block">
                        <div class="general-count-label" data-bind="text: res.sheetSettingDialog.frozenColumnCount">Frozen
                            Column Count:
                        </div>
                        <input id="frozen-column-count" class="general-count-value" />
                    </div>
                    <div class="setting-block">
                        <div class="general-count-label" data-bind="text: res.sheetSettingDialog.frozenRowCount">Frozen
                            Row
                            Count:
                        </div>
                        <input id="frozen-row-count" class="general-count-value" />
                    </div>
                    <div class="setting-block">
                        <div class="general-count-label" data-bind="text: res.sheetSettingDialog.trailingFrozenColumnCount">
                            Trailing Frozen Column Count:
                        </div>
                        <input id="trailing-column-count" class="general-count-value" />
                    </div>
                    <div class="setting-block">
                        <div class="general-count-label" data-bind="text: res.sheetSettingDialog.trailingFrozenRowCount">
                            Trailing Frozen Row Count:
                        </div>
                        <input id="trailing-row-count" class="general-count-value" />
                    </div>
                </div>
                <div class="general-selection">
                    <fieldset>
                        <legend data-bind="text: res.sheetSettingDialog.selectionPolicy">Selection Policy</legend>
                        <div class="setting-block">
                            <input type="radio" id="selection-policy-single" name="selection-policy" value="0" />
                            <label for="selection-policy-single" data-bind="text: res.sheetSettingDialog.singleSelection">Single
                                Select</label>
                        </div>
                        <div class="setting-block">
                            <input type="radio" id="selection-policy-range" name="selection-policy" value="1" />
                            <label for="selection-policy-range" data-bind="text: res.sheetSettingDialog.rangeSelection">Range
                                Select</label>
                        </div>
                        <div class="setting-block">
                            <input type="radio" id="selection-policy-multiple" name="selection-policy" value="2" />
                            <label for="selection-policy-multiple" data-bind="text: res.sheetSettingDialog.multiRangeSelection">MultiRange
                                Select</label>
                        </div>
                    </fieldset>
                    <div class="setting-block">
                        <input type="checkbox" id="sheet-protect" />
                        <label for="sheet-protect" data-bind="text: res.sheetSettingDialog.protect">Protect</label>
                    </div>
                </div>
            </div>
            <div id="sheet-setting-gridline">
                <div class="gridline-column">
                    <div class="setting-block">
                        <input type="checkbox" id="gridline-horizontal" />
                        <label for="gridline-horizontal" data-bind="text: res.sheetSettingDialog.horizontalGridline">Horizontal
                            GridLine</label>
                    </div>
                    <div class="setting-block">
                        <input type="checkbox" id="gridline-vertical" />
                        <label for="gridline-vertical" data-bind="text: res.sheetSettingDialog.verticalGridline">Vertical
                            GridLine</label>
                    </div>
                </div>
                <div class="gridline-column">
                    <div class="setting-block">
                        <div class="float-left" data-bind="text: res.sheetSettingDialog.gridlineColor">GridLine Color:</div>
                        <div id="gridline-color-frame" class="color-frame float-left">
                            <span>
                                <span id="gridline-color-span" class="color-span"></span>
                            </span>
                            <div id="gridline-color-picker">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sheet-setting-calculation">
                <div class="setting-block" data-bind="text: res.sheetSettingDialog.referenceStyle">Reference Style:</div>
                <div class="setting-block">
                    <input type="radio" id="reference-A1" name="reference-style" value="0" />
                    <label for="reference-A1" data-bind="text: res.sheetSettingDialog.a1">A1</label>
                </div>
                <div class="setting-block">
                    <input type="radio" id="reference-R1C1" name="reference-style" value="1" />
                    <label for="reference-R1C1" data-bind="text: res.sheetSettingDialog.r1c1">R1C1</label>
                </div>
            </div>
            <div id="sheet-setting-headers">
                <div class="header-setting-tab">
                    <ul>
                        <li style="width:120px"><a href="#header-setting-column" data-bind="text: res.sheetSettingDialog.columnHeaders">Column
                                Headers</a>
                        </li>
                        <li style="width:120px"><a href="#header-setting-row" data-bind="text: res.sheetSettingDialog.rowHeaders">Row
                                Headers</a></li>
                    </ul>
                    <div id="header-setting-column">
                        <div class="setting-block">
                            <div class="header-count-label" data-bind="text: res.sheetSettingDialog.columnHeaderRowCount">
                                Column Header Row Count:
                            </div>
                            <input id="header-column-count" class="header-count-value" />
                        </div>
                        <div class="setting-block">
                            <div class="header-count-label" data-bind="text: res.sheetSettingDialog.columnHeaderAutoText">
                                Column Header Auto Text:
                            </div>
                            <div class="header-count-autotext">
                                <input type="radio" id="header-column-autotext-blank" name="column-autotext" value="0" />
                                <label for="header-column-autotext-blank" data-bind="text: res.sheetSettingDialog.blank">Blank</label>
                                <input type="radio" id="header-column-autotext-numbers" name="column-autotext" value="1" />
                                <label for="header-column-autotext-numbers" data-bind="text: res.sheetSettingDialog.numbers">Numbers</label>
                                <input type="radio" id="header-column-autotext-letters" name="column-autotext" value="2" />
                                <label for="header-column-autotext-letters" data-bind="text: res.sheetSettingDialog.letters">Letters</label>
                            </div>
                        </div>
                        <div class="setting-block">
                            <div class="header-count-label" data-bind="text: res.sheetSettingDialog.columnHeaderAutoIndex">
                                Column Header Auto Index:
                            </div>
                            <input id="header-column-autoindex" class="header-count-value" />
                        </div>
                        <div class="setting-block">
                            <div class="header-count-label" data-bind="text: res.sheetSettingDialog.defaultRowHeight">
                                Default Row Height:
                            </div>
                            <input id="header-column-default-height" class="header-count-value" />
                        </div>
                        <div class="setting-block">
                            <input type="checkbox" id="header-column-visible" />
                            <label for="header-column-visible" data-bind="text: res.sheetSettingDialog.columnHeaderVisible">Column
                                Header Visible</label>
                        </div>
                    </div>
                    <div id="header-setting-row">
                        <div class="setting-block">
                            <div class="header-count-label" data-bind="text: res.sheetSettingDialog.rowHeaderColumnCount">
                                Row Header Column Count:
                            </div>
                            <input id="header-row-count" class="header-count-value" />
                        </div>
                        <div class="setting-block">
                            <div class="header-count-label" data-bind="text: res.sheetSettingDialog.rowHeaderAutoText">Row
                                Header Auto Text:
                            </div>
                            <div class="header-count-autotext">
                                <input type="radio" id="header-row-autotext-blank" name="row-autotext" value="0" />
                                <label for="header-row-autotext-blank" data-bind="text: res.sheetSettingDialog.blank">Blank</label>
                                <input type="radio" id="header-row-autotext-numbers" name="row-autotext" value="1" />
                                <label for="header-row-autotext-numbers" data-bind="text: res.sheetSettingDialog.numbers">Numbers</label>
                                <input type="radio" id="header-row-autotext-letters" name="row-autotext" value="2" />
                                <label for="header-row-autotext-letters" data-bind="text: res.sheetSettingDialog.letters">Letters</label>
                            </div>
                        </div>
                        <div class="setting-block">
                            <div class="header-count-label" data-bind="text: res.sheetSettingDialog.rowHeaderAutoIndex">Row
                                Header Auto Index:
                            </div>
                            <input id="header-row-autoindex" class="header-count-value" />
                        </div>
                        <div class="setting-block">
                            <div class="header-count-label" data-bind="text: res.sheetSettingDialog.defaultColumnWidth">
                                Default Column Width:
                            </div>
                            <input id="header-row-default-width" class="header-count-value" />
                        </div>
                        <div class="setting-block">
                            <input type="checkbox" id="header-row-visible" />
                            <label for="header-row-visible" data-bind="text: res.sheetSettingDialog.rowHeaderVisible">Row
                                Header Visible</label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sheet-setting-sheettab">
                <div class="setting-block">
                    <div class="float-left" data-bind="text: res.sheetSettingDialog.sheetTabColor">Sheet Tab Color:</div>
                    <div id="sheettab-color-frame" class="color-frame float-left">
                        <span>
                            <span id="sheettab-color-span" class="color-span"></span>
                        </span>
                        <div id="sheettab-color-picker">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="data-validation-dialog">
        <div class="data-validation-tab">
            <ul>
                <li style="width:120px"><a href="#validation-settings" data-bind="text: res.dataValidationDialog.settings">Settings</a>
                </li>
                <li style="width:120px"><a href="#validation-input-message" data-bind="text: res.dataValidationDialog.inputMessage">Input
                        Message</a></li>
                <li style="width:120px"><a href="#validation-error-alert" data-bind="text: res.dataValidationDialog.errorAlert">Error
                        Alert</a></li>
            </ul>
            <div id="validation-settings">
                <fieldset class="validation-fieldset">
                    <legend data-bind="text: res.dataValidationDialog.validationCriteria">Validation criteria</legend>
                    <div class="validation-content">
                        <div class="float-left">
                            <div data-bind="text: res.dataValidationDialog.allow + ':'">Allow:</div>
                            <div class="setting-block">
                                <select class="validation-criteria-type">
                                    <option value="anyValue" data-bind="text: res.dataValidationDialog.anyValue">Any
                                        value
                                    </option>
                                    <option value="wholeNumber" data-bind="text: res.dataValidationDialog.wholeNumber">Whole
                                        number
                                    </option>
                                    <option value="decimalValues" data-bind="text: res.dataValidationDialog.decimal">
                                        Decimal
                                    </option>
                                    <option value="list" data-bind="text: res.dataValidationDialog.list">List</option>
                                    <option value="date" data-bind="text: res.dataValidationDialog.date">Date</option>
                                    <option value="textLength" data-bind="text: res.dataValidationDialog.textLength">Text
                                        length
                                    </option>
                                    <option value="custom" data-bind="text: res.dataValidationDialog.custom">Custom</option>
                                </select>
                            </div>
                            <div data-bind="text: res.dataValidationDialog.dataLabel">Data:</div>
                            <div class="setting-block">
                                <select class="validation-comparison-operator">
                                    <option value="between" data-bind="text: res.dataValidationDialog.between">between
                                    </option>
                                    <option value="notBetween" data-bind="text: res.dataValidationDialog.notBetween">not
                                        between
                                    </option>
                                    <option value="equalsTo" data-bind="text: res.dataValidationDialog.equalTo">equal
                                        to
                                    </option>
                                    <option value="notEqualsTo" data-bind="text: res.dataValidationDialog.notEqualTo">not
                                        equal to
                                    </option>
                                    <option value="greaterThan" data-bind="text: res.dataValidationDialog.greaterThan">
                                        greater than
                                    </option>
                                    <option value="lessThan" data-bind="text: res.dataValidationDialog.lessThan">less
                                        than
                                    </option>
                                    <option value="greaterThanOrEqualsTo" data-bind="text: res.dataValidationDialog.greaterEqual">greater
                                        than or equal to
                                    </option>
                                    <option value="lessThanOrEqualsTo" data-bind="text: res.dataValidationDialog.lessEqual">
                                        less than or equal to
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="float-left validation-content">
                            <div>&nbsp;</div>
                            <div class="setting-block">
                                <input type="checkbox" id="validation-ignore-blank" class="validation-ignore-blank" />
                                <label for="validation-ignore-blank" data-bind="text: res.dataValidationDialog.ignoreBlank">Ignore
                                    blank</label>
                            </div>
                            <div class="validation-incell-dropdown-parent setting-block">
                                <input type="checkbox" id="validation-incell-dropdown" class="validation-incell-dropdown" />
                                <label for="validation-incell-dropdown" data-bind="text: res.dataValidationDialog.inCellDropDown">In-cell
                                    dropdown</label>
                            </div>
                        </div>
                        <div class="clear-float"></div>
                        <div class="validation-values">
                            <div>
                                <label class="validation-value1-label">&nbsp;</label>
                            </div>
                            <div class="setting-block">
                                <div class="rangeSelectContainer validation-value1-container">
                                    <input type="text" class="validation-value1" />
                                    <div class="rangeSelectButton" data-name="validation-value1">
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="validation-value2-label">&nbsp;</label>
                            </div>
                            <div class="setting-block">
                                <div class="rangeSelectContainer validation-value2-container">
                                    <input type="text" class="validation-value2" />
                                    <div class="rangeSelectButton" data-name="validation-value2">
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div id="validation-input-message">
                <div class="setting-block">
                    <input type="checkbox" id="show-input-message" class="show-input-message" />
                    <label for="show-input-message" data-bind="text: res.dataValidationDialog.showMessage">Show input
                        message when cell is selected</label>
                </div>
                <fieldset class="validation-fieldset">
                    <legend data-bind="text: res.dataValidationDialog.showMessage2">When cell is selected, show this
                        input
                        message:
                    </legend>
                    <div class="validation-content">
                        <div data-bind="text: res.dataValidationDialog.title + ':'">Title:</div>
                        <div class="setting-block">
                            <input type="text" class="input-title" />
                        </div>
                        <div data-bind="text: res.dataValidationDialog.inputMessage + ':'">Input message:</div>
                        <div class="setting-block">
                            <textarea class="input-message">
                                </textarea>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div id="validation-error-alert">
                <div class="setting-block">
                    <input type="checkbox" id="show-error-message" class="show-error-message" />
                    <label for="show-error-message" data-bind="text: res.dataValidationDialog.showError">Show error
                        alert
                        after invalid data is entered</label>
                </div>
                <fieldset class="validation-fieldset">
                    <legend data-bind="text: res.dataValidationDialog.showError2">When user enters invalid data, show
                        this
                        error alert:
                    </legend>
                    <div class="validation-content">
                        <div class="error-column float-left">
                            <div data-bind="text: res.dataValidationDialog.style">Style:</div>
                            <select class="error-style setting-block">
                                <option value="stop" data-bind="text: res.dataValidationDialog.stop">Stop</option>
                                <option value="warning" data-bind="text: res.dataValidationDialog.warning">Warning</option>
                                <option value="information" data-bind="text: res.dataValidationDialog.information">
                                    Information
                                </option>
                            </select>
                            <div class="error-icon">
                            </div>
                        </div>
                        <div class="error-column float-left">
                            <div data-bind="text: res.dataValidationDialog.title + ':'">Title:</div>
                            <div class="setting-block">
                                <input type="text" class="error-title" />
                            </div>
                            <div data-bind="text: res.dataValidationDialog.errorMessage + ':'">Error message:</div>
                            <div class="setting-block">
                                <textarea class="error-message"></textarea>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>

    <div class="group-direction-dialog">
        <fieldset class="group-fieldset">
            <legend data-bind="text: res.groupDirectionDialog.direction">Direction</legend>
            <div class="setting-block">
                <input type="checkbox" id="row-group-direction" />
                <label for="row-group-direction" data-bind="text: res.groupDirectionDialog.rowDirection">Summary rows
                    below
                    detail</label>
            </div>
            <div class="setting-block">
                <input type="checkbox" id="column-group-direction" />
                <label for="column-group-direction" data-bind="text: res.groupDirectionDialog.columnDirection">Summary
                    columns to right of detail</label>
            </div>
        </fieldset>
    </div>

    <div class="zoom-dialog">
        <fieldset class="zoom-ratio-setting-fieldset">
            <legend data-bind="text: res.zoomDialog.magnification">Magnification</legend>
            <div class="zoom-ratio-setting">
                <input id="zoom-ratio1" type="radio" name="zoom-ratio" value="200" />
                <label for="zoom-ratio1" data-bind="text: res.zoomDialog.double">200%</label>
            </div>
            <div class="zoom-ratio-setting">
                <input id="zoom-ratio2" class="default-ratio" type="radio" name="zoom-ratio" value="100" />
                <label for="zoom-ratio2" data-bind="text: res.zoomDialog.normal">100%</label>
            </div>
            <div class="zoom-ratio-setting">
                <input id="zoom-ratio3" type="radio" name="zoom-ratio" value="75" />
                <label for="zoom-ratio3" data-bind="text: res.zoomDialog.threeFourths">75%</label>
            </div>
            <div class="zoom-ratio-setting">
                <input id="zoom-ratio4" type="radio" name="zoom-ratio" value="50" />
                <label for="zoom-ratio4" data-bind="text: res.zoomDialog.half">50%</label>
            </div>
            <div class="zoom-ratio-setting">
                <input id="zoom-ratio5" type="radio" name="zoom-ratio" value="25" />
                <label for="zoom-ratio5" data-bind="text: res.zoomDialog.quarter">25%</label>
            </div>
            <div class="zoom-ratio-setting">
                <input id="zoom-ratio6" class="fit-selection" name="zoom-ratio" type="radio" value="Fit selection" />
                <label for="zoom-ratio6" data-bind="text: res.zoomDialog.fitSelection">Fit selection</label>
            </div>
            <div class="zoom-ratio-setting">
                <input id="zoom-ratio7" class="custom-ratio" type="radio" name="zoom-ratio" value="Custom:" />
                <label for="zoom-ratio7" data-bind="text: res.zoomDialog.custom">Custom:</label>
                <input class="show-zoom-text" type="text" name="zoom-custom-ratio" value="custom" id="customValue" />
                <label for="customValue" data-bind="text: res.zoomDialog.percent">%</label>
            </div>
        </fieldset>
    </div>
    <div class="insert-sparkline-dialog">
        <fieldset class="setting-block">
            <legend data-bind="text: res.insertSparklineDialog.dataRangeTitle">Choose the data that you want</legend>
            <label class="sparkline-lable" data-bind="text: res.insertSparklineDialog.dataRange">Data Range:</label>
            <div class="rangeSelectContainer">
                <input class="sparkline-data-range" type="text" />
                <div class="rangeSelectButton" data-name="sparkline-data-range">
                    <span></span>
                </div>
            </div>
        </fieldset>
        <fieldset class="setting-block">
            <legend data-bind="text: res.insertSparklineDialog.locationRangeTitle">Choose where you want the sparkline
                to be
                placed
            </legend>
            <label class="sparkline-lable" data-bind="text: res.insertSparklineDialog.locationRange">Location Range:</label>
            <div class="rangeSelectContainer">
                <input class="sparkline-location-range" type="text" />
                <div class="rangeSelectButton" data-name="sparkline-location-range">
                    <span></span>
                </div>
            </div>
        </fieldset>
        <div class="element-disabled">
            <input id="is-formula-sparkline" type="checkbox" />
            <label for="is-formula-sparkline" data-bind="text:res.insertSparklineDialog.isFormulaSparkline">IsFormulaSparkline</label>
        </div>
    </div>
    <div class="sparkline-weight-dialog">
        <div class="setting-block">
            <label data-bind="text: res.sparklineWeightDialog.inputWeight">Enter the Sparkline weight(pt)</label>
        </div>
        <div class="setting-block">
            <input type="number" class="sparkline-weight" />
        </div>
    </div>
    <div class="sparkline-marker-color-dialog">
        <div class="setting-block">
            <div class="sparkline-label" data-bind="text: res.sparklineMarkerColorDialog.negativePoints">Negative
                Points:
            </div>
            <div class="sparkline-negative-point-color-frame color-frame">
                <span>
                    <span class="sparkline-negative-point-color-span color-span"></span>
                </span>
                <div class="sparkline-negative-point-color-picker">
                </div>
            </div>
        </div>
        <div class="setting-block">
            <div class="sparkline-label" data-bind="text: res.sparklineMarkerColorDialog.markers">Markers:</div>
            <div class="sparkline-marker-point-color-frame color-frame">
                <span>
                    <span class="sparkline-marker-point-color-span color-span"></span>
                </span>
                <div class="sparkline-marker-point-color-picker">
                </div>
            </div>
        </div>
        <div class="setting-block">
            <div class="sparkline-label" data-bind="text: res.sparklineMarkerColorDialog.highPoint">High Point:</div>
            <div class="sparkline-high-point-color-frame color-frame">
                <span>
                    <span class="sparkline-high-point-color-span color-span"></span>
                </span>
                <div class="sparkline-high-point-color-picker">
                </div>
            </div>
        </div>
        <div class="setting-block">
            <div class="sparkline-label" data-bind="text: res.sparklineMarkerColorDialog.lowPoint">Low Point:</div>
            <div class="sparkline-low-point-color-frame color-frame">
                <span>
                    <span class="sparkline-low-point-color-span color-span"></span>
                </span>
                <div class="sparkline-low-point-color-picker">
                </div>
            </div>
        </div>
        <div class="setting-block">
            <div class="sparkline-label" data-bind="text: res.sparklineMarkerColorDialog.firstPoint">First Point:</div>
            <div class="sparkline-first-point-color-frame color-frame">
                <span>
                    <span class="sparkline-first-point-color-span color-span"></span>
                </span>
                <div class="sparkline-first-point-color-picker">
                </div>
            </div>
        </div>
        <div class="setting-block">
            <div class="sparkline-label" data-bind="text: res.sparklineMarkerColorDialog.lastPoint">Last Point:</div>
            <div class="sparkline-last-point-color-frame color-frame">
                <span>
                    <span class="sparkline-last-point-color-span color-span"></span>
                </span>
                <div class="sparkline-last-point-color-picker">
                </div>
            </div>
        </div>
    </div>

    <div class="pie-sparkline-dialog">
        <div class="pie-sparkline-block">
            <label data-bind="text: res.pieSparklineDialog.percentage">Percentage</label>
            <input class="pie-percentage" />
        </div>
        <div class="pie-colors-list pie-sparkline-block">
        </div>
    </div>
    <div class="area-sparkline-dialog">
        <div class="area-sparkline-block">
            <label class="area-column1" data-bind="text: res.areaSparklineDialog.line1 + ':'">Line 1:</label>
            <input class="area-line1-value" />
            <label class="area-column2" data-bind="text: res.areaSparklineDialog.line2 + ':'">Line 2:</label>
            <input class="area-line2-value" />
        </div>
        <div class="area-sparkline-block">
            <label class="area-column1" data-bind="text: res.areaSparklineDialog.min + ':'">Minimum Value:</label>
            <input class="area-min-value" />
            <label class="area-column2" data-bind="text: res.areaSparklineDialog.max + ':'">Maximum Value:</label>
            <input class="area-max-value" />
        </div>

        <div class="area-sparkline-block">
            <label class="area-column1" data-bind="text: res.areaSparklineDialog.points + ':'">Points:</label>
            <input class="area-data-range" />
        </div>
        <div class="area-sparkline-block">
            <label class="area-column1" data-bind="text: res.areaSparklineDialog.positiveColor + ':'">Positive Color</label>
            <div class="positive-color area-color"></div>
        </div>
        <div class="area-sparkline-block">
            <label class="area-column1" data-bind="text: res.areaSparklineDialog.negativeColor + ':'">Negative Color</label>
            <div class="negative-color area-color"></div>
        </div>
    </div>
    <div class="scatter-sparkline-dialog">
        <div class="scatter-sparkline-block">
            <label class="scatter-column1" data-bind="text: res.scatterSparklineDialog.points1 + ':'">Points1</label>
            <input class="scatter-points1" />
            <label class="scatter-column2" data-bind="text: res.scatterSparklineDialog.points2 + ':'">Points2</label>
            <input class="scatter-points2" />
        </div>
        <div class="scatter-sparkline-block">
            <label class="scatter-column1" data-bind="text: res.scatterSparklineDialog.minX + ':'">MinX</label>
            <input class="scatter-minx" />
            <label class="scatter-column2" data-bind="text: res.scatterSparklineDialog.minY + ':'">MinY</label>
            <input class="scatter-miny" />
        </div>
        <div class="scatter-sparkline-block">
            <label class="scatter-column1" data-bind="text: res.scatterSparklineDialog.maxX + ':'">MaxX</label>
            <input class="scatter-maxx" />
            <label class="scatter-column2" data-bind="text: res.scatterSparklineDialog.maxY + ':'">MaxY</label>
            <input class="scatter-maxy" />
        </div>
        <div class="scatter-sparkline-block">
            <label class="scatter-column1" data-bind="text: res.scatterSparklineDialog.hLine + ':'">HLine</label>
            <input class="scatter-hline" />
            <label class="scatter-column2" data-bind="text: res.scatterSparklineDialog.vLine + ':'">VLine</label>
            <input class="scatter-vline" />
        </div>
        <div class="scatter-sparkline-block">
            <label class="scatter-column1" data-bind="text: res.scatterSparklineDialog.xMinZone + ':'">XMinZone</label>
            <input class="scatter-xmin-zone" />
            <label class="scatter-column2" data-bind="text: res.scatterSparklineDialog.yMinZone + ':'">YMinZone</label>
            <input class="scatter-ymin-zone" />
        </div>
        <div class="scatter-sparkline-block">
            <label class="scatter-column1" data-bind="text: res.scatterSparklineDialog.xMaxZone + ':'">XMaxZone</label>
            <input class="scatter-xmax-zone" />
            <label class="scatter-column2" data-bind="text: res.scatterSparklineDialog.yMaxZone + ':'">YMaxZone</label>
            <input class="scatter-ymax-zone" />
        </div>
        <div class="scatter-sparkline-block">
            <label class="scatter-column1" data-bind="text: res.scatterSparklineDialog.color1 + ':'">Color 1:</label>
            <div class="scatter-color1 scatter-color-picker-block"></div>
            <label class="scatter-column2" data-bind="text: res.scatterSparklineDialog.color2 + ':'">Color 2:</label>
            <div class="scatter-color2 scatter-color-picker-block"></div>
        </div>
        <div class="scatter-sparkline-block">
            <input type="checkbox" class="scatter-tags" id="scatter-tags" />
            <label data-bind="text: res.scatterSparklineDialog.tags" for="scatter-tags">Tags</label>
            <input type="checkbox" class="scatter-drawsymbol" id="scatter-drawsymbol" />
            <label data-bind="text: res.scatterSparklineDialog.drawSymbol" for="scatter-drawsymbol">Draw Symbol</label>
            <input type="checkbox" class="scatter-drawlines" id="scatter-drawlines" />
            <label data-bind="text: res.scatterSparklineDialog.drawLines" for="scatter-drawlines">Draw Lines</label>
            <input type="checkbox" class="scatter-dash" id="scatter-dash" />
            <label data-bind="text: res.scatterSparklineDialog.dash" for="scatter-dash">Dash Line</label>
        </div>
    </div>
    <div class="compatible-sparkline-dialog">
        <div class="compatible-sparkline-argument">
            <fieldset class="separator-line">
                <legend data-bind="text: res.compatibleSparklineDialog.data">Data</legend>
                <div class="compatible-block">
                    <label class="com-data-column1" data-bind="text: res.compatibleSparklineDialog.data + ':'">Data:</label>
                    <input class="com-data com-data-input" />
                    <label class="com-data-column2" data-bind="text:res.compatibleSparklineDialog.dataOrientation + ':'">DataOrientation:</label>
                    <select class="com-data-select com-data-orientation">
                        <option value="vertical" data-bind="text: res.compatibleSparklineDialog.vertical">Vertical</option>
                        <option value="horizontal" data-bind="text: res.compatibleSparklineDialog.horizontal">Horizontal
                        </option>
                    </select>
                </div>
                <div class="compatible-block">
                    <label class="com-data-column1" data-bind="text: res.compatibleSparklineDialog.dateAxisData + ':'">DateAxisData:</label>
                    <input class="com-date-axis-data com-data-input" />
                    <label class="com-data-column2" data-bind="text: res.compatibleSparklineDialog.dateAxisOrientation + ':'">DateAxisOrientation:</label>
                    <select class="com-data-select com-date-axis-orientation">
                        <option value="vertical" data-bind="text: res.compatibleSparklineDialog.vertical">Vertical</option>
                        <option value="horizontal" data-bind="text: res.compatibleSparklineDialog.horizontal">Horizontal
                        </option>
                    </select>
                </div>
                <div class="compatible-block">
                    <label class="com-data-column1" data-bind="text:res.compatibleSparklineDialog.displayEmptyCellsAs + ':'">DisplayEmptyCellsAs:</label>
                    <select class="com-display-emptycell-as com-data-select">
                        <option value="gaps" data-bind="text: res.compatibleSparklineDialog.gaps">Gaps</option>
                        <option value="zero" data-bind="text: res.compatibleSparklineDialog.zero">Zero</option>
                        <option value="connect" data-bind="text: res.compatibleSparklineDialog.connect">Connect</option>
                    </select>
                </div>
                <div class="compatible-block">
                    <input id="com-display-hidden" type="checkbox" class="com-display-hidden" />
                    <label for="com-display-hidden" data-bind="text:res.compatibleSparklineDialog.displayHidden"></label>
                </div>
            </fieldset>
            <fieldset class="separator-line">
                <legend data-bind="text: res.compatibleSparklineDialog.show"></legend>
                <div class="compatible-block">
                    <input type="checkbox" id="com-show-first" class="com-show-first" />
                    <label for="com-show-first" class="com-show-column1" data-bind="text: res.compatibleSparklineDialog.showFirst">Show
                        First</label>
                    <input type="checkbox" id="com-show-last" class="com-show-last" />
                    <label for="com-show-last" class="com-show-column2" data-bind="text: res.compatibleSparklineDialog.showLast">Show
                        Last</label>
                    <input type="checkbox" id="com-show-high" class="com-show-high" />
                    <label for="com-show-high" class="com-show-column3" data-bind="text: res.compatibleSparklineDialog.showHigh">Show
                        High</label>
                </div>
                <div class="compatible-block">
                    <input type="checkbox" id="com-show-low" class="com-show-low" />
                    <label for="com-show-low" class="com-show-column1" data-bind="text: res.compatibleSparklineDialog.showLow">Show
                        Low</label>
                    <input type="checkbox" id="com-show-negative" class="com-show-negative" />
                    <label for="com-show-negative" class="com-show-column2" data-bind="text: res.compatibleSparklineDialog.showNegative">Show
                        Negative</label>
                    <input type="checkbox" id="com-show-markers" class="com-show-markers" />
                    <label for="com-show-markers" class="com-show-column3" data-bind="text: res.compatibleSparklineDialog.showMarkers">Show
                        Markers</label>
                </div>
            </fieldset>
            <fieldset class="separator-line">
                <legend data-bind="text: res.compatibleSparklineDialog.group"></legend>
                <div class="compatible-block">
                    <label class="com-group-column1" data-bind="text:res.compatibleSparklineDialog.minAxisType + ':'"></label>
                    <select class="com-group-select com-min-axis-type">
                        <option value="individual" data-bind="text: res.compatibleSparklineDialog.individual"></option>
                        <option value="custom" data-bind="text: res.compatibleSparklineDialog.custom"></option>
                    </select>
                    <label class="com-group-column2" data-bind="text:res.compatibleSparklineDialog.maxAxisType + ':'"></label>
                    <select class="com-group-select com-max-axis-type">
                        <option value="individual" data-bind="text: res.compatibleSparklineDialog.individual"></option>
                        <option value="custom" data-bind="text: res.compatibleSparklineDialog.custom"></option>
                    </select>
                </div>
                <div class="compatible-block">
                    <label class="com-group-column1 com-manual-min-label" data-bind="text: res.compatibleSparklineDialog.manualMin + ':'"></label>
                    <input class="com-group-input com-manual-min" />
                    <label class="com-group-column2 com-manual-max-label" data-bind="text:res.compatibleSparklineDialog.manualMax + ':'"></label>
                    <input class="com-group-input com-manual-max" />
                </div>
                <div class="compatible-block">
                    <input id="com-right-to-left" class="com-right-to-left" type="checkbox" />
                    <label for="com-right-to-left" data-bind="text: res.compatibleSparklineDialog.rightToLeft">RightToLeft</label>
                    <input id="com-display-xaxis" class="com-display-xaxis" type="checkbox" />
                    <label for="com-display-xaxis" data-bind="text: res.compatibleSparklineDialog.displayXAxis"></label>
                </div>
            </fieldset>
            <fieldset class="separator-line">
                <legend data-bind="text: res.compatibleSparklineDialog.style"></legend>
                <div class="compatible-block">
                    <input class="com-color-setting" type="button" data-bind="value: res.compatibleSparklineDialog.stylesetting" />
                </div>
            </fieldset>
        </div>
    </div>
    <div class="compatible-style-setting">
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.negativeColor + ':'">Negative:</label>
            <div class="compatible-style-sets compatible-negative"></div>
        </div>
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.markersColor + ':'">Markers:</label>
            <div class="compatible-style-sets compatible-markers"></div>
        </div>
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.axisColor + ':'">Axis:</label>
            <div class="compatible-style-sets compatible-axis"></div>
        </div>
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.seriesColor + ':'">Series:</label>
            <div class="compatible-style-sets compatible-series"></div>
        </div>
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.highMarkerColor + ':'">High
                Marker:</label>
            <div class="compatible-style-sets compatible-high-marker"></div>
        </div>
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.lowMarkerColor + ':'">Low
                Marker:</label>
            <div class="compatible-style-sets compatible-low-marker"></div>
        </div>
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.firstMarkerColor + ':'">First
                Marker:</label>
            <div class="compatible-style-sets compatible-first-marker"></div>
        </div>
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.lastMarkerColor + ':'">Last
                Marker:</label>
            <div class="compatible-style-sets compatible-last-marker"></div>
        </div>
        <div class="compatible-style-block">
            <label class="compatible-style-column1" data-bind="text: res.compatibleSparklineDialog.lineWeight + ':'">Line
                Weight:</label>
            <input class="compatible-line-weight" />
        </div>
    </div>
    <div class="bullet-sparkline-dialog">
        <div class="bullet-sparkline-block">
            <label class="bullet-column1" data-bind="text: res.bulletSparklineDialog.measure + ':'">Measure:</label>
            <input class="bullet-measure bullet-column2" />
            <label class="bullet-column3" data-bind="text: res.bulletSparklineDialog.target + ':'">Target:</label>
            <input class="bullet-target bullet-column4" />
        </div>
        <div class="bullet-sparkline-block">
            <label class="bullet-column1" data-bind="text:res.bulletSparklineDialog.maxi + ':'">Maxi:</label>
            <input class="bullet-maxi bullet-column2" />
            <label class="bullet-column3" data-bind="text: res.bulletSparklineDialog.forecast + ':'">Forecast:</label>
            <input class="bullet-forecast bullet-column4" />
        </div>
        <div class="bullet-sparkline-block">
            <label class="bullet-column1" data-bind="text: res.bulletSparklineDialog.good + ':'">Good:</label>
            <input class="bullet-good bullet-column2" />
            <label class="bullet-column3" data-bind="text:res.bulletSparklineDialog.bad + ':'">Bad:</label>
            <input class="bullet-bad bullet-column4" />
        </div>
        <div class="bullet-sparkline-block">
            <label class="bullet-column1" data-bind="text:res.bulletSparklineDialog.tickunit + ':'">Tickunit:</label>
            <input class="bullet-tickunit bullet-column2" />

            <label class="bullet-column3" data-bind="text: res.bulletSparklineDialog.colorScheme + ':'">ColorScheme:</label>
            <div class="bullet-color-frame sparkline-ex-colorframe bullet-column4">
                <span>
                    <span class="bullet-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="bullet-color-picker"></div>
            </div>
        </div>
        <div class="bullet-sparkline-block">
            <input type="checkbox" class="bullet-vertical" id="bullet-vertical" />
            <label for="bullet-vertical" data-bind="text:res.bulletSparklineDialog.vertical">Vertical</label>
        </div>
    </div>
    <div class="spread-sparkline-dialog">
        <div class="spread-sparkline-block">
            <label class="spread-column1" data-bind="text:res.spreadSparklineDialog.points + ':'">Points:</label>
            <input class="spread-points spread-column2" />
            <label class="spread-column3" data-bind="text: res.spreadSparklineDialog.style + ':'">Style:</label>
            <select class="spread-style spread-column4">
                <option value="1" data-bind="text: res.spreadSparklineDialog.stacked">Stacked</option>
                <option value="2" data-bind="text: res.spreadSparklineDialog.spread">Spread</option>
                <option value="3" data-bind="text: res.spreadSparklineDialog.jitter">Jitter</option>
                <option value="4" data-bind="text: res.spreadSparklineDialog.poles">Poles</option>
                <option value="5" data-bind="text: res.spreadSparklineDialog.stackedDots">StackedDots</option>
                <option value="6" data-bind="text: res.spreadSparklineDialog.stripe">Stripe</option>
            </select>
        </div>
        <div class="spread-sparkline-block">
            <label class="spread-column1" data-bind="text:res.spreadSparklineDialog.scaleStart + ':'">ScaleStart:</label>
            <input class="spread-scale-start spread-column2" />
            <label class="spread-column3" data-bind="text:res.spreadSparklineDialog.scaleEnd + ':'">ScaleEnd:</label>
            <input class="spread-scale-end spread-column4" />
        </div>
        <div class="spread-sparkline-block">
            <label class="spread-column1" data-bind="text:res.spreadSparklineDialog.colorScheme + ':'">ColorScheme:</label>
            <div class="spread-color-frame sparkline-ex-colorframe spread-column2">
                <span>
                    <span class="spread-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="spread-color-picker"></div>
            </div>
        </div>
        <div class="spread-sparkline-block">
            <input type="checkbox" id="spread-show-average" class="spread-show-average" />
            <label for="spread-show-average" data-bind="text:res.spreadSparklineDialog.showAverage">ShowAverage</label>
            <input type="checkbox" id="spread-vertical" class="spread-vertical" />
            <label for="spread-vertical" data-bind="text:res.spreadSparklineDialog.vertical">Vertical</label>
        </div>
    </div>
    <div class="stacked-sparkline-dialog">
        <div class="stacked-sparkline-block">
            <label class="stacked-column1" data-bind="text:res.stackedSparklineDialog.points + ':'">Points:</label>
            <input class="stacked-column2 stacked-points" />
            <label class="stacked-column3" data-bind="text: res.stackedSparklineDialog.colorRange + ':'">ColorRange:</label>
            <input class="stacked-column4 stacked-color-range" />
        </div>
        <div class="stacked-sparkline-block">
            <label class="stacked-column1" data-bind="text: res.stackedSparklineDialog.labelRange + ':'">LabelRange:</label>
            <input class="stacked-column2 stacked-label-range" />
            <label class="stacked-column3" data-bind="text: res.stackedSparklineDialog.maximum + ':'">Maximum:</label>
            <input class="stacked-column4 stacked-maximum" />
        </div>
        <div class="stacked-sparkline-block">
            <label class="stacked-column1" data-bind="text:res.stackedSparklineDialog.targetRed + ':'">TargetRed:</label>
            <input class="stacked-column2 stacked-target-red" />
            <label class="stacked-column3" data-bind="text:res.stackedSparklineDialog.targetGreen + ':'">TargetGreen:</label>
            <input class="stacked-column4 stacked-target-green" />
        </div>
        <div class="stacked-sparkline-block">
            <label class="stacked-column1" data-bind="text:res.stackedSparklineDialog.targetBlue + ':'">TargetBlue:</label>
            <input class="stacked-column2 stacked-target-blue" />
            <label class="stacked-column3" data-bind="text:res.stackedSparklineDialog.targetYellow + ':'">TargetYellow:</label>
            <input class="stacked-column4 stacked-target-yellow" />
        </div>
        <div class="stacked-sparkline-block">
            <label class="stacked-column1" data-bind="text:res.stackedSparklineDialog.color + ':'">Color:</label>
            <div class="stacked-color-frame sparkline-ex-colorframe stacked-column2">
                <span>
                    <span class="stacked-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="stacked-color-picker"></div>
            </div>
            <label class="stacked-column3" data-bind="text:res.stackedSparklineDialog.highlightPosition + ':'">HighlightPosition:</label>
            <input class="stacked-column4 stacked-highlight-position" />
        </div>
        <div class="stacked-sparkline-block">
            <label class="stacked-column1" data-bind="text:res.stackedSparklineDialog.textOrientation + ':'">TextOrientation:</label>
            <select class="stacked-column2 stacked-text-orientation">
                <option value="0" data-bind="text:res.stackedSparklineDialog.textHorizontal">Horizontal</option>
                <option value="1" data-bind="text:res.stackedSparklineDialog.textVertical">Vertical</option>
            </select>
            <label class="stacked-column3" data-bind="text:res.stackedSparklineDialog.textSize + ':'">TextSize:</label>
            <input class="stacked-column4 stacked-text-size" />
            <label data-bind="text:res.stackedSparklineDialog.px">px</label>
        </div>
        <div class="stacked-sparkline-block">
            <input id="stacked-vertical" class="stacked-vertical" type="checkbox" />
            <label for="stacked-vertical" data-bind="text:res.stackedSparklineDialog.vertical">Vertical</label>
        </div>
        <div class="stacked-sparkline-block"></div>
    </div>
    <div class="barbase-sparkline-dialog">
        <div class="barbase-sparkline-block">
            <label class="barbase-column1" data-bind="text: res.barbaseSparklineDialog.value + ':'">Value:</label>
            <input class="barbase-column2 barbase-value" />
        </div>
        <div class="barbase-sparkline-block">
            <label class="barbase-column1" data-bind="text: res.barbaseSparklineDialog.colorScheme + ':'">ColorScheme:</label>
            <div class="barbase-color-frame sparkline-ex-colorframe barbase-column2">
                <span>
                    <span class="barbase-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="barbase-color-picker"></div>
            </div>
        </div>
    </div>
    <div class="vari-sparkline-dialog">
        <div class="vari-sparkline-block">
            <label class="vari-column1" data-bind="text:res.variSparklineDialog.variance + ':'">Variance:</label>
            <input class="vari-column2 vari-variance" />
            <label class="vari-column3" data-bind="text:res.variSparklineDialog.reference + ':'">Reference:</label>
            <input class="vari-column4 vari-reference" />
        </div>
        <div class="vari-sparkline-block">
            <label class="vari-column1" data-bind="text:res.variSparklineDialog.mini + ':'">Mini:</label>
            <input class="vari-column2 vari-mini" />
            <label class="vari-column3" data-bind="text:res.variSparklineDialog.maxi + ':'">Maxi:</label>
            <input class="vari-column4 vari-maxi" />
        </div>
        <div class="vari-sparkline-block">
            <label class="vari-column1" data-bind="text:res.variSparklineDialog.mark + ':'">Mark:</label>
            <input class="vari-column2 vari-mark" />
            <label class="vari-column3" data-bind="text:res.variSparklineDialog.tickunit + ':'">TickUnit:</label>
            <input class="vari-column4 vari-tickunit" />
        </div>
        <div class="vari-sparkline-block">
            <label class="vari-column1" data-bind="text: res.variSparklineDialog.colorPositive + ':'">ColorPositive:</label>
            <div class="vari-positive-color-frame sparkline-ex-colorframe vari-column2">
                <span>
                    <span class="vari-positive-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="vari-positive-color-picker"></div>
            </div>
            <label class="vari-column3" data-bind="text: res.variSparklineDialog.colorNegative + ':'">ColorNegative:</label>
            <div class="vari-negative-color-frame sparkline-ex-colorframe vari-column4">
                <span>
                    <span class="vari-negative-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="vari-negative-color-picker"></div>
            </div>
        </div>
        <div class="vari-sparkline-block">
            <input type="checkbox" class="vari-legend" id="vari-legend" />
            <label data-bind="text:res.variSparklineDialog.legend" for="vari-legend">Legend</label>
            <input type="checkbox" class="vari-vertical" id="vari-vertical" />
            <label data-bind="text: res.variSparklineDialog.vertical" for="vari-vertical">Vertical</label>
        </div>
    </div>
    <div class="boxplot-sparkline-dialog">
        <div class="boxplot-sparkline-block">
            <label class="boxplot-column1" data-bind="text: res.boxplotSparklineDialog.points + ':'">Points:</label>
            <input class="boxplot-column2 boxplot-points" />
            <label class="boxplot-column3" data-bind="text: res.boxplotSparklineDialog.boxPlotClass + ':'">BoxPlotClass:</label>
            <select class="boxplot-column4 boxplot-class">
                <option data-bind="text:res.boxplotSparklineDialog.fiveNS" value="5ns">5NS</option>
                <option data-bind="text: res.boxplotSparklineDialog.sevenNS" value="7ns">7NS</option>
                <option data-bind="text: res.boxplotSparklineDialog.tukey" value="tukey">Tukey</option>
                <option data-bind="text: res.boxplotSparklineDialog.bowley" value="bowley">Bowley</option>
                <option data-bind="text: res.boxplotSparklineDialog.sigma" value="sigma3">Sigma3</option>
            </select>
        </div>
        <div class="boxplot-sparkline-block">
            <label class="boxplot-column1" data-bind="text:res.boxplotSparklineDialog.scaleStart + ':'">ScaleStart:</label>
            <input class="boxplot-column2 boxplot-scale-start" />
            <label class="boxplot-column3" data-bind="text: res.boxplotSparklineDialog.scaleEnd + ':'">ScaleEnd:</label>
            <input class="boxplot-column4 boxplot-scale-end" />
        </div>
        <div class="boxplot-sparkline-block">
            <label class="boxplot-column1" data-bind="text: res.boxplotSparklineDialog.acceptableStart + ':'">AcceptableStart:</label>
            <input class="boxplot-column2 boxplot-acceptable-start" />
            <label class="boxplot-column3" data-bind="text: res.boxplotSparklineDialog.acceptableEnd + ':'">AcceptableEnd:</label>
            <input class="boxplot-column4 boxplot-acceptable-end" />
        </div>
        <div class="boxplot-sparkline-block">
            <label class="boxplot-column1" data-bind="text: res.boxplotSparklineDialog.colorScheme + ':'">ColorScheme:</label>
            <div class="boxplot-color-frame sparkline-ex-colorframe boxplot-column2">
                <span>
                    <span class="boxplot-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="boxplot-color-picker"></div>
            </div>
            <label class="boxplot-column3" data-bind="text:res.boxplotSparklineDialog.style + ':'">Style:</label>
            <select class="boxplot-column4 boxplot-style">
                <option data-bind="text:res.boxplotSparklineDialog.classical" value=0>Classical</option>
                <option data-bind="text:res.boxplotSparklineDialog.neo" value=1>Neo</option>
            </select>
        </div>
        <div class="boxplot-sparkline-block">
            <input type="checkbox" class="boxplot-show-average" id="boxplot-show-average" />
            <label data-bind="text: res.boxplotSparklineDialog.showAverage" for="boxplot-show-average">Show Average</label>
            <input type="checkbox" class="boxplot-vertical" id="boxplot-vertical" />
            <label data-bind="text: res.boxplotSparklineDialog.vertical" for="boxplot-vertical">Vertical</label>
        </div>
    </div>
    <div class="cascade-sparkline-dialog">
        <div class="cascade-sparkline-block">
            <label class="cascade-column1" data-bind="text:res.cascadeSparklineDialog.pointsRange + ':'">PointsRange:</label>
            <input class="cascade-column2 cascade-points-range" />
            <label class="cascade-column3" data-bind="text: res.cascadeSparklineDialog.pointIndex + ':'">PointIndex:</label>
            <input class="cascade-column4 cascade-point-index" />
        </div>
        <div class="cascade-sparkline-block">
            <label class="cascade-column1" data-bind="text: res.cascadeSparklineDialog.minimum + ':'">Minimum:</label>
            <input class="cascade-column2 cascade-mini" />
            <label class="cascade-column3" data-bind="text: res.cascadeSparklineDialog.maximum + ':'">Maximum:</label>
            <input class="cascade-column4 cascade-maxi" />
        </div>
        <div class="cascade-sparkline-block">
            <label class="cascade-column1" data-bind="text: res.cascadeSparklineDialog.colorPositive + ':'">ColorPositive:</label>
            <div class="cascade-positive-color-frame sparkline-ex-colorframe cascade-column2">
                <span>
                    <span class="cascade-positive-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="cascade-positive-color-picker"></div>
            </div>
            <label class="cascade-column3" data-bind="text: res.cascadeSparklineDialog.colorNegative + ':'">ColorNegative:</label>
            <div class="cascade-negative-color-frame sparkline-ex-colorframe cascade-column4">
                <span>
                    <span class="cascade-negative-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="cascade-negative-color-picker"></div>
            </div>
        </div>
        <div class="cascade-sparkline-block">
            <label class="cascade-column1" data-bind="text: res.cascadeSparklineDialog.labelsRange + ':'">LabelsRange:</label>
            <input class="cascade-column2 cascade-labels-range" />
            <input type="checkbox" class="cascade-vertical" id="cascade-vertical" />
            <label data-bind="text: res.cascadeSparklineDialog.vertical" for="cascade-vertical">Vertical</label>
        </div>
    </div>
    <div class="pareto-sparkline-dialog">
        <div class="pareto-sparkline-block">
            <label class="pareto-column1" data-bind="text: res.paretoSparklineDialog.points + ':'">Points:</label>
            <input class="pareto-column2 pareto-points" />
            <label class="pareto-column3" data-bind="text: res.paretoSparklineDialog.pointIndex + ':'">PointIndex:</label>
            <input class="pareto-column4 pareto-point-index" />
        </div>
        <div class="pareto-sparkline-block">
            <label class="pareto-column1" data-bind="text: res.paretoSparklineDialog.colorRange + ':'">ColorRange:</label>
            <input class="pareto-column2 pareto-color-range" />
            <label class="pareto-column3" data-bind="text: res.paretoSparklineDialog.highlightPosition + ':'">HighlightPosition:</label>
            <input class="pareto-column4 pareto-highlightPosition" />
        </div>
        <div class="pareto-sparkline-block">
            <label class="pareto-column1" data-bind="text: res.paretoSparklineDialog.target + ':'">Target:</label>
            <input class="pareto-column2 pareto-target" />
            <label class="pareto-column3" data-bind="text: res.paretoSparklineDialog.target2 + ':'">Target2:</label>
            <input class="pareto-column4 pareto-target2" />
        </div>
        <div class="pareto-sparkline-block">
            <label class="pareto-column1" data-bind="text: res.paretoSparklineDialog.label + ':'">Label:</label>
            <select class="pareto-column2 pareto-label">
                <option data-bind="text: res.paretoSparklineDialog.none" value=0>None</option>
                <option data-bind="text: res.paretoSparklineDialog.single" value=2>Single</option>
                <option data-bind="text:res.paretoSparklineDialog.cumulated" value=1>Cumulate</option>
            </select>
            <input type="checkbox" class="pareto-vertical" id="pareto-vertical" />
            <label data-bind="text: res.cascadeSparklineDialog.vertical" for="pareto-vertical">Vertical</label>
        </div>
    </div>
    <div class="calendar-sparkline-dialog">
        <div class="calendar-sparkline-block">
            <label class="calendar-column1" data-bind="text: res.insertSparklineDialog.dataRange">Data Range:</label>
            <div class="calendar-column2">
                <div class="rangeSelectContainer">
                    <input class="calendar-sparkline-data" />
                    <div class="rangeSelectButton" data-name="data-range">
                        <span></span>
                    </div>
                </div>
            </div>
            <label class="calendar-column3" data-bind="text: res.calendarSparklineDialog.emptyColor">Empty Color:</label>
            <div class="calendar-emptyColor-frame sparkline-ex-colorframe calendar-column4 color-picker-container">
                <span>
                    <span class="calendar-emptyColor-span sparkline-ex-colorspan"></span>
                </span>
                <div class="calendar-emptyColor-picker"></div>
            </div>
        </div>
        <div class="calendar-sparkline-block">
            <label class="calendar-column1" data-bind="text: res.insertSparklineDialog.locationRange">Location
                Range:</label>
            <div class="calendar-column2">
                <div class="rangeSelectContainer">
                    <input class="calendar-sparkline-location" />
                    <div class="rangeSelectButton" data-name="location-range">
                        <span></span>
                    </div>
                </div>
            </div>
            <label class="calendar-column3" data-bind="text: res.calendarSparklineDialog.startColor">Start Color:</label>
            <div class="calendar-startColor-frame sparkline-ex-colorframe calendar-column4 color-picker-container">
                <span>
                    <span class="calendar-startColor-span sparkline-ex-colorspan"></span>
                </span>
                <div class="calendar-startColor-picker"></div>
            </div>
        </div>
        <div class="calendar-sparkline-block">
            <label class="calendar-column1" data-bind="text: res.calendarSparklineDialog.year">Year:</label>
            <div class="calendar-column2">
                <input class="calendar-sparkline-year" />
            </div>
            <label class="calendar-column3" data-bind="text: res.calendarSparklineDialog.middleColor">Middle Color:</label>
            <div class="calendar-middleColor-frame sparkline-ex-colorframe calendar-column4 color-picker-container">
                <span>
                    <span class="calendar-middleColor-span sparkline-ex-colorspan"></span>
                </span>
                <div class="calendar-middleColor-picker"></div>
            </div>
        </div>
        <div class="calendar-sparkline-block">
            <label class="calendar-column1" data-bind="text: res.calendarSparklineDialog.month">Month:</label>
            <div class="calendar-column2">
                <select class="calendar-sparkline-month">
                </select>
            </div>
            <label class="calendar-column3" data-bind="text: res.calendarSparklineDialog.endColor">End Color:</label>
            <div class="calendar-endColor-frame sparkline-ex-colorframe calendar-column4 color-picker-container">
                <span>
                    <span class="calendar-endColor-span sparkline-ex-colorspan"></span>
                </span>
                <div class="calendar-endColor-picker"></div>
            </div>
        </div>
        <div class="calendar-sparkline-block">
            <div class="calendar-column1"></div>
            <div class="calendar-column2"></div>
            <div class="calendar-column3">
                <input type="checkbox" class="calendar-sparkline-checkbox" />
                <label data-bind="text: res.calendarSparklineDialog.rangeColor">Range Color</label>
            </div>
            <div class="rangeSelectContainer calendar-column4 range-color-container">
                <input class="calendar-sparkline-color" />
                <div class="rangeSelectButton color-range-button" data-name="color-range">
                    <span></span>
                </div>
            </div>
        </div>
    </div>
    <div class="barcode-dialog">
        <div class="barcode-common-block">
            <label class="barcode-column1" data-bind="text: res.barcodeDialog.locationReference">Location Reference:</label>
            <div class="rangeSelectContainer">
                <input class="barcode-locationReference" />
                <div class="rangeSelectButton" data-name="location-range">
                    <span></span>
                </div>
            </div>
        </div>
        <div class="barcode-common-block">
            <label class="barcode-column1" data-bind="text: res.barcodeDialog.value">value:</label>
            <div class='barcode-column2'>
                <input class='barcode-value' />
            </div>
            <label class="barcode-column3" data-bind="text: res.barcodeDialog.barcodetype">Barcode Type:</label>
            <div class="barcode-column4">
                <select class="barcode-type">
                    <option value='QRCODE'>QRCode</option>
                    <option value='EAN13'>EAN-13</option>
                    <option value='EAN8'>EAN-8</option>
                    <option value='CODABAR'>Codabar</option>
                    <option value='CODE39'>Code39</option>
                    <option value='CODE93'>Code93</option>
                    <option value='CODE128'>CODE128</option>
                    <option value='GS1_128'>GS1_128</option>
                    <option value='CODE49'>CODE49</option>
                    <option value='PDF417'>PDF417</option>
                    <option value='DATAMATRIX'>DataMatrix</option>
                </select>
            </div>
        </div>
        <div class="barcode-common-block">
            <label class="barcode-column1" data-bind="text: res.barcodeDialog.color">Color:</label>
            <div class="barcode-color-frame sparkline-ex-colorframe barcode-column2 color-picker-container">
                <span>
                    <span class="barcode-color-span sparkline-ex-colorspan"></span>
                </span>
                <div class="barcode-color-picker"></div>
            </div>
            <label class="barcode-column3" data-bind="text: res.barcodeDialog.backgroudColor">Background Color:</label>
            <div class="barcode-backgroundcolor-frame sparkline-ex-colorframe barcode-column4 color-picker-container">
                <span>
                    <span class="barcode-backgroundcolor-span sparkline-ex-colorspan"></span>
                </span>
                <div class="barcode-backgroundcolor-picker"></div>
            </div>
        </div>
        <div class="barcode-common-block">
            <label class="barcode-column1" data-bind="text: res.barcodeDialog.quietZoneLeft">quietZoneLeft:</label>
            <div class='barcode-column2'>
                <input class='barcode-quietZoneLeft' />
            </div>
            <label class="barcode-column3" data-bind="text: res.barcodeDialog.quietZoneRight">quietZoneRight:</label>
            <div class='barcode-column4'>
                <input class='barcode-quietZoneRight' />
            </div>
        </div>
        <div class="barcode-common-block">
            <label class="barcode-column1" data-bind="text: res.barcodeDialog.quietZoneTop">quietZoneTop:</label>
            <div class='barcode-column2'>
                <input class='barcode-quietZoneTop' />
            </div>
            <label class="barcode-column3" data-bind="text: res.barcodeDialog.quietZoneBottom">quietZoneBottom:</label>
            <div class='barcode-column4'>
                <input class='barcode-quietZoneBottom' />
            </div>
        </div>
        <hr />
        <div class='barcode-content'></div>
    </div>

    <div class="create-table-dialog">
        <div class="setting-block">
            <label data-bind="text: res.createTableDialog.whereYourTable">Where is the data for your table?</label>
        </div>
        <div class="setting-block">
            <div class="rangeSelectContainer">
                <input type="text" class="table-source-range" />
                <div class="rangeSelectButton" data-name="table-source-range">
                    <span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="format-table-dialog">
        <div class="name-block">
            <label for="table-style-id" data-bind="text: res.formatTableStyle.name">Name:</label>
            <input type="text" class="table-style-input" id="table-style-id" />
        </div>
        <div class="table-element-block">
            <fieldset class="table-element-fieldset">
                <legend data-bind="text: res.formatTableStyle.tableElement">Table Element:</legend>
                <select class="table-element-select"></select>
                <button class="format-table-element">
                    <span data-bind="text: res.formatTableStyle.format">Format</span>
                </button>
                <button class="clear-table-element">
                    <span data-bind="text: res.formatTableStyle.clear">Clear</span>
                </button>
            </fieldset>
        </div>
        <div class="table-preview-block">
            <fieldset class="table-preview-fieldset">
                <legend data-bind="text: res.formatTableStyle.preview">Preview</legend>
                <div class="pre-spread"></div>
            </fieldset>
        </div>
        <div class="stripe-size-block">
            <fieldset class="stripe-size-fieldset">
                <legend data-bind="text: res.formatTableStyle.stripeSize"></legend>
                <select class="stripe-size-select"></select>
            </fieldset>
        </div>
    </div>

    <div class="resize-table-dialog">
        <div class="setting-block">
            <label data-bind="text: res.resizeTableDialog.dataRangeTitle">Select the new data range for your table:</label>
        </div>
        <div>
            <div class="rangeSelectContainer">
                <input type="text" class="table-source-range" />
                <div class="rangeSelectButton" data-name="table-source-range">
                    <span></span>
                </div>
            </div>
        </div>
        <div>
            <label data-bind="text: res.resizeTableDialog.note">Note: The headers must remain in the same row,<br />
                and the resulting table range must overlap<br />
                the original table range.
            </label>
        </div>
    </div>

    <div class="cell-style-dialog">
        <div class="name-setting">
            <label for="cell-style-name" data-bind="text: res.newCellStyleDialog.styleName">Style Name:</label>
            <input id="cell-style-name" class="cell-style-name" type="text" />
        </div>
        <button class="cell-style-format-setting" data-bind="text:res.newCellStyleDialog.format">Format...</button>
    </div>

    <div class="pdf-printer-setting-dialog">
        <div class="pdf-printer-setting">
            <label data-bind="text: res.fileMenu.copiesLabel" class="printer-settings-label">Copies:</label>
            <select class="pdf-printer-input copies-number">
                <option selected="selected">1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
            </select>
        </div>
        <div class="pdf-printer-setting">
            <label data-bind="text: res.fileMenu.scalingTypeLabel" class="printer-settings-label">Scaling Type:</label>
            <select class="pdf-printer-input scaling-type"></select>
        </div>
        <div class="pdf-printer-setting">
            <label data-bind="text: res.fileMenu.duplexModeLabel" class="printer-settings-label">Duplex Mode:</label>
            <select class="pdf-printer-input duplex-mode"></select>
        </div>
        <div class="pdf-printer-setting">
            <input type="checkbox" class="papersource-by-pagesize" />
            <label data-bind="text: res.fileMenu.choosePaperSource">Choose paper source by pdf page size</label>
        </div>
        <fieldset class="printer-range-setting">
            <legend data-bind="text: res.fileMenu.printRanges">Print ranges</legend>
            <label data-bind="text: res.fileMenu.indexLabel" class="printer-range-label">Index:</label>
            <input type="text" class="page-range-index" />
            <label data-bind="text: res.fileMenu.countLabel" class="printer-range-label">Count:</label>
            <input type="text" class="page-range-count" />
        </fieldset>
        <button class="add-print-range printer-range-setting-button">
            <span data-bind="text:res.fileMenu.addRange">Add</span>
        </button>
        <button class="remove-print-range printer-range-setting-button">
            <span data-bind="text: res.fileMenu.removeRange">Remove</span>
        </button>
        <div class="printer-range-panel">
            <table class="printer-range-table">
                <tr>
                    <td data-bind="text: res.fileMenu.indexLabel" class="printer-range-table-header">Index</td>
                    <td data-bind="text: res.fileMenu.countLabel" class="printer-range-table-header">Count</td>
                </tr>
            </table>
        </div>

    </div>

    <div class="format-comment">
        <div class="main-comment-tab">
            <ul class="comment-tab-container">
                <li><a href="#comment-font" data-bind="text:res.formatDialog.font">Font</a></li>
                <li><a href="#comment-alignment" data-bind="text:res.formatDialog.alignment">Alignment</a></li>
                <li style="width:100px"><a href="#comment-colors" data-bind="text:res.formatComment.colors">Colors and
                        Lines</a></li>
                <li><a href="#comment-size" data-bind="text:res.formatComment.size">Size</a></li>
                <li><a href="#comment-protection" data-bind="text: res.formatComment.protection">Protection</a></li>
                <li><a href="#comment-properties" data-bind="text: res.formatComment.properties">Properties</a></li>
            </ul>

            <div id="comment-font" class="comment-tab-page">
                <div class="comment-font-picker"></div>
            </div>
            <div id="comment-alignment" class="comment-tab-page alignment-container">
                <div>
                    <fieldset class="comment-separator-line">
                        <legend data-bind="text: res.formatDialog.textAlignment">Text alignment</legend>

                        <div class="comment-text-alignment">
                            <label class="align-column" data-bind="text: res.formatDialog.horizontalAlignment">Horizontal:</label>
                            <select class="alignment-select hAlign-select">
                                <option data-bind="text: res.formatDialog.left" value="left">Left</option>
                                <option data-bind="text: res.formatDialog.center" value="center">Center</option>
                                <option data-bind="text: res.formatDialog.right" value="right">Right</option>
                            </select>
                        </div>
                    </fieldset>
                    <div>
                        <input type="checkbox" id="auto-size" />
                        <label for="auto-size" data-bind="text: res.formatComment.autoSize">Automatic size</label>
                    </div>
                </div>

                <div>
                    <fieldset class="comment-separator-line">
                        <legend data-bind="text: res.formatComment.internalMargin">Internal margin</legend>
                        <div>
                            <input type="checkbox" id="automatic-padding" />
                            <label for="automatic-padding" data-bind="text: res.formatComment.automatic">Automatic</label>
                        </div>
                        <div>
                            <div class="padding-group">
                                <label class="padding-column1" for="padding-left" data-bind="text: res.formatDialog.left + ':'">Left:</label>
                                <input id="padding-left" class="padding-box" />
                                <label class="padding-column2" for="padding-top" data-bind="text: res.formatDialog.top + ':'">Top:</label>
                                <input id="padding-top" class="padding-box" />
                            </div>
                            <div class="padding-group">
                                <label class="padding-column1" for="padding-right" data-bind="text: res.formatDialog.right + ':'">Right:</label>
                                <input id="padding-right" class="padding-box" />
                                <label class="padding-column2" for="padding-bottom" data-bind="text: res.formatDialog.bottom + ':'">Bottom:</label>
                                <input id="padding-bottom" class="padding-box" />
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div id="comment-colors" class="comment-tab-page colors-container">
                <fieldset class="comment-separator-line">
                    <legend data-bind="text:res.formatComment.fill">Fill</legend>
                    <div>
                        <label class="color-label" data-bind="text:res.formatComment.color + ':'"></label>
                        <div class="fill-color-picker"></div>
                    </div>
                    <div>
                        <label class="slider-label" data-bind="text: res.formatComment.transparency + ':'">Transparency</label>
                        <div class="color-transparent-slider"></div>
                        <input id="colors-transparent-input" />
                        <label>%</label>
                    </div>
                </fieldset>
                <fieldset class="comment-separator-line">
                    <legend data-bind="text:res.formatComment.line">Line</legend>
                    <div>
                        <label class="lines-column" data-bind="text: res.formatComment.color + ':'">Color:</label>
                        <div class="line-color-picker"></div>
                    </div>
                    <div>
                        <label class="lines-column" data-bind="text: res.formatComment.style + ':'">Style:</label>
                        <select class="lines-style-select">
                            <option data-bind="text: res.formatComment.none" value="none">None</option>
                            <option data-bind="text: res.formatComment.dotted" value="dotted">Dotted</option>
                            <option data-bind="text: res.formatComment.dashed" value="dashed">Dashed</option>
                            <option data-bind="text: res.formatComment.solid" value="solid">Solid</option>
                            <option data-bind="text: res.formatComment.double" value="double">Double</option>
                            <option data-bind="text: res.formatComment.groove" value="groove">Groove</option>
                            <option data-bind="text: res.formatComment.ridge" value="ridge">Ridge</option>
                            <option data-bind="text: res.formatComment.inset" value="inset">Inset</option>
                            <option data-bind="text: res.formatComment.outset" value="outset">Outset</option>
                        </select>
                    </div>
                    <div>
                        <label class="lines-column" data-bind="text: res.formatComment.width + ':'">Width:</label>
                        <input class="lines-width" />
                    </div>
                </fieldset>
            </div>
            <div id="comment-size" class="comment-tab-page size-container">
                <fieldset class="comment-separator-line">
                    <legend data-bind="text:res.formatComment.size"></legend>
                    <div class="size-div">
                        <label class="size-column" for="comment-size-height" data-bind="text:res.formatComment.height + ':'">Height:</label>
                        <input id="comment-size-height" />
                    </div>
                    <div class="size-div">
                        <label class="size-column" for="comment-size-width" data-bind="text:res.formatComment.width + ':'">Width:</label>
                        <input id="comment-size-width" />
                    </div>
                </fieldset>
            </div>
            <div id="comment-protection" class="comment-tab-page protection-container">
                <div>
                    <input type="checkbox" id="comment-locked" />
                    <label for="comment-locked" data-bind="text: res.formatComment.commentLocked">Locked</label>
                </div>
                <div>
                    <input type="checkbox" id="comment-lock-text" />
                    <label for="comment-lock-text" data-bind="text: res.formatComment.commentLockText">Lock text</label>
                </div>
                <div class="protect-comment-div">
                    <span data-bind="text: res.formatComment.commentLockComments"></span>
                </div>
            </div>
            <div id="comment-properties" class="comment-tab-page">
                <div>
                    <fieldset class="comment-separator-line">
                        <legend data-bind="text: res.formatComment.positioning">Object positioning</legend>
                        <div>
                            <input type="radio" name="dynamic-ratio" id="move-size" />
                            <label for="move-size" data-bind="text: res.formatComment.moveSize"></label>
                        </div>
                        <div>
                            <input type="radio" name="dynamic-ratio" id="move-no-size" />
                            <label for="move-no-size" data-bind="text: res.formatComment.moveNoSize"></label>
                        </div>
                        <div>
                            <input type="radio" name="dynamic-ratio" id="no-move-size" />
                            <label for="no-move-size" data-bind="text: res.formatComment.noMoveSize"></label>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <div class="protection-option">
        <div class="protection-option-label" data-bind="text: res.protectionOptionDialog.label">Allow all users of this
            worksheet to:
        </div>
        <div class="protection-option-container">
        </div>
    </div>

    <div class="insert-slicer-dialog">
        <div class="insert-slicer-container">
        </div>
    </div>
    <div class="slicer-setting-dialog">
        <div id="slicer-setting-name" class="slicer-name-margin">
            <div>
                <label data-bind="text: res.slicerSettingDialog.sourceName">Source Name:</label>
                <label id="source-name" class="textBold">Column</label>
            </div>
            <div>
                <label data-bind="text: res.slicerSettingDialog.name">Name:</label>
                <input type="text" size="29" maxlength="255" id="slicer-name" />
            </div>
        </div>
        <div>
            <fieldset class="slicer-fieldset">
                <legend data-bind="text: res.slicerSettingDialog.header">Header</legend>
                <div>
                    <input type="checkbox" id="display-header" />
                    <label for="display-header" data-bind="text: res.slicerSettingDialog.display">Display header</label>
                </div>
                <div>
                    <label data-bind="text: res.slicerSettingDialog.caption" class="caption-margin">Caption:</label>
                    <input type="text" size="29" maxlength="255" id="slicer-caption" />
                </div>
            </fieldset>
        </div>
        <div>
            <fieldset class="slicer-fieldset">
                <legend data-bind="text: res.slicerSettingDialog.items">Item Sorting and Filtering</legend>
                <div class="item-sorting">
                    <div>
                        <input type="radio" name="item-sort" value="ascending" id="ascending" />
                        <label for="ascending" data-bind="text: res.slicerSettingDialog.ascending">Ascending(A to Z)</label>
                    </div>
                    <div>
                        <input type="radio" name="item-sort" value="descending" id="descending" />
                        <label for="descending" data-bind="text: res.slicerSettingDialog.descending">Descending(Z to
                            A)</label>
                    </div>
                </div>
                <div class="item-show-hide">
                    <div>
                        <input type="checkbox" id="hide-item" class="hide-item-margin" />
                        <label for="hide-item" data-bind="text: res.slicerSettingDialog.hideItem">Hide item with no
                            data</label>
                    </div>
                    <div>
                        <input type="checkbox" id="visually-item" class="visually-item-margin" />
                        <label for="visually-item" data-bind="text: res.slicerSettingDialog.visuallyItem">Visually
                            indicate
                            items with no data</label>
                    </div>
                    <div>
                        <input type="checkbox" id="show-item" class="show-item-margin" />
                        <label for="show-item" data-bind="text: res.slicerSettingDialog.showItem">Show items with no
                            data
                            last</label>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="slicer-style-dialog">
        <div class="name-block">
            <label for="slicer-style-id" data-bind="text: res.formatSlicerStyle.name">Name:</label>
            <input type="text" class="slicer-style-input" id="slicer-style-id" />
        </div>
        <div class="slicer-element-block">
            <fieldset class="slicer-element-fieldset">
                <legend data-bind="text: res.formatSlicerStyle.slicerElement">Table Element:</legend>
                <select class="slicer-element-select"></select>
                <button class="format-slicer-element">
                    <span data-bind="text: res.formatSlicerStyle.format">Format</span>
                </button>
                <button class="clear-slicer-element">
                    <span data-bind="text: res.formatSlicerStyle.clear">Clear</span>
                </button>
            </fieldset>
        </div>
        <div class="slicer-preview-block">
            <fieldset class="slicer-preview-fieldset">
                <legend data-bind="text: res.formatSlicerStyle.preview">Preview</legend>
                <div class="slicer-pre-style">
                    <div id="slicer-pre-header">
                        <span class="slicer-pre-left-style"></span>
                        <span class="slicer-pre-right-style"></span>
                    </div>
                    <div>
                        <span id="selected-item-data" class="slicer-pre-left-style"></span>
                        <span id="hovered-selected-item-data" class="slicer-pre-right-style"></span>
                    </div>
                    <div>
                        <span id="unselected-item-data" class="slicer-pre-left-style"></span>
                        <span id="hovered-unselected-item-data" class="slicer-pre-right-style"></span>
                    </div>
                    <div>
                        <span id="selected-item-no-data" class="slicer-pre-left-style"></span>
                        <span id="hovered-selected-item-no-data" class="slicer-pre-right-style"></span>
                    </div>
                    <div>
                        <span id="unselected-item-no-data" class="slicer-pre-left-style"></span>
                        <span id="hovered-unselected-item-no-data" class="slicer-pre-right-style"></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="format-slicer-dialog">
        <div class="slicer-property-tab">
            <ul>
                <li id="position-layout"><a href="#slicer-property-position" data-bind="text: res.slicerPropertyDialog.position">POSITION
                        AND LAYOUT</a></li>
                <li id="size"><a href="#slicer-property-size" data-bind="text: res.slicerPropertyDialog.size">SIZE</a></li>
                <li id="properties"><a href="#slicer-property-properties" data-bind="text: res.slicerPropertyDialog.properties">PROPERTIES</a></li>
            </ul>
            <div id="slicer-property-position">
                <div class="general-selection">
                    <fieldset>
                        <legend data-bind="text: res.slicerPropertyDialog.pos">Position</legend>
                        <div class="setting-block">
                            <div>
                                <label for="slicer-position-horizontal" data-bind="text: res.slicerPropertyDialog.horizontal">Horizontal:</label>
                            </div>
                            <input type="text" id="slicer-position-horizontal" name="slicer-horizontal" size="10" />
                        </div>
                        <div class="setting-block">
                            <div>
                                <label for="slicer-position-vertial" data-bind="text: res.slicerPropertyDialog.vertial">Vertial:</label>
                            </div>
                            <input type="text" id="slicer-position-vertial" name="slicer-vertial" size="10" />
                        </div>
                        <div class="setting-block">
                            <input type="checkbox" id="slicer-disable-resize" name="disable-resize" class="diasble-resizing-moving" />
                            <label for="slicer-disable-resize" data-bind="text: res.slicerPropertyDialog.disableResizingMoving">Disable
                                resizing and
                                moving</label>
                        </div>
                    </fieldset>
                </div>
                <div class="general-selection">
                    <fieldset>
                        <legend data-bind="text: res.slicerPropertyDialog.layout">Layout</legend>
                        <div class="setting-block">
                            <div>
                                <label for="slicer-layout-column-count" data-bind="text: res.slicerPropertyDialog.numberColumn">Number
                                    of columns:</label>
                            </div>
                            <input type="text" id="slicer-layout-column-count" name="slicer-column-count" size="10" />
                        </div>
                        <div class="setting-block">
                            <div>
                                <label for="slicer-layout-item-height" data-bind="text: res.slicerPropertyDialog.buttonHeight">Button
                                    Height:</label>
                            </div>
                            <input type="text" id="slicer-layout-item-height" name="slicer-item-height" size="10" />
                        </div>
                        <div class="setting-block">
                            <div>
                                <label for="slicer-layout-item-width" data-bind="text: res.slicerPropertyDialog.buttonWidth">Button
                                    Width:</label>
                            </div>
                            <input type="text" id="slicer-layout-item-width" name="slicer-item-width" size="10" />
                        </div>
                    </fieldset>
                </div>
            </div>
            <div id="slicer-property-size">
                <div class="setting-block">
                    <div>
                        <label for="slicer-size-height" data-bind="text: res.slicerPropertyDialog.height">Height</label>
                    </div>
                    <input type="text" id="slicer-size-height" name="slicer-height" size="10" />
                </div>
                <div class="setting-block">
                    <div>
                        <label for="slicer-size-width" data-bind="text: res.slicerPropertyDialog.width">Width</label>
                    </div>
                    <input type="text" id="slicer-size-width" name="slicer-width" size="10" />
                </div>
                <div class="setting-block">
                    <div>
                        <label for="slicer-size-scale-height" data-bind="text: res.slicerPropertyDialog.scaleHeight">Scale
                            Height</label>
                    </div>
                    <input type="text" id="slicer-size-scale-height" name="slicer-scale-height" size="10" class="percent-input" />
                    <label class="percent-label">%</label>
                </div>
                <div class="setting-block">
                    <div>
                        <label for="slicer-size-scale-width" data-bind="text: res.slicerPropertyDialog.scaleWidth">Scale
                            Width</label>
                    </div>
                    <input type="text" id="slicer-size-scale-width" name="slicer-scale-width" size="10" class="percent-input" />
                    <label class="percent-label">%</label>
                </div>
            </div>
            <div id="slicer-property-properties">
                <div class="setting-block">
                    <input type="radio" id="move-size" name="move-size-cell" />
                    <label for="move-size" data-bind="text: res.slicerPropertyDialog.moveSize">Move and size with
                        cells</label>
                </div>
                <div class="setting-block">
                    <input type="radio" id="move-no-size" name="move-size-cell" />
                    <label for="move-no-size" data-bind="text: res.slicerPropertyDialog.moveNoSize">Move and don't size
                        with
                        cells</label>
                </div>
                <div class="setting-block">
                    <input type="radio" id="no-move-size" name="move-size-cell" />
                    <label for="no-move-size" data-bind="text: res.slicerPropertyDialog.noMoveSize">Don't move and size
                        with
                        cells</label>
                </div>
                <div class="setting-block">
                </div>
                <div class="setting-block">
                    <input type="checkbox" id="slicer-locked" name="lock-slicer" class="locked-slicer" />
                    <label for="slicer-locked" data-bind="text: res.slicerPropertyDialog.locked">Locked</label>
                </div>
            </div>
        </div>
    </div>

