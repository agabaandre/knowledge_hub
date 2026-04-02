@extends(admin_layout())

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Communities</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format((int) $totalCommunities) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Unique Memberships</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format((int) $totalUniqueMemberships) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Memberships by {{ $geoLabel }}</div>
                    <div class="h4 mb-2 font-weight-bold text-gray-800">{{ number_format((int) $totalMembershipsByGeography) }}</div>
                    <div class="small text-muted">
                        @foreach($membershipsByGeography->take(5) as $g)
                            <div>{{ $g->geography_name ?: 'Unspecified' }}: {{ (int) $g->total }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-3">
                <i class="fa fa-users mr-2"></i>Community Participants Directory
            </h3>
            <form method="GET" action="{{ route('admin.commsofpractice.participants') }}" class="mb-0">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="small font-weight-bold">Search</label>
                        <input type="text" class="form-control form-control-sm" name="q" value="{{ $search->q ?? '' }}" placeholder="Name, email, title, organisation, country">
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Title</label>
                        <input type="text" class="form-control form-control-sm" name="title" value="{{ $search->title ?? '' }}" placeholder="Job title">
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Organisation</label>
                        <input type="text" class="form-control form-control-sm" name="organisation" value="{{ $search->organisation ?? '' }}" placeholder="Organisation">
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">{{ $geoLabel }}</label>
                        <select class="form-control form-control-sm" name="geography_id">
                            <option value="">All {{ \Illuminate\Support\Str::lower($geoLabel) }}s</option>
                            @foreach($geographies as $c)
                                @php $selectedGeo = (string)($search->geography_id ?? ($search->country_id ?? '')); @endphp
                                <option value="{{ $c->id }}" {{ $selectedGeo === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small font-weight-bold">Community</label>
                        <select class="form-control form-control-sm" name="community_id">
                            <option value="">All communities</option>
                            @foreach($communities as $c)
                                <option value="{{ $c->id }}" {{ (string)($search->community_id ?? '') === (string)$c->id ? 'selected' : '' }}>{{ $c->community_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Badge</label>
                        <select class="form-control form-control-sm" name="badge_type_id">
                            <option value="">Any badge</option>
                            @foreach($badgeTypes as $b)
                                <option value="{{ $b->id }}" {{ (string)($search->badge_type_id ?? '') === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm mr-1" type="submit"><i class="fa fa-filter mr-1"></i>Filter</button>
                        <a href="{{ route('admin.commsofpractice.participants') }}" class="btn btn-secondary btn-sm">Clear</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Participant Name</th>
                        <th>Email Address</th>
                        <th>Title</th>
                        <th>Organisation</th>
                        <th>{{ $geoLabel }}</th>
                        <th>Publication Contributions</th>
                        <th>Forum Contributions</th>
                        <th>Badge(s)</th>
                        <th>Communities Subscribed</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($participants as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->email }}</td>
                            <td>{{ $p->job_title ?: '—' }}</td>
                            <td>{{ $p->organization_name ?: '—' }}</td>
                            <td>{{ $p->geo_name ?: '—' }}</td>
                            <td>{{ $p->publication_contributions }}</td>
                            <td>{{ $p->forum_contributions }}</td>
                            <td>{{ $p->badge_labels ?: '—' }}</td>
                            <td>{{ $p->community_labels ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No participants found for selected filters.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $participants->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

