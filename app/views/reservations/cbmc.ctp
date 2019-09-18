<script>
    $(document).ready(function () {
        var costa = 550;
        var costc = 450;
        var cost_total = 0;
        var tour1 = 160;
        var tour2 = 650;
        var tour_adult = 0;
        var tour_child = 0;
        var credit_fee = 0;
        var extra_hotel = 180;
        var hotelNight = 0;
        var hotel_total = 0;
        var tour_total = 0;
        var total_price = 0;
        var final_price = 0;

        var adults = 1;
        var children = Number($("#children").val());
        var pax = adults + children;

        $(".kcbmc-form-submit").click(function () {
            $("input").each(function () {
                if ($(this).val() == '') {
                    $(this).css('border', '1px solid red');
                } else {
                    $(this).css('border', '');
                }
            });
        });

        $(".date").each(function () {
            $(this).datepicker();
            $(this).datepicker("option", "dateFormat", "yy-mm-dd");
            $(this).datepicker("option", "changeYear", true);
            $(this).datepicker("option", "minDate", new Date(1940, 1 - 1, 1));
            $(this).datepicker("option", "yearRange", "1940:2020");
        });

        $("#check_in").val("2020-06-25");
        $("#check_out").val("2020-06-27");

        $("#check_in").change(function () {
            $("#right-check_in").html($("#check_in").val());
            hotelNight = dateDiff($("#check_in").val(), $("#check_out").val());
            console.log(hotelNight);
            hotel_total = extra_hotel * hotelNight;
        });

        $("#check_out").change(function () {
            $("#right-check_out").html($("#check_out").val());
            hotelNight = dateDiff($("#check_in").val(), $("#check_out").val());
            hotel_total = extra_hotel * hotelNight;
        });

        $("#rooms").change(function () {
            $("#right-rooms").html($("#rooms").val());
        });

        $("#adults, #children").change(function () {
            adults = Number($("#adults").val());
            children = Number($("#children").val());
            pax = adults + children;

            //Update Number of rooms
            $("#rooms").html("");
            for (var i = 0; i < pax; i++) {
                if (i > 3) break;
                $("#rooms").append("<option value=\"" + (i + 1) + "\">" + (i + 1) + "</option>");
            }

            //Add input tag for participants
            $("#participants").html("");
            for (var i = 0; i < pax; i++) {
                $("#participants").append("<div class=\"kcbmc-participants\">\n" +
                    "                                <input disabled=\"disabled\" value=\"Guest #" + (i + 1) + "\" class=\"num\">\n" +
                    "                                <select name=\"data[Cbmc][title]\">\n" +
                    "                                    <option>Mr.</option>\n" +
                    "                                    <option>Ms.</option>\n" +
                    "                                    <option>Dr.</option>\n" +
                    "                                </select>\n" +
                    "                                <input class=\"kcbmc-participants-last\" type=\"text\" name=\"data[CbmcCustomers][lname0]\"\n" +
                    "                                       size=\"8\" placeholder=\"Last\" required>\n" +
                    "                                <input class=\"kcbmc-participants-first\" type=\"text\" name=\"data[CbmcCustomers][fname0]\"\n" +
                    "                                       size=\"15\" placeholder=\"First\">\n" +
                    "                                <input class=\"kcbmc-participants-dob date\" size=\"10\" type=\"text\"\n" +
                    "                                       name=\"data[CbmcCustomers][dob0]\"\n" +
                    "                                       placeholder=\"DOB\">\n" +
                    "                                <img src=\"https://ozshuttle.com/ozshuttle/images/icon_calen.gif\">\n" +
                    "                                <input class=\"kcbmc-participants-is_child\" type=\"hidden\"\n" +
                    "                                       name=\"data[CbmcCustomers][is_child0]\" value=\"0\">\n" +
                    "                            </div>");
            }

            $("#ptcp_info").html("");
            for (var i = 0; i < pax; i++) {
                $("#ptcp_info").append("<div style=\"display: flex\">\n" +
                    "                                <input disabled=\"disabled\" value=\"Guest #" + (i + 1) + "\" class=\"num\">\n" +
                    "                                <input class=\"col\" type=\"text\" name=\"data[CbmcCustomers][phone0]\"\n" +
                    "                                       placeholder=\"Phone 000-000-0000\">\n" +
                    "                                <input class=\"col\" type=\"text\" name=\"data[CbmcCustomers][email0]\" placeholder=\"Email\">\n" +
                    "                                <input class=\"col\" type=\"text\" name=\"data[CbmcCustomers][kakao0]\"\n" +
                    "                                       placeholder=\"Kakao Talk ID\">\n" +
                    "                            </div>");
            }

            $(".date").each(function () {
                $(this).datepicker();
                $(this).datepicker("option", "dateFormat", "yy-mm-dd");
                $(this).datepicker("option", "changeYear", true);
                $(this).datepicker("option", "minDate", new Date(1940, 1 - 1, 1));
                $(this).datepicker("option", "yearRange", "1940:2020");
            });

            if ($("#tour1").prop("checked")) {
                $("#sum_tour1").css('display', 'block');
                $("#sum_tour2").css('display', 'none');
                tour_total = (tour1 * adults) + (tour1 * children);
            } else if ($("#tour2").prop("checked")) {
                $("#sum_tour1").css('display', 'none');
                $("#sum_tour2").css('display', 'block');
                tour_total = (tour2 * adults) + (tour2 * children);
            } else if ($("#tour0").prop("checked")) {
                $("#sum_tour1").css('display', 'none');
                $("#sum_tour2").css('display', 'none');
                tour_total = 0;
            }

            //Change number of participants of summary
            $(".adults").each(function () {
                $(this).html(adults);
            });

            $(".children").each(function () {
                $(this).html(children);
            });
            //////////////////////////////////////////

            $(".kcbmc-form-submit").click(function () {
                $("input").each(function () {
                    if ($(this).val() == '') {
                        $(this).css('border', '1px solid red');
                    } else {
                        $(this).css('border', '');
                    }
                });
            });

            //Change cost of summary
            $("#adult_cost").html((costa * adults).toFixed(2));
            $("#child_cost").html((costc * children).toFixed(2));
            $("#tour1_costa").html((tour1 * adults).toFixed(2));
            $("#tour1_costc").html((tour1 * children).toFixed(2));
            $("#tour2_costa").html((tour2 * adults).toFixed(2));
            $("#tour2_costc").html((tour2 * children).toFixed(2));

            cost_total = (adults * costa) + (children * costc);
            total_price = cost_total + tour_total + hotel_total;
            if ($("#card").prop("checked"))
                credit_fee = total_price * 0.035;
            else
                credit_fee = 0;
            final_price = total_price - credit_fee;
            $("#credit_fee").html(credit_fee.toFixed(2));
            $("#total_price").val(final_price);
            $("#total").html("$" + final_price.toFixed(2) + "");
            $("#total2").html(final_price.toFixed(2));
        });

        $("#air, #car").change(function () {
            if ($("#car").prop("checked")) {
                $("#arr_info").html("<input type=\"text\" class=\"col\" name=\"data[Cbmc][dep_city]\" size=\"5\" placeholder=\"출발 도시\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][dep_time]\" size=\"5\" placeholder=\"출발 시간\">\n" +
                    "                                <input disabled=\"disabled\" class=\"col\" type=\"text\" name=\"data[Cbmc][dep_flight]\" size=\"5\" placeholder=\"N/A\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_city]\" size=\"5\" placeholder=\"리턴 도시\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_time]\" size=\"5\" placeholder=\"출발 시간\">\n" +
                    "                                <input disabled=\"disabled\" class=\"col\" type=\"text\" name=\"data[Cbmc][rtn_flight]\" size=\"5\" placeholder=\"N/A\">");
            } else {
                $("#arr_info").html("<input type=\"text\" class=\"col\" name=\"data[Cbmc][dep_city]\" size=\"5\" placeholder=\"출발 도시\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][dep_time]\" size=\"5\" placeholder=\"출발 시간\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][dep_flight]\" size=\"5\" placeholder=\"항공편명\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_city]\" size=\"5\" placeholder=\"리턴 도시\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_time]\" size=\"5\" placeholder=\"출발 시간\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_flight]\" size=\"5\" placeholder=\"항공편명\">")
            }

            $(".kcbmc-form-submit").click(function () {
                $("input").each(function () {
                    if ($(this).val() == '') {
                        $(this).css('border', '1px solid #f00');
                    } else {
                        $(this).css('border', '');
                    }
                });
            });
        });

        $("#tour0, #tour1, #tour2").change(function () {
            if ($("#tour1").prop("checked")) {
                $("#sum_tour1").css('display', 'block');
                $("#sum_tour2").css('display', 'none');
                tour_total = (tour1 * adults) + (tour1 * children);
            } else if ($("#tour2").prop("checked")) {
                $("#sum_tour1").css('display', 'none');
                $("#sum_tour2").css('display', 'block');
                tour_total = (tour2 * adults) + (tour2 * children);
            } else if ($("#tour0").prop("checked")) {
                $("#sum_tour1").css('display', 'none');
                $("#sum_tour2").css('display', 'none');
                tour_total = 0;
            }

            cost_total = (adults * costa) + (children * costc);
            total_price = cost_total + tour_total + hotel_total;
            if ($("#card").prop("checked"))
                credit_fee = total_price * 0.035;
            else
                credit_fee = 0;
            final_price = total_price - credit_fee;
            $("#credit_fee").html(credit_fee.toFixed(2));
            $("#total_price").val(final_price);
            $("#total").html("$" + final_price.toFixed(2) + "");
            $("#total2").html(final_price.toFixed(2));
        });

        $("#card, #check").change(function () {
            if ($("#card").prop("checked")) {
                $("#payment-contents").html("<input type=\"text\" placeholder=\"Credit Card Number\">\n" +
                    "                            <input type=\"text\" placeholder=\"Exp Date\">\n" +
                    "                            <input type=\"text\" placeholder=\"CID Number\">\n" +
                    "                            <input type=\"text\" placeholder=\"Name of Card Holder\">\n" +
                    "                            <input type=\"text\" placeholder=\"Address\">\n" +
                    "                            <input type=\"text\" placeholder=\"City\">\n" +
                    "                            <input type=\"text\" placeholder=\"State\">\n" +
                    "                            <input type=\"text\" placeholder=\"Zip\">");
                cost_total = (adults * costa) + (children * costc);
                total_price = cost_total + tour_total + hotel_total;
                credit_fee = total_price * 0.035;
                final_price = total_price - credit_fee;
                $("#credit_fee").html(credit_fee.toFixed(2));
                $("#total_price").val(final_price);
                $("#total").html("$" + final_price.toFixed(2) + "");
                $("#total2").html(final_price.toFixed(2));
            } else {
                $("#payment-contents").html("<div>\n" +
                    "                            <p>개인 수표 보내실 주소</p>\n" +
                    "                            <p>Payable to KCBMC</p>\n" +
                    "                            <p>1355 W. Cheltenham Ave</p>\n" +
                    "                            <p>Suite 105</p>\n" +
                    "                            <p>Elkins Park, 19027</p>\n" +
                    "                        </div>");
                cost_total = (adults * costa) + (children * costc);
                total_price = cost_total + tour_total + hotel_total;
                credit_fee = 0;
                final_price = total_price - credit_fee;
                $("#credit_fee").html(credit_fee.toFixed(2));
                $("#total_price").val(final_price);
                $("#total").html("$" + final_price.toFixed(2) + "");
                $("#total2").html(final_price.toFixed(2));
            }

            $(".kcbmc-form-submit").click(function () {
                $("input").each(function () {
                    if ($(this).val() == '') {
                        $(this).css('border', '1px solid red');
                    } else {
                        $(this).css('border', '');
                    }
                });
            });
        });
        $("#sig_date").datepicker().datepicker('setDate', 'today')
    });
