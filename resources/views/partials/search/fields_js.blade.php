<script>

    $('.rcc').on('change', function (e) {


        if ($(this).val()) {
            var regions = JSON.parse('<?php echo json_encode($regions->toArray()); ?>');
            let selectedRegion = regions.find(item => item.id === parseInt($(this).val()));

            $('.country').html('<option value="" selected >All</option>');

            selectedRegion.countries.forEach(function (item) {

                const option = `<option value="${item.id}">${item.name}</option>`;
                $('.country').append(option);

            });

        }

    });


    $('.theme').on('change', function (e) {

        if ($(this).val()) {

            var themes = JSON.parse('<?php echo json_encode($themes->toArray()); ?>');


            let selectedTheme = themes.find(item => item.id === parseInt($(this).val()));

            $('.subtheme').html('<option value="" selected >All</option>');

            selectedTheme.subthemes.forEach(function (item) {

                const option = `<option value="${item.id}">${item.description}</option>`;
                $('.subtheme').append(option);

            });

        }

    });

</script>