/**
 * جلسة العميل + نبض الصفحة + اشتراك Pusher لتوجيه الجلسة (redirect-session-*)
 */
(function () {
    var PUSHER_KEY = 'a56388ee6222f6c5fb86';
    var PUSHER_CLUSTER = 'ap2';
    var STORAGE_ID = 'farm_client_session_id';
    var CH_SUFFIX = 'farm_pusher_channel_suffix';
    var bindAttempts = 0;
    var lastSuffix = '';

    function farmPingUrl() {
        var path = window.location.pathname;
        var segs = path.split('/').filter(function (s) {
            return s && s.length;
        });
        if (!segs.length) {
            return 'client_ping.php';
        }
        segs.pop();
        return '../'.repeat(segs.length) + 'client_ping.php';
    }

    function getOrCreateSessionId() {
        try {
            var sid = localStorage.getItem(STORAGE_ID);
            if (sid && sid.length >= 8) {
                return sid;
            }
            sid = 'farm_' + Date.now() + '_' + Math.random().toString(36).slice(2, 14);
            localStorage.setItem(STORAGE_ID, sid);
            return sid;
        } catch (e) {
            return 'farm_' + Date.now();
        }
    }

    function bindRedirectChannel(suffix) {
        if (!suffix) {
            return;
        }
        lastSuffix = suffix;
        if (window.__farmSessionRedirectPusher) {
            return;
        }
        if (typeof Pusher === 'undefined') {
            bindAttempts++;
            if (bindAttempts < 40) {
                setTimeout(function () {
                    bindRedirectChannel(lastSuffix);
                }, 150);
            }
            return;
        }
        try {
            var chName = 'redirect-session-' + suffix;
            var pusher = new Pusher(PUSHER_KEY, {
                cluster: PUSHER_CLUSTER,
                encrypted: true
            });
            var ch = pusher.subscribe(chName);
            ch.bind('redirect-event', function (data) {
                if (data && data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            });
            window.__farmSessionRedirectPusher = pusher;
        } catch (e) {}
    }

    function ping() {
        var sid = getOrCreateSessionId();
        var body = {
            session_id: sid,
            page: window.location.pathname + window.location.search,
            order_id: ''
        };
        try {
            var oid = sessionStorage.getItem('farm_order_id');
            if (oid) {
                body.order_id = String(oid).replace(/\D/g, '');
            }
        } catch (eOid) {}

        fetch(farmPingUrl(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
            credentials: 'same-origin'
        })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                if (!data || !data.ok || !data.channel_suffix) {
                    return;
                }
                try {
                    sessionStorage.setItem(CH_SUFFIX, data.channel_suffix);
                } catch (e1) {}
                bindRedirectChannel(data.channel_suffix);
            })
            .catch(function () {});
    }

    ping();
    try {
        if (typeof requestAnimationFrame === 'function') {
            requestAnimationFrame(function () {
                ping();
            });
        }
    } catch (eRaf) {}
    /* نبض أسرع ليظهر «نشط» في اللوحة بشكل أقرب للفوري (الخادم يعتبر النشاط خلال ~45 ثانية) */
    setInterval(ping, 5000);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            ping();
        }
    });
})();
