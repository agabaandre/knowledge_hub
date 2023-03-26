


 jQuery(document).ready(function($){
 $('.red-btn').on({
     'click': function(){
         $('#change-image').attr('src','../../assets/img/brand/logo-blue.png');
         $('#change-logo-dark').attr('src','../../assets/img/brand/logo-blue-dark.png');
         $('#img-change').attr('src','../../assets/img/svgicons/offer-blue.svg');
     }
 });
 
$('.purple-btn').on({
     'click': function(){
        $('#change-image').attr('src','../../assets/img/brand/logo-purple.png');
		 $('#change-logo-dark').attr('src','../../assets/img/brand/logo-purple-dark.png');
		$('#img-change').attr('src','../../assets/img/svgicons/offer-purple.svg');
		$('#change-js').attr('src','../../assets/js/index-purple.js');
     }
 });
 
$('.green-btn').on({
     'click': function(){
         $('#change-image').attr('src','../../assets/img/brand/logo-green.png');
		  $('#change-logo-dark').attr('src','../../assets/img/brand/logo-green-dark.png');
		 $('#img-change').attr('src','../../assets/img/svgicons/offer-green.svg');
     }
 });
 
$('.pink-btn').on({
     'click': function(){
         $('#change-image').attr('src','../../assets/img/brand/logo-pink.png');
		  $('#change-logo-dark').attr('src','../../assets/img/brand/logo-pink-dark.png');
		 $('#img-change').attr('src','../../assets/img/svgicons/offer-pink.svg');
     }
 });
 $('.orange-btn').on({
     'click': function(){
         $('#change-image').attr('src','../../assets/img/brand/logo-orange.png');
		  $('#change-logo-dark').attr('src','../../assets/img/brand/logo-orange-dark.png');
		 $('#img-change').attr('src','../../assets/img/svgicons/offer-orange.svg');
     }
 });
});