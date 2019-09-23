<?php

class ReservationsController extends AppController
{

    var $name = 'Reservations';

    /* var $scaffold; */

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'default';
        $this->Auth->allowedActions = array('cbmc_book');
        //$this->layout = 'sub_L';
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

                // not using the profile check existence
                if (!$this->data['Profile']['id']) {
                    //echo 'create new profile';
                    $this->Profile->save($this->data);
                    $this->data['Profile']['Profile'][] = $this->Profile->id;
                    $this->data['Cbmc']['profile_id'] = $this->Profile->id;
                    $this->data['Cbmc']['profile_id'] = $this->data['Cbmc']['profile_id'] - 0;
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
}

?>
