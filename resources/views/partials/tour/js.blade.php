@if ((!get_cookie('CDC_Tour_Finished') && !get_cookie('CDC_Tour_Declined')) || !env('SITE_LIVE'))
<script src="{{ asset('assets')}}/webtour/webtour.min.js"></script>

<style>
.tour-welcome-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100000;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.tour-welcome-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
    text-align: center;
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.tour-welcome-icon {
    font-size: 4rem;
    color: var(--theme-color-primary, #119A48);
    margin-bottom: 1rem;
}

.tour-welcome-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
}

.tour-welcome-description {
    color: #4a5568;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.tour-welcome-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.tour-btn {
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    min-width: 140px;
}

.tour-btn-primary {
    background: var(--theme-color-primary, #119A48);
    color: white;
}

.tour-btn-primary:hover {
    background: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
}

.tour-btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.tour-btn-secondary:hover {
    background: #cbd5e0;
    transform: translateY(-2px);
}

.tour-skip-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    color: #718096;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.tour-skip-btn:hover {
    background: white;
    color: #2d3748;
    border-color: #cbd5e0;
}

.wt-popover-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-top: 1px solid #e2e8f0;
    gap: 1rem;
}

.wt-popover-footer .tour-skip-link {
    color: #718096;
    text-decoration: none;
    font-size: 0.875rem;
    cursor: pointer;
    transition: color 0.2s ease;
}

.wt-popover-footer .tour-skip-link:hover {
    color: #2d3748;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .tour-welcome-card {
        padding: 2rem 1.5rem;
    }
    
    .tour-welcome-buttons {
        flex-direction: column;
    }
    
    .tour-btn {
        width: 100%;
    }
}
</style>

<div id="tourWelcomeModal" class="tour-welcome-modal" style="display: none;">
    <div class="tour-welcome-card">
        <div class="tour-welcome-icon">
            <i class="fa fa-map-marked-alt"></i>
        </div>
        <h2 class="tour-welcome-title">Welcome to the Site Tour!</h2>
        <p class="tour-welcome-description">
            We'd love to show you around and help you get familiar with the platform. 
            This quick tour will take just a few minutes.
        </p>
        <div class="tour-welcome-buttons">
            <button class="tour-btn tour-btn-primary" id="startTourBtn">
                <i class="fa fa-play me-2"></i> Start Tour
            </button>
            <button class="tour-btn tour-btn-secondary" id="skipTourBtn">
                <i class="fa fa-times me-2"></i> Skip Tour
            </button>
        </div>
        <label style="display: block; margin-top: 1.5rem; font-size: 0.875rem; color: #718096; cursor: pointer;">
            <input type="checkbox" id="dontShowAgain" style="margin-right: 0.5rem;">
            Don't show this again
        </label>
    </div>
</div>

<script>
// Function to check if element exists
function elementExists(selector) {
    return document.querySelector(selector) !== null;
}

// Build tour steps dynamically based on what's available
function buildTourSteps() {
    var steps = [
        {
          title: `Let's show you around`,
          content: `Please take a few seconds for us to show you how this site is organized.`,
          width: '500px'
        }
    ];

    // Add language selector step if it exists
    if (elementExists('#languageSelectorBtn') || elementExists('#languageSelector')) {
        steps.push({
          element: '#languageSelectorBtn',
          title: 'Language Selection',
          content: 'You can change to your favourite language here. Click to see available options.',
          placement: 'bottom-start',
        });
    }

    // Navigation - check if exists
    if (elementExists('#navigation')) {
        steps.push({
          element: '#navigation',
          title: 'Navigation Menu',
          content: 'Use these navigation links to move to different sections of the platform.',
          placement: 'bottom-start',
        });
    }

    // Search bar - check if exists
    if (elementExists('#simple_search')) {
        steps.push({
          element: '#simple_search',
          title: 'Search Resources',
          content: 'Use this search bar to find resources by keywords. Type what you\'re looking for and press search.',
          placement: 'bottom-start',
        });
    }

    // Advanced search - check if exists
    if (elementExists('#advanced_search')) {
        steps.push({
          element: '#advanced_search',
          title: 'Advanced Search Filters',
          content: 'Click here to access advanced filters like Region, Member State, Source, and File Type for more precise searches.',
          placement: 'bottom-start',
        });
    }

    // Theme tabs - check if exists
    if (elementExists('#themes')) {
        steps.push({
          element: '#themes',
          title: 'Resource Themes',
          content: 'Browse resources by theme. Click on any theme card to view resources related to that category.',
          placement: 'top-start',
        });
    }

    // Quotes - conditional
    if (elementExists('#quotes')) {
        steps.push({
          element: '#quotes',
          title: 'Published Quotes',
          content: 'Specially chosen insightful quotes appear here to inspire and inform.',
          placement: 'top-start',
        });
    }

    // Tags - conditional
    if (elementExists('#tags')) {
        steps.push({
          element: '#tags',
          title: 'Content Tags',
          content: 'Browse content by tags. Click on any tag to view all related resources.',
          placement: 'top-start',
        });
    }

    // Top searches - check if exists
    if (elementExists('#top_searches')) {
        steps.push({
          element: '#top_searches',
          title: 'Top Searched Resources',
          content: 'Discover the most popular and frequently accessed resources here.',
          placement: 'top-start',
        });
    }

    // Explore link - conditional
    if (elementExists('#explore')) {
        steps.push({
          element: '#explore',
          title: 'Explore More Resources',
          content: 'Click here to view all available resources and explore the complete collection.',
          placement: 'top-start',
        });
    }

    // Final step
    steps.push({
      title: `Site Tour Finished`,
      content:'Thanks for taking the time to walk through this. You\'re now ready to explore our platform!',
      width: '500px',
      onNext: function(){
        endTour('finished');
      }
    });

    return steps;
}

