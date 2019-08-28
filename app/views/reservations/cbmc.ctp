<script src="https://ihanatour.com/js/jquery-3.4.1.min.js"></script>
<script src="https://ihanatour.com/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://ihanatour.com/css/bootstrap.min.css">
<style>
    .form {
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 40px;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .form-check-inline {
        padding-left: 15px;
    }
</style>
<script>
    $(document).ready(function () {

        $("#adults, #children").change(function () {
            var adults = Number($("#adults").val());
            var children = Number($("#children").val());
            var pax = adults + children;

            if ($("#tour_none").prop("checked")) {
                $("#total_price").val((adults * 500) + (children * 350));
            }

            $("#customer").html("");
            for (var i = 0; i < pax; i++) {
                $("#customer").append("<div class=\"row\">\n" +
                    "                    <div class=\"form-group col\">\n" +
                    "                        <label for=\"lastName\">Last Name</label>\n" +
                    "                        <input type=\"text\" class=\"form-control form-control-sm\" name=\"data[Reservation][lname"+i+"]\" id=\"lastName\" required>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col\">\n" +
                    "                        <label for=\"firstName\">First Name</label>\n" +
                    "                        <input type=\"text\" class=\"form-control form-control-sm\" name=\"data[Reservation][fname"+i+"]\" id=\"firstName\"\n" +
                    "                               required>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col-md-1\">\n" +
                    "                        <label for=\"gender\">Gender</label>\n" +
                    "                        <select type=\"text\" class=\"form-control form-control-sm\" name=\"data[Reservation][gender"+i+"]\" id=\"gender\" required>\n" +
                    "                            <option>M</option>\n" +
                    "                            <option>F</option>\n" +
                    "                        </select>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col-md\">\n" +
                    "                        <label for=\"DOB\">DOB</label>\n" +
                    "                        <input type=\"date\" class=\"form-control form-control-sm\" name=\"data[Reservation][dob"+i+"]\" id=\"dob\" required>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col\">\n" +
                    "                        <label for=\"tel\">Phone</label>\n" +
                    "                        <input type=\"tel\" class=\"form-control form-control-sm\" name=\"data[Reservation][phone_number"+i+"]\" id=\"tel\"\n" +
                    "                               placeholder=\"e.g. 000-000-0000\" required>\n" +
                    "                    </div>\n" +
                    "                    <div class=\"form-group col-md\">\n" +
                    "                        <label for=\"tel\">E-mail</label>\n" +
                    "                        <input type=\"email\" class=\"form-control form-control-sm\" name=\"data[Reservation][email"+i+"]\" id=\"email\"\n" +
                    "                               placeholder=\"e.g. email@email.com\"\n" +
                    "                               required>\n" +
                    "                    </div>\n" +
                    "                </div>");
            }
        });
    });
</script>
<div class="container">
    <div class="row justify-content-center">
        <form class="col-md-10 form" action="/reservations/cbmc_book" enctype="multipart/form-data" method="post"
              accept-charset="utf-8">
            <input type="hidden" name="data[Reservation][status]" value="New" id="ReservationStatus">
            <input type="hidden" name="data[Reservation][cat_id]" value="7" id="ReservationCatId">
            <input type="hidden" name="data[Reservation][wholesaler_id]" value="" id="ReservationWholesalerId">
            <input type="hidden" name="data[Item][tour_id]" value="974" id="ItemTourId">
            <input type="hidden" name="data[Item][tour_date]" value="2020-06-25" id="ItemTourDate">
            <input type="hidden" name="data[Reservation][tour_name]" value="[CBMS] Philadelphia"
                   id="ReservationTourName">
            <input type="hidden" name="data[Item][status]" value="대기" id="ItemStatus">
            <div class="booking-header">
                <h1>Make your reservation</h1>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="guests">Adults</label>
                        <select type="number" class="form-control form-control-sm" name="data[Reservation][adult_num]" id="adults" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="guests">Children</label>
                        <select type="number" class="form-control form-control-sm" name="data[Reservation][child_num]" id="children"
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
                <div class="col-md">
                    <div class="form-group">
                        <label for="bed">Bed type</label>
                        <select type="bed" class="form-control form-control-sm" name="data[Reservation][room]" id="bed" required>
                            <option value="Twin">Twin</option>
                            <option value="Queen">Queen</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="customer"> <!-- customer info -->
                <div class="row">
                    <div class="form-group col">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control form-control-sm" name="data[Reservation][lname0]" id="lastName" required>
                    </div>
                    <div class="form-group col">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control form-control-sm" name="data[Reservation][fname0]" id="firstName"
                               required>
                    </div>
                    <div class="form-group col-md-1">
                        <label for="gender">Gender</label>
                        <select type="text" class="form-control form-control-sm" name="data[Reservation][gender0]" id="gender" required>
                            <option>M</option>
                            <option>F</option>
                        </select>
                    </div>
                    <div class="form-group col-md">
                        <label for="dob">DOB</label>
                        <input type="date" class="form-control form-control-sm" name="data[Reservation][dob0]" id="dob" required>
                    </div>
                    <div class="form-group col">
                        <label for="tel">Phone</label>
                        <input type="tel" class="form-control form-control-sm" name="data[Reservation][phone_number0]" id="tel"
                               placeholder="e.g. 000-000-0000" required>
                    </div>
                    <div class="form-group col-md">
                        <label for="tel">E-mail</label>
                        <input type="email" class="form-control form-control-sm" name="data[Reservation][email0]" id="email"
                               placeholder="e.g. email@email.com"
                               required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="address">Address</label>
                    <input type="text" class="form-control form-control-sm" name="data[Reservation][address]" id="address" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="city">City</label>
                    <input type="text" class="form-control form-control-sm" name="data[Reservation][city]" id="city">
                </div>
                <div class="form-group col-md-3">
                    <label for="state">State</label>
                    <input type="text" class="form-control form-control-sm" name="data[Reservation][state]" id="state">
                </div>
                <div class="form-group col-md-3">
                    <label for="zip">Zip</label>
                    <input type="text" class="form-control form-control-sm" name="data[Reservation][zip]" id="zip">
                </div>
            </div>
            <div class="row">
                <div class="form-group form-check-inline">
                    <label class="form-check-label">
                        <input id="tour_none" type="radio" name="tour" class="form-check-input" checked="checked">None
                    </label>
                </div>
                <div class="form-group form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" name="tour" class="form-check-input">None
                    </label>
                </div>
                <div class="form-group form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" name="tour" class="form-check-input">None
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="ReservationTotal">Total price</label>
                    <input type="text" value="500" name="data[Reservation][total]" id="total_price" readonly>
                </div>
            </div>
            <button type="submit" class="btn btn-primary float-right">Submit</button>
        </form>
    </div>
</div>
