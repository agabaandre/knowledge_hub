<script src="{{asset('assets/plugins/blockui/js/jquery.blockui.min.js')}}"></script>

<script>
    
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

</script>