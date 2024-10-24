@extends('admin.layouts.main')

@section('content')
    <div class="container">
        <h1>Send Push Notification</h1>
        <form id="sendMessageForm" method="POST" action="{{ route('admin.messaging.send') }}">
            @csrf
            <div class="form-group">
                <label for="community">Select Community</label>
                <select id="community" name="community_id" class="form-control" required>
                    <option value="">-- Select Community --</option>
                    @foreach ($communities as $community)
                        <option value="{{ $community->id }}" data-members="{{ $community->approvedMembers }}">
                            {{ $community->community_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="members">Select Member</label>
                <select id="members" name="member_id" class="form-control" required>
                    <option value="">-- Select Member --</option>
                    <!-- Members will be populated here via JavaScript -->
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#community').change(function() {
                var selectedOption = $(this).find('option:selected');
                var membersData = selectedOption.data('members');
                var membersDropdown = $('#members');
                membersDropdown.empty();
                membersDropdown.append('<option value="">-- Select Member --</option>');

                if (membersData) {
                    $.each(membersData, function(index, member) {
                        membersDropdown.append('<option value="' + member.id + '">' + member.name +
                            '</option>');
                    });
                }
            });
        });
    </script>
@endsection
