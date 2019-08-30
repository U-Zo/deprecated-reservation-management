<?php

class ReservationsController extends AppController {
    var $name = 'Reservations';

    //set view
    function cbmc() {
        $this->layout = 'booking';
    }

    //find my reservation
    function cbmc_find() {

    }

    //make a reservation
    function cbmc_book() {
        $this->loadModel('CbmcCustomers');

        $i = 0;
        $this->data['CbmcCustomers']['lname'] = $this->data['CbmcCustomers']['lname' . $i];
        $this->data['CbmcCustomers']['fname'] = $this->data['CbmcCustomers']['fname' . $i];
        $this->data['CbmcCustomers']['gender'] = $this->data['CbmcCustomers']['gender' . $i];
        $this->data['CbmcCustomers']['dob'] = $this->data['CbmcCustomers']['dob' . $i];
        $this->data['CbmcCustomers']['phone'] = $this->data['CbmcCustomers']['phone' . $i];
        $this->data['CbmcCustomers']['email'] = $this->data['CbmcCustomers']['email' . $i];
        $this->data['CbmcCustomers']['is_child'] = $this->data['CbmsCustomers']['is_child' . $i];
        $this->data['CbmcCustomers']['tour'] = $this->data['CbmcCustomers']['tour' . $i];
        $this->CbmcCustomers->save($this->data);
    }
}

?>
