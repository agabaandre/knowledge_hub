<script type="text/javascript">
console.log('=== FORUM SCRIPT LOADING (FROM PARTIAL) ===');

// Image Modal for full view
function openImageModal(imageUrl, imageAlt) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('imageViewModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'imageViewModal';
        modal.className = 'image-view-modal';
        modal.innerHTML = `
            <div class="image-view-modal-overlay" onclick="closeImageModal()"></div>
            <div class="image-view-modal-content">
                <button class="image-view-modal-close" onclick="closeImageModal()">&times;</button>
                <img id="imageViewModalImg" src="" alt="" />
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    document.getElementById('imageViewModalImg').src = imageUrl;
    document.getElementById('imageViewModalImg').alt = imageAlt || 'Image';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageViewModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Make functions globally accessible
window.openImageModal = openImageModal;
window.closeImageModal = closeImageModal;

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

(function() {
    'use strict';
    try {
        console.log('=== FORUM SCRIPT TAG EXECUTING ===');
        
        // Add to window for global access test
        window.forumScriptLoaded = true;
    } catch(e) {
        console.error('Error in forum script test:', e);
        alert('Forum script test error: ' + e.message);
    }
})();

// Function to copy forum/comment link - Must be global
window.copyForumLink = function(url){
    console.log('copyForumLink called:', url);
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(function(){
            showLobiboxNotification('success', 'Link copied to clipboard!');
        }).catch(function(err) {
            fallbackCopy(url);
        });
    } else {
        fallbackCopy(url);
    }
};

function fallbackCopy(url) {
    const el = document.createElement('textarea');
    el.value = url; 
    el.style.position = 'fixed';
    el.style.left = '-9999px';
    document.body.appendChild(el); 
    el.select();
    try { 
        document.execCommand('copy');
        showLobiboxNotification('success', 'Link copied to clipboard!');
    } catch(err) {
        showLobiboxNotification('error', 'Failed to copy link. Please copy manually: ' + url);
    } finally { 
        document.body.removeChild(el); 
    }
}

function showLobiboxNotification(type, message) {
    if (typeof Lobibox !== 'undefined') {
        Lobibox.notify(type, {
            size: 'mini',
            sound: false,
            delay: 3000,
            title: false,
            pauseDelayOnHover: true,
            position: 'top right',
            msg: message
        });
    } else if (typeof window.notifyx === 'function') {
        window.notifyx(type, message);
    } else {
        alert(message);
    }
}

// Wait for everything to be ready - Run AFTER all scripts load
try {
    (function() {
        console.log('=== FORUM SCRIPT IIFE STARTING ===');
    
    function waitForJQuery(callback, maxAttempts) {
        maxAttempts = maxAttempts || 100;
        var attempts = 0;
        
        function check() {
            attempts++;
            var $ = window.jQuery || window.$;
            if ($ && $.fn && $.fn.jquery) {
                console.log('jQuery found after', attempts, 'attempts - Version:', $.fn.jquery);
                callback();
            } else if (attempts < maxAttempts) {
                setTimeout(check, 50);
            } else {
                console.error('jQuery not found after', attempts, 'attempts');
            }
        }
        check();
    }
    
    // Wait for window load to ensure all scripts are loaded
    function initForumHandlers() {
        waitForJQuery(function() {
            var $ = window.jQuery || window.$;
            console.log('Initializing forum handlers...');
            
            $(document).ready(function() {
                console.log('=== DOM READY ===');
                console.log('Reply buttons found:', $('.reply-comment-btn').length);
                console.log('Share buttons found:', $('.share-comment-btn').length);
                console.log('File upload areas found:', $('#fileUploadArea').length);
            
            // Debug: Check if buttons exist
            var replyBtnCount = $('.reply-comment-btn').length;
            var shareBtnCount = $('.share-comment-btn').length;
            var fileUploadAreaCount = $('#fileUploadArea').length;
            
            console.log('Found ' + replyBtnCount + ' reply buttons, ' + shareBtnCount + ' share buttons, ' + fileUploadAreaCount + ' file upload areas');
        
        let fileInput = document.getElementById('forumCommentFiles');
        let fileUploadAreaEl = document.getElementById('fileUploadArea');
        let filePreview = document.getElementById('filePreview');
        const form = document.getElementById('forumCommentForm');
        
        console.log('File input found:', !!fileInput);
        console.log('File upload area found:', !!fileUploadAreaEl);
        console.log('File preview found:', !!filePreview);
        console.log('Reply buttons found:', $('.reply-comment-btn').length);
        console.log('Share buttons found:', $('.share-comment-btn').length);
        const maxFileSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/pjpeg', 'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation',
            'application/rtf', 'text/rtf',
            'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm', 'video/x-ms-wmv', 'video/x-flv',
            'video/3gpp', 'video/mpeg', 'audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/x-m4a', 'audio/m4a',
            'audio/wav', 'audio/x-wav', 'audio/aac', 'audio/ogg', 'audio/flac', 'audio/x-ms-wma', 'audio/webm'
        ];
        const dangerousTypes = ['application/x-msdownload', 'application/x-sh', 'application/x-executable', 
                                'application/x-msdos-program', 'application/javascript', 'application/x-php'];

        let selectedFiles = [];

        // File upload - Use jQuery
        function setupFileUpload() {
            const $fileUploadArea = $('#fileUploadArea');
            const $fileInput = $('#forumCommentFiles');
            const filePreviewEl = document.getElementById('filePreview');
            
            if ($fileUploadArea.length && $fileInput.length && filePreviewEl) {
                // Update references
                fileInput = document.getElementById('forumCommentFiles');
                filePreview = filePreviewEl;
                
                console.log('Setting up file upload handlers');
                
                // Remove existing handlers to prevent duplicates
                $fileUploadArea.off('click dragover dragleave drop');
                $fileInput.off('change');
                
                // Click to upload
                $fileUploadArea.on('click', function(e) {
                    // Don't trigger if clicking on remove buttons or preview items
                    if ($(e.target).closest('.file-preview-item, .file-preview-remove').length) {
                        return;
                    }
                    if (!$(e.target).is('input') && !$(e.target).is('textarea')) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('File upload area clicked, triggering file input');
                        $fileInput[0].click();
                    }
                });

                // Drag and drop
                $fileUploadArea.on('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('dragover');
                });

                $fileUploadArea.on('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('dragover');
                });

                $fileUploadArea.on('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('dragover');
                    
                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        handleFiles(Array.from(files));
                    }
                });

                // File input change
                $fileInput.on('change', function(e) {
                    const files = this.files;
                    if (files.length > 0) {
                        handleFiles(Array.from(files));
                    }
                });
                
                console.log('File upload area initialized successfully');
                return true;
            } else {
                console.warn('File upload elements not found, will retry...');
                return false;
            }
        }
        
        // Initialize file upload - retry if needed
        var uploadSetupAttempts = 0;
        function trySetupFileUpload() {
            uploadSetupAttempts++;
            if (!setupFileUpload() && uploadSetupAttempts < 10) {
                console.log('File upload setup retry', uploadSetupAttempts);
                setTimeout(trySetupFileUpload, 500);
            }
        }
        trySetupFileUpload();

        function handleFiles(files) {
            files.forEach(file => {
                // Validate file size
                if (file.size > maxFileSize) {
                    alert(file.name + ' is too large. Maximum file size is 2MB.');
                    return;
                }

                const typeOk = allowedTypes.includes(file.type)
                    || (file.type && file.type.indexOf('video/') === 0)
                    || (file.type && file.type.indexOf('audio/') === 0)
                    || (file.type && file.type.indexOf('application/vnd') === 0)
                    || (file.type && (file.type === 'application/msword' || file.type === 'application/rtf' || file.type === 'text/rtf'));
                if (!typeOk && !isAllowedExtension(file.name)) {
                    alert(file.name + ' is not allowed. Use images, PDF, Word/Excel/PowerPoint, or common audio/video formats.');
                    return;
                }

                // Check for dangerous file types
                if (dangerousTypes.includes(file.type) || isDangerousExtension(file.name)) {
                    alert(file.name + ' is not allowed for security reasons.');
                    return;
                }

                // Add to selected files
                selectedFiles.push(file);
                displayFilePreview(file);
            });

            // Update file input
            updateFileInput();
        }

        function isAllowedExtension(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const allowed = [
                'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf',
                'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'rtf',
                'mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg',
                'mp3', 'm4a', 'wav', 'aac', 'ogg', 'oga', 'opus', 'flac', 'wma'
            ];
            return allowed.includes(ext);
        }

        function isDangerousExtension(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const dangerous = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'apk', 'dll', 'sh', 'php', 
                             'asp', 'jsp', 'py', 'rb', 'pl', 'cgi', 'bin', 'msi', 'deb', 'rpm'];
            return dangerous.includes(ext);
        }

        function displayFilePreview(file) {
            // Ensure filePreview exists
            if (!filePreview) {
                filePreview = document.getElementById('filePreview');
                if (!filePreview) {
                    console.error('File preview container not found');
                    return;
                }
            }
            
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item';
            previewItem.dataset.filename = file.name;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" class="file-preview-img" alt="${file.name}" 
                             onclick="openImageModal('${e.target.result}', '${file.name.replace(/'/g, "\\'")}')" 
                             style="cursor: pointer;">
                        <span class="file-preview-remove" onclick="removeFile('${file.name.replace(/'/g, "\\'")}')">×</span>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                const icon = file.type.includes('pdf') ? 'file-pdf'
                    : (file.type.includes('word') || /\.(doc|docx)$/i.test(file.name)) ? 'file-word'
                    : (file.type.includes('excel') || file.type.includes('spreadsheet') || /\.(xls|xlsx)$/i.test(file.name)) ? 'file-excel'
                    : (file.type.includes('powerpoint') || file.type.includes('presentation') || /\.(ppt|pptx)$/i.test(file.name)) ? 'file-powerpoint'
                    : (file.type.includes('audio') || /\.(mp3|m4a|wav|aac|ogg|oga|opus|flac|wma)$/i.test(file.name)) ? 'file-audio'
                    : (file.type.includes('video') || /\.(mp4|m4v|mov|avi|webm|mkv|wmv|flv|3gp|3gpp|mpeg|mpg)$/i.test(file.name)) ? 'file-video'
                    : 'file';
                
                previewItem.innerHTML = `
                    <div style="width: 80px; height: 80px; background: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 4px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem;">
                        <i class="fa fa-${icon}" style="font-size: 1.5rem; color: #64748b; margin-bottom: 0.25rem;"></i>
                        <span style="font-size: 0.625rem; color: #64748b; text-align: center; word-break: break-all; max-width: 100%;">${file.name.length > 10 ? file.name.substring(0, 10) + '...' : file.name}</span>
                    </div>
                    <span class="file-preview-remove" onclick="removeFile('${file.name}')">×</span>
                `;
            }

            filePreview.appendChild(previewItem);
        }

        function removeFile(filename) {
            selectedFiles = selectedFiles.filter(f => f.name !== filename);
            const previewItem = document.querySelector(`.file-preview-item[data-filename="${filename}"]`);
            if (previewItem) {
                previewItem.remove();
            }
            updateFileInput();
        }

        function updateFileInput() {
            if (!fileInput) {
                fileInput = document.getElementById('forumCommentFiles');
                if (!fileInput) {
                    console.error('File input not found');
                    return;
                }
            }
            
            const dt = new DataTransfer();
            selectedFiles.forEach(file => {
                dt.items.add(file);
            });
            fileInput.files = dt.files;
            console.log('Updated file input, now has', fileInput.files.length, 'files');
        }

        // Make removeFile available globally
        window.removeFile = removeFile;

        // Form validation
        // AJAX form submission for main comment form
        $(document).off('submit', '#forumCommentForm').on('submit', '#forumCommentForm', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $form = $(this);
            const textarea = document.getElementById('forumCommentTextarea');
            if (!textarea || !textarea.value.trim()) {
                showLobiboxNotification('error', 'Please enter a comment.');
                return false;
            }

            const commentTrimmed = textarea.value.trim();
            const wc = commentTrimmed.split(/\s+/).filter(function (w) { return w.length > 0; }).length;
            if (wc > 300) {
                showLobiboxNotification('error', 'Comments are limited to 300 words.');
                return false;
            }
            if (commentTrimmed.length > 20000) {
                showLobiboxNotification('error', 'Comment is too long.');
                return false;
            }

            // Validate file sizes one more time
            for (let i = 0; i < selectedFiles.length; i++) {
                if (selectedFiles[i].size > maxFileSize) {
                    showLobiboxNotification('error', selectedFiles[i].name + ' exceeds the 2MB limit.');
                    return false;
                }
            }

            // Get reCAPTCHA response if available
            let recaptchaResponse = '';
            const recaptchaElement = $form.find('[name="g-recaptcha-response"]');
            if (recaptchaElement.length) {
                recaptchaResponse = grecaptcha.getResponse(0);
            }

            // Create FormData
            const formData = new FormData();
            formData.append('_token', $form.find('[name="_token"]').val());
            formData.append('id', $form.find('[name="id"]').val());
            formData.append('comment', textarea.value.trim());
            if (recaptchaResponse) {
                formData.append('g-recaptcha-response', recaptchaResponse);
            }

            // Append files
            console.log('Appending', selectedFiles.length, 'files to formData');
            for (let i = 0; i < selectedFiles.length; i++) {
                console.log('Appending file:', selectedFiles[i].name, 'Size:', selectedFiles[i].size, 'Type:', selectedFiles[i].type);
                formData.append('attachments[]', selectedFiles[i]);
            }
            
            // Log FormData contents for debugging
            console.log('FormData prepared with', selectedFiles.length, 'files');

            // Disable submit button and show loading
            const $submitBtn = $form.find('button[type="submit"]');
            const originalBtnHtml = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Posting...');

            // Submit via AJAX
            console.log('Submitting AJAX request with', selectedFiles.length, 'files');
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    console.log('AJAX request sending...');
                },
                success: function(response) {
                    if (response.success && response.comment_html) {
                        // Clear form
                        textarea.value = '';
                        updateCharCount(textarea);
                        
                        // Clear file previews
                        selectedFiles = [];
                        if (filePreview) {
                            filePreview.innerHTML = '';
                        }
                        if (fileInput) {
                            fileInput.value = '';
                        }

                        // Reset reCAPTCHA if it exists
                        if (typeof grecaptcha !== 'undefined' && recaptchaElement.length) {
                            grecaptcha.reset(0);
                        }

                        // Hide comment form container
                        const formContainer = document.getElementById('commentFormContainer');
                        if (formContainer) {
                            formContainer.style.display = 'none';
                            const toggle = document.querySelector('.comment-form-toggle');
                            const icon = document.getElementById('commentFormToggleIcon');
                            if (toggle && icon) {
                                icon.style.transform = 'rotate(0deg)';
                                toggle.style.background = '#f8f9fa';
                                toggle.style.borderBottom = '1px solid #e2e8f0';
                            }
                        }

                        // Add new comment to the list (prepend to comments section)
                        const $commentsSection = $('.comments-section');
                        if ($commentsSection.length) {
                            // Create a temporary container for the new comment HTML
                            const $tempDiv = $('<div>').html(response.comment_html);
                            const $newComment = $tempDiv.find('.comment-item').first();
                            
                            // Insert at the beginning of comments list (if comments exist) or after comments header
                            const $commentsList = $commentsSection.find('.comment-item').first();
                            if ($commentsList.length) {
                                $newComment.insertBefore($commentsList);
                            } else {
                                // No comments yet, insert after the comments header
                                const $commentsHeader = $commentsSection.find('.comments-header');
                                if ($commentsHeader.length) {
                                    $newComment.insertAfter($commentsHeader);
                                } else {
                                    $commentsSection.prepend($newComment);
                                }
                            }

                            // Update comments count in header
                            const $commentsHeader = $commentsSection.find('.comments-header');
                            if ($commentsHeader.length) {
                                const currentCount = parseInt($commentsHeader.text().match(/\d+/)?.[0] || '0');
                                const newCount = currentCount + 1;
                                const countText = newCount === 1 ? 'Comment' : 'Comments';
                                const primaryColor = '{{ settings()->primary_color ?? "#119A48" }}';
                                $commentsHeader.html('<i class="fa fa-comments me-2" style="color: ' + primaryColor + ';"></i> ' + newCount + ' ' + countText);
                            } else {
                                // If no header exists, create one
                                const primaryColor = '{{ settings()->primary_color ?? "#119A48" }}';
                                const headerHtml = '<div class="comments-header" style="margin-bottom: 1rem; font-size: 1.1rem; font-weight: 600; color: #2d3748;"><i class="fa fa-comments me-2" style="color: ' + primaryColor + ';"></i> 1 Comment</div>';
                                if ($commentsSection.find('.comment-item').length === 1) {
                                    $commentsSection.prepend(headerHtml);
                                }
                            }

                            // Hide "no comments" message if it exists
                            $('.no-comments').hide();

                            // Scroll to the new comment
                            $('html, body').animate({
                                scrollTop: $newComment.offset().top - 100
                            }, 500);

                            // Re-initialize event handlers for the new comment
                            initCommentHandlers($newComment);
                        }

                        showLobiboxNotification('success', 'Comment posted successfully!');
                    } else {
                        showLobiboxNotification('error', response.error || 'Failed to post comment. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    console.error('Response status:', xhr.status);
                    console.error('Response text:', xhr.responseText);
                    let errorMsg = 'Failed to post comment. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errs = xhr.responseJSON.errors;
                        const firstKey = Object.keys(errs)[0];
                        if (firstKey && errs[firstKey] && errs[firstKey][0]) {
                            errorMsg = errs[firstKey][0];
                        }
                    } else if (xhr.responseText) {
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.error) {
                                errorMsg = errorResponse.error;
                            }
                        } catch(e) {
                            // Use default error message
                            console.error('Error parsing response:', e);
                        }
                    }
                    showLobiboxNotification('error', errorMsg);
                },
                complete: function() {
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).html(originalBtnHtml);
                }
            });

            return false;
        });

        $(document).off('submit', 'form.reply-form').on('submit', 'form.reply-form', function(e) {
            const $ta = $(this).find('textarea[name="comment"]');
            const commentTrimmed = ($ta.val() || '').trim();
            if (!commentTrimmed) {
                e.preventDefault();
                showLobiboxNotification('error', 'Please enter a reply.');
                return false;
            }
            const wc = commentTrimmed.split(/\s+/).filter(function (w) { return w.length > 0; }).length;
            if (wc > 300) {
                e.preventDefault();
                showLobiboxNotification('error', 'Comments are limited to 300 words.');
                return false;
            }
            if (commentTrimmed.length > 20000) {
                e.preventDefault();
                showLobiboxNotification('error', 'Comment is too long.');
                return false;
            }
        });

        // Helper function to initialize comment handlers for dynamically added comments
        function initCommentHandlers($commentElement) {
            // Re-initialize like, reply, share handlers for this comment
            // Event delegation should handle most of this, but we can trigger any needed setup here
            console.log('Initialized handlers for new comment');
        }

        // Forum Like Handler - Use jQuery
        $(document).off('click', '.like-forum-btn').on('click', '.like-forum-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $btn = $(this);
            const forumId = $btn.data('forum-id');
            const $icon = $btn.find('i');
            const $countSpan = $btn.find('.like-count');
            
            $.ajax({
                url: '{{ url("forums/like") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                contentType: 'application/json',
                data: JSON.stringify({ forum_id: forumId }),
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    $icon.removeClass('fa-heart fa-heart-o').addClass(data.liked ? 'fa-heart' : 'fa-heart-o');
                    $icon.css('color', data.liked ? '#ef4444' : 'inherit');
                    $countSpan.text(data.count);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Comment Like Handler - Use jQuery
        $(document).off('click', '.like-comment-btn').on('click', '.like-comment-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $btn = $(this);
            const commentId = $btn.data('comment-id');
            const $icon = $btn.find('i');
            const $countSpan = $btn.find('.like-count');
            
            $.ajax({
                url: '{{ url("forums/comment/like") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                contentType: 'application/json',
                data: JSON.stringify({ comment_id: commentId }),
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    $icon.removeClass('fa-heart fa-heart-o').addClass(data.liked ? 'fa-heart' : 'fa-heart-o');
                    $icon.css('color', data.liked ? '#ef4444' : 'inherit');
                    $btn.css('color', data.liked ? '#ef4444' : '#64748b');
                    const likeText = data.count === 1 ? 'like' : 'likes';
                    $countSpan.text(data.count + ' ' + likeText);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Reply Button Handler - Use jQuery event delegation
        $(document).off('click', '.reply-comment-btn').on('click', '.reply-comment-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Reply button clicked!');
            const $btn = $(this);
            const commentId = $btn.data('comment-id') || $btn.attr('data-comment-id');
            console.log('Comment ID:', commentId);
            const $replyForm = $('#reply-form-' + commentId);
            if ($replyForm.length) {
                if ($replyForm.is(':visible')) {
                    $replyForm.hide();
                    console.log('Reply form hidden');
                } else {
                    $replyForm.show();
                    $replyForm.find('textarea').focus();
                    console.log('Reply form shown');
                }
            } else {
                console.error('Reply form not found for comment:', commentId);
            }
        });

        // Cancel Reply Handler - Use jQuery event delegation
        $(document).off('click', '.cancel-reply-btn').on('click', '.cancel-reply-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $btn = $(this);
            const commentId = $btn.data('comment-id');
            const $replyForm = $('#reply-form-' + commentId);
            if ($replyForm.length) {
                $replyForm.hide();
                $replyForm.find('textarea').val('');
            }
        });

        // Share Comment/Reply Handler - Use jQuery event delegation
        $(document).off('click', '.share-comment-btn').on('click', '.share-comment-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Share button clicked!');
            const $btn = $(this);
            const shareUrl = $btn.data('share-url') || $btn.attr('data-share-url');
            console.log('Share URL:', shareUrl);
            if (shareUrl) {
                if (typeof window.copyForumLink === 'function') {
                    console.log('Using copyForumLink function');
                    window.copyForumLink(shareUrl);
                } else {
                    console.warn('copyForumLink not available, using fallback');
                    fallbackCopy(shareUrl);
                }
            } else {
                console.error('No share URL found on button');
            }
        });

        // Copy Forum Link Handler - Use jQuery event delegation
        $(document).off('click', '.copy-link-btn').on('click', '.copy-link-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $btn = $(this);
            const shareUrl = $btn.data('share-url');
            console.log('Copy link button clicked, URL:', shareUrl);
            if (shareUrl) {
                if (typeof window.copyForumLink === 'function') {
                    window.copyForumLink(shareUrl);
                } else {
                    console.warn('copyForumLink not available, using fallback');
                    fallbackCopy(shareUrl);
                }
            }
        });

            }); // End jQuery ready - all handlers are set up here
        });
    }
    
    // Start initialization after window loads
    if (document.readyState === 'complete') {
        // Already loaded, start immediately
        console.log('Document already complete, starting handlers');
        initForumHandlers();
    } else {
        // Wait for window load to ensure all scripts (including footer scripts) are loaded
        window.addEventListener('load', function() {
            console.log('Window loaded - initializing forum handlers');
            initForumHandlers();
        });
    }
    })();
} catch(e) {
    console.error('Error in forum script initialization:', e);
    alert('Forum script error: ' + e.message);
}
</script>

