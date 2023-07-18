
<script type="text/javascript">
	
	// ------------step-wizard-------------
$(document).ready(function () {

    function buildStepsBreadcrumb (wizard, element, steps) {
            const $steps = document.getElementById(element)
            $steps.innerHTML = ''
            for (let label in steps) {
                if (steps.hasOwnProperty(label)) {
                const $li = document.createElement('li')
                const $a = document.createElement('a')
                $li.classList.add('nav-item')
                $a.classList.add('nav-link')
                if (steps[label].active) {
                    $a.classList.add('active')
                }
                $a.setAttribute('href', '#')
                $a.innerText = label
                $a.addEventListener('click', e => {
                    e.preventDefault()
                    wizard.revealStep(label)
                })
                $li.appendChild($a)
                $steps.appendChild($li)
                }
            }
}

function onStepChange(wizard, selector) {
    const steps = wizard.getBreadcrumb()
    buildStepsBreadcrumb(wizard, selector, steps)
}

const wizard = new window.Zangdar('#wizard', {
  onStepChange: () => {
    onStepChange(wizard, 'steps-native')
  },
  onSubmit(e) {
    e.preventDefault()
    console.log(e.target.elements)
    return false
  }
})

onStepChange(wizard, 'steps-native');
    
});


</script>