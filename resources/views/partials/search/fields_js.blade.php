<script>
    $('.rcc').on('change', function(e) {
        // Publication wizard has its own region → member state linking (supports All for both).
        if ($('#publication_form').length || $('#smartwizard').length) {
            return;
        }

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

            var regions = JSON.parse('<?php echo json_encode(($regions ?? collect())->toArray()); ?>');
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

            var themes = JSON.parse('<?php echo json_encode(($themes ?? collect())->toArray()); ?>');


            let selectedTheme = themes.find(item => item.id === parseInt($(this).val()));

            $('.subtheme').html('<option value="" selected >All</option>');

            selectedTheme.subthemes.forEach(function(item) {

                const option = `<option value="${item.id}">${item.description}</option>`;
                $('.subtheme').append(option);

            });

        }

    });

    /**
     * Publish wizard: rebuild Sub Category options so only rows linked to the selected
     * data category exist (no greyed-out / hidden options in Select2).
     */
    function filterPublishWizardFileCategoriesRebuild(dataCategoryEl, fileCategoryEl) {
        if (!window.__publishWizardFileCategorySnapshot) {
            var snap = [];
            Array.prototype.forEach.call(fileCategoryEl.options, function (opt) {
                snap.push({
                    value: opt.value,
                    text: opt.text,
                    linked: String(opt.getAttribute('data-linked-data-categories') || ''),
                    isPlaceholder: !opt.value
                });
            });
            window.__publishWizardFileCategorySnapshot = snap;
        }

        var snap = window.__publishWizardFileCategorySnapshot;
        var selectedDataCategory = String(dataCategoryEl.value || '');
        var selectedFileCategory = String(fileCategoryEl.value || '');
        var $el = window.jQuery ? window.jQuery(fileCategoryEl) : null;

        if ($el && $el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }

        fileCategoryEl.innerHTML = '';
        var hasRealSelection = false;

        var placeholderAdded = false;
        snap.forEach(function (item) {
            if (item.isPlaceholder) {
                if (placeholderAdded) {
                    return;
                }
                placeholderAdded = true;
                var ph = document.createElement('option');
                ph.value = '';
                ph.textContent = item.text || 'Select Category';
                ph.disabled = true;
                ph.selected = !hasRealSelection;
                fileCategoryEl.appendChild(ph);
                return;
            }
            var linked = item.linked.split(',').map(function (v) { return v.trim(); }).filter(Boolean);
            var allowed = !selectedDataCategory || linked.indexOf(selectedDataCategory) !== -1;
            if (!allowed) {
                return;
            }
            var o = document.createElement('option');
            o.value = item.value;
            o.setAttribute('data-linked-data-categories', item.linked);
            o.textContent = item.text;
            if (String(item.value) === selectedFileCategory) {
                o.selected = true;
                hasRealSelection = true;
            }
            fileCategoryEl.appendChild(o);
        });

        if (!placeholderAdded) {
            var ph0 = document.createElement('option');
            ph0.value = '';
            ph0.textContent = 'Select Category';
            ph0.disabled = true;
            ph0.selected = !hasRealSelection;
            fileCategoryEl.insertBefore(ph0, fileCategoryEl.firstChild);
        }

        if (hasRealSelection) {
            var phOpt = fileCategoryEl.querySelector('option[value=""]');
            if (phOpt) {
                phOpt.selected = false;
            }
        } else if (fileCategoryEl.options[0]) {
            fileCategoryEl.options[0].selected = true;
        }

        if ($el && typeof $el.select2 === 'function') {
            $el.select2({
                width: '100%',
                dir: 'ltr',
                minimumResultsForSearch: 0,
                placeholder: function () {
                    return $el.data('placeholder') || 'Select an option';
                }
            });
        }
    }

    function filterSearchFileCategoriesByDataCategory() {
        var dataCategoryEl = document.getElementById('data_category_id');
        // Search uses file_category_id; publish wizard uses category_id for the same linkage.
        var fileCategoryEl = document.getElementById('file_category_id') || document.getElementById('category_id');
        if (!dataCategoryEl || !fileCategoryEl) {
            return;
        }

        var isPublishWizard = !!(document.getElementById('smartwizard') && fileCategoryEl.id === 'category_id');
        if (isPublishWizard) {
            filterPublishWizardFileCategoriesRebuild(dataCategoryEl, fileCategoryEl);
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
        } else if (window.jQuery && window.jQuery(fileCategoryEl).hasClass('select2-hidden-accessible')) {
            window.jQuery(fileCategoryEl).trigger('change.select2');
        }
    }

    $(document).on('change', '#data_category_id', function () {
        filterSearchFileCategoriesByDataCategory();
    });

    $(document).ready(function () {
        filterSearchFileCategoriesByDataCategory();
        setTimeout(filterSearchFileCategoriesByDataCategory, 450);
    });
</script>
