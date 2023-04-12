$(function() {
	
	
	 var loadFile = function(event) {
		var output = document.getElementById('output');
		output.src = URL.createObjectURL(event.target.files[0]);
		output.onload = function() {
		  URL.revokeObjectURL(output.src) // free memory
		}
	  };
	 function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#image_upload_preview').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#inputFile").change(function () {
        readURL(this);
    });
	
	
	$("div.account-setting-tab-menu>div.list-group>a").click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        $("div.account-setting-tab>div.account-setting-tab-content").removeClass("active");
        $("div.account-setting-tab>div.account-setting-tab-content").eq(index).addClass("active");
    });
	
});