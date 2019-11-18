<script src="//cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
<style>
    body {
        background-color: #ddd;
        padding: 20px;
    }

    #topPanel {
        position: fixed;
        top: 0px;
        width: 100%;
        display: flex;
        background-color: #333;
        color: #fff;
        left: 0px;
        right: 0px;
        padding-left: 20px;
        align-items: center;
        height: 45px;
        z-index: 10;
    }

    .drop {
        margin-top: 40px;
        border: 1px solid #aaa;
        background-color: #fff;
        padding: 10px;
    }

    #div_submit {
        display: flex;
        justify-content: flex-end;
    }

    #div_submit > button {
        background-color: #333;
    }
</style>
<div class="drop">
    <div id="topPanel">
        <img src="https://ihanatour.com/img/logo_white_ver2.png" style="height: 30px;">
    </div>
    <div class="load">
        <form action="/settings/cbmc_notice_edit" enctype="multipart/form-data" method="post" accept-charset="utf-8">
            <div class="row" style="margin: 0; justify-content: space-around">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="hotel_text">호텔 안내</label>
                        <textarea name="data[CbmcText][hotel_text]" class="form-control" rows="5" id="hotel_text">
                    <?= $text['CbmcText']['hotel_text']; ?>
                </textarea>
                        <script>
                            CKEDITOR.replace('hotel_text');
                        </script>
                    </div>
                    <div class="form-group">
                        <label for="tour_text">여행 안내</label>
                        <textarea name="data[CbmcText][tour_text]" class="form-control" rows="5" id="tour_text">
                    <?= $text['CbmcText']['tour_text']; ?>
                </textarea>
                        <script>
                            CKEDITOR.replace('tour_text');
                        </script>
                    </div>
                    <div class="form-group">
                        <label for="form_text">E-Sign 안내</label>
                        <textarea name="data[CbmcText][form_text]" class="form-control" rows="5" id="form_text">
                    <?= $text['CbmcText']['form_text']; ?>
                </textarea>
                        <script>
                            CKEDITOR.replace('form_text');
                        </script>
                    </div>
                    <div class="form-group">
                        <label for="notice_text">하단 안내</label>
                        <textarea name="data[CbmcText][notice_text]" class="form-control" rows="5" id="notice_text">
                    <?= $text['CbmcText']['notice_text']; ?>
                </textarea>
                        <script>
                            CKEDITOR.replace('notice_text');
                        </script>
                    </div>
                    <div id="div_submit">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </div>
                <div style="width: 2px; background-color: #ddd;"></div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="hotel_text_en">Hotel notice</label>
                        <textarea name="data[CbmcText][hotel_text_en]" class="form-control" rows="5" id="hotel_text_en">
                    <?= $text['CbmcText']['hotel_text_en']; ?>
                </textarea>
                        <script>
                            CKEDITOR.replace('hotel_text_en');
                        </script>
                    </div>
                    <div class="form-group">
                        <label for="tour_text_en">Tour notice</label>
                        <textarea name="data[CbmcText][tour_text_en]" class="form-control" rows="5" id="tour_text_en">
                    <?= $text['CbmcText']['tour_text_en']; ?>
                </textarea>
                        <script>
                            CKEDITOR.replace('tour_text_en');
                        </script>
                    </div>
                    <div class="form-group">
                        <label for="form_text_en">E-Sign notice</label>
                        <textarea name="data[CbmcText][form_text_en]" class="form-control" rows="5" id="form_text_en">
                    <?= $text['CbmcText']['form_text_en']; ?>
                </textarea>
                        <script>
                            CKEDITOR.replace('form_text_en');
                        </script>
                    </div>
                    <div class="form-group">
                        <label for="notice_text_en">Bottom notice</label>
                        <textarea name="data[CbmcText][notice_text_en]" class="form-control" rows="5" id="notice_text_en">
                    <?= $text['CbmcText']['notice_text_en']; ?>
                </textarea>
                        <script>
                            CKEDITOR.replace('notice_text_en');
                        </script>
                    </div>
                    <div id="div_submit">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>