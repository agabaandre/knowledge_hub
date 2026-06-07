@if(!empty($canFetchCourses))
<div id="courseSyncProgressWrap" class="alert alert-info d-none mb-3" role="status">
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
        <strong id="courseSyncProgressTitle">Fetching courses…</strong>
        <span id="courseSyncProgressPercent" class="badge bg-light text-dark">0%</span>
    </div>
    <div class="progress mb-2" style="height: 22px;">
        <div id="courseSyncProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
    </div>
    <div id="courseSyncProgressStep" class="small text-muted mb-1"></div>
    <div id="courseSyncProgressMessage" class="small"></div>
    <div id="courseSyncProgressCount" class="small fw-semibold mt-1"></div>
</div>

<script>
(function () {
    var pollTimer = null;
    var startUrl = @json(route('courses.fetch'));
    var statusUrlTemplate = @json(url('courses/fetch/__ID__'));
    var csrf = @json(csrf_token());

    function statusUrl(runId) {
        return statusUrlTemplate.replace('__ID__', runId);
    }

    function showProgressBox() {
        var wrap = document.getElementById('courseSyncProgressWrap');
        if (wrap) wrap.classList.remove('d-none');
    }

    function setAlertType(type) {
        var wrap = document.getElementById('courseSyncProgressWrap');
        if (!wrap) return;
        wrap.classList.remove('alert-info', 'alert-success', 'alert-danger', 'alert-warning');
        wrap.classList.add('alert-' + (type || 'info'));
    }

    function updateProgress(data) {
        showProgressBox();
        var bar = document.getElementById('courseSyncProgressBar');
        var pct = document.getElementById('courseSyncProgressPercent');
        var step = document.getElementById('courseSyncProgressStep');
        var message = document.getElementById('courseSyncProgressMessage');
        var count = document.getElementById('courseSyncProgressCount');
        var progress = Math.max(0, Math.min(100, parseInt(data.progress || 0, 10)));

        if (bar) {
            bar.style.width = progress + '%';
            bar.setAttribute('aria-valuenow', progress);
            bar.textContent = progress + '%';
            if (data.finished) {
                bar.classList.remove('progress-bar-animated');
            }
        }
        if (pct) pct.textContent = progress + '%';
        if (step) step.textContent = data.step || '';
        if (message) message.textContent = data.message || '';
        if (count) {
            var fetched = parseInt(data.courses_fetched || 0, 10);
            var total = parseInt(data.courses_total || 0, 10);
            count.textContent = total > 0
                ? fetched + ' of ' + total + ' courses fetched'
                : (fetched > 0 ? fetched + ' courses fetched' : '');
        }

        if (data.finished) {
            setAlertType(data.alert_type || 'success');
            document.getElementById('courseSyncProgressTitle').textContent =
                data.status === 'failed' ? 'Course fetch failed' : 'Course fetch complete';
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
            if (data.status === 'completed') {
                setTimeout(function () { window.location.reload(); }, 1500);
            }
        }
    }

    function pollRun(runId) {
        if (!runId) return;
        showProgressBox();

        function fetchStatus() {
            fetch(statusUrl(runId), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) { return response.json(); })
                .then(updateProgress)
                .catch(function () {});
        }

        fetchStatus();
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(fetchStatus, 2000);
    }

    var fetchBtn = document.getElementById('courseFetchBtn');
    if (fetchBtn) {
        fetchBtn.addEventListener('click', function () {
            if (!window.confirm('Fetch the latest courses from connected learning platforms?')) {
                return;
            }

            fetchBtn.disabled = true;
            showProgressBox();
            setAlertType('info');
            document.getElementById('courseSyncProgressTitle').textContent = 'Starting course fetch…';

            var body = new FormData();
            body.append('_token', csrf);

            fetch(startUrl, {
                method: 'POST',
                body: body,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    if (!result.ok || !result.data.success) {
                        setAlertType('danger');
                        document.getElementById('courseSyncProgressTitle').textContent = 'Unable to start fetch';
                        document.getElementById('courseSyncProgressMessage').textContent =
                            (result.data && result.data.message) ? result.data.message : 'Please try again.';
                        fetchBtn.disabled = false;
                        return;
                    }
                    if (result.data.sync && result.data.status) {
                        updateProgress(Object.assign({ finished: true, status: 'completed' }, result.data.status));
                        fetchBtn.disabled = false;
                        return;
                    }
                    pollRun(result.data.run_id);
                    fetchBtn.disabled = false;
                })
                .catch(function () {
                    fetchBtn.disabled = false;
                });
        });
    }
})();
</script>
@endif
