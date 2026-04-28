@once
    <style>
        .imp-notify-wrap { position: relative; display: inline-flex; align-items: center; margin-right: 10px; }
        .imp-notify-btn {
            position: relative;
            width: 40px; height: 40px;
            border: 1px solid var(--green-soft, #C8E6C9); border-radius: 10px;
            background: var(--green-white, #E8F5E9);
            color: var(--green-dark, #1B5E20);
            cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .imp-notify-btn:hover { background: var(--green-soft, #C8E6C9); }
        .imp-notify-badge {
            position: absolute; top: 4px; right: 4px;
            min-width: 16px; height: 16px; padding: 0 4px;
            border-radius: 999px; background: #EF4444; color: #fff;
            font-size: 0.65rem; font-weight: 700; line-height: 16px; text-align: center;
            display: none;
        }
        .imp-notify-badge.imp-notify-badge--on { display: inline-block; }
        .imp-notify-panel {
            display: none;
            position: absolute; right: 0; top: 46px; z-index: 2000;
            width: min(360px, calc(100vw - 24px));
            max-height: 70vh; overflow: auto;
            background: #fff; color: #111827;
            border-radius: 12px; box-shadow: 0 12px 40px rgba(0,0,0,0.18);
            border: 1px solid #e5e7eb;
        }
        .imp-notify-panel.imp-notify-panel--open { display: block; }
        .imp-notify-head {
            padding: 10px 12px;
            font-weight: 700;
            font-size: 0.95rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
        .imp-notify-head-title { flex: 1; min-width: 0; }
        .imp-notify-mark-all {
            flex-shrink: 0;
            padding: 5px 10px;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 8px;
            border: 1px solid #047857;
            background: #fff;
            color: #047857;
            cursor: pointer;
            white-space: nowrap;
        }
        .imp-notify-mark-all:hover { background: #ecfdf5; }
        .imp-notify-mark-all:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }
        .imp-notify-mark-all[hidden] { display: none !important; }
        .imp-notify-row { padding: 12px 14px; border-bottom: 1px solid #f3f4f6; font-size: 0.88rem; }
        .imp-notify-row:last-child { border-bottom: none; }
        .imp-notify-title { font-weight: 600; margin-bottom: 4px; color: #065f46; }
        .imp-notify-body { color: #4b5563; line-height: 1.45; }
        .imp-notify-meta { margin-top: 8px; font-size: 0.75rem; color: #9ca3af; }
        .imp-notify-actions { margin-top: 8px; }
        .imp-notify-actions a { color: #047857; font-weight: 600; text-decoration: none; }
        .imp-notify-actions a:hover { text-decoration: underline; }
        .imp-notify-empty { padding: 16px 14px; color: #6b7280; font-size: 0.88rem; }
        .imp-notify-error { padding: 12px 14px; color: #b91c1c; font-size: 0.85rem; }
    </style>
@endonce

<div class="imp-notify-wrap" data-imp-notify data-csrf="{{ csrf_token() }}">
    <button type="button" class="imp-notify-btn" aria-expanded="false" aria-haspopup="true" data-imp-notify-toggle title="Notifications">
        <i class="fas fa-bell" aria-hidden="true"></i>
        <span class="imp-notify-badge" data-imp-notify-badge aria-hidden="true"></span>
    </button>
    <div class="imp-notify-panel" data-imp-notify-panel role="region" aria-label="Notifications">
        <div class="imp-notify-head">
            <span class="imp-notify-head-title">Important updates</span>
            <button type="button" class="imp-notify-mark-all" data-imp-notify-mark-all hidden>Mark all as read</button>
        </div>
        <div data-imp-notify-list></div>
    </div>
</div>

<script>
(function () {
    var root = document.querySelector('[data-imp-notify]');
    if (!root) return;
    var btn = root.querySelector('[data-imp-notify-toggle]');
    var panel = root.querySelector('[data-imp-notify-panel]');
    var list = root.querySelector('[data-imp-notify-list]');
    var badge = root.querySelector('[data-imp-notify-badge]');
    var markAllBtn = root.querySelector('[data-imp-notify-mark-all]');
    if (!btn || !panel || !list || !badge || !markAllBtn) return;

    function csrf() {
        var t = root.getAttribute('data-csrf');
        if (t) return t;
        var m = document.querySelector('meta[name="csrf-token"]');
        if (m && m.getAttribute('content')) return m.getAttribute('content');
        var match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
        return match ? decodeURIComponent(match[1]) : '';
    }

    function esc(s) {
        return String(s || '').replace(/[&<>"']/g, function (c) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c] || c;
        });
    }

    var open = false;

    function setBadge(n) {
        if (n > 0) {
            badge.textContent = n > 99 ? '99+' : String(n);
            badge.classList.add('imp-notify-badge--on');
        } else {
            badge.textContent = '';
            badge.classList.remove('imp-notify-badge--on');
        }
    }

    function syncMarkAllButton(unreadCount) {
        if (unreadCount > 0) {
            markAllBtn.hidden = false;
            markAllBtn.disabled = false;
        } else {
            markAllBtn.hidden = true;
            markAllBtn.disabled = true;
        }
    }

    function render(items) {
        if (!items || !items.length) {
            list.innerHTML = '<div class="imp-notify-empty">No important notifications yet.</div>';
            return;
        }
        var html = '';
        for (var i = 0; i < items.length; i++) {
            var it = items[i];
            var link = it.action_url ? '<div class="imp-notify-actions"><a href="' + esc(it.action_url) + '">' + esc(it.action_label || 'Open') + '</a></div>' : '';
            var unread = !it.read_at;
            html += '<div class="imp-notify-row" data-nid="' + esc(it.id) + '"' + (unread ? ' data-unread="1"' : '') + '>' +
                '<div class="imp-notify-title">' + esc(it.title) + '</div>' +
                '<div class="imp-notify-body">' + esc(it.body) + '</div>' +
                link +
                '<div class="imp-notify-meta">' + esc((it.created_at || '').replace('T', ' ').split('.')[0]) + '</div>' +
                '</div>';
        }
        list.innerHTML = html;
    }

    function markRead(id, row) {
        fetch('/notifications/' + encodeURIComponent(id) + '/read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '{}'
        }).then(function (r) { return r.ok ? r.json() : null; }).then(function () {
            if (row) {
                row.removeAttribute('data-unread');
            }
            return fetch('/notifications', {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
        }).then(function (r) { return r && r.ok ? r.json() : {}; }).then(function (d) {
            var uc = (d && d.unread_count) || 0;
            setBadge(uc);
            syncMarkAllButton(uc);
        }).catch(function () {});
    }

    function load() {
        list.innerHTML = '<div class="imp-notify-empty">Loading…</div>';
        fetch('/notifications', {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) {
            if (!r.ok) throw new Error('bad');
            return r.json();
        }).then(function (data) {
            if (data && data.schema_pending) {
                list.innerHTML = '<div class="imp-notify-empty">Notifications are not enabled on this database yet. Run <code style="font-size:0.8em;">php artisan migrate</code> (central) and <code style="font-size:0.8em;">php artisan tenants:migrate</code> for tenant apps.</div>';
                setBadge(0);
                syncMarkAllButton(0);
                return;
            }
            render(data.items || []);
            var uc = data.unread_count || 0;
            setBadge(uc);
            syncMarkAllButton(uc);
        }).catch(function () {
            list.innerHTML = '<div class="imp-notify-error">Could not load notifications.</div>';
        });
    }

    function markAllRead() {
        markAllBtn.disabled = true;
        fetch('/notifications/read-all', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '{}'
        }).then(function (r) { return r.ok ? r.json() : null; }).then(function () {
            return fetch('/notifications', {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
        }).then(function (r) { return r && r.ok ? r.json() : {}; }).then(function (d) {
            if (d && d.schema_pending) {
                setBadge(0);
                syncMarkAllButton(0);
                return;
            }
            render((d && d.items) || []);
            var uc = (d && d.unread_count) || 0;
            setBadge(uc);
            syncMarkAllButton(uc);
        }).catch(function () {
            markAllBtn.disabled = false;
        });
    }

    markAllBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        markAllRead();
    });

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        open = !open;
        panel.classList.toggle('imp-notify-panel--open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            load();
        }
    });

    document.addEventListener('click', function () {
        if (!open) return;
        open = false;
        panel.classList.remove('imp-notify-panel--open');
        btn.setAttribute('aria-expanded', 'false');
    });

    panel.addEventListener('click', function (e) { e.stopPropagation(); });

    list.addEventListener('click', function (e) {
        var a = e.target.closest('a');
        if (a && a.getAttribute('href')) {
            e.preventDefault();
            var row = a.closest('.imp-notify-row');
            var id = row && row.getAttribute('data-nid');
            if (id) markRead(id, row);
            window.location.href = a.getAttribute('href');
            return;
        }
        var row = e.target.closest('.imp-notify-row[data-unread="1"]');
        if (row) {
            var nid = row.getAttribute('data-nid');
            if (nid) markRead(nid, row);
        }
    });
})();
</script>
