<style>
    #kcbmc-form-info div {
        margin-right: 2px;
    }

    textarea {
        padding: 10px;
    }
</style>
<script>
    $(document).ready(function () {
        var costa = 450;
        var costc = 320;
        var early1 = dateDiff(printToday(), "2020-01-31");
        var early2 = dateDiff(printToday(), "2020-04-30");
        if (early1 < 0 && ealry2 >= 0) {
            costa = 470;
            costc = 340;
        } else if (early2 < 0) {
            costa = 490;
            costc = 360;
        }
        var cost_total = 0;
        var tour1 = 160;
        var tour2 = 680;
        var tour_adult = 0;
        var tour_child = 0;
        var credit_fee = 0;
        var extra_hotel = 210;
        var hotela = 140;
        var hotelc = 100;
        var hotel_rooms = 1;
        var hotelNight = 0;
        var hotel_total = 0;
        var tour_total = 0;
        var total_price = 0;
        var final_price = 0;

        var adults = 1;
        var children = Number($("#children").val());
        var pax = adults + children;
        var summaryOffset = $(".kcbmc-summary").offset();

        $("#costa").html(costa.toFixed(2));
        $("#costc").html(costc.toFixed(2));
        $("#adult_cost").html((costa * adults).toFixed(2));

        $(".kcbmc-participants-last, .kcbmc-participants-first").change(function () {
            var val = $(this).val();
            $(this).val(val.toUpperCase());
        });

        $(".time").timepicker({
            timeFormat: 'HH:mm',
            interval: 30,
            minTime: '0',
            maxTime: '23:59',
            startTime: '0',
            dynamic: false,
            dropdown: true,
            scrollbar: false
        });

        $("#mmyy").keyup(function () {
            var _val = this.value.trim();
            this.value = inputMmyy(_val);
        });

        $("#noc").change(function () {
            $("#sig_name").val($(this).val());
        });

        $("#phone0").keyup(function () {
            var _val = this.value.trim();
            this.value = inputPhone(_val);
        });

        $("#card_num").keyup(function () {
            var _val = this.value.trim();
            this.value = inputCredit(_val);
        });

        var windowWidth = $(window).width();

        if (windowWidth > 767) {
            $(window).scroll(function () {
                if ($(document).scrollTop() > (summaryOffset.top - 40)) {
                    $(".kcbmc-summary").css({
                        'position': 'fixed',
                        'top': '40px',
                        'width': '267px'
                    })
                } else {
                    $(".kcbmc-summary").css({
                        'position': '',
                        'top': '',
                        'width': ''
                    })
                }
            });
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

        $(".adult_date").each(function () {
            $(this).datepicker();
            $(this).datepicker("option", "dateFormat", "yy-mm-dd");
            $(this).datepicker("option", "changeYear", true);
            $(this).datepicker("option", "maxDate", new Date(2002, 6 - 1, 24));
            $(this).datepicker("option", "yearRange", "1920:2002");
        });

        $(".date").each(function () {
            $(this).datepicker();
            $(this).datepicker("option", "dateFormat", "yy-mm-dd");
            $(this).datepicker("option", "minDate", new Date(2020, 6 - 1, 23));
            $(this).datepicker("option", "maxDate", new Date(2020, 6 - 1, 30));
        });

        $("#check_in").val("2020-06-25");
        $("#check_out").val("2020-06-27");

        $("#check_in").change(function () {
            $("#right-check_in").html($("#check_in").val());
            hotelNight = dateDiff($("#check_in").val(), $("#check_out").val());
            if (hotelNight < 2) {
                hotelNight = 0;
            } else {
                hotelNight = hotelNight - 2;
                $(".night").html(hotelNight);
            }
            hotel_total = extra_hotel * hotelNight * hotel_rooms;
            $("#hotel_cost").html(hotel_total.toFixed(2));
            cost_total = (adults * costa) + (children * costc);
            total_price = cost_total + tour_total + hotel_total;
            if ($("#card").prop("checked"))
                credit_fee = total_price * 0.035;
            else
                credit_fee = 0;
            final_price = total_price + credit_fee;
            $("#credit_fee").html(credit_fee.toFixed(2));
            $("#total_price").val(final_price);
            $("#total").html("$" + final_price.toFixed(2) + "");
            $("#total2").html(final_price.toFixed(2));
        });

        $("#check_out").change(function () {
            $("#right-check_out").html($("#check_out").val());
            hotelNight = dateDiff($("#check_in").val(), $("#check_out").val());
            if (hotelNight < 2) {
                hotelNight = 0;
            } else {
                hotelNight = hotelNight - 2;
                $(".night").html(hotelNight);
            }
            hotel_total = extra_hotel * hotelNight * hotel_rooms;
            $("#hotel_cost").html(hotel_total.toFixed(2));
            cost_total = (adults * costa) + (children * costc);
            total_price = cost_total + tour_total + hotel_total;
            if ($("#card").prop("checked"))
                credit_fee = total_price * 0.035;
            else
                credit_fee = 0;
            final_price = total_price + credit_fee;
            $("#credit_fee").html(credit_fee.toFixed(2));
            $("#total_price").val(final_price);
            $("#total").html("$" + final_price.toFixed(2) + "");
            $("#total2").html(final_price.toFixed(2));
        });

        $("#rooms").change(function () {
            $("#right-rooms").html($(this).val());
            $(".rooms").html($(this).val());
            hotel_rooms = Number($(this).val());
            hotel_total = extra_hotel * hotelNight * hotel_rooms;
            $("#hotel_cost").html(hotel_total.toFixed(2));
            cost_total = (adults * costa) + (children * costc);
            total_price = cost_total + tour_total + hotel_total;
            if ($("#card").prop("checked"))
                credit_fee = total_price * 0.035;
            else
                credit_fee = 0;
            final_price = total_price + credit_fee;
            $("#credit_fee").html(credit_fee.toFixed(2));
            $("#total_price").val(final_price);
            $("#total").html("$" + final_price.toFixed(2) + "");
            $("#total2").html(final_price.toFixed(2));
        });

        $("#adults, #children").change(function () {
            adults = Number($("#adults").val());
            children = Number($("#children").val());
            pax = adults + children;

            //Update Number of rooms
            $("#rooms").html("");
            for (var i = 0; i < pax; i++) {
                if (i > 1) {
                    break;
                }
                $("#rooms").append("<option value=\"" + (i + 1) + "\">" + (i + 1) + "</option>");
            }

            //Add input tag for participants
            var j = 0;
            $("#participants").html("");
            for (j = 0; j < adults; j++) {
                $("#participants").append("<div class=\"kcbmc-participants\">\n" +
                    "                                <input disabled=\"disabled\" value=\"Adult #" + (j + 1) + "\" class=\"num\">\n" +
                    "                                <select name=\"data[Cbmc][title" + j + "]\">\n" +
                    "                                    <option value='Mr.'>Mr.</option>\n" +
                    "                                    <option value='Ms.'>Ms.</option>\n" +
                    "                                    <option value='Dr.'>Dr.</option>\n" +
                    "                                </select>\n" +
                    "                                <input class=\"kcbmc-participants-last\" type=\"text\" name=\"data[CbmcCustomers][lname" + j + "]\"\n" +
                    "                                       size=\"8\" placeholder=\"Last\" required>\n" +
                    "                                <input class=\"kcbmc-participants-first\" type=\"text\" name=\"data[CbmcCustomers][fname" + j + "]\"\n" +
                    "                                       size=\"15\" placeholder=\"First\" required>\n" +
                    "                                <input class=\"kcbmc-participants-dob adult_date\" size=\"10\" type=\"text\"\n" +
                    "                                       name=\"data[CbmcCustomers][dob" + j + "]\"\n" +
                    "                                       readonly='readonly' placeholder=\"DOB\" required>\n" +
                    "                                <img src=\"https://ozshuttle.com/ozshuttle/images/icon_calen.gif\">\n" +
                    "                                <input class=\"kcbmc-participants-is_child\" type=\"hidden\"\n" +
                    "                                       name=\"data[CbmcCustomers][is_child" + j + "]\" value=\"0\">\n" +
                    "                            </div>");
            }

            for (; j < pax; j++) {
                $("#participants").append("<div class=\"kcbmc-participants\">\n" +
                    "                                <input disabled=\"disabled\" value=\"Child #" + (j + 1) + "\" class=\"num\">\n" +
                    "                                <select name=\"data[Cbmc][title" + j + "]\" required>\n" +
                    "                                    <option value='MR.'>Mr.</option>\n" +
                    "                                    <option value='Ms.'>Ms.</option>\n" +
                    "                                    <option value='Dr.'>Dr.</option>\n" +
                    "                                </select>\n" +
                    "                                <input class=\"kcbmc-participants-last\" type=\"text\" name=\"data[CbmcCustomers][lname" + j + "]\"\n" +
                    "                                       size=\"8\" placeholder=\"Last\" required>\n" +
                    "                                <input class=\"kcbmc-participants-first\" type=\"text\" name=\"data[CbmcCustomers][fname" + j + "]\"\n" +
                    "                                       size=\"15\" placeholder=\"First\" required>\n" +
                    "                                <input class=\"kcbmc-participants-dob date_child\" size=\"10\" type=\"text\"\n" +
                    "                                       readonly='readonly' name=\"data[CbmcCustomers][dob" + j + "]\"\n" +
                    "                                       placeholder=\"DOB\" required>\n" +
                    "                                <img src=\"https://ozshuttle.com/ozshuttle/images/icon_calen.gif\">\n" +
                    "                                <input class=\"kcbmc-participants-is_child\" type=\"hidden\"\n" +
                    "                                       name=\"data[CbmcCustomers][is_child" + j + "]\" value=\"1\">\n" +
                    "                            </div>");
            }

            $(".kcbmc-participants-last, .kcbmc-participants-first").change(function () {
                var val = $(this).val()
                $(this).val(val.toUpperCase());
            });

            $("#ptcp_info").html("");
            for (j = 0; j < adults; j++) {
                $("#ptcp_info").append("<div style=\"display: flex\">\n" +
                    "                                <input disabled=\"disabled\" value=\"Adult #" + (j + 1) + "\" class=\"num\">\n" +
                    "                                <input id=\"phone" + j + "\" maxlength='12' class=\"col\" type=\"text\" name=\"data[CbmcCustomers][phone" + j + "]\"\n" +
                    "                                       placeholder=\"Phone\" required>\n" +
                    "                                <input id=\"email" + j + "\" class=\"col\" type=\"text\" name=\"data[CbmcCustomers][email" + j + "]\" placeholder=\"Email\" required>\n" +
                    "                                <input id=\"kakao" + j + "\" class=\"col\" type=\"text\" name=\"data[CbmcCustomers][kakao" + j + "]\"\n" +
                    "                                       placeholder=\"Kakao Talk ID\" required>\n" +
                    "                            </div>");
                $("#phone" + j + "").keyup(function () {
                    var _val = this.value.trim();
                    this.value = inputPhone(_val);
                });
            }

            for (; j < pax; j++) {
                $("#ptcp_info").append("<div style=\"display: flex\">\n" +
                    "                                <input disabled=\"disabled\" value=\"Child #" + (j + 1) + "\" class=\"num\">\n" +
                    "                                <input id=\"phone" + j + "\" maxlength='12' class=\"col\" type=\"text\" name=\"data[CbmcCustomers][phone" + j + "]\"\n" +
                    "                                       placeholder=\"Phone\" required>\n" +
                    "                                <input id=\"email" + j + "\" class=\"col\" type=\"text\" name=\"data[CbmcCustomers][email" + j + "]\" placeholder=\"Email\" required>\n" +
                    "                                <input id=\"kakao" + j + "\" class=\"col\" type=\"text\" name=\"data[CbmcCustomers][kakao" + j + "]\"\n" +
                    "                                       placeholder=\"Kakao Talk ID\" required>\n" +
                    "                            </div>");
                $("#phone" + j + "").keyup(function () {
                    var _val = this.value.trim();
                    this.value = inputPhone(_val);
                });
            }

            $("#phone0").change(function () {
                for (var i = 1; i < pax; i++) {
                    $("#phone" + i + "").val($("#phone0").val());
                }
            });

            $("#email0").change(function () {
                for (var i = 1; i < pax; i++) {
                    $("#email" + i + "").val($("#email0").val());
                }
            });

            $("#kakao0").change(function () {
                for (var i = 1; i < pax; i++) {
                    $("#kakao" + i + "").val($("#kakao0").val());
                }
            });

            $(".adult_date").each(function () {
                $(this).datepicker();
                $(this).datepicker("option", "dateFormat", "yy-mm-dd");
                $(this).datepicker("option", "changeYear", true);
                $(this).datepicker("option", "maxDate", new Date(2002, 6 - 1, 24));
                $(this).datepicker("option", "yearRange", "1920:2002");
            });

            $(".date_child").each(function () {
                $(this).datepicker();
                $(this).datepicker("option", "dateFormat", "yy-mm-dd");
                $(this).datepicker("option", "changeYear", true);
                $(this).datepicker("option", "minDate", new Date(2002, 6 - 1, 25));
                $(this).datepicker("option", "yearRange", "2002:2020");
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
            hotel_total = extra_hotel * hotelNight * hotel_rooms;
            total_price = cost_total + tour_total + hotel_total;
            if ($("#card").prop("checked"))
                credit_fee = total_price * 0.035;
            else
                credit_fee = 0;
            final_price = total_price + credit_fee;
            $("#credit_fee").html(credit_fee.toFixed(2));
            $("#total_price").val(final_price);
            $("#total").html("$" + final_price.toFixed(2) + "");
            $("#total2").html(final_price.toFixed(2));
        });

        $("#hotel_need, #hotel_no").change(function () {
            if ($("#hotel_need").prop("checked")) {
                $("#hotel").css("display", "");
                costa = costa + hotela;
                costc = costc + hotelc;
                $("#adult_cost").html((costa * adults).toFixed(2));
                $("#child_cost").html((costc * children).toFixed(2));
                $("#costa").html(costa.toFixed(2));
                $("#costc").html(costc.toFixed(2));
                calTotal();
            } else {
                $("#hotel").css("display", "none");
                costa = costa - hotela;
                costc = costc - hotelc;
                $("#adult_cost").html((costa * adults).toFixed(2));
                $("#child_cost").html((costc * children).toFixed(2));
                $("#costa").html(costa.toFixed(2));
                $("#costc").html(costc.toFixed(2));
                calTotal();
            }
        });

        $("#air, #car").change(function () {
            if ($("#car").prop("checked")) {
                $("#arr_info").html("<input type=\"text\" class=\"col\" name=\"data[Cbmc][dep_city]\" size=\"5\" placeholder=\"출발 도시\" required>\n" +
                    "                                <input class=\"time\" type=\"text\" class=\"col\" name=\"data[Cbmc][dep_time]\" size=\"5\" placeholder=\"도착 시간\" required>\n" +
                    "                                <input disabled=\"disabled\" class=\"col\" type=\"text\" name=\"data[Cbmc][dep_flight]\" size=\"5\" placeholder=\"N/A\">\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_city]\" size=\"5\" placeholder=\"리턴 도시\" required>\n" +
                    "                                <input class=\"time\" type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_time]\" size=\"5\" placeholder=\"출발 시간\" required>\n" +
                    "                                <input disabled=\"disabled\" class=\"col\" type=\"text\" name=\"data[Cbmc][rtn_flight]\" size=\"5\" placeholder=\"N/A\">");
            } else {
                $("#arr_info").html("<input type=\"text\" class=\"col\" name=\"data[Cbmc][dep_city]\" size=\"5\" placeholder=\"출발 도시\" required>\n" +
                    "                                <input class=\"time\" type=\"text\" class=\"col\" name=\"data[Cbmc][dep_time]\" size=\"5\" placeholder=\"도착 시간\" required>\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][dep_flight]\" size=\"5\" placeholder=\"항공편명\" required>\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_city]\" size=\"5\" placeholder=\"리턴 도시\" required>\n" +
                    "                                <input class=\"time\" type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_time]\" size=\"5\" placeholder=\"출발 시간\" required>\n" +
                    "                                <input type=\"text\" class=\"col\" name=\"data[Cbmc][rtn_flight]\" size=\"5\" placeholder=\"항공편명\" required>")
            }

            $(".time").timepicker({
                timeFormat: 'HH:mm',
                interval: 30,
                minTime: '0',
                maxTime: '23:59',
                startTime: '0',
                dynamic: false,
                dropdown: true,
                scrollbar: false
            });

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
            final_price = total_price + credit_fee;
            $("#credit_fee").html(credit_fee.toFixed(2));
            $("#total_price").val(final_price);
            $("#total").html("$" + final_price.toFixed(2) + "");
            $("#total2").html(final_price.toFixed(2));
        });

        $("#card, #check").change(function () {
            if ($("#card").prop("checked")) {
                $("#payment-contents").html("<input id=\"card_num\" type=\"text\" maxlength=\"19\" name=\"data[Cbmc][credit_num]\" placeholder=\"Credit Card Number\" required><br>\n" +
                    "                            <input id='mmyy' type=\"text\" maxlength=\"7\" size=\"10\" name=\"data[Cbmc][credit_exp]\" placeholder=\"MM/YYYY\" required>\n" +
                    "                            <input type=\"text\" maxlength=\"3\" size=\"3\" name=\"data[Cbmc][credit_cid]\" placeholder=\"CID\" required><br>\n" +
                    "                            <input id='noc' type=\"text\" name=\"data[Cbmc][credit_holder]\" placeholder=\"Name of Card Holder\" required><br>\n" +
                    "                            <input type=\"text\" name=\"data[Cbmc][credit_bill]\" placeholder=\"Billing Address\" required><br>\n" +
                    "                            <input type=\"text\" size=\"10\" name=\"data[Cbmc][credit_city]\" placeholder=\"City\" required>\n" +
                    "                            <select type=\"text\" name=\"data[Cbmc][credit_state]\" placeholder=\"State\" required>\n" +
                    "                                <option value=\"AL\">AL</option>\n" +
                    "                                <option value=\"AK\">AK</option>\n" +
                    "                                <option value=\"AR\">AR</option>\n" +
                    "                                <option value=\"AZ\">AZ</option>\n" +
                    "                                <option value=\"CA\">CA</option>\n" +
                    "                                <option value=\"CO\">CO</option>\n" +
                    "                                <option value=\"CT\">CT</option>\n" +
                    "                                <option value=\"DC\">DC</option>\n" +
                    "                                <option value=\"DE\">DE</option>\n" +
                    "                                <option value=\"FL\">FL</option>\n" +
                    "                                <option value=\"GA\">GA</option>\n" +
                    "                                <option value=\"HI\">HI</option>\n" +
                    "                                <option value=\"IA\">IA</option>\n" +
                    "                                <option value=\"ID\">ID</option>\n" +
                    "                                <option value=\"IL\">IL</option>\n" +
                    "                                <option value=\"IN\">IN</option>\n" +
                    "                                <option value=\"KS\">KS</option>\n" +
                    "                                <option value=\"KY\">KY</option>\n" +
                    "                                <option value=\"LA\">LA</option>\n" +
                    "                                <option value=\"MA\">MA</option>\n" +
                    "                                <option value=\"MD\">MD</option>\n" +
                    "                                <option value=\"ME\">ME</option>\n" +
                    "                                <option value=\"MI\">MI</option>\n" +
                    "                                <option value=\"MN\">MN</option>\n" +
                    "                                <option value=\"MO\">MO</option>\n" +
                    "                                <option value=\"MS\">MS</option>\n" +
                    "                                <option value=\"MT\">MT</option>\n" +
                    "                                <option value=\"NC\">NC</option>\n" +
                    "                                <option value=\"NE\">NE</option>\n" +
                    "                                <option value=\"NH\">NH</option>\n" +
                    "                                <option value=\"NJ\">NJ</option>\n" +
                    "                                <option value=\"NM\">NM</option>\n" +
                    "                                <option value=\"NV\">NV</option>\n" +
                    "                                <option value=\"NY\">NY</option>\n" +
                    "                                <option value=\"ND\">ND</option>\n" +
                    "                                <option value=\"OH\">OH</option>\n" +
                    "                                <option value=\"OK\">OK</option>\n" +
                    "                                <option value=\"OR\">OR</option>\n" +
                    "                                <option value=\"PA\">PA</option>\n" +
                    "                                <option value=\"RI\">RI</option>\n" +
                    "                                <option value=\"SC\">SC</option>\n" +
                    "                                <option value=\"SD\">SD</option>\n" +
                    "                                <option value=\"TN\">TN</option>\n" +
                    "                                <option value=\"TX\">TX</option>\n" +
                    "                                <option value=\"UT\">UT</option>\n" +
                    "                                <option value=\"VT\">VT</option>\n" +
                    "                                <option value=\"VA\">VA</option>\n" +
                    "                                <option value=\"WA\">WA</option>\n" +
                    "                                <option value=\"WI\">WI</option>\n" +
                    "                                <option value=\"WV\">WV</option>\n" +
                    "                                <option value=\"WY\">WY</option>\n" +
                    "                            </select>\n" +
                    "                            <input type=\"text\" size=\"5\" maxlength=\"5\" name=\"data[Cbmc][credit_zip]\" placeholder=\"Zip\" required>");
                cost_total = (adults * costa) + (children * costc);
                total_price = cost_total + tour_total + hotel_total;
                credit_fee = total_price * 0.035;
                final_price = total_price + credit_fee;
                $("#credit_fee").html(credit_fee.toFixed(2));
                $("#total_price").val(final_price);
                $("#total").html("$" + final_price.toFixed(2) + "");
                $("#total2").html(final_price.toFixed(2));
                $("#card_num").keyup(function () {
                    var _val = this.value.trim();
                    this.value = inputCredit(_val);
                });
                $("#noc").change(function () {
                    $("#sig_name").val($(this).val());
                });
                $("#mmyy").keyup(function () {
                    var _val = this.value.trim();
                    this.value = inputMmyy(_val);
                });
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
                final_price = total_price + credit_fee;
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
        $("#sig_date").val(printToday());

        cost_total = (adults * costa) + (children * costc);
        total_price = cost_total + tour_total + hotel_total;
        if ($("#card").prop("checked"))
            credit_fee = total_price * 0.035;
        else
            credit_fee = 0;
        final_price = total_price + credit_fee;
        $("#credit_fee").html(credit_fee.toFixed(2));
        $("#total_price").val(final_price);
        $("#total").html("$" + final_price.toFixed(2) + "");
        $("#total2").html(final_price.toFixed(2));

        function calTotal() {
            hotel_total = extra_hotel * hotelNight * hotel_rooms;
            $("#hotel_cost").html(hotel_total.toFixed(2));
            cost_total = (adults * costa) + (children * costc);
            total_price = cost_total + tour_total + hotel_total;
            if ($("#card").prop("checked"))
                credit_fee = total_price * 0.035;
            else
                credit_fee = 0;
            final_price = total_price + credit_fee;
            $("#credit_fee").html(credit_fee.toFixed(2));
            $("#total_price").val(final_price);
            $("#total").html("$" + final_price.toFixed(2) + "");
            $("#total2").html(final_price.toFixed(2));
        }
    });

    function printToday() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }

        if (mm < 10) {
            mm = '0' + mm
        }

        today = yyyy + '-' + mm + '-' + dd;
        return today;
    }

    function dateDiff(_date1, _date2) {
        var diffDate_1 = _date1 instanceof Date ? _date1 : new Date(_date1);
        var diffDate_2 = _date2 instanceof Date ? _date2 : new Date(_date2);

        diffDate_1 = new Date(diffDate_1.getFullYear(), diffDate_1.getMonth() + 1, diffDate_1.getDate());
        diffDate_2 = new Date(diffDate_2.getFullYear(), diffDate_2.getMonth() + 1, diffDate_2.getDate());

        var diff = Math.abs(diffDate_2.getTime() - diffDate_1.getTime());
        diff = Math.ceil(diff / (1000 * 3600 * 24));

        return diff;
    }

    // auto insert '-'
    function inputPhone(str) {
        str = str.replace(/[^0-9]/g, '');
        var tmp = '';
        if (str.length < 4) {
            return str;
        } else if (str.length < 7) {
            tmp += str.substr(0, 3);
            tmp += '-';
            tmp += str.substr(3);
            return tmp;
        } else if (str.length < 11) {
            tmp += str.substr(0, 3);
            tmp += '-';
            tmp += str.substr(3, 3);
            tmp += '-';
            tmp += str.substr(6);
            return tmp;
        } else {
            tmp += str.substr(0, 3);
            tmp += '-';
            tmp += str.substr(3, 4);
            tmp += '-';
            tmp += str.substr(7);
            return tmp;
        }
        return str;
    }

    function inputCredit(str) {
        str = str.replace(/[^0-9]/g, '');
        var tmp = '';
        if (str.length < 5) {
            return str;
        } else if (str.length < 9) {
            tmp += str.substr(0, 4);
            tmp += '-';
            tmp += str.substr(4);
            return tmp;
        } else if (str.length < 13) {
            tmp += str.substr(0, 4);
            tmp += '-';
            tmp += str.substr(4, 4);
            tmp += '-';
            tmp += str.substr(8);
            return tmp;
        } else {
            tmp += str.substr(0, 4);
            tmp += '-';
            tmp += str.substr(4, 4);
            tmp += '-';
            tmp += str.substr(8, 4);
            tmp += '-';
            tmp += str.substr(12);
            return tmp;
        }
        return str;
    }

    function inputMmyy(str) {
        str = str.replace(/[^0-9]/g, '');
        var tmp = '';
        if (str.length < 3) {
            return str;
        } else {
            tmp += str.substr(0, 2);
            tmp += '/';
            tmp += str.substr(2);
            return tmp;
        }
        return str;
    }
</script>

<div class="kcbmc-container">
    <div class="kcbmc-wrapper">
        <div class="kcbmc-top">
            <div class="kcbmc-logo">
                <img src="/img/kcbmc/CBMC-Logo-1200.jpg">
            </div>
            <div id="kcbcm-div" style="width: 1px; height: 80%; background-color: #bbb;"></div>
            <div class="kcbmc-title">
                <div class="kcbmc-title-mobile">
                    <p>제25차 북미주 KCBMC</p>
                    <p id="kcbmc-title-sub">필라델피아 대회</p>
                </div>
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
                        <div id="kcbmc-form-info" style="display: flex;">
                            <div style="width: 10%;">
                                <p>어른</p>
                                <select style="width: 100%;" id="adults" name="data[CbmcReservations][adults]" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div style="width: 10%;">
                                <p>소아</p>
                                <select style="width: 100%;" id="children" name="data[CbmcReservations][children]"
                                        required>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div style="width: 25%;">
                                <p>소속 국가</p>
                                <select style="width: 100%;" name="data[Cbmc][country]" required>
                                    <option value="USA">USA</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Korea">Korea</option>
                                    <option value="China">China</option>
                                    <option value="Vietnam">Vietnam</option>
                                    <option value="European Union">European Union</option>
                                    <option value="Etc.">Etc.</option>
                                </select>
                            </div>
                            <div style="width: 25%;">
                                <p>지부</p>
                                <select style="width: 100%;" name="data[Cbmc][chapter]" required>
                                    <option value="NorthEastern">NorthEastern</option>
                                    <option value="Eastern">Eastern</option>
                                    <option value="Central">Central</option>
                                    <option value="Central Northern">Central Northern</option>
                                    <option value="Southern">Southern</option>
                                    <option value="Not Known">Not Known</option>
                                </select>
                            </div>
                            <div style="width: 15%;">
                                <p>직책</p>
                                <select style="width: 100%;" name="data[Cbmc][position]" required>
                                    <option value="회장">회장</option>
                                    <option value="부회장">부회장</option>
                                    <option value="간사">간사</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Name of Participants</p>
                        <div id="participants">
                            <div class="kcbmc-participants">
                                <input disabled="disabled" value="Adult #1" class="num">
                                <select name="data[Cbmc][title0]" required>
                                    <option value="Mr.">Mr.</option>
                                    <option value="Ms.">Ms.</option>
                                    <option value="Dr.">Dr.</option>
                                </select>
                                <input class="kcbmc-participants-last" type="text" name="data[CbmcCustomers][lname0]"
                                       size="8" placeholder="Last" required>
                                <input class="kcbmc-participants-first" type="text" name="data[CbmcCustomers][fname0]"
                                       size="15" placeholder="First" required>
                                <input readonly="readonly" class="kcbmc-participants-dob adult_date" size="10"
                                       type="text"
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
                            <input type="text" size="40" name="data[Cbmc][business_name]" placeholder="Name of Business"
                                   required>
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
                                   maxlength="5" placeholder="Zip" required>
                        </div>
                        <div id="ptcp_info">
                            <div style="display: flex">
                                <input disabled="disabled" value="Adult #1" class="num" required>
                                <input maxlength="12" id="phone0" class="col" type="text"
                                       name="data[CbmcCustomers][phone0]"
                                       placeholder="Phone" required>
                                <input class="col" type="text" name="data[CbmcCustomers][email0]" placeholder="Email"
                                       required>
                                <input class="col" type="text" name="data[Cbmc][kakao0]"
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
                                <input type="text" class="col" name="data[Cbmc][dep_city]" size="5" placeholder="출발 도시"
                                       required>
                                <input class="time" readonly="readonly" class="col" name="data[Cbmc][dep_time]" size="5"
                                       placeholder="도착 시간" required>
                                <input type="text" class="col" name="data[Cbmc][dep_flight]" size="5"
                                       placeholder="항공편명" required>
                                <input type="text" class="col" name="data[Cbmc][rtn_city]" size="5" placeholder="리턴 도시"
                                       required>
                                <input class="time" readonly="readonly" class="col" name="data[Cbmc][rtn_time]" size="5"
                                       placeholder="출발 시간" required>
                                <input type="text" class="col" name="data[Cbmc][rtn_flight]" size="5"
                                       placeholder="항공편명" required>
                            </div>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">Hotel Reservations
                            <label for="hotel_need">
                                <input id="hotel_need" type="radio" name="hotel_need" value="1" checked="checked">
                                예약
                            </label>
                            <label for="hotel_no">
                                <input id="hotel_no" type="radio" name="hotel_need" value="0">
                                예약 안 함
                            </label>
                        </p>
                        <div id="hotel">
                            <div>
                                <span>Check in</span>
                                <input id="check_in" class="date" type="text" name="data[Cbmc][check_in]"
                                       required>
                                <img src="https://ozshuttle.com/ozshuttle/images/icon_calen.gif">
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;방 갯수</span>
                                <select id="rooms" name="data[Reservation][room]">
                                    <option value="1">1</option>
                                </select>
                            </div>
                            <div>
                                <span>Check out</span>
                                <input id="check_out" class="date" type="text" name="data[Cbmc][check_out]"
                                       required>
                                <img src="https://ozshuttle.com/ozshuttle/images/icon_calen.gif">
                                <span>&nbsp;&nbsp;침대 종류</span>
                                <select name="data[Reservation][room_types]">
                                    <option value="Twin">Twin</option>
                                    <option value="Queen">Queen</option>
                                </select>
                            </div>
                            <div class="kcbmc-exp">
                                <p>- Double Tree Hotel Philadelphia Downtown</p>
                                <p>&nbsp;&nbsp;Address: 237 S Broad St, Philadelphia, PA 19107</p>
                                <p>&nbsp;&nbsp;Phone: (215) 893-1600</p>
                                <p>&nbsp;&nbsp;호텔은 2인1실 기준이며. 개인 독방을 원하시면 $210 을 추가하셔야 합니다.</p>
                                <p>- 대회전/후의 추가객실예약은 대회가격 ($210/1박)의 혜택을 협회에서 드립니다.</p>
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
                                <a style="margin-left: 10px;" href="https://www.ihanatour.com/files/QueenEsther.pdf">
                                    [일정보기]
                                </a>
                                <div class="modal fade" id="tour_m1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">랭캐스터 성극, Queen Esther</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                            </div>

                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                Modal body..
                                            </div>

                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center">
                                <input id="tour2" style="margin: 0 10px" type="radio" name="data[Cbmc][tour]"
                                       value="2">
                                <span>
                                    미동부지역 4박5일 하이라이트 <strong>$680.00/pp</strong>
                                </span>
                                <a style="margin-left: 10px;"
                                   href="https://www.ihanatour.com/files/USEastern4nt5dyTour.pdf">
                                    [일정보기]
                                </a>
                                <div class="modal fade" id="tour_m2">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">미동부지역 4박5일 하이라이트</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                            </div>

                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                Modal body..
                                            </div>

                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kcbmc-exp">
                                <p>- 옵션투어는 날자가 겹치는 관계로 두가지중 하나만 선택합니다.</p>
                                <p>- 상기옵션투어는 어린이 할인이 적용되지 않습니다.</p>
                                <p>- 미동부투어 하이라이트 4박투어는 2인1실기준이며<br>
                                    &nbsp;&nbsp;전일정 식사/현지옵션/가이드팁이 포함되어있습니다. 또한 <span style="color: red;">여권지참필수</span>입니다.
                                </p>
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
                            <input id="card_num" type="text" maxlength="19" name="data[Cbmc][credit_num]"
                                   placeholder="Credit Card Number" required><br>
                            <input id="mmyy" type="text" maxlength="7" size="10" name="data[Cbmc][credit_exp]"
                                   placeholder="MM/YYYY" required>
                            <input type="text" maxlength="3" size="3" name="data[Cbmc][credit_cid]" placeholder="CID"
                                   required><br>
                            <input id="noc" type="text" name="data[Cbmc][credit_holder]"
                                   placeholder="Name of Card Holder" required><br>
                            <input type="text" name="data[Cbmc][credit_bill]" placeholder="Billing Address"
                                   required><br>
                            <input type="text" size="10" name="data[Cbmc][credit_city]" placeholder="City" required>
                            <select type="text" name="data[Cbmc][credit_state]" placeholder="State" required>
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
                            <input type="text" size="5" maxlength="5" name="data[Cbmc][credit_zip]" placeholder="Zip"
                                   required>
                        </div>
                    </div>
                    <div class="kcbmc-form-input-wrapper">
                        <p class="kcbmc-form-input-label">E-Signature and Authorization to register</p>
                        <div id="kcbmc-sig">
                            <input id="sig_name" type="text" name="data[Cbmc][sig_name]" placeholder="Names" required>
                            <input type="text" name="data[Cbmc][signature]" placeholder="Signature" required>
                            <input readonly="readonly" id="sig_date" name="data[Cbmc][sig_date]" type="text"
                                   placeholder="Date" required>
                        </div>
                    </div>
                    <div>
                        <p style="font-size: 11px;">
                            By Electronically signing above, I fully authorize the KCBMC to accept, register my
                            reservation, and charge my credit card in the amount $<span id="total2">0.00</span>. I am
                            also fully aware of cancellation policy provided by the KCBMC.
                        </p>
                    </div>
                </div>
                <div class="kcbmc-section-right">
                    <div>
                        <div class="kcbmc-summary">
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
                                        <p>&nbsp;&nbsp;$<span id="costa">550.00</span> x <span class="adults">1</span> =
                                            $<span
                                                    id="adult_cost">550.00</span> (Adults)</p>
                                        <p>&nbsp;&nbsp;$<span id="costc">450.00</span> x <span class="children">0</span>
                                            = $<span
                                                    id="child_cost">0.00</span> (Child)</p>
                                    </div>
                                    <div class="kcbmc-cost" id="sum_hotel">
                                        <p style="font-weight: 600">추가 투숙 비용</p>
                                        <p id="extra_night">&nbsp;&nbsp;$210.00 x <span class="night">0</span>박 x <span
                                                    class="rooms">1</span>방 = $<span id="hotel_cost">0.00</span>
                                        </p>
                                    </div>
                                    <div class="kcbmc-cost" id="sum_tour1" style="display:none">
                                        <p style="font-weight: 600">랭캐스터성극: 에스더여왕</p>
                                        <p>Departs 6/27/19 3:30pm</p>
                                        <p id="adult_tour1">&nbsp;&nbsp;$160.00 x <span class="adults">1</span> = $<span
                                                    id="tour1_costa">160.00</span> (Adults)</p>
                                        <p id="child_tour1">&nbsp;&nbsp;$160.00 x <span class="children">0</span> =
                                            $<span
                                                    id="tour1_costc">0</span> (Child)</p>
                                    </div>
                                    <div class="kcbmc-cost" id="sum_tour2" style="display:none;">
                                        <p style="font-weight: 600">미동부 하이라이트 4박5일</p>
                                        <p id="adult_tour2">&nbsp;&nbsp;$680.00 x <span class="adults">1</span> = $<span
                                                    id="tour2_costa">680.00</span> (Adults)</p>
                                        <p id="child_tour2">&nbsp;&nbsp;$680.00 x <span class="children">0</span> =
                                            $<span
                                                    id="tour2_costc">0.00</span> (Child)</p>
                                    </div>
                                    <div class="kcbmc-cost">
                                        <p style="font-weight: 600">크레딧 카드 수수료 (3.5%)</p>
                                        <p>&nbsp;&nbsp;Cost: $<span id="credit_fee">49.50</span></p>
                                    </div>
                                    <div id="kcbmc-price" class="kcbmc-cost">
                                        <p>Total Due Now</p>
                                        <p><span id="total">$0.00</span><br>(included Tax)</p>
                                    </div>
                                    <div id="kcbmc-notice">
                                        <div class="notice-title">
                                            Early Bird (~ 01/31/2020)
                                        </div>
                                        <div class="notice-contents">
                                            <div class="contents">
                                                <p>With Hotel</p>
                                                <dl>
                                                    <dd>Adult: $450.00</dd>
                                                    <dd>Child: $320.00</dd>
                                                </dl>
                                            </div>
                                            <div class="contents">
                                                <p>Without Hotel</p>
                                                <dl>
                                                    <dd>$310.00</dd>
                                                    <dd>$220.00</dd>
                                                </dl>
                                            </div>
                                        </div>
                                        <div class="notice-title">
                                            2nd Early Bird (~ 04/30/2020)
                                        </div>
                                        <div class="notice-contents">
                                            <div class="contents">
                                                <p>With Hotel</p>
                                                <dl>
                                                    <dd>Adult: $470.00</dd>
                                                    <dd>Child: $340.00</dd>
                                                </dl>
                                            </div>
                                            <div class="contents">
                                                <p>Without Hotel</p>
                                                <dl>
                                                    <dd>$330.00</dd>
                                                    <dd>$240.00</dd>
                                                </dl>
                                            </div>
                                        </div>
                                        <div class="notice-title">
                                            Regular
                                        </div>
                                        <div class="notice-contents">
                                            <div class="contents">
                                                <p>With Hotel</p>
                                                <dl>
                                                    <dd>Adult: $490.00</dd>
                                                    <dd>Child: $360.00</dd>
                                                </dl>
                                            </div>
                                            <div class="contents">
                                                <p>Without Hotel</p>
                                                <dl>
                                                    <dd>$350.00</dd>
                                                    <dd>$260.00</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                    <input value="500" type="hidden" id="total_price" name="data[CbmcCustomers][price]">
                                    <input class="kcbmc-form-submit" type="submit" value="예약하기">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kcbmc-footer">
                <p>안내사항</p>
                <ol class="kcbmc-footer-contents">
                    <li>
                        필라공항/호텔 간 셔틀서비스는 개별적으로 택시/기차/우버등을 이용하여주십시요.<br>
                        참고로 필라델피아시에서 지정한 공항/호텔간 택시비는 균일가로 $25.00/편도이며 공항에서
                        호텔까지는 약 15분 소요됩니다.
                    </li>
                    <li>
                        자세한 정보는 대회 웹사이트 <a href="http://www.2019kcbmc.com">www.2019kcbmc.com</a>에서 상세히 안내해드리고 있습니다
                    </li>
                    <li>
                        옵션투어안내<br>랭캐스터 성극은 사전 좌석지정이 안됩니다. 따라서 극장에 도착하여 좌석을 배분 받습니다.<br>
                        동부투어는 투어 종료 후 필라공항에 위치한 호텔에 투숙하시고 익일 조식을 마치신 후 자유체크아웃입니다.
                    </li>
                    <li>
                        추가 문의 사항이 있으시면 총연 사무국의 이메일 contact@kcbmc.net 또는 전화 703-439-1703 으로 문의 바랍니다.
                    </li>
                </ol>
            </div>
        </form>
    </div>
</div>