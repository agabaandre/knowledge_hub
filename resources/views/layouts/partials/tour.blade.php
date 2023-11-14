
@if(!get_cookie('CDC_Tour_Finished') || !env('SITE_LIVE')):

<script src="{{ asset('assets/plugins/tour/tour.js')}}"></script>

<script>

let tourOptions = {

    options : {
        darkLayerPersistence : true,
        next : 'Next',
        prev : 'Previous',
        finish : 'Okay!',
        mobileThreshold: 768
    },

    tips : [

    {
        title : 'Data Categories',
        description : 'Choose a data category to browse ',
       // image : 'my/image/path.png',
        selector : '.categories',
        x : 50,
        y : 50,
        offx : 10,
        offy : 10,
        position : 'bottom',
        onSelected : false
    },
    {
        title : 'Dashboards',
        description : 'Here you will have accees to the different data dashboards',
       // image : 'my/image/path.png',
        selector : '.dashboards',
        x : 50,
        y : 50,
        offx : 10,
        offy : 10,
        position : 'bottom',
        onSelected : false
    },
    {
        title : 'Advanced Search',
        description : 'Use the form here to apply advanced filters and refine your search results',
       // image : 'my/image/path.png',
        selector : '.filter',
        x : 50,
        y : 50,
        offx : 10,
        offy : 20,
        position : 'top',
        onSelected : false
    },
    {
        title : 'Search resources',
        description : 'Here you can use keywords to search for resources or the automatic suggestions.',
       // image : 'my/image/path.png',
        selector : '.main_search',
        x : 50,
        y : 50,
        offx : 10,
        offy : 45,
        position : 'bottom',
        onSelected : function(){
          fetch("{{ url('/endtour')}}");
        }
    }


    ],
    'on-product-tour-js-exit':function(){
    console.log('TOur ended');
    }
};

ProductTourJS.init(tourOptions);
ProductTourJS.start();

</script>

@endif
