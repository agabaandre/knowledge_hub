@extends('admin.layouts.main')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h1>Send Push Notification</h1>
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
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message
                            <p class="text-sm">Use {name} when u want the message to contain the users name</p>
                        </label>
                        <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize select2 for all select elements
            $('.select2').select2();

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

                // Refresh the select2 dropdown to reflect changes
                membersDropdown.trigger('change');
            });
        });
    </script>

    @include('common.select2')
@endsection
