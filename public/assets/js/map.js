/*$(document).ready(function(){

  var lastClicked=null

    $(".our-member").on('click', function(event){

      console.log(event);
      
     
        var content = $(this).attr('data-info');
        //alert(content);
        $('.mapcontent').html(content);

        $('.our-member').css("fill",'var(--theme-color-primary)');

        // //change the clicked country and display corresponding content
        var countryId =  '#'+event.target.id;
        lastClicked=event.target.id;
        var textClass = '.'+event.target.id;
        $(countryId).css("fill",'var(--theme-color-secondary)');

         var popover = document.getElementById("mappopover");

         var desc  = $('#'+countryId).attr('data-info'); //$("#desc-" + countryId).html();
         popover.innerHTML = desc;
         popover.style.display = "block";

       // $(textClass).css("display", "block");
       // $(".country-click").css("display","none");
      })

      

      $(".our-member").on("mouseover", function(event) {

            $('.our-member').css("fill",'var(--theme-color-primary)');

           console.log('Hover',event)
            var countryId =  event.target.id;
            $('#'+countryId).css("fill",'var(--theme-color-secondary)');
        
            var popover = document.getElementById("mappopover");
           
            $('#'+countryId).css("fill",'var(--theme-color-secondary)');
        
            var desc  = $('#'+countryId).attr('data-info'); //$("#desc-" + countryId).html();
            popover.innerHTML = desc;
            popover.style.display = "block";

           // popover.style.left = event.pageX + "px";
           // popover.style.top = event.pageY + "px";
        });

        $(".our-member").on("mouseout", function(event) {

          var countryId =  event.target.id;
          $('#'+countryId).css("fill",'');
          
          var popover = document.getElementById("mappopover");

          
          if(lastClicked!==null){

            $('#'+lastClicked).css("fill",'var(--theme-color-secondary)');
        
            var desc  = $('#'+lastClicked).attr('data-info'); //$("#desc-" + countryId).html();
            popover.innerHTML = desc;
            popover.style.display = "block";
            
          }
          
          // if(lastMotionAct==='hover'){
          //  $('#'+countryId).css("fill",'');
          //  popover.style.display ='none';
          // }
          // else
          //   popover.style.display ='block';

        
        });
});
    
*/


  $(document).ready(function(){
      var lastClicked = null;

  // Initialize popover
  $('[data-toggle="popover"]').popover({
    trigger: 'manual',
  html: true,
  placement: 'top'
      });

  $(".our-member").on('click', function(event){
    console.log(event);

  var content = $(this).attr('data-info');
  $('.mapcontent').html(content);

  $('.our-member').css("fill", 'var(--theme-color-primary)');

  var countryId = '#' + event.target.id;
  lastClicked = event.target.id;
  var textClass = '.' + event.target.id;
  $(countryId).css("fill", 'var(--theme-color-secondary)');

  // Hide all popovers
  $('[data-toggle="popover"]').popover('hide');

  var desc = $(countryId).attr('data-info');
  $(this).popover('dispose').popover({
    trigger: 'manual',
  html: true,
  content: desc,
  placement: 'top'
        }).popover('show');
      });

  $(".our-member").on("mouseover", function(event) {
    $('.our-member').css("fill", 'var(--theme-color-primary)');

  console.log('Hover', event);
  var countryId = event.target.id;
  $('#' + countryId).css("fill", 'var(--theme-color-secondary)');

  // Hide all popovers
    $('.popover').hide();

  var desc = $('#' + countryId).attr('data-info');
  $(this).popover('dispose').popover({
     trigger: 'manual',
      html: true,
      content: desc,
      placement: 'top'
        }).popover('show');
      });

  $(".our-member").on("mouseout", function(event) {
        var countryId = event.target.id;
  $('#' + countryId).css("fill", '');

  if (lastClicked !== null) {
    $('#' + lastClicked).css("fill", 'var(--theme-color-secondary)');

  var desc = $('#' + lastClicked).attr('data-info');
  var popover = $('#mappopover');
  popover.html(desc).show();

  $('#' + lastClicked).popover('dispose').popover({
    trigger: 'manual',
    html: true,
    content: desc,
    placement: 'top'
  }).popover('show');
 
}
      });
    });