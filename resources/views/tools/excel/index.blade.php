<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <!-- disable IE compatible view -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <title>Mutima|Docs</title>

    <style type="text/css">
        :root{
            --color-main:#3c4ba6; /*#217346*/;
            --menu-selected:#0d1a66; /* #9fd5b7*/;
            --menu-hover:#091a7d; /*#d3f0e0;*/
            --menu-nohover: #091a7d; /*#0a6332*/;
        }
    </style>
    <!-- Libraries -->
    <link href="{{ asset('assets/excel/lib/jquery-ui/css/smoothness/jquery-ui-1.10.3.custom.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/excel/lib/gcui.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/excel/lib/spread/gc.spread.sheets.excel2013white.12.0.0.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/excel/lib/zTreeStyle/css/zTreeStyle.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/excel/css/gc.spread.sheets.designer.12.0.0.css')}}" rel="stylesheet" type="text/css">
    <style type="text/css">
        .mytoolbar{
            padding: 10px;
            width: 100%;
            padding-top: 2px;
            padding-bottom: 2px;
            display: flex;
            flex-direction: row;
            position: absolute;
        }
        .title{
            font-family: sans-serif;
            font-size: large;
            color: maroon;
            letter-spacing: 1px;
            font-weight: lighter;
            padding-right: 3rem;
        }
        .title span{
            font-weight: bold;
        }
        .buttons{
            position: absolute;
            display: flex;
            padding-right: 1rem;
            left: 68%;
            margin-right: 1%;
            z-index: 1;
            border: none;
            justify-content: safe flex-end;
        }
        .custom-btn{
            width: 6rem;
            margin-right: 5px;
            padding: 1px;
            border: none;
            background-color: var(--color-main);
            color: white;
            border-radius: 2px;
            cursor: pointer;
            max-height: 28px;
        }
        .btn-save{
            padding: 2px;
            width:width: 70%; ;
            margin: 20px;
            margin-top: 2px;
            float: right;
            margin-right: 12%;
        }
        .topmost{
            z-index: 10!important;
            top: -5px!important;
            background-color: #217346!important;
        }
        .custom-input{
            width: 80%;
            border-radius: 0px;
            border: 1px grey solid;
            padding: 10px;
        }
    </style>
</head>
<body unselectable="on" style="-moz-user-select: none; -webkit-user-select: none; user-select: none;" class="myapp">

    <div class="container">
      <div class="mytoolbar">
            
            <div class="buttons">
                <div class="title">
                <img src="{{ asset('assets/excel/mutimadocs.jpg')}}" width="80" />
            </div>

               <!-- <select name="people" id="people">
                  <option value="1" data-class="avatar" data-style="background-image: url(&apos;http://www.gravatar.com/avatar/b3e04a46e85ad3e165d66f5d927eb609?d=monsterid&amp;r=g&amp;s=16&apos;);">John Resig</option>
                  <option value="2" data-class="avatar" data-style="background-image: url(&apos;http://www.gravatar.com/avatar/e42b1e5c7cfd2be0933e696e292a4d5f?d=monsterid&amp;r=g&amp;s=16&apos;);">Tauren Mills</option>
                  <option value="3" data-class="avatar" data-style="background-image: url(&apos;http://www.gravatar.com/avatar/bdeaec11dd663f26fa58ced0eb7facc8?d=monsterid&amp;r=g&amp;s=16&apos;);">Jane Doe</option>
                </select> -->

              <button class="custom-btn" >Close</button>
              <button class="custom-btn" >Share</button>
              <button class="custom-btn" onclick="save(true)">Save</button>
            </div>
        </div>
        <div class="loading-placeholder background-mask-layer topmost"></div>
        <span class="loading-placeholder loading-pic"></span>
        <div class="header">
            
            @include('tools.excel.ribbon')
            @include('tools.excel.formulaBar')
            
        </div>
        <div class="content">
            <div class="vertical-splitter ui-draggable hidden" id="verticalSplitter"></div>
            <div class="fill-spread-content">
                @include('tools.excel.spreadWrapper')
            </div>
        </div>
        <div class="footer">
            @include('tools.excel.statusBar')
        </div>
        <div class="file-menu hidden">
            @include('tools.excel.fileMenu')
        </div>
        <div class="slicer-contextmenu-width hidden">
            <span id="name-container"></span>
        </div>
        <div class="ui-button-text-icon-primary" style="position: absolute; left: -1000px; top: -1000px; visibility: hidden">
            <span id="measureWidth" class="ui-button-text"></span>
        </div>
        <div class="hidden">
                <input type="file" id="fileSelector" name="files[]" />
        </div>
    </div>

    <!-- Libraries -->
    <script src="{{ asset('frontend/js/jquery.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/jquery-ui/js/jquery-ui-1.10.3.custom.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/knockout-2.3.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/spread/gc.spread.sheets.all.12.0.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/spread/plugins/gc.spread.sheets.charts.12.0.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/spread/interop/gc.spread.excelio.12.0.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/spread/plugins/gc.spread.sheets.print.12.0.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/spread/plugins/gc.spread.sheets.pdf.12.0.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/spread/plugins/gc.spread.sheets.shapes.12.0.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/spread/plugins/gc.spread.sheets.barcode.12.0.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/zTreeStyle/js/jquery.ztree.all-3.5.min.js')}}"></script>
    <script src="{{ asset('assets/excel/lib/FileSaver.min.js')}}"></script>
    <script src="{{ asset('assets/excel/scripts/gc.spread.sheets.designer.resource.12.0.0.min.js')}}"></script>
    <script src="{{ asset('assets/excel/scripts/gc.spread.sheets.designer.12.0.0.min.js')}}"></script>
    

    <?php $file_name = "uploads/myfile.xlsx"; ?>
    <?php $hasPassword = 0; ?>

<script type="text/javascript">

  var spread, excelIO;
  var passwordRetries =0;

  $(document).ready(function () {  
         $.support.cors = true;  
         excelIO = new GC.Spread.Excel.IO();

         console.log('excelIO',excelIO);
    });

    function getInstance(instance){
            console.log("SET INSTANCE HERE")
            spread = instance;
            //ImportFileFromServer();
        }


    function ImportFileFromServer() {

            var openFileUrl = '<?php echo $file_name ?>';
            var hasPassword = '<?php echo $hasPassword; ?>';
            var password ='';
            var requirePass = (hasPassword == 1)?true:false;
            
            if(requirePass){
                password = prompt('Enter File Password');
            }

            var excelFile = openFileUrl;
            var oReq = new XMLHttpRequest();
            oReq.open('get', excelFile, true);
            oReq.responseType = 'blob';
            oReq.onload = function () {
                var blob = oReq.response;
                console.log(blob);
                 excelIO.open(blob, function(json){
                    jsonData = json;
                    spread.fromJSON(json); 
                 }, function (message) {
                   console.log(message);
                    alert(message.errorMessage);
                    passwordRetries++;

                    if(requirePass && passwordRetries <3){

                     ImportFileFromServer();
                     
                    }else if(requirePass){
                        alert("Get the correct password and try again");
                    }
                 },
                 {'password':password}
                 );
            };
            oReq.send(null);
        }

     function save(toServer=false){
        let filename = prompt("Enter File Name", 'myfile');
        let saveas= filename+'.xlsx';
        var json = spread.toJSON();
        excelIO.save(json, function (blob) {

            var excelFile = blob;
            if(toServer){
                form = new FormData();
                //var fileOfBlob = new File([blob], 'myfile.ssjson');
                 //form.append("fileToUpload", fileOfBlob);
                form.append("fileToUpload", excelFile, saveas);
                // make xhr request to send excel file to server
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "upload.php", true);      
                xhr.onload = function(res){
                    // uploaded to server
                    console.log(res);
                }
                xhr.send(form);
            }
            else{
                saveAs(excelFile, saveas);
            }

            }, function (e) {   
                console.log("error", e);
                alert(e.errorMessage);
            },
            {'password':''}
            );
            
        }


 

    </script>
</body>
</html>

