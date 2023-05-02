<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>

<script>
    
// Select2
$('.select2').select2({
    width: '100%',
    dir: "ltr",
});


$('.select2Modal').select2({
    width: '100%',
    dir: "ltr",
    dropdownParent: $(".modalselect")
});

$('.services').select2({
    width: '100%',
    dropdownParent: $(".modalselect"),
    minimumInputLength: 2
});


$('.trainings').select2({
    width: '100%',
    dropdownParent: $(".modalselect")
});


$('.districts').select2({
    width: '100%',
    dropdownParent: $(".modalselect")
});


</script>