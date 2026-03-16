@if((settings()->site_theme ?? '') !== 'theme1.')
{{-- Theme1: Select2 and init are provided by theme1 footer (front) and main_nifty layout (admin) --}}
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Standard Select2 - with search enabled for all dropdowns
        $('.select2').select2({
            width: '100%',
            dir: "ltr",
            minimumResultsForSearch: 0, // Always show search box
            placeholder: function() {
                return $(this).data('placeholder') || 'Select an option';
            }
        });

        // Select2 with modal parent
        $('.select2Modal').select2({
            width: '100%',
            dir: "ltr",
            dropdownParent: $(".modalselect"),
            minimumResultsForSearch: 0 // Always show search box
        });

        // Auto-initialize Select2 for common dropdown patterns
        $('select.js-example-basic-single').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).addClass('select2');
                $(this).select2({
                    width: '100%',
                    dir: "ltr",
                    minimumResultsForSearch: 0,
                    placeholder: function() {
                        return $(this).data('placeholder') || 'Select an option';
                    }
                });
            }
        });

        // Initialize Select2 on all select elements that have form-control class but aren't initialized
        $('select.form-control').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible') && !$(this).hasClass('no-select2')) {
                $(this).addClass('select2');
                $(this).select2({
                    width: '100%',
                    dir: "ltr",
                    minimumResultsForSearch: 0,
                    placeholder: function() {
                        return $(this).data('placeholder') || 'Select an option';
                    }
                });
            }
        });

        // Special cases
        $('.services').select2({
            width: '100%',
            dropdownParent: $(".modalselect"),
            minimumInputLength: 0, // Changed to 0 to always show search
            minimumResultsForSearch: 0
        });

        $('.trainings').select2({
            width: '100%',
            dropdownParent: $(".modalselect"),
            minimumResultsForSearch: 0
        });

        $('.districts').select2({
            width: '100%',
            dropdownParent: $(".modalselect"),
            minimumResultsForSearch: 0
        });

        $('subject_areas').select2({
            width: '100%',
            minimumResultsForSearch: 0
        });
    });
</script>
@endif