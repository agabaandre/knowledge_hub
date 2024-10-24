
    <div class="fileMenu">
        <div class="menu-panel">
            <ul>
                <li id="file-menu-goback" class="nohover-effect">
                    <div class="goback-image" /></li>
                <li><a href="#new-page" data-bind="text: res.fileMenu.new">New</a></li>
                <li><a href="#save-page">Save</a></li>
                <li><a href="#import-page" data-bind="text: res.fileMenu.import">Import</a></li>
                <li><a href="#export-page" data-bind="text: res.fileMenu.export">Export</a></li>
            </ul>
        </div>

        <div class="content-panel">
            <div class="menu-title">
                <span></span>
            </div>
            <div class="content-view">
                <div id="new-page" class="content-item">
                </div>
                <!--save ppage-->
                <div id="save-page" class="content-item">
                    <div class="left-view">
                        <input class="custom-input" type="text" name="filename" placeholder="Enter filename">
                        <button class="custom-btn btn-save" onclick="save()">Save File</button>
                    </div>
                </div>
                <!--import page -->
                <div id="import-page" class="content-item">
                    <div class="left-view">
                        <ul>
                            <li><a href="#import-json-page" data-bind="text: res.fileMenu.spreadSheetJsonFile">SpreadSheet File (JSON)</a></li>
                            <li><a href="#import-excel-page" data-bind="text: res.fileMenu.excelFile">Excel File</a></li>
                            <li><a href="#import-csv-page" data-bind="text: res.fileMenu.csvFile">CSV File</a></li>
                        </ul>
                    </div>
                    <div class="right-view">
                        <div id="import-json-page">
                            <div class="submenu-title" data-bind="text: res.fileMenu.spreadSheetJsonFile">SpreadSheet File (JSON)</div>
                            <div class="iconbutton OFL"  id="import-json" data-action="import-json">
                                <div class="iconbutton-big-image ssjson-icon"/>
                                <span data-bind="text: res.fileMenu.importSpreadSheetFile">Import SpreadSheet File</span>
                            </div>
                        </div>
                        <div id="import-excel-page">
                            <div class="submenu-title" data-bind="text: res.fileMenu.excelFile">Excel File</div>
                            <div class="sub-title" data-bind="text: res.fileMenu.openFlags">Open Flags</div>
                            <ul>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="import_excel_ignoreStyle" />
                                    <label for="import_excel_ignoreStyle" data-bind="text: res.fileMenu.importIgnoreStyle">Do not import style</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="import_excel_ignoreFormula" />
                                    <label for="import_excel_ignoreFormula" data-bind="text: res.fileMenu.importIgnoreFormula">Do not import formula</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="import_excel_doNotRecalculateAfterLoad" />
                                    <label for="import_excel_doNotRecalculateAfterLoad" data-bind="text: res.fileMenu.importDoNotRecalculateAfterLoad">Do not auto-calculate formulas after importing</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="import_excel_rowAndColumnHeaders" />
                                    <label for="import_excel_rowAndColumnHeaders" data-bind="text: res.fileMenu.importRowAndColumnHeaders">Import both frozen columns and rows as headers</label>
                                </li>
                                <li class="nohover-effect">
                                    <ul>
                                        <li class="nohover-effect">
                                            <input type="checkbox" id="import_excel_columnHeaders" />
                                            <label for="import_excel_columnHeaders" data-bind="text: res.fileMenu.importRowHeaders">Import frozen rows as column headers</label>
                                        </li>
                                        <li class="nohover-effect">
                                            <input type="checkbox" id="import_excel_rowHeaders" />
                                            <label for="import_excel_rowHeaders" data-bind="text: res.fileMenu.importColumnHeaders">Import frozen columns as row headers</label>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <div class="sub-title" data-bind="text: res.fileMenu.importPassword">Password</div>
                            <input type="password" id="import_excel_password" />
                            <div class="iconbutton OFL" id="import-excel" data-action="importExcel">
                                <div class="iconbutton-big-image xlsx-icon"/>
                                <span data-bind="text: res.fileMenu.importExcelFile">Import Excel File</span>
                            </div>
                        </div>
                        <div id="import-csv-page">
                            <div class="submenu-title" data-bind="text: res.fileMenu.csvFile">CSV File</div>
                            <!--hidden csv flag-->
                            <div class="sub-title hidden" data-bind="text: res.fileMenu.openFlags">Open Flags</div>
                            <ul class="hidden">
                                <li class="nohover-effect">
                                    <input type="checkbox" id="import_csv_includeRowHeader" />
                                    <label for="import_csv_includeRowHeader" data-bind="text: res.fileMenu.importIncludeRowHeader">Import row header</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="import_csv_includeColumnHeader" />
                                    <label for="import_csv_includeColumnHeader" data-bind="text: res.fileMenu.importIncludeColumnHeader">Import column header</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="import_csv_unformatted" />
                                    <label for="import_csv_unformatted" data-bind="text: res.fileMenu.importUnformatted">Leave the data unformatted</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="import_csv_importFormula" />
                                    <label for="import_csv_importFormula" data-bind="text: res.fileMenu.importImportFormula">Import cell formulas</label>
                                </li>
                            </ul>
                            <div class="sub-title" data-bind="text: res.fileMenu.importRowDelimiter">Row Delimiter</div>
                            <input type="text" id="import_csv_rowDelimiter" value="\r\n"/>
                            <div class="sub-title" data-bind="text: res.fileMenu.importColumnDelimiter">Column Delimiter</div>
                            <input type="text" id="import_csv_columnDelimiter" value=","/>

                            <div class="sub-title hidden" data-bind="text: res.fileMenu.importCellDelimiter">Cell Delimiter</div>
                            <input type="text" class="hidden" id="import_csv_cellDelimiter" value='"'/>

                            <div class="sub-title" data-bind="text: res.fileMenu.importEncoding">File Encoding</div>
                            <input type="text" id="import_csv_encoding" value="UTF8"/>

                            <div class="iconbutton OFL" id="import-csv" data-action="importCsv">
                                <div class="iconbutton-big-image csv-icon"/>
                                <span data-bind="text: res.fileMenu.importCsvFile">Import CSV File</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="export-page" class="content-item">
                    <div class="left-view">
                        <ul>
                            <li><a href="#export-json-page" data-bind="text: res.fileMenu.spreadSheetJsonFile">SpreadSheet File (JSON)</a></li>
                            <li><a href="#export-excel-page" data-bind="text: res.fileMenu.excelFile">Excel File</a></li>
                            <li><a href="#export-csv-page" data-bind="text: res.fileMenu.csvFile">CSV File</a></li>
                            <li><a href="#export-pdf-page" data-bind="text: res.fileMenu.pdfFile">PDF File</a></li>
                        </ul>
                    </div>
                    <div class="right-view">
                        <div id="export-json-page">
                            <div class="submenu-title" data-bind="text: res.fileMenu.spreadSheetJsonFile">SpreadSheet File (JSON)</div>
                            <div class="iconbutton OFL" id="export-json" data-action="exportJson">
                                <div class="iconbutton-big-image ssjson-icon"/>
                                <span data-bind="text: res.fileMenu.exportSpreadSheetFile">Export SpreadSheet File</span>
                            </div>
                            <div class="iconbutton OFL" id="export-js" data-action="exportJS">
                                <div class="iconbutton-big-image ssjson-icon"/>
                                <span data-bind="text: res.fileMenu.exportJSFile">Export Javascript File</span>
                            </div>
                        </div>
                        <div id="export-excel-page">
                            <div class="submenu-title" data-bind="text: res.fileMenu.excelFile">Excel File</div>
                            <div class="sub-title" data-bind="text: res.fileMenu.saveFlags">Save Flags</div>
                            <ul>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="export_excel_ignoreStyle" />
                                    <label for="export_excel_ignoreStyle" data-bind="text: res.fileMenu.exportIgnoreStyle">Do not export Style</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="export_excel_ignoreFormulas" />
                                    <label for="export_excel_ignoreFormulas" data-bind="text: res.fileMenu.exportIgnoreFormulas">Do not export formulas</label>
                                </li>
                                <!--<li class="nohover-effect">-->
                                    <!--<input type="checkbox" id="export_excel_autoRowHeight" />-->
                                    <!--<label for="export_excel_autoRowHeight" data-bind="text: res.fileMenu.exportAutoRowHeight">Autofit row height</label>-->
                                <!--</li>-->
                                <!--<li class="nohover-effect">-->
                                    <!--<input type="checkbox" id="export_excel_saveAsFiltered" checked="checked"/>-->
                                    <!--<label for="export_excel_saveAsFiltered" data-bind="text: res.fileMenu.exportSaveAsFiltered">Export as filtered</label>-->
                                <!--</li>-->
                                <!--<li class="nohover-effect">-->
                                    <!--<input type="checkbox" id="export_excel_saveAsViewed" />-->
                                    <!--<label for="export_excel_saveAsViewed" data-bind="text: res.fileMenu.exportSaveAsViewed">Export as viewed</label>-->
                                <!--</li>-->
                                <li class="nohover-effect">
                                    <input type="checkbox" id="export_excel_saveBothCustomRowAndColumnHeaders" />
                                    <label for="export_excel_saveBothCustomRowAndColumnHeaders" data-bind="text: res.fileMenu.exportSaveBothCustomRowAndColumnHeaders">Export row header as Excel frozen columns and column header as Excel frozen rows</label>
                                </li>
                                <li class="nohover-effect">
                                    <ul>
                                        <li class="nohover-effect">
                                            <input type="checkbox" id="export_excel_saveCustomRowHeaders" />
                                            <label for="export_excel_saveCustomRowHeaders" data-bind="text: res.fileMenu.exportSaveCustomRowHeaders">Export row header as Excel frozen columns</label>
                                        </li>
                                        <li class="nohover-effect">
                                            <input type="checkbox" id="export_excel_saveCustomColumnHeaders" />
                                            <label for="export_excel_saveCustomColumnHeaders" data-bind="text: res.fileMenu.exportSaveCustomColumnHeaders">Export column header as Excel frozen rows</label>
                                        </li>
                                    </ul>
                                </li>
                            </ul>

                            <div class="sub-title" data-bind="text: res.fileMenu.exportPassword">Password</div>
                            <input type="password" id="export_excel_password" />
                            
                            <div>
                                <div class="iconbutton horizontal OFL" id="export-excel" data-action="exportExcel">
                                    <div class="iconbutton-big-image xlsx-icon" />
                                    <span data-bind="text: res.fileMenu.exportExcelFile">Export Excel File</span>
                                </div>
                            </div>
                        </div>
                        <div id="export-csv-page">
                            <div class="submenu-title" data-bind="text: res.fileMenu.csvFile">CSV File</div>
                            <!--hidden csv flag-->
                            <div class="sub-title hidden" data-bind="text: res.fileMenu.saveFlags">Save Flags</div>
                            <ul class="hidden">
                                <li class="nohover-effect">
                                    <input type="checkbox" id="export_csv_includeRowHeader" />
                                    <label for="export_csv_includeRowHeader" data-bind="text: res.fileMenu.exportIncludeRowHeader">Include row headers</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="export_csv_includeColumnHeader" />
                                    <label for="export_csv_includeColumnHeader" data-bind="text: res.fileMenu.exportIncludeColumnHeader">Include column headers</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="export_csv_unFormatted" />
                                    <label for="export_csv_unFormatted" data-bind="text: res.fileMenu.exportUnFormatted">Do not include any style information</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="export_csv_exportFormula" />
                                    <label for="export_csv_exportFormula" data-bind="text: res.fileMenu.exportFormula">Include formulas</label>
                                </li>
                                <li class="nohover-effect">
                                    <input type="checkbox" id="export_csv_asViewed" />
                                    <label for="export_csv_asViewed" data-bind="text: res.fileMenu.exportVisibleRowCol">Only include visible rows and columns</label>
                                </li>
                            </ul>
                            <div class="sub-title" data-bind="text: res.fileMenu.exportSheetIndex">Sheet Index</div>
                            <input type="text" id="export_csv_sheetIndex" value="0"/>
                            <div class="sub-title" data-bind="text: res.fileMenu.exportEncoding">Encoding</div>
                            <input type="text" id="export_csv_encoding" value="UTF8"/>
                            <div class="sub-title" data-bind="text: res.fileMenu.exportRow">Row</div>
                            <input type="text" id="export_csv_row" />
                            <div class="sub-title" data-bind="text: res.fileMenu.exportColumn">Column</div>
                            <input type="text" id="export_csv_column" />
                            <div class="sub-title" data-bind="text: res.fileMenu.exportRowCount">Row Count</div>
                            <input type="text" id="export_csv_rowCount" />
                            <div class="sub-title" data-bind="text: res.fileMenu.exportColumnCount">Column Count</div>
                            <input type="text" id="export_csv_columnCount" />
                            <div class="sub-title" data-bind="text: res.fileMenu.exportRowDelimiter">Row Delimiter</div>
                            <input type="text" id="export_csv_rowDelimiter" value="\r\n"/>
                            <div class="sub-title" data-bind="text: res.fileMenu.exportColumnDelimiter">Column Delimiter</div>
                            <input type="text" id="export_csv_columnDelimiter" value=","/>

                            <div class="sub-title hidden" data-bind="text: res.fileMenu.exportCellDelimiter">Cell Delimiter</div>
                            <input type="text" class="hidden" id="export_csv_cellDelimiter" value='"'/>

                            <div class="iconbutton OFL" id="export-csv" data-action="exportCsv">
                                <div class="iconbutton-big-image csv-icon"/>
                                <span data-bind="text: res.fileMenu.exportCsvFile">Export CSV File</span>
                            </div>
                        </div>
                        <div id="export-pdf-page">
                            <div class="submenu-title" data-bind="text: res.fileMenu.pdfFile">PDF File</div>
                            <fieldset class="pdf-export-setting">
                                <legend data-bind="text: res.fileMenu.pdfBasicSetting">Basic Setting</legend>
                                <div class="basic-element-container">
                                    <label data-bind="text: res.fileMenu.pdfTitle" class="pdf-basic-setting-label">Title:</label>
                                    <input type="text" class="pdf-basic-setting-input pdf-title" />
                                </div>
                                <div class="basic-element-container">
                                    <label data-bind="text: res.fileMenu.pdfAuthor" class="pdf-basic-setting-label">Author:</label>
                                    <input type="text" class="pdf-basic-setting-input pdf-author" />
                                </div>
                                <div class="basic-element-container">
                                    <label data-bind="text: res.fileMenu.pdfApplication" class="pdf-basic-setting-label">Application:</label>
                                    <input type="text" class="pdf-basic-setting-input pdf-application" />
                                </div>
                                <div class="basic-element-container">
                                    <label data-bind="text: res.fileMenu.pdfSubject" class="pdf-basic-setting-label">Subject:</label>
                                    <input type="text" class="pdf-basic-setting-input pdf-subject" />
                                </div>
                                <div class="basic-element-container">
                                    <label data-bind="text: res.fileMenu.pdfKeyWords" class="pdf-basic-setting-label">Key words:</label>
                                    <input type="text" class="pdf-basic-setting-input pdf-keywords" />
                                </div>
                            </fieldset>
                            <fieldset class="pdf-export-setting">
                                <legend data-bind="text: res.fileMenu.pdfExportSetting">Export Settings</legend>
                                <div class="export-setting-container">
                                    <label data-bind="text: res.fileMenu.exportSheetLabel" class="export-setting-label">Choose the sheet to export:</label>
                                    <select class="export-setting-input export-sheet-select"></select>
                                </div>
                            </fieldset>
                            <!--hide not support settings start-->
                            <!--<fieldset class="pdf-export-setting">-->
                                <!--<legend data-bind="text: res.fileMenu.pdfDisplaySetting">Display Setting</legend>-->
                                <!--<div>-->
                                    <!--<div class="display-setting-container">-->
                                        <!--<input type="checkbox" id="center-window" class="center-window" />-->
                                        <!--<label data-bind="text: res.fileMenu.centerWindowLabel" for="center-window">Center window</label>-->
                                    <!--</div>-->
                                    <!--<div class="display-setting-container">-->
                                        <!--<input type="checkbox" id="show-title" class="show-title" />-->
                                        <!--<label data-bind="text: res.fileMenu.showTitleLabel" for="show-title">Show title</label>-->
                                    <!--</div>-->
                                    <!--<div class="display-setting-container">-->
                                        <!--<input type="checkbox" id="show-toolbar" class="show-toolbar" checked="checked" />-->
                                        <!--<label data-bind="text: res.fileMenu.showToolBarLabel" for="show-toolbar">Show toolbar</label>-->
                                    <!--</div>-->
                                <!--</div>-->
                                <!--<div>-->
                                    <!--<div class="display-setting-container">-->
                                        <!--<input type="checkbox" id="fit-window" class="fit-window" />-->
                                        <!--<label data-bind="text: res.fileMenu.fitWindowLabel" for="fit-window">Fit window</label>-->
                                    <!--</div>-->
                                    <!--<div class="display-setting-container">-->
                                        <!--<input type="checkbox" id="show-menu-bar" class="show-menu-bar" checked="checked" />-->
                                        <!--<label data-bind="text: res.fileMenu.showMenuBarLabel" for="show-menu-bar">Show menu bar</label>-->
                                    <!--</div>-->
                                    <!--<div class="display-setting-container">-->
                                        <!--<input type="checkbox" id="show-window-ui" class="show-window-ui" checked="checked" />-->
                                        <!--<label data-bind="text: res.fileMenu.showWindowUILabel" for="show-window-ui">Show window UI</label>-->
                                    <!--</div>-->
                                <!--</div>-->

                                <!--<div>-->
                                    <!--<div class="display-type-container">-->
                                        <!--<label data-bind="text: res.fileMenu.destinationTypeLabel" for="destination-type">Destination type:</label>-->
                                        <!--<select id="destination-type" class="destination-type display-setting-select">-->
                                            <!--<option data-bind="text: res.fileMenu.destinationType.autoDestination">Auto</option>-->
                                            <!--<option data-bind="text: res.fileMenu.destinationType.fitPageDestination">FitPage</option>-->
                                            <!--<option data-bind="text: res.fileMenu.destinationType.fitWidthDestination">FitWidth</option>-->
                                            <!--<option data-bind="text: res.fileMenu.destinationType.fitHeightDestination">FitHeight</option>-->
                                            <!--<option data-bind="text: res.fileMenu.destinationType.fitBoxDestination">FitBox</option>-->
                                        <!--</select>-->
                                    <!--</div>-->
                                    <!--<div class="display-type-container">-->
                                        <!--<label data-bind="text: res.fileMenu.openTypeLabel" for="show-window-ui">Open type:</label>-->
                                        <!--<select id="open-type" class="open-type display-setting-select">-->
                                            <!--<option data-bind="text: res.fileMenu.openType.autoOpen">Auto</option>-->
                                            <!--<option data-bind="text: res.fileMenu.openType.useNoneOpen">UseNone</option>-->
                                            <!--<option data-bind="text: res.fileMenu.openType.useOutlinesOpen">UseOutlines</option>-->
                                            <!--<option data-bind="text: res.fileMenu.openType.useThumbsOpen">UseThumbs</option>-->
                                            <!--<option data-bind="text: res.fileMenu.openType.fullScreenOpen">FullScreen</option>-->
                                            <!--<option data-bind="text: res.fileMenu.openType.useOCOpen">UseOC</option>-->
                                            <option data-bind="text: res.fileMenu.openType.useAttachmentsOpen">UseAttachments</option>
                                        <!--</select>-->
                                    <!--</div>-->
                                <!--</div>-->


                            <!--</fieldset>-->
                            <!--<fieldset class="pdf-export-setting">-->
                                <!--<legend data-bind="text: res.fileMenu.pdfPageSetting">Page Settings</legend>-->
                                <!--<div>-->
                                    <!--<div class="page-setting-container">-->
                                        <!--<label data-bind="text: res.fileMenu.openPageNumberLabel" class="page-settings-label">Open page number:</label>-->
                                        <!--<input type="text" class="page-settings open-page-number" value="1" />-->
                                    <!--</div>-->
                                    <!--<div class="page-setting-container">-->
                                        <!--<label data-bind="text: res.fileMenu.pageLayoutLabel" class="page-settings-label">Page layout:</label>-->
                                        <!--<select class="pdf-page-layout page-settings">-->
                                            <!--<option data-bind="text: res.fileMenu.pageLayout.autoLayout">Auto</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageLayout.singlePageLayout">SinglePage</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageLayout.oneColumnLayout">OneColumn</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageLayout.twoColumnLeftLayout">TwoColumnLeft</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageLayout.twoColumnRightLayout">TwoColumnRight</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageLayout.twoPageLeftLayout">TwoPageLeft</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageLayout.twoPageRight">TwoPageRight</option>-->
                                        <!--</select>-->
                                    <!--</div>-->
                                <!--</div>-->
                                <!--<div>-->
                                    <!--<div class="page-setting-container">-->
                                        <!--<label data-bind="text: res.fileMenu.pageDurationLabel" class="page-settings-label">Page duration:</label>-->
                                        <!--<input type="text" class="page-duration page-settings" value="-1" />-->
                                    <!--</div>-->
                                    <!--<div class="page-setting-container">-->
                                        <!--<label data-bind="text: res.fileMenu.pageTransitionLabel" class="page-settings-label">Page transition:</label>-->
                                        <!--<select class="pdf-page-transition page-settings">-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.defaultTransition">Default</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.splitTransition">Split</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.blindsTransition">Blinds</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.boxTransition">Box</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.wipeTransition">Wipe</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.dissolveTransition">Dissolve</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.glitterTransition">Glitter</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.flyTransition">Fly</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.pushTransition">Push</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.coverTransition">Cover</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.uncoverTransition">Uncover</option>-->
                                            <!--<option data-bind="text: res.fileMenu.pageTransition.fadeTransition">Fade</option>-->
                                        <!--</select>-->
                                    <!--</div>-->
                                <!--</div>-->

                            <!--</fieldset>-->

                            <!--<button class="export-pdf-printer">-->
                                <!--<span data-bind="text: res.fileMenu.printerSetting">Printer Setting...</span>-->
                            <!--</button>-->
                            <!--hide not support settings end-->

                            <div class="iconbutton OFL" id="export-pdf" data-action="exportPdf">
                                <div class="iconbutton-big-image pdf-icon"/>
                                <span data-bind="text: res.fileMenu.exportPdfFile">Export PDF File</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

