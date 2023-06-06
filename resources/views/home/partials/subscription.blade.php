<!-- ======================= Newsletter Start ============================ -->
<section class="space bg-cover" style="background-color:#7a7a7a; background-repeat:no-repeat;">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="sec_title position-relative text-center mb-5">
                    <h6 class="text-light mb-0">Subscribe Now</h6>
                    <h2 class="ft-bold text-light">Get All New Updates</h2>
                </div>
            </div>
        </div>

        <div class="row align-items-center justify-content-center">
            <div class="col-xl-7 col-lg-10 col-md-12 col-sm-12 col-12">

                @if(@session()->error_message)
                <div class="form-group mt-2">
                    <div class="alert alert-danger">
                        {{ @session()->error_message }}
                    </div>
                </div>
                @endif

                @if(@session()->success_message)
                <div class="form-group mt-2">
                    <div class="alert alert-success">
                        {{@session()->success_message}}
                    </div>
                </div>
                @endif



                <form action="{{url('api/subscribe')}}" class='bg-white rounded p-1' id='subscription-form'>

                    <!-- Row with alert centered to show alert messages, hidden by default -->
                    <div class="alert alert-danger d-none" role="alert">
                        <strong>Error!</strong>
                    </div>

                    <div class="row no-gutters">
                        <div class="col-xl-9 col-lg-9 col-md-8 col-sm-8 col-8">
                            <div class="form-group mb-0 position-relative">
                                <input id="subscribe-email" type="text" class="form-control lg left-ico" placeholder="Enter Your Email Address" name="email">
                                <i class="bnc-ico lni lni-envelope"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-4 col-4">
                            <div class="form-group mb-0 position-relative">
                                <button type="submit" id="subscribe-bt" class="btn full-width custom-height-lg theme-bg text-light fs-md">Subscribe</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</section>
<!-- ======================= Newsletter Start ============================ -->


<!-- JQuery code to subscribe via ajax -->
<script>
    $(document).ready(function() {
        var alertTimeout; // Variable to store the timeout ID

        $('#subscription-form').submit(function(e) {
            e.preventDefault();

            var url = $(this).attr('action');
            var $alert = $('#subscription-form').find('.alert'); // Store a reference to the alert element
            var $submitButton = $('#subscription-form').find('button[type="submit"]'); // Store a reference to the submit button

            clearTimeout(alertTimeout); // Clear any previous timeout

            // Disable the submit button and show processing text
            $submitButton.prop('disabled', true).html('Processing...');

            $.ajax({
                type: 'POST',
                url: url,
                data: $('#subscription-form').serialize(),
                success: function(response) {
                    console.log(response.message);

                    $alert.removeClass('d-none')
                        .removeClass('alert-danger')
                        .addClass('alert-success')
                        .html(response.message);

                    // Set a timeout to clear the alert after 5 seconds (adjust as needed)
                    alertTimeout = setTimeout(function() {
                        $alert.addClass('d-none');
                    }, 5000);
                },
                error: function(response) {
                    console.log(response.message);

                    $alert.removeClass('d-none')
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .html('Something went wrong. Please try again later.');

                    // Set a timeout to clear the alert after 5 seconds (adjust as needed)
                    alertTimeout = setTimeout(function() {
                        $alert.addClass('d-none');
                    }, 5000);
                },
                complete: function() {
                    // Re-enable the submit button and restore its original text
                    $submitButton.prop('disabled', false).html('Submit');
                    $('#subscription-form')[0].reset(); // Reset the form after AJAX request is complete
                }
            });
        });
    });
</script>
