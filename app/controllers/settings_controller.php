<?php
class SettingsController extends AppController	{
	var $name = 'Settings';
	/* var $scaffold; */
	function beforeFilter() {
		parent::beforeFilter();
		// AGENT PERMISSION
		$agentAction = array('index', 'bulletin', 'bulletin_news', 'bulletin_view', 'bulletin_write', 'user_myinfo', 'calendar', 'calendar_new', 'email_ticket', 'air_news', 'get_air_news_id');
		$wholesalerAction = array('user_myinfo', 'reservations', 'reservations_view');
		if($this->isAuthorized() == false || ($this->Auth->user('group_id') == 3 && !in_array($this->params['action'], $agentAction) ) || ($this->Auth->user('group_id') == 6 && !in_array($this->params['action'], $wholesalerAction) ) ) {
			$this->Session->setFlash('You don\'t have the permission to access the page your requested.');
			$this->redirect('/');
		}
		$setting = $this->Setting->read(null, 1);
		if($this->Auth->user('group_id') != 1 && $this->params['action'] == 'report' && (date('H:i:s') < $setting['Setting']['accesstime_from'] || date('H:i:s') > $setting['Setting']['accesstime_to'])) {
			$this->Session->setFlash('It can not be accessed');
			$this->redirect($this->referer());
		}
        $this->layout = 'admin';
        $this->set('params', $this->params);
		$this->Auth->allowedActions = array('cbmc_list', 'cbmc_notice', 'cbmc_notice_edit');
	}

	function index() {
		if(!empty($this->data)) {
			if($this->Setting->saveAll($this->data)) {
				$this->Session->setFlash('Successfully updated!!');
			} else {
				$this->Session->setFlash('Please try again!!');
			}
		} else {
			$this->data = $this->Setting->read(null, 1);
		}
		$this->set('download', $this->Board->read(null, 2));
	}

	function bulletin($id=null, $limit=null) {
		$this->set('users', $this->User->find('list'));
		// PAGENATION
		$conditions = array('Board.category_id' => $id, 'Board.sticky' => 0);
		$this->paginate = array(
			'conditions'=>$conditions,
			'order'=>array( 'Board.created DESC' ),
			'limit' => $limit
		);
		$posts = $this->paginate('Board');

		$conditions['Board.sticky'] = 1;
		$this->set('stickyPosts', $this->Board->find('all', array(
			'conditions' => $conditions,
			'order' => 'Board.created DESC'
		)));
		$this->set('posts', $posts);
		$this->set('board_id', $id);
		$this->helpers['Paginator'] = array('ajax' => 'Ajax');

		// latest posts
		if(isset($this->params['requested'])) {
			return $posts;
			//return array('posts' => $posts, 'paging' => $this->params['paging']);
		} else {
			$this->set('posts', $posts);
		}
	}

