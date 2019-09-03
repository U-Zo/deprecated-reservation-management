<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<style type="text/css" >
    body { background: none; }
    th, td { font-family: "Arial Narrow", Arial, sans-serif; color: #000; vertical-align: top; text-align: left; padding: 0;}
    /* .extraInfo {display: none;} */
    .manageBtn, .btn {float:left; margin-right:1px; cursor: pointer;}
    @media print {
        .noPrint { display: none; }
        .comments th, #expenses th { border-bottom: 1px solid #000;}
        .comments td, #expenses td { border-bottom: 1px solid #ccc;}
        td { font-size: 10px; }
        th { font-size: 11px; }
        h4 { font-size: 14px; margin: -15px 0px 0px 0px; font-family: malgun Gothic;}

    }

    @media screen {
        body {padding: 20px;}
        #topPanel {position: fixed; top: 0px; width: 100%; background: #fff;}
        #load {margin-top: 40px;}

        .comments, #expenses, .fileupload { margin: 20px 0; border: none; }
        .comments {margin-right: 30px;}
        .expensesDivider { border-top: 1px solid #c0c0c0; }
    }

    #View_area {
        position:relative;
        width: 100px;
        height: 100px;
        color: black;
        border: 0px solid black;
        dispaly: inline;
    }
    .hide {
        display: none;
    }
</style>
<div class="drop">
    <div class="print">
        <hr>
        <div class="letterhead" >
            <div class="printLogo"><?php e($this->Html->image('/img/logo.jpg')); ?></div>
            <div class="txt">7827 Old York Road Suite 101, Elkins Park, PA 19027, TEL: 215-935-2500
            </div>
        </div>
        <span style="display: block; text-align: right; margin-top:-15px;">www.ihanatour.com</b></span>
    </div>
    <div id="topPanel">
        <?php
        echo '<h4>'. $itemInfo['Tour']['title'] . ' (' . $itemInfo['Item']['tour_date'] .')</h4>';
        echo '<a href="'.$this->webroot.'settings/reservations_calendar" class="btn blue">Dash board</a> ';
        echo '<a class="btn blue" onClick="location.reload();">Back to list</a> ';
        echo '<a href="'.$this->webroot.'settings/tour_book/'.$items[0]['Item']['tour_id'].'" class="btn blue addBtn" >Add reservation</a> ';
        echo '<a class="btn blue detailBtn">Hide detail</a> ';
        echo '<a class="btn blue commentBtn">Hide comments</a> ';
        echo '<a href="'. $this->webroot .'settings/tour_application/'. urldecode($itemInfo['Tour']['id']) .'/'. $itemInfo['Item']['tour_date'] .'" class="btn blue" target="_blank">Blank sign up Sheet</a> ';
        echo '<a href="'. $this->webroot .'settings/pax_list_order/'.$itemInfo['Item']['id'].'" class="btn blue">Roommates</a> ';
        echo '<a class="btn blue more-info">Show more info</a>';
        echo '<a href="https://ihanatour.com/tours/view/'.$itemInfo['Item']['tour_id'].'" class="btn blue">Goto Tour</a>';
        ?>
    </div>
    <div id="load">
        <br>
        <div class="noPrint"><span class="New">New</span>, <span class="Pending">Pending</span>, <span class="Confirmed">Confirmed</span>, <span class="Paid">Paid</span></div>
        <table style="width: 100%;" cellspacing="0">
            <tr>
                <th style="border-bottom: 2px solid #000; width:10px;">no</th>
                <th style="border-bottom: 2px solid #000; width:40px;">Last</th>
                <th style="border-bottom: 2px solid #000; width:60px;">First</th>
                <th style="border-bottom: 2px solid #000; width:55px;">DOB</th>
                <th style="border-bottom: 2px solid #000; width:20px;" class="hide gender">Gender</th>
                <th style="border-bottom: 2px solid #000; width:20px;">Nation</th>
                <th style="border-bottom: 2px solid #000; width:60px;">Passport</th>
                <th style="border-bottom: 2px solid #000; width:55px;">Exp</th>
                <th style="border-bottom: 2px solid #000; width:100px;">Address</th>
                <th style="border-bottom: 2px solid #000; width:60px;">City</th>
                <th style="border-bottom: 2px solid #000; width:20px;">ST</th>
                <th style="border-bottom: 2px solid #000; width:40px;">Zip</th>
                <th style="border-bottom: 2px solid #000; width:70px;">Home</th>
                <th style="border-bottom: 2px solid #000; width:70px;">Cell</th>
                <th style="border-bottom: 2px solid #000; width:70px;">Email</th>
                <th style="border-bottom: 2px solid #000; width:80px;" class="extraInfo">Payment</th>
                <th style="border-bottom: 2px solid #000; width:50px;" class="extraInfo">Balance</th>
                <th style="border-bottom: 2px solid #000; " class="extraInfo">Remarks</th>
                <th style="border-bottom: 2px solid #000; " class="extraInfo">Invoice Remarks</th>
                <th style="border-bottom: 2px solid #000; width: 110px;" class="noPrint extraInfo">Manage</th>
            </tr>
            <?php
            /* 	PER RESERVATION / ROOM */
            $no = 1;
            foreach($items as $item) {
                /* 		DEFINE PAYMENTS */
                $balance = $item['Reservation']['total'];
                $paymentTxt = '';
                foreach($item['Payment'] as $payment) :
                    $paymentTxt .= '<span title="'. $payment['remark'] . ' '. $payment['created'] . '">' . $this->Number->currency($payment['amount']) . ' ('. $payment['type'] .')</span><br>';
                    $balance = $balance - $payment['amount'];
                    $totalReceipts += $payment['amount'];
                endforeach;

                $paxNum = sizeof($item['Profile']) ;
                for ($i=0; $i < $paxNum; $i++) {
                    /* 				GROUP DIVIDER */
                    if($i == $paxNum-1) {
                        $border = 'border-bottom: 1px solid #000;';
                    } else {
                        $border = 'border-bottom: 1px solid #eee;';
                    }

                    if($item['Reservation']['status']!='Canceled'){
                        echo '<tr style="border-bottom: 1px solid #000;">';
                    }
                    else {
                        echo '<tr style="border-bottom: 1px solid #000;" class="hide">';
                    }
                    if($item['Reservation']['status']!='Canceled'){
                        echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $no .'</td>';
                    }
                    else {
                        echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'"></td>';
                    }
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['lname'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['fname'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['dob'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .' hide gender">'. $item['Profile'][$i]['gender'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['nation'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['passport'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['exp'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['home_address1'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['home_city'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['home_state'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['home_zip'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['home_phone'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['cell_phone'] .'</td>';
                    echo '<td style="'. $border .' " class="'. $item['Reservation']['status'] .'">'. $item['Profile'][$i]['home_email'].'</td>';
                    if($i == 0) {
                        echo '<td class="extraInfo" rowspan="'. $paxNum .'" style="border-bottom: 1px solid #000;">'.$paymentTxt.'</td>';
                        echo '<td class="extraInfo" rowspan="'. $paxNum .'" style="border-bottom: 1px solid #000;"><b>'. $this->Number->currency($balance) .'</b></td>';
                        echo '<td class="extraInfo" rowspan="'. $paxNum .'" style="border-bottom: 1px solid #000; ">'. nl2br( $item['Reservation']['remark'] ) .'</td>';
                        echo '<td class="extraInfo" rowspan="'. $paxNum .'" style="border-bottom: 1px solid #000; ">'. nl2br( $item['Reservation']['remark_invoice'] ) .'</td>';
                        echo '<td rowspan="'. $paxNum .'" style="border-bottom: 1px solid #000; " class="noPrint extraInfo">';
                        echo '<a href="'.$this->webroot.'settings/voucher/'.$item['Reservation']['id'].'" class="btn blue manageBtn">C</a>';
                        echo '<a href="'.$this->webroot.'settings/reservations_edit/'.$item['Reservation']['id'].'" class="btn blue manageBtn">M</a>';
                        echo '<a href="'.$this->webroot.'settings/reservations_view/'.$item['Reservation']['id'].'" class="btn blue manageBtn">V</a>';
                        echo '<a href="'.$this->webroot.'settings/tour_application/'.$item['Item']['tour_id'].'/'. $item['Item']['tour_date'] .'/'.$item['Reservation']['id'].'" class="btn blue" target="_blank">S</a>';
                        echo $this->Form->create('Setting', array('action'=>'changeStatus'));
                        echo $this->Form->input('Reservation.id', array(
                            'value' => $item['Reservation']['id']
                        ));
                        /*
                        echo $this->Form->input('Reservation.status', array(
                        'type' => 'hidden',
                        'value' => 'Canceled'
                        ));
                        echo $this->Form->end(array(
                        'label' => 'X',
                        'class' => 'btn orange cancelBtn',
                        'div' => false,
                        'style' => 'float:left; border: 1px solid #a52706; font-size:8px;'
                        ));
                        */
                        e($this->Form->input('Reservation.status', array(
                            'type'=>'select',
                            'label'=>false,
                            'div' => false,
                            'class' => 'statusSelect',
                            'value' => $item['Reservation']['status'],
                            'options'=> array(
                                'New'=>'New',
                                'Pending'=>'Pending',
                                'Confirmed'=>'Confirmed',
                                'Paid'=>'Paid',
                                'Canceled'=>'Canceled'
                            )
                        )));
                        echo $this->Form->end();
                        echo '</td>';
                    }
                    echo '</tr>';
                    if($item['Reservation']['status']!='Canceled'){
                        $no++;
                    }
                }

            }
            echo '</table>';
            echo '<br>';
            // echo $this->Form->create('Setting', array('id'=>'ItemForm', 'action' => 'pax_list/' . $items[0]['Item']['id']));
            // echo $this->Form->input('Item.id', array('type'=>'hidden', 'value' => $items[0]['Item']['id']));
            // echo $this->Form->input('Item.deposit', array(
            // 	'label' => false,
            // 	'div' => false,
            // 	'before' => 'Deposit ',
            // 	'value' => $itemInfo['Item']['deposit']
            // ));
            // echo $this->Form->input('Item.deposit_date', array(
            // 	'type' => 'text',
            // 	'label' => false,
            // 	'div' => false,
            // 	'before' => '&nbsp;&nbsp;&nbsp;&nbsp; Deposit by ',
            // 	'value' => $itemInfo['Item']['deposit_date']
            // ));
            // echo $this->Form->input('Item.payment_date', array(
            // 	'type' => 'text',
            // 	'label' => false,
            // 	'div' => false,
            // 	'before' => '&nbsp;&nbsp;&nbsp;&nbsp; Full payment by ',
            // 	'value' => $itemInfo['Item']['payment_date']
            // ));
            // echo $this->Form->end(array(
            // 	'label'=>'Update',
            // 	'div' => false
            // ));
            ?>
            <table>
                <tr>
                    <td style="width: 50%">
                        <div class="comments">
                            <h6>Comments</h6>
                            <?php
                            e('<table >');
                            echo '<tr>					 ';
                            echo '	<th>Name</th>		 ';
                            echo '	<th>Comment</th>';
                            echo '	<th>Action</th>			 ';
                            echo '</tr>					 ';
                            foreach ($comments as $comment):
                                // Delete button for admin user
                                if ($Auth['User']['group_id'] <= 2) {
                                    $dText = '<a href="' . $this->webroot . 'comments/delete/' . $comment['Comment']['id'] . '" class="del_comment" onclick="return confirm(&#039;Are you sure?&#039;);">Delete</a> | <a href="' . $this->webroot . 'boards/comment_edit/' . $comment['Comment']['id'] . '" class="editComment">Edit Comment</a>';
                                } else {
                                    $dText = '<a href="' . $this->webroot . 'boards/comment_delete/' . $comment['Comment']['id'] . '" class="del_comment" onclick="return confirm(&#039;Are you sure?&#039;);">Delete</a>';
                                }
                                $cName = $comment['User']['name'];

                                e($this->Html->tableCells(
                                    array(
                                        array(
                                            array('<b>' . $cName . '</b><br /><span class="timeStamp">' . $comment['Comment']['created'] . '</span>', array('style' => 'width: 150px;')),
                                            array(nl2br($comment['Comment']['comment']), array('style' => 'font-family: Malgun Gothic, Lucida Grande, Lucida Sans, Arial, sans-serif;')),
                                            array($dText, array('style' => 'width: 100px;'))
                                        )
                                    ),
                                    array('class' => 'grayBG')
                                )
                                );
                            endforeach;
                            e('</table>');
                            // Comment Form
                            e($this->Form->create('Board', array('action'=>'comment_write')));
                            e($this->Form->input('Comment.item_id', array(
                                'value' => $items[0]['Item']['id'],
                                'type' => 'hidden'
                            )));
                            if(!$Auth) {
                                e('Ã¬ï¿½Â´Ã«Â¦â€ž <input type="text" name="data[Comment][name]" class="validate[required]" id="CommentName" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ã­Å’Â¨Ã¬Å Â¤Ã¬â€ºÅ’Ã«â€œÅ“ <input type="password" name="data[Comment][password]" class="validate[required]" id="CommentPassword" >');
                            }
                            e($this->Form->input('Comment.comment', array(
                                'type'=>'textarea',
                                'label' => false,
                                'style' => 'width: 98%; height:80px; Font-Family: Malgun Gothic, Lucida Grande, Lucida Sans, Arial, sans-serif',
                                'class' => 'validate[required]',
                                'escape' => false
                            )));
                            e($this->Form->end('Post comment'));
                            ?>
                            <script>
                                $('td').contents().filter(function(){
                                    return this.nodeType == 3 // Text node
                                }).each(function(){
                                    this.data = this.data.replace(/ /g, '\u00a0');
                                });
                            </script>
                        </div>
                        <div id="editComment" ></div>
                    </td>
                    <td>
                        <div id="expenses" class="extraInfo">
                            <h6>Expenses</h6>
                            <table>
                                <tr>
                                    <th class="noPrint">Delete</th>
                                    <th>Descriptions</th>
                                    <th>Qt</th>
                                    <th style="text-align: right;">Per Unit</th>
                                    <th style="text-align: right;">Sub Total</th>
                                </tr>
                                <?php
                                foreach ($itemInfoExpenses as $expense):
                                    e($this->Html->tableCells(
                                        array(
                                            array(
                                                array( '<a href="'. $this->webroot .'settings/expense_delete/'.$expense['Expense']['id'].'" class="btn orange">x</a>', array('class' => 'noPrint') ),
                                                $expense['Expense']['description'],
                                                $expense['Expense']['quantaty'],
                                                array( $this->Number->currency($expense['Expense']['price']), array('style' => 'text-align: right;') ),
                                                array( $this->Number->currency($expense['Expense']['quantaty'] * $expense['Expense']['price']), array('style' => 'text-align: right;') )
                                            )
                                        ),
                                        array('class' => 'grayBG')
                                    )
                                    );
                                    $totalExpenses += $expense['Expense']['quantaty'] * $expense['Expense']['price'];
                                endforeach;
                                ?>
                                <tr class="expensesDivider">
                                    <td class="noPrint"></td>
                                    <td>Total Expenses</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;"><?= $this->Number->currency($totalExpenses)?></td>
                                </tr>
                                <tr>
                                    <td class="noPrint"></td>
                                    <td>Total Gross Receipts</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;"><?= $this->Number->currency($totalReceipts) ?></td>
                                </tr>
                                <tr>
                                    <td class="noPrint"></td>
                                    <td>Operational Margin</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;"><?= $this->Number->currency($totalReceipts - $totalExpenses) ?></td>
                                </tr>
                            </table>
                            <?php
                            echo $this->Form->create('Setting', array('id'=>'ExpenseForm', 'action' => 'pax_list/' . $items[0]['Item']['id']));
                            echo $this->Form->input('Expense.item_id', array('type'=>'hidden', 'value' => $items[0]['Item']['id']));
                            echo $this->Form->input('Expense.description', array(
                                'label' => false,
                                'div' => false,
                                'before' => 'Description ',
                                'class' => 'validate[required]'
                            ));
                            echo $this->Form->input('Expense.quantaty', array(
                                'label' => false,
                                'div' => false,
                                'before' => '&nbsp;&nbsp;&nbsp;&nbsp; Qt ',
                                'class'=>'validate[required]',
                                'style' => 'width: 30px;'
                            ));
                            echo $this->Form->input('Expense.price', array(
                                'label' => false,
                                'div' => false,
                                'before' => '&nbsp;&nbsp;&nbsp;&nbsp; Price per unit ',
                                'class'=>'validate[required]'
                            ));
                            echo $this->Form->end(array(
                                'label' => 'Add',
                                'div' => false
                            ));
                            ?>
                            <a href="javascript:void(0)" onclick="window.open('https://ihanatour.com/settings/expense_order/<?= $items[0]['Item']['id'] ?>','_blank','width=630px,height=333px');" class="btn blue" style="margin-top: -20px; margin-right: 2px; float: right;">Order</a>
                        </div>

                        <div class="fileupload noPrint extraInfo">
                            <h6>File upload</h6>

                            <table>
                                <tr>
                                    <th style="width:15px">No</th><th>Description</th><th>File name</th><th>Last uploaded</th><th></th>
                                </tr>
                                <?php
                                $no = 1;
                                if ($itemfiles['Upload']) {
                                    foreach ($itemfiles['Upload'] as $file) {
                                        echo '<tr';
                                        if($no%2==1){echo ' class="grayBG"';}
                                        echo '>';
                                        e('<td style="width:15px">'.$no.'</td>');
                                        e('<td>'.$file['description'].'</td>');
                                        echo '<td>';
                                        // file list
                                        if (preg_match('/pdf/', $file['type'])) {
                                            e('<img src="' . $this->webroot . 'img/icon-pdf.png" > <a href="'. $this->webroot . $file['path'] . $file['name'] . '" target="_blank" > '.strstr($file['name'], '_').'</a><br /> ');
                                        } else if (preg_match('/word/', $file['type'])) {
                                            e('<img src="' . $this->webroot . 'img/icon-doc.png" > <a href="'. $this->webroot . $file['path'] . $file['name'] . '" target="_blank" > '.strstr($file['name'], '_').'</a><br /> ');
                                        } else if (preg_match('/excel/', $file['type'])) {
                                            e('<img src="' . $this->webroot . 'img/icon-xls.png" > <a href="'. $this->webroot . $file['path'] . $file['name'] . '" target="_blank" > '.strstr($file['name'], '_').'</a><br /> ');
                                        } else {
                                            e('<img src="' . $this->webroot . 'img/icon_file.png" > <a href="'. $this->webroot . $file['path'] . $file['name'] . '" target="_blank" > '.strstr($file['name'], '_').'</a><br /> ');
                                        }
                                        echo '</td>';
                                        e('<td>'.$file['created'].'</td>');
                                        echo '<td>';
                                        ?>		<a href="javascript:void(0)" onclick="window.open('https://ihanatour.com/settings/pax_upload_edit/<?=$file['id']?>','_blank','width=650px,height=180px');" style="color: blue">Edit</a>	<?php
                                        e(' / '.'<a href="'.$this->webroot.'uploads/delete/'.$file['id'].'" style="color:red;">Delete</a><br /> ');
                                        echo '</td>';
                                        echo '</tr>';
                                        $no += 1;
                                    }
                                }
                                ?>
                                <tr>
                                    <td colspan="2">
                                        <div style="float:left; position:relative">
                                            <?php
                                            echo $this->Form->create('Setting', array('enctype' => 'multipart/form-data','action'=>'pax_upload/'.$items[0]['Item']['id']));
                                            e($this->Form->input('Upload.item_id', array(
                                                'value' => $items[0]['Item']['id'],
                                                'type' => 'hidden'
                                            )));

                                            echo $this->Form->input('Upload.file', array(
                                                'type' => 'file',
                                                'label' => false,
                                                'onchange' => 'javascript:previewImage(this,"View_area");',
                                                'style' => 'position:absolute;'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div style="float:right;">
                                            <?php
                                            echo $this->Form->input('Upload.description', array(
                                                'type' => 'text',
                                                'label' => 'description',
                                                'style' => 'margin-left:8px;'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="float:right;">
                                            <a href="javascript:void(0)" onclick="window.open('https://ihanatour.com/settings/pax_upload_order/<?= $items[0]['Item']['id'] ?>','_blank','width=650px,height=350px');" class="btn blue" style="margin-top:2.5px;margin-right:2px">Order</a>
                                            <input type="submit" value="update" class="ui-button ui-widget ui-state-default ui-corner-all" role="button" aria-disabled="false">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><div id="View_area"></div></td>
                                </tr>
                            </table>
                            </form>
                        </div>
                    </td>
                </tr>
            </table>
    </div>
    <?php
    echo '</div>';
    ?>

    <script>
        $(document).ready(function () {
            $(this).on("dragenter", function (e) {
                $(".drop").parent().css("pointer-events", "none");
                e.stopPropagation();
                e.preventDefault();
                $("body").css("opacity", "0.5");
            });

            $(this).on("dragover", function (e) {
                e.stopPropagation();
                e.preventDefault();
            });

            $(this).on("dragleave", function (e) {
                $(".drop").parent().css("pointer-events", "");
                e.stopPropagation();
                e.preventDefault();
                $("body").css("opacity", "");
            })

            $(this).on("drop", function (e) {
                $(".drop").parent().css("pointer-events", "");
                e.preventDefault();
                $("body").css("opacity", "");
                selectFiles(e.originalEvent.dataTransfer.files);
            });

            function selectFiles(file) {
                var a = $('input#UploadFile');
                a[a.length - 1].files = file;

                return 0;
            }

            $('.manageBtn').each(function () {
                $(this).click(function () {
                    $("#spinner").fadeIn();
                    $('#load').load($(this).attr("href") + '/' + new Date().getTime(), function () {
                        $("#spinner").fadeOut();
                        $('#load a.editBtn').hide();
                    });
                    return false;
                });
            });

            $('.addBtn').each(function () {
                $(this).click(function () {
                    $("#spinner").fadeIn();
                    $('#load').load($(this).attr("href") + '/' + new Date().getTime(), function () {
                        $('#load .title').hide();
                        $("#spinner").fadeOut();
                        $('#ItemTourDate').val('<?= $items[0]['
					Item
					']['
					tour_date
					'] ?>'
                        )
                        ;
                        jQuery("form").validationEngine({autoHidePrompt: true, autoHideDelay: 2500});
                    });
                    return false;
                });
            });

            $("a.more-info").click(function (event) {
                if ($(this).text() == 'Show more info') {
                    $(".Canceled").closest('tr').removeClass('hide');
                    $(".gender").removeClass('hide');
                    $(this).text('Hide info');
                } else {
                    $(".Canceled").closest('tr').addClass('hide');
                    $(".gender").addClass('hide');
                    $(this).text('Show more info');
                }
            });

            /*
                    $('.cancelBtn').click(function() {
                      return confirm('You sure you want to continue?');
                    });
            */

            var prev_val;
            $('.statusSelect').focus(function () {
                prev_val = $(this).val();
            }).change(function () {
                $(this).blur(); // FOR FIREFOX
                var success = confirm('Are you sure you want to change the Dropdown?');
                if (success) {
                    $(this).closest('form').submit();
                } else {
                    $(this).val(prev_val);
                }
            });

            $("a.detailBtn").click(function (event) {
                $(".extraInfo").toggle();
                if ($(this).text() == 'Hide detail') {
                    $(this).text('Show detail');
                } else {
                    $(this).text('Hide detail');
                }
            });
            $("a.commentBtn").click(function (event) {
                $(".comments").toggle();
                if ($(this).text() == 'Hide comments') {
                    $(this).text('Show comments');
                } else {
                    $(this).text('Hide comments');
                }
            });
            // a workaround for a flaw in the demo system (https://dev.jqueryui.com/ticket/4375), ignore!
            $("#dialog:ui-dialog").dialog("destroy");

            $("#editComment").dialog({
                autoOpen: false,
                modal: true,
            });

            $(".editComment").click(function () {
                $("#spinner").fadeIn();
                var title = $(this).text();
                $("#editComment").load($(this).attr("href") + '/' + new Date().getTime(), function () {
                    $("#spinner").fadeOut();
                    $("#editComment").dialog({width: 650, title: title});
                    $("#editComment").dialog("open");
                });
                return false;
            });

            $("#ItemDepositDate").datepicker({
                maxDate: '<?= $itemInfo['Item']['tour_date'] ?>', dateFormat: 'yy-mm-dd'
            });
            $("#ItemPaymentDate").datepicker({
                maxDate: '<?= $itemInfo['Item']['tour_date'] ?>', dateFormat: 'yy-mm-dd'
            });
        });

    </script>

    <?php
    debug($items);
    ?>
