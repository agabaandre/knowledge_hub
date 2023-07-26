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
        description : 'Choose Data categorie to browse tthrough a list of data of that category',
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
        description : 'Here you will accees to the different data Dashboards',
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
        title : 'Search resources',
        description : 'Here you can use keywords to search for resources or use the suggestions.',
       // image : 'my/image/path.png',
        selector : '.main_search',
        x : 50,
        y : 50,
        offx : 10,
        offy : 5,
        position : 'bottom',
        onSelected : false
    }
    
    
    ]
};

ProductTourJS.init(tourOptions); 
ProductTourJS.start();

</script>