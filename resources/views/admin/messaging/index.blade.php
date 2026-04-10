@extends(admin_layout())

@section('styles')
    <link href="{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h1 class="h4 mb-0">Messaging</h1>
                <p class="text-muted small mb-0">Send a push notification to the app, or email only with a rich HTML message.</p>
            </div>
            <div class="card-body">
                <form id="sendMessageForm" method="POST" action="{{ route('admin.messaging.send') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="community">Select Communities</label>
                                <select id="community" name="community_ids[]" class="form-control select2" required
                                    multiple>
                                    <option value="">-- Select Communities --</option>
                                    @foreach ($communities as $community)
                                        <option value="{{ $community->id }}"
                                            data-members="{{ json_encode($community->approvedMembers) }}">
                                            {{ $community->community_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="members">Select Members</label>
                                <select id="members" name="member_ids[]" class="form-control select2" multiple>
                                    <!-- Members will be populated here via JavaScript -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="email_only" name="email_only"
                                value="1" {{ old('email_only') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="email_only">Send as email only (no push notification)</label>
                        </div>
                        <p class="small text-muted mb-0" id="mode-help-push">
                            Push uses plain text. Use <code>{name}</code> in the message body to personalize (each push shows a generic placeholder).
                        </p>
                        <p class="small text-muted mb-0 d-none" id="mode-help-email">
                            Email is sent as HTML. Use <code>{name}</code> for the recipient&rsquo;s display name (escaped in the body).
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control" required
                            value="{{ old('title') }}">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="6" required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            function uploadImageForMessage(file, editor) {
                var data = new FormData();
                data.append('file', file);
                data.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ route('image.upload') }}",
                    type: 'POST',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var imageUrl = response.url || response;
                        $(editor).summernote('insertImage', imageUrl);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Image upload failed: ' + textStatus + ' ' + errorThrown);
                    }
                });
            }

            function htmlToPlain(html) {
                var d = document.createElement('div');
                d.innerHTML = html || '';
                return (d.textContent || d.innerText || '').trim();
            }

            function messageSummernoteActive() {
                return $('#message').next('.note-editor').length > 0;
            }

            function initMessageSummernote() {
                if (messageSummernoteActive()) {
                    return;
                }
                $('#message').summernote({
                    placeholder: 'Email body (HTML)',
                    tabsize: 2,
                    height: 280,
                    callbacks: {
                        onImageUpload: function(files) {
                            if (files && files.length > 0) {
                                uploadImageForMessage(files[0], this);
                            }
                        }
                    }
                });
            }

            function destroyMessageSummernote() {
                if (!messageSummernoteActive()) {
                    return;
                }
                var html = $('#message').summernote('code');
                $('#message').summernote('destroy');
                $('#message').val(htmlToPlain(html));
            }

            function applyMessagingMode() {
                var emailOnly = $('#email_only').is(':checked');
                if (emailOnly) {
                    $('#mode-help-push').addClass('d-none');
                    $('#mode-help-email').removeClass('d-none');
                    initMessageSummernote();
                } else {
                    $('#mode-help-email').addClass('d-none');
                    $('#mode-help-push').removeClass('d-none');
                    destroyMessageSummernote();
                }
            }

            $('#email_only').on('change', applyMessagingMode);
            applyMessagingMode();

            $('#community').change(function() {
                var selectedOptions = $(this).find('option:selected');
                var membersDropdown = $('#members');
                membersDropdown.empty();

                var membersSet = new Set();

                selectedOptions.each(function() {
                    var membersData = $(this).data('members');
                    if (membersData) {
                        $.each(membersData, function(index, member) {
                            membersSet.add(JSON.stringify(member.user));
                        });
                    }
                });

                membersSet.forEach(function(member) {
                    member = JSON.parse(member);
                    membersDropdown.append('<option value="' + member.id + '">' + member.name +
                        '</option>');
                });

                membersDropdown.trigger('change');
            });

            $('#sendMessageForm').on('submit', function() {
                if ($('#email_only').is(':checked') && messageSummernoteActive()) {
                    $('#message').val($('#message').summernote('code'));
                }
            });
        });
    </script>

    @include('common.select2')
@endsection
