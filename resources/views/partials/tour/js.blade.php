<script src="{{ asset('assets')}}/webtour/webtour.min.js"></script>

<script>

var steps = [
    {
      title: `Let's show you around`,
      content: `Please take a few seconds for us to show you you how this site is organized.`,
      width: '500px'
    },
    {
      element: '#language',
      title: 'Language Selection',
      content: 'You can change to your favourite language here.',
      placement: 'bottom-start',
    },
    {
      element: '#navigation',
      title: 'Navigation',
      content: 'Use these navigation links to move to different sections.',
      placement: 'bottom-start',
    },
    {
      element: '#simple_search',
      title: 'General Search',
      content: 'You search for resources here',
      placement: 'bottom-start',
    },
    {
      element: '#advanced_search',
      title: 'Advanced Search',
      content: 'You can access an advanced search filter here, for more advanced searching.',
      placement: 'bottom-start',
    },
    {
      element: '#quotes',
      title: 'Published Quotes',
      content: 'Specially chosen insightful quotes will always appear here',
      placement: 'top-start',
    },
    {
      element: '#themes',
      title: 'Resource Themes',
      content: 'Here are the resource themes, most content will be published in accordance to this category.',
      placement: 'top-start',
    },
    {
      element: '#tags',
      title: 'Tags',
      content: 'Here are the tags,content published can also be accesed by clicking on one of these.',
      placement: 'top-start',
    },
    {
      element: '#categorization',
      title: 'Resource Categorization',
      content: 'These tabs represent our resource categorizations, click on them to visit there detailed pages.',
      placement: 'bottom-start',
    },
    {
      element: '#recommendations',
      title: 'Recommended Publications',
      content: 'Here are tour recommended resources, check the out anytime!',
      placement: 'top-start',
    },
    {
      element: '#top_searches',
      title: 'Top Searches',
      content: 'Here are the most searched published resources, we think you might be interested in them too!',
      placement: 'top-start',
    },
    {
      element: '#explore',
      title: 'View More Resources',
      content: 'You can click here to view more resources.',
      placement: 'top-start',
    },
    {
      element: '#authors',
      title: 'Content Authors',
      content: 'Here are some of the content authors. Click the button at the bottom to view more.',
      placement: 'top-start',
      onNext: function(){
        //catch backend to set cookie
        fetch("{{ url('/endtour')}}");
      }
    },
    {
      title: `Site Tour Finished`,
      content:'Thanks for taking the time to walk through this.',
      width: '500px'
    }]

var wt = new WebTour();
wt.setSteps(steps)
wt.start();

</script>