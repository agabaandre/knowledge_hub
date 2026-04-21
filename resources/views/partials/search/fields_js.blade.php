<script>
    $('.rcc').on('change', function(e) {

        console.log($(this).val());

        if (typeof $(this).val() == 'object' && $(this).val().length > 1) {
            $('.country').html('<option value="all" selected >All</option>');
            $('.country').attr('disabled', true);
            return;
        } else if ($(this).val() == 'all') {
            $('.country').html('<option value="all" selected >All</option>');
            $('.country').attr('disabled', true);
        } else {
            $('.country').html('');
            $('.country').attr('disabled', false);
        }

        if ($(this).val()) {

            let selected = (typeof $(this).val() == 'object') ? $(this).val()[0] :
                $(this).val();

            var regions = JSON.parse('<?php echo json_encode($regions->toArray()); ?>');
            let selectedRegion = regions.find(item => item.id === parseInt(selected));

            $('.country').html('<option value="all" >All</option>');

            try {
                selectedRegion.countries.forEach(function(item) {

                    const option = `<option value="${item.id}">${item.name}</option>`;
                    $('.country').append(option);

                });

            } catch (e) {
                console.log(e);
            }

        }

    });


    $('.theme').on('change', function(e) {

        if ($(this).val()) {

            var themes = JSON.parse('<?php echo json_encode($themes->toArray()); ?>');


            let selectedTheme = themes.find(item => item.id === parseInt($(this).val()));

            $('.subtheme').html('<option value="" selected >All</option>');

            selectedTheme.subthemes.forEach(function(item) {

                const option = `<option value="${item.id}">${item.description}</option>`;
                $('.subtheme').append(option);

            });

        }

    });

    function filterSearchFileCategoriesByDataCategory() {
        var dataCategoryEl = document.getElementById('data_category_id');
        var fileCategoryEl = document.getElementById('file_category_id');
        if (!dataCategoryEl || !fileCategoryEl) {
            return;
        }

        var selectedDataCategory = String(dataCategoryEl.value || '');
        var selectedFileCategory = String(fileCategoryEl.value || '');
        var selectedStillValid = false;

        Array.prototype.forEach.call(fileCategoryEl.options, function (opt) {
            if (!opt.value) return;
            var linked = String(opt.getAttribute('data-linked-data-categories') || '')
                .split(',')
                .map(function (v) { return v.trim(); })
                .filter(Boolean);
            var allowed = !selectedDataCategory || linked.indexOf(selectedDataCategory) !== -1;
            opt.hidden = !allowed;
            opt.disabled = !allowed;

            if (allowed && opt.value === selectedFileCategory) {
                selectedStillValid = true;
            }
        });

        if (selectedFileCategory && !selectedStillValid) {
            fileCategoryEl.value = '';
            if (window.jQuery && window.jQuery(fileCategoryEl).hasClass('select2-hidden-accessible')) {
                window.jQuery(fileCategoryEl).val('').trigger('change');
            }
        }
    }

    $(document).on('change', '#data_category_id', function () {
        filterSearchFileCategoriesByDataCategory();
    });

    $(document).ready(function () {
        filterSearchFileCategoriesByDataCategory();
    });
</script>
