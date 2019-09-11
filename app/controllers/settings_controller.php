<?php

class SettingsController extends AppController
{
    var $name = 'Settings';

    function cbmc_list()
    {
        $this->loadModel('Cbmc');
        $this->layout = 'admin_blank';
        if ($this->data['Expense']) {
            if ($this->Expense->save($this->data)) {
                $this->Session->setFlash('Successfully saved.');
                $this->redirect($this->referer() . '#expenses');
            } else {
                $this->Session->setFlash('Something wrong. Please try again.');
            }
        } else if ($this->data['Item']) {
            if ($this->Item->save($this->data, array('validate' => false))) {
                $this->Session->setFlash('Successfully updated.');
                $this->redirect($this->referer());
            } else {
                $this->Session->setFlash('Something wrong. Please try again.');
            }
        }

        $pro = $this->Profile->findById(11629);
        $this->set('pro', $pro);

        $items = $this->Reservation->find('all', array(
            'conditions' => array('item_id' => 1370),
            'order' => 'Reservation.created ASC',
            'recursive' => 2
        ));
        $itemInfo = $this->Item->findById(1370);
        $itemInfoExpenses = $this->Expense->find('all', array(
            'conditions' => array('item_id' => 1370),
            'order' => 'Expense.order ASC'
        ));
        $comments = $this->Comment->find('all', array(
            'conditions' => array('Comment.item_id' => 1370),
            'order' => 'Comment.created DESC'
        ));
        $itemfiles = $this->Item->read(null, 1370);
        $Cbmc = $this->Cbmc->find('all');

        $this->set('items', $items);
        $this->set('itemInfo', $itemInfo);
        $this->set('itemInfoExpenses', $itemInfoExpenses);
        $this->set('comments', $comments);
        $this->set('itemfiles', $itemfiles);
        $this->set('cbmc', $Cbmc);
    }
}