<style>
    body {
        font-family: 'Roboto', sans-serif;
        font-size: 12px;
        background-image: url('http://ihanatour.com/img/cbmc_back.jpg');
        background-position: center;
        background-attachment: fixed;
    }

    .form {
        border-radius: 10px;
        padding: 40px;
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        margin-bottom: 50px;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 30px;
        font-weight: bold;
    }

    .form-check-inline {
        padding-left: 15px;
    }

    .form-control-sm {
        font-size: 12px;
    }

    label {
        color: #ffb800;
        letter-spacing: 1px;
    }

    .form-control {
        border: none;
        background-color: #414147;
        color: #fff;
    }

    .form-control:focus {
        background-color: #414147;
        color: #fff;
        box-shadow: 0 0 2px 2px #ffb800;
    }

    .btn-cbmc {
        background-color: #ffb800;
        color: #000;
        font-weight: bold;
    }

    .btn-cbmc:hover {
        background-color: #946e00;
    }

    select {
        border-radius: 0; /* 아이폰 사파리 보더 없애기 */
        -webkit-appearance: none; /* 화살표 없애기 for chrome*/
        -moz-appearance: none; /* 화살표 없애기 for firefox*/
        appearance: none /* 화살표 없애기 공통*/
    }
</style>
<script>
    $(document).ready(function () {
        var adults = 0;
        var children = 0;
        var basicPrice = 500;
        var hotelPrice = 0;
        var tourPrice = 0;

        $("#hotel_yes, #hotel_no, #adults, #children").change(function () {
            adults = Number($("#adults").val());
            children = Number($("#children").val());
            var pax = adults + children;

            if ($("#hotel_yes").prop("checked")) {
                $("#hotel_info").css("display", "block");
                $("#hotel_wrapper").html("");
                for (var i = 0; i < pax; i++) {
                    $("#hotel_wrapper").append("<div class=\"form-group col-md-3\">\n" +
                        "                        <label id=\"label_lastName" + i + "\">" + $('#lastName' + i + '').val() + " " + $('#firstName' + i + '').val() + "</label>\n" +
                        "                        <select id= \"room" + i + "\" type=\"text\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][room" + i + "]\"\n" +
                        "                                required>\n" +
                        "                            <option value=\"1\">Room #1</option>\n" +
                        "                        </select>\n" +
                        "                    </div>");
                }
            } else {
                $("#hotel_info").css("display", "none");
            }

            $("#rooms").change(function () {
                for (var i = 0; i < pax; i++) {
                    $("#room" + i).html("");
                    for (var j = 0; j < $("#rooms").val(); j++) {
                        $("#room" + i).append("<option value=" + (j+1) + ">Room #" + (j+1) + "</option>");
                    }
                }
            });
        });

        $("#tour_yes, #tour_no, #adults, #children").change(function () {
            adults = Number($("#adults").val());
            children = Number($("#children").val());
            var pax = adults + children;

            if ($("#tour_yes").prop("checked")) {
                $("#tour_info").css("display", "block");
                $("#tour_wrapper").html("");
                for (var i = 0; i < pax; i++) {
                    $("#tour_wrapper").append("<div class=\"form-group col-md-3\">\n" +
                        "                        <label>" + $('#lastName' + i + '').val() + " " + $('#firstName' + i + '').val() + "</label>\n" +
                        "                        <select type=\"text\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][tour" + i + "]\"\n" +
                        "                                required>\n" +
                        "                            <option value=\"0\">None</option>\n" +
                        "                            <option value=\"1\">Tour 1</option>\n" +
                        "                            <option value=\"2\">Tour 2</option>\n" +
                        "                            <option value=\"3\">Tour 3</option>\n" +
                        "                        </select>\n" +
                        "                    </div>")
                }
            } else {
                $("#tour_info").css("display", "none");
                tourPrice = 0;
                $("#total_price").val(basicPrice + hotelPrice + tourPrice);
            }

            $("#tour_wrapper select").each(function () {
                $(this).change(function () {
                    tourPrice = 0;
                    for (var i = 0; i < pax; i++) {
                        var tourNum = $("#tour_wrapper select").eq(i).val();

                        switch (tourNum) {
                            case '1':
                                tourPrice += 100;
                                break;
                            case '2':
                                tourPrice += 200;
                                break;
                            case '3':
                                tourPrice += 300;
                                break;
                            default:
                                tourPrice += 0;
                        }
                    }
                    $("#total_price").val(basicPrice + hotelPrice + tourPrice);
                });
            });
        });

        $(".datepicker").each(function (index) {
            $(this).datepicker();
            $(this).datepicker("option", "dateFormat", "yy-mm-dd");
            if (index == 0)
                $(this).val("2020-06-25");
            else
                $(this).val("2020-06-27");
        });

        $("#adults, #children").change(function () {
            adults = Number($("#adults").val());
            children = Number($("#children").val());
            var pax = adults + children;

            basicPrice = 0;
            for (var i = 0; i < adults; i++) {
                basicPrice += 500;
            }

            for (var i = 0; i < children; i++) {
                basicPrice += 350;
            }

            $("#total_price").val(basicPrice + hotelPrice + tourPrice);

            $("#customer").html("");
            var i;
            for (i = 0; i < adults; i++) {
                $("#customer").append("<div class=\"row\">\n" +
                    "                    <div class=\"form-group col\">\n" +
                    "                        <label for=\"lastName\">Last Name</label>\n" +
                    "                        <input type=\"text\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][lname" + i + "]\"\n" +
                    "                               id=\"lastName" + i + "\" required>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col\">\n" +
                    "                        <label for=\"firstName\">First Name</label>\n" +
                    "                        <input type=\"text\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][fname" + i + "]\"\n" +
                    "                               id=\"firstName" + i + "\"\n" +
                    "                               required>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col-md-2\">\n" +
                    "                        <label for=\"gender\">Gender</label>\n" +
                    "                        <select type=\"text\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][gender" + i + "]\"\n" +
                    "                                id=\"gender" + i + "\" required>\n" +
                    "                            <option value=\"M\">M</option>\n" +
                    "                            <option value=\"F\">F</option>\n" +
                    "                        </select>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col-md\">\n" +
                    "                        <label for=\"dob\">DOB</label>\n" +
                    "                        <input type=\"date\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][dob" + i + "]\"\n" +
                    "                               id=\"dob" + i + "\" required>\n" +
                    "                    </div>\n" +
                    "                    <input type=\"hidden\" value=\"0\" name=\"data[CbmcCustomers][is_child" + i + "]\">\n" +
                    "                </div>");
            }

            for (; i < pax; i++) {
                $("#customer").append("<div class=\"row\">\n" +
                    "                    <div class=\"form-group col\">\n" +
                    "                        <label for=\"lastName\">(C)Last Name</label>\n" +
                    "                        <input type=\"text\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][lname" + i + "]\"\n" +
                    "                               id=\"lastName" + i + "\" required>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col\">\n" +
                    "                        <label for=\"firstName\">First Name</label>\n" +
                    "                        <input type=\"text\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][fname" + i + "]\"\n" +
                    "                               id=\"firstName" + i + "\"\n" +
                    "                               required>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col-md-2\">\n" +
                    "                        <label for=\"gender\">Gender</label>\n" +
                    "                        <select type=\"text\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][gender" + i + "]\"\n" +
                    "                                id=\"gender" + i + "\" required>\n" +
                    "                            <option value=\"M\">M</option>\n" +
                    "                            <option value=\"F\">F</option>\n" +
                    "                        </select>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col-md\">\n" +
                    "                        <label for=\"dob\">DOB</label>\n" +
                    "                        <input type=\"date\" class=\"form-control form-control-sm\" name=\"data[CbmcCustomers][dob" + i + "]\"\n" +
                    "                               id=\"dob" + i + "\" required>\n" +
                    "                    </div>\n" +
                    "                    <input type=\"hidden\" value=\"1\" name=\"data[CbmcCustomers][is_child" + i + "]\">\n" +
                    "                </div>");
            }
        });
    });

    function dateDiff(_date1, _date2) {
        var diffDate_1 = _date1 instanceof Date ? _date1 : new Date(_date1);
        var diffDate_2 = _date2 instanceof Date ? _date2 : new Date(_date2);

        diffDate_1 = new Date(diffDate_1.getFullYear(), diffDate_1.getMonth() + 1, diffDate_1.getDate());
        diffDate_2 = new Date(diffDate_2.getFullYear(), diffDate_2.getMonth() + 1, diffDate_2.getDate());

        var diff = Math.abs(diffDate_2.getTime() - diffDate_1.getTime());
        diff = Math.ceil(diff / (1000 * 3600 * 24));

        return diff;
    }

    var a = '2016-01-01';

    console.log('a는 오늘로 부터 ' + dateDiff(a, new Date()) + '일 전입니다.');
</script>
<div class="container" style="height: 100vh;">
    <div class="row justify-content-center" style="position: relative; top: 50px;">
        <form class="col-md-10 form" action="/reservations/cbmc_book" enctype="multipart/form-data" method="post"
              accept-charset="utf-8">
            <div class="booking-header">
                <h1>CBMC / Make your reservation</h1>
            </div>
            <input type="hidden" name="data[Reservation][status]" value="New" id="ReservationStatus">
            <input type="hidden" name="data[Reservation][cat_id]" value="7" id="ReservationCatId">
            <input type="hidden" name="data[Item][tour_id]" value="974" id="ItemTourId">
            <input type="hidden" name="data[Reservation][tour_name]" value="[CBMS] Philadelphia"
                   id="ReservationTourName">
            <input type="hidden" name="data[Item][status]" value="대기" id="ItemStatus">
            <input type="hidden" name="data[Item][tour_date]" value="2020-06-25">
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <label for="guests">Adults</label>
                        <select type="number" class="form-control form-control-sm" name="data[CbmcReservations][adults]"
                                id="adults" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label for="guests">Children</label>
                        <select type="number" class="form-control form-control-sm"
                                name="data[CbmcReservations][children]" id="children"
                                required>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="test" value="test" id="ReservationTotal">
            </div>
            <!-- customer information -->
            <div id="customer">
                <div class="row">
                    <div class="form-group col-2">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control form-control-sm" name="data[CbmcCustomers][lname0]"
                               id="lastName0" required>
                    </div>
                    <div class="form-group col-3" style="width: 20%;">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control form-control-sm" name="data[CbmcCustomers][fname0]"
                               id="firstName0"
                               required>
                    </div>
                    <div class="form-group col-1">
                        <label for="gender">Gender</label>
                        <select type="text" class="form-control form-control-sm" name="data[CbmcCustomers][gender0]"
                                id="gender0" required>
                            <option value="M">M</option>
                            <option value="F">F</option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="dob">DOB</label>
                        <input type="date" class="form-control form-control-sm" name="data[CbmcCustomers][dob0]"
                               id="dob0" required>
                    </div>
                    <input type="hidden" value="0" name="data[CbmcCustomers][is_child0]">
                    <div class="form-group col">
                        <label for="tel">Phone</label>
                        <input type="tel" class="form-control form-control-sm" name="data[CbmcCustomers][phone]"
                               id="tel"
                               placeholder="e.g. 000-000-0000" required>
                    </div>
                    <div class="form-group col">
                        <label for="tel">E-mail</label>
                        <input type="email" class="form-control form-control-sm" name="data[CbmcCustomers][email]"
                               id="email"
                               placeholder="e.g. email@email.com"
                               required>
                    </div>
                </div>
            </div>
            <!--<div class="row">
                <div class="form-group col">
                    <label for="tel">Phone</label>
                    <input type="tel" class="form-control form-control-sm" name="data[CbmcCustomers][phone]"
                           id="tel"
                           placeholder="e.g. 000-000-0000" required>
                </div>
                <div class="form-group col-md">
                    <label for="tel">E-mail</label>
                    <input type="email" class="form-control form-control-sm" name="data[CbmcCustomers][email]"
                           id="email"
                           placeholder="e.g. email@email.com"
                           required>
                </div>
            </div>-->
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="address">Address</label>
                    <input type="text" class="form-control form-control-sm" name="data[CbmcCustomers][address]"
                           id="address" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="city">City</label>
                    <input type="text" class="form-control form-control-sm" name="data[CbmcCustomers][city]" id="city">
                </div>
                <div class="form-group col-md-1">
                    <label for="state">State</label>
                    <select name="data[CbmcCustomers][state]" class="form-control form-control-sm" id="state">
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
                </div>
                <div class="form-group col-md-2">
                    <label for="zip">Zip</label>
                    <input type="text" maxlength="5" class="form-control form-control-sm"
                           name="data[CbmcCustomers][zip]" id="zip">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    Hotel Require?
                    <div class="form-group form-check-inline">
                        <label class="form-check-label">
                            <input id="hotel_yes" class="form-check-input" type="radio" name="data[CbmcCustomers][need_hotel]" value="1">Yes
                        </label>
                    </div>
                    <div class="form-group form-check-inline">
                        <label class="form-check-label">
                            <input id="hotel_no" class="form-check-input" type="radio" name="data[CbmcCustomers][need_hotel]" value="0"
                                   checked="checked">No
                        </label>
                    </div>
                </div>
            </div>
            <div id="hotel_info" style="display: none;">
                <div style="height: 1px; background-color: #855d00;"></div>
                <div class="row" style="margin-top: 1rem;">
                    <div class="form-group col-md-3">
                        <label for="lastName">Check in</label>
                        <input type="text" class="form-control form-control-sm datepicker"
                               name="data[CbmcCustomers][check-in]" placeholder="yyyy-mm-dd"
                               required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="lastName">Check out</label>
                        <input type="text" class="form-control form-control-sm datepicker"
                               name="data[CbmcCustomers][check-out]" placeholder="yyyy-mm-dd"
                               required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="gender">Rooms</label>
                        <select id="rooms" type="text" class="form-control form-control-sm" name="data[CbmcCustomers][rooms]"
                                required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                </div>
                <div id="hotel_wrapper" class="row">
                    <div class="form-group col-md-3">
                        <label>Name</label>
                        <select type="text" class="form-control form-control-sm" name="data[CbmcCustomers][room1]"
                                required>
                            <option value="1">Room 1</option>
                            <option value="2">Room 2</option>
                            <option value="3">Room 3</option>
                            <option value="4">Room 4</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    Tour Require?
                    <div class="form-group form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" id="tour_yes" type="radio" name="data[CbmcCustomers][need_tour]" value="1">Yes
                        </label>
                    </div>
                    <div class="form-group form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" id="tour_no" type="radio" name="data[CbmcCustomers][need_tour]" value="0"
                                   checked="checked">No
                        </label>
                    </div>
                </div>
            </div>
            <div id="tour_info" style="display: none;">
                <div style="height: 1px; background-color: #855d00;"></div>
                <div id="tour_wrapper" class="row" style="margin-top: 1rem;">
                    <div class="form-group col-md-3">
                        <label>Name 1</label>
                        <select type="text" class="form-control form-control-sm" name="data[CbmcCustomers][tour1]"
                                required>
                            <option value="0">None</option>
                            <option value="1">Tour 1</option>
                            <option value="2">Tour 2</option>
                            <option value="3">Tour 3</option>
                        </select>
                    </div>
                </div>
            </div>
            <!--<div class="row">
                <div class="col-3">
                    <div class="row justify-content-center">
                        <label for="gender">Room 1</label>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="gender">Adults</label>
                            <select type="text" class="form-control form-control-sm" name="data[CbmcCustomers][rooms]"
                                    id="gender" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group col">
                            <label for="gender">Children</label>
                            <select type="text" class="form-control form-control-sm" name="data[CbmcCustomers][rooms]"
                                    id="gender" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>-->
            <!--
            <div class="row" id="cbmc_tours">
                <div class="form-group form-check-inline">
                    <label class="form-check-label">
                        <input id="tour0" value="none" type="radio" name="data[CbmcCustomers][tour0]"
                               class="form-check-input" checked="checked">None
                    </label>
                </div>
                <div class="form-group form-check-inline">
                    <label class="form-check-label">
                        <input id="tour1" type="radio" value="tour1" name="data[CbmcCustomers][tour0]" class="form-check-input">tour1
                    </label>
                </div>
                <div class="form-group form-check-inline">
                    <label class="form-check-label">
                        <input id="tour2" type="radio" value="tour2" name="data[CbmcCustomers][tour0]" class="form-check-input">tour2
                    </label>
                </div>
            </div>
            -->
            <div class="row">
                <div class="form-group form-group-inline col-6">
                    <label for="ReservationTotal">Total price:</label>
                    <input value="500" readonly="readonly" id="total_price" class=""
                           style="color: #fff; display: inline-block; background-color: inherit; border: none;"
                           name="data[CbmcCustomers][price]">
                </div>
            </div>
            <button type="submit" class="btn btn-cbmc float-right">Submit</button>
        </form>
    </div>
</div>
