<?php

class ReservationsController extends AppController
{

    var $name = 'Reservations';

    /* var $scaffold; */

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'default';
        $this->Auth->allowedActions = array('cbmc', 'cbmc_book', 'cbmc_find');
        //$this->layout = 'sub_L';
    }

    function index()
    {
        $this->set('tours', $this->Tour->find('list'));
        $this->set('toursCat', $this->Tour->find('list', array(
            'fields' => array('id', 'category_id')
        )));
        $this->set('categories', $this->Category->find('list', array(
            'fields' => array('id', 'code')
        )));

        // conditions set up
        if (!empty($this->passedArgs[0])) {
            $this->data['Reservation']['date1'] = $this->passedArgs[0];
            $this->data['Reservation']['date2'] = $this->passedArgs[0];
        }
        if (empty($this->data)) {
            $this->data['Reservation']['date1'] = date('Y-m-d', strtotime("-1 month"));
            $this->data['Reservation']['date2'] = date('Y-m-d', strtotime("+1 year"));
        }
        // conditions set up
        $conditions = array(
            'AND' => array(
                array('Item.tour_date >=' => $this->data['Reservation']['date1']),
                array('Item.tour_date <=' => $this->data['Reservation']['date2'])
            )
        );

        array_push($conditions, array(
            'OR' => array(
                array('Reservation.user_id' => $this->Auth->user('id')),
                array('Reservation.supplier_id' => $this->Auth->user('id'))
            )
        ));
        $this->set('reservations', $this->Reservation->find('all', array(
            'conditions' => $conditions,
            'order' => array(
                'Reservation.created DESC'
            )
        )));
    }

    function index_org()
    {
        $this->layout = 'sub_L';
        $this->set('tours', $this->Tour->find('list'));
        $this->set('toursCat', $this->Tour->find('list', array(
            'fields' => array('id', 'category_id')
        )));
        $this->set('categories', $this->Category->find('list', array(
            'fields' => array('id', 'code')
        )));

        // conditions set up
        if (!empty($this->passedArgs[0])) {
            $this->data['Reservation']['date1'] = $this->passedArgs[0];
            $this->data['Reservation']['date2'] = $this->passedArgs[0];
        }
        if (empty($this->data)) {
            $this->data['Reservation']['date1'] = date('Y-m-d', strtotime("-1 month"));
            $this->data['Reservation']['date2'] = date('Y-m-d', strtotime("+1 year"));
        }
        // conditions set up
        $conditions = array(
            'AND' => array(
                array('Item.tour_date >=' => $this->data['Reservation']['date1']),
                array('Item.tour_date <=' => $this->data['Reservation']['date2'])
            )
        );

        array_push($conditions, array(
            'OR' => array(
                array('Reservation.user_id' => $this->Auth->user('id')),
                array('Reservation.supplier_id' => $this->Auth->user('id'))
            )
        ));
        $this->set('reservations', $this->Reservation->find('all', array(
            'conditions' => $conditions,
            'order' => array(
                'Reservation.created DESC'
            )
        )));
    }

    function view($id = null)
    {

        $reservation = $this->Reservation->read(null, $id);
        $this->set('tours', $this->Tour->find('list'));
        $this->set('tour', $this->Tour->read(null, $reservation['Item']['tour_id']));
        $this->set('categories', $this->Category->find('list', array(
            'fields' => array('id', 'code')
        )));
        $this->set('users', $this->User->find('list'));

        // attention reset
        if ($this->Auth->user('group_id') <= 2 && $reservation['Reservation']['attention'] > 1) {
            $this->Reservation->id = $id;
            $this->Reservation->saveField('attention', '');
        }

        // write Comment
        if (!empty($this->data)) {
            // make attention if agency modify reservation
            if ($this->Auth->user('group_id') > 2) {
                $this->Reservation->id = $id;
                $this->Reservation->saveField('attention', $this->Auth->user('group_id'));

            }
            $this->writeComment($id, $this->data['Comment']['comment'], $this->data['Comment']['user_id'], $this->data['Comment']['reservation_num'], $this->data['Comment']['supplier_id']);
        }

        // check up this reservation's owner
        if ($this->Auth->user('group_id') >= 2) {
            if ($reservation['Reservation']['user_id'] == $this->Auth->user('id') || $reservation['Reservation']['supplier_id'] == $this->Auth->user('id')) {
                $this->set('reservation', $reservation);
                $this->set('tours', $this->Tour->find('list'));
            } else {
                $this->Session->setFlash('Oops!!! You do not have the permission for this request!!');
                $this->redirect($this->referer());
            }
            // for admin account
        } else {
            $this->set('reservation', $reservation);
            $this->set('logs', $this->Log->find('all', array(
                'conditions' => array(
                    'reservation_id' => $id
                ),
                'order' => 'created DESC'
            )));
        }

    }

    function view_org($id = null)
    {

        $this->layout = 'sub_L';

        $reservation = $this->Reservation->read(null, $id);
        $this->set('tours', $this->Tour->find('list'));
        $this->set('tour', $this->Tour->read(null, $reservation['Item']['tour_id']));
        $this->set('categories', $this->Category->find('list', array(
            'fields' => array('id', 'code')
        )));
        $this->set('users', $this->User->find('list'));

        // attention reset
        if ($this->Auth->user('group_id') <= 2 && $reservation['Reservation']['attention'] > 1) {
            $this->Reservation->id = $id;
            $this->Reservation->saveField('attention', '');
        }

        // write Comment
        if (!empty($this->data)) {
            // make attention if agency modify reservation
            if ($this->Auth->user('group_id') > 2) {
                $this->Reservation->id = $id;
                $this->Reservation->saveField('attention', $this->Auth->user('group_id'));

            }
            $this->writeComment($id, $this->data['Comment']['comment'], $this->data['Comment']['user_id'], $this->data['Comment']['reservation_num'], $this->data['Comment']['supplier_id']);
        }

        // check up this reservation's owner
        if ($this->Auth->user('group_id') >= 2) {
            if ($reservation['Reservation']['user_id'] == $this->Auth->user('id') || $reservation['Reservation']['supplier_id'] == $this->Auth->user('id')) {
                $this->set('reservation', $reservation);
                $this->set('tours', $this->Tour->find('list'));
            } else {
                $this->Session->setFlash('Oops!!! You do not have the permission for this request!!');
                $this->redirect($this->referer());
            }
            // for admin account
        } else {
            $this->set('reservation', $reservation);
            $this->set('logs', $this->Log->find('all', array(
                'conditions' => array(
                    'reservation_id' => $id
                ),
                'order' => 'created DESC'
            )));
        }

    }

    function voucher($id = null)
    {

        $this->layout = 'sub_L';

        $reservation = $this->Reservation->read(null, $id);
        $this->set('tours', $this->Tour->find('list'));
        $this->set('tour', $this->Tour->read(null, $reservation['Item']['tour_id']));
        $this->set('categories', $this->Category->find('list', array(
            'fields' => array('id', 'code')
        )));

        // check up this reservation's owner
        if ($this->Auth->user('group_id') >= 2) {
            if ($reservation['Reservation']['user_id'] == $this->Auth->user('id') && ($reservation['Reservation']['status'] == 'Confirmed' || $reservation['Reservation']['status'] == 'Paid')) {
                $this->set('reservation', $reservation);
                $this->set('tours', $this->Tour->find('list'));
            } else {
                $this->Session->setFlash('Oops!!! You do not have permission for this request!!');
                $this->redirect($this->referer());
            }
            // for admin account
        } else {
            $this->set('reservation', $reservation);
            $this->set('tours', $this->Tour->find('list'));
        }
    }

    function writeComment($reservation_id, $comment, $user_id, $reservation_number, $supplier_id)
    {
        $this->Comment->set(array(
            'user_id' => $user_id,
            'comment' => $comment,
            'reservation_id' => $reservation_id,
            'supplier_id' => $supplier_id
        ));
        $email = $this->User->find('all', array(
            'conditions' => array('User.id ' => $user_id),
            'fields' => array('User.email')
        ));
        $email = $email[0]['User']['email'];
        if ($this->Comment->save()) {
            if (!empty($supplier_id)) {
                $toGroupType = 'supplier';
            } else {
                $toGroupType = 'agency';
            }
            $this->commentNotiMail($user_id, $reservation_id, $reservation_number, $comment, $toGroupType, $email);
            $this->Session->setFlash('Saved!');
            $this->redirect('/reservations/view/' . $reservation_id);

        } else {
            $this->Session->setFlash('Failed!');
        }
    }

    function bookNotiMail($id, $reservation_id)
    {
        //Set view variables as normal
        $User = $this->User->read(null, $id);
        $this->set('user', $User);
        $this->set('users', $this->User->find('list'));
        $reservation = $this->Reservation->read(null, $reservation_id);
        $this->set('tours', $this->Tour->find('list'));
        $this->set('tour', $this->Tour->read(null, $reservation['Item']['tour_id']));
        $this->set('categories', $this->Category->find('list', array(
            'fields' => array('id', 'code')
        )));
        $this->set('reservation', $reservation);

        $this->Email->to = $User['User']['email'];
        if ($User['User']['group_id'] > 1) {
            $this->Email->bcc = array('info@ihanatour.com');
        }
        $this->Email->subject = $reservation['Reservation']['tour_name'];
        $this->Email->replyTo = 'info@ihanatour.com';
        $this->Email->from = 'HanaTourTripBooking <info@ihanatour.com>';
        if ($User['User']['group_id'] <= 1 || $User['User']['group_id'] == 6) {
            $this->Email->template = 'notiBookDetail'; // note no '.ctp'
        } else {
            $this->Email->template = 'notiBook'; // note no '.ctp'
        }
        //Send as 'html', 'text' or 'both' (default is 'text')
        $this->Email->sendAs = 'html'; // because we like to send pretty mail

        //Do not pass any args to send()
        if ($this->Email->send()) {
            $this->Session->setFlash('Email sent');
        } else {
            $this->Session->setFlash('Email fail');
        }

    }

    function cbmc()
    {
        $this->layout = 'booking';
    }

    // CBMC - find my reservation
    function cbmc_find()
    {

    }

    // CBMC - print reservation list
    function cbmc_list()
    {
        $this->layout = 'booking';
    }

    function cbmc_book()
    {
        $this->layout = "booking";
        $this->loadModel('Cbmc');
        $tour = $this->Tour->find('list');
        $this->data['Reservation']['tour_name'] = $tour[$this->data['Item']['tour_id']];

        /* Initial checking Item and user */
        $checkItem = $this->Item->find('first', array(
            'conditions' => array(
                'Item.tour_id' => $this->data['Item']['tour_id'],
                'Item.tour_date' => $this->data['Item']['tour_date']
            )));
        /* find out the latest reservation id number */
        $checkReservation = $this->Reservation->find('first', array('order' => array('Reservation.id DESC')));
        $this->data['Reservation']['user_id'] = $this->Auth->user('id');

        // collect pax names and information
        if (in_array($this->data['Reservation']['cat_id'], $this->hotelCategory)) {
            // define pax num
            $paxNum = $this->data['Reservation']['room'];
            $this->data['Reservation']['adult_num'] = $paxNum;
            // define Room type
            if ($this->data['Reservation']['room'] > 0) {
                for ($i = 0; $i < $this->data['Reservation']['room']; $i++) {
                    $this->data['Reservation']['room_types'] .= $this->data['Reservation']['room_type' . $i] . '-' . $this->data['Reservation']['lname' . $i] . '/' . $this->data['Reservation']['fname' . $i] . ',';
                }
            }
            // total price for Hotel reservation
            $totalDay = (strtotime($this->data['Reservation']['date_checkout']) - strtotime($this->data['Item']['tour_date'])) / (60 * 60 * 24);
            $this->data['Reservation']['total'] = $this->data['Reservation']['total'] * $totalDay * $paxNum;
            $this->data['Reservation']['total_commission'] = $this->data['Reservation']['commission'] * $totalDay * $paxNum;
            $this->data['Reservation']['total_fees'] = $this->data['Reservation']['fee'] * $totalDay * $paxNum;
        } else {

            //print_r($this->data);

            //define pax num
            $paxNum = $this->data['CbmcReservations']['adults'] + $this->data['CbmcReservations']['children'];
            // 	PAX INFORMATION LIKE NAME, DOB, PASSPORT, ADDRESS
            for ($i = 0; $i < $paxNum; $i++) {

                $profile_id = $this->data['Reservation']['profileID' . $i];
                $org_profile = $this->Profile->findById($profile_id);

                //echo '바꾼거';
                //echo $this->data['Reservation']['fname'.$i];
                //echo '원래';
                //echo $org_profile['Profile']['fname'];

                if ($this->data['Reservation']['lname' . $i] == $org_profile['Profile']['lname']
                    && $this->data['Reservation']['fname' . $i] == $org_profile['Profile']['fname']
                    && $this->data['Reservation']['dob' . $i] == $org_profile['Profile']['dob']
                    && $this->data['Reservation']['phone_number' . $i] == $org_profile['Profile']['home_phone']
                    && $this->data['Reservation']['email' . $i] == $org_profile['Profile']['home_email']
                    && $this->data['Reservation']['address' . $i] == $org_profile['Profile']['home_address1']) {
                    $this->data['Profile']['id'] = $this->data['Reservation']['profileID' . $i];
                } else {
                    $this->data['Profile']['id'] = '';
                }
                //echo '/-';
                //echo $this->data['Profile']['id'];
                //echo '-/';
                $this->data['Profile']['lname'] = $this->data['CbmcCustomers']['lname' . $i];
                $this->data['Profile']['fname'] = $this->data['CbmcCustomers']['fname' . $i];
                $this->data['Profile']['dob'] = $this->data['CbmcCustomers']['dob' . $i];
                $this->data['Profile']['gender'] = $this->data['CbmcCustomers']['gender' . $i];
                $this->data['Profile']['home_address1'] = $this->data['CbmcCustomers']['address'];
                $this->data['Profile']['home_city'] = $this->data['CbmcCustomers']['city'];
                $this->data['Profile']['home_state'] = $this->data['CbmcCustomers']['state'];
                $this->data['Profile']['home_zip'] = $this->data['CbmcCustomers']['zip'];
                $this->data['Profile']['home_phone'] = $this->data['CbmcCustomers']['phone' . $i];
                $this->data['Profile']['home_email'] = $this->data['CbmcCustomers']['email' . $i];
                $this->data['Profile']['room'] = $this->data['CbmcReservations']['bed_type'];
                $this->data['Profile']['is_child'] = $this->data['CbmcCustomers']['is_child' . $i];
                $this->data['Reservation']['room_types'] = $this->data['CbmcReservations']['bed_type'];

                // not using the profile check existence
                if (!$this->data['Profile']['id']) {
                    //echo 'create new profile';
                    $this->Profile->save($this->data);
                    $this->data['Profile']['Profile'][] = $this->Profile->id;
                    $this->data['Cbmc']['profile_id'] = $this->Profile->id;
                    $this->data['Cbmc']['profile_id'] = $this->data['Cbmc']['profile_id'] - 0;
                    $this->data['Cbmc']['tour'] = $this->data['CbmcCustomers']['tour' . $i];
                    $this->data['Cbmc']['room'] = $this->data['CbmcCustomers']['room' . $i];
                    $this->data['Cbmc']['check_in'] = $this->data['CbmcCustomers']['check_in'];
                    $this->data['Cbmc']['check_out'] = $this->data['CbmcCustomers']['check_out'];
                    $this->Cbmc->create();
                    $this->Cbmc->save($this->data);
                } else {
                    $this->Profile->save($this->data);
                    $this->data['Profile']['Profile'][] = $this->data['Reservation']['profileID' . $i];
                }
                $profile_id = '';
                $org_profile = '';
                //echo $profile_id;
                //echo $org_profile;
                //echo '////';
            }
            // total price for Tour reservation
            $this->data['Reservation']['total'] = $this->data['CbmcCustomers']['price'];
            //$this->data['Reservation']['total_commission'] = $this->data['Reservation']['commission'] * $paxNum;
            //$this->data['Reservation']['total_fees'] = $this->data['Reservation']['fee'] * $paxNum;
            $this->data['Reservation']['adult_num'] = $paxNum;
        }
        $this->data['Item']['pax_num'] = $paxNum;

        // PAYMENT STUFF
        if (empty($this->data['Payment'][0]['amount']) || $this->data['Payment'][0]['amount'] == 0) {
            unset($this->data['Payment']);
        } else {
            $payment = $this->data['Payment'][0]['amount'];
            if ($this->data['Reservation']['total'] <= $payment) {
                $this->data['Reservation']['status'] = 'Paid';
            }
        }

        /* 			$this->data['Reservation']['cc'] = Security::cipher($this->data['Reservation']['cc'], Configure::read('Security.salt')); */

        /* Checking if a item exists */
        if ($checkItem) {
            /* update existing item */
            $this->data['Reservation']['item_id'] = $checkItem['Item']['id'];

            // Item status checking
            $totalPax = $checkItem['Item']['pax_num'] + $paxNum;
            if ($totalPax >= $checkItem['Tour']['minimum']) {
                $statusTxt = '확정';
            } else {
                $statusTxt = '대기';
            }
            $this->data['Item']['id'] = $checkItem['Item']['id'];
            $this->data['Item']['pax_num'] = $totalPax;
            $this->data['Item']['status'] = $statusTxt;

            // updating Item and create new record for Reservation
            if ($this->Reservation->saveAll($this->data)) {
                $this->bookNotiMail($this->Auth->user('id'), $this->data['Reservation']['id']);
                if ($this->data['Reservation']['wholesaler_id']) {
                    $this->bookNotiMail($this->data['Reservation']['wholesaler_id'], $this->data['Reservation']['id']);
                }
                //$this->Reservation->setFlash('Successfully booked.<script>alert(\'귀사의 상품예약요청을 접수하였습니다.  다음사항을 주의하여 주십시요.  본상품예약이 확정되면 현지시각으로 손님께서 투어를  시작하는일 또는 호텔 체크인 일로부터 최소 4일전까지 상품비를 완납을 하셔야 합니다.   투어시작 또는 호텔체크인 4일전까지 상품비를 미납하시는경우 모든 투어 일정 또는 호텔예약이 자동으로 취소됨을 사전에 알려드립니다. 자동으로 취소된 후에는 취소되었다는 노티스를 따로 드리지 않습니다.   대단히 감사합니다.\')</script>');
                echo "<script>alert('Successfully booked.'); location.href = 'https://ihanatour.com/reservations/cbmc';</script>";
            } else {
                //$this->Reservation->setFlash('Please try again.');
                $this->redirect($this->referer());
            }

        } else {
            /* New Item record */
            // find out new item id number
            /*
                            $checkItem = $this->Item->find('first', array('order' => array('Item.id DESC')));
                            $this->data['Reservation']['item_id'] = $checkItem['Item']['id']+1;
            */
            /* create a new item for reservation */
            if ($this->Reservation->saveAll($this->data)) {
                $this->bookNotiMail($this->Auth->user('id'), $this->data['Reservation']['id']);
                if ($this->data['Reservation']['wholesaler_id']) {
                    $this->bookNotiMail($this->data['Reservation']['wholesaler_id'], $this->data['Reservation']['id']);
                }
                $this->Session->setFlash('Successfully booked.<script>alert(\'귀사의 상품예약요청을 접수하였습니다.  다음사항을 주의하여 주십시요.  본상품예약이 확정되면 현지시각으로 손님께서 투어를  시작하는일 또는 호텔 체크인 일로부터 최소 4일전까지 상품비를 완납을 하셔야 합니다.   투어시작 또는 호텔체크인 4일전까지 상품비를 미납하시는경우 모든 투어 일정 또는 호텔예약이 자동으로 취소됨을 사전에 알려드립니다. 자동으로 취소된 후에는 취소되었다는 노티스를 따로 드리지 않습니다.   대단히 감사합니다.\')</script>');
                $this->redirect($this->referer());
            } else {
                $this->Session->setFlash('Please try again.');
                $this->redirect($this->referer());
            }
        }
    }

    function book()
    {
        if (!empty($this->data)) {
            /* Find tour name */
            $tour = $this->Tour->find('list');
            $this->data['Reservation']['tour_name'] = $tour[$this->data['Item']['tour_id']];

            /* Initial checking Item and user */
            $checkItem = $this->Item->find('first', array(
                'conditions' => array(
                    'Item.tour_id' => $this->data['Item']['tour_id'],
                    'Item.tour_date' => $this->data['Item']['tour_date']
                )));
            /* find out the latest reservation id number */
            $checkReservation = $this->Reservation->find('first', array('order' => array('Reservation.id DESC')));
            $this->data['Reservation']['user_id'] = $this->Auth->user('id');

            // collect pax names and information
            if (in_array($this->data['Reservation']['cat_id'], $this->hotelCategory)) {
                // define pax num
                $paxNum = $this->data['Reservation']['room'];
                $this->data['Reservation']['adult_num'] = $paxNum;
                // define Room type
                if ($this->data['Reservation']['room'] > 0) {
                    for ($i = 0; $i < $this->data['Reservation']['room']; $i++) {
                        $this->data['Reservation']['room_types'] .= $this->data['Reservation']['room_type' . $i] . '-' . $this->data['Reservation']['lname' . $i] . '/' . $this->data['Reservation']['fname' . $i] . ',';
                    }
                }
                // total price for Hotel reservation
                $totalDay = (strtotime($this->data['Reservation']['date_checkout']) - strtotime($this->data['Item']['tour_date'])) / (60 * 60 * 24);
                $this->data['Reservation']['total'] = $this->data['Reservation']['total'] * $totalDay * $paxNum;
                $this->data['Reservation']['total_commission'] = $this->data['Reservation']['commission'] * $totalDay * $paxNum;
                $this->data['Reservation']['total_fees'] = $this->data['Reservation']['fee'] * $totalDay * $paxNum;
            } else {

                //print_r($this->data);

                //define pax num
                $paxNum = $this->data['Reservation']['adult_num'] /* + $this->data['Reservation']['child_num'] */
                ;
                // 	PAX INFORMATION LIKE NAME, DOB, PASSPORT, ADDRESS
                for ($i = 0; $i < $paxNum; $i++) {

                    $profile_id = $this->data['Reservation']['profileID' . $i];
                    $org_profile = $this->Profile->findById($profile_id);

                    //echo '바꾼거';
                    //echo $this->data['Reservation']['fname'.$i];
                    //echo '원래';
                    //echo $org_profile['Profile']['fname'];

                    if ($this->data['Reservation']['lname' . $i] == $org_profile['Profile']['lname']
                        && $this->data['Reservation']['fname' . $i] == $org_profile['Profile']['fname']
                        && $this->data['Reservation']['dob' . $i] == $org_profile['Profile']['dob']
                        && $this->data['Reservation']['phone_number' . $i] == $org_profile['Profile']['home_phone']
                        && $this->data['Reservation']['email' . $i] == $org_profile['Profile']['home_email']
                        && $this->data['Reservation']['address' . $i] == $org_profile['Profile']['home_address1']) {
                        $this->data['Profile']['id'] = $this->data['Reservation']['profileID' . $i];
                    } else {
                        $this->data['Profile']['id'] = '';
                    }
                    //echo '/-';
                    //echo $this->data['Profile']['id'];
                    //echo '-/';
                    $this->data['Profile']['lname'] = $this->data['Reservation']['lname' . $i];
                    $this->data['Profile']['fname'] = $this->data['Reservation']['fname' . $i];
                    $this->data['Profile']['dob'] = $this->data['Reservation']['dob' . $i];
                    $this->data['Profile']['gender'] = $this->data['Reservation']['gender' . $i];
                    $this->data['Profile']['nation'] = $this->data['Reservation']['nation' . $i];
                    $this->data['Profile']['passport'] = $this->data['Reservation']['passport' . $i];
                    $this->data['Profile']['exp'] = $this->data['Reservation']['exp' . $i];
                    $this->data['Profile']['home_address1'] = $this->data['Reservation']['address' . $i];
                    $this->data['Profile']['home_city'] = $this->data['Reservation']['city' . $i];
                    $this->data['Profile']['home_state'] = $this->data['Reservation']['state' . $i];
                    $this->data['Profile']['home_zip'] = $this->data['Reservation']['zip' . $i];
                    $this->data['Profile']['home_phone'] = $this->data['Reservation']['phone_number' . $i];
                    $this->data['Profile']['work_phone'] = $this->data['Reservation']['work_number' . $i];
                    $this->data['Profile']['cell_phone'] = $this->data['Reservation']['cell_number' . $i];
                    $this->data['Profile']['home_email'] = $this->data['Reservation']['email' . $i];
                    $this->data['Profile']['room'] = $this->data['Reservation']['room_types'];

                    // not using the profile check existence
                    if (!$this->data['Profile']['id']) {
                        //echo 'create new profile';
                        $this->Profile->create();
                        $this->Profile->save($this->data);
                        $this->data['Profile']['Profile'][] = $this->Profile->id;
                    } else {
                        $this->Profile->save($this->data);
                        $this->data['Profile']['Profile'][] = $this->data['Reservation']['profileID' . $i];
                    }
                    $profile_id = '';
                    $org_profile = '';
                    //echo $profile_id;
                    //echo $org_profile;
                    //echo '////';
                }
                // total price for Tour reservation
                $this->data['Reservation']['total'] = $this->data['Reservation']['total'] * $paxNum;
                $this->data['Reservation']['total_commission'] = $this->data['Reservation']['commission'] * $paxNum;
                $this->data['Reservation']['total_fees'] = $this->data['Reservation']['fee'] * $paxNum;
            }

            $this->data['Item']['pax_num'] = $paxNum;

            // PAYMENT STUFF
            if (empty($this->data['Payment'][0]['amount']) || $this->data['Payment'][0]['amount'] == 0) {
                unset($this->data['Payment']);
            } else {
                $payment = $this->data['Payment'][0]['amount'];
                if ($this->data['Reservation']['total'] <= $payment) {
                    $this->data['Reservation']['status'] = 'Paid';
                }
            }

            /* 			$this->data['Reservation']['cc'] = Security::cipher($this->data['Reservation']['cc'], Configure::read('Security.salt')); */

            /* Checking if a item exists */
            if ($checkItem) {
                /* update existing item */
                $this->data['Reservation']['item_id'] = $checkItem['Item']['id'];

                // Item status checking
                $totalPax = $checkItem['Item']['pax_num'] + $paxNum;
                if ($totalPax >= $checkItem['Tour']['minimum']) {
                    $statusTxt = '확정';
                } else {
                    $statusTxt = '대기';
                }
                $this->data['Item']['id'] = $checkItem['Item']['id'];
                $this->data['Item']['pax_num'] = $totalPax;
                $this->data['Item']['status'] = $statusTxt;

                // updating Item and create new record for Reservation
                if ($this->Reservation->saveAll($this->data)) {
                    $this->bookNotiMail($this->Auth->user('id'), $this->data['Reservation']['id']);
                    if ($this->data['Reservation']['wholesaler_id']) {
                        $this->bookNotiMail($this->data['Reservation']['wholesaler_id'], $this->data['Reservation']['id']);
                    }
                    $this->Session->setFlash('Successfully booked.<script>alert(\'귀사의 상품예약요청을 접수하였습니다.  다음사항을 주의하여 주십시요.  본상품예약이 확정되면 현지시각으로 손님께서 투어를  시작하는일 또는 호텔 체크인 일로부터 최소 4일전까지 상품비를 완납을 하셔야 합니다.   투어시작 또는 호텔체크인 4일전까지 상품비를 미납하시는경우 모든 투어 일정 또는 호텔예약이 자동으로 취소됨을 사전에 알려드립니다. 자동으로 취소된 후에는 취소되었다는 노티스를 따로 드리지 않습니다.   대단히 감사합니다.\')</script>');
                    $this->redirect($this->referer());
                } else {
                    $this->Session->setFlash('Please try again.');
                    $this->redirect($this->referer());
                }

            } else {
                /* New Item record */
                // find out new item id number
                /*
                                $checkItem = $this->Item->find('first', array('order' => array('Item.id DESC')));
                                $this->data['Reservation']['item_id'] = $checkItem['Item']['id']+1;
                */
                /* create a new item for reservation */
                if ($this->Reservation->saveAll($this->data)) {
                    $this->bookNotiMail($this->Auth->user('id'), $this->data['Reservation']['id']);
                    if ($this->data['Reservation']['wholesaler_id']) {
                        $this->bookNotiMail($this->data['Reservation']['wholesaler_id'], $this->data['Reservation']['id']);
                    }
                    $this->Session->setFlash('Successfully booked.<script>alert(\'귀사의 상품예약요청을 접수하였습니다.  다음사항을 주의하여 주십시요.  본상품예약이 확정되면 현지시각으로 손님께서 투어를  시작하는일 또는 호텔 체크인 일로부터 최소 4일전까지 상품비를 완납을 하셔야 합니다.   투어시작 또는 호텔체크인 4일전까지 상품비를 미납하시는경우 모든 투어 일정 또는 호텔예약이 자동으로 취소됨을 사전에 알려드립니다. 자동으로 취소된 후에는 취소되었다는 노티스를 따로 드리지 않습니다.   대단히 감사합니다.\')</script>');
                    $this->redirect($this->referer());
                } else {
                    $this->Session->setFlash('Please try again.');
                    $this->redirect($this->referer());
                }
            }
        }
    }

    function commentNotiMail($id, $reservation_id, $reservation_num, $text, $toGroupType, $email)
    {
        $User = $this->User->read(null, $id);
        $Reservation = $this->Reservation->read(null, $reservation_id);
        $Owner = $this->User->read(null, $Reservation['Reservation']['user_id']);
        $Supplier = $this->User->read(null, $Reservation['Reservation']['supplier_id']);
        // for admin
        if ($User['User']['group_id'] <= 1) {
            if ($toGroupType == 'agency') {
                $this->Email->to = $Owner['User']['email'];
            } else if ($toGroupType == 'supplier') {
                $this->Email->to = $Supplier['User']['email'];
            }
        } else {
            //for agency
            $this->Email->to = array('info@ihanatour.com');
        }
        $this->Email->subject = 'Comment on reservation#, ' . $reservation_num;
        $this->Email->replyTo = $email;
        $this->Email->from = 'HANATOUR Web App <' . $email . '>';
        $this->Email->template = 'notiComment'; // note no '.ctp'
        //Send as 'html', 'text' or 'both' (default is 'text')
        $this->Email->sendAs = 'html'; // because we like to send pretty mail
        //Set view variables as normal
        $this->set('user', $User);
        $this->set('comment', $text);
        $this->set('reservation_num', $reservation_num);
        $this->set('reservation_id', $reservation_id);
        //Do not pass any args to send()
        if ($this->Email->send()) {
            $this->Session->setFlash('Email sent');
        } else {
            $this->Session->setFlash('Email fail');
        }

    }

    function maskCardNumber($cardNumber)
    {
        return 'XXXXXXXXXXXX' . substr($cardNumber, -4);
    }

}

?>