</script>

<div class="kcbmc-container">
    <div class="kcbmc-wrapper">
        <div class="kcbmc-top">
            <div class="kcbmc-logo">
                <img src="/img/kcbmc/CBMC-Logo-1200.jpg">
            </div>
            <div style="width: 1px; height: 80%; background-color: #bbb;"></div>
            <div class="kcbmc-title">
                <p>제25차 북미주 KCBMC</p>
                <p style="font-size: 20px;">필라델피아 대회</p>
                <p>2020년 6월 25일 ~ 27일</p>
            </div>
        </div>
        <div class="kcbmc-bar">
            <p style="color: maroon; font-size: 16px;"><strong>Official Registration Form</strong></p>
            <p style="color: blue;">각 칸을 비우지 말고 모두 채워주세요.</p>
        </div>
        <div style="position: relative; width: 95%; height: 2px; background-color: #ddd; margin: 0 auto;"></div>
        <form class="kcbmc-form needs-validation" action="/reservations/cbmc_book" enctype="multipart/form-data"
              method="post"
              accept-charset="utf-8">

            <!-------------------------------------- Hidden contents --------------------------------------------->
            <input type="hidden" name="data[Reservation][status]" value="New" id="ReservationStatus">
            <input type="hidden" name="data[Reservation][cat_id]" value="7" id="ReservationCatId">
            <input type="hidden" name="data[Item][tour_id]" value="974" id="ItemTourId">
            <input type="hidden" name="data[Reservation][tour_name]" value="[CBMS] Philadelphia"
                   id="ReservationTourName">
            <input type="hidden" name="data[Item][status]" value="대기" id="ItemStatus">
            <input type="hidden" name="data[Item][tour_date]" value="2020-06-25">
            <!-------------------------------------- Hidden contents --------------------------------------------->

            <div class="kcbmc-section">
                <div class="kcbmc-section-left">
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Number of Participants & Associated regional Chapter</p>
                        <div>
                            <select id="adults" name="data[CbmcReservations][adults]" required>
                                <option disabled="disabled" value="1" selected>Adults</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                            <select id="children" name="data[CbmcReservations][children]" required>
                                <option disabled="disabled" value="0" selected>Children</option>
                                <option>0</option>
                                <option>1</option>
                                <option>2</option>
                            </select>
                            <select name="data[CbmcCustomers][country]" required>
                                <option disabled="disabled" value="USA" selected>소속 국가</option>
                                <option>USA</option>
                                <option>Canada</option>
                                <option>Korea</option>
                                <option>China</option>
                                <option>Vietnam</option>
                                <option>European Union</option>
                                <option>Etc.</option>
                            </select>
                            <select name="data[CbmcCustomers][branch]" required>
                                <option disabled="disabled" selected>지부</option>
                                <option>NorthEastern</option>
                                <option>Eastern</option>
                                <option>Central</option>
                                <option>Central Northern</option>
                                <option>Southern</option>
                                <option>Not Known</option>
                            </select>
                            <select name="data[CbmcCustomers][position]" required>
                                <option disabled="disabled" selected="selected">직책</option>
                                <option>회장</option>
                                <option>부회장</option>
                                <option>간사</option>
                            </select>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Name of Participants</p>
                        <div id="participants">
                            <div class="kcbmc-participants">
                                <input disabled="disabled" value="Guest #1" class="num">
                                <select name="data[Cbmc][title]" required>
                                    <option>Mr.</option>
                                    <option>Ms.</option>
                                    <option>Dr.</option>
                                </select>
                                <input class="kcbmc-participants-last" type="text" name="data[CbmcCustomers][lname0]"
                                       size="8" placeholder="Last" required>
                                <input class="kcbmc-participants-first" type="text" name="data[CbmcCustomers][fname0]"
                                       size="15" placeholder="First" required>
                                <input class="kcbmc-participants-dob date" size="10" type="text"
                                       name="data[CbmcCustomers][dob0]"
                                       placeholder="DOB" required>
                                <img src="https://ozshuttle.com/ozshuttle/images/icon_calen.gif">
                                <input class="kcbmc-participants-is_child" type="hidden"
                                       name="data[CbmcCustomers][is_child0]" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Name of Business</p>
                        <div id="participants">
                            <input type="text" size="40" placeholder="Name of Business" required>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Mailing Address, Email, & Cell Phone numbers</p>
                        <div>
                            <input style="width: 45%;" type="text" name="data[CbmcCustomers][address]"
                                   size="25"
                                   placeholder="Address" required><br>
                            <input style="width: 30%" type="text" name="data[CbmcCustomers][city]" size="10"
                                   placeholder="City" required>
                            <select style="width: 10%" name="data[CbmcCustomers][state]" required>
                                <option value="AL">AL</option>
                                <option value="AK">AK</option>
                                <option value="AR">AR</option>
                                <option value="AZ">AZ</option>
                                <option value="CA">CA</option>
                                <option value="CO">CO</option>
                                <option value="CT">CT</option>
                                <option value="DC">DC</option>
                                <option value="DE">DE</option>
                                <option value="FL">FL</option>
                                <option value="GA">GA</option>
                                <option value="HI">HI</option>
                                <option value="IA">IA</option>
                                <option value="ID">ID</option>
                                <option value="IL">IL</option>
                                <option value="IN">IN</option>
                                <option value="KS">KS</option>
                                <option value="KY">KY</option>
                                <option value="LA">LA</option>
                                <option value="MA">MA</option>
                                <option value="MD">MD</option>
                                <option value="ME">ME</option>
                                <option value="MI">MI</option>
                                <option value="MN">MN</option>
                                <option value="MO">MO</option>
                                <option value="MS">MS</option>
                                <option value="MT">MT</option>
                                <option value="NC">NC</option>
                                <option value="NE">NE</option>
                                <option value="NH">NH</option>
                                <option value="NJ">NJ</option>
                                <option value="NM">NM</option>
                                <option value="NV">NV</option>
                                <option value="NY">NY</option>
                                <option value="ND">ND</option>
                                <option value="OH">OH</option>
                                <option value="OK">OK</option>
                                <option value="OR">OR</option>
                                <option value="PA">PA</option>
                                <option value="RI">RI</option>
                                <option value="SC">SC</option>
                                <option value="SD">SD</option>
                                <option value="TN">TN</option>
                                <option value="TX">TX</option>
                                <option value="UT">UT</option>
                                <option value="VT">VT</option>
                                <option value="VA">VA</option>
                                <option value="WA">WA</option>
                                <option value="WI">WI</option>
                                <option value="WV">WV</option>
                                <option value="WY">WY</option>
                            </select>
                            <input style="width: 15%" type="text" name="data[CbmcCustomers][zip]" size="2"
                                   placeholder="Zip" required>
                        </div>
                        <div id="ptcp_info">
                            <div style="display: flex">
                                <input disabled="disabled" value="Guest #1" class="num" required>
                                <input class="col" type="text" name="data[CbmcCustomers][phone0]"
                                       placeholder="Phone 000-000-0000" required>
                                <input class="col" type="text" name="data[CbmcCustomers][email0]" placeholder="Email" required>
                                <input class="col" type="text" name="data[CbmcCustomers][kakao0]"
                                       placeholder="Kakao Talk ID" required>
                            </div>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Philadelphia Arrival information
                            <label for="air">
                                <input id="air" type="radio" name="data[Cbmc][arr_method]" checked="checked"
                                       value="Airplane">
                                비행기
                            </label>
                            <label for="car">
                                <input id="car" type="radio" name="data[Cbmc][arr_method]" value="Car">
                                자동차
                            </label>
                        </p>
                        <div>
                            <div id="arr_info">
                                <input type="text" class="col" name="data[Cbmc][dep_city]" size="5" placeholder="출발 도시" required>
                                <input type="text" class="col" name="data[Cbmc][dep_time]" size="5" placeholder="출발 시간" required>
                                <input type="text" class="col" name="data[Cbmc][dep_flight]" size="5"
                                       placeholder="항공편명" required>
                                <input type="text" class="col" name="data[Cbmc][rtn_city]" size="5" placeholder="리턴 도시" required>
                                <input type="text" class="col" name="data[Cbmc][rtn_time]" size="5" placeholder="출발 시간" required>
                                <input type="text" class="col" name="data[Cbmc][rtn_flight]" size="5"
                                       placeholder="항공편명" required>
                            </div>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Hotel Reservations</p>
                        <div id="hotel">
                            <div>
                                <span>Check in</span>
                                <input id="check_in" class="date" type="text" name="data[CbmcCustomers][check_in]" required>
                                <img src="https://ozshuttle.com/ozshuttle/images/icon_calen.gif">
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;방 갯수</span>
                                <select id="rooms" name="data[Reservations][rooms]">
                                    <option value="1">1</option>
                                </select>
                            </div>
                            <div>
                                <span>Check out</span>
                                <input id="check_out" class="date" type="text" name="data[CbmcCustomers][check_out]" required>
                                <img src="https://ozshuttle.com/ozshuttle/images/icon_calen.gif">
                                <span>&nbsp;&nbsp;침대 종류</span>
                                <select name="data['CbmcReservations']['bed_type']">
                                    <option value="Twin">Twin</option>
                                    <option value="Queen">Queen</option>
                                </select>
                            </div>
                            <div class="kcbmc-exp">
                                <p>- Double Tree Hotel Philadelphia Downtown</p>
                                <p>- Address: 237 S Broad St, Philadelphia, PA 19107</p>
                                <p>- Phone: (215) 893-1600</p>
                                <p>- 호텔은 2인1실 기준이며. 개인 독방을 원하시면 $180 을 추가하셔야 합니다.</p>
                                <p>- 대회전/후의 추가객실예약은 대회가격 ($180/1박)의 혜택을 협회에서 드립니다.</p>
                                <p>- 부부가 아닌경우 룸메이트를 원하시면 신청서에 알려 주시기 바랍니다.</p>
                            </div>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Please select optional items</p>
                        <div id="kcbmc-tour">
                            <div style="display: flex; align-items: center">
                                <input id="tour0" style="margin: 0 10px" type="radio" name="data[Cbmc][tour]"
                                       checked="checked" value="0">
                                <span>
                                    선택 안 함
                                </span>
                            </div>
                            <div style="display: flex; align-items: center">
                                <input id="tour1" style="margin: 0 10px" type="radio" name="data[Cbmc][tour]"
                                       value="1">
                                <span>
                                    랭캐스터 성극, Queen Esther <strong>$160.00/pp</strong>
                                </span>
                                <a href="#">
                                    [일정보기]
                                </a>
                            </div>
                            <div style="display: flex; align-items: center">
                                <input id="tour2" style="margin: 0 10px" type="radio" name="data[Cbmc][tour]"
                                       value="2">
                                <span>
                                    미동부지역 4박5일 하이라이트 <strong>$680.00/pp</strong>
                                </span>
                                <a href="#">
                                    [일정보기]
                                </a>
                            </div>
                            <div class="kcbmc-exp">
                                <p>- 옵션투어는 날자가 겹치는 관계로 두가지중 하나만 선택합니다.</p>
                                <p>- 상기옵션투어은 어린이 또는 소아할인이 적용되지 않습니다.</p>
                                <p>- 미동부투어 하이라이트 4박투어는 2인1실기준이며 전일정 식사/현지옵션/가이드팁이 포함되어있습니다.</p>
                            </div>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Special instructions or Remarks</p>
                        <div id="kcbmc-remark">
                            <textarea style="width: 100%;" rows="5" name="data[Reservation][remark]"
                                      placeholder="고객의 말씀 자유롭게 적어주세요."></textarea>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Payment Information (크레딧카드 결재사 3.5%의 수수료가 추가됩니다.)</p>
                        <label for="card" style="margin-right: 10px;">
                            <div style="display: flex; align-items: center">
                                <input id="card" type="radio" name="data[Reservation][payment_method]"
                                       checked="checked">Credit
                                Card
                            </div>
                        </label>
                        <label for="check" style="margin-right: 10px;">
                            <div style="display: flex; align-items: center">
                                <input id="check" type="radio" name="data[Reservation][payment_method]">Check
                            </div>
                        </label>
                        <div id="payment-contents">
                            <input type="text" name="data[Cbmc][credit_num]" placeholder="Credit Card Number" required>
                            <input type="text" name="data[Cbmc][credit_exp]" placeholder="MM/YYYY" required>
                            <input type="text" name="data[Cbmc][credit_cid]" placeholder="CID Number" required>
                            <input type="text" name="data[Cbmc][credit_holder]" placeholder="Name of Card Holder" required>
                            <input type="text" name="data[Cbmc][credit_bill]" placeholder="Billing Address" required>
                            <input type="text" name="data[Cbmc][credit_city]" placeholder="City" required>
                            <input type="text" name="data[Cbmc][credit_state]" placeholder="State" required>
                            <input type="text" name="data[Cbmc][credit_zip]" placeholder="Zip" required>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">E-Signature and Authorization to register</p>
                        <div id="kcbmc-sig">
                            <input type="text" name="data[Cbmc][sig_name]" placeholder="Names" required>
                            <input type="text" name="data[Cbmc][signature]" placeholder="Signature" required>
                            <input id="sig_date" name="data[Cbmc][sig_date]" class="date" type="text" placeholder="Date" required>
                        </div>
                    </div>
                    <div>
                        <p style="font-size: 11px;">
                            By Electronically signing above, I fully authorize the KCBMC to accept, register my
                            reservation, and charge my credit card in the amount $<span id="total2">0.00</span>. I am also fully
                            aware of
                            cancellation policy provided by the KCBMC.
                        </p>
                    </div>
                </div>
                <div class="kcbmc-section-right">
                    <div>Your Booking Detail</div>
                    <div class="kcbmc-details">
                        <div class="kcbmc-details-title">
                            Hotel Accommodation
                        </div>
                        <div class="kcbmc-details-contents">
                            <p>Check in: <span id="right-check_in">2020-06-25</span></p>
                            <p>Check Out: <span id="right-check_out">2020-06-27</span></p>
                            <p>Rooms booked: <span id="right-rooms">1</span></p>
                        </div>
                    </div>
                    <div class="kcbmc-details">
                        <div class="kcbmc-details-title">
                            Your Price Summary
                        </div>
                        <div class="kcbmc-details-contents">
                            <div class="kcbmc-cost">
                                <p style="font-weight: 600">대회 참가 비용 (Cost)</p>
                                <p>&nbsp;&nbsp;$550.00 x <span class="adults">1</span> = $<span
                                            id="adult_cost">550.00</span> (Adults)</p>
                                <p>&nbsp;&nbsp;$450.00 x <span class="children">0</span> = $<span
                                            id="child_cost">0.00</span> (Child)</p>
                            </div>
                            <div class="kcbmc-cost">
                                <p style="font-weight: 600">추가 투숙 비용</p>
                                <p id="extra_night">$200.00 x <span class="night">0</span>박 = $<span id="hotel_cost">0.00</span>
                                </p>
                            </div>
                            <div class="kcbmc-cost" id="sum_tour1" style="display:none">
                                <p style="font-weight: 600">랭캐스터성극: 에스더여왕</p>
                                <p>Departs 6/27/19 3:30pm</p>
                                <p id="adult_tour1">&nbsp;&nbsp;$160.00 x <span class="adults">1</span> = $<span
                                            id="tour1_costa">160.00</span> (Adults)</p>
                                <p id="child_tour1">&nbsp;&nbsp;$160.00 x <span class="children">0</span> = $<span
                                            id="tour1_costc">0</span> (Child)</p>
                            </div>
                            <div class="kcbmc-cost" id="sum_tour2" style="display:none;">
                                <p style="font-weight: 600">미동부 하이라이트 4박5일</p>
                                <p id="adult_tour2">&nbsp;&nbsp;$650.00 x <span class="adults">1</span> = $<span
                                            id="tour2_costa">650.00</span> (Adults)</p>
                                <p id="child_tour2">&nbsp;&nbsp;$650.00 x <span class="children">0</span> = $<span
                                            id="tour2_costc">0.00</span> (Child)</p>
                            </div>
                            <div class="kcbmc-cost">
                                <p style="font-weight: 600">크레딧 카드 수수료 (3.5%)</p>
                                <p>&nbsp;&nbsp;Cost: $<span id="credit_fee">49.50</span></p>
                            </div>
                            <div id="kcbmc-price" class="kcbmc-cost">
                                <p style="padding-top: 10px; border-top: 2px solid #0000cc">Total Due Now</p>
                                <p id="total">$0.00</p>
                                <p style="margin-top:10px; border-top: 2px solid #0000cc; color: red">1차 접수 마감일인<br>1월
                                    3일 전까지 완납하시면<br>15% 할인혜택을 받으실 수 있습니다.</p>
                            </div>
                            <input value="500" type="hidden" id="total_price" name="data[CbmcCustomers][price]">
                            <input class="kcbmc-form-submit" type="submit" value="예약하기">
                        </div>
                    </div>
                </div>
            </div>
            <div class="kcbmc-footer">
                <p>안내사항</p>
                <div class="kcbmc-footer-contents">
                    <p>
                        1. 필라공항/호텔간 셔틀서비스는 개별적으로 택시/기차/우버등을 이용하여주십시요.<br>
                        - 참로고 필라델피아시에서 지정한 공항/호텔간 택시비는 균일가로 $25.00/편도이며<br>
                        - 공항에서 호텔까지는 약 15분 소요됩니다.
                    </p>
                    <p>
                        2. 자세한 정보는 대회 웹사이트 <a href="http://www.2019kcbmc.com">www.2019kcbmc.com</a>에서 상세히 안내해 드리고 있습니다
                    </p>
                    <p>
                        3. 옵션투어안내<br>
                        - 랭캐스터성극은 사전 좌석지정이 안됩니다. 따라서 극장에 도착하여 좌석을 배분 받습니다.<br>
                        - 동부투어는 투어가 종료한후 필라공항에 위치한 호텔에 투숙하신후 익일 조식을 마치신후 자유체크아웃입니다.
                    </p>
                    <p>
                        4. 추가 문의 사항이 있으시면 총연 사무국의 이메일 contact@kcbmc.net 또는 전화 703-439-1703 으로 문의 바랍니다.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>