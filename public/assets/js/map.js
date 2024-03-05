$(document).ready(function(){
    $(".our-member").on('click', function(event){
      
      //change all other countries to regular background
        $(".our-member").each(function(i){
          var currentElement = '#'+$(".our-member").get(i).id;
          $(currentElement).css("fill", "#f2c200");
        });
        
        //display no content in .detail box
        // $(".mapcontent").each(function(i){
        //   var currentContent = $(".mapcontent").get(i);
        //   $(currentContent).css("display", "none");
        // })
        
        //change the clicked country and display corresponding content
        var countryId =  '#'+event.target.id;
        var textClass = '.'+event.target.id;
        $(countryId).css("fill", "rgba(0,0,0,0.5)");
        $(textClass).css("display", "block");
        $('.mapcontent').html(textClass);
        $(".country-click").css("display","none");
      })
});
    