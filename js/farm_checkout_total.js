/**
 * يحفظ مبلغ الطلب في sessionStorage + localStorage ويستعيده بين صفحات KNET
 * حتى لا يُفقد مع إغلاق التبويب أو التنقل الطويل.
 */
(function (w) {
    var KEY = 'farm_checkout_total';

    function parseNum(s) {
        if (s == null || s === '') {
            return null;
        }
        var n = parseFloat(String(s).replace(',', '.'));
        return isFinite(n) && n >= 0 ? n : null;
    }

    function getStored() {
        var a = null;
        try {
            a = parseNum(sessionStorage.getItem(KEY));
        } catch (e) {}
        if (a !== null && a > 0) {
            return a;
        }
        try {
            a = parseNum(localStorage.getItem(KEY));
        } catch (e2) {}
        return a;
    }

    function setBoth(n) {
        var s = n.toFixed(3);
        try {
            sessionStorage.setItem(KEY, s);
        } catch (e) {}
        try {
            localStorage.setItem(KEY, s);
        } catch (e2) {}
    }

    w.farmCheckoutTotal = {
        storageKey: KEY,

        remember: function (totalStr) {
            var n = parseNum(totalStr);
            if (n !== null && n > 0) {
                setBoth(n);
            }
        },

        /**
         * @param {string} fromUrlOrServer قيمة total من الرابط أو من الخادم
         * @returns {string} ثلاث خانات عشرية
         */
        resolve: function (fromUrlOrServer) {
            var fromInput = parseNum(fromUrlOrServer);
            var stored = getStored();

            if (fromInput !== null && fromInput > 0) {
                setBoth(fromInput);
                return fromInput.toFixed(3);
            }

            if (stored !== null && stored > 0) {
                return stored.toFixed(3);
            }

            if (fromInput !== null) {
                return fromInput.toFixed(3);
            }
            if (stored !== null) {
                return stored.toFixed(3);
            }
            return '0.000';
        }
    };
})(window);
