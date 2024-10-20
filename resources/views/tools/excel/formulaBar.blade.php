
    <div class="container">
        <div class="header">
            <div>
                <div class="formulaBar">
                    <table>
                        <tr>
                            <td>
                                <div class="union-container formula-bar-item">
                                    <input type="text" readonly="readonly" name="namedRangeSelector" class="named-range-selector union-item" />
                                    <!--<span class="currentSelection union-item">A1</span>-->
                                </div>
                            </td>
                            <td>
                                <span class="splitter formula-bar-item ui-draggable"></span>
                            </td>
                            <td>
                                <div class="btn-group formula-bar-item">
                                    <button class="cancel-btn"></button>
                                    <button class="ok-btn"></button>
                                    <button class="functions-btn"></button>
                                </div>
                            </td>
                            <td>
                                <div class="formulaBar-container">
                                    <div id="formulaBarText" contenteditable="true" spellcheck="false" class="formula-bar-item fill-content"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="content" style="height: 500px">
            <div class="fill-content" style="height: 100%">
                @include('tools.excel.spreadWrapper')
                <span>spread</span></div>
        </div>
    </div>