var steps = buildTourSteps();

var wt = null;
var tourStarted = false;

// Function to end tour
function endTour(action) {
    var dontShowAgain = document.getElementById('dontShowAgain')?.checked || false;
    
    if (wt && wt.isRunning) {
        wt.stop();
    }
    
    // Hide welcome modal if visible
    var welcomeModal = document.getElementById('tourWelcomeModal');
    if (welcomeModal) {
        welcomeModal.style.display = 'none';
    }
    
    // Send request to backend
    fetch("{{ url('/endtour')}}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            action: action,
            dont_show_again: dontShowAgain
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Tour ended:', data);
        // Reload page to update cookie
        if (dontShowAgain || action === 'declined') {
            setTimeout(() => window.location.reload(), 500);
        }
    })
    .catch(error => {
        console.error('Error ending tour:', error);
        // Still reload to set cookie on backend
        if (dontShowAgain || action === 'declined') {
            setTimeout(() => window.location.reload(), 500);
        }
    });
}

// Show welcome modal after page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if tour should be shown
    var tourFinished = getCookie('CDC_Tour_Finished');
    var tourDeclined = getCookie('CDC_Tour_Declined');
    
    if ((!tourFinished && !tourDeclined) || '{{ env("SITE_LIVE") }}' === 'false') {
        // Show modal after a short delay
        setTimeout(function() {
            var modal = document.getElementById('tourWelcomeModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }, 1000);
    }
});

// Start tour button
document.addEventListener('DOMContentLoaded', function() {
    var startBtn = document.getElementById('startTourBtn');
    var skipBtn = document.getElementById('skipTourBtn');
    
    if (startBtn) {
        startBtn.addEventListener('click', function() {
            var modal = document.getElementById('tourWelcomeModal');
            if (modal) {
                modal.style.display = 'none';
            }
            
            // Initialize and start tour
            setTimeout(function() {
                wt = new WebTour({
                    onExit: function() {
                        endTour('skipped');
                    }
                });
                
                // Add skip button to each step
                var originalRender = wt.render || function() {};
                
                // Rebuild steps to ensure we only include existing elements
                var currentSteps = buildTourSteps();
                wt.setSteps(currentSteps);
                tourStarted = true;
                wt.start();
                
                // Add skip button to popover after it's rendered
                setTimeout(function() {
                    addSkipButtonToTour();
                }, 100);
            }, 300);
        });
    }
    
    if (skipBtn) {
        skipBtn.addEventListener('click', function() {
            var dontShowAgain = document.getElementById('dontShowAgain')?.checked || false;
            endTour(dontShowAgain ? 'declined' : 'skipped');
        });
    }
});

// Function to add skip button to tour popover
function addSkipButtonToTour() {
    var popover = document.querySelector('.wt-popover');
    if (popover && !document.querySelector('.tour-skip-link-inline')) {
        var footer = document.querySelector('.wt-popover-footer');
        if (footer) {
            var skipLink = document.createElement('a');
            skipLink.className = 'tour-skip-link tour-skip-link-inline';
            skipLink.href = '#';
            skipLink.innerHTML = '<i class="fa fa-times me-1"></i> Skip Tour';
            skipLink.addEventListener('click', function(e) {
                e.preventDefault();
                endTour('skipped');
            });
            
            // Insert before the buttons
            if (footer.firstChild) {
                footer.insertBefore(skipLink, footer.firstChild);
            } else {
                footer.appendChild(skipLink);
            }
        }
    }
}

// Watch for popover changes and add skip button
var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length) {
            addSkipButtonToTour();
        }
    });
});

// Start observing when tour starts
document.addEventListener('DOMContentLoaded', function() {
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Keyboard shortcut to skip tour (ESC)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && tourStarted && wt && wt.isRunning) {
        endTour('skipped');
    }
});

// Helper function to get cookie
function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
    return null;
}
</script>

@endif