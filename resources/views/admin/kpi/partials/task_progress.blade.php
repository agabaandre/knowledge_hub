<div id="kpiTaskProgressWrap" class="alert alert-info d-none mb-3" role="status">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong id="kpiTaskProgressTitle">Running background task…</strong>
        <span id="kpiTaskProgressPercent" class="badge badge-light">0%</span>
    </div>
    <div class="progress mb-2" style="height: 22px;">
        <div id="kpiTaskProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
    </div>
    <div id="kpiTaskProgressStep" class="small text-muted mb-1"></div>
    <div id="kpiTaskProgressMessage" class="small"></div>
</div>

<script>
(function () {
    var pollTimer = null;
    var statusUrlTemplate = @json(url('admin/kpi/owid/task/__ID__'));
    var initialRunId = @json(session('kpi_sync_run_id'));

    function statusUrl(runId) {
        return statusUrlTemplate.replace('__ID__', runId);
    }

    function showProgressBox() {
        var wrap = document.getElementById('kpiTaskProgressWrap');
        if (wrap) {
            wrap.classList.remove('d-none');
        }
    }

    function setAlertType(type) {
        var wrap = document.getElementById('kpiTaskProgressWrap');
        if (!wrap) return;
        wrap.classList.remove('alert-info', 'alert-success', 'alert-danger', 'alert-warning');
        wrap.classList.add('alert-' + (type || 'info'));
    }

    function updateProgress(data) {
        showProgressBox();
        var bar = document.getElementById('kpiTaskProgressBar');
        var pct = document.getElementById('kpiTaskProgressPercent');
        var step = document.getElementById('kpiTaskProgressStep');
        var message = document.getElementById('kpiTaskProgressMessage');
        var progress = Math.max(0, Math.min(100, parseInt(data.progress || 0, 10)));

        if (bar) {
            bar.style.width = progress + '%';
            bar.setAttribute('aria-valuenow', progress);
            bar.textContent = progress + '%';
            if (data.finished) {
                bar.classList.remove('progress-bar-animated');
            }
        }
        if (pct) {
            pct.textContent = progress + '%';
        }
        if (step) {
            step.textContent = data.step || '';
        }
        if (message) {
            message.textContent = data.message || '';
        }

        if (data.finished) {
            setAlertType(data.alert_type || 'success');
            document.getElementById('kpiTaskProgressTitle').textContent =
                data.status === 'failed' ? 'Task failed' : 'Task complete';
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
            if (data.status === 'completed' && typeof reloadKpiIndicatorsTable === 'function') {
                reloadKpiIndicatorsTable();
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
        if (pollTimer) {
            clearInterval(pollTimer);
        }
        pollTimer = setInterval(fetchStatus, 2000);
    }

    window.startKpiTaskPoll = pollRun;

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || !form.matches || !form.matches('.js-kpi-queued-task')) {
            return;
        }

        event.preventDefault();
        var confirmMessage = form.getAttribute('data-confirm');
        if (confirmMessage && !window.confirm(confirmMessage)) {
            return;
        }

        var submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
        }

        if (form.closest('.modal') && window.jQuery) {
            window.jQuery(form.closest('.modal')).modal('hide');
        }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (!result.ok || !result.data.success) {
                    setAlertType('danger');
                    showProgressBox();
                    document.getElementById('kpiTaskProgressTitle').textContent = 'Unable to start task';
                    document.getElementById('kpiTaskProgressMessage').textContent = (result.data && result.data.message) ? result.data.message : 'Please try again.';
                    if (submitBtn) submitBtn.disabled = false;
                    return;
                }
                if (result.data.sync) {
                    updateProgress({
                        progress: 100,
                        step: 'Complete',
                        message: result.data.message || 'Done.',
                        finished: true,
                        status: 'completed',
                        alert_type: 'success'
                    });
                    if (submitBtn) submitBtn.disabled = false;
                    setTimeout(function () {
                        if (typeof reloadKpiIndicatorsTable === 'function') {
                            reloadKpiIndicatorsTable();
                        } else {
                            window.location.reload();
                        }
                    }, 1200);
                    return;
                }
                pollRun(result.data.run_id);
                if (submitBtn) submitBtn.disabled = false;
            })
            .catch(function () {
                form.submit();
            });
    }, true);

    if (initialRunId) {
        pollRun(initialRunId);
    }
})();
</script>