	function bulletin_news($id=null, $action=null) {

		$this->set('users', $this->User->find('list'));
		$this->set('newsBulletins', $this->BoardCategory->find('list', array(
			'conditions' => array( 'type' => 'bulletin_news' )
		)));
		// PAGENATION
		$conditions = array('Board.category_id' => $id);
		if(!empty($this->data['Setting']['cat'])) {
			$conditions['Airdoc.territory'] = $this->data['Setting']['cat'];
		}
		if(!empty($this->data['Setting']['searchWord'])) {
			$conditions['OR']['Board.body LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$conditions['OR']['Board.title LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$conditions['OR']['Airdoc.auth LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$this->set('searchWord', $this->data['Setting']['searchWord']);
		}
		if($this->Auth->user('group_id') > 2) {
			$conditions['Board.secured !='] = 1;
		}
		$this->paginate = array(
			'conditions'=>$conditions,
			'order'=>array( 'Board.created DESC' ),
			'limit' => 50
		);

		$posts = $this->paginate('Board');
//		$this->set('posts', $posts);
//		$posts = $this->Board->find('all', array(
//			'conditions'=>$conditions,
//			'order'=>array( 'Board.created DESC' )
//		));
		$this->set('conditions', $conditions);

		//COMMENT
		if(!empty($this->data['Setting']['comment'])) {
			$this->data['Comment']['board_id'] = 1;
			$this->data['Comment']['user_id'] = $this->Auth->user('id');
			$this->data['Comment']['comment'] = $this->data['Setting']['comment'];
			$this->Comment->save($this->data);
			$this->data['Setting']['comment'] ="";
		}

		//DELETE BULK DATA
		if(!empty($this->data['delete'])) {
			if ($action == 'moveto') {
				foreach( $this->data['delete'] as $key=>$value ) {
					if($value == 1) {
						$this->Board->id = $key;
						$this->Board->saveField('category_id', $this->data['Setting']['moveTo']);
					}
				}
			} else {
				foreach( $this->data['delete'] as $key=>$value ) {
					if($value == 1) {
						$files = $this->Board->read(null, $key);
						if($this->Board->delete($key)) {
							// delete files on the server
							if ( !empty($files['Upload']) ) {
								foreach($files['Upload'] as $file) {
									$oldfile = $file['path'] . $file['name'];
									unlink($oldfile);
								}
							}
						}
					}
				}
			}
			$this->redirect('/settings/bulletin_news/'.$id);
		}

		$this->set('posts', $posts);
		$this->set('board_id', $id);
		$this->set('cat', $this->data['Setting']['cat']);
		$this->set('comments', $this->Comment->find('all', array(
			'conditions' => array('board_id'=>1),
			'order' => 'Comment.created DESC',
			'recursive' => -1
		)));

		$lastUpdated = $this->Board->find('first', array(
			'conditions' => array('Board.category_id'=> array(12, 13, 14, 15)),
			'order' => 'Board.modified DESC'
		));
		$this->set('lastUpdated', $lastUpdated['Board']['modified']);

		// Automatically Move to Archive
		/* foreach ($posts as $post) :
			if($post['Board']['created'] <= date("Y-m-d h:i:s",strtotime ("-1 years"))){
				$this->Board->id = $post['Board']['id'];
				$this->Board->saveField('category_id', 15);
			}
		endforeach; */

		// latest posts
		if(isset($this->params['requested'])) {
			return $posts;
			//return array('posts' => $posts, 'paging' => $this->params['paging']);
		} else {
			$this->set('posts', $posts);
		}
	}

	function bulletin_view($id=null) {
		if(!empty($id)) {
			$board = $this->Board->read(null, $id);
			$this->set('notice', $board);
			$this->Board->set('hit', $board['Board']['hit'] + 1 );
			$this->Board->save();

			// PAGENATION
			$conditions = array('Board.category_id' => $board['Board']['category_id'], 'Board.secured' => 0);
			//get total of articles of the board
			$this->set('total_article', $this->Board->find('count', array('conditions'=>$conditions)));
			$this->paginate = array(
				'conditions'=>$conditions,
				'order'=>array( 'Board.created DESC' ),
				'limit' => 15
			);
			$boards = $this->paginate('Board');
/* 			$this->helpers['Paginator'] = array('ajax' => 'Ajax'); */
			$this->set(compact('boards'));

			// FOR ONLY ADMIN BULLETIN
			if ( $this->Auth->user('group_id') > 2 && (in_array($board['Board']['category_id'], $this->adminBullet ) || $board['Board']['secured'] != 0 ) ) {
			    if($this->Auth->user('username') != 'dannyboy'){
				    $this->Session->setFlash('You do not have permission.');
				    $this->redirect(array('action' => 'index'));
			    }
			}
		} else {
			$this->redirect(array('action' => 'index'));
		}
		$this->set('boardCategories', $this->BoardCategory->find('list'));
		$this->set('idTitle', $id);
		$this->set('comments', $this->Comment->find('all', array(
			'conditions'=> array(
				'board_id' => $id
			),
			'order' => 'Comment.created ASC'
		)));
		$this->set('users', $this->User->find('list'));
	}

	function bulletin_write($id = null) {
		/* Agency permission check */
		if( $this->Auth->user('group_id') == 3 && !empty($this->params['named']['category']) && $this->params['named']['category'] != 11 ) {
			$this->Session->setFlash('You don\'t have a permission to request.');
			$this->redirect(array('action' => 'index'));
		}

		$this->set('boardCategories', $this->BoardCategory->find('list'));
		$this->set('newsCategories', $this->BoardCategory->find('list', array(
			'conditions' => array( 'type' => 'bulletin_news' )
		)));
		$this->set('category_id', $id);

		//extract agencies who will get newsletter
		$agencies = $this->User->find('list', array(
			'conditions' => array('group_id' => 3, 'newsletter LIKE' => '%'.$this->data['Board']['category_id'].'%'),
			'order' => 'name ASC',
			'fields' => array('email','email_extra')
		));
		foreach($agencies as $key=>$value) {
			$agencyEmails[] = $key;
			if(!empty($value)) {
				$extraEmails = explode(',', $value);
				foreach ($extraEmails as $extraEmail) {
					$agencyEmails[] = $extraEmail;
				}
			}
		}

		$category = $this->BoardCategory->find('first', array(
			'conditions' => array( 'id' => $this->data['Board']['category_id'] )
		));

		if(!empty($this->data)) {
			// WHEN IT IS NEW ARTICLE
			if ( empty($this->data['Board']['id']) ) {
				$this->data['Board']['user_id'] = $this->Auth->user('id');
			}
			// When it is not
			else {
				$this->data['Board']['modified'] = DboSource::expression('NOW()');
			}

			if ($this->Board->save($this->data)) {
				if ( !empty($this->data['Upload'])) {
					if($this->data['Board']['id']) {
						$this->upload($this->data['Board']['id']);
					} else {
						$this->upload($this->Board->getLastInsertID());
					}
				}
				if ( !empty($this->data['Airdoc']) ) {
					if(!$this->data['Board']['id']) {
						$this->data['Airdoc']['board_id'] = $this->Board->getLastInsertID();
					}
					$this->Airdoc->save($this->data);
				}

				// IF SENDING EMAIL TO AGENCIES
				if($category['BoardCategory']['type'] == 'bulletin_news'){
					if ($this->data['Setting']['sendEmail'] == 1 && $this->data['Board']['secured'] != 1) {
						if ( empty($this->data['Board']['id']) ) {
							$id = $this->Board->getLastInsertID();
						}
						$news = $this->Board->read(null, $id);

				    	$this->Email->to = 'info@ihanatour.com';
				    	$this->Email->bcc = $agencyEmails;
				    	$this->Email->subject = $this->data['Airdoc']['airline'].' news ['.$this->data['Airdoc']['territory'].'-'.$this->data['Airdoc']['doc'].'] ' . $this->data['Board']['title'];
				    	$this->Email->replyTo = 'info@ihanatour.com';
				    	$this->Email->from = 'HANATOUR Web App <info@ihanatour.com>';
				    	$this->Email->template = 'bulletin_news'; // note no '.ctp'
				    	//Send as 'html', 'text' or 'both' (default is 'text')
				    	$this->Email->sendAs = 'html'; // because we like to send pretty mail
				    	//Set view variables as normal
				    	$this->set('news', $news);
				    	//Do not pass any args to send()
				    	if( $this->Email->send() ) {
				    		$this->Session->setFlash('Email sent');
				    	} else {
				    		$this->Session->setFlash('Email fail');
				    	}
					}
				}
				$this->Session->setFlash('Successfully saved');
				if($category['BoardCategory']['type'] == 'bulletin_news'){
					$this->redirect(array('action' => 'air_news/'.$this->data['Board']['category_id'].'/15'));
				}
				else {
					$this->redirect(array('action' => 'index'));
				}
			} else {
				$this->Session->setFlash('Please try again!!');
				if($category['BoardCategory']['type'] == 'bulletin_news'){
					$this->redirect(array('action' => 'air_news/'.$this->data['Board']['category_id'].'/15'));
				}
				else {
					$this->redirect(array('action' => 'index'));
				}
			}
		} else {
			if($id){
				$this->data = $this->Board->read(null, $id);
			}
			if (!empty($this->data) && $this->Auth->user('group_id') == 3 && $this->data['Board']['user_id'] != $this->Auth->user('id')) {
				$this->Session->setFlash('It is not yours!!');
				$this->redirect(array('action' => 'index'));
			}
		}
	}

	function air_news($id, $action) {
		$this->set('users', $this->User->find('list'));
		$this->set('newsBulletinInfos', $this->BoardCategory->find('all', array(
			'conditions' => array(
				'AND' => array(
					array('type' => 'bulletin_news'),
					array('BoardCategory.id !=' => '15')
				) ),
			'order' => array('BoardCategory.created' => 'asc'),
			'fields' => array('BoardCategory.id', 'BoardCategory.name')
		)));
		$this->set('newsBulletins', $this->BoardCategory->find('list', array(
			'conditions' => array( 'type' => 'bulletin_news' )
		)));
		// PAGENATION
		$conditions = array('Board.category_id' => $id);
		if(!empty($this->data['Setting']['cat'])) {
			$conditions['Airdoc.territory'] = $this->data['Setting']['cat'];
		}
		if(!empty($this->data['Setting']['searchWord'])) {
			$conditions['OR']['Board.body LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$conditions['OR']['Board.title LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$conditions['OR']['Airdoc.auth LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$this->set('searchWord', $this->data['Setting']['searchWord']);
		}
		if($this->Auth->user('group_id') > 2) {
			$conditions['Board.secured !='] = 1;
		}
		$this->paginate = array(
			'conditions'=>$conditions,
			'order'=>array( 'Board.created DESC' ),
			'limit' => 50
		);

		$posts = $this->paginate('Board');
//		$this->set('posts', $posts);
//		$posts = $this->Board->find('all', array(
//			'conditions'=>$conditions,
//			'order'=>array( 'Board.created DESC' )
//		));
		$this->set('conditions', $conditions);

		//COMMENT
		if(!empty($this->data['Setting']['comment'])) {
			$this->data['Comment']['board_id'] = 1;
			$this->data['Comment']['user_id'] = $this->Auth->user('id');
			$this->data['Comment']['comment'] = $this->data['Setting']['comment'];
			$this->Comment->save($this->data);
			$this->data['Setting']['comment'] ="";
		}

		//DELETE BULK DATA
		if(!empty($this->data['delete'])) {
			if ($action == 'moveto') {
				foreach( $this->data['delete'] as $key=>$value ) {
					if($value == 1) {
						$this->Board->id = $key;
						$this->Board->saveField('category_id', $this->data['Setting']['moveTo']);
					}
				}
			} else {
				foreach( $this->data['delete'] as $key=>$value ) {
					if($value == 1) {
						$files = $this->Board->read(null, $key);
						if($this->Board->delete($key)) {
							// delete files on the server
							if ( !empty($files['Upload']) ) {
								foreach($files['Upload'] as $file) {
									$oldfile = $file['path'] . $file['name'];
									unlink($oldfile);
								}
							}
						}
					}
				}
			}
			$this->redirect('/settings/bulletin_news/'.$id);
		}

		$this->set('posts', $posts);
		$this->set('board_id', $id);
		$this->set('cat', $this->data['Setting']['cat']);
		$this->set('comments', $this->Comment->find('all', array(
			'conditions' => array('board_id'=>1),
			'order' => 'Comment.created DESC',
			'recursive' => -1
		)));

		$lastUpdated = $this->Board->find('first', array(
			'conditions' => array('Board.category_id'=> array(12, 13, 14, 15, 17, 18, 19)),
			'order' => 'Board.modified DESC'
		));
		$this->set('lastUpdated', $lastUpdated['Board']['modified']);


		// Automatically Move to Archive
		foreach ($posts as $post) :
			if($post['Airdoc']['date_end_ticket'] < date("Y-m-d")){
				$this->Board->id = $post['Board']['id'];
				$this->Board->saveField('category_id', 15);
			}
		endforeach;

		// latest posts
		if(isset($this->params['requested'])) {
			return $posts;
			//return array('posts' => $posts, 'paging' => $this->params['paging']);
		} else {
			$this->set('posts', $posts);
		}
	}

	function add_airlines_code() {
		if ($this->BoardCategory->save($this->data)) {
			$this->Session->setFlash('Saved');
		} else {
			$this->Session->setFlash('Please try again.');
		}
	}

	function air_news_test($id, $action) {
		$this->set('users', $this->User->find('list'));
		$this->set('newsBulletins', $this->BoardCategory->find('list', array(
			'conditions' => array( 'type' => 'bulletin_news' )
		)));
		// PAGENATION
		$conditions = array('Board.category_id' => $id);
		if(!empty($this->data['Setting']['cat'])) {
			$conditions['Airdoc.territory'] = $this->data['Setting']['cat'];
		}
		if(!empty($this->data['Setting']['searchWord'])) {
			$conditions['OR']['Board.body LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$conditions['OR']['Board.title LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$conditions['OR']['Airdoc.auth LIKE'] = '%'.$this->data['Setting']['searchWord'].'%';
			$this->set('searchWord', $this->data['Setting']['searchWord']);
		}
		if($this->Auth->user('group_id') > 2) {
			$conditions['Board.secured !='] = 1;
		}
		$this->paginate = array(
			'conditions'=>$conditions,
			'order'=>array( 'Board.created DESC' ),
			'limit' => 50
		);

		$posts = $this->paginate('Board');
//		$this->set('posts', $posts);
//		$posts = $this->Board->find('all', array(
//			'conditions'=>$conditions,
//			'order'=>array( 'Board.created DESC' )
//		));
		$this->set('conditions', $conditions);

		//COMMENT
		if(!empty($this->data['Setting']['comment'])) {
			$this->data['Comment']['board_id'] = 1;
			$this->data['Comment']['user_id'] = $this->Auth->user('id');
			$this->data['Comment']['comment'] = $this->data['Setting']['comment'];
			$this->Comment->save($this->data);
			$this->data['Setting']['comment'] ="";
		}

		//DELETE BULK DATA
		if(!empty($this->data['delete'])) {
			if ($action == 'moveto') {
				foreach( $this->data['delete'] as $key=>$value ) {
					if($value == 1) {
						$this->Board->id = $key;
						$this->Board->saveField('category_id', $this->data['Setting']['moveTo']);
					}
				}
			} else {
				foreach( $this->data['delete'] as $key=>$value ) {
					if($value == 1) {
						$files = $this->Board->read(null, $key);
						if($this->Board->delete($key)) {
							// delete files on the server
							if ( !empty($files['Upload']) ) {
								foreach($files['Upload'] as $file) {
									$oldfile = $file['path'] . $file['name'];
									unlink($oldfile);
								}
							}
						}
					}
				}
			}
			$this->redirect('/settings/bulletin_news/'.$id);
		}

		$this->set('posts', $posts);
		$this->set('board_id', $id);
		$this->set('cat', $this->data['Setting']['cat']);
		$this->set('comments', $this->Comment->find('all', array(
			'conditions' => array('board_id'=>1),
			'order' => 'Comment.created DESC',
			'recursive' => -1
		)));

		$lastUpdated = $this->Board->find('first', array(
			'conditions' => array('Board.category_id'=> array(12, 13, 14, 15, 17, 18, 19)),
			'order' => 'Board.modified DESC'
		));
		$this->set('lastUpdated', $lastUpdated['Board']['modified']);

		// Automatically Move to Archive
		/* foreach ($posts as $post) :
			if($post['Board']['created'] <= date("Y-m-d h:i:s",strtotime ("-1 years"))){
				$this->Board->id = $post['Board']['id'];
				$this->Board->saveField('category_id', 15);
			}
		endforeach; */

		// latest posts
		if(isset($this->params['requested'])) {
			return $posts;
			//return array('posts' => $posts, 'paging' => $this->params['paging']);
		} else {
			$this->set('posts', $posts);
		}
	}

	function get_air_news_id() {
		if ( $this->Auth->user('group_id') < 2 ) {
			$news_id = 12;
		} else {
			$news_id = $this->BoardCategory->find('first', array(
				'conditions' => array('type' => 'bulletin_news')
			));
		}
		return $news_id;
	}

	function general() {
		if(!empty($this->data)) {
			if($this->Setting->saveAll($this->data)) {
				$this->Session->setFlash('Successfully updated!!');
			} else {
				$this->Session->setFlash('Please try again!!');
			}
		} else {
			$this->data = $this->Setting->read(null, 1);
		}
	}

	/* TOUR FUNCTIONS */
	function tour_edit($id=null) {
		$this->set('categories', $this->Category->find('list'));
		$this->set('wholesalers', $this->User->find('list', array(
			'conditions' => array('group_id' => 6 ),
			'order' => 'name ASC'
		)));
		$this->set('tour', $this->Tour->read(null, $id));
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid item');
			$this->redirect(array('action'=>'tour_items'));
		}
		if (!empty($this->data)) {
			if ($this->data['Tour']['type'] == 1) {
				/* Changeing Array to text data */
				$this->data['Tour']['available_date'] = implode(",", $this->data['Tour']['available_date']);
			}


			$tour = $this->Tour->read(null, $id);

			/* IF IMAGE UPDATE */
			if(!empty($this->data['Tour']['image']['name'])) {
				/* image array data */
				$target_path = "img/uploads/";
				$file_size = $this->data['Tour']['image']['size'];
				$file_temp_name = $this->data['Tour']['image']['tmp_name'];
				$file_error = $this->data['Tour']['image']['error'];
				$file_type = $this->data['Tour']['image']['type'];
				$rename = 'tour' . $id . '-' . $this->data['Tour']['image']['name'];
				$target_path = $target_path . basename( $rename);

				/* push image name into array */
				$this->Tour->set(array(
					'type' => $this->data['Tour']['type'],
					'category_id' => $this->data['Tour']['category_id'],
					'wholesaler_id' => $this->data['Tour']['wholesaler_id'],
					'active' => $this->data['Tour']['active'],
				    'is_from' => $this->data['Tour']['is_from'],
					'title' => $this->data['Tour']['title'],
					'night' => $this->data['Tour']['night'],
					'day' => $this->data['Tour']['day'],
					'price' => $this->data['Tour']['price'],
					'price_fees' => $this->data['Tour']['price_fees'],
					'price_commission' => $this->data['Tour']['price_commission'],
					'price_retail' => $this->data['Tour']['price_retail'],
					'deposit' => $this->data['Tour']['deposit'],
					'deposit_date' => $this->data['Tour']['deposit_date'],
					'payment_date' => $this->data['Tour']['payment_date'],
					'airfair' => $this->data['Tour']['airfair'],
					'minimum' => $this->data['Tour']['minimum'],
					'available_date' => $this->data['Tour']['available_date'],
					'overview' => $this->data['Tour']['overview'],
					'description' => $this->data['Tour']['description'],
					// 'description_mobile' => $this->data['Tour']['description_mobile'],
					'information' => $this->data['Tour']['information'],
					//'faq' => $this->data['Tour']['faq'],
					'address' => $this->data['Tour']['address'],
					'image' => $rename,
					'modified' => date('Y-m-d h:m:s')
				));
			} else {
				$this->Tour->set(array(
					'type' => $this->data['Tour']['type'],
					'category_id' => $this->data['Tour']['category_id'],
					'wholesaler_id' => $this->data['Tour']['wholesaler_id'],
					'active' => $this->data['Tour']['active'],
				    'is_from' => $this->data['Tour']['is_from'],
					'title' => $this->data['Tour']['title'],
					'night' => $this->data['Tour']['night'],
					'day' => $this->data['Tour']['day'],
					'price' => $this->data['Tour']['price'],
					'price_fees' => $this->data['Tour']['price_fees'],
					'price_commission' => $this->data['Tour']['price_commission'],
					'price_retail' => $this->data['Tour']['price_retail'],
					'deposit' => $this->data['Tour']['deposit'],
					'deposit_date' => $this->data['Tour']['deposit_date'],
					'payment_date' => $this->data['Tour']['payment_date'],
					'airfair' => $this->data['Tour']['airfair'],
					'minimum' => $this->data['Tour']['minimum'],
					'available_date' => $this->data['Tour']['available_date'],
					'overview' => $this->data['Tour']['overview'],
					'description' => $this->data['Tour']['description'],
					// 'description_mobile' => $this->data['Tour']['description_mobile'],
					'information' => $this->data['Tour']['information'],
					//'faq' => $this->data['Tour']['faq'],
					'address' => $this->data['Tour']['address'],
					'modified' => date('Y-m-d H:i:s')
				));
			}


			if ($this->Tour->save()) {
				/* uploading image if an image selected */
				if (!empty($this->data['Tour']['image']['name'])) {
					move_uploaded_file($file_temp_name, $target_path);
					$oldfile = "img/uploads/". $tour['Tour']['image'];
					unlink($oldfile);
				}
				$this->Session->setFlash('The item has been saved.');
				$this->redirect(array('action'=>'tour_items'));
			}else {
				$this->Session->setFlash('The tour could not be saved. Please try again.');
			}
		}

		if (empty($this->data)) {
			$this->data = $this->Tour->read(null, $id);
		}
	}

	function tour_new() {

		if(!empty($this->data)) {

			//Check tour type and set tour date
			if($this->data['Tour']['type'] == 1 ) {
				/* Changeing Array to text data */
				if($this->data['Tour']['available_date_1']) {
					 $this->data['Tour']['available_date'] = implode(",", $this->data['Tour']['available_date_1']);
				}
			}else {
				$this->data['Tour']['available_date'] = $this->data['Tour']['available_date_2'];
			}
			/* image array data */
			$lastTour = $this->Tour->find('first', array('order' => array('Tour.id DESC')));
			$lastId = $lastTour['Tour']['id'] +1;
			$target_path = "img/uploads/";
			$file_size = $this->data['Tour']['image']['size'];
			$file_temp_name = $this->data['Tour']['image']['tmp_name'];
			$file_error = $this->data['Tour']['image']['error'];
			$file_type = $this->data['Tour']['image']['type'];
			$rename = 'tour' . $lastId . '-' . $this->data['Tour']['image']['name'];
			$target_path = $target_path . basename( $rename );

			/* uploading file */
			/*
			if(move_uploaded_file($file_temp_name, $target_path)) {
			    echo "The file ".  basename( $this->data['Tour']['image']).
			    " has been uploaded";
			} else{
			    echo "There was an error uploading the file, please try again!";
			}
*/
			$this->data['Tour']['image'] = $rename;
			if ($this->Tour->save($this->data)) {
				if ($file_temp_name) {
					move_uploaded_file($file_temp_name, $target_path);
				}
				$this->Session->setFlash('Saved');
				$this->redirect(array('action' => 'index/'.$this->data['Tour']['category_id']));
			} else {
				$this->Session->setFlash('Please try again.');
			}
		}
		$this->set('categories', $this->Category->find('list', array(
			'conditions' => array(
				"NOT" => array("Category.id" => array(19, 20))
			)
		)));
		$this->set('wholesalers', $this->User->find('list', array(
			'conditions' => array('group_id' => 6 ),
			'order' => 'name ASC'
		)));
	}

	function tour_delete($id) {
		$tour = $this->Tour->read(null, $id);
		if ($this->Tour->delete($id)) {
			// delete related image
			$oldfile = "img/uploads/". $tour['Tour']['image'];
			unlink($oldfile);
            /* REMOVE ADDITIONAL IMAGES FROM SERVER */
			foreach ($tour['Upload'] as $image) {
				$oldfile = "img/uploads/".$image['name'];
				unlink($oldfile);
			}
			$this->Session->setFlash('The tour with id: ' . $id . ' has been deleted.');
			$this->redirect(array('action' => 'tour_items'));
		}
	}

	function tours_delete() {
	    //DELETE BULK DATA
		if(!empty($this->data['delete'])) {
			foreach( $this->data['delete'] as $key=>$value ) {
				if($value == 'on') {
					$files = $this->Tour->read(null, $key);
					if($this->Tour->delete($key)) {
						// delete files on the server
						if ( !empty($files['Upload']) ) {
							foreach($files['Upload'] as $file) {
								$oldfile = $file['path'] . $file['name'];
								unlink($oldfile);
							}
						}
					}
				}
			}
			$this->redirect('/settings/tour_items/');
        }
	}

	function prac($id=24){

	    $this->layout = 'admin_blank';

	    $items = $this->Reservation->find('all', array(
	        'conditions' => array('Reservation.created >=' => '2015-01-01')
	    ));
	    $this->set('items', $items);

	    $this->layout = 'admin_blank';
	    $emails = $this->Reservation->read(null, $id);
	    $this->set('tour', $tour);

	    if(!empty($this->data)) {
	        if ($this->Category->save($this->data['Category'])) {
	            echo 'done';
	        }
	    }

	}

	function tour_items() {
		/* $this->Tour->useDbConfig = 'amt'; */
        if($this->isAuthorized() == true) {
			$conditions = [];
			if (!empty($this->data['Setting']['category'])) {
	 			$conditions['category_id'] = $this->data['Setting']['category'];
	 		}
	 		if (!empty($this->data['Setting']['title'])) {
	 			$conditions['title LIKE'] = '%' . $this->data['Setting']['title'] . '%';
	 		}
	 		if (!empty($this->data['Setting']['wholesaler_id'])) {
	 		    $conditions['wholesaler_id'] = $this->data['Setting']['wholesaler_id'];
	 		}
			if($conditions){
				$this->set('tours', $this->Tour->find('all', array(
					'conditions' => $conditions,
					'order' => 'Tour.created DESC'
				)));
			} else {
				$this->set('tours', $this->Tour->find('all', array(
					'order' => 'Tour.created DESC'
				)));
			}
			$this->set('email_agencies', $this->User->find('list', array(
				'fields'=> array('User.email'),
				'conditions' => array('User.group_id =' => 3)
			)));
			$this->set('wholesalers', $this->User->find('list', array(
                'conditions' => array('group_id' => 6 ),
                'order' => 'name ASC'
			)));
		} else {
			$this->redirect('/');
		}

		/* $this->set('sid', $this->Auth->user('name')); */

		if (!empty($this->data['Tour'])) {
			if ($this->Tour->saveAll($this->data['Tour'])) {
				$this->Session->setFlash('Data saved');
				$this->redirect(array('action' => 'tour_items'));
			} else {
				$this->Session->setFlash('Failed');
			}
		}
		$this->set('categories', $this->Category->find('list'));
	}

	 function tour_duplicate($id) {
		 if ($id) {
			$record = $this->Tour->read(null, $id);

			// check permission
			if ($this->Auth->user('group_id') >= 3 ) {
				$this->Session->setFlash('You don\'t have the permission');
				$this->redirect($this->referer());
			}

			// get data
			unset($record['Tour']['id'], $record['Tour']['created'], $record['Tour']['modified']);
			foreach ($record['Tour'] as $key => $value) {
				$this->data['Tour'][$key] = $value;
			}
/* 			COPY IMAGE */
			$newfile = 'tour' . date(YmdHis) . '-' . $record['Tour']['image'];
			copy('img/uploads/' . $record['Tour']['image'], 'img/uploads/' . $newfile);
			$this->data['Tour']['image'] = $newfile;

			// copy data - make validation false to copy
			$this->Tour->create();
			if ($this->Tour->save($this->data, array('validate'=>false))) {
/* 				GET ADDITIONAL IMAGES AND SAVE */
				if (!empty($record['Upload'][0]['name'])) {
					$i = 0;
					foreach ($record['Upload'] as $num => $image) {
						foreach ($image as $key => $value) {
							$this->data['Upload'][$num][$key] = $value;
						}
						$newfile = 'tour' . date(YmdHis) . '-' . $record['Upload'][$num]['name'];
						copy('img/uploads/' . $record['Upload'][$num]['name'], 'img/uploads/' . $newfile);
						unset($this->data['Upload'][$num]['id'], $this->data['Upload'][$num]['created']);
						$this->data['Upload'][$num]['name'] = $newfile;
						$this->data['Upload'][$num]['tour_id'] = $this->Tour->getLastInsertID();
						$i++;
					}
					$this->Upload->saveAll($this->data['Upload']);
				}
				$this->Session->setFlash('Successfully duplicated.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Action failed.');
				$this->redirect($this->referer());
			}
		} else {
			$this->Session->setFlash('Invalid request.');
			$this->redirect($this->referer());
		}
	 }

	 function tour_book($id=null) {
		$this->Tour->id = $id;
		$this->set('tour', $this->Tour->read());
		$this->set('items', $this->Item->find('all', array(
			'conditions'=> array(
				'tour_id'=>$id,
				"tour_date >" => date('Y-m-d')
			),
			'order' => 'Item.tour_date ASC'
		)));
		$this->set('users', $this->User->find('list'));

		$this->paginate = array(
			'conditions' => array(
				'Comment.tour_id' => $id
			),
			'order' => array('Comment.created' => 'DESC'),
			'limit' => 2
		);

		$comments = $this->paginate('Comment');
		$this->helpers['Paginator'] = array('ajax' => 'Ajax');
		$this->set(compact('comments'));
	}

	function tour_application($tourID=null, $date=null, $revID=null) {
		$this->layout = 'admin_blank';
/* 		$tour = $this->Tour->findById($tourID); */
		$reservation = $this->Reservation->findById($revID);
		if($date) {
			$item = $this->Item->findByTourIdAndTourDate($tourID, $date);
		} else {
			$item = $this->Tour->findById($tourID);
		}
		$this->set('reservation', $reservation);
		$this->set('item', $item);
/* 		$this->set('tour', $tour); */
		$this->set('date', $date);
	}

	function tour_featured() {
		if(!empty($this->data)) {
			if($this->Tour->saveAll($this->data['Tour'])) {
				$this->Session->setFlash('The order of the featured changed.');
			} else {
				$this->Session->setFlash('Failed to change the order of the featured. Try again.');
			}
		}
		 $tours = $this->Tour->findAllByActiveAndFeatured('1', '1', array(), array('Tour.order_featured' => 'ASC'));
		 $this->set('tours', $tours);
	}

	/* USER FUNCTIONS */
	function user_list() {
		$this->set('users', $this->User->find('all'));
		$groups = $this->Group->find('list');
		$this->set('groups', $groups);
	}

	function user_new() {
		$this->set('newsBulletins', $this->BoardCategory->find('list', array(
			'conditions' => array( 'AND' => array(
					array('type' => 'bulletin_news'),
					array('id !=' => '15')
				))
		)));
		if ($this->isAuthorized() == true && $this->Auth->user('group_id') < 2) {
			if(!empty($this->data)) {
				$this->data['User']['password'] = $this->Auth->password('password');
				$this->data['User']['agree'] = 1;
				for ($i = 0; $i < sizeof($this->data['User']['newsletter_item']); $i++ ) {
					$this->data['User']['newsletter'] .= $this->data['User']['newsletter_item'][$i] . ',';
				}
				if ($this->User->save($this->data)) {
					if($this->data['Setting']['send_email'] === 1) {
						$this->Email->from    = 'HANATOUR Web App <info@ihanatour.com>';
						$this->Email->to      = $this->data['User']['email'];
						$this->Email->subject = 'Your account was created by Hana Tour Admin';
						$this->Email->sendAs = 'html';
						$content = 'The following is your account information.<br>Username: ' . $this->data['User']['username'] . '<br>Initial password: password<br>You can log in now to <a href="https://ihanatour.com">www.ihanatouor.com</a> and change your password.';
						$this->Email->send($content);
					}

				    $this->Session->setFlash('User created!!');
				    $this->redirect('/settings/user_list');
				} else {
				    $this->Session->setFlash('Oops! Somthing wrong!!');
				}
			}
			$groups = $this->Group->find('list');
			if ($this->Auth->user('group_id') > 1) {
				unset($groups[1]);
			}
			$this->set('groups', $groups);
		} else {
			$this->Session->setFlash('Invalid request');
			$this->redirect('/');
		}

	}

	function user_edit($id=null) {
		$this->set('newsBulletins', $this->BoardCategory->find('list', array(
			'conditions' => array( 'AND' => array(
					array('type' => 'bulletin_news'),
					array('id !=' => '15')
				))
		)));
		if ($this->isAuthorized() == true) {
			if(!empty($this->data)) {
				$this->User->id = $id;
				for ($i = 0; $i < sizeof($this->data['User']['newsletter_item']); $i++ ) {
					$this->data['User']['newsletter'] .= $this->data['User']['newsletter_item'][$i] . ',';
				}
				if (/* $this->User->validates(array('fieldList' => array('email'))) &&  */$this->User->save($this->data)) {
				    $this->Session->setFlash('Updated!!');
				    $this->redirect('/settings/user_list');
				} else {
				    $this->Session->setFlash('Oops! Somthing wrong!!');
				}
			} else {
				$this->data = $this->User->read(null, $id);
				$this->data['User']['newsletter_item'] = explode(',', $this->data['User']['newsletter']);
				if($this->data['User']['group_id'] == 1 && $this->Auth->user('group_id') > 1) {
					$this->Session->setFlash('Invalid request');
					$this->redirect($this->referer());
				}
			}
			$this->User->id = $id;
			$this->set('info', $this->User->read());
			$groups = $this->Group->find('list');
			if ($this->Auth->user('group_id') > 1) {
				unset($groups[1]);
			}
			$this->set('groups', $groups);
		} else {
			$this->Session->setFlash('Invalid request');
			$this->redirect('/');
		}

	}

	function user_myinfo() {
		if ($this->isAuthorized() == true) {
			if(!empty($this->data)) {
				$this->User->id = $this->Auth->user('id');
				if ($this->data['User']['password'] == $this->Auth->password($this->data['User']['password1']) ) {
					for ($i = 0; $i < sizeof($this->data['User']['newsletter_item']); $i++ ) {
						$this->data['User']['newsletter'] .= $this->data['User']['newsletter_item'][$i] . ',';
					}
					if (/* $this->User->validates(array('fieldList' => array('email'))) &&  */$this->User->save($this->data)) {
					    $this->Session->setFlash('Updated!!');
					    $this->redirect('/settings/user_myinfo');
					} else {
					    $this->Session->setFlash('Oops! Somthing wrong!!');
					}
				} else {
					$this->Session->setFlash('Oops! Did you put your password correctly? Please try again.');
				}
			} else {
				$this->data = $this->User->read(null, $this->Auth->user('id'));
				$this->data['User']['newsletter_item'] = explode(',', $this->data['User']['newsletter']);
			}
			$this->User->id = $this->Auth->user('id');
			$this->set('info', $this->User->read());
			$this->set('groups', $this->Group->find('list'));
		} else {
			$this->Session->setFlash('Invalid request');
			$this->redirect('/');
		}

	}

	function user_delete($id=null) {
		if(!empty($id) && $this->Auth->user('group_id') <= 1) {
			if($this->User->delete($id)) {
				$this->Session->setFlash('The user you selected is deleted.');
				$this->redirect('/settings/user_list');
			} else {
				$this->Session->setFlash('Please try again.');
				$this->redirect('/settings/users_list');
			}

		} else {
			$this->Session->setFlash('Invalid request.');
			$this->redirect($this->referer());
		}
	}

	function resetPass($id=null) {
		if(!empty($id) && $this->Auth->user('group_id') <= 2 ) {
			$this->User->id = $id;
			$email = $this->User->find('all', array(
				'conditions' => array(
					'User.id' => $id
				)
			));
			$email = $email[0]['User']['email'];

			$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
			$pass = array(); //remember to declare $pass as an array
    		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    		for ($i = 0; $i < 8; $i++) {
        		$n = rand(0, $alphaLength);
        		$pass[] = $alphabet[$n];
    		}
			$pass = implode($pass);

			if($this->User->saveField('password', $this->Auth->password($pass))) {
				$this->Session->setFlash('Password is reset as "'.$pass.'".');

				$this->Email->to = $email;
				$this->Email->subject = 'Your password has been changed.';
				$this->Email->replyTo = 'info@ihanatour.com';
				$this->Email->from = 'info@ihanatour.com';
				$this->Email->template = 'emailPassword';
				$this->Email->sendAs = 'html';

				$this->set('id', $id);
				$this->set('pass', $pass);

				if( $this->Email->send() ) {
					$this->redirect($this->referer());
				} else {
					$this->redirect($this->referer());
				}

			} else {
				$this->Session->setFlash('Please try again.');
				$this->redirect($this->referer());
			}

		} else {
			$this->Session->setFlash('Invalid request.');
			$this->redirect($this->referer());
		}
	}

	function register_supplier() {
		if(!empty($this->data)) {
			$this->data['User']['password'] = $this->Auth->password('password');
			$this->data['User']['agree'] = '3';
			$this->User->create();
		    if ($this->User->save($this->data)) {
		    	$this->Session->setFlash('A supplier created!!');
		    	$this->redirect('/users');
		    } else {
		    	$this->Session->setFlash('Oops! Please try again!!');
		    }
		}
		$this->set('groups', $this->Group->find('list'));
	}

	/* RESERVATION FUNCTIONS */
	function reservations() {
		$this->set('tours', $this->Tour->find('list'));
		$this->set('toursCat', $this->Tour->find('list', array(
			'fields'=> array('id', 'category_id')
		)));
		$this->set('categories', $this->Category->find('list', array(
			'fields' => array('id', 'code')
		)));
		$this->set('wholesalers', $this->User->find('list', array(
		    'conditions' => array('group_id' => 6),
		    'order' => 'name ASC'
		)));
		$this->set('users', $this->User->find('list'));
		$this->set('comments', $this->Comment->find('all', array(
			'conditions' => array('board_id'=>0),
			'order' => 'Comment.created DESC',
			'recursive' => -1
		)));

		// conditions set up
		if( !empty($this->passedArgs[0])) {
			$this->data['Reservation']['date1'] = $this->passedArgs[0];
			$this->data['Reservation']['date2'] = $this->passedArgs[0];
		}
		if(empty($this->data)) {
			$this->data['Reservation']['date1'] = date('Y-m-d', strtotime("-2 month"));
			$this->data['Reservation']['date2'] = date('Y-m-d', strtotime("+1 year"));
		}
		// conditions set up
		$conditions = array(
			'AND'=>array(
	    		array('Item.tour_date >=' => $this->data['Reservation']['date1']),
	    		array('Item.tour_date <=' => $this->data['Reservation']['date2'])
	    	)
	    );
	    if( empty($this->data['Reservation']['status']) || $this->data['Reservation']['status'] == 'All') {
	    	$conditions['AND'][] = array('Reservation.status !=' => 'Canceled');
	    } else {
	    	$conditions['AND'][] = array('Reservation.status =' => $this->data['Reservation']['status']);
	    }
	    if(!empty($this->data['Setting']['tour_name'])) {
		    $conditions['AND'][] = array('Reservation.tour_name LIKE' => '%'. $this->data['Setting']['tour_name'] .'%');
	    }
	    if(!empty($this->data['Setting']['wholesaler_id'])) {
	        $conditions['AND'][] = array('Reservation.wholesaler_id =' => $this->data['Setting']['wholesaler_id']);
	    }
	    //print_r($conditions);


		if ($this->Auth->user('group_id') <= 2) {
/*
			$this->set('reservations', $this->Reservation->find('all', array(
				'conditions'=>$conditions,
				'order'=>array(
				'Reservation.created DESC'
				)
			)));
*/
		} else {
			array_push($conditions, array(
				'OR' => array(
					array('Reservation.user_id'=>$this->Auth->user('id')),
					array('Reservation.wholesaler_id'=>$this->Auth->user('id'))
				)
			));
		}

		$this->paginate = array(
			'conditions'=>$conditions,
		    'order'=>array( 'Reservation.created DESC' ),
			'limit' => 50
		);

		$reservations = $this->paginate('Reservation');
		$this->helpers['Paginator'] = array('ajax' => 'Ajax');
		$this->set(compact('reservations'));

		//COMMENT
		if(!empty($this->data['Setting']['comment'])) {
			$this->data['Comment']['board_id'] = 0;
			$this->data['Comment']['user_id'] = $this->Auth->user('id');
			$this->data['Comment']['comment'] = $this->data['Setting']['comment'];
			$this->Comment->save($this->data);
			$this->redirect('/settings/reservations');
		}

	}

	function reservations_calendar() {
		if( !empty($this->passedArgs[0]) && !empty($this->passedArgs[1])) {
			$this->data['Reservation']['year']['year'] = $this->passedArgs[0];
			$this->data['Reservation']['month']['month'] = $this->passedArgs[1];
		}
		if (empty($this->data)) {
			$this->data['Reservation']['year']['year'] = date('Y');
			$this->data['Reservation']['month']['month'] = date('m');
		}
		if ( $this->Auth->user('group_id') > 2 ) {
			$conditions = array(
				'OR' => array(
					array('user_id' => $this->Auth->user('id')),
					array('wholesaler_id' => $this->Auth->user('id'))
				),
				'Item.tour_date LIKE' => '%'. $this->data['Reservation']['year']['year']. '-' . $this->data['Reservation']['month']['month']. '%',
				'Reservation.status !=' => 'Canceled'
			);
		} else {
			$conditions = array(
				'Item.tour_date LIKE' => '%'. $this->data['Reservation']['year']['year']. '-' . $this->data['Reservation']['month']['month']. '%',
				'Reservation.status !=' => 'Canceled'
			);
		}

		//COMMENT
		if(!empty($this->data['Setting']['comment'])) {
			$this->data['Comment']['board_id'] = 0;
			$this->data['Comment']['user_id'] = $this->Auth->user('id');
			$this->data['Comment']['comment'] = $this->data['Setting']['comment'];
			$this->Comment->save($this->data);
			$this->redirect('/settings/reservations_calendar');
		}

		$this->set('categories', $this->Category->find('list', array(
			'fields' => array('id', 'code')
		)));
		$this->set('tours', $this->Tour->find('list', array(
			'fields'=> array('id', 'category_id')
		)));
		$this->set('wholesalers', $this->User->find('list', array(
			'conditions' => array('group_id' => 6),
			'order' => 'name ASC'
		)));
		$this->set('reservations', $this->Reservation->find('all', array(
			'conditions' => $conditions,
			'order' => 'Reservation.status ASC',
			'recursive' => 2
		)));
		$this->set('users', $this->User->find('list'));
		$this->set('comments', $this->Comment->find('all', array(
			'conditions' => array('board_id'=>0),
			'order' => 'Comment.created DESC',
			'recursive' => -1
		)));
	}

	function reservations_view($id=null) {
		$reservation = $this->Reservation->read(null, $id);
		$this->set('tours', $this->Tour->find('list'));
		$this->set('tour', $this->Tour->read(null, $reservation['Item']['tour_id']));
		$this->set('categories', $this->Category->find('list', array(
			'fields' => array('id', 'code')
		)));
		$this->set('users', $this->User->find('list'));
		$this->set('wholesaler', $this->User->read(null, $reservation['Reservation']['wholesaler_id']));

		// attention reset
		if($this->Auth->user('group_id') <= 2 && $reservation['Reservation']['attention'] > 1) {
			$this->Reservation->id = $id;
			$this->Reservation->saveField('attention', '');
		}

		// write Comment
		if(!empty($this->data)) {
			// EMAIL MANIFEST
			if ($this->data['Reservation']['recipient']) {
				$emails = explode(',', $this->data['Reservation']['recipient']);
				foreach ($emails as $email) {
					$this->manifestMail($email, $id, $this->data['Reservation']['subject']);
				}
				$this->redirect('/settings/reservations_view/'.$id);
			}

			// make attention for Customers
			if ($this->Auth->user('group_id') > 2) {
				$this->Reservation->id = $id;
				$this->Reservation->saveField('attention', $this->Auth->user('group_id'));
			}
			$this->writeComment($id, $this->data['Comment']['comment'], $this->data['Comment']['user_id'], $this->data['Comment']['reservation_num'], $this->data['Comment']['wholesaler_id']);
		}

		// check up this reservation's owner
		if($this->Auth->user('group_id') > 2) {
			if($reservation['Reservation']['user_id'] == $this->Auth->user('id') || $reservation['Reservation']['wholesaler_id'] == $this->Auth->user('id')) {
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
				'conditions'=> array(
			    	'reservation_id' => $id
			    ),
			    'order' => 'created DESC'
			)));
		}

	}

	function reservations_edit($id=null) {
		$this->set('tours', $this->Tour->find('list'));
		$tours = $this->Tour->find('list');
		$reservation = $this->Reservation->read(null, $id);
		$this->set('tour', $this->Tour->read(null, $reservation['Item']['tour_id']));
		$tour = $this->Tour->read(null, $reservation['Item']['tour_id']);
		$this->set('suppliers', $this->User->find('all', array(
			'conditions' => array(
				'group_id' => 6
				)
			)
		));
		$this->set('hotels', $this->Tour->find('list', array(
			'conditions' => array(
				'category_id' => array(15, 16, 17, 19)
			)
		)));

		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid item');
			$this->redirect(array('action'=>'index'));
		}

		if ($this->Auth->user('group_id') > 2  && !in_array($reservation['Reservation']['status'], $this->editableStatus) ) {
			$this->Session->setFlash('You don\'t have the permission');
			$this->redirect(array('action'=>'index'));
		}

		if (empty($this->data)) {
			$this->data = $reservation;
		} else {

			//intial data back up
			$this->data['Reservation']['modified_by'] = $this->Auth->user('id');


			// collect pax names
            if (in_array($tour['Tour']['category_id'], $this->hotelCategory)) {
            	// When Hotel changed
            	$this->data['Reservation']['tour_name'] = $tours[$this->data['Item']['tour_id']];
            	// define pax num
            	$paxNum = $this->data['Reservation']['room'];
            	$this->data['Reservation']['adult_num'] = $paxNum;
            	// define Room type
				if ( $this->data['Reservation']['room'] > 0 ) {
					for ($i= 0; $i< $this->data['Reservation']['room'] ; $i++) {
						$this->data['Reservation']['room_types'] .= $this->data['Reservation']['room_type'.$i] . '-' .$this->data['Reservation']['lname'.$i] . '/' . $this->data['Reservation']['fname'.$i] . ',';
					}
				}
				// total price for Hotel reservation
				$totalDay = (strtotime($this->data['Reservation']['date_checkout']) - strtotime($this->data['Item']['tour_date'])) / (60 * 60 * 24);
				$this->data['Reservation']['total_commission'] = $tour['Tour']['price_commission'] * $totalDay * $paxNum;
				$this->data['Reservation']['total_fees'] = $tour['Tour']['price_fees'] * $totalDay * $paxNum;
				if (empty($this->data['Reservation']['totalPrice_alternative'])) {
					if (empty($tour['Tour']['price_retail'])) {
						$this->data['Reservation']['total'] = ($tour['Tour']['price'] + $tour['Tour']['price_fees'] + $tour['Tour']['price_commission']) * $totalDay * $paxNum;
					} else {
						$this->data['Reservation']['total'] = $tour['Tour']['price_retail'] * $totalDay * $paxNum;
					}
				} else {
					$this->data['Reservation']['total'] = $this->data['Reservation']['totalPrice_alternative'] + $this->data['Reservation']['total_fees'] + $this->data['Reservation']['total_commission'];
				}


            } else {
            	//define pax num
            	$paxNum = $this->data['Reservation']['adult_num'] /* + $this->data['Reservation']['child_num'] */;
            	$this->data['Reservation']['total_commission'] = $tour['Tour']['price_commission'] * $paxNum;

            	foreach ($this->data['Reservation']['lnameA'] as $key => $value) {

            	  $this->data['Profile']['id'] = $this->data['Reservation']['profileIDA'][$key];
                  $this->data['Profile']['lname'] = $this->data['Reservation']['lnameA'][$key];
                  $this->data['Profile']['fname'] = $this->data['Reservation']['fnameA'][$key];
                  $this->data['Profile']['dob'] = $this->data['Reservation']['dobA'][$key];
                  $this->data['Profile']['gender'] = $this->data['Reservation']['genderA'][$key];
                  $this->data['Profile']['nation'] = $this->data['Reservation']['nationA'][$key];
                  $this->data['Profile']['passport'] = $this->data['Reservation']['passportA'][$key];
                  $this->data['Profile']['exp'] = $this->data['Reservation']['expA'][$key];
                  $this->data['Profile']['home_address1'] = $this->data['Reservation']['addressA'][$key];
                  $this->data['Profile']['home_city'] = $this->data['Reservation']['cityA'][$key];
                  $this->data['Profile']['home_state'] = $this->data['Reservation']['stateA'][$key];
                  $this->data['Profile']['home_zip'] = $this->data['Reservation']['zipA'][$key];
                  $this->data['Profile']['home_phone'] = $this->data['Reservation']['homePhoneA'][$key];
                  $this->data['Profile']['work_phone'] = $this->data['Reservation']['workPhoneA'][$key];
                  $this->data['Profile']['cell_phone'] = $this->data['Reservation']['cellPhoneA'][$key];
                  $this->data['Profile']['home_email'] = $this->data['Reservation']['emailA'][$key];

                  if( !$this->data['Reservation']['profileIDA'][$key] ) {
                  	$this->Profile->create();
                  	$this->Profile->save($this->data);
                  	$this->data['Profile']['Profile'][] = $this->Profile->id;
                  } else {
                  	$this->Profile->save($this->data);
                  	$this->data['Profile']['Profile'][] = $this->data['Reservation']['profileIDA'][$key];
                  }
            	}
            	// total price for Tour reservation
            	$this->data['Reservation']['total_commission'] = $tour['Tour']['price_commission'] * $paxNum;
            	$this->data['Reservation']['total_fees'] = $tour['Tour']['price_fees'] * $paxNum;
            	if (empty($this->data['Reservation']['totalPrice_alternative'])) {
            		if (empty($tour['Tour']['price_retail'])) {
            			$this->data['Reservation']['total'] = ($tour['Tour']['price'] + $tour['Tour']['price_fees'] + $tour['Tour']['price_commission']) * $paxNum;
            		} else {
	            		$this->data['Reservation']['total'] = $tour['Tour']['price_retail'] * $paxNum;
            		}
            	} else {
            		$this->data['Reservation']['total'] = $this->data['Reservation']['totalPrice_alternative'] + $this->data['Reservation']['total_fees'] + $this->data['Reservation']['total_commission'];
            	}

            }

			$prevItemId = $this->data['Item']['id'];
			$prevItemPaxnum = $reservation['Item']['pax_num'];
			$prevItemTourDate = $reservation['Item']['tour_date'];

			$finalPax = $prevItemPaxnum - $paxNum;
    		if ($finalPax >= $tour['Tour']['minimum']) {
    			$finalPaxTxt = '확정';
    		} else {
    			$finalPaxTxt = '대기';
    		}

   			//difference check for logs
			$logTxt = '';

	        if ($reservation['Reservation']['room_types'] != $this->data['Reservation']['room_types']) {
	        	$logTxt .= '방 정보가 <b>' . $reservation['Reservation']['room_types'] . '</b>에서 <b>' . $this->data['Reservation']['room_types'] . '</b>으로 수정되었습니다.<br />';
	        }
	        if ($reservation['Reservation']['flight'] != $this->data['Reservation']['flight']) {
	        	$logTxt .= '비행 정보가 <b>' . $reservation['Reservation']['flight'] . '</b>에서 <b>' . $this->data['Reservation']['flight'] . '</b>으로 수정되었습니다.<br />';
	        }
	        if($reservation['Item']['tour_date'] != $this->data['Item']['tour_date']) {
				$logTxt .= '날짜를 <b>' .$reservation['Item']['tour_date'] . '</b>에서 <b>'. $this->data['Item']['tour_date'] .'</b>으로 수정하셨습니다.<br />';
			}
			if($reservation['Reservation']['remark'] != $this->data['Reservation']['remark']) {
				$logTxt .= 'Remark를 <b>' .$reservation['Reservation']['remark'] . '</b>에서 <b>'. $this->data['Reservation']['remark'] .'</b>으로 수정하셨습니다.<br />';
			}
			if ($reservation['Reservation']['total'] != $this->data['Reservation']['total']) {
				$oldTotal = $reservation['Reservation']['total'] - $reservation['Reservation']['total_commission'];
				$newTotal = $this->data['Reservation']['total'] - $reservation['Reservation']['total_commission'];
				$logTxt .= 'Total net price가 <b>$' . $oldTotal . '</b>에서 <b>$'. $newTotal .'</b>으로 변경되었습니다.<br />';
			}

			// make attention if agency modify reservation
			if ($this->Auth->user('group_id') > 2 /* && !empty($logTxt) */) {
				$this->data['Reservation']['attention'] = $this->Auth->user('group_id') ;
			}

/* 			PAYMENTS STUFF */
			if (empty($this->data['Payment'][count($reservation['Payment'])]['amount']) || $this->data['Payment'][count($reservation['Payment'])]['amount'] == 0) {
				unset($this->data['Payment']);
			} else {
				$payments = 0;
				foreach($reservation['Payment'] as $payment) :
					$payments = $payments + $payment['amount'];
				endforeach;
				$totalData = $payments + $this->data['Payment'][count($reservation['Payment'])]['amount'];
				if($this->data['Reservation']['total'] <= $totalData) {
					$this->data['Reservation']['status'] = 'Paid';
				}
				$this->data['Payment'][count($reservation['Payment'])]['remark'] .= ' by '.$this->Auth->user('name');
			}
			// saving Data
			if ($reservation['Item']['tour_date'] == $this->data['Item']['tour_date'] && $reservation['Item']['tour_id'] == $this->data['Item']['tour_id']) {
				$paxNum = $reservation['Reservation']['adult_num'] - $paxNum;
				$this->data['Item']['pax_num'] = $this->data['Item']['pax_num'] - $paxNum;
				// Item status check up and update
				if ($tour['Tour']['minimum'] <= $this->data['Item']['pax_num']) {
					$this->data['Item']['status'] = '확정';
				}
				if ($this->Reservation->saveAll($this->data)) {
					// write log
					$this->Log->set(array(
					    'reservation_id'=> $this->data['Reservation']['id'],
					    'content'=> $logTxt,
					    'user_id'=>$this->Auth->user('id')
					));
					if(!empty($logTxt)) {
							$this->Log->save();
						}
					$this->Session->setFlash('Successfully updated.');
					if ( strpos($this->referer(),'settings/reservations_edit') ) {
						$this->redirect('/settings/reservations_view/' . $this->data['Reservation']['id']);
					} else {
						$this->redirect($this->referer());
					}
				} else {
					$this->Session->setFlash('Please try again.');
				}

			} else {

				// Initial checking if Item exists
				$checkItem = $this->Item->find('first', array(
				    'conditions' => array(
				    	'Item.tour_id' => $this->data['Item']['tour_id'],
				    	'Item.tour_date' => $this->data['Item']['tour_date']
				)));


				// When Item exists
				if($checkItem) {
					$this->data['Reservation']['item_id'] = $checkItem['Item']['id'];
					$this->data['Item']['id'] = $checkItem['Item']['id'];
					$this->data['Item']['pax_num'] = $checkItem['Item']['pax_num'] + $paxNum;
					// Item status check up
					if ($this->data['Item']['pax_num'] >= $tour['Tour']['minimum']) {
						$this->data['Item']['status'] = '확정';
					} else {
						$this->data['Item']['status'] = '대기';
					}


					// updating Item and create new record for Reservation
					if ($this->Reservation->saveAll($this->data)) {
						//updating prev. item pax num
						$this->Item->id = $prevItemId;
						$this->Item->set(array(
						    'pax_num' => $finalPax,
						    'tour_date' => $prevItemTourDate,
						    'status' => $finalPaxTxt,
						    'modified' => date('Y-m-d h:m:s')
						));
						$this->Item->save();
					    // write log
						$this->Log->set(array(
							'reservation_id'=> $this->data['Reservation']['id'],
							'content'=> $logTxt,
							'user_id'=>$this->Auth->user('id')
						));
						if(!empty($logTxt)) {
							$this->Log->save();
						}

					    $this->Session->setFlash('Successfully updated.');
						if ( strpos($this->referer(),'settings/reservations_edit') ) {
							$this->redirect('/settings/reservations_view/' . $this->data['Reservation']['id']);
						} else {
							$this->redirect($this->referer());
						}
					} else {
						$this->Session->setFlash('Please try again!!');
					}
				} else {
				// When an item doesn't exist
					$this->data['Item']['id'] = '';
					$this->data['Item']['pax_num'] = $paxNum;
					if ($paxNum >= $tour['Tour']['minimum']) {
    					$this->data['Item']['status'] = '확정';
    				} else {
    					$this->data['Item']['status'] = '대기';
    				}


					if ($this->Reservation->saveAll($this->data)) {
						// updating the prev. item pax num
						$this->Item->id = $prevItemId;
						$this->Item->set(array(
						    'pax_num' => $finalPax,
						    'tour_date'=>$prevItemTourDate,
						    'modified' => date('Y-m-d h:m:s')
						));
						$this->Item->save();
						// write log
						$this->Log->set(array(
							'reservation_id'=> $this->data['Reservation']['id'],
							'content'=> $logTxt,
							'user_id'=>$this->Auth->user('id')
						));
						if(!empty($logTxt)) {
							$this->Log->save();
						}

						$this->Session->setFlash('Successfully updated.');
						if ( strpos($this->referer(),'settings/reservations_edit') ) {
							$this->redirect('/settings/reservations_view/' . $this->data['Reservation']['id']);
						} else {
							$this->redirect($this->referer());
						}
					} else {
						$this->Session->setFlash('Please try again.');
					}
				}
			}
		}
	}

	function report() {
		$this->set('tours', $this->Tour->find('list'));
		$this->set('toursCat', $this->Tour->find('list', array(
			'fields'=> array('id', 'category_id')
		)));
		$this->set('categories', $this->Category->find('list', array(
			'fields' => array('id', 'code')
		)));
		$this->set('users', $this->User->find('list'));

		// conditions set up
		if( !empty($this->passedArgs[0])) {
			$this->data['Reservation']['date1'] = $this->passedArgs[0];
			$this->data['Reservation']['date2'] = $this->passedArgs[0];
		}
		if(empty($this->data)) {
			$this->data['Reservation']['date1'] = date('Y-m-01');
			$this->data['Reservation']['date2'] = date('Y-m-t');
		}
		// conditions set up
		$conditions = array(
			'AND'=>array(
	    		array('Item.tour_date >=' => $this->data['Reservation']['date1']),
	    		array('Item.tour_date <=' => $this->data['Reservation']['date2'])
	    	)
	    );
		$items = $this->Item->find('all', array(
			'conditions' => $conditions,
			'order' => 'Item.tour_date ASC'
		));
		$this->set('items', $items);
	}

	function cbmc_notice() {
		$this->layout = 'booking';
		$this->loadModel('CbmcText');
		$text = $this->CbmcText->find('first');
		$this->set('text', $text);

	}

	function cbmc_notice_edit() {
		$this->layout = 'admin_blank';
		$this->loadModel('CbmcText');
		if (!empty($this->data)) {
			$this->CbmcText->id = 1;
			$this->CbmcText->save($this->data);
			echo "<script>alert('Successfully updated.'); location.href = 'https://ihanatour.com/settings/cbmc_notice';</script>";
		} else {
			echo "<script>alert('Please try again.'); location.href = 'https://ihanatour.com/settings/cbmc_notice';</script>";
			exit();
		}
	}

	function cbmc_list() {
		$this->loadModel('Cbmc');
		$this->layout = 'admin_blank';
		if ($this->data['Expense']) {
			if($this->Expense->save($this->data)) {
				$this->Session->setFlash('Successfully saved.');
				$this->redirect($this->referer() . '#expenses');
			} else {
				$this->Session->setFlash('Please try again.');
			}
		} else if ($this->data['Item']) {
			if($this->Item->save($this->data, array('validate' => false))) {
				$this->Session->setFlash('Successfully updated.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Something wrong. Please try again.');
			}
		}

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

		$this->set('items', $items);
		$this->set('itemInfo', $itemInfo);
		$this->set('itemInfoExpenses', $itemInfoExpenses);
		$this->set('comments', $comments);
		$this->set('itemfiles',$itemfiles);
	}

	function pax_list($id) {
		$this->layout = 'admin_blank';
		if ($this->data['Expense']) {
			if($this->Expense->save($this->data)) {
				$this->Session->setFlash('Successfully saved.');
				$this->redirect($this->referer() . '#expenses');
			} else {
				$this->Session->setFlash('Something wrong. Please try again.');
			}
		} else if ($this->data['Item']) {
			if($this->Item->save($this->data, array('validate' => false))) {
				$this->Session->setFlash('Successfully updated.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Something wrong. Please try again.');
			}
		}

		$items = $this->Reservation->find('all', array(
			'conditions' => array('item_id' => $id),
 			'order' => 'Reservation.created ASC'
/* 			'recursive' => 2 */
		));
		$itemInfo = $this->Item->findById($id);
		$itemInfoExpenses = $this->Expense->find('all', array(
			'conditions' => array('item_id' => $id),
			'order' => 'Expense.order ASC'
		));
		$comments = $this->Comment->find('all', array(
		    'conditions' => array('Comment.item_id' => $id),
		    'order' => 'Comment.created DESC'
		));
		$itemfiles = $this->Item->read(null, $id);

		$this->set('items', $items);
		$this->set('itemInfo', $itemInfo);
		$this->set('itemInfoExpenses', $itemInfoExpenses);
		$this->set('comments', $comments);
		$this->set('itemfiles',$itemfiles);
	}

	function pax_list2($id) {
		$this->layout = 'admin_blank';
		if ($this->data['Expense']) {
			if($this->Expense->save($this->data)) {
				$this->Session->setFlash('Successfully saved.');
				$this->redirect($this->referer() . '#expenses');
			} else {
				$this->Session->setFlash('Something wrong. Please try again.');
			}
		} else if ($this->data['Item']) {
			if($this->Item->save($this->data, array('validate' => false))) {
				$this->Session->setFlash('Successfully updated.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Something wrong. Please try again.');
			}
		}

		$items = $this->Reservation->find('all', array(
			'conditions' => array('item_id' => $id),
 			'order' => 'Reservation.created ASC'
/* 			'recursive' => 2 */
		));
		$itemInfo = $this->Item->findById($id);
		$itemInfoExpenses = $this->Expense->find('all', array(
			'conditions' => array('item_id' => $id),
			'order' => 'Expense.order ASC'
		));
		$comments = $this->Comment->find('all', array(
		    'conditions' => array('Comment.item_id' => $id),
		    'order' => 'Comment.created DESC'
		));
		$itemfiles = $this->Item->read(null, $id);

		$this->set('items', $items);
		$this->set('itemInfo', $itemInfo);
		$this->set('itemInfoExpenses', $itemInfoExpenses);
		$this->set('comments', $comments);
		$this->set('itemfiles',$itemfiles);
	}

	function pax_list_order($id) {
		$this->layout = 'admin_blank';

		$profiles_ids = $this->Reservation->find('all', array(
				'conditions' => array('Reservation.item_id' => $id, 'Reservation.status !=' => 'Canceled'),
				'fields'=>array('Reservation.id')
		));
		for($i=0; $i<count($profiles_ids); $i++){
			for($j=0; $j<count($profiles_ids[$i]['Profile']); $j++){
				$ids[] =+ $profiles_ids[$i]['Profile'][$j]['id'];
			}
		}
		$profiles = $this->Profile->find('all', array(
			'conditions' => array(
					'Profile.id' => $ids
			),
			'order' => 'Profile.order ASC'
		));

		$items = $this->Reservation->find('all', array(
				'conditions' => array('Reservation.item_id' => $id, 'Reservation.status !=' => 'Canceled')
		));
		$itemInfo = $this->Item->findById($id);

		$this->set('profiles', $profiles);
		$this->set('items', $items);
		$this->set('itemInfo', $itemInfo);
	}

	function pax_list_order2($id) {
		$this->layout = 'admin_blank';

		$profiles_ids = $this->Reservation->find('all', array(
			'conditions' => array('Reservation.item_id' => $id, 'Reservation.status !=' => 'Canceled'),
			'fields'=>array('Reservation.id')
		));
		for($i=0; $i<count($profiles_ids); $i++){
			for($j=0; $j<count($profiles_ids[$i]['Profile']); $j++){
				$ids[] =+ $profiles_ids[$i]['Profile'][$j]['id'];
			}
		}
		$profiles = $this->Profile->find('all', array(
			'conditions' => array(
				'Profile.id' => $ids
			),
			'order' => 'Profile.order ASC'
		));

		$items = $this->Reservation->find('all', array(
			'conditions' => array('Reservation.item_id' => $id, 'Reservation.status !=' => 'Canceled')
		));
		$itemInfo = $this->Item->findById($id);

		$this->set('profiles', $profiles);
		$this->set('items', $items);
		$this->set('itemInfo', $itemInfo);
	}

	function sendPaxOrder($id){
		if (!empty($this->data['Setting']['recipient'])) {
			$items = $this->Reservation->find('all', array(
					'conditions' => array('item_id' => $id, 'Reservation.status !=' => 'Canceled'),
					'order' => 'Reservation.order ASC'
			));
			$profiles_ids = $this->Reservation->find('all', array(
					'conditions' => array('Reservation.item_id' => $id, 'Reservation.status !=' => 'Canceled'),
					'fields'=>array('Reservation.id')
			));
			for($i=0; $i<count($profiles_ids); $i++){
				for($j=0; $j<count($profiles_ids[$i]['Profile']); $j++){
					$ids[] =+ $profiles_ids[$i]['Profile'][$j]['id'];
				}
			}
			$profiles = $this->Profile->find('all', array(
					'conditions' => array(
							'Profile.id' => $ids
					),
					'order' => 'Profile.order ASC'
			));
			$comment = $this->data['Setting']['comment'];
			$user = $this->User->read(null, $this->Auth->user('id'));
			$recipients = preg_split("/[,]+/", $this->data['Setting']['recipient'].', '.$user['User']['email']);
			$this->Email->to = $recipients;
			$this->Email->subject = $this->data['Setting']['subject'];
			$this->Email->replyTo = $user['User']['email'];
			$this->Email->from = $user['User']['email'];
			$this->Email->template = 'emailRoomates'; // note no '.ctp'
			//Send as 'html', 'text' or 'both' (default is 'text')
			$this->Email->sendAs = 'html'; // because we like to send pretty mail
			//Set view variables as normal
			$this->set('user', $user);
			$this->set('items', $items);
			$this->set('profiles', $profiles);
			$this->set('comment', $comment);

			//Do not pass any args to send()
			if( $this->Email->send() ) {
				$this->Session->setFlash('Email sent');
				$this->redirect('/settings/pax_list_order/'.$id);
			} else {
				$this->Session->setFlash('Email fail');
				$this->redirect('/settings/pax_list_order/'.$id);
			}
		} else {
			$this->Session->setFlash('You did not enter a recipient.');
			$this->redirect('/settings/pax_list_order/'.$id);
		}
	}

	function expense_order($id) {
		$this->layout = 'admin_blank';
		$item_expenses = $this->Expense->find('all', array(
				'conditions' => array(
						'item_id' => $id
				),
				'order' => 'Expense.order ASC'
		));
		$this->set('item_expenses', $item_expenses);
	}

	function expense_delete($id) {
		if ($this->isAuthorized() == true) {
			if ($this->Expense->delete($id)) {
				$this->Session->setFlash('The expense item has been deleted.');
				if ($this->referer() != '/') {
				$this->redirect($this->referer() . '#expenses');
				} else {
				$this->redirect(array('action' => 'index'));
				}
			}
		} else {
			$this->Session->setFlash('You don\'t have a permission for your request.');
			$this->redirect($this->referer());
		}
	}

	function voucher($id=null) {
		$reservation = $this->Reservation->read(null, $id);
		$this->set('tours', $this->Tour->find('list'));
		$this->set('tour', $this->Tour->read(null, $reservation['Item']['tour_id']));
		$this->set('categories', $this->Category->find('list', array(
			'fields' => array('id', 'code')
		)));

		// check up this reservation's owner
		if($this->Auth->user('group_id') > 2) {
			if($reservation['Reservation']['user_id'] == $this->Auth->user('id')) {
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

	function changeStatus() {
		if($this->data) {
			// check out current status of the reservation
			$reservation = $this->Reservation->read(null, $this->data['Reservation']['id']);
			$item = $this->Item->read(null, $reservation['Reservation']['item_id']);
			$currentStatus = $reservation['Reservation']['status'];
			$finalPax = $item['Item']['pax_num'] - $reservation['Reservation']['adult_num'] /* - $this->data['Reservation']['child_num'] */;

			if($this->Reservation->save($this->data, array('validate' => false))) {
				// adjust Item toal pax if it is cancelled
            	if($this->data['Reservation']['status'] == 'Canceled') {
            		//updating prev. item pax num
					$this->Item->id = $reservation['Reservation']['item_id'];
					$finalPax = $item['Item']['pax_num'] - $reservation['Reservation']['adult_num'] /* - $this->data['Reservation']['child_num'] */;
					if ($finalPax >= $item['Tour']['minimum']) {
						$finalPaxTxt = '확정';
					} else {
						$finalPaxTxt = '대기';
					}

					$this->Item->set(array(
					    'pax_num' => $finalPax,
					    'status' => $finalPaxTxt,
					    'modified' => date('Y-m-d h:m:s')
					));
					$this->Item->save();
            	} else {
            		if($reservation['Reservation']['status'] == 'Canceled') {
            			//updating prev. item pax num
						$this->Item->id = $reservation['Reservation']['item_id'];
						$finalPax = $item['Item']['pax_num'] + $reservation['Reservation']['adult_num'] /* + $this->data['Reservation']['child_num'] */;
						if ($finalPax >= $item['Tour']['minimum']) {
							$finalPaxTxt = '확정';
						} else {
							$finalPaxTxt = '대기';
						}

						$this->Item->set(array(
						    'pax_num' => $finalPax,
						    'status' => $finalPaxTxt,
						    'modified' => date('Y-m-d h:m:s')
						));
						$this->Item->save();
            		}
            	}

				// save comment if there is comment
				if($this->data['Comment']['comment'] != '') {
					$this->Comment->save($this->data);
				}

				// email notification
				$this->statusNotiMail($this->data['Reservation']['id'], $this->data['Comment']['comment']);

				// write log
				$logTxt = '예약 상태를 <b>' . $this->data['Reservation']['status'] . '</b>으로 변경하였습니다.' ;
				$this->Log->set(array(
					'reservation_id'=> $this->data['Reservation']['id'],
					'content'=> $logTxt,
					'user_id'=>$this->Auth->user('id')
				));
				$this->Log->save();
				$this->Session->setFlash('Successfully updated.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Please try again!');
				$this->redirect($this->referer());
			}
		} else {
			$this->redirect($this->referer());
		}
	}
	function writeComment($reservation_id, $comment, $user_id, $reservation_number, $wholesaler_id) {
		$this->Comment->set(array(
					    'user_id' => $user_id,
					    'comment' => $comment,
					    'reservation_id' => $reservation_id,
					    'wholesaler_id' => $wholesaler_id
					));
		if ($this->Comment->save()) {
			if(!empty($wholesaler_id)) {$toGroupType = 'supplier';} else {$toGroupType = 'agency';}
			$this->commentNotiMail($user_id, $reservation_id, $reservation_number, $comment, $toGroupType);
			$this->Session->setFlash('Saved!');
			$this->redirect('/settings/reservations_view/' . $reservation_id);

		} else {
			$this->Session->setFlash('Failed!');
		}
	}

	function statusNotiMail($reservation_id, $comment) {
	    $Reservation = $this->Reservation->read(null, $reservation_id);
	    $Owner = $this->User->read(null, $Reservation['Reservation']['user_id']);

	    //for agency
	    $this->Email->to = $Owner['User']['email'];

	    $this->Email->subject = 'Changed Status on your reservation.';
	    $this->Email->replyTo = 'info@ihanatour.com';
	    $this->Email->from = 'HANATOUR Web App <info@ihanatour.com>';
	    $this->Email->template = 'notiStatus'; // note no '.ctp'
	    //Send as 'html', 'text' or 'both' (default is 'text')
	    $this->Email->sendAs = 'html'; // because we like to send pretty mail
	    //Set view variables as normal
	   	$this->set('owner', $Owner);
	   	$this->set('comment', $comment);
	    $this->set('reservation', $Reservation);
	    //Do not pass any args to send()
	    if( $this->Email->send() ) {
	    	$this->Session->setFlash('Email sent');
	    } else {
	    	$this->Session->setFlash('Email fail');
	    }
	 }

	 function emailVoucher() {
	 	if (!empty($this->data['Reservation']['recipient'])) {
	    	$recipients = preg_split("/[,]+/", $this->data['Reservation']['recipient']);
	    	$comment = $this->data['Reservation']['comment'];
	    	$user = $this->User->read(null, $this->Auth->user('id'));
			$reservation = $this->Reservation->read(null, $this->data['Reservation']['id']);

			$this->Email->to = $recipients;
			$this->Email->subject = $this->data['Reservation']['subject'];
			$this->Email->replyTo = $user['User']['email'];
			$this->Email->from = $user['User']['email'];
			$this->Email->template = 'emailVoucher'; // note no '.ctp'
			//Send as 'html', 'text' or 'both' (default is 'text')
			$this->Email->sendAs = 'html'; // because we like to send pretty mail
			//Set view variables as normal
			$this->set('user', $user);
			$this->set('reservation', $reservation);

			$this->set('tours', $this->Tour->find('list'));
			$this->set('comment', $comment);
			$this->set('voucherInfo', $this->data['Reservation']['voucherInfo']);
                  	$this->set('itineraryInfo', $this->data['Reservation']['itineraryInfo']);
			$this->set('voucherNum', $this->data['Reservation']['voucherNum'] );
			$this->set('categoryTxt', $this->data['Reservation']['categoryTxt'] );


			//Do not pass any args to send()
			if( $this->Email->send() ) {
				$this->Session->setFlash('Email sent');
				$this->redirect('/settings/voucher/'.$this->data['Reservation']['id']);
			} else {
				$this->Session->setFlash('Email fail');
				$this->redirect('/settings/voucher/'.$this->data['Reservation']['id']);
			}
	    } else {
	    	$this->Session->setFlash('You did not enter a recipient.');
	    	$this->redirect('/rsettings/voucher/'.$this->data['Reservation']['id']);
	    }
	 }

	 function manifestMail($emails, $reservation_id, $subject) {
	    //Set view variables as normal
	    $this->set('user', $User);
	    $this->set('users', $this->User->find('list'));
	    $reservation = $this->Reservation->read(null, $reservation_id);
	    $this->set('tours', $this->Tour->find('list'));
		$this->set('tour', $this->Tour->read(null, $reservation['Item']['tour_id']));
		$this->set('categories', $this->Category->find('list', array(
			'fields' => array('id', 'code')
		)));
		$this->set('reservation', $reservation);

	    $this->Email->to = $emails;
	    $this->Email->subject = $subject;
	    $this->Email->replyTo = 'info@ihanatour.com';
	    $this->Email->from = 'HanaTourTripBooking <info@ihanatour.com>';
		$this->Email->template = 'notiBookDetail'; // note no '.ctp'

	    //Send as 'html', 'text' or 'both' (default is 'text')
	    $this->Email->sendAs = 'html'; // because we like to send pretty mail

	    //Do not pass any args to send()
	    if( $this->Email->send() ) {
	    	$this->Session->setFlash('Email sent');
	    	// WRITE LOG
			$this->Log->set(array(
			    'reservation_id'=> $reservation_id,
			    'content'=> $emails . '에게 Manifest 발송하였습니다.',
			    'user_id'=>$this->Auth->user('id')
			));
			$this->Log->saveAll();
	    } else {
	    	$this->Session->setFlash('Email fail');
	    }

	 }

	 function commentNotiMail($id, $reservation_id, $reservation_num, $text, $toGroupType) {
	    $User = $this->User->read(null,$id);
	    $Reservation = $this->Reservation->read(null, $reservation_id);
	    $Owner = $this->User->read(null, $Reservation['Reservation']['user_id']);
	    $Supplier = $this->User->read(null, $Reservation['Reservation']['wholesaler_id']);
	    // for admin
	    if ($User['User']['group_id'] <= 2) {
	    	if($toGroupType == 'agency') {
	    		$this->Email->to = $Owner['User']['email'];
	    	} else if($toGroupType == 'supplier') {
	    		$this->Email->to = $Supplier['User']['email'];
	    	}
	    } else {
	    //for agency
	    	$this->Email->to = array('info@ihanatour.com');
	    }
	    $this->Email->subject = 'Comment on reservation#, ' . $reservation_num;
	    $this->Email->replyTo = 'info@ihanatour.com';
	    $this->Email->from = 'HANATOUR Web App <info@ihanatour.com>';
	    $this->Email->template = 'notiComment'; // note no '.ctp'
	    //Send as 'html', 'text' or 'both' (default is 'text')
	    $this->Email->sendAs = 'html'; // because we like to send pretty mail
	    //Set view variables as normal
	    $this->set('user', $User);
	    $this->set('comment', $text);
	    $this->set('reservation_num', $reservation_num);
	    $this->set('reservation_id', $reservation_id);
	    //Do not pass any args to send()
	    if( $this->Email->send() ) {
	    	$this->Session->setFlash('Email sent');
	    } else {
	    	$this->Session->setFlash('Email fail');
	    }

	 }

	 function emailTicket(){
	 	//$this->layout = 'email/html/ticket_default';
	 	if(!empty($this->data)) {
	 		if($this->Setting->saveAll($this->data)) {
	 			$this->Session->setFlash('Successfully updated!!');
	 		} else {
	 			$this->Session->setFlash('Please try again!!');
	 		}
	 	} else {
	 		$this->data = $this->Setting->read(null, 3);
	 	}
	 	if (!empty($this->data['Setting']['recipient'])) {
	 		$user = $this->User->read(null, $this->Auth->user('id'));
	 		$lastName = $this->data['Setting']['lastName'];
	 		$locator = $this->data['Setting']['locator'];
	 		$shuttle_rsv_num = $this->data['Setting']['shuttle_rsv_num'];
	 		$shuttle_type = $this->data['Setting']['shuttle_type'];
	 		$email_body = $this->Setting->read(null, 3);
	 		$tours = $this->Tour->find('all', array(
	 				'conditions' => array(
	 						'featured' => '1',
	 						'active' => '1'
	 				),
	 				'order' => 'Tour.order_featured ASC',
	 				'limit' => $limit
	 		));

	 		$recipients = preg_split("/[,]+/", $this->data['Setting']['recipient'].', '.$user['User']['email']);
	 		$this->Email->to = $recipients;
	 		$this->Email->subject = $this->data['Setting']['subject'];
	 		$this->Email->replyTo = $user['User']['email'];
	 		$this->Email->from = $user['User']['email'];
	 		$this->Email->template = 'emailTicket'; // note no '.ctp'
	 		//Send as 'html', 'text' or 'both' (default is 'text')
	 		$this->Email->sendAs = 'html'; // because we like to send pretty mail

	 		$this->set('lastName', $lastName);
	 		$this->set('locator', $locator);
	 		$this->set('shuttle_rsv_num', $shuttle_rsv_num);
	 		$this->set('shuttle_type', $shuttle_type);
	 		$this->set('email_body', $email_body);
	 		$this->set('tours', $tours);

	 		//Do not pass any args to send()
	 		if( $this->Email->send() ) {
	 			$this->Session->setFlash('Email sent');
	 			return $this->render('/elements/email/html/emailTicket');
	 		} else {
	 			$this->Session->setFlash('Email fail');
	 			//return $this->render('/elements/email/html/emailTicket');
	 		}
	 	} else {
	 		$this->Session->setFlash('You did not enter a recipient.');
	 		$this->redirect('email_ticket');
	 	}
	 }

	/* ATICLES FUNCTIONS */
	function articles($id=null) {
		if(!empty($id)) {
			$conditions = array('Board.category_id' => $id);
			$this->set('idTitle', $id);
		} else {
			$conditions = array('Board.category_id <' => 12);
		}
		$this->set('boards', $this->Board->find('all', array(
			'order'=> 'Board.created DESC',
			'conditions' => $conditions
		)));
		$this->set('boardCategories', $this->BoardCategory->find('list', array(
			'conditions' => 'id < 12'
		)));
		$this->set('users', $this->User->find('list'));

		//DELETE BULK DATA
		if(!empty($this->data['delete'])) {
			foreach( $this->data['delete'] as $key=>$value ) {
				if($value == 1) {
					$files = $this->Board->read(null, $key);
					if($this->Board->delete($key)) {
						// delete files on the server
						if ( !empty($files['Upload']) ) {
							foreach($files['Upload'] as $file) {
								$oldfile = $file['path'] . $file['name'];
								unlink($oldfile);
							}
						}
					}
				}
			}
			$this->redirect('/settings/articles/'.$id);
		}
	}

	function articles_write($id=null) {
		if(!empty($this->data)) {
			// WHEN IT IS NEW ARTICLE
			if ( empty($this->data['Board']['id']) ) {
				$this->data['Board']['user_id'] = $this->Auth->user('id');
			}
			if ($this->Board->save($this->data)) {
				if ( !empty($this->data['Upload'])) {
					if($this->data['Board']['id']) {
						$this->upload($this->data['Board']['id']);
					} else {
						$this->upload($this->Board->getLastInsertID());
					}
				}
				$this->Session->setFlash('Successfully saved');
				$this->redirect(array('action' => 'articles'));
			} else {
				$this->Session->setFlash('Please try again!!');
			}
		}  else {
			$this->data = $this->Board->read(null, $id);
		}
		$this->set('boardCategories', $this->BoardCategory->find('list', array(
			'conditions' => 'id < 8'
		)));
		$this->set('idTitle', $id);
	}

	function articles_delete($id=null) {
		if(!empty($id) && $this->isAuthorized() == true) {
			if($this->Board->delete($id)) {
				$this->Session->setFlash('The post you selected is deleted.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Please try again.');
				$this->redirect($this->referer());
			}

		} else {
			$this->Session->setFlash('Invalid request.');
			$this->redirect($this->referer());
		}
	}

	/* EVENTS FUNCTIONS */
	function events() {

	    $this->set('events', $this->Event->find('all', array(
	        'order'=> 'Event.created DESC'
	    )));

	    if(!empty($this->data['Event'])) {
	        if ($this->Event->saveAll($this->data['Event'])) {
	            $this->Session->setFlash('Data saved');
	            $this->redirect(array('action' => 'events'));
	        } else {
	            $this->Session->setFlash('Failed');
	        }
	    }
	}

	function events_write($id=null) {
	    if(!empty($this->data)) {
	        // WHEN IT IS NEW EVENT
	        if ( empty($this->data['Event']['id']) ) {
	            $this->data['Event']['user_id'] = $this->Auth->user('id');
	            $lastEvent = $this->Event->find('first', array('order' => array('Event.id DESC')));
	            $eventId = $lastEvent['Event']['id'] + 1;
	        } else {
	            $eventId = $this->data['Event']['id'];
	        }
	        /* image array data */
	        $target_path = "img/uploads/";
	        $file_temp_name = $this->data['Upload']['file']['tmp_name'];
	        $file_type = $this->data['Upload']['file']['type'];
	        $rename = 'event' . $eventId . '-' . $this->data['Upload']['file']['name'];
	        $target_path = $target_path . basename( $rename );

	        if ($this->Event->save($this->data)) {
	            if ( !empty($this->data['Upload'])) {
    	            if ($file_temp_name) {
    	                if(move_uploaded_file($file_temp_name, $target_path)){
    	                    $this->Upload->create();
    	                    $this->data['Upload']['type'] = $file_type;
    	                    $this->data['Upload']['name'] = $rename;
    	                    $this->data['Upload']['path'] = 'img/uploads/';
    	                    $this->Upload->save($this->data['Upload']);
    	                    debug($this->Upload->invalidFields());
    	                }
	                }
	            }
	            $this->Session->setFlash('Successfully saved');
	            $this->redirect(array('action' => 'events'));
	        } else {
	            $this->Session->setFlash('Please try again!!');
	        }
	    } else {
            $this->data = $this->Event->read(null, $id);
	    }
	}

	function events_delete($id=null) {
	    if(!empty($id) && $this->isAuthorized() == true) {
	        if($this->Event->delete($id)) {
	            $this->Session->setFlash('The post you selected is deleted.');
	            $this->redirect($this->referer());
	        } else {
	            $this->Session->setFlash('Please try again.');
	            $this->redirect($this->referer());
	        }

	    } else {
	        $this->Session->setFlash('Invalid request.');
	        $this->redirect($this->referer());
	    }
	}

	/* NEWSLETTERS FUNCTIONS */
	function newsletters($id=null) {
		if(!empty($id)) {
			$conditions = array('Newsletter.category_id' => $id);
			$this->set('idTitle', $id);
		}
		$this->set('newsletters', $this->Newsletter->find('all', array(
			'order'=> 'Newsletter.created DESC',
			'conditions' => $conditions
		)));
		$this->set('categories', $this->NewsletterCategory->find('list'));
		$this->set('users', $this->User->find('list'));

		//extract agencies who will get newsletter
		$Emails = $this->User->find('all', array(
			'conditions' => array('newsletter !=' => ','),
			'order'=> 'User.name ASC'
		));
		$result = Set::combine($Emails, '{n}.User.name', '{n}.User', '{n}.User.group_id' );

		foreach($result as $group => $username) {
			foreach($username as $key => $value) {
				$userEmails[$group][$key][$value['email']] = $value['email'];
				if(!empty($value['email_extra'])) {
					$extraEmail = explode(',', $value['email_extra']);
					foreach ($extraEmail as $extra) {
						$userEmails[$group][$key][$extra] = $extra;
					}
				}
			}
		}
/*
		$this->set('agencies', $userEmails[3]);
		$this->set('wholesaler', $userEmails[6]);
*/
		$this->set('customers', $userEmails[4]);
		$this->set('subscriptions', $this->Subscription->find('list', array(
			'fields' => array(
				'Subscription.email',
				'Subscription.email'
			)
		)));

		$this->set('profiles', $this->Profile->find('list', array(
			'fields' => array(
				'Profile.home_email',
				'Profile.home_email'
			),
			'conditions' => array( 'Profile.home_email !=' => NULL )
		)));

		//DELETE BULK DATA
		if(!empty($this->data['delete'])) {
			foreach( $this->data['delete'] as $key=>$value ) {
				if($value == 1) {
					$files = $this->Newsletter->read(null, $key);
					if($this->Newsletter->delete($key)) {
						// delete files on the server
						if ( !empty($files['Upload']) ) {
							foreach($files['Upload'] as $file) {
								$oldfile = $file['path'] . $file['name'];
								unlink($oldfile);
							}
						}
					}
				}
			}
			$this->redirect('/settings/newsletters/'.$id);
		}

		//SENDING EMAIL
		if(!empty($this->data['Setting']['profiles']) || !empty($this->data['Setting']['subscriptions']) || !empty($this->data['Setting']['customers']) || !empty($this->data['Setting']['typedEmails'])) {

			$newsletter = $this->Newsletter->read(null, $this->data['Newsletter']['id']);

			if (!empty($this->data['Setting']['typedEmails'])) {
				$this->data['Setting']['typedEmails'] = explode(',', strtolower($this->data['Setting']['typedEmails']));
			}
			foreach($this->data['Setting'] as $value) {
				foreach ($value as $email) {
					$recipients[] = strtolower($email);
				}
			}

			foreach($recipients as $recipient) {
				if(!in_array($recipient, $success)) {
					$this->Email->reset();
					$this->Email->to = $recipient;
					$this->Email->subject = $newsletter['Newsletter']['title'];
					$this->Email->replyTo = 'iHanatour Newsletter <info@ihanatour.com>';
					$this->Email->from = 'iHanatour Newsletter <info@ihanatour.com>';
					$this->Email->template = 'template'; // note no '.ctp'
					//Send as 'html', 'text' or 'both' (default is 'text')
					$this->Email->sendAs = 'html'; // because we like to send pretty mail
					//$this->Email->delivery = 'debug';
					//Set view variables as normal
					$this->set('body', $newsletter['Newsletter']['body']);

					//Do not pass any args to send()
					if( $this->Email->send() ) {
						$this->Session->setFlash('Emails are successfully sent.');
						$success[] = $recipient;
					} else {
						$this->Session->setFlash('Sending emails failed. Try again.');
						$fail[] = $recipient;
					}
				}
			}

			$this->Newsletter->set('sent', $newsletter['Newsletter']['sent'] + 1);
			$this->Newsletter->save();

			$this->set('result', $success);
		}
	}

	function newsletters_write($id=null) {
		if(!empty($this->data)) {
			// WHEN IT IS NEW ARTICLE
			if ( empty($this->data['Newsletter']['id']) ) {
				$this->data['Newsletter']['user_id'] = $this->Auth->user('id');
			}
			if ($this->Newsletter->save($this->data)) {
				if ( !empty($this->data['Upload'])) {
					if($this->data['Newsletter']['id']) {
						$this->upload($this->data['Newsletter']['id']);
					} else {
						$this->upload($this->Newsletter->getLastInsertID());
					}
				}
				$this->Session->setFlash('Successfully saved');
				$this->redirect(array('action' => 'newsletters'));
			} else {
				$this->Session->setFlash('Please try again!!');
			}
		}  else {
			$this->data = $this->Newsletter->read(null, $id);
			$this->set('categories', $this->NewsletterCategory->find('list'));
		}

		$this->set('idTitle', $id);
	}

	function newsletters_view($id=null) {
		$this->layout = 'blank';
		if(!empty($id) && $this->isAuthorized() == true) {
			$this->set('newsletter', $this->Newsletter->read(null, $id));
		} else {
			$this->Session->setFlash('Invalid request.');
			$this->redirect($this->referer());
		}
	}

	function newsletters_delete($id=null) {
		if(!empty($id) && $this->isAuthorized() == true) {
			if($this->Newsletter->delete($id)) {
				$this->Session->setFlash('The post you selected is deleted.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Please try again.');
				$this->redirect($this->referer());
			}

		} else {
			$this->Session->setFlash('Invalid request.');
			$this->redirect($this->referer());
		}
	}

	/* SUBSCRIPTIONS FUNCTIONS */
	function subscriptions() {
		$this->set('subscriptions', $this->Subscription->find('all', array(
			'order'=> 'Subscription.email ASC'
		)));

		//DELETE BULK DATA
		if(!empty($this->data['delete'])) {
			foreach( $this->data['delete'] as $key=>$value ) {
				if($value == 1) {
					$this->Subscription->delete($key);
				}
			}
			$this->redirect('/settings/subscriptions');
		}

		//SAVEING EMAIL
		if(!empty($this->data['Setting']['typedEmails'])) {
			if (!empty($this->data['Setting']['typedEmails'])) {
				$emails = explode(',', $this->data['Setting']['typedEmails']);
				foreach($emails as $email) {
					$this->data['Subscription'][]['email'] = $email;
				}
			}
			if ($this->Subscription->saveAll($this->data['Subscription'])) {
				$this->Session->setFlash('Successfully saved.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Failed. Try again!!');
			}
		}
	}

	function subscriptions_delete($id=null) {
		if(!empty($id) && $this->isAuthorized() == true) {
			if($this->Subscription->delete($id)) {
				$this->Session->setFlash('The email you selected is deleted.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Please try again.');
				$this->redirect($this->referer());
			}

		} else {
			$this->Session->setFlash('Invalid request.');
			$this->redirect($this->referer());
		}
	}

	/* ADDITIONAL PAGES FUNCTIONS */
	function information($id=16) {
		if(!empty($id)) {
			$conditions = array('Board.category_id' => $id);
			$this->set('idTitle', $id);
		} else {
			$conditions = array('Board.category_id <' => 12);
		}
		$this->set('boards', $this->Board->find('all', array(
			'order'=> 'Board.created DESC',
			'conditions' => $conditions
		)));

		$this->set('users', $this->User->find('list'));

		//DELETE BULK DATA
		if(!empty($this->data['delete'])) {
			foreach( $this->data['delete'] as $key=>$value ) {
				if($value == 1) {
					$files = $this->Board->read(null, $key);
					if($this->Board->delete($key)) {
						// delete files on the server
						if ( !empty($files['Upload']) ) {
							foreach($files['Upload'] as $file) {
								$oldfile = $file['path'] . $file['name'];
								unlink($oldfile);
							}
						}
					}
				}
			}
			$this->redirect('/settings/information/');
		}
	}

	function information_on_menu() {
		if (!empty($this->data['Board'])) {
			if ($this->Board->saveAll($this->data['Board'])) {
				$this->Session->setFlash('Data saved');
				$this->redirect('/settings/information/');
			} else {
				$this->Session->setFlash(var_dump($this->Board->invalidFields()));
				echo var_dump($this->Board->invalidFields());
				$this->redirect('/settings/information/');
			}
		}
	}

	function information_write($id=null) {
		if(!empty($this->data)) {
			// WHEN IT IS NEW ARTICLE
			if ( empty($this->data['Board']['id']) ) {
				$this->data['Board']['user_id'] = $this->Auth->user('id');
			}
			if ($this->Board->save($this->data)) {
				if ( !empty($this->data['Upload'])) {
					if($this->data['Board']['id']) {
						$this->upload($this->data['Board']['id']);
					} else {
						$this->upload($this->Board->getLastInsertID());
					}
				}
				$this->Session->setFlash('Successfully saved');
				$this->redirect(array('action' => 'information'));
			} else {
				$this->Session->setFlash('Please try again!!');
			}
		}  else {
			$this->data = $this->Board->read(null, $id);
		}
		$this->set('boardCategories', $this->BoardCategory->find('list', array(
			'conditions' => 'id < 8'
		)));
		$this->set('idTitle', $id);
	}

	/* ARC */
	function arc() {

	    $this->set('users', $this->User->find('list', array(
	        'conditions' => array(
	            'User.group_id' => array(3),
	            'not' => array('User.acct' => null)
	        ),
	        'fields' => array('User.id', 'User.acct')
	    )));

	    if(!empty($this->data)) {

	        $paxNum = $this->data['Arc']['pax_num'];

	        for ($i= 0; $i < $paxNum; $i++) {

	            $this->data['Arc']['ISS_DT'] = $this->data['Arc']['ISS_DT'.$i];
	            $this->data['Arc']['AIRLINE_NO'] = $this->data['Arc']['AIRLINE_NO'.$i];
	            $this->data['Arc']['TICKET_NO'] = $this->data['Arc']['TICKET_NO'.$i];
	            $this->data['Arc']['DEST_CD'] = $this->data['Arc']['DEST_CD'.$i];
	            $this->data['Arc']['OB_ARPT_CD'] = $this->data['Arc']['OB_ARPT_CD'.$i];
	            $this->data['Arc']['OB_DT'] = $this->data['Arc']['OB_DT'.$i];
	            $this->data['Arc']['IB_ARPT_CD'] = $this->data['Arc']['IB_ARPT_CD'.$i];
	            $this->data['Arc']['IB_DT'] = $this->data['Arc']['IB_DT'.$i];
	            $this->data['ArcCust']['PAX_NM'] = $this->data['ArcCust']['PAX_NM'.$i];
	            $this->data['Arc']['PRTN'] = $this->data['Arc']['PRTN'.$i];
	            $this->data['Arc']['OB_BAS_AMT'] = $this->data['Arc']['OB_BAS_AMT'.$i];
	            $this->data['Arc']['IB_BAS_AMT'] = $this->data['Arc']['IB_BAS_AMT'.$i];
	            $this->data['Arc']['YQ_AMT'] = $this->data['Arc']['YQ_AMT'.$i];
	            $this->data['Arc']['XT_AMT'] = $this->data['Arc']['XT_AMT'.$i];
	            $this->data['Arc']['TOT_AMT'] = $this->data['Arc']['TOT_AMT'.$i];
	            $this->data['Arc']['FOP_CD'] = $this->data['Arc']['FOP_CD'.$i];
	            $this->data['Arc']['CTI_AMT'] = $this->data['Arc']['CTI_AMT'.$i];
	            $this->data['Arc']['CTI_AMT_PER'] = $this->data['Arc']['CTI_AMT_PER'.$i];
	            $this->data['Arc']['AGT_AMT'] = $this->data['Arc']['AGT_AMT'.$i];
	            $this->data['Arc']['AGT_AMT_PER'] = $this->data['Arc']['AGT_AMT_PER'.$i];
	            $this->data['Arc']['A/C_AMT'] = $this->data['Arc']['A/C_AMT'.$i];
	            $this->data['Arc']['TASF_AMT'] = $this->data['Arc']['TASF_AMT'.$i];
	            $this->data['Arc']['NET_AMT'] = $this->data['Arc']['NET_AMT'.$i];
	            $this->data['Arc']['AGT_NET_AMT'] = $this->data['Arc']['AGT_NET_AMT'.$i];
	            $this->data['Arc']['ARC_NET_AMT'] = $this->data['Arc']['ARC_NET_AMT'.$i];
	            $this->data['Arc']['SUB_AG_ID'] = $this->data['Arc']['SUB_AG_ID'.$i];

	            $this->data['Arc']['PIC_ID'] = $this->Auth->user('id');
	            $this->data['Arc']['ISS_DT'] = date("Y-m-d", strtotime($this->data['Arc']['ISS_DT']));
	            $this->data['Arc']['OB_DT'] = date("Y-m-d", strtotime($this->data['Arc']['OB_DT']));
	            $this->data['Arc']['IB_DT'] = date("Y-m-d", strtotime($this->data['Arc']['IB_DT']));
	            if($this->data['Arc']['OB_DT'] > $this->data['Arc']['IB_DT']){
	                $this->data['Arc']['IB_DT'] = date("Y-m-d", strtotime($this->data['Arc']['IB_DT']." + 1 year"));
	            }

	            if($this->Arc->saveAll($this->data)) {
	                $this->Session->setFlash('Successfully updated!!');
	            } else {
	                $this->Session->setFlash('Please try again!!');
	            }

	        }
	    }
	}

	function arc_view() {
	    $this->helpers['Paginator'] = array('ajax' => 'Ajax');
	    $this->set('page_limit_options', $page_limit_options);
	    $this->set('users', $this->User->find('list', array(
	    		'fields' => 'User.acct'
	    )));
	    $this->set('sub_users', $this->User->find('list', array(
	        'conditions' => array(
	            'User.group_id' => array(3),
	            'not' => array('User.acct' => null)
	        ),
	        'fields' => array('User.id', 'User.acct')
	    )));
	    $this->set('hana_users', $this->User->find('list', array(
	        'conditions' => array(
	            'User.group_id' => array(1, 2),
	            'not' => array('User.acct' => null)
	        ),
	        'fields' => array('User.id', 'User.acct')
	    )));
	    for( $i = 1 ; $i <= 5 ; $i++ ) {
	        $page_limit_options[$i] .= 50 * $i;
	    }
	    $this->set('page_limit_options', $page_limit_options);

	    if( !empty($this->data) ) {
	        if (!empty($this->data['Setting']['lname'])) {
	            $conditions['ArcCust.PAX_NM LIKE'] = $this->data['Setting']['lname'] . '/%';
	        }
	        if (!empty($this->data['Setting']['fname'])) {
	            $conditions['ArcCust.PAX_NM LIKE'] = '%' . $this->data['Setting']['fname'];
	        }
	        if (!empty($this->data['Setting']['email'])) {
	            $conditions['ArcCust.EMAIL LIKE'] = '%' . $this->data['Setting']['EMAIL'] . '%';
	        }
	        if (!empty($this->data['Setting']['MILEAGE_NO'])) {
	            $conditions['ArcCust.MILEAGE_NO'] = $this->data['Setting']['MILEAGE_NO'];
	        }
	        if (!empty($this->data['Setting']['DOB_DT'])) {
	            $conditions['ArcCust.DOB_DT'] = $this->data['Setting']['DOB_DT'];
	        }
	        if (!empty($this->data['Setting']['phone'])) {
	            $conditions['ArcCust.PHONE_NO_1'] = $this->data['Setting']['phone'];
	        }
	        if (!empty($this->data['Setting']['phone'])) {
	            $conditions['ArcCust.PHONE_NO_2'] = $this->data['Setting']['phone'];
	        }
	        if (!empty($this->data['Setting']['address'])) {
	            $conditions['ArcCust.address LIKE'] = '%' . $this->data['Setting']['address'] . '%';
	        }
	        if (!empty($this->data['Setting']['TICKET_NO'])) {
	            $conditions['TICKET_NO'] = $this->data['Setting']['TICKET_NO'];
	        }
	        if (!empty($this->data['Setting']['AIRLINE_NO'])) {
	            $conditions['AIRLINE_NO'] = $this->data['Setting']['AIRLINE_NO'];
	        }
	        if (!empty($this->data['Setting']['issued_from'])) {
	            $start_date = $this->data['Setting']['issued_from'];
	            $end_date = $this->data['Setting']['issued_to'];
	            $conditions[0] = array(
	                'and' => array(
	                   'ISS_DT >= ' => $start_date,
	                   'ISS_DT <= ' => $end_date
	                    )
	            );
	        }
	        if (!empty($this->data['Setting']['ob_dt_from'])) {
	            $start_date = $this->data['Setting']['ob_dt_from'];
	            $end_date = $this->data['Setting']['ob_dt_to'];
	            $conditions[1] = array(
	                'and' => array(
	                    'OB_DT >= ' => $start_date,
	                    'OB_DT <= ' => $end_date
	                )
	            );
	        }
	        if (!empty($this->data['Setting']['ib_dt_from'])) {
	            $start_date = $this->data['Setting']['ib_dt_from'];
	            $end_date = $this->data['Setting']['ib_dt_to'];
	            $conditions[2] = array(
	                'and' => array(
	                    'IB_DT >= ' => $start_date,
	                    'IB_DT <= ' => $end_date
	                )
	            );
	        }
	        if (!empty($this->data['Setting']['DEST_CD'])) {
	            $conditions['DEST_CD'] = $this->data['Setting']['DEST_CD'];
	        }
	        if (!empty($this->data['Setting']['gate'])) {
	            $conditions[3] = array(
	                'OR' => array('Arc.IB_ARPT_CD' => $this->data['Setting']['gate'], 'Arc.OB_ARPT_CD' => $this->data['Setting']['gate'])
	            );
	        }
	        if (!empty($this->data['Setting']['sub_agc'])) {
	            $conditions['SUB_AG_ID'] = $this->data['Setting']['sub_agc'];
	        }
	        if (!empty($this->data['Setting']['pic'])) {
	            $conditions['PIC_ID'] = $this->data['Setting']['pic'];
	        }

	        if (!empty($this->data['Setting']['limit'])){
	            $page_limit = $this->data['Setting']['limit'];
	        }

	        $flag = $this->data['Setting']['order_flag'];
	        if (!empty($this->data['Setting']['order_key'])){
	            if( $this->data['Setting']['order_flag'] == 0 ){
	                if ($this->data['Setting']['order_key'] == 'PAX_NM'){
	                    $order = 'ArcCust.'.$this->data['Setting']['order_key'].' ASC';
	                }
	                else {
                        $order = 'Arc.'.$this->data['Setting']['order_key'].' ASC';
	                }
                    $flag = 1;
	            }
	            else if( $this->data['Setting']['order_flag'] == 1 ) {
	                if ($this->data['Setting']['order_key'] == 'PAX_NM'){
	                    $order = 'ArcCust.'.$this->data['Setting']['order_key'].' DESC';
	                }
	                else {
	                    $order = 'Arc.'.$this->data['Setting']['order_key'].' DESC';
	                }
	                $flag = 0;
	            }
	        }

	        $this->paginate = array(
	            'conditions' => $conditions,
	            'limit' => $page_limit,
	            'order' => $order
	        );
	        $arcs = $this->paginate('Arc');

	        $this->set(compact('arcs'));
            $this->set('flag', $flag);
	        $this->set('conditions', $conditions);
	        $this->set('page_limit', $page_limit);

	    }
	    else {

	        $k = 0;

	        $this->paginate = array(
	            'order' => array('Arc.ISS_DT' => 'desc'),
	            'limit' => 50
	        );
	        $arcs = $this->paginate('Arc');
	        $this->set(compact('arcs'));

	    }
	}

	function arc_edit($id=null) {

	    $arc = $this->Arc->read(null, $id);
	    $this->set('arc', $arc);

	    $this->set('users', $this->User->find('list', array(
	        'conditions' => array(
	            'User.group_id' => array(3),
	            'not' => array('User.acct' => null)
	        ),
	        'fields' => array('User.id', 'User.acct')
	    )));

	    if(!empty($this->data)) {
	        $this->Arc->id = $id;
	        if($this->Arc->save($this->data)) {
	            print_r($this->data);
	            $this->Session->setFlash('Successfully updated!!');
	            $this->redirect('/settings/arc_view');
	        } else {
	            $this->Session->setFlash('Please try again!!');
	            $this->redirect('/settings/arc_view');
	        }
	    }
	}

	function arc_ren() {

	}

	function arc_cust_edit($id = null) {
	    $arc_cust = $this->ArcCust->read(null, $id);
	    $this->set('arc_cust', $arc_cust);

	    if(!empty($this->data)) {
	        $this->ArcCust->id = $id;
	        if($this->ArcCust->save($this->data)) {
	            $this->redirect('/settings/arc_view');
	        } else {
	            $this->redirect('/settings/arc_view');
	        }
	    }
	}

	/* Calendar */
	function calendar() {

		$this->loadModel('Calendar');

		$titles_unique = $this->Calendar->find('all',array(
				'order' => 'Calendar.title ASC',
				'fields' => 'DISTINCT Calendar.title'
		));
		$this->set('titles_unique',$titles_unique);

		if( !empty($this->passedArgs[0]) && !empty($this->passedArgs[1])) {
			$this->data['Calendar']['year']['year'] = $this->passedArgs[0];
			$this->data['Calendar']['month']['month'] = $this->passedArgs[1];
		}

		if (empty($this->data)) {
			$this->data['Calendar']['year']['year'] = date('Y');
			$this->data['Calendar']['month']['month'] = date('m');
		}

		$conditions = array(
				'Calendar.date_from LIKE' => '%'. $this->data['Calendar']['year']['year']. '-' . $this->data['Calendar']['month']['month']. '%'
		);
		$conditions2 = array(
				'AND' => array(
						'Calendar.date_to LIKE' => '%'. $this->data['Calendar']['year']['year']. '-' . $this->data['Calendar']['month']['month']. '%',
						'Calendar.date_from NOT LIKE' => '%'. $this->data['Calendar']['year']['year']. '-' . $this->data['Calendar']['month']['month']. '%'
				)
		);

		$vacations = $this->Calendar->find('all',array(
				'conditions' => $conditions,
				'order' => 'Calendar.date_from ASC'
		));
		$vacations2= $this->Calendar->find('all',array(
				'conditions' => $conditions2,
				'order' => 'Calendar.date_from ASC'
		));

		$this->set('vacations', $vacations);
		$this->set('vacations2', $vacations2);
	}

	function calendar2() {

		$this->loadModel('Calendar');

		$titles_unique = $this->Calendar->find('all',array(
				'order' => 'Calendar.title ASC',
				'fields' => 'DISTINCT Calendar.title'
		));
		$this->set('titles_unique',$titles_unique);

		if( !empty($this->passedArgs[0]) && !empty($this->passedArgs[1])) {
			$this->data['Calendar']['year']['year'] = $this->passedArgs[0];
			$this->data['Calendar']['month']['month'] = $this->passedArgs[1];
		}

		if (empty($this->data)) {
			$this->data['Calendar']['year']['year'] = date('Y');
			$this->data['Calendar']['month']['month'] = date('m');
		}

		$conditions = array(
				'Calendar.date_from LIKE' => '%'. $this->data['Calendar']['year']['year']. '-' . $this->data['Calendar']['month']['month']. '%'
		);
		$conditions2 = array(
				'AND' => array(
						'Calendar.date_to LIKE' => '%'. $this->data['Calendar']['year']['year']. '-' . $this->data['Calendar']['month']['month']. '%',
						'Calendar.date_from NOT LIKE' => '%'. $this->data['Calendar']['year']['year']. '-' . $this->data['Calendar']['month']['month']. '%'
				)
		);

		$vacations = $this->Calendar->find('all',array(
				'conditions' => $conditions,
				'order' => 'Calendar.date_from ASC'
		));
		$vacations2= $this->Calendar->find('all',array(
				'conditions' => $conditions2,
				'order' => 'Calendar.date_from ASC'
		));

		$this->set('vacations', $vacations);
		$this->set('vacations2', $vacations2);
	}

	function calendar_view($id){
		$this->loadModel('Calendar');
		$titles_unique = $this->Calendar->find('all',array(
				'order' => 'Calendar.title ASC',
				'fields' => 'DISTINCT Calendar.title'
		));
		$this->set('titles_unique',$titles_unique);

		if ($this->isAuthorized() == true) {
			if(!empty($this->data)) {
				$this->data['Calendar']['modified'] = DboSource::expression('NOW()');
				if($this->Calendar->save($this->data)){
					$this->redirect('/settings/calendar');
				}
			}
			$vacation = $this->Calendar->findById($id);
			$this->set('vacation', $vacation);
		}

		$comments = $this->Comment->find('all',array(
			'conditions' => array('Comment.calendar_id =' => $id)
		));
		$this->set('comments', $comments);
	}

	function calendar_list(){
		$this->loadModel('Calendar');
		$this->set('users', $this->User->find('list'));

		$titles_unique = $this->Calendar->find('all',array(
				'order' => 'Calendar.title ASC',
				'fields' => 'DISTINCT Calendar.title'
		));
		$this->set('titles_unique',$titles_unique);

		// conditions set up
		if( !empty($this->passedArgs[0])) {
			$this->data['Calendar']['date_from'] = $this->passedArgs[0];
			$this->data['Calendar']['date_to'] = $this->passedArgs[0];
		}
		if(empty($this->data)) {
			$this->data['Calendar']['date_from'] = date('Y-m-d', strtotime("-2 month"));
			$this->data['Calendar']['date_to'] = date('Y-m-d', strtotime("+1 year"));
		}
		// conditions set up
		$conditions = array(
				'AND'=>array(
						array('Calendar.date_from >=' => $this->data['Calendar']['date_from']),
						array('Calendar.date_to <=' => $this->data['Calendar']['date_to'])
				)
		);

		if(!empty($this->data['Setting']['description'])) {
			$conditions['AND'][] = array('Calendar.description LIKE' => '%'. $this->data['Setting']['description'] .'%');
		}
		if(!empty($this->data['Calendar']['title'])){
			$conditions['AND'][] = array('Calendar.title' => $this->data['Calendar']['title']);
		}

		$this->paginate = array(
				'conditions'=>$conditions,
				'order'=>array( 'Calendar.created DESC' ),
				'limit' => 50
		);

		$calendars = $this->paginate('Calendar');
		$this->helpers['Paginator'] = array('ajax' => 'Ajax');
		$this->set(compact('calendars'));
	}

	function calendar_new(){
		$this->loadModel('Calendar');
		if (isset($this->params['requested'])) {
			$calendars = $this->Calendar->find('all', array(
				'conditions' => array('status' => 'new')
			));
			return $calendars;
		}
	}

	function email_ticket(){
		$lastName = $this->data['Setting']['lastName'];
		$locator = $this->data['Setting']['locator'];

		if(!empty($this->data)) {
			if($this->Setting->saveAll($this->data)) {
				$this->Session->setFlash('Successfully updated!!');
			} else {
				$this->Session->setFlash('Please try again!!');
			}
		} else {
			$this->data = $this->Setting->read(null, 3);
		}
	}

	function prac2($id = 1098){
		$this->layout = 'admin_blank';

		$profiles_ids = $this->Reservation->find('all', array(
				'conditions' => array('Reservation.item_id' => $id, 'Reservation.status !=' => 'Canceled'),
				'fields'=>array('Reservation.id')
		));
		for($i=0; $i<count($profiles_ids); $i++){
			for($j=0; $j<count($profiles_ids[$i]['Profile']); $j++){
				$ids[] =+ $profiles_ids[$i]['Profile'][$j]['id'];
			}
		}
		$profiles = $this->Profile->find('all', array(
			'conditions' => array(
					'Profile.id' => $ids
			),
			'order' => 'Profile.order ASC'
		));

		$items = $this->Reservation->find('all', array(
				'conditions' => array('Reservation.item_id' => $id, 'Reservation.status !=' => 'Canceled')
		));
		$itemInfo = $this->Item->findById($id);

		$this->set('profiles', $profiles);
		$this->set('items', $items);
		$this->set('itemInfo', $itemInfo);
	}

	/* MAIN IMAGES */
	function images_main() {
		if(!empty($this->data)){
			$maxsize = 150000000;
			/* image array data */
			$this->data['Upload']['path'] = "img/uploads/";
	    	$file_size = $this->data['Upload']['image']['size'];
	    	$file_temp_name = $this->data['Upload']['image']['tmp_name'];
	    	$file_error = $this->data['Upload']['image']['error'];
	    	$file_type = $this->data['Upload']['image']['type'];
	    	if ( !empty($this->data['Upload']['tour_id']) ) {
	    		$rename = 'tour' . $this->data['Upload']['tour_id'] . '_' . $this->data['Upload']['image']['name'];
	    	} else {
	    		$rename = 'board' . $this->data['Upload']['board_id'] . '_' . $this->data['Upload']['image']['name'];
	    	}

	    	$target_path = $this->data['Upload']['path'] . basename($rename);
	    	$this->data['Upload']['name'] = $rename;
	    	$this->data['Upload']['type'] = $file_type;

			if($file_size > $maxsize){
				$this->Session->setFlash('Oops! Yoour file size is too big. It should be below 150kb.');
				$this->redirect($this->referer());
			} else {
				if ( move_uploaded_file($file_temp_name, $target_path) ) {
					$this->Upload->save($this->data);
					$this->Session->setFlash('A image is uploaded.');
					$this->redirect($this->referer());
				} else {
					$this->Session->setFlash('Your request failed.');
					$this->redirect($this->referer());
				}

			}
		}
		$images = $this->Upload->find('all', array(
			'conditions' => array('Upload.board_id' => '6'),
			'order' => array('Upload.order ASC')
		));
		$images_main = $this->Upload->find('all', array(
				'conditions' => array('Upload.board_id' => '5'),
				'order' => array('Upload.order ASC')
		));
		$images_tours = $this->Upload->find('all', array(
				'conditions' => array('Upload.board_id' => '80'),
				'order' => array('Upload.order ASC')
		));
		$images_board = $this->Upload->find('all', array(
				'conditions' => array('Upload.board_id' => '81'),
				'order' => array('Upload.order ASC')
		));
		$images_contactUs = $this->Upload->find('all', array(
				'conditions' => array('Upload.board_id' => '82'),
				'order' => array('Upload.order ASC')
		));
		$images_travelTools = $this->Upload->find('all', array(
				'conditions' => array('Upload.board_id' => '83'),
				'order' => array('Upload.order ASC')
		));
		$images_login = $this->Upload->find('all', array(
				'conditions' => array('Upload.board_id' => '84'),
				'order' => array('Upload.order ASC')
		));
		$this->set('images', $images);
		$this->set('images_main', $images_main);
		$this->set('images_tours', $images_tours);
		$this->set('images_board', $images_board);
		$this->set('images_contactUs', $images_contactUs);
		$this->set('images_travelTools', $images_travelTools);
		$this->set('images_login', $images_login);
	}

	function images_sponsor() {
		if(!empty($this->data)){
			if ( $this->upload($this->data['Upload']['board_id']) ) {
				$this->Session->setFlash('A image is uploaded.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Your request failed.');
				$this->redirect($this->referer());
			}
		}
		$images = $this->Upload->find('all', array(
			'conditions' => array('Upload.board_id' => '7'),
			'order' => array('Upload.order ASC')
		));
		$this->set('images', $images);
	}

	function update_order() {
		if(!empty($this->data)) {
			if($this->Upload->saveAll($this->data['Upload'])) {
				$this->Session->setFlash('The order of images changed.');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('Failed to change the order of images. Try again.');
				$this->redirect($this->referer());
			}
		}
	}

	function image_edit($id, $refer=null) {
		if(!empty($this->data)) {
			$this->Upload->id = $id;
			if ($this->Upload->save($this->data)) {
				$this->Session->setFlash('The image has been updated.');
				if ($refer) {
					$this->redirect(array('action'=>$refer));
				} else {
					$this->redirect(array('action'=>'images_main'));
				}
			}else {
				$this->Session->setFlash('The image could not be updated. Please try again.');
			}
		} else {
			$this->data = $this->Upload->read(null, $id);
		}
	}

	function image_delete($id) {
		$upload = $this->Upload->read(null, $id);
		$oldfile = $upload['Upload']['path'] . $upload['Upload']['name'];
		if ($this->Upload->delete($id)) {
			unlink($oldfile);

			$this->Session->setFlash('The image has been deleted frome the server.');
			if ($this->referer() != '/') {
				$this->redirect($this->referer());
			} else {
				$this->redirect(array('action' => 'index'));
			}
		}
	}

	function upload($id) {
		$maxsize = 150000;
		/* image array data */
		$this->data['Upload']['path'] = "img/uploads/";
		$file_size = $this->data['Upload']['file']['size'];
		$file_temp_name = $this->data['Upload']['file']['tmp_name'];
		$file_error = $this->data['Upload']['file']['error'];
		$file_type = $this->data['Upload']['file']['type'];
		$rename = 'board' . $id . '_' . $this->data['Upload']['file']['name'];
		$target_path = $this->data['Upload']['path'] . basename($rename);
		$this->data['Upload']['name'] = $rename;
		$this->data['Upload']['type'] = $file_type;
		$this->data['Upload']['board_id'] = $id;

		if ( move_uploaded_file($file_temp_name, $target_path) ) {
			$this->Upload->save($this->data);
			return true;
		} else {
			return false;
		}
	}

	function pax_upload($id) {
		$maxsize = 500000;
		/* image array data */
		$this->data['Upload']['path'] = "img/uploads/item/";
		$file_size = $this->data['Upload']['file']['size'];
		$file_temp_name = $this->data['Upload']['file']['tmp_name'];
		$file_error = $this->data['Upload']['file']['error'];
		$file_type = $this->data['Upload']['file']['type'];
		$rename = 'item' . $id . '_' . $this->data['Upload']['file']['name'];
		$target_path = $this->data['Upload']['path'] . basename($rename);
		$this->data['Upload']['name'] = $rename;
		$this->data['Upload']['type'] = $file_type;
		$this->data['Upload']['item_id'] = $id;

		if ( move_uploaded_file($file_temp_name, $target_path) ) {
			$this->Upload->save($this->data);
			$this->redirect('/settings/pax_list/' .$id);
			return true;
		} else {
			$this->redirect('/settings/pax_list/' .$id);
			return false;
		}
	}

	function pax_upload_edit($id = null) {
		$this->layout = 'admin_blank';

		$file = $this->Upload->findById($id);
		$this->set('file', $file);
	}

	function pax_upload_order($id){
		$this->layout = 'admin_blank';
		$itemfiles = $this->Item->read(null, $id);
		$this->set('itemfiles', $itemfiles);
	}

	function upload_addition() {
		if(!empty($this->data)){
			$maxsize = 500000;
			/* image array data */
			$this->data['Upload']['path'] = "img/uploads/";
	    	$file_size = $this->data['Upload']['image']['size'];
	    	$file_temp_name = $this->data['Upload']['image']['tmp_name'];
	    	$file_error = $this->data['Upload']['image']['error'];
	    	$file_type = $this->data['Upload']['image']['type'];
	    	$rename = 'tour' . $this->data['Upload']['tour_id'] . '_add-' . $this->data['Upload']['image']['name'];
	    	$target_path = $this->data['Upload']['path'] . basename($rename);
	    	$this->data['Upload']['name'] = $rename;
	    	$this->data['Upload']['type'] = $file_type;

			if($file_size > $maxsize){
				$this->Session->setFlash('Oops! Yoour file size is too big. It should be below 500kb.');
				$this->redirect($this->referer());
			} else {
				if ( move_uploaded_file($file_temp_name, $target_path) ) {
					$this->Upload->save($this->data);
					$this->Session->setFlash('Your additional image is uploaded.');
					$this->redirect($this->referer());
				} else {
					$this->Session->setFlash('Your request failed.');
					$this->redirect($this->referer());
				}

			}
		}
	 }

	 function profile_new() {
	 	// if data exists
 		if( !empty($this->data['Profile']) ) {
 			// get last id
 			$group = $this->Profile->find('first', array('order' => array('Profile.id DESC')));
 			for ( $i = 0; $i < $this->data['num']; $i++ ) {
 				$this->data['Profile'][$i]['parent_id'] = $group['Profile']['id']+1;
 				$this->data['Profile'][$i]['special'] = $this->data['special'];
 				$this->data['Profile'][$i]['remark'] = $this->data['remark'];
 				$this->data['Profile'][$i]['history'] = $this->data['history'];
 				$this->data['Profile'][$i]['cc_num'] = $this->encryptCard($this->data['Profile'][$i]['cc_num']);
 			}
 			if ($this->Profile->saveAll($this->data['Profile'])) {
 				$this->Session->setFlash('Successfully saved');
 			} else {
 				$this->Session->setFlash('Please try again!!');
 			}
 		}
	}

	function profile_search() {
		// if data exists
		if( !empty($this->data) ) {
			if(!empty($this->data['Setting']['lname'])) {
				//$conditions['Profile.lname LIKE'] = '%' . $this->data['Setting']['lname'] . '%';
				$conditions = array(
					'Profile.lname LIKE' => '%' . $this->data['Setting']['lname'] . '%',
					'Profile.fname LIKE' => '%' . $this->data['Setting']['fname'] . '%'
				);
				foreach($this->data['Setting'] as $key => $value) :
					$conditions['Profile.'. $key .' LIKE'] = '%' . $value . '%';
				endforeach;
			}
/*
			if(!empty($this->data['Setting']['home_email'])) {
				$conditions['Profile.home_email LIKE'] = '%' . $this->data['Setting']['home_email'] . '%' ;
			}
*/
			$profiles = $this->Profile->find('all', array('conditions' => $conditions));

		  		for ($i=0; $i < count($profiles); $i++ ) {
					if($profiles[$i]['Profile']['cc_num'] != ''){
		  				$profiles[$i]['Profile']['cc_num'] = $this->decryptCard($profiles[$i]['Profile']['cc_num']);
		  			}
				}
		  	$this->set('profiles', $profiles);
	  	}
	}

	function profile_result($parent_id=null) {
		// if data exists
		if( !empty($this->data) ) {
			if(!empty($this->data['lname'])) {
				//$conditions['Profile.lname LIKE'] = '%' . $this->data['lname'] . '%';
				$conditions = array(
					'Profile.lname LIKE' => '%' . $this->data['lname'] . '%',
					'Profile.fname LIKE' => '%' . $this->data['fname'] . '%'
				);
			}
			if(!empty($this->data['dob'])) {
				$conditions['Profile.dob LIKE'] = '%' . $this->data['dob'] . '%';
			}
			if(!empty($this->data['email'])) {
				$conditions['Profile.email'] = $this->data['email'] ;
			}
			if(!empty($this->data['home_zip'])) {
				$conditions['Profile.home_zip'] = $this->data['home_zip'] ;
			}
			if(!empty($this->data['work_zip'])) {
				$conditions['Profile.work_zip'] = $this->data['work_zip'] ;
			}
	  	} else if ( $parent_id != null ) {
	  		$conditions = array('Profile.parent_id' => $parent_id );
	  	}
	  	$profiles = $this->Profile->find('all', array('conditions' => $conditions));
		if($profiles[$i]['Profile']['cc_num'] != ''){
	  		for ($i=0; $i < count($profiles); $i++ ) {
	  			$profiles[$i]['Profile']['cc_num'] = $this->decryptCard($profiles[$i]['Profile']['cc_num']);
	  		}
		}
	  	$this->set('profiles', $profiles);
	}

	function profile_edit($id) {
		$this->Profile->bindModel(
				array(
						'hasAndBelongsToMany' => array(
								'Reservation' => array(
										'className' => 'Reservation',
										'joinTable' => 'profiles_reservations',
										'foreignKey' => 'profile_id',
										'associationForeignKey' => 'reservation_id',
										'unique' => 'keepExisting'
								)
						)
				)
		);
		if( !empty($this->data['Profile']) ) {

			for ($i=0; $i < count($this->data['Profile']); $i++ ) {
				//$this->data['Profile'][$i]['cc_num'] = $this->encryptCard($this->data['Profile'][$i]['cc_num']);
				$this->data['id'][$i] = $this->data['Profile'][$i]['id'];
			}
			if ($this->Profile->saveAll($this->data['Profile'])) {
				$this->Session->setFlash('Successfully saved');
			} else {
				$this->Session->setFlash('Please try again!!');
			}
		}
		// if data exists
		if( $id ) {
			$conditions = array('Profile.id' => $id);
	  	} else {
		  	//$this->Session->setFlash('Invalid request.');
		  	$this->redirect($this->referer());
	  	}
	  	$profiles = $this->Profile->find('all', array(
	  			'conditions' => $conditions,
	  			'recursive' => '2'
	  	));
	  	for ($i=0; $i < count($profiles); $i++ ) {
	  		//$profiles[$i]['Profile']['cc_num'] = $this->decryptCard($profiles[$i]['Profile']['cc_num']);
	  	}
	  	$this->set('profiles', $profiles);
	}

	function profile_checkExistence($lname, $fname) {
		if ($this->Auth->user('group_id') && $this->Auth->user('group_id') <= 2) {

			$this->Profile->bindModel(
					array(
							'hasAndBelongsToMany' => array(
									'Reservation' => array(
											'className' => 'Reservation',
											'joinTable' => 'profiles_reservations',
											'foreignKey' => 'profile_id',
											'associationForeignKey' => 'reservation_id',
											'unique' => 'keepExisting'
									)
							)
					)
			);

			$profiles = $this->Profile->find('all', array(
				'conditions' => array(
					'Profile.lname' => $lname,
					'Profile.fname LIKE' => '%' . $fname . '%'
				),
				'order'=>array( 'Profile.created DESC' )
			));
			$this->set('profiles', $profiles);
		}
	}



	function prac3($limit=null) {
// 		$this->Profile->bindModel(
// 				array(
// 						'hasAndBelongsToMany' => array(
// 								'Reservation' => array(
// 										'className' => 'Reservation',
// 										'joinTable' => 'profiles_reservations',
// 										'foreignKey' => 'profile_id',
// 										'associationForeignKey' => 'reservation_id',
// 										'unique' => 'keepExisting'
// 								)
// 						)
// 				)
// 		);
		$profiles = $this->Profile->find('all', array(
				'conditions' => array(
						'Profile.lname LIKE' => '%소%',
				        'Profile.fname LIKE' => '%창호%'
				),
				'order'=>array( 'Profile.created DESC' )
		));
		$this->set('profiles', $profiles);
		print_r($profiles);
		print_r($profiles['Reservation'][0]['item_id']);

		foreach($profiles as $key => $profile):
			for($i=0;$i<sizeof($profile['Reservation']);$i++){
				$item_id = $profile['Reservation'][$i]['item_id'];
				$tour_date = $this->Item->find('all', array(
						'conditions' => array(
								'id' => $item_id
						)
				));
				print_r($tour_date);
			}
		endforeach;
		print_r($tour_date);
		$tour_dates = $this->Item->find('all', array(
			'conditions' => array(
					'id' => $item_id
			)
		));
		print_r($tour_dates);
	}

	function syncProfile() {
		$items = $this->Reservation->find('all');

		/* 		CREATE PROFILE FROM ORIGIN RESERVATION */
		foreach ($items as $item) :
			$names = preg_split("/[,]+/", $item['Reservation']['customer_name']);
			$dobs = preg_split("/[;]+/", $item['Reservation']['dob']);
			$nations = preg_split("/[;]+/", $item['Reservation']['nation']);
			$passports = preg_split("/[;]+/", $item['Reservation']['passport']);
			$exps = preg_split("/[;]+/", $item['Reservation']['exp']);
			$addresses = preg_split("/[;]+/", $item['Reservation']['address']);
			$cities = preg_split("/[;]+/", $item['Reservation']['city']);
			$states = preg_split("/[;]+/", $item['Reservation']['state']);
			$zips = preg_split("/[;]+/", $item['Reservation']['zip']);
			$emails = preg_split("/[;]+/", $item['Reservation']['email']);
			$phone_numbers = preg_split("/[;]+/", $item['Reservation']['phone_number']);
			$work_numbers = preg_split("/[;]+/", $item['Reservation']['work_number']);
			$cell_numbers = preg_split("/[;]+/", $item['Reservation']['cell_number']);
			$paxNum = sizeof($names)-1;
		  	for ($i = 0; $i < $paxNum ; $i++) {
		  		$custName[$i] = preg_split("/[\/]+/", $names[$i]);
		  		$this->data['Profile']['lname'] = $custName[$i][0];
		  		$this->data['Profile']['fname'] = $custName[$i][1];
		  		$this->data['Profile']['dob'] = substr($dobs[$i], -4) . '/' . substr($dobs[$i], 0, -5);
		  		$this->data['Profile']['nation'] = $nations[$i];
		  		$this->data['Profile']['passport'] = $passports[$i];
		  		$this->data['Profile']['exp'] = substr($exps[$i], -4) . '/' . substr($exps[$i], 0, -5);
		  		$this->data['Profile']['home_address1'] = $addresses[$i];
		  		$this->data['Profile']['home_city'] = $cities[$i];
		  		$this->data['Profile']['home_state'] = $states[$i];
		  		$this->data['Profile']['home_zip'] = $zips[$i];
		  		$this->data['Profile']['home_email'] = $emails[$i];
		  		$this->data['Profile']['home_phone'] = $phone_numbers[$i];
		  		$this->data['Profile']['work_phone'] = $work_numbers[$i];
		  		$this->data['Profile']['cell_phone'] = $cell_numbers[$i];
		  		$this->Profile->create();
		  		$this->Profile->save($this->data['Profile']);
		  		$data['Profile']['Profile'][] = $this->Profile->id;
		  	}

			$data['Reservation']['id'] = $item['Reservation']['id'];
			$this->Reservation->save($data);
			unset($data);
		endforeach;

/*
		$data['Reservation']['id'] = 322;
		$data['Profile']['Profile'][0] = 49;
		$this->Reservation->save($data);
		$this->data['Profile'][0]['lname'] = 'Jkang';
		$this->data['Profile'][1]['lname'] = 'Kjang';
		$this->Profile->saveAll($this->data['Profile']);
*/
	}

}

?>
