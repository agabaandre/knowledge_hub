

<script type="text/javascript">
  var resourceId=null;
  var isForum = 0;

 function  summarise(resource,isResourceForum=0){

  $('#summarise-modal').modal('show');
  resourceId=resource;
  isForum = isResourceForum; 

}

function startAiSummary(){

$('.copy').hide();
$('.ai-content').html(`<h3 style='width:100%; text-align:center;'>Analyzing, please wait...<br><center><img src="{{asset("assets/images/loader.gif")}}"></center></h3>`);
    
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let lang   = $('.language').val();
let prompt = $('#prompt').val();

console.log('Prompt',prompt);

        fetch(`{{url('ai/summarise')}}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({
            resource_id: resourceId,
            type: isForum,
            language: lang,
            prompt: prompt
        })
        })
        .then(response => {

            console.log(response);
            
            if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
            }

            return response.json();
        })
        .then(data => {
            console.log(data); // Handle the data
            
            console.log('Resp:',data);
            
            $('.modal-dialog').addClass(' modal-fullscreen');
            $('.ai-content').html(data.content);
            $('.copy').show();
            //typeHtml($('.ai-content'), data.content, 5000);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            $('.ai-content').html(error);
        });
}

function typeText(element, text, speed) {
    
    let index = 0;

    function type() {
        if (index < text.length) {
            element.append(text.charAt(index));
            index++;
            setTimeout(type, speed);
        }
    }

    type();
}

function typeHtml(element, html, speed) {
let container = $('<div>').html(html).contents(); // Create a container for the HTML content
let index = 0;

function type() {
    if (index < container.length) {
        let node = container[index];
        let $node = $(node);
        if (node.nodeType === 1) { // If it's an element node
            element.append($node.clone().addClass('hidden')); // Add the element to the DOM with hidden class
            let $newNode = element.find(':last-child');
                $newNode.removeClass('hidden'); 
        } else if (node.nodeType === 3) { // If it's a text node
            typeText(element, node.textContent, 500);
        }
        index++;
        setTimeout(type, speed);
    }
}

type();

}

function copyToClipboard() {
            const content = document.getElementById('coppable').innerText;
            navigator.clipboard.writeText(content).then(function() {
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
        
</script>