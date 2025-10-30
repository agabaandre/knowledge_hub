@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
 <style>
  .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
  .af-card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
  .af-card-body{padding:16px}
 </style>
@endsection

@section('content')
<div class="row">
  <div class="card col-lg-12 af-card">
    <div class="af-card-header d-flex align-items-center justify-content-between">
      <strong>Dashboards & Admin-only Content</strong>
      <form method="get" class="form-inline">
        <input type="text" name="term" value="{{ request('term') }}" class="form-control form-control-sm" placeholder="Search title...">
        <button class="btn btn-outline-dark btn-sm ml-1" type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
    <div class="af-card-body">
      <div class="row" style="row-gap:16px;">
        @forelse($dashboards as $idx => $db)
          @php
            $statusText = $db->is_approved ? 'Approved' : ($db->is_rejected ? 'Rejected' : 'Pending');
            $statusClass = $db->is_approved ? 'badge-success' : ($db->is_rejected ? 'badge-danger' : 'badge-secondary');
            $isDashboard = ($db->publication_catgory_id ?? null) == 6 ? 'Dashboard' : '';
            $isAdminOnly = ($db->is_admin_only_access ?? 0) ? 'Admin Only' : '';
            $typeText = trim($isDashboard.' '.$isAdminOnly) ?: '—';
            $url = !$db->is_embedded ? url('/admin/dashboards').'?resource='.$db->id : ($db->publication ?? '#');
            $cover = $db->cover ?? asset('assets/images/cover.png');
          @endphp
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100" style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
              <div style="height:140px;background:#f8fafc;background-image:url('{{ $cover }}');background-size:cover;background-position:center;"></div>
              <div class="card-body d-flex flex-column">
                <h6 class="mb-1" style="font-weight:700;line-height:1.2;">{{ strip_tags(Str::limit($db->title, 80)) }}</h6>
                <div class="text-muted small mb-2">{{ $typeText }}</div>
                <p class="text-muted" style="font-size:.9rem;">{{ Str::limit(strip_tags($db->description), 110) }}</p>
                <div class="mt-auto d-flex align-items-center justify-content-between">
                  <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                  <a href="{{ $url }}" target="{{ $db->is_embedded ? '_blank' : '_self' }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-chart-line mr-1"></i> Open
                  </a>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-muted">No dashboards or admin-only content found.</div>
        @endforelse
      </div>
      @if(method_exists($dashboards, 'links'))
      <div class="py-2">{{ $dashboards->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
