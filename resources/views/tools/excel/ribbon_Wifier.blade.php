<div class="ribbon">
    <div id="chartPreviewer"></div>
    <div class="ribbon-bar gcui-ribbon-width">
        <ul>
            <li>
                <div id="file-menu-tab" class="ribbon-menu-caption" data-bind="text: res.ribbon.home.file">File</div>
            </li>
            <li><a href="#homeTab" class="ribbon-menu-caption" data-bind="text: res.ribbon.home.home">HOME</a></li>
            <li><a href="#insertTab" class="ribbon-menu-caption" data-bind="text: res.ribbon.insert.insert">INSERT</a>
            </li>
            <li><a href="#formulaTab" class="ribbon-menu-caption" data-bind="text: res.ribbon.formulas.formulas">FORMULAS</a>
            </li>
            <li><a href="#dataTab" class="ribbon-menu-caption" data-bind="text: res.ribbon.data.data">DATA</a></li>
            <li><a href="#viewTab" class="ribbon-menu-caption" data-bind="text: res.ribbon.view.view">VIEW</a></li>
            <li><a href="#settingTab" class="ribbon-menu-caption"
                   data-bind="text: res.ribbon.setting.setting">SETTINGS</a></li>
            <li><a href="#sparklineTab" class="ribbon-menu-caption" data-bind="text: res.ribbon.sparkLineDesign.design">DESIGN</a>
            </li>
            <li><a href="#formulaSparklineTab" class="ribbon-menu-caption"
                   data-bind="text: res.ribbon.formulaSparklineDesign.design">DESIGN</a></li>
            <li><a href="#tableTab" class="ribbon-menu-caption"
                   data-bind="text: res.ribbon.tableDesign.design">DESIGN</a></li>
            <li><a href="#slicerTab" class="ribbon-menu-caption" data-bind="text: res.ribbon.slicerOptions.options">OPTIONS</a>
            </li>
            <li><a href="#chartTab" class="ribbon-menu-caption"
                   data-bind="text: res.ribbon.chartDesign.design">DESIGN</a></li>
        </ul>
        <div id="homeTab">
            <ul>
                <li id="clipboardgroup">
                    <div class="gcui-ribbon-list">
                        <button id="paste-options" title="Paste" data-bind="attr: { title: res.ribbon.home.paste }"
                                class="gcui-ribbon-bigbutton" name="paste">
                            <span class="gcui-ribbon-paste"></span>
                            <span data-bind="text: res.ribbon.home.paste">Paste</span>
                        </button>
                        <ul>
                            <li>
                                <button name="paste-all"
                                        data-bind="text: res.ribbon.home.all, attr: { title: res.ribbon.home.all }">All
                                </button>
                            </li>
                            <li>
                                <button name="paste-formulas"
                                        data-bind="text: res.ribbon.home.formulas, attr: { title: res.ribbon.home.formulas }">
                                    Formulas
                                </button>
                            </li>
                            <li>
                                <button name="paste-values"
                                        data-bind="text: res.ribbon.home.values, attr: { title: res.ribbon.home.values }">
                                    Values
                                </button>
                            </li>
                            <li>
                                <button name="paste-formatting"
                                        data-bind="text: res.ribbon.home.formatting, attr: { title: res.ribbon.home.formatting }">
                                    Formatting
                                </button>
                            </li>
                        </ul>

                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Cut" data-bind="attr: { title: res.ribbon.home.cut }" class="gcui-ribbon-cut"
                                    name="cut">
                            </button>
                        </div>
                        <div>
                            <button title="Copy" data-bind="attr: { title: res.ribbon.home.copy }"
                                    class="gcui-ribbon-copy" name="copy">
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.home.clipboard">Clipboard</div>
                </li>
                <li id="fontgroup">
                    <div>
                        <div class="gcui-ribbon-list gcui-ribbon-fontfamily-parent">
                            <button id="font-family" title="Font Family" data-bind="attr: { title: res.ribbon.home.fontFamily }" name="font-family">Calibri</button>
                            <ul>
                                <li>
                                    <input type="radio" id="font-family-001" name="font-family" />
                                    <label for="font-family-001" name="ff1" id="ff1" data-bind="text: res.ribbon.fontFamilies.ff1.text, attr: { title: res.ribbon.fontFamilies.ff1.text, 'data-font': res.ribbon.fontFamilies.ff1.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-002" name="font-family" />
                                    <label for="font-family-002" name="ff2" id="ff2" data-bind="text: res.ribbon.fontFamilies.ff2.text, attr: { title: res.ribbon.fontFamilies.ff2.text, 'data-font': res.ribbon.fontFamilies.ff2.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-003" name="font-family" />
                                    <label for="font-family-003" name="ff3" id="ff3" data-bind="text: res.ribbon.fontFamilies.ff3.text, attr: { title: res.ribbon.fontFamilies.ff3.text, 'data-font': res.ribbon.fontFamilies.ff3.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-004" name="font-family" />
                                    <label for="font-family-004" name="ff4" id="ff4" data-bind="text: res.ribbon.fontFamilies.ff4.text, attr: { title: res.ribbon.fontFamilies.ff4.text, 'data-font': res.ribbon.fontFamilies.ff4.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-005" name="font-family" />
                                    <label for="font-family-005" name="ff5" id="ff5" data-bind="text: res.ribbon.fontFamilies.ff5.text, attr: { title: res.ribbon.fontFamilies.ff5.text, 'data-font': res.ribbon.fontFamilies.ff5.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-006" name="font-family" />
                                    <label for="font-family-006" name="ff6" id="ff6" data-bind="text: res.ribbon.fontFamilies.ff6.text, attr: { title: res.ribbon.fontFamilies.ff6.text, 'data-font': res.ribbon.fontFamilies.ff6.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-007" name="font-family" />
                                    <label for="font-family-007" name="ff7" id="ff7" data-bind="text: res.ribbon.fontFamilies.ff7.text, attr: { title: res.ribbon.fontFamilies.ff7.text, 'data-font': res.ribbon.fontFamilies.ff7.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-008" name="font-family" />
                                    <label for="font-family-008" name="ff8" id="ff8" data-bind="text: res.ribbon.fontFamilies.ff8.text, attr: { title: res.ribbon.fontFamilies.ff8.text, 'data-font': res.ribbon.fontFamilies.ff8.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-009" name="font-family" />
                                    <label for="font-family-009" name="ff9" id="ff9" data-bind="text: res.ribbon.fontFamilies.ff9.text, attr: { title: res.ribbon.fontFamilies.ff9.text, 'data-font': res.ribbon.fontFamilies.ff9.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-010" name="font-family" />
                                    <label for="font-family-010" name="ff10" id="ff10" data-bind="text: res.ribbon.fontFamilies.ff10.text, attr: { title: res.ribbon.fontFamilies.ff10.text, 'data-font': res.ribbon.fontFamilies.ff10.name }"></label>
								</li>
                                <li>
                                    <input type="radio" id="font-family-011" name="font-family" />
                                    <label for="font-family-011" name="ff11" id="ff11" data-bind="text: res.ribbon.fontFamilies.ff11.text, attr: { title: res.ribbon.fontFamilies.ff11.text, 'data-font': res.ribbon.fontFamilies.ff11.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-012" name="font-family" />
                                    <label for="font-family-012" name="ff12" id="ff12" data-bind="text: res.ribbon.fontFamilies.ff12.text, attr: { title: res.ribbon.fontFamilies.ff12.text, 'data-font': res.ribbon.fontFamilies.ff12.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-013" name="font-family" />
                                    <label for="font-family-013" name="ff13" id="ff13" data-bind="text: res.ribbon.fontFamilies.ff13.text, attr: { title: res.ribbon.fontFamilies.ff13.text, 'data-font': res.ribbon.fontFamilies.ff13.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-014" name="font-family" />
                                    <label for="font-family-014" name="ff14" id="ff14" data-bind="text: res.ribbon.fontFamilies.ff14.text, attr: { title: res.ribbon.fontFamilies.ff14.text, 'data-font': res.ribbon.fontFamilies.ff14.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-015" name="font-family" />
                                    <label for="font-family-015" name="ff15" id="ff15" data-bind="text: res.ribbon.fontFamilies.ff15.text, attr: { title: res.ribbon.fontFamilies.ff15.text, 'data-font': res.ribbon.fontFamilies.ff15.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-016" name="font-family" />
                                    <label for="font-family-016" name="ff16" id="ff16" data-bind="text: res.ribbon.fontFamilies.ff16.text, attr: { title: res.ribbon.fontFamilies.ff16.text, 'data-font': res.ribbon.fontFamilies.ff16.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-017" name="font-family" />
                                    <label for="font-family-017" name="ff17" id="ff17" data-bind="text: res.ribbon.fontFamilies.ff17.text, attr: { title: res.ribbon.fontFamilies.ff17.text, 'data-font': res.ribbon.fontFamilies.ff17.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-018" name="font-family" />
                                    <label for="font-family-018" name="ff18" id="ff18" data-bind="text: res.ribbon.fontFamilies.ff18.text, attr: { title: res.ribbon.fontFamilies.ff18.text, 'data-font': res.ribbon.fontFamilies.ff18.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-019" name="font-family" />
                                    <label for="font-family-019" name="ff19" id="ff19" data-bind="text: res.ribbon.fontFamilies.ff19.text, attr: { title: res.ribbon.fontFamilies.ff19.text, 'data-font': res.ribbon.fontFamilies.ff19.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-020" name="font-family" />
                                    <label for="font-family-020" name="ff20" id="ff20" data-bind="text: res.ribbon.fontFamilies.ff20.text, attr: { title: res.ribbon.fontFamilies.ff20.text, 'data-font': res.ribbon.fontFamilies.ff20.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-021" name="font-family" />
                                    <label for="font-family-021" name="ff21" id="ff21" data-bind="text: res.ribbon.fontFamilies.ff21.text, attr: { title: res.ribbon.fontFamilies.ff21.text, 'data-font': res.ribbon.fontFamilies.ff21.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-022" name="font-family" />
                                    <label for="font-family-022" name="ff22" id="ff22" data-bind="text: res.ribbon.fontFamilies.ff22.text, attr: { title: res.ribbon.fontFamilies.ff22.text, 'data-font': res.ribbon.fontFamilies.ff22.name }"></label>
                                </li>
                                <li>
                                    <input type="radio" id="font-family-023" name="font-family" />
                                    <label for="font-family-023" name="ff23" id="ff23" data-bind="text: res.ribbon.fontFamilies.ff23.text, attr: { title: res.ribbon.fontFamilies.ff23.text, 'data-font': res.ribbon.fontFamilies.ff23.name }"></label>
                                </li>
                            </ul>
                        </div>
                        <div class="gcui-ribbon-list  gcui-ribbon-fontsize-parent">
                            <button title="Font Size" data-bind="attr: { title: res.ribbon.home.fontSize }"
                                    name="font-size" id="font-size">10
                            </button>
                            <ul>
                                <li>
                                    <input type="radio" id="font-size-001" name="font-size"/>
                                    <label for="font-size-001" name="fs1" id="fs1" title="8">8</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-002" name="font-size"/>
                                    <label for="font-size-002" name="fs2" id="fs2" title="9">9</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-003" name="font-size"/>
                                    <label for="font-size-003" name="fs3" id="fs3" title="10">10</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-004" name="font-size"/>
                                    <label for="font-size-004" name="fs4" id="fs4" title="11">11</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-005" name="font-size"/>
                                    <label for="font-size-005" name="fs5" id="fs5" title="12">12</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-006" name="font-size"/>
                                    <label for="font-size-006" name="fs6" id="fs6" title="14">14</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-007" name="font-size"/>
                                    <label for="font-size-007" name="fs7" id="fs7" title="16">16</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-008" name="font-size"/>
                                    <label for="font-size-008" name="fs8" id="fs8" title="18">18</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-009" name="font-size"/>
                                    <label for="font-size-009" name="fs9" id="fs9" title="20">20</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-010" name="font-size"/>
                                    <label for="font-size-010" name="fs10" id="fs10" title="24">24</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-011" name="font-size"/>
                                    <label for="font-size-011" name="fs11" id="fs11" title="26">26</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-012" name="font-size"/>
                                    <label for="font-size-012" name="fs12" id="fs12" title="28">28</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-013" name="font-size"/>
                                    <label for="font-size-013" name="fs13" id="fs13" title="36">36</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-014" name="font-size"/>
                                    <label for="font-size-014" name="fs14" id="fs14" title="48">48</label>
                                </li>
                                <li>
                                    <input type="radio" id="font-size-015" name="font-size"/>
                                    <label for="font-size-015" name="fs15" id="fs15" title="72">72</label>
                                </li>
                            </ul>
                        </div>
                        <button title="Increase Font Size" data-bind="attr: { title: res.ribbon.home.increaseFontSize }"
                                name="increase-fontsize" class="gcui-ribbon-increaseFontSize">
                        </button>
                        <button title="Decrease Font Size" data-bind="attr: { title: res.ribbon.home.decreaseFontSize }"
                                name="decrease-fontsize" class="gcui-ribbon-decreaseFontSize">
                        </button>
                    </div>
                    <div class="clear-float">
                        <div class="gcui-ribbon-list">
                            <input type="checkbox" id="font-weight"/>
                            <label for="font-weight" name="font-weight" title="Bold"
                                   data-bind="attr: { title: res.ribbon.home.bold }" class="gcui-ribbon-bold"></label>

                            <input type="checkbox" id="font-italic"/>
                            <label for="font-italic" name="font-italic" title="Italic"
                                   data-bind="attr: { title: res.ribbon.home.italic }"
                                   class="gcui-ribbon-italic"></label>

                            <input type="checkbox" id="font-underline"/>
                            <label for="font-underline" name="font-underline"
                                   data-bind="attr: {title:res.ribbon.home.underline}"
                                   class="gcui-ribbon-underline"></label>

                            <input type="checkbox" id="font-double-underline"/>
                            <label for="font-double-underline" name="font-double-underline"
                                   data-bind="attr: {title:res.ribbon.home.doubleUnderline}"
                                   class="gcui-ribbon-double-underline"></label>
                            <div class="gcui-ribbon-separator"></div>
                            <span>
                                    <button id="borders" title="Borders"
                                            data-bind="attr: { title: res.ribbon.home.border }" name="bottom-border"
                                            class="gcui-ribbon-bottomborder">
                                    </button>
                                    <ul>
                                        <li>
                                            <button name="bottom-border" title="Bottom Border"
                                                    data-bind="attr: { title: res.ribbon.home.bottomBorder }">
                                                <span class="gcui-ribbon-bottomborder"></span>
                                                <span data-bind="text: res.ribbon.home.bottomBorder">Bottom Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="top-border" title="Top Border"
                                                    data-bind="attr: { title: res.ribbon.home.topBorder }">
                                                <span class="gcui-ribbon-topborder"></span>
                                                <span data-bind="text: res.ribbon.home.topBorder">Top Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="left-border" title="Left Border"
                                                    data-bind="attr: { title: res.ribbon.home.leftBorder }">
                                                <span class="gcui-ribbon-leftborder"></span>
                                                <span data-bind="text: res.ribbon.home.leftBorder">Left Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="right-border" title="Right Border"
                                                    data-bind="attr: { title: res.ribbon.home.rightBorder }">
                                                <span class="gcui-ribbon-rightborder"></span>
                                                <span data-bind="text: res.ribbon.home.rightBorder">Right Border</span>
                                            </button>
                                        </li>

                                        <li>
                                            <div class="gcui-ribbon-listseparator"></div>
                                        </li>

                                        <li>
                                            <button name="no-border" title="No Border"
                                                    data-bind="attr: { title: res.ribbon.home.noBorder }">
                                                <span class="gcui-ribbon-noborder"></span>
                                                <span data-bind="text: res.ribbon.home.noBorder">No Border</span>
                                            </button>
                                        </li>

                                        <li>
                                            <button name="all-border" title="All Borders"
                                                    data-bind="attr: { title: res.ribbon.home.allBorder }">
                                                <span class="gcui-ribbon-allborder"></span>
                                                <span data-bind="text: res.ribbon.home.allBorder">All Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="outside-border" title="Outside Borders"
                                                    data-bind="attr: { title: res.ribbon.home.outsideBorder }">
                                                <span class="gcui-ribbon-outsideborder"></span>
                                                <span data-bind="text: res.ribbon.home.outsideBorder">Outside Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="thickbox-border" title="Thick Box Border"
                                                    data-bind="attr: { title: res.ribbon.home.thickBoxBorder }">
                                                <span class="gcui-ribbon-thickborder"></span>
                                                <span data-bind="text: res.ribbon.home.thickBoxBorder">Thick Box Border</span>
                                            </button>
                                        </li>

                                        <li>
                                            <div class="gcui-ribbon-listseparator"></div>
                                        </li>
                                        <li>
                                            <button name="bottom-double-border" title="Bottom Double Border"
                                                    data-bind="attr: { title: res.ribbon.home.bottomDoubleBorder }">
                                                <span class="gcui-ribbon-bdborder"></span>
                                                <span data-bind="text: res.ribbon.home.bottomDoubleBorder">Bottom Double Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="thick-bottom-border" title="Thick Bottom Border"
                                                    data-bind="attr: { title: res.ribbon.home.thickBottomBorder }">
                                                <span class="gcui-ribbon-tbborder"></span>
                                                <span data-bind="text: res.ribbon.home.thickBottomBorder">Thick Bottom Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="top-bottom-border" title="Top and Bottom Border"
                                                    data-bind="attr: { title: res.ribbon.home.topBottomBorder }">
                                                <span class="gcui-ribbon-tb2border"></span>
                                                <span data-bind="text: res.ribbon.home.topBottomBorder">Top and Bottom Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="top-thick-bottom-border" title="Top and Thick Bottom Border"
                                                    data-bind="attr: { title: res.ribbon.home.topThickBottomBorder }">
                                                <span class="gcui-ribbon-ttbborder"></span>
                                                <span data-bind="text: res.ribbon.home.topThickBottomBorder">Top and Thick Bottom Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="top-double-bottom-border" title="Top and Double Bottom Border"
                                                    data-bind="attr: { title: res.ribbon.home.topDoubleBottomBorder }">
                                                <span class="gcui-ribbon-tdbborder"></span>
                                                <span data-bind="text: res.ribbon.home.topDoubleBottomBorder">Top and Double Bottom Border</span>
                                            </button>
                                        </li>
                                        <li>
                                            <div class="gcui-ribbon-listseparator"></div>
                                        </li>
                                        <li>
                                            <button name="more-border" title="More Borders"
                                                    data-bind="attr: { title: res.ribbon.home.moreBorders }">
                                                <span class="gcui-ribbon-moreborder"></span>
                                                <span data-bind="text: res.ribbon.home.moreBorders">More Borders...</span>
                                            </button>
                                        </li>

                                    </ul>
                                </span>

                            <div class="gcui-ribbon-separator"></div>

                            <button id="backcolor-button" title="Background Color"
                                    data-bind="attr: { title: res.ribbon.home.backColor }" class="gcui-ribbon-bgcolor"
                                    name="backcolor"></button>
                            <button id="forecolor-button" title="Font Color"
                                    data-bind="attr: { title: res.ribbon.home.fontColor }" class="gcui-ribbon-forecolor"
                                    name="forecolor"></button>
                        </div>
                    </div>
                    <div>
                        <span data-bind="text: res.ribbon.home.fonts">Fonts</span>
                        <button title="Font" class="gcui-ribbon-indicatoricon"
                                data-bind="attr: { title: res.ribbon.home.fonts }" name="fontgroup">
                        </button>
                    </div>
                </li>
                <li id="alignmentgroup">
                    <div class="gcui-ribbon-list ui-buttonset">
                    <div>
                            <span class="gcui-ribbon-list">
                                <input type="radio" name="vertical-align" id="top-align"/>
                                <label for="top-align" name="top-align" class="gcui-ribbon-topalign" title="Top Align"
                                       data-bind="attr: { title: res.ribbon.home.topAlign }"></label>

                                <input type="radio" name="vertical-align" id="middle-align"/>
                                <label for="middle-align" name="middle-align" class="gcui-ribbon-middlealign"
                                       title="Middle Align"
                                       data-bind="attr: { title: res.ribbon.home.middleAlign }"></label>

                                <input type="radio" name="vertical-align" id="bottom-align"/>
                                <label for="bottom-align" name="bottom-align" class="gcui-ribbon-bottomalign"
                                       title="Bottom Align"
                                       data-bind="attr: { title: res.ribbon.home.bottomAlign }"></label>
                            </span>

                        <div class="gcui-ribbon-separator"></div>
                            <input type="checkbox" id="vertical-text"/>
                            <label for="vertical-text" name="vertical-text" class="gcui-ribbon-vertical-text" title="Vertical Text"
                                   data-bind="attr: { title: res.ribbon.home.verticalText }"></label>
                    </div>
                    <div class="clear-float">
                            <span class="gcui-ribbon-list">
                                <input type="radio" name="horizontal-align" id="left-algin"/>
                                <label for="left-algin" name="left-align" class="gcui-ribbon-leftalign"
                                       title="Left Align"
                                       data-bind="attr: { title: res.ribbon.home.leftAlign }"></label>

                                <input type="radio" name="horizontal-align" id="center-align"/>
                                <label for="center-align" name="center-align" class="gcui-ribbon-centeralign"
                                       title="Center Align"
                                       data-bind="attr: { title: res.ribbon.home.centerAlign }"></label>

                                <input type="radio" name="horizontal-align" id="right-align"/>
                                <label for="right-align" name="right-align" class="gcui-ribbon-rightalign"
                                       title="Right Align"
                                       data-bind="attr: { title: res.ribbon.home.rightAlign }"></label>
                            </span>

                        <div class="gcui-ribbon-separator"></div>
                            <button title="Increase Indent" data-bind="attr: { title: res.ribbon.home.increaseIndent }"
                                    class="gcui-ribbon-increaseindent" name="increase-indent">
                            </button>
                        <button title="Decrease Indent" data-bind="attr: { title: res.ribbon.home.decreaseIndent }"
                                class="gcui-ribbon-decreaseindent"
                                name="decrease-indent">
                        </button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-longseparator"></div>
                    <div class="gcui-ribbon-list">
                        <div>
                        <span>
                                <input type="checkbox" id="wrap-text"/>
                                <label for="wrap-text" name="wrap-text" title="Wrap Text"
                                       data-bind="attr: { title: res.ribbon.home.wrapText }, text: res.ribbon.home.wrapText"
                                       class="gcui-ribbon-wraptext">Wrap Text</label>
                            </span>
                        </div>
                        <div>
                        <span>
                                <input type="checkbox" id="merge-center"/>
                                <label for="merge-center" name="merge-center" title="Merge &amp; Center" data-bind="attr: { title: res.ribbon.home.mergeCenter }, text: res.ribbon.home.mergeCenter" class="gcui-ribbon-mergecenter">Merge &amp; Center</label>
                            </span>
                        <span>
                                <button class="ui-button-icon-only gcui-ribbon-smallsplit"></button>
                                <ul>
                                    <li>
                                        <button name="merge-center-button" title="Merge &amp; Center"
                                                data-bind="attr: { title: res.ribbon.home.mergeCenter }">
                                            <span class="gcui-ribbon-mergecenter"></span>
                                            <span data-bind="text: res.ribbon.home.mergeCenter">Merge &amp; Center</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="merge-across" title="Merge Across"
                                                data-bind="attr: { title: res.ribbon.home.mergeAcross }">
                                            <span class="gcui-ribbon-mergeacross"></span>
                                            <span data-bind="text: res.ribbon.home.mergeAcross">Merge Across</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="merge-cells" title="Merge Cells"
                                                data-bind="attr: { title: res.ribbon.home.mergeCells }">
                                            <span class="gcui-ribbon-mergecells"></span>
                                            <span data-bind="text: res.ribbon.home.mergeCells">Merge Cells</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="unmerge-cells" title="UnMerge Cells"
                                                data-bind="attr: { title: res.ribbon.home.unMergeCells }">
                                            <span class="gcui-ribbon-unmergecells"></span>
                                            <span data-bind="text: res.ribbon.home.unMergeCells">UnMerge Cells</span>
                                        </button>
                                    </li>

                                </ul>
                            </span>
                        </div>
                    </div>
                    <div>
                        <span data-bind="text: res.ribbon.home.alignment">Alignment</span>
                        <button title="Alignment" class="gcui-ribbon-indicatoricon"
                                data-bind="attr: { title: res.ribbon.home.alignment }" name="aligngroup">
                        </button>
                    </div>
                </li>
                <li id="numbergroup">
                    <div>
                        <div class="gcui-ribbon-number-parent">
                            <button id="number-format" title="Number Format"
                                    data-bind="attr: { title: res.ribbon.home.numberFormat }, text: res.ribbon.home.general"
                                    name="numberformat">General
                            </button>
                            <ul>
                                <li class="gcui-ribbon-biglabel">
                                    <input type="radio" id="format-general" name="numberformat"/>
                                    <label for="format-general" name="format-general" title="General"
                                           data-bind="attr: { title: res.ribbon.home.general }, text: res.ribbon.home.general">General</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-number" name="numberformat"/>
                                    <label for="format-number" name="format-number" title="Number"
                                           data-bind="attr: { title: res.ribbon.home.Number }, text: res.ribbon.home.Number">Number</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-currency" name="numberformat"/>
                                    <label for="format-currency" name="format-currency" title="Currency"
                                           data-bind="attr: { title: res.ribbon.home.currency }, text: res.ribbon.home.currency">Currency</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-accouting" name="numberformat"/>
                                    <label for="format-accouting" name="format-accouting" title="Accounting"
                                           data-bind="attr: { title: res.ribbon.home.accounting }, text: res.ribbon.home.accounting">Accounting</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-shortdate" name="numberformat"/>
                                    <label for="format-shortdate" name="format-shortdate" title="Short Date"
                                           data-bind="attr: { title: res.ribbon.home.shortDate }, text: res.ribbon.home.shortDate">Short
                                        Date</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-longdate" name="numberformat"/>
                                    <label for="format-longdate" name="format-longdate" title="Long Date"
                                           data-bind="attr: { title: res.ribbon.home.longDate }, text: res.ribbon.home.longDate">Long
                                        Date</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-time" name="numberformat"/>
                                    <label for="format-time" name="format-time" title="Time"
                                           data-bind="attr: { title: res.ribbon.home.time }, text: res.ribbon.home.time">Time</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-percentage" name="numberformat"/>
                                    <label for="format-percentage" name="format-percentage" title="Percentage"
                                           data-bind="attr: { title: res.ribbon.home.percentage }, text: res.ribbon.home.percentage">Percentage</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-fraction" name="numberformat"/>
                                    <label for="format-fraction" name="format-fraction" title="Fraction"
                                           data-bind="attr: {title:res.ribbon.home.fraction },text:res.ribbon.home.fraction">Fraction</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-scientific" name="numberformat"/>
                                    <label for="format-scientific" name="format-scientific" title="Scientific"
                                           data-bind="attr: { title: res.ribbon.home.scientific }, text: res.ribbon.home.scientific">Scientific</label>
                                </li>
                                <li>
                                    <input type="radio" id="format-text" name="numberformat"/>
                                    <label for="format-text" name="format-text" title="Text"
                                           data-bind="attr: { title: res.ribbon.home.text }, text: res.ribbon.home.text">Text</label>
                                </li>
                                <li>
                                    <div class="gcui-ribbon-listseparator"></div>
                                </li>
                                <li>
                                    <button name="format-more" title="More Number Formats"
                                            data-bind="attr: { title: res.ribbon.home.moreNumberFormat }, text: res.ribbon.home.moreNumberFormat">
                                        More Number Formats...
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <button title="Percent Style" data-bind="attr: { title: res.ribbon.home.percentStyle }"
                                class="gcui-ribbon-percentstyle" name="format-percentage"></button>
                        <button title="Comma Style" data-bind="attr: { title: res.ribbon.home.commaStyle }"
                                class="gcui-ribbon-commastyle" name="format-comma"></button>
                        <div class="gcui-ribbon-separator"></div>
                        <button title="Increase Decimal" data-bind="attr: { title: res.ribbon.home.increaseDecimal }"
                                class="gcui-ribbon-increasedecimal" name="increase-decimal"></button>
                        <button title="Decrease Decimal" data-bind="attr: { title: res.ribbon.home.decreaseDecimal }"
                                class="gcui-ribbon-decreasedecimal" name="decrease-decimal"></button>
                    </div>
                    <div>
                        <span data-bind="text: res.ribbon.home.numbers">Numbers</span>
                        <button title="Number" class="gcui-ribbon-indicatoricon"
                                data-bind="attr: { title: res.ribbon.home.numbers }" name="numbergroup">
                        </button>
                    </div>
                </li>
                <li id="celltypegroup">
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Button CellType" data-bind="attr: { title: res.ribbon.home.buttonCellType }"
                                    class="gcui-ribbon-buttoncelltype" name="button-celltype"></button>
                            <button title="CheckBox CellType"
                                    data-bind="attr: { title: res.ribbon.home.checkboxCellType }"
                                    class="gcui-ribbon-checkboxcelltype" name="checkbox-celltype"></button>
                        </div>
                        <div>
                            <button title="ComboBox CellType"
                                    data-bind="attr: { title: res.ribbon.home.comboBoxCellType }"
                                    class="gcui-ribbon-comboboxcelltype" name="combobox-celltype"></button>
                            <button title="HyperLink CellType"
                                    data-bind="attr: { title: res.ribbon.home.hyperlinkCellType }"
                                    class="gcui-ribbon-hyperlinkcelltype" name="hyperlink-celltype"></button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-list">
                        <button title="Clear CellType" data-bind="attr: { title: res.ribbon.home.clearCellType1 }"
                                class="gcui-ribbon-bigbutton" name="clear-celltype">
                            <span class="gcui-ribbon-clear-celltype"></span>
                            <span data-bind="text: res.ribbon.home.clearCellType">Clear CellType</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.home.cellType">CellType</div>
                </li>
                <li id="stylesgroup">
                    <button id="condition-format" title="Conditional Formatting" data-button-size="65px"
                            data-bind="attr: { title: res.ribbon.home.conditionFormat1 }" class="gcui-ribbon-bigbutton"
                            name="conditional-format">
                        <span class="gcui-ribbon-conditionalformat"></span>
                        <span data-bind="text: res.ribbon.home.conditionFormat">Conditional Format</span>
                    </button>
                    <button title="Format as Table" data-button-size="85px"
                            data-bind="attr: { title: res.ribbon.home.formatTable1 }" class="gcui-ribbon-bigbutton"
                            id="format-table-button" name="format-table">
                        <span class="gcui-ribbon-formattable"></span>
                        <span data-bind="text: res.ribbon.home.formatTable">Format Table</span>
                    </button>
                    <button title="Cell Styles" data-bind="attr: { title: res.cellStylesDialog.cellStyles }"
                            class="gcui-ribbon-bigbutton" id="cell-styles-button"
                            name="cell-styles">
                        <span class="gcui-ribbon-cellstyles"></span>
                        <span data-bind="text:res.cellStylesDialog.cellStyles">Cell Styles</span>
                    </button>
                    <div data-bind="text:res.ribbon.home.styles">Styles</div>
                </li>
                <li id="cellsgroup">
                    <div class="gcui-ribbon-list">
                        <button id="insert-row-column" title="Insert"
                                data-bind="attr: { title: res.ribbon.home.insert }" class="gcui-ribbon-bigbutton"
                                name="insert">
                            <span class="gcui-ribbon-insert"></span>
                            <span data-bind="text: res.ribbon.home.insert">Insert</span>
                        </button>
                        <ul>
                            <li>
                                <button name="insert-cells" title="Insert Cells"
                                        data-bind="attr: { title: res.ribbon.home.insertCells }">
                                    <span class="gcui-ribbon-insertcells"></span>
                                    <span data-bind="text: res.ribbon.home.insertCells">Insert Cells...</span>
                                </button>
                            </li>
                            <li>
                                <div class="gcui-ribbon-listseparator"></div>
                            </li>
                            <li>
                                <button name="insert-rows" title="Insert Sheet Rows"
                                        data-bind="attr: { title: res.ribbon.home.insertSheetRows }">
                                    <span class="gcui-ribbon-insertcells"></span>
                                    <span data-bind="text: res.ribbon.home.insertSheetRows">Insert Sheet Rows</span>
                                </button>
                            </li>
                            <li>
                                <button name="insert-columns" title="Insert Sheet Columns"
                                        data-bind="attr: { title: res.ribbon.home.insertSheetColumns }">
                                    <span class="gcui-ribbon-insertcolumns"></span>
                                    <span data-bind="text: res.ribbon.home.insertSheetColumns">Insert Sheet Columns</span>
                                </button>
                            </li>
                            <li>
                                <div class="gcui-ribbon-listseparator"></div>
                            </li>
                            <li>
                                <button name="insert-sheet" title="Insert Sheet"
                                        data-bind="attr: { title: res.ribbon.home.insertSheet }">
                                    <span class="gcui-ribbon-insertsheet"></span>
                                    <span data-bind="text: res.ribbon.home.insertSheet">Insert Sheet</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="gcui-ribbon-list">

                        <button id="delete-row-column" title="Delete"
                                data-bind="attr: { title: res.ribbon.home.Delete }" class="gcui-ribbon-bigbutton"
                                name="delete">
                            <span class="gcui-ribbon-delete"></span>
                            <span data-bind="text: res.ribbon.home.Delete">Delete</span>
                        </button>
                        <ul>
                            <li>
                                <button name="delete-cells" title="Delete Cells"
                                        data-bind="attr: { title: res.ribbon.home.deleteCells }">
                                    <span class="gcui-ribbon-deletecells"></span>
                                    <span data-bind="text: res.ribbon.home.deleteCells">Delete Cells...</span>
                                </button>
                            </li>
                            <li>
                                <div class="gcui-ribbon-listseparator"></div>
                            </li>
                            <li>
                                <button name="delete-rows" title="Delete Sheet Rows"
                                        data-bind="attr: { title: res.ribbon.home.deleteSheetRows }">
                                    <span class="gcui-ribbon-deleterows"></span>
                                    <span data-bind="text: res.ribbon.home.deleteSheetRows">Delete Sheet Rows</span>
                                </button>
                            </li>
                            <li>
                                <button name="delete-columns" title="Delete Sheet Columns"
                                        data-bind="attr: { title: res.ribbon.home.deleteSheetColumns }">
                                    <span class="gcui-ribbon-deletecolumns"></span>
                                    <span data-bind="text: res.ribbon.home.deleteSheetColumns">Delete Sheet Columns</span>
                                </button>
                            </li>
                            <li>
                                <div class="gcui-ribbon-listseparator"></div>
                            </li>
                            <li>
                                <button name="delete-sheet" title="Delete Sheet"
                                        data-bind="attr: { title: res.ribbon.home.deleteSheet }">
                                    <span class="gcui-ribbon-deletesheet"></span>
                                    <span data-bind="text: res.ribbon.home.deleteSheet">Delete Sheet</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button id="format-row-column" title="Format"
                                    data-bind="attr: { title: res.ribbon.home.format }" class="gcui-ribbon-bigbutton"
                                    name="format">
                                <span class="gcui-ribbon-format"></span>
                                <span data-bind="text: res.ribbon.home.format">Format</span>
                            </button>
                            <ul>
                                <li>
                                    <button name="set-rowheight" title="Row Height"
                                            data-bind="attr: { title: res.ribbon.home.rowHeight }">
                                        <span class="gcui-ribbon-rowheight"></span>
                                        <span data-bind="text: res.ribbon.home.rowHeight">Row Height...</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="autofit-rowheight" title="AutoFit Row Height"
                                            data-bind="attr: { title: res.ribbon.home.autofitRowHeight }">
                                        <span class="gcui-ribbon-emptyicon"></span>
                                        <span data-bind="text: res.ribbon.home.autofitRowHeight">AutoFit Row Height</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="default-rowheight" title="Default Height"
                                            data-bind="attr: { title: res.ribbon.home.defaultHeight }">
                                        <span class="gcui-ribbon-emptyicon"></span>
                                        <span data-bind="text: res.ribbon.home.defaultHeight">Default Height...</span>
                                    </button>
                                </li>
                                <li>
                                    <div class="gcui-ribbon-listseparator"></div>
                                </li>
                                <li>
                                    <button name="set-columnwidth" title="Column Width..."
                                            data-bind="attr: { title: res.ribbon.home.columnWidth }">
                                        <span class="gcui-ribbon-columnwidth"></span>
                                        <span data-bind="text: res.ribbon.home.columnWidth">Column Width...</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="autofit-columnwidth" title="AutoFit Column Width"
                                            data-bind="attr: { title: res.ribbon.home.autofitColumnWidth }">
                                        <span class="gcui-ribbon-emptyicon"></span>
                                        <span data-bind="text: res.ribbon.home.autofitColumnWidth">AutoFit Column Width</span>

                                    </button>
                                </li>
                                <li>
                                    <button name="default-columnwidth" title="Default Width"
                                            data-bind="attr: { title: res.ribbon.home.defaultWidth }">
                                        <span class="gcui-ribbon-emptyicon"></span>
                                        <span data-bind="text: res.ribbon.home.defaultWidth">Default Width...</span>
                                    </button>
                                </li>
                                <li>
                                    <div class="gcui-ribbon-listseparator"></div>
                                </li>
                                <li>
                                    <button name="hide-rows" title="Hide Rows"
                                            data-bind="attr: { title: res.ribbon.home.hideRows }">
                                        <span class="gcui-ribbon-emptyicon"></span>
                                        <span data-bind="text: res.ribbon.home.hideRows">Hide Rows</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="hide-columns" title="Hide Columns"
                                            data-bind="attr: { title: res.ribbon.home.hideColumns }">
                                        <span class="gcui-ribbon-emptyicon"></span>
                                        <span data-bind="text: res.ribbon.home.hideColumns">Hide Columns</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="unhide-rows" title="UnHide Rows"
                                            data-bind="attr: { title: res.ribbon.home.unHideRows }">
                                        <span class="gcui-ribbon-emptyicon"></span>
                                        <span data-bind="text: res.ribbon.home.unHideRows">UnHide Rows</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="unhide-columns" title="UnHide Columns"
                                            data-bind="attr: { title: res.ribbon.home.unHideColumns }">
                                        <span class="gcui-ribbon-emptyicon"></span>
                                        <span data-bind="text: res.ribbon.home.unHideColumns">UnHide Columns</span>
                                    </button>
                                </li>
                                <li>
                                    <div class="gcui-ribbon-listseparator"></div>
                                </li>
                                <li>
                                    <button id="protect-sheet" name="protect-sheet" title="Protect Sheet"
                                            data-bind="attr: { title: res.ribbon.home.protectSheet }">
                                        <span class="gcui-ribbon-protectsheet"></span>
                                        <span data-bind="text: res.ribbon.home.protectSheet">Protect Sheet</span>
                                    </button>
                                </li>
                                <li>
                                    <button id="unprotect-sheet" name="unprotect-sheet" title="UnProtect Sheet"
                                            data-bind="attr: { title: res.ribbon.home.unProtectSheet }">
                                        <span class="gcui-ribbon-protectsheet"></span>
                                        <span data-bind="text: res.ribbon.home.unProtectSheet">UnProtect Sheet</span>
                                    </button>
                                </li>
                                <li>
                                    <button id="lock-cells" name="lock-cells" title="Lock Cells"
                                            data-bind="attr: { title: res.ribbon.home.lockCells }">
                                        <span class="gcui-ribbon-lockcells"></span>
                                        <span data-bind="text: res.ribbon.home.lockCells">Lock Cells</span>
                                    </button>
                                </li>
                                <li>
                                    <button id="unlock-cells" name="unlock-cells" title="UnLock Cells"
                                            data-bind="attr: { title: res.ribbon.home.unLockCells }">
                                        <span class="gcui-ribbon-lockcells"></span>
                                        <span data-bind="text: res.ribbon.home.unLockCells">UnLock Cells</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.home.cells">Cells</div>
                </li>
                <li id="editinggroup">
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Auto Sum"
                                    data-bind="attr: { title: res.ribbon.home.autoSum }, text: res.ribbon.home.autoSum"
                                    name="auto-sum" class="gcui-ribbon-autosum">Auto Sum
                            </button>
                            <span>
                                    <button class="ui-button-icon-only gcui-ribbon-smallsplit" title="Auto Sum"
                                            data-bind="attr: { title: res.ribbon.home.autoSum }"></button>
                                    <ul>
                                        <li>
                                            <button name="auto-sum" title="Sum"
                                                    data-bind="attr: { title: res.ribbon.home.sum }">
                                                <span class="gcui-ribbon-autosum"></span>
                                                <span data-bind="text: res.ribbon.home.sum">Sum</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="auto-average" title="Average"
                                                    data-bind="attr: { title: res.ribbon.home.average }">
                                                <span class="gcui-ribbon-emptyicon"></span>
                                                <span data-bind="text: res.ribbon.home.average">Average</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="auto-count" title="Count Numbers"
                                                    data-bind="attr: { title: res.ribbon.home.countNumbers }">
                                                <span class="gcui-ribbon-emptyicon"></span>
                                                <span data-bind="text: res.ribbon.home.countNumbers">Count Numbers</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="auto-max" title="Max"
                                                    data-bind="attr: { title: res.ribbon.home.max }">
                                                <span class="gcui-ribbon-emptyicon"></span>
                                                <span data-bind="text: res.ribbon.home.max">Max</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button name="auto-min" title="Min"
                                                    data-bind="attr: { title: res.ribbon.home.min }">
                                                <span class="gcui-ribbon-emptyicon"></span>
                                                <span data-bind="text: res.ribbon.home.min">Min</span>
                                            </button>
                                        </li>
                                    </ul>
                                </span>
                        </div>
                        <div>
                            <div>
                                <button title="Fill"
                                        data-bind="attr: { title: res.ribbon.home.fill }, text: res.ribbon.home.fill"
                                        class="gcui-ribbon-filldown" name="fill-down">Fill
                                </button>
                                <span>
                                        <button class="ui-button-icon-only gcui-ribbon-smallsplit"></button>
                                        <ul>
                                            <li>
                                                <button name="fill-down"
                                                        data-bind="attr: { title: res.ribbon.home.down }">
                                                    <span class="gcui-ribbon-filldown"></span>
                                                    <span data-bind="text: res.ribbon.home.down">Down</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button name="fill-right"
                                                        data-bind="attr: { title: res.ribbon.home.right }">
                                                    <span class="gcui-ribbon-fillright"></span>
                                                    <span data-bind="text: res.ribbon.home.right">Right</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button name="fill-up" data-bind="attr: { title: res.ribbon.home.up }">
                                                    <span class="gcui-ribbon-fillup"></span>
                                                    <span data-bind="text: res.ribbon.home.up">Up</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button name="fill-left"
                                                        data-bind="attr: { title: res.ribbon.home.left }">
                                                    <span class="gcui-ribbon-fillleft"></span>
                                                    <span data-bind="text: res.ribbon.home.left">Left</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button name="fill-series"
                                                        data-bind="attr: { title: res.ribbon.home.series }">
                                                    <span class="gcui-ribbon-emptyicon"></span>
                                                    <span data-bind="text: res.ribbon.home.series">Series...</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </span>
                            </div>
                        </div>
                        <div>
                            <div>
                                <button title="Clear"
                                        data-bind="attr: { title: res.ribbon.home.clear }, text: res.ribbon.home.clear"
                                        class="gcui-ribbon-clearall" name="clear-all">Clear
                                </button>
                                <span>
                                        <button class="ui-button-icon-only gcui-ribbon-smallsplit"></button>
                                        <ul>
                                            <li>
                                                <button name="clear-all" title="Clear All"
                                                        data-bind="attr: { title: res.ribbon.home.clearAll }">
                                                    <span class="gcui-ribbon-clearall"></span>
                                                    <span data-bind="text: res.ribbon.home.clearAll">Clear All</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button name="clear-format" title="Clear Format"
                                                        data-bind="attr: { title: res.ribbon.home.clearFormat }">
                                                    <span class="gcui-ribbon-clearformat"></span>
                                                    <span data-bind="text: res.ribbon.home.clearFormat">Clear Format</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button name="clear-content" title="Clear Content"
                                                        data-bind="attr: { title: res.ribbon.home.clearContent }">
                                                    <span class="gcui-ribbon-emptyicon"></span>
                                                    <span data-bind="text: res.ribbon.home.clearContent">Clear Content</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button name="clear-comments" title="Clear Comments"
                                                        data-bind="attr: { title: res.ribbon.home.clearComments }">
                                                    <span class="gcui-ribbon-emptyicon"></span>
                                                    <span data-bind="text: res.ribbon.home.clearComments">Clear Comments</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button id="sort-filter" title="Sort&amp;Filter"
                                    data-bind="attr: { title: res.ribbon.home.sortFilter1 }"
                                    class="gcui-ribbon-bigbutton" name="sortfilter" data-button-size="60px">
                                <span class="gcui-ribbon-sortfilter"></span>
                                <span data-bind="text: res.ribbon.home.sortFilter">Sort &amp; Filter</span>
                            </button>
                            <ul>
                                <li>
                                    <button name="sort-AZ" title="Sort A to Z"
                                            data-bind="attr: { title: res.ribbon.home.sortAtoZ }">
                                        <span class="gcui-ribbon-sortAZ"></span>
                                        <span data-bind="text: res.ribbon.home.sortAtoZ">Sort A to Z</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="sort-ZA" title="Sort Z to A"
                                            data-bind="attr: { title: res.ribbon.home.sortZtoA }">
                                        <span class="gcui-ribbon-sortZA"></span>
                                        <span data-bind="text: res.ribbon.home.sortZtoA">Sort Z to A</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="custom-sort" title="Custom Sort"
                                            data-bind="attr: { title: res.ribbon.home.customSort }">
                                        <span class="gcui-ribbon-customsort"></span>
                                        <span data-bind="text: res.ribbon.home.customSort">Custom Sort...</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="set-filter" title="Filter"
                                            data-bind="attr: { title: res.ribbon.home.filter }">
                                        <span class="gcui-ribbon-filter"></span>
                                        <span data-bind="text: res.ribbon.home.filter">Filter</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="clear-filter" title="Clear Filter"
                                            data-bind="attr: { title: res.ribbon.home.clearFilter }">
                                        <span class="gcui-ribbon-clearfilter"></span>
                                        <span data-bind="text: res.ribbon.home.clearFilter">Clear Filter</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="reapply-filter" title="Reapply"
                                            data-bind="attr: { title: res.ribbon.home.reapply }">
                                        <span class="gcui-ribbon-reapplyfilter"></span>
                                        <span data-bind="text: res.ribbon.home.reapply">Reapply</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button id="find-goto" title="Find" data-bind="attr: { title: res.ribbon.home.find }"
                                    class="gcui-ribbon-bigbutton" name="find">
                                <span class="gcui-ribbon-find"></span>
                                <span data-bind="text: res.ribbon.home.find">Find</span>
                            </button>
                            <ul>
                                <li>
                                    <button name="find" title="Find" data-bind="attr: { title: res.ribbon.home.find }">
                                        <span class="gcui-ribbon-smallfind"></span>
                                        <span data-bind="text: res.ribbon.home.find1">Find...</span>
                                    </button>
                                </li>
                                <li>
                                    <button name="goto" title="Go To" data-bind="attr: { title: res.ribbon.home.goto }">
                                        <span class="gcui-ribbon-goto"></span>
                                        <span data-bind="text: res.ribbon.home.goto">Go to...</span>
                                    </button>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.home.editing">Editing</div>
                </li>
            </ul>
        </div>
        <div id="insertTab">
            <ul>
                <li id="tables">
                    <div class="gcui-ribbon-list">
                        <button title="Insert Table" data-button-size="50px"
                                data-bind="attr: { title: res.ribbon.insert.insertTable }" class="gcui-ribbon-bigbutton"
                                name="insert-table">
                            <span class="gcui-ribbon-insert-table"></span>
                            <span data-bind="text: res.ribbon.insert.table">Table</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.insert.table">Tables</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button title="Insert Chart" data-button-size="50px"
                                data-bind="attr: { title: res.ribbon.insert.insertChart }" class="gcui-ribbon-bigbutton"
                                name="insert-chart">
                            <span class="gcui-ribbon-insert-chart ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.insert.chart">Chart</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.insert.chart">Chart</div>
                </li>
                <li id="illustrations">
                    <div class="gcui-ribbon-list">
                        <button title="Insert Picture" data-bind="attr: {title:res.ribbon.insert.insertPicture}"
                                class="gcui-ribbon-bigbutton" name="insert-picture">
                            <span class="gcui-ribbon-insert-picture"></span>
                            <span data-bind="text:res.ribbon.insert.picture">Picture</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.insert.illustrations">Illustr..</div>
                </li>
                <li id="sparklines">
                    <button title="Insert Line Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertLineSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-line">
                        <span class="gcui-ribbon-sparkline-line"></span>
                        <span data-bind="text: res.ribbon.insert.line">Line</span>
                    </button>
                    <button title="Insert Column Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertColumnSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-column">
                        <span class="gcui-ribbon-sparkline-column"></span>
                        <span data-bind="text: res.ribbon.insert.column">Column</span>
                    </button>
                    <button title="Insert Win/Loss Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertWinlossSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-winloss" style="width:55px">
                        <span class="gcui-ribbon-sparkline-winloss"></span>
                        <span data-bind="text: res.ribbon.insert.winloss">Win/Loss</span>
                    </button>
                    <button title="Insert Pie Sparkline" data-bind="attr: {title:res.ribbon.insert.insertPieSparkline}"
                            class="gcui-ribbon-bigbutton" name="spark-pie">
                        <span class="gcui-ribbon-sparkline-pie"></span>
                        <span data-bind="text:res.ribbon.insert.pie">Pie</span>
                    </button>
                    <button title="Insert Area Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertAreaSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-area">
                        <span class="gcui-ribbon-sparkline-area"></span>
                        <span data-bind="text: res.ribbon.insert.area">Area</span>
                    </button>
                    <button title="Insert Scatter Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertScatterSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-scatter">
                        <span class="gcui-ribbon-sparkline-scatter"></span>
                        <span data-bind="text: res.ribbon.insert.scatter">Scatter</span>
                    </button>
                    <button title="Insert Spread Sparkline"
                            data-bind="attr: {title:res.ribbon.insert.insertSpreadSparkline}"
                            class="gcui-ribbon-bigbutton" name="spark-spread" data-button-size="61px">
                        <span class="gcui-ribbon-sparkline-spread"></span>
                        <span data-bind="text:res.ribbon.insert.spread">Spread</span>
                    </button>
                    <button title="Insert Stacked Sparkline"
                            data-bind="attr: {title:res.ribbon.insert.insertStackedSparkline}"
                            class="gcui-ribbon-bigbutton" name="spark-stacked">
                        <span class="gcui-ribbon-sparkline-stacked"></span>
                        <span data-bind="text:res.ribbon.insert.stacked">Stacked</span>
                    </button>
                    <button title="Insert BoxPlot Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertBoxPlotSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-boxplot">
                        <span class="gcui-ribbon-sparkline-boxplot"></span>
                        <span data-bind="text: res.ribbon.insert.boxplot">BoxPlot</span>
                    </button>
                    <button title="Insert Cascade Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertCascadeSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-cascade" data-button-size="61px">
                        <span class="gcui-ribbon-sparkline-cascade"></span>
                        <span data-bind="text: res.ribbon.insert.cascade">Cascade</span>
                    </button>
                    <button title="Insert Pareto Sparkline"
                            data-bind="attr: {title:res.ribbon.insert.insertParetoSparkline}"
                            class="gcui-ribbon-bigbutton" name="spark-pareto">
                        <span class="gcui-ribbon-sparkline-pareto"></span>
                        <span data-bind="text:res.ribbon.insert.pareto">Pareto</span>
                    </button>
                    <button title="Insert Bullet Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertBulletSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-bullet">
                        <span class="gcui-ribbon-sparkline-bullet"></span>
                        <span data-bind="text: res.ribbon.insert.bullet">Bullet</span>
                    </button>
                    <button title="Insert Hbar Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertHbarSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-hbar">
                        <span class="gcui-ribbon-sparkline-hbar"></span>
                        <span data-bind="text: res.ribbon.insert.hbar">Hbar</span>
                    </button>
                    <button title="Insert Vbar Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertVbarSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-vbar">
                        <span class="gcui-ribbon-sparkline-vbar"></span>
                        <span data-bind="text: res.ribbon.insert.vbar">Vbar</span>
                    </button>
                    <button title="Insert Variance Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertVariSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-variance" style="width:55px">
                        <span class="gcui-ribbon-sparkline-variance"></span>
                        <span data-bind="text: res.ribbon.insert.variance">Variance</span>
                    </button>
                    <button title="Insert Month Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertMonthSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-month" style="width:55px">
                        <span class="gcui-ribbon-sparkline-month"></span>
                        <span data-bind="text: res.ribbon.insert.month">Month</span>
                    </button>
                    <button title="Insert Year Sparkline"
                            data-bind="attr: { title: res.ribbon.insert.insertYearSparkline }"
                            class="gcui-ribbon-bigbutton" name="spark-year" style="width:55px">
                        <span class="gcui-ribbon-sparkline-year"></span>
                        <span data-bind="text: res.ribbon.insert.year">Year</span>
                    </button>
                    <div data-bind="text: res.ribbon.insert.sparklines">Sparklines</div>
                </li>
            </ul>
        </div>
        <div id="formulaTab">
            <ul>
                <li id="functions">
                    <button title="Insert Function" data-bind="attr: { title: res.ribbon.formulas.insertFunction1 }"
                            class="gcui-ribbon-bigbutton" name="insert-function">
                        <span class="gcui-ribbon-insertfunction"></span>
                        <span data-bind="text: res.ribbon.formulas.insertFunction">Insert Function</span>
                    </button>
                    <div data-bind="text: res.ribbon.formulas.functions">Functions</div>
                </li>
                <li id="names">
                    <button title="Name Manager" data-bind="attr: { title: res.ribbon.formulas.nameManager1 }"
                            class="gcui-ribbon-bigbutton" name="name-manager">
                        <span class="gcui-ribbon-namemanager"></span>
                        <span data-bind="text: res.ribbon.formulas.nameManager">Name Manager</span>
                    </button>
                    <div data-bind="text: res.ribbon.formulas.names">Names</div>
                </li>
                <!-- V1 runtime doesn't support it, so comment it. Enable it in the future.
                <li id="calculation">
                    <div class="gcui-ribbon-list">
                        <button id="calculate-option" title="Calculation Option" class="gcui-ribbon-bigbutton">
                            <span class="gcui-ribbon-calculateoption"></span>
                            <span>Calculate Option</span>
                        </button>
                        <ul>
                            <li>
                                <input type="radio" id="calculate-auto" name="calculate-options" />
                                <label for="calculate-auto" name="calculate-auto" title="Automatic">Automatic</label>
                            </li>
                            <li>
                                <input type="radio" id="calculate-manual" name="calculate-options" />
                                <label for="calculate-manual" name="calculate-manual" title="Manual">Manual</label>
                            </li>
                        </ul>
                    </div>
                    <button title="Calculate Now" class="gcui-ribbon-bigbutton" name="calculate-now">
                        <span class="gcui-ribbon-calculatenow"></span>
                        <span>Calculate Now</span>
                    </button>
                    <div>Calculation</div>
                </li>
                    -->
            </ul>
        </div>
        <div id="dataTab">
            <ul>
                <li id="sortAndFilter">
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Sort A to Z" data-bind="attr: { title: res.ribbon.data.sortAtoZ }"
                                    class="gcui-ribbon-sortAZ" name="sort-AZ">
                            </button>
                        </div>
                        <div>
                            <button title="Sort Z to A" data-bind="attr: { title: res.ribbon.data.sortZtoA }"
                                    class="gcui-ribbon-sortZA" name="sort-ZA">
                            </button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-list">
                        <button title="Sort" data-bind="attr: { title: res.ribbon.data.customSort }"
                                class="gcui-ribbon-bigbutton" name="custom-sort">
                            <span class="gcui-ribbon-sortbig"></span>
                            <span data-bind="text: res.ribbon.data.sort">Sort</span>
                        </button>
                    </div>
                    <div class="gcui-ribbon-longseparator"></div>
                    <div class="gcui-ribbon-list">
                        <button title="Filter" data-bind="attr: { title: res.ribbon.data.filter }"
                                data-button-size="61px" class="gcui-ribbon-bigbutton" name="set-filter">
                            <span class="gcui-ribbon-filterbig"></span>
                            <span data-bind="text: res.ribbon.data.filter">Filter</span>
                        </button>
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <div>
                                <button class="gcui-ribbon-clearfilter" name="clear-filter" title="Clear Filter"
                                        data-bind="attr: { title: res.ribbon.data.clearFilter }, text: res.ribbon.data.clear">
                                    Clear
                                </button>
                            </div>
                            <div>
                                <button class="gcui-ribbon-reapplyfilter" name="reapply-filter" title="Reapply"
                                        data-bind="attr: { title: res.ribbon.data.reapply }, text: res.ribbon.data.reapply">
                                    Reapply
                                </button>
                            </div>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.data.sortFilter">Sort &amp; Filter</div>
                </li>
                <li>
                    <button title="Data Validation" data-bind="attr: { title: res.ribbon.data.dataValidation1 }"
                            class="gcui-ribbon-bigbutton" name="data-validation" data-button-size="60">
                        <span class="gcui-ribbon-datavalidationbig"></span>
                        <span data-bind="text: res.ribbon.data.dataValidation">Data Validation</span>
                    </button>
                    <div class="gcui-ribbon-splitbutton">
                        <button class="ui-button-icon-only gcui-ribbon-bigbutton" title="Data Validation"
                                data-bind="attr: { title: res.ribbon.data.dataValidation1 }"></button>
                        <ul>
                            <li>
                                <button name="data-validation" title="Data Validation"
                                        data-bind="attr: { title: res.ribbon.data.dataValidation1 }">
                                    <span class="gcui-ribbon-datavalidation"></span>
                                    <span data-bind="text: res.ribbon.data.dataValidation1">Data Validation</span>
                                </button>
                            </li>
                            <li>
                                <button name="circle-invalid" title="Circle Invalid Data"
                                        data-bind="attr: { title: res.ribbon.data.circleInvalidData }">
                                    <span class="gcui-ribbon-emptyicon"></span>
                                    <span data-bind="text: res.ribbon.data.circleInvalidData">Circle Invalid Data</span>
                                </button>
                            </li>
                            <li>
                                <button name="clear-circle" title="Clear Invalid Circles"
                                        data-bind="attr: { title: res.ribbon.data.clearInvalidCircles }">
                                    <span class="gcui-ribbon-emptyicon"></span>
                                    <span data-bind="text: res.ribbon.data.clearInvalidCircles">Clear Invalid Circles</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div data-bind="text: res.ribbon.data.dataTools">Data Tools</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button title="Group" data-bind="attr: { title: res.ribbon.data.group }" data-button-size="65px"
                                class="gcui-ribbon-bigbutton" name="group">
                            <span class="gcui-ribbon-groupbig"></span>
                            <span data-bind="text: res.ribbon.data.group">Group</span>
                        </button>
                        <button title="UnGroup" data-bind="attr: { title: res.ribbon.data.unGroup }"
                                data-button-size="75px" class="gcui-ribbon-bigbutton" name="ungroup">
                            <span class="gcui-ribbon-ungroup"></span>
                            <span data-bind="text: res.ribbon.data.unGroup">UnGroup</span>
                        </button>
                    </div>
                    <div class="gcui-ribbon-longseparator"></div>
                    <div class="gcui-ribbon-list" id="groupDetailAppearance">
                        <div>
                            <button title="Show Detail" data-bind="attr: { title: res.ribbon.data.showDetail }"
                                    name="show-detail">
                                <span class="gcui-ribbon-showdetail"></span>
                                <span data-bind="text: res.ribbon.data.showDetail">Show Detail</span>
                            </button>
                        </div>
                        <div>
                            <button title="Hide Detail" data-bind="attr: { title: res.ribbon.data.hideDetail }"
                                    name="hide-detail">
                                <span class="gcui-ribbon-hidedetail"></span>
                                <span data-bind="text: res.ribbon.data.hideDetail">Hide Detail</span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <span data-bind="text: res.ribbon.data.outline">Outline</span>
                        <button title="Outline" data-bind="attr: { title: res.ribbon.data.outline }"
                                class="gcui-ribbon-indicatoricon" name="group-direction">
                        </button>
                    </div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button id="template-design-mode" title="Enter Template Design Mode" data-checked="false"
                                data-bind="attr: { title: res.ribbon.data.enterTemplate }" class="gcui-ribbon-bigbutton"
                                name="template-design-mode" data-button-size="73px">
                            <span class="gcui-ribbon-template-designmode"></span>
                            <span data-bind="text: res.ribbon.data.template">Template</span>
                        </button>
                    </div>
                    <div class="gcui-ribbon-longseparator"></div>
                    <div class="gcui-ribbon-list"  id="schemaActionList">
                        <div>
                            <button title="Load schema to get a tree view"
                                    data-bind="attr: { title: res.ribbon.data.loadSchemaTitle }" name="loadSchema">
                                <span class="gcui-ribbon-loadSchema"></span>
                                <span data-bind="text: res.ribbon.data.loadSchema">Load Schema</span>
                            </button>
                        </div>
                        <div>
                            <button title="Save schema into a json file"
                                    data-bind="attr: { title: res.ribbon.data.saveSchemaTitle }" name="saveSchema">
                                <span class="gcui-ribbon-saveSchema"></span>
                                <span data-bind="text: res.ribbon.data.saveSchema">Save Schema</span>
                            </button>
                        </div>
                        <div>
                            <button title="Clear BindingPath"
                                    data-bind="attr: { title: res.ribbon.data.clearBindingPath }"
                                    name="clearBindingPath">
                                <span class="gcui-ribbon-clearBindingPath"></span>
                                <span data-bind="text: res.ribbon.data.clearBindingPath">Clear BindingPath</span>
                            </button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-longseparator"></div>
                    <div class="gcui-ribbon-list">
                        <button title="Automatically Generate Data Label"
                                data-bind="attr: { title: res.ribbon.data.autoGenerateLabelTip }, text: res.ribbon.data.autoGenerateLabel"
                                name="auto-generate-label" id="auto-generate-label" class="gcui-ribbon-unchecked">
                            AutoGenerateLabel
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.data.designMode">Design Mode</div>
                </li>
            </ul>
        </div>
        <div id="viewTab">
            <ul>
                <li>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Show/Hide RowHeader"
                                    data-bind="attr: { title: res.ribbon.view.rowHeaderTip }, text: res.ribbon.view.rowHeader"
                                    id="showhide-rowheader" name="showhide-rowheader" class="gcui-ribbon-checked">Row
                                Header
                            </button>
                        </div>
                        <div>
                            <button title="Show/Hide ColumnHeader"
                                    data-bind="attr: { title: res.ribbon.view.columnHeaderTip }, text: res.ribbon.view.columnHeader"
                                    id="showhide-columnheader" name="showhide-columnheader" class="gcui-ribbon-checked">
                                Column Header
                            </button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-longseparator"></div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Show/Hide Vertical Gridline"
                                    data-bind="attr: { title: res.ribbon.view.verticalGridlineTip }, text: res.ribbon.view.verticalGridline"
                                    id="showhide-vgridline" name="showhide-vgridline" class="gcui-ribbon-checked">
                                Vertical Gridline
                            </button>
                        </div>

                        <div>
                            <button title="Show/Hide Horizontal Gridline"
                                    data-bind="attr: { title: res.ribbon.view.horizontalGridlineTip }, text: res.ribbon.view.horizontalGridline"
                                    id="showhide-hgridline" name="showhide-hgridline" class="gcui-ribbon-checked">
                                Horizontal Gridline
                            </button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-longseparator"></div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Show/Hide TabStrip"
                                    data-bind="attr: { title: res.ribbon.view.tabStripTip }, text: res.ribbon.view.tabStrip"
                                    id="showhide-tabstrip" name="showhide-tabstrip" class="gcui-ribbon-checked">TabStrip
                            </button>
                        </div>

                        <div>
                            <button title="Show/Hide NewTab"
                                    data-bind="attr: { title: res.ribbon.view.newTabTip }, text: res.ribbon.view.newTab"
                                    id="showhide-newtab" name="showhide-newtab" class="gcui-ribbon-checked">NewTab
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.view.showHide">Show/Hide</div>

                </li>
                <li>
                    <button title="Zoom" data-bind="attr: { title: res.ribbon.view.zoom }" class="gcui-ribbon-bigbutton"
                            name="zoom">
                        <span class="gcui-ribbon-zoom"></span>
                        <span data-bind="text: res.ribbon.view.zoom">Zoom</span>
                    </button>
                    <button title="100%" class="gcui-ribbon-bigbutton" name="zoom-default">
                        <span class="gcui-ribbon-zoomdefault"></span>
                        <span>100%</span>
                    </button>
                    <button title="Zoom to Selection" data-bind="attr: { title: res.ribbon.view.zoomToSelection1 }"
                            class="gcui-ribbon-bigbutton" data-button-size="110px" name="zoom-selection">
                        <span class="gcui-ribbon-zoomselection"></span>
                        <span data-bind="text: res.ribbon.view.zoomToSelection">ZoomTo Selection</span>
                    </button>
                    <div data-bind="text: res.ribbon.view.zoom">Zoom</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button id="freeze-panes" title="Freeze Panes"
                                data-bind="attr: { title: res.ribbon.view.freezePane1 }" class="gcui-ribbon-bigbutton"
                                data-button-size="72px" name="freeze-panes">
                            <span class="gcui-ribbon-freeze"></span>
                            <span data-bind="text: res.ribbon.view.freezePane">Freeze Pane</span>
                        </button>
                        <ul>
                            <li>
                                <button name="freeze-panes"
                                        data-bind="attr: { title: res.ribbon.view.freezePane1 }, text: res.ribbon.view.freezePane1">
                                    Freeze Panes
                                </button>
                            </li>
                            <li>
                                <button name="freeze-toprow"
                                        data-bind="attr: { title: res.ribbon.view.freezeTopRow }, text: res.ribbon.view.freezeTopRow">
                                    Freeze Top Row
                                </button>
                            </li>
                            <li>
                                <button name="freeze-firstcolumn"
                                        data-bind="attr: { title: res.ribbon.view.freezeFirstColumn }, text: res.ribbon.view.freezeFirstColumn">
                                    Freeze First Column
                                </button>
                            </li>
                            <li>
                                <button name="freeze-bottomrow"
                                        data-bind="attr: { title: res.ribbon.view.freezeBottomRow }, text: res.ribbon.view.freezeBottomRow">
                                    Freeze Bottom Row
                                </button>
                            </li>
                            <li>
                                <button name="freeze-lastcolumn"
                                        data-bind="attr: { title: res.ribbon.view.freezeLastColumn }, text: res.ribbon.view.freezeLastColumn">
                                    Freeze Last Column
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="gcui-ribbon-list">
                        <button title="UnFreeze Panes" data-bind="attr: { title: res.ribbon.view.unFreezePane1 }"
                                class="gcui-ribbon-bigbutton" data-button-size="73px" name="unfreeze-panes">
                            <span class="gcui-ribbon-unfreeze"></span>
                            <span data-bind="text: res.ribbon.view.unFreezePane">UnFreeze Pane</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.view.viewport">Viewport</div>
                </li>
            </ul>
        </div>
        <div id="settingTab">
            <ul>
                <li>
                    <div class="gcui-ribbon-list">
                        <button title="General Setting" data-bind="attr: { title: res.ribbon.setting.generalTip }"
                                class="gcui-ribbon-bigbutton" name="spread-setting-general">
                            <span class="gcui-ribbon-spreadgeneral"></span>
                            <span data-bind="text: res.ribbon.setting.general">General</span>
                        </button>
                    </div>
                    <div class="gcui-ribbon-list">
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="ScrollBars" data-bind="attr: { title: res.ribbon.setting.scrollBars }"
                                    name="spread-setting-scrollbar">
                                <span class="gcui-ribbon-scrollbars"></span>
                                <span data-bind="text: res.ribbon.setting.scrollBars">ScrollBars</span>
                            </button>
                        </div>
                        <div>
                            <button title="TabStrip" data-bind="attr: { title: res.ribbon.setting.tabStrip }"
                                    name="spread-setting-tabstrip">
                                <span class="gcui-ribbon-tabstrip"></span>
                                <span data-bind="text: res.ribbon.setting.tabStrip">TabStrip</span>
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.setting.spreadSetting">Spread Setting</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button title="General Setting" data-bind="attr: { title: res.ribbon.setting.generalTip }"
                                class="gcui-ribbon-bigbutton" name="sheet-setting-general">
                            <span class="gcui-ribbon-sheetgeneral"></span>
                            <span data-bind="text: res.ribbon.setting.general">General</span>
                        </button>
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="GridLines" data-bind="attr: { title: res.ribbon.setting.gridLines }"
                                    name="sheet-setting-gridline">
                                <span class="gcui-ribbon-gridlines"></span>
                                <span data-bind="text: res.ribbon.setting.gridLines">GridLines</span>
                            </button>
                        </div>
                        <div>
                            <button title="Calculation" data-bind="attr: { title: res.ribbon.setting.calculation }"
                                    name="sheet-setting-calculation">
                                <span class="gcui-ribbon-calculation"></span>
                                <span data-bind="text: res.ribbon.setting.calculation">Calculation</span>
                            </button>
                        </div>
                        <div>
                            <button title="Headers" data-bind="attr: { title: res.ribbon.setting.headers }"
                                    name="sheet-setting-headers">
                                <span class="gcui-ribbon-headers"></span>
                                <span data-bind="text: res.ribbon.setting.headers">Headers</span>
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.setting.sheetSetting">Sheet Setting</div>
                </li>
            </ul>
        </div>
        <div id="sparklineTab">
            <ul>
                <li>
                    <div class="gcui-ribbon-list"></div>
                    <input type="radio" name="sparkline-type" id="sparkline-type-line"/>
                    <label for="sparkline-type-line" name="sparkline-type-line"
                           class="gcui-ribbon-sparkline-line gcui-ribbon-bigbutton" title="Line Sparkline"
                           data-bind="attr: { title: res.ribbon.sparkLineDesign.lineTip }, text: res.ribbon.sparkLineDesign.line">Line</label>

                    <input type="radio" name="sparkline-type" id="sparkline-type-column"/>
                    <label for="sparkline-type-column" name="sparkline-type-column"
                           class="gcui-ribbon-sparkline-column gcui-ribbon-bigbutton" title="Column Sparkline"
                           data-bind="attr: { title: res.ribbon.sparkLineDesign.columnTip }, text: res.ribbon.sparkLineDesign.column">Column</label>

                    <input type="radio" name="sparkline-type" id="sparkline-type-winloss"/>
                    <label for="sparkline-type-winloss" name="sparkline-type-winloss"
                           class="gcui-ribbon-sparkline-winloss gcui-ribbon-bigbutton" title="Win/Loss Sparkline"
                           data-bind="attr: { title: res.ribbon.sparkLineDesign.winLossTip }, text: res.ribbon.sparkLineDesign.winLoss">Win/Loss</label>
                    <div data-bind="text: res.ribbon.sparkLineDesign.type">Type</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Toggle Sparkline High Point"
                                    data-bind="attr: { title: res.ribbon.sparkLineDesign.highPointTip }, text: res.ribbon.sparkLineDesign.highPoint"
                                    id="sparkline-high-point" name="sparkline-high-point" class="gcui-ribbon-unchecked">
                                High Point
                            </button>
                        </div>
                        <div>
                            <button title="Toggle Sparkline Low Point"
                                    data-bind="attr: { title: res.ribbon.sparkLineDesign.lowPointTip }, text: res.ribbon.sparkLineDesign.lowPoint"
                                    id="sparkline-low-point" name="sparkline-low-point" class="gcui-ribbon-unchecked">
                                Low Point
                            </button>
                        </div>
                        <div>
                            <button title="Toggle Sparkline Negative Point"
                                    data-bind="attr: { title: res.ribbon.sparkLineDesign.negativePointTip }, text: res.ribbon.sparkLineDesign.negativePoint"
                                    id="sparkline-negative-point" name="sparkline-negative-point"
                                    class="gcui-ribbon-unchecked">Negative Points
                            </button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Toggle Sparkline First Point"
                                    data-bind="attr: { title: res.ribbon.sparkLineDesign.firstPointTip }, text: res.ribbon.sparkLineDesign.firstPoint"
                                    id="sparkline-first-point" name="sparkline-first-point"
                                    class="gcui-ribbon-unchecked">First Point
                            </button>
                        </div>
                        <div>
                            <button title="Toggle Sparkline Last Point"
                                    data-bind="attr: { title: res.ribbon.sparkLineDesign.lastPointTip }, text: res.ribbon.sparkLineDesign.lastPoint"
                                    id="sparkline-last-point" name="sparkline-last-point" class="gcui-ribbon-unchecked">
                                Last Point
                            </button>
                        </div>
                        <div>
                            <button title="Toggle Sparkline Market Point"
                                    data-bind="attr: { title: res.ribbon.sparkLineDesign.markersTip }, text: res.ribbon.sparkLineDesign.markers"
                                    id="sparkline-marker-point" name="sparkline-marker-point"
                                    class="gcui-ribbon-unchecked">Markers
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.sparkLineDesign.show">Show</div>
                </li>
                <li>
                    <div>
                        <button title="Sparkline Color"
                                data-bind="attr: { title: res.ribbon.sparkLineDesign.sparklineColor }"
                                name="sparkline-color" id="sparkline-color">
                            <span class="gcui-ribbon-sparkline-color"></span>
                            <span data-bind="text: res.ribbon.sparkLineDesign.sparklineColor">Sparkline Color</span>
                        </button>
                    </div>
                    <div>
                        <button title="Marker Color" data-bind="attr: { title: res.ribbon.sparkLineDesign.markerColor }"
                                name="sparkline-marker-color">
                            <span class="gcui-ribbon-sparkline-marker-color"></span>
                            <span data-bind="text: res.ribbon.sparkLineDesign.markerColor">Marker Color</span>
                        </button>
                    </div>
                    <div>
                            <span>
                                <button id="sparkline-weight">
                                    <span class="gcui-ribbon-sparkline-weight"></span>
                                    <span data-bind="text: res.ribbon.sparkLineDesign.sparklineWeight">Sparkline Weight</span>
                                </button>
                                <ul>
                                    <li>
                                        <button name="sparkline-weight-dot25">
                                            <span class="gcui-ribbon-sparkline-dot25"></span>
                                            <span>¼ pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-dot5">
                                            <span class="gcui-ribbon-sparkline-dot25"></span>
                                            <span>½ pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-dot75">
                                            <span class="gcui-ribbon-sparkline-dot25"></span>
                                            <span>¾ pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-1">
                                            <span class="gcui-ribbon-sparkline-dot1"></span>
                                            <span>1 pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-1dot5">
                                            <span class="gcui-ribbon-sparkline-dot1"></span>
                                            <span>1½ pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-2dot25">
                                            <span class="gcui-ribbon-sparkline-dot225"></span>
                                            <span>2¼ pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-3">
                                            <span class="gcui-ribbon-sparkline-dot3"></span>
                                            <span>3 pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-4dot5">
                                            <span class="gcui-ribbon-sparkline-dot45"></span>
                                            <span>4½ pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-6">
                                            <span class="gcui-ribbon-sparkline-dot6"></span>
                                            <span>6 pt</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button name="sparkline-weight-custom">
                                            <span class="gcui-ribbon-sparkline-weight-custom"></span>
                                            <span data-bind="text: res.ribbon.sparkLineDesign.customWeight">Custom Weight</span>
                                        </button>
                                    </li>
                                </ul>
                            </span>
                    </div>
                    <div data-bind="text: res.ribbon.sparkLineDesign.style">Style</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Group Selected Sparkline"
                                    data-bind="attr: { title: res.ribbon.sparkLineDesign.groupTip }"
                                    name="sparkline-group">
                                <span class="gcui-ribbon-sparkline-group"></span>
                                <span data-bind="text: res.ribbon.sparkLineDesign.group">Group</span>
                            </button>
                        </div>
                        <div>
                            <button title="UnGroup Selected Sparkline"
                                    data-bind="attr: { title: res.ribbon.sparkLineDesign.unGroupTip }"
                                    name="sparkline-ungroup">
                                <span class="gcui-ribbon-sparkline-ungroup"></span>
                                <span data-bind="text: res.ribbon.sparkLineDesign.unGroup">UnGroup</span>
                            </button>
                        </div>
                        <div>
                            <div>
                                <button title="Clear Selected Sparkline"
                                        data-bind="attr: { title: res.ribbon.sparkLineDesign.clearSparkline }, text: res.ribbon.sparkLineDesign.clear"
                                        name="sparkline-clear" class="gcui-ribbon-clearall">Clear
                                </button>
                                <span>
                                        <button class="ui-button-icon-only gcui-ribbon-smallsplit"></button>
                                        <ul>
                                            <li>
                                                <button name="sparkline-clear" title="Clear Selected Sparkline"
                                                        data-bind="attr: { title: res.ribbon.sparkLineDesign.clearSparkline }">
                                                    <span class="gcui-ribbon-clearall"></span>
                                                    <span data-bind="text: res.ribbon.sparkLineDesign.clearSparkline">Clear Selected Sparkline</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button name="sparkline-clear-group"
                                                        title="Clear Selected Sparkline Groups"
                                                        data-bind="attr: { title: res.ribbon.sparkLineDesign.clearSparklineGroup }">
                                                    <span class="gcui-ribbon-clearall"></span>
                                                    <span data-bind="text: res.ribbon.sparkLineDesign.clearSparklineGroup">Clear Selected Sparkline Groups</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.sparkLineDesign.groups">Groups</div>
                </li>
            </ul>
        </div>
        <div id="formulaSparklineTab">
            <ul>
                <li>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Settings" class="gcui-ribbon-bigbutton" name="formulaSparkline-setting"
                                    data-bind="attr: {title:res.ribbon.formulaSparklineDesign.settings}">
                                <span class="gcui-ribbon-sheetgeneral"></span>
                                <span data-bind="text: res.ribbon.formulaSparklineDesign.settings">Settings</span>
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.formulaSparklineDesign.argument">Argument</div>
                </li>
            </ul>
        </div>
        <div id="tableTab">
            <ul>
                <li>
                    <div class="gcui-ribbon-list">
                        <div>
                            <label data-bind="text: res.ribbon.tableDesign.tableName">Table Name:</label>
                        </div>
                        <div>
                            <input type="text" class="designer-table-name"/>
                        </div>
                        <div>
                            <button name="resize-table" title="Resize Table"
                                    data-bind="attr: { title: res.ribbon.tableDesign.resizeTable}">
                                <span class="gcui-ribbon-resize-table"></span>
                                <span data-bind="text: res.ribbon.tableDesign.resizeTable">Resize Table</span>
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.tableDesign.property">Properties</div>
                </li>
                <li>
                    <button title="Insert Slicer" data-bind="attr: { title: res.ribbon.tableDesign.insertSlicer }"
                            id="insert-slicer-button" class="gcui-ribbon-bigbutton" name="insert-slicer">
                        <span class="gcui-ribbon-table-insert-slicer"></span>
                        <span data-bind="text: res.ribbon.tableDesign.insertSlicer">Insert Slicer</span>
                    </button>
                    <div data-bind="text: res.ribbon.tableDesign.tools">Tools</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Header Row"
                                    data-bind="attr: { title: res.ribbon.tableDesign.headerRow }, text: res.ribbon.tableDesign.headerRow"
                                    id="table-header-row" name="table-header-row" class="gcui-ribbon-checked">Header Row
                            </button>
                        </div>
                        <div>
                            <button title="Total Row"
                                    data-bind="attr: { title: res.ribbon.tableDesign.totalRow }, text: res.ribbon.tableDesign.totalRow"
                                    id="table-total-row" name="table-total-row" class="gcui-ribbon-checked">Total Row
                            </button>
                        </div>
                        <div>
                            <button title="Banded Rows"
                                    data-bind="attr: { title: res.ribbon.tableDesign.bandedRows }, text: res.ribbon.tableDesign.bandedRows"
                                    id="table-banded-rows" name="table-banded-rows" class="gcui-ribbon-checked">Banded
                                Rows
                            </button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="First Column"
                                    data-bind="attr: { title: res.ribbon.tableDesign.firstColumn }, text: res.ribbon.tableDesign.firstColumn"
                                    id="table-first-column" name="table-first-column" class="gcui-ribbon-checked">First
                                Column
                            </button>
                        </div>

                        <div>
                            <button title="Last Column"
                                    data-bind="attr: { title: res.ribbon.tableDesign.lastColumn }, text: res.ribbon.tableDesign.lastColumn"
                                    id="table-last-column" name="table-last-column" class="gcui-ribbon-checked">Last
                                Column
                            </button>
                        </div>
                        <div>
                            <button title="Banded Columns"
                                    data-bind="attr: { title: res.ribbon.tableDesign.bandedColumns }, text: res.ribbon.tableDesign.bandedColumns"
                                    id="table-banded-columns" name="table-banded-columns" class="gcui-ribbon-checked">
                                Banded Columns
                            </button>
                        </div>
                    </div>
                    <div class="gcui-ribbon-list">
                        <div>
                            <button title="Filter Button"
                                    data-bind="attr: { title: res.ribbon.tableDesign.filterButton}, text: res.ribbon.tableDesign.filterButton"
                                    id="table-filter-button" name="table-filter-button" class="gcui-ribbon-checked">
                                Filter Button
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.tableDesign.tableOption">Table Style Options</div>
                </li>
                <li>
                    <button title="Table Styles" data-bind="attr: { title: res.ribbon.tableDesign.tableStyle }"
                            id="table-styles-button" class="gcui-ribbon-bigbutton" name="table-styles">
                        <span class="gcui-ribbon-table-style"></span>
                        <span data-bind="text: res.ribbon.tableDesign.style">Styles</span>
                    </button>
                    <div data-bind="text: res.ribbon.tableDesign.tableStyle">Table Styles</div>
                </li>
            </ul>
        </div>
        <div id="slicerTab">
            <ul>
                <li>
                    <div class="gcui-ribbon-list">
                        <div>
                            <label data-bind="text: res.ribbon.slicerOptions.slicerCaptionShow">Slicer Caption:</label>
                        </div>
                        <div>
                            <input type="text" class="slicer-caption-name" size="16" maxlength="255"
                                   title="Slicer Caption"
                                   data-bind="attr: { title: res.ribbon.slicerOptions.slicerCaption}"/>
                        </div>
                        <div>
                            <button name="slicer-setting" title="Slicer Settings"
                                    data-bind="attr: { title: res.ribbon.slicerOptions.slicerSettings}">
                                <span class="gcui-ribbon-slicer-setting"></span>
                                <span data-bind="text: res.ribbon.slicerOptions.slicerSettings">Slicer Settings</span>
                            </button>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.slicerOptions.slicer">Slicer</div>
                </li>

                <li>
                    <button title="Slicer Styles" data-bind="attr: { title: res.ribbon.slicerOptions.slicerStyles }"
                            id="slicer-styles-button" class="gcui-ribbon-bigbutton" name="slicer-styles">
                        <span class="gcui-ribbon-slicer-styles"></span>
                        <span data-bind="text: res.ribbon.slicerOptions.styles">Styles</span>
                    </button>
                    <div data-bind="text: res.ribbon.slicerOptions.slicerStyles">Slicer Styles</div>
                </li>

                <li>
                    <div class="gcui-ribbon-list">
                        <div title="Column" data-bind="attr: { title: res.ribbon.slicerOptions.columns }">
                            <button data-bind="text: res.ribbon.slicerOptions.columnsShow" class="slicer-ribbon-column gcui-ribbon-slicerbigbutton" data-button-size="57px">
                                Columns:
                            </button>
                            <span>
                                    <input type="text" size="8" id="slicer-column-count"/>
                                </span>
                        </div>
                        <div title="Height" data-bind="attr: { title: res.ribbon.slicerOptions.height }">
                            <button data-bind="text: res.ribbon.slicerOptions.heightShow" class="slicer-ribbon-height gcui-ribbon-slicerbigbutton" data-button-size="57px">
                                Height:
                            </button>
                            <span>
                                    <input type="text" size="8" id="slicer-item-height"/>
                                </span>
                        </div>
                        <div title="Width" data-bind="attr: { title: res.ribbon.slicerOptions.width }">
                            <button data-bind="text: res.ribbon.slicerOptions.widthShow" class="slicer-ribbon-width gcui-ribbon-slicerbigbutton" data-button-size="57px">
                                Width:
                            </button>
                            <span>
                                    <input type="text" size="8" id="slicer-item-width"/>
                                </span>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.slicerOptions.buttons">Buttons</div>
                </li>

                <li>
                    <div class="gcui-ribbon-list">
                        <div title="Height" data-bind="attr: { title: res.ribbon.slicerOptions.shapeHeight }">
                            <button data-bind="text: res.ribbon.slicerOptions.heightShow" class="slicer-ribbon-height gcui-ribbon-slicerbigbutton" data-button-size="57px">
                                Height:
                            </button>
                            <span>
                                    <input type="text" size="8" id="slicer-shape-height"/>
                                </span>
                        </div>
                        <div title="Width" data-bind="attr: { title: res.ribbon.slicerOptions.shapeWidth }">
                            <button data-bind="text: res.ribbon.slicerOptions.widthShow" class="slicer-ribbon-width gcui-ribbon-slicerbigbutton" data-button-size="57px">
                                Width:
                            </button>
                            <span>
                                    <input type="text" size="8" id="slicer-shape-width"/>
                                </span>
                        </div>
                    </div>
                    <div data-bind="text: res.ribbon.slicerOptions.size">Size</div>
                </li>
            </ul>
        </div>
        <div id="chartTab">
            <ul>
                <li>
                    <div class="gcui-ribbon-list">
                        <button id="add-chart-element" title="Add Chart Element"
                                data-bind="attr: { title: res.ribbon.chartDesign.addChartElement1 }"
                                class="gcui-ribbon-bigbutton" data-button-size="70px" name="add-chart-element">
                            <span class="gcui-ribbon-add-chart-element ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.chartDesign.addChartElement">Add Chart Element</span>
                        </button>
                    </div>

                    <div class="gcui-ribbon-list">
                        <button id="quick-layout" title="Quick Layout"
                                data-bind="attr: { title: res.ribbon.chartDesign.quickLayout }"
                                class="gcui-ribbon-bigbutton" data-button-size="70px" name="quick-layout">
                            <span class="gcui-ribbon-quick-layout ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.chartDesign.quickLayout">Quick Layout</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.chartDesign.chartLayouts">Chart Layouts</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button id="change-colors" title="Change Colors"
                                data-bind="attr: { title: res.ribbon.chartDesign.changeColors }"
                                class="gcui-ribbon-bigbutton" data-button-size="70px" name="change-colors">
                            <span class="gcui-ribbon-change-colors ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.chartDesign.changeColors">Change Colors</span>
                        </button>
                    </div>
                    <div class="gcui-ribbon-list">
                        <button id="chart-styles" title="Change Styles"
                                data-bind="attr: { title: res.ribbon.chartDesign.chartStyles }"
                                class="gcui-ribbon-bigbutton" data-button-size="70px" name="change-styles">
                            <span class="gcui-ribbon-chart-styles ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.chartDesign.chartStyles">Change Styles</span>
                        </button>
                    </div>
                    <!--<div class="gcui-ribbon-longseparator"></div>-->
                    <!--<div id="chart-style-list">-->
                    <!--</div>-->
                    <div data-bind="text: res.ribbon.chartDesign.chartStyles">Chart Styles</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button title="Switch Row Column"
                                data-bind="attr: { title: res.ribbon.chartDesign.switchRowColumn }"
                                data-button-size="70px" class="gcui-ribbon-bigbutton" name="switch-row-column">
                            <span class="gcui-ribbon-switch-row-column ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.chartDesign.switchRowColumn">Switch Row/Column</span>
                        </button>

                        <button title="Select Data" data-bind="attr: { title: res.ribbon.chartDesign.selectData }"
                                data-button-size="75px" class="gcui-ribbon-bigbutton" name="select-data">
                            <span class="gcui-ribbon-selectData ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.chartDesign.selectData">Select Data</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.chartDesign.data">Data</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button title="Change Chart Type"
                                data-bind="attr: { title: res.ribbon.chartDesign.changeChartType }"
                                data-button-size="65px" class="gcui-ribbon-bigbutton" name="change-chart-type">
                            <span class="gcui-ribbon-change-chart-type ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.chartDesign.changeChartType">Change Chart Type</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.chartDesign.type">Type</div>
                </li>
                <li>
                    <div class="gcui-ribbon-list">
                        <button title="Move Chart" data-bind="attr: { title: res.ribbon.chartDesign.moveChart }"
                                data-button-size="65px" class="gcui-ribbon-bigbutton" name="move-chart">
                            <span class="gcui-ribbon-move-chart ui-chart-icon"></span>
                            <span data-bind="text: res.ribbon.chartDesign.moveChart">Move Chart</span>
                        </button>
                    </div>
                    <div data-bind="text: res.ribbon.chartDesign.location">Location</div>
                </li>
            </ul>
        </div>
    </div>

    <div id="backcolor-popup" class="designer-colorpicker">
        <div id="backcolor-picker"></div>
    </div>
    <div id="forecolor-popup" class="designer-colorpicker">
        <div id="forecolor-picker"></div>
    </div>
    <div id="sparklinecolor-popup" class="designer-colorpicker">
        <div id="sparklinecolor-picker"></div>
    </div>
    <div id="label-color-popup" class="designer-colorpicker">
        <div id="label-color-picker"></div>
    </div>
    <div id="condition-format-popup">
        <ul id="condition-format-popup-menu"
            class="hidden ui-helper-reset ui-helper-clearfix ui-corner-all gcui-ribbon-dropdown">
            <li class="ui-corner-all">
                <button name="highlight-cells-rules"
                        class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                    <span class="ui-icon-big gcui-ribbon-highlight-cells-rules ui-button-icon-primary"></span>
                    <span class="ui-button-text-big menu-has-subitem"
                          data-bind="text: res.conditionalFormat.highlightCellsRules">Highlight Cells Rules</span>
                    <div class="align-right-icon-container">
                        <span class="ui-icon menu-right-arrow"></span>
                    </div>
                </button>
            </li>
            <li class="ui-corner-all">
                <button name="top-bottom-rules"
                        class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                    <span class="ui-icon-big gcui-ribbon-top-bottom-rules ui-button-icon-primary"></span>
                    <span class="ui-button-text-big menu-has-subitem"
                          data-bind="text: res.conditionalFormat.topBottomRules">Top/Bottom Rules</span>
                    <div class="align-right-icon-container">
                        <span class="ui-icon menu-right-arrow"></span>
                    </div>
                </button>
            </li>

            <li class="ui-corner-all">
                <div class="gcui-ribbon-listseparator condition-format"></div>
            </li>

            <li class="ui-corner-all">
                <button name="data-bars" class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                    <span class="ui-icon-big gcui-ribbon-data-bars ui-button-icon-primary"></span>
                    <span class="ui-button-text-big menu-has-subitem" data-bind="text: res.conditionalFormat.dataBars">Data Bars</span>
                    <div class="align-right-icon-container">
                        <span class="ui-icon menu-right-arrow"></span>
                    </div>
                </button>
            </li>
            <li class="ui-corner-all">
                <button name="color-scales" class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                    <span class="ui-icon-big gcui-ribbon-color-scales ui-button-icon-primary"></span>
                    <span class="ui-button-text-big menu-has-subitem"
                          data-bind="text: res.conditionalFormat.colorScales">Color Scales</span>
                    <div class="align-right-icon-container">
                        <span class="ui-icon menu-right-arrow"></span>
                    </div>
                </button>
            </li>
            <li class="ui-corner-all">
                <button name="icon-sets" class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                    <span class="ui-icon-big gcui-ribbon-icon-sets ui-button-icon-primary"></span>
                    <span class="ui-button-text-big menu-has-subitem" data-bind="text: res.conditionalFormat.iconSets">Icon Sets</span>
                    <div class="align-right-icon-container">
                        <span class="ui-icon menu-right-arrow"></span>
                    </div>
                </button>
            </li>

            <li class="ui-corner-all">
                <div class="gcui-ribbon-listseparator"></div>
            </li>

            <li class="ui-corner-all">
                <button name="new-rule" class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                    <span class="ui-icon-small gcui-ribbon-new-rule ui-button-icon-primary"></span>
                    <span class="ui-button-text-small"
                          data-bind="text: res.conditionalFormat.newRule">New Rule...</span>
                </button>
            </li>
            <li class="ui-corner-all">
                <button name="clear-rules" class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                    <span class="ui-icon-small gcui-ribbon-clear-rules ui-button-icon-primary"></span>
                    <span class="ui-button-text-small menu-has-subitem"
                          data-bind="text: res.conditionalFormat.clearRules">Clear Rules...</span>
                    <div class="align-right-icon-container">
                        <span class="ui-icon menu-right-arrow"></span>
                    </div>
                </button>
            </li>
            <li class="ui-corner-all">
                <button name="manage-rules" class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                    <span class="ui-icon-small gcui-ribbon-manage-rules ui-button-icon-primary"></span>
                    <span class="ui-button-text-small" data-bind="text: res.conditionalFormat.manageRules">Manage Rules..</span>
                </button>
            </li>
        </ul>


        <div id="highlight-cells-rules-popup">
            <ul id="highlight-cells-rules-popup-menu"
                class="hidden ui-helper-reset ui-helper-clearfix ui-corner-all gcui-ribbon-dropdown">
                <li class="ui-corner-all">
                    <button name="highlight-cells-rules-greater-than"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-greater-than ui-button-icon-primary"></span>
                        <span class="ui-button-text-big" data-bind="text: res.conditionalFormat.greaterThan">Greater Than...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="highlight-cells-rules-less-than"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-less-than ui-button-icon-primary"></span>
                        <span class="ui-button-text-big"
                              data-bind="text: res.conditionalFormat.lessThan">Less Than...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="highlight-cells-rules-between"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-between ui-button-icon-primary"></span>
                        <span class="ui-button-text-big"
                              data-bind="text: res.conditionalFormat.between">Between...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="highlight-cells-rules-equal-to"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-equal-to ui-button-icon-primary"></span>
                        <span class="ui-button-text-big"
                              data-bind="text: res.conditionalFormat.equalTo">Equal To...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="highlight-cells-rules-text-contains"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-text-contains ui-button-icon-primary"></span>
                        <span class="ui-button-text-big" data-bind="text: res.conditionalFormat.textThatContains">Text that Contains...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="highlight-cells-rules-a-date-occurring"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-a-date-occurring ui-button-icon-primary"></span>
                        <span class="ui-button-text-big" data-bind="text: res.conditionalFormat.aDateOccurring">A Date Occurring...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="highlight-cells-rules-duplicate-values"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-duplicate-values ui-button-icon-primary"></span>
                        <span class="ui-button-text-big" data-bind="text: res.conditionalFormat.duplicateValues">Duplicate Values...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <div class="gcui-ribbon-listseparator  highlight-cells-rules"></div>
                </li>
                <li class="ui-corner-all">
                    <button name="highlight-cells-rules-more-rules"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-button-text-small" data-bind="text: res.conditionalFormat.moreRules">More Rules...</span>
                    </button>
                </li>
            </ul>
        </div>
        <div id="top-bottom-rules-popup">
            <ul id="top-bottom-rules-popup-menu"
                class="hidden ui-helper-reset ui-helper-clearfix ui-corner-all gcui-ribbon-dropdown">
                <li class="ui-corner-all">
                    <button name="top-bottom-rules-top-10-items"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-top-10-items ui-button-icon-primary"></span>
                        <span class="ui-button-text-big" data-bind="text: res.conditionalFormat.top10Items">Top 10 Items...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="top-bottom-rules-bottom-10-items"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-bottom-10-items ui-button-icon-primary"></span>
                        <span class="ui-button-text-big" data-bind="text: res.conditionalFormat.bottom10Items">Bottom 10 Items...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="top-bottom-rules-above-average"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-above-average ui-button-icon-primary"></span>
                        <span class="ui-button-text-big" data-bind="text: res.conditionalFormat.aboveAverage">Above Average...</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="top-bottom-rules-below-average"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-icon-big gcui-ribbon-below-average ui-button-icon-primary"></span>
                        <span class="ui-button-text-big" data-bind="text: res.conditionalFormat.belowAverage">Below Average...</span>
                    </button>
                </li>

                <li class="ui-corner-all">
                    <div class="gcui-ribbon-listseparator top-bottom-rules"></div>
                </li>
                <li class="ui-corner-all">
                    <button name="top-bottom-rules-more-rules"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-button-text-small" data-bind="text: res.conditionalFormat.moreRules">More Rules...</span>
                    </button>
                </li>
            </ul>
        </div>

        <div id="data-bars-popup">
            <ul id="data-bars-popup-menu"
                class="hidden ui-helper-reset ui-helper-clearfix ui-corner-all gcui-ribbon-dropdown">
                <li class="ui-corner-all submenu-title">
                    <span data-bind="text: res.conditionalFormat.gradientFill">Gradient Fill</span>
                </li>
                <li class="ui-corner-all">
                    <div class="horizontal-3-big-icon-button-container">
                        <button name="gradient-fill-blue-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gradient-fill-blue-data-bar"></span>
                                </span>
                        </button>
                        <button name="gradient-fill-green-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gradient-fill-green-data-bar"></span>
                                </span>
                        </button>
                        <button name="gradient-fill-red-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gradient-fill-red-data-bar"></span>
                                </span>
                        </button>
                        <button name="gradient-fill-orange-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gradient-fill-orange-data-bar"></span>
                                </span>
                        </button>
                        <button name="gradient-fill-lightblue-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gradient-fill-lightblue-data-bar"></span>
                                </span>
                        </button>
                        <button name="gradient-fill-purple-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gradient-fill-purple-data-bar"></span>
                                </span>
                        </button>
                    </div>
                </li>
                <li class="ui-corner-all submenu-title">
                    <span data-bind="text: res.conditionalFormat.solidFill">Solid Fill</span>
                </li>
                <li class="ui-corner-all">
                    <div class="horizontal-3-big-icon-button-container">
                        <button name="solid-fill-blue-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-solid-fill-blue-data-bar"></span>
                                </span>
                        </button>
                        <button name="solid-fill-green-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-solid-fill-green-data-bar"></span>
                                </span>
                        </button>
                        <button name="solid-fill-red-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-solid-fill-red-data-bar"></span>
                                </span>
                        </button>
                        <button name="solid-fill-orange-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-solid-fill-orange-data-bar"></span>
                                </span>
                        </button>
                        <button name="solid-fill-lightblue-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-solid-fill-lightblue-data-bar"></span>
                                </span>
                        </button>
                        <button name="solid-fill-purple-data-bar"
                                class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-solid-fill-purple-data-bar"></span>
                                </span>
                        </button>
                    </div>
                </li>
                <li class="ui-corner-all">
                    <div class="gcui-ribbon-listseparator data-bars"></div>
                </li>
                <li class="ui-corner-all">
                    <button name="data-bars-more-rules"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-button-text-small" data-bind="text: res.conditionalFormat.moreRules">More Rules...</span>
                    </button>
                </li>
            </ul>
        </div>
        <div id="color-scales-popup">
            <ul id="color-scales-popup-menu"
                class="hidden ui-helper-reset ui-helper-clearfix ui-corner-all gcui-ribbon-dropdown">
                <li class="ui-corner-all">
                    <div class="horizontal-4-big-icon-button-container">
                        <button name="gyr-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gyr-color-scale"></span>
                                </span>
                        </button>
                        <button name="ryg-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-ryg-color-scale"></span>
                                </span>
                        </button>
                        <button name="gwr-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gwr-color-scale"></span>
                                </span>
                        </button>
                        <button name="rwg-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-rwg-color-scale"></span>
                                </span>
                        </button>

                        <button name="bwr-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-bwr-color-scale"></span>
                                </span>
                        </button>
                        <button name="rwb-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-rwb-color-scale"></span>
                                </span>
                        </button>
                        <button name="wr-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-wr-color-scale"></span>
                                </span>
                        </button>
                        <button name="rw-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-rw-color-scale"></span>
                                </span>
                        </button>

                        <button name="gw-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gw-color-scale"></span>
                                </span>
                        </button>
                        <button name="wg-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-wg-color-scale"></span>
                                </span>
                        </button>
                        <button name="gy-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-gy-color-scale"></span>
                                </span>
                        </button>
                        <button name="yg-color-scale" class="ui-button big-icon-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon gcui-ribbon-yg-color-scale"></span>
                                </span>
                        </button>
                    </div>
                </li>
                <li class="ui-corner-all">
                    <div class="gcui-ribbon-listseparator icon-sets"></div>
                </li>
                <li class="ui-corner-all">
                    <button name="color-scales-more-rules"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-button-text-small" data-bind="text: res.conditionalFormat.moreRules">More Rules...</span>
                    </button>
                </li>
            </ul>
        </div>
        <div id="icon-sets-popup">
            <ul id="icon-sets-popup-menu"
                class="hidden ui-helper-reset ui-helper-clearfix ui-corner-all gcui-ribbon-dropdown">
                <li class="ui-corner-all submenu-title">
                    <span data-bind="text: res.conditionalFormat.directional">Directional</span>
                </li>
                <li class="ui-corner-all">
                    <div class="horizontal-2-small-iconset-button-container">
                        <button name="3-arrows-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-up-arrow-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-arrow-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-down-arrow-red"></span>
                                </span>
                        </button>
                        <button name="3-arrows-gray-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-up-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-down-arrow-gray"></span>
                                </span>
                        </button>
                        <button name="3-triangles-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-up-triangle-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-minus-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-down-triangle-red"></span>
                                </span>
                        </button>
                        <button name="4-arrows-gray-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-up-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-up-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-down-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-down-arrow-gray"></span>
                                </span>
                        </button>

                        <button name="4-arrows-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-up-arrow-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-up-arrow-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-down-arrow-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-down-arrow-red"></span>
                                </span>
                        </button>
                        <button name="5-arrows-gray-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-up-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-up-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-down-arrow-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-down-arrow-gray"></span>
                                </span>
                        </button>
                        <button name="5-arrows-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-up-arrow-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-up-arrow-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-arrow-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-right-down-arrow-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-down-arrow-red"></span>
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
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-red"></span>
                                </span>
                        </button>
                        <button name="3-traffic-lights-rimmed-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-rimmed-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-rimmed-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-rimmed-red"></span>
                                </span>
                        </button>
                        <button name="3-signs-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-up-triangle-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-down-rhombus-red"></span>
                                </span>
                        </button>
                        <button name="4-traffic-lights-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-red"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-traffic-light-black"></span>
                                </span>
                        </button>
                        <button name="red-to-black-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-ball-red"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-ball-pink"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-ball-gray"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-ball-black"></span>
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
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-check-circled-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-notice-circled-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-close-circled-red"></span>
                                </span>
                        </button>
                        <button name="3-symbols-uncircled-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-check-uncircled-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-notice-uncircled-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-close-uncircled-red"></span>
                                </span>
                        </button>
                        <button name="3-flags-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-flag-green"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-flag-yellow"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-flag-red"></span>
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
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-star-solid"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-star-half"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-star-hollow"></span>
                                </span>
                        </button>
                        <button name="4-ratings-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-3"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-2"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-1"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-0"></span>
                                </span>
                        </button>
                        <button name="5-quarters-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-quarters-4"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-quarters-3"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-quarters-2"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-quarters-1"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-quarters-0"></span>
                                </span>
                        </button>
                        <button name="5-ratings-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-4"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-3"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-2"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-1"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-rating-0"></span>
                                </span>
                        </button>
                        <button name="5-boxes-icon-set"
                                class="ui-button small-icon-set-only ui-widget ui-state-default">
                                <span class="icon-button-container">
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-box-4"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-box-3"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-box-2"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-box-1"></span>
                                    <span class="ui-icon horizontal-icon-set-item gcui-ribbon-box-0"></span>
                                </span>
                        </button>
                    </div>
                </li>
                <li class="ui-corner-all">
                    <div class="gcui-ribbon-listseparator"></div>
                </li>
                <li class="ui-corner-all">
                    <button name="icon-sets-more-rules"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-button-text-small" data-bind="text: res.conditionalFormat.moreRules">More Rules...</span>
                    </button>
                </li>
            </ul>
        </div>
        <div id="clear-rules-popup">
            <ul id="clear-rules-popup-menu"
                class="hidden ui-helper-reset ui-helper-clearfix ui-corner-all gcui-ribbon-dropdown">
                <li class="ui-corner-all">
                    <button name="clear-rules-from-cells"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-button-text-small"
                              data-bind="text: res.conditionalFormat.clearRulesFromSelectedCells">Clear Rules from Selected Cells</span>
                    </button>
                </li>
                <li class="ui-corner-all">
                    <button name="clear-rules-from-sheet"
                            class="ui-button ui-widget ui-state-default ui-button-text-icon-primary">
                        <span class="ui-button-text-small"
                              data-bind="text: res.conditionalFormat.clearRulesFromEntireSheet">Clear Rules from Entire Sheet</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <div id="format-table-popup" class="designer-table-format hidden">

        <div class="custom-format-table table-format-category-div">
            <div class="table-format-label-container">
                <label class="table-format-label" data-bind="text: res.formatTableStyle.custom">Custom</label>
            </div>
            <div class="custom-preview"></div>
        </div>

        <div class="light-format-table table-format-category-div">
            <div class="table-format-label-container">
                <label class="table-format-label" data-bind="text: res.formatTableStyle.light">Light</label>
            </div>
            <div class="light-preview"></div>
        </div>

        <div class="medium-format-table table-format-category-div">
            <div class="table-format-label-container">
                <label class="table-format-label" data-bind="text: res.formatTableStyle.medium">Medium</label>
            </div>
            <div class="medium-preview"></div>
        </div>

        <div class="dark-format-table table-format-category-div">
            <div class="table-format-label-container">
                <label class="table-format-label" data-bind="text: res.formatTableStyle.dark">Dark</label>
            </div>
            <div class="dark-preview"></div>
        </div>

        <div class="new-table-style-div">
            <button class="ui-button ui-widget ui-state-default ui-button-text-icon-primary new-table-style"
                    name="new-table-style">
                <span class="ui-button-icon-primary new-table-style-span"></span>
                <span class="ui-button-text-small new-table-style-text"
                      data-bind="text: res.formatTableStyle.newTableStyle">New Table Style...</span>
            </button>
        </div>
    </div>
    <div id="cell-styles-popup" class="designer-cell-styles hidden">
        <ul class="cell-styles-popup-menu">
            <li class="submenu-title cell-style-custom-li hidden">
                <span data-bind="text: res.cellStylesDialog.custom">Custom</span>
            </li>
            <li>
                <div class="custom-cell-styles-preview sub-cellstyle-category"></div>
            </li>
            <li class="submenu-title">
                <span data-bind="text: res.cellStylesDialog.goodBadAndNeutral">Good, Bad and Neutral</span>
            </li>
            <li>
                <div class="good-bad-style-preview sub-cellstyle-category"></div>
            </li>
            <li class="submenu-title">
                <span data-bind="text: res.cellStylesDialog.dataAndModel">Data And Model</span>
            </li>
            <li>
                <div class="data-model-style-preview sub-cellstyle-category"></div>
            </li>
            <li class="submenu-title">
                <span data-bind="text: res.cellStylesDialog.titlesAndHeadings">Titles and Headings</span>
            </li>
            <li>
                <div class="titles-headings-preview sub-cellstyle-category"></div>
            </li>
            <li class="submenu-title">
                <span data-bind="text: res.cellStylesDialog.themedCellStyle">Themed Cell Style</span>
            </li>
            <li>
                <div class="themed-preview sub-cellstyle-category"></div>
            </li>
            <li class="submenu-title">
                <span data-bind="text: res.cellStylesDialog.numberFormat">Number Format</span>
            </li>
            <li>
                <div class="number-format-preview sub-cellstyle-category"></div>
            </li>
            <li class="ui-corner-all">
                <div class="gcui-ribbon-listseparator"></div>
            </li>
            <li>
                <button class="ui-button ui-widget ui-state-default ui-button-text-icon-primary new-cell-style-button"
                        name="new-cell-style">
                    <span class="ui-icon-small gcui-ribbon-new-cell-style ui-button-icon-primary"></span>
                    <span class="ui-button-text-small new-cell-style-text"
                          data-bind="text: res.cellStylesDialog.newCellStyle">New Cell Style...</span>
                </button>
            </li>
        </ul>


        <div id="cellstyle-menu-container">
            <ul class="cellstyle-menu hidden">
                <li class="cellstyle-modify" id="cellstyle-modify"><a href="#"><span
                        class="cellstyle-icon-empty"></span><span class="cellstyle-menu-text"
                                                                  data-bind="text: res.cellStyleContextMenu.modify"></span></a>
                </li>
                <li class="cellstyle-delete" id="cellstyle-delete"><a href="#"><span
                        class="cellstyle-icon-empty"></span><span class="cellstyle-menu-text"
                                                                  data-bind="text: res.cellStyleContextMenu.delete"></span></a>
                </li>
            </ul>
        </div>
    </div>
    <div id="format-slicer-popup" class="designer-slicer-format hidden">

        <div class="custom-format-slicer slicer-format-category-div">
            <div class="slicer-format-label-container">
                <label class="slicer-format-label" data-bind="text: res.formatSlicerStyle.custom">Custom</label>
            </div>
            <div class="slicer-custom-preview"></div>
        </div>

        <div class="light-format-slicer slicer-format-category-div">
            <div class="slicer-format-label-container">
                <label class="slicer-format-label" data-bind="text: res.formatSlicerStyle.light">Light</label>
            </div>
            <div class="slicer-light-preview"></div>
        </div>

        <div class="dark-format-slicer slicer-format-category-div">
            <div class="slicer-format-label-container">
                <label class="slicer-format-label" data-bind="text: res.formatSlicerStyle.dark">Dark</label>
            </div>
            <div class="slicer-dark-preview"></div>
        </div>

        <div class="new-slicer-style-div">
            <button class="ui-button ui-widget ui-state-default ui-button-text-icon-primary new-slicer-style"
                    name="new-slicer-style">
                <span class="ui-button-icon-primary new-slicer-style-span"></span>
                <span class="ui-button-text-small new-slicer-style-text"
                      data-bind="text: res.formatSlicerStyle.newSlicerStyle">New Slicer Style...</span>
            </button>
        </div>
    </div>

    <div id="chart-color-popup" class="designer-chart-color designer-colorpicker">
        <div id="chart-color-picker"></div>
    </div>
    <div id="ribbon-chart-layout-list-popup">
        <div class="chart-layout-list-container">
        </div>
    </div>

    <div id="ribbon-chart-styles-list-popup" hidden="hidden">
        <div id="chart-styles-list-container">
        </div>
    </div>

    <div id="add-chart-element-popup" class="designer-addChartElement">
        <div id="add-chart-element-container">
            <div id="add-chart-element-submenu" class="designer-addChartElement"></div>
        </div>
    </div>

    <div id="data-binding-setting-indicator" hidden="hidden">
        <span class="designer-data-binding-icon"></span>
    </div>
    <div class="data-binding-path-overlay" hidden="hidden"></div>
    <div class="data-binding-decoration-container"></div>
    <div class="data-binding-celltype-option" hidden="hidden">
        <div class="data-binding-button-container">
            <button class="data-binding-celltype-button" data-datafieldtype="table">
                <span class="data-binding data-binding-table-icon"></span>
                <span data-bind="text: res.ribbon.data.table">Table</span>
            </button>
        </div>
        <div class="data-binding-button-container">
            <button class="data-binding-celltype-button" data-datafieldtype="checkbox">
                <span class="data-binding data-binding-checkbox-icon"></span>
                <span data-bind="text: res.ribbon.data.checkbox">CheckBox</span>
            </button>
        </div>
        <div class="data-binding-button-container">
            <button class="data-binding-celltype-button" data-datafieldtype="hyperlink">
                <span class="data-binding data-binding-hyperlink-icon"></span>
                <span data-bind="text: res.ribbon.data.hyperlink">HyperLink</span>
            </button>
        </div>
        <div class="data-binding-button-container">
            <button class="data-binding-celltype-button" data-datafieldtype="combox">
                <span class="data-binding data-binding-combox-icon"></span>
                <span data-bind="text: res.ribbon.data.combox">Combox</span>
            </button>
        </div>
        <div class="data-binding-button-container">
            <button class="data-binding-celltype-button" data-datafieldtype="button">
                <span class="data-binding data-binding-button-icon"></span>
                <span data-bind="text: res.ribbon.data.button">Button</span>
            </button>
        </div>
        <div class="data-binding-button-container">
            <button class="data-binding-celltype-button" data-datafieldtype="text">
                <span class="data-binding data-binding-text-icon"></span>
                <span data-bind="text: res.ribbon.data.text">Text</span>
            </button>
        </div>
    </div>
    <div id="data-binding-setting-popup" hidden="hidden">
        <div class="table-level-data-binding">
            <div>
                <label class="table-binding-block1"
                       data-bind="text: res.ribbon.data.bindingPath + ':'">BindingPath:</label>
                <input class="table-binding-path-input table-binding-block2" disabled="disabled"/>
            </div>
            <div class="auto-generate-columns-container">
                <input type="checkbox" class="auto-generate-columns" id="auto-generate-columns"/>
                <label for="auto-generate-columns" data-bind="text: res.ribbon.data.autoGenerateColumns">AutoGenerateColumns</label>
            </div>
            <div class="columninfo-fieldset-container">
                <fieldset class="table-binding-columninfo-fieldset">
                    <legend data-bind="text: res.ribbon.data.dataField">DataField</legend>
                    <div class="table-column-data-field"></div>
                </fieldset>
            </div>
            <div>
                <button class="cancel-table-binding-button table-binding-setting-button">
                    <span></span>
                    <span data-bind="text: res.ribbon.data.cancel">Cancel</span>
                </button>
                <button class="ok-table-binding-button table-binding-setting-button">
                    <span></span>
                    <span data-bind="text:res.ribbon.data.ok">Ok</span>
                </button>
            </div>
        </div>
    </div>
</div>