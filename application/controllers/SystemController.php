<?php
/**
 * System handles only some system stuff, such as who can access to this system,
 * calculation status/start/stop, and report alerts via e-mail.
 */

use Koldy\Url;
use Koldy\Log;
use Koldy\Validator;
use Koldy\Application;
use Koldy\Input;
use Koldy\Mail;
use Koldy\Request;
use Koldy\Exception;
use Koldy\Timezone;

class SystemController extends AbstractSessionController {
	
	/**
	 * Show page with calculation status and Start button
	 */
	public function calculationAction() {
		$content = array();
		
		if (Status::isCalculationInProgress()) {
			$content[] = Bootstrap::row()
				->add(8, Bootstrap::alert('Calculation is in progress!')->color('warning'), 2);
		}
		
		$content[] = Bootstrap::row()
			->add(8, Bootstrap::h(1, 'Calculation'), 2);
		
		if (Status::getCalculationStatus() === null) {
			$remoteButton = BootstrapUI::buttonRemote('Start now')
				->url(Url::href('calculation', 'start'))
				->promptText('Are you sure you want to start calculation process? It might take a long time for the first time.')
				->color('green');
			
			$content[] = Bootstrap::row()
				->add(8, Bootstrap::alert('Calculation was never started yet! ' . $remoteButton)->color('warning'), 2);
		} else {
			$panel = Bootstrap::panel('Calculation status')->color('blue');
			$inProgress = Status::isCalculationInProgress();
			
			$table = Bootstrap::table()
				->column('id', 'state')
				->column('value', 'value');
			
			if (!$inProgress) {
				$processStart = Status::getLastCalculationProcessStart();
				$table->row(array(
					'id' => 'last calculation started on',
					'value' => $this->date('Y-m-d H:i:s', strtotime($processStart))
				));
				
				$processEnd = Status::getLastCalculationProcessEnd();
				$table->row(array(
					'id' => 'last calculation finished on',
					'value' => $this->date('Y-m-d H:i:s', strtotime($processEnd))
				));
				
				$table->row(array(
					'id' => 'last calculation duration',
					'value' => $this->duration($processStart, $processEnd)
				));
				
				$table->row(array(
					'id' => 'last status',
					'value' => Status::getCalculationStatus()
				));
				
				$content[] = Bootstrap::row()->add(8,
					\BootstrapUI::buttonRemote('Start recalculation')
						->promptText('Do you really want to start database recalculation?')
						->progressText('Starting recalculation...')
						->url(\Url::href('system', 'start-recalculation'))
						->color('red')
						->setAttribute('style', 'margin-bottom: 10px')
				, 2);
				
// 				$table->row(array(
// 					'id' => 'next calculation',
// 					'value' => BootstrapUI::buttonRemote('Start now')
// 						->url(Url::href('calculation', 'start'))
// 						->promptText('Are you sure you want to start calculation process now?')
// 						->size('xs')
// 						->color('green')
// 				));
			} else {
				$processStart = Status::getLastCalculationProcessStart();
				$table->row(array(
					'id' => 'last calculation started on',
					'value' => $this->date('Y-m-d H:i:s', strtotime($processStart))
				));
				
				$terminateButton = BootstrapUI::buttonRemote('Terminate')
					->url(Url::href('calculation', 'terminate'))
					->promptText('Do you really want to terminate calculation?')
					->size('xs')
					->color('red');
				
				$table->row(array(
					'id' => 'current status',
					'value' => Status::getCalculationStatus() // .'<br/>'. $terminateButton
				));
			}
			
			$panel->content($table);
			$content[] = Bootstrap::row()
				->add(8, $panel, 2);
		}
		
		return View::create('base')
			->with('title', 'Calculation')
			->with('content', $content);
	}

	/**
	 * Start the calculation "in background"
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function startAjax() {
		if (Status::isCalculationInProgress()) {
			return BootstrapUI::buttonRemoteResponse()
				->text('Already in progress')
				->disableButton()
				->refresh();
		}
		
		Log::info("{$this->getUser('username')} started calculation!");
		
		return BootstrapUI::buttonRemoteResponse()
			->disableButton()
			->text('Calculation has started')
			->after(function() {
				$calculation = new \Calculation();
				$calculation->calculate();
			});
	}
	
	public function startRecalculationAjax() {
		if (Status::isCalculationInProgress()) {
			return \BootstrapUI::buttonRemoteResponse()
				->text('Calculation is already in progress');
		}
		
		return \BootstrapUI::buttonRemoteResponse()
			->disableButton()
			->text('Recalculation is in progress...')
			->after(function() {
				set_time_limit(0);
				
				\Status::calculationStarted();
				
				\Status::setCalculationStatus('Recalculating brands');
				\Brand::recalculate();
				
				\Status::setCalculationStatus('Recalculating countries');
				\Country::recalculate();
				
				\Status::setCalculationStatus('Recalculating packages');
				\Package::recalculate();
				
				\Status::setCalculationStatus('Recalculating package versions');
				\Package\Version::recalculate();
				
				\Status::setCalculationStatus('Recalculating phone models');
				\Phone\Model::recalculate();
				
				\Status::setCalculationStatus('Recalculating product names');
				\Product::recalculate();
				
				\Status::setCalculationStatus('Recalculating providers');
				\Provider::recalculate();
				
				\Status::setCalculationStatus('Recalculating stack traces');
				\Stack\Trace::recalculate();
				
				\Status::setCalculationStatus('Recalculating OS versions');
				\Version::recalculate();
				
				\Status::calculationFinished('all');
			});
	}
	
	/**
	 * Manually stop the calculation (which is not recommended)
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function terminateAjax() {
		if (Status::isCalculationInProgress()) {
			Status::terminateCalculation();
			Log::info("{$this->getUser('username')} requested calculation termination");
			return BootstrapUI::buttonRemoteResponse()
				->text('Requested termination')
				->disableButton();
		} else {
			return BootstrapUI::buttonRemoteResponse()
				->text('Not running')
				->disableButton();
		}
	}
	
	/**
	 * Show the page with list of e-mail alert triggers
	 */
	public function alertsAction() {
		$content = array();
		
		$content[] = Bootstrap::row()->add(12, Bootstrap::h(1, 'E-mail alerts'));
		
		$content[] = Bootstrap::row()->add(12, array(
			Bootstrap::anchor('Add new rule', Url::href('system', 'add-email-alert'))->asButton()->color('green'),
			Bootstrap::anchor('Test e-email', Url::href('system', 'test-email'))->asButton()->color('lightblue')
		))->setAttribute('style', 'margin-bottom: 10px');
		
		$table = BootstrapUI::tableRemote()
			->title('List of existing e-mail alerts')
			->column('name')
			->column('description')
			->column('action', '', 70)
			->sortableColumns(array('name'));
		
		$content[] = Bootstrap::row()->add(12, $table);
		
		return View::create('base')
			->with('title', 'E-mail alerts')
			->with('content', $content);
	}
	
	/**
	 * Get the list of email triggers from database
	 * @return \Bootstrap\Response\TableRemote
	 */
	public function alertsAjax() {
		return BootstrapUI::tableRemoteResponse()
			->column('name')
			->column('description')
			->column('action', function($value, $row) {
				$edit = \Bootstrap::anchor(\Bootstrap::icon('edit'), \Koldy\Url::href('system', 'edit-email-alert', array($row['id'])))
					->asButton()
					->size('xs')
					->color('blue');
				
				$delete = \BootstrapUI::buttonRemote(\Bootstrap::icon('remove'))
					->url(\Koldy\Url::href('system', 'delete-email-alert'))
					->param('id', $row['id'])
					->promptText("Do you really want to delete the '{$row['name']}'?")
					->size('xs')
					->color('red');
				
				return "{$edit} {$delete}";
			})
			->resultSet(Email\Trigger::resultSet())
			->handle();
	}
	
	/**
	 * Handle delete of e-mail trigger in database
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function deleteEmailAlertAjax() {
		$params = Input::requireParams('id');
		$id = (int) $params->id;
		if ($id <= 0) {
			return BootstrapUI::buttonRemoteResponse()->failed('Invalid ID');
		}
		
		$r = Email\Trigger::fetchOne($id);
		if ($r === false) {
			return BootstrapUI::buttonRemoteResponse()->failed('Invalid ID');
		}
		
		if ($r->destroy()) {
			Log::info("Deleted e-mail alert #{$id}");
			return BootstrapUI::buttonRemoteResponse()
				->disableButton()
				->disableOtherButtons()
				->removeParentRow()
				->reloadParentTable();
		} else {
			return BootstrapUI::buttonRemoteResponse();
		}
	}
	
	/**
	 * Get the form to add/edit e-mail trigger
	 * @param array $values
	 * @return \Bootstrap\Panel
	 */
	private function getForm(array $values = null) {
		if ($values === null) {
			$values = array(
				'id' => 0,
				'package' => null,
				'package_version' => null,
				'os_version' => null,
				'brand' => null,
				'model' => null,
				'product' => null,
				'country' => null,
				'name' => null,
				'to_emails' => null,
				'email_delay_minutes' => 60,
				'description' => null
			);
		}
		
		$panel = Bootstrap::panel('New rule')->color('blue');
		
		$info = Bootstrap::blockquote('E-mail with report will be sent immediately ONLY if ALL rules match your criteria. You have to define at least one criteria.');
		$form = BootstrapUI::form()->horizontal(3)->add(Bootstrap::hiddenfield('id', $values['id']))
				
			->add($info)
			->add(BootstrapUI::select2('package', 'package(s)')->value($values['package'])->tags()->tagsTokenSeparator(array(',', ' '))->placeholder('e.g. com.yourapp.package.name')->maxSelections(5))
			->add(BootstrapUI::select2('package_version', 'package version(s)')->value($values['package_version'])->tags()->tagsTokenSeparator(array(',', ' '))->placeholder('e.g. 1.2')->maxSelections(5))
			->add(BootstrapUI::select2('os_version', 'OS version(s)')->value($values['os_version'])->tags()->tagsTokenSeparator(array(',', ' '))->placeholder('e.g. 2.3.5')->maxSelections(5))
			->add(BootstrapUI::select2('brand', 'brand(s)')->value($values['brand'])->tags()->tagsTokenSeparator(array(',', ' '))->placeholder('e.g. samsung')->maxSelections(5))
			->add(BootstrapUI::select2('model', 'model(s)')->value($values['model'])->tags()->tagsTokenSeparator(array(',', ' '))->placeholder('e.g. Galaxy Nexus')->maxSelections(5))
			->add(BootstrapUI::select2('product', 'product(s)')->value($values['product'])->tags()->tagsTokenSeparator(array(',', ' '))->placeholder('e.g. espresso10rfxx')->maxSelections(5))
			->add(BootstrapUI::select2('country', 'country(s)', Country::fetchKeyValue('tld', 'country'), $values['country'])->multiple()->maxSelections(10))
				
			->add(Bootstrap::blockquote('Other alert configs:'))
			->add(Bootstrap::textfield('name', $values['name'], 'criteria name')->placeholder('This is also subject of e-mail'))
			->add(BootstrapUI::select2('to_emails', 'send report to email(s)')->value($values['to_emails'])->tags()->tagsTokenSeparator(array(',', ' ')))
			->add(Bootstrap::numberfield('email_delay_minutes', $values['email_delay_minutes'], 'delay in minutes between two reports')->min(1))
			->add(Bootstrap::textarea('description', $values['description'], 'description')->rows(4)->placeholder('Short description about this'))
				
			->addSubmit('Submit')
			->addButton(Bootstrap::anchor('Cancel', Url::href('system', 'alerts'))->asButton()->color('red'));
		$panel->content($form);
		
		return $panel;
	}
	
	/**
	 * Show the page to add new e-mail trigger
	 */
	public function addEmailAlertAction() {
		$title = 'New e-mail alert rule';
		
		$content = array();
		$content[] = Bootstrap::row()->add(12, Bootstrap::h(1, $title));
		$content[] = Bootstrap::row()->add(12, $this->getForm());
		
		return View::create('base')
			->with('title', $title)
			->with('content', $content);
	}
	
	/**
	 * Add new e-mail trigger to database so it can later be really used
	 * @return \BootstrapUI\Response\Form
	 */
	public function addEmailAlertAjax() {
		$validator = Validator::create(array(
			'id' => 'is:0',
			'name' => 'required|max:255',
			'to_emails' => 'required',
			'email_delay_minutes' => 'required|integer|min:1',
			'description' => null,
			
			'package' => 'max:255',
			'package_version' => 'max:255',
			'os_version' => 'max:255',
			'brand' => 'max:255',
			'model' => 'max:255',
			'product' => 'max:255',
			'country' => 'array'
		));
		
		if ($validator->failed()) {
			return BootstrapUI::formResponse()->failedOn($validator);
		}
		
		$params = $validator->getParamsObj();
		if ($params->package === null
			&& $params->package_version === null
			&& $params->os_version === null
			&& $params->brand === null
			&& $params->model === null
			&& $params->product === null
			&& $params->country === null
		) {
			return BootstrapUI::formResponse()->failed('Define at least one criteria');
		}
		
		$data = $validator->getParams();
		$data['created_by'] = (int) $this->user['id'];
		$data['created_at'] = $data['last_update'] = gmdate('Y-m-d H:i:s');
		
		$r = Email\Trigger::create($data);
		
		if ($r === false) {
			return BootstrapUI::formResponse()->failed('Something went wrong');
		}
		
		Log::info("Created new e-mail alert #{$r->id}");
		return BootstrapUI::formResponse()->redirect(Url::href('system', 'alerts'))->message('Cool!');
	}
	
	/**
	 * Show the page to edit settings of e-mail trigger
	 */
	public function editEmailAlertAction() {
		$title = 'Editing e-mail alert rule';
		$id = (int) Url::getVar(2);
		
		if ($id <= 0) {
			Application::throwError(400);
		}
		
		$r = Email\Trigger::fetchOne($id);
		if ($r === false) {
			Application::throwError(404, 'Email trigger not found');
		}
		
		$data = $r->getData();
		$data['country'] = ($data['country'] !== null) ? explode(',', $data['country']) : null;
		
		$content = array();
		$content[] = Bootstrap::row()->add(12, Bootstrap::h(1, $title));
		$content[] = Bootstrap::row()->add(12, $this->getForm($data));
	
		return View::create('base')
			->with('title', $title)
			->with('content', $content);
	}
	
	/**
	 * Update settings of e-mail trigger into database
	 * @return \Bootstrap\Response\Form
	 */
	public function editEmailAlertAjax() {
		$validator = Validator::create(array(
			'id' => 'required|integer',
			'name' => 'required|max:255',
			'to_emails' => 'required',
			'email_delay_minutes' => 'required|integer|min:1',
			'description' => null,
				
			'package' => 'max:255',
			'package_version' => 'max:255',
			'os_version' => 'max:255',
			'brand' => 'max:255',
			'model' => 'max:255',
			'product' => 'max:255',
			'country' => null
		));
	
		if ($validator->failed()) {
			return BootstrapUI::formResponse()->failedOn($validator);
		}
		
		$params = $validator->getParamsObj();
		if ($params->package === null
			&& $params->package_version === null
			&& $params->os_version === null
			&& $params->brand === null
			&& $params->model === null
			&& $params->product === null
			&& $params->country === null
		) {
			return BootstrapUI::formResponse()->failed('Define at least one criteria');
		}
		
		$r = Email\Trigger::fetchOne($params->id);
		if ($r === false) {
			return BootstrapUI::formResponse()->failed('Can not find record ' . $params->id);
		}
		
		$r->set($validator->getParams());
		$r->country = $params->country !== null ? implode(',', array_values($params->country)) : null;
		$r->last_update = gmdate('Y-m-d H:i:s');
		
		if ($r->save()) {
			Log::info("Updated email trigger #{$params->id}");
		} else {
			return BootstrapUI::formResponse()->failed('Something went wrong');
		}
	
		return BootstrapUI::formResponse()->message('Cool!');
	}
	
	/**
	 * Show the page to test the e-mail configuration settings
	 */
	public function testEmailAction() {
		$content = array();
		$title = 'Test e-mail configuration';
		
		$content[] = Bootstrap::row()->add(12, Bootstrap::h(1, $title));
		
		$panel = Bootstrap::panel('Send test mail')->color('blue');
		$form = BootstrapUI::form()
			->horizontal()
			->add(Bootstrap::textfield('to', null, 'to e-mail')->placeholder('your@email.com'))
			->addSubmit('Submit')
			->addButton(Bootstrap::anchor('Cancel', Url::href('system', 'alerts'))->asButton()->color('red'));
		$panel->content($form);
		$content[] = Bootstrap::row()->add(8, $panel, 2);
		
		return View::create('base')
			->with('title', $title)
			->with('content', $content);
	}
	
	/**
	 * Do actual e-mail test by sending the e-mail to the given e-mail address
	 * @return \Bootstrap\Response\Form
	 */
	public function testEmailAjax() {
		$validator = Validator::create(array(
			'to' => 'required|email'
		));
		
		if ($validator->failed()) {
			return BootstrapUI::formResponse()->failedOn($validator);
		}

		if (!Mail::isEnabled()) {
			return BootstrapUI::formResponse()->message('Mail sender is not enabled!');
		}
		
		$params = $validator->getParamsObj();
		
		$mail = Mail::create()
			->from('no-replay@' . Request::hostNameDomain(), Request::hostName())
			->to($params->to)
			->subject('Configuration test mail')
			
			// and now, do make some short dummy mail
			->body("If you got this e-mail, then your mail configuration is just fine!\n\n" . Url::current());
		
		try {
			if ($mail->send()) {
				Log::info("Test e-mail is sent to {$params->to}");
				return BootstrapUI::formResponse()->message('Test e-mail is sent!');
			}
		} catch (Exception $e) {
			Log::info("Couldn\'t send test e-mail to {$params->to}");
			Log::exception($e); // we'll log this if needed, just to make sure it won't be forgotten
			return BootstrapUI::formResponse()->failed('E-mail wasn\'t sent:<br/>' . $e->getMessage()); // show user the actual error
		}
	}
	
	/**
	 * Show the page with list of system users
	 */
	public function usersAction() {
		$title = 'System users';
		$content = array(Bootstrap::row()->add(12, Bootstrap::h(1, $title)));
		
		$content[] = Bootstrap::row()->add(12,
			Bootstrap::anchor('Add new user', Url::href('system', 'add-user'))->asButton()->color('green')
		)->setAttribute('style', 'margin-bottom: 10px;');
		
		$table = BootstrapUI::tableRemote()
			->title($title)
			->column('username')
			->column('first_name')
			->column('last_name')
			->column('action', '', 70)
			->sortableColumns(array('username', 'first_name', 'last_name'))
			->sortField('username', 'asc')
			->searchable();
		$content[] = $table;
		
		return View::create('base')
			->with('title', $title)
			->with('content', $content);
	}
	
	/**
	 * Get the list of users from database
	 * @return \Bootstrap\Response\TableRemote
	 */
	public function usersAjax() {
		return BootstrapUI::tableRemoteResponse()
			->column('username')
			->column('first_name')
			->column('last_name')
			->column('action', function($value, $row) {
				$edit = \Bootstrap::anchor(\Bootstrap::icon('edit'), \Koldy\Url::href('system', 'edit-user', array($row['id'])))
					->asButton()
					->size('xs')
					->color('blue');
				
				$delete = \BootstrapUI::buttonRemote(\Bootstrap::icon('remove'))
					->url(\Koldy\Url::href('system', 'delete-user'))
					->param('id', $row['id'])
					->promptText("Do you really want to delete user {$row['username']}?")
					->size('xs')
					->color('red');
				
				return "{$edit} {$delete}";
			})
			->search(array('username', 'first_name', 'last_name'))
			->resultSet(User::resultSet())
			->handle();
	}
	
	/**
	 * Delets the user record from database
	 * @throws Exception
	 * @return Bootstrap\Response\ButtonRemote
	 */
	public function deleteUserAjax() {
		$params = Input::requireParams('id');
		$id = (int) $params->id;
		
		if ($id <= 0) {
			throw new Exception('Invalid ID');
		}
		
		$user = User::fetchOne($id);
		if ($user !== false) {
			$user->destroy();
			Log::info("Deleted user #{$id} {$user->username} from database");
		}
		
		return BootstrapUI::buttonRemoteResponse()
			->disableButton()
			->disableOtherButtons()
			->removeParentRow();
	}
	
	/**
	 * Get the user form for add/update
	 * @param array $values
	 */
	private function getUserForm(array $values = null) {
		if ($values === null) {
			$values = array(
				'id' => 0,
				'username' => null,
				'first_name' => null,
				'last_name' => null,
				'password' => null,
				'password2' => null,
				'account_type' => 'normal',
				'timezone' => 'Europe/London'
			);
		}
		
		$form = BootstrapUI::form()
			->horizontal(3)
			->add(Bootstrap::hiddenfield('id', $values['id']))
			->add(Bootstrap::textfield('username', $values['username'], 'username'))
			->add(Bootstrap::textfield('first_name', $values['first_name'], 'first name'))
			->add(Bootstrap::textfield('last_name', $values['last_name'], 'last name'))
			->add(Bootstrap::textfield('pass', null, 'password')->type('password'))
			->add(Bootstrap::textfield('pass2', null, 'password again')->type('password'))
			->add(Bootstrap::radio('account_type', array(
				'admin' => 'administrator',
				'normal' => 'normal user'		
			), 'account type', $values['account_type']))
			->add(BootstrapUI::select2('timezone', 'timezone', Timezone::$timezones, $values['timezone']))
			->addSubmit('Submit')
			->addButton(Bootstrap::anchor('Cancel', Url::href('system', 'users'))->asButton()->color('red'));
		
		return $form;
	}
	
	/**
	 * Show the page with user edit form
	 */
	public function editUserAction() {
		$id = (int) Url::getVar(2);
		
		$user = User::fetchOne($id);
		if ($user === false) {
			Application::throwError(404, "Can not find user");
		}
		
		$title = "Edit user {$user->username}";
		$elements = array(
			\Bootstrap::h(1, $title),
			\Bootstrap::panel("User #{$id}", $this->getUserForm($user->getData()))->color('blue')
		);
		
		if ($user->last_login !== null) {
			$info = \Bootstrap::alert('User logged in last time at ' .
					Misc::userDate('Y-m-d H:i:s', $user->last_login) . " from {$user->last_login_ip}"
			)->color('info');
			$elements[] = $info;
		}
		
		$content[] = Bootstrap::row()->add(8, $elements, 2);
		
		return View::create('base')
			->with('title', $title)
			->with('content', $content);
	}
	
	/**
	 * Update user settings if needed
	 */
	public function editUserAjax() {
		// TODO: Finish this
		$params = Input::requireParams('id');
		$id = (int) $params->id;
		
		$user = User::fetchOne($id);
		if ($user === false) {
			return BootstrapUI::formResponse()->failed('Invalid user');	
		}
		
		$validator = Validator::create(array(
			'id' => 'required|integer',
			'username' => "required|min:2|max:32|unique:\User,username,{$id},id",
			'first_name' => 'max:255',
			'last_name' => 'max:255',
			'pass' => 'min:5|max:255',
			'pass2' => 'identical:pass',
			'account_type' => 'required',
			'timezone' => 'required'
		));
		
		if ($validator->failed()) {
			return BootstrapUI::formResponse()->failedOn($validator);
		}
		
		$params = $validator->getParamsObj();
		
		$data = $validator->getParams();
		unset($data['id'], $data['pass'], $data['pass2']);
		
		if ($params->pass !== null && strlen($params->pass) >= 5) {
			$data['pass'] = md5($params->pass);
		}
		
		if ($user->save($data)) {
			Log::info("Updated user #{$id} {$params->username}");
		}
		
		return BootstrapUI::formResponse();
	}
	
	/**
	 * Show the page to add new user
	 */
	public function addUserAction() {
		$title = 'Add new user';
		$content = array(Bootstrap::row()->add(8, \Bootstrap::h(1, $title), 2));
		
		$content[] = Bootstrap::row()->add(8, Bootstrap::panel('New user', $this->getUserForm())->color('blue'), 2);
		
		return View::create('base')
			->with('title', $title)
			->with('content', $content);
	}
	
	/**
	 * Actually add new user into database
	 */
	public function addUserAjax() {
		$validator = Validator::create(array(
			'id' => 'is:0',
			'username' => "required|min:2|max:32|unique:\User,username",
			'first_name' => 'max:255',
			'last_name' => 'max:255',
			'pass' => 'required|min:5|max:255',
			'pass2' => 'identical:pass',
			'account_type' => 'required',
			'timezone' => 'required'
		));
		
		if ($validator->failed()) {
			return BootstrapUI::formResponse()->failedOn($validator);
		}
		
		$data = $validator->getParams();
		unset($data['id'], $data['pass2']);
		$data['pass'] = md5($data['pass']);
		
		$user = User::create($data);
		if ($user === false) {
			Log::error('Can not create new user');
			return BootstrapUI::formResponse()->failed('Something went wrong');
		}
		
		return BootstrapUI::formResponse()->redirect(Url::href('system', 'users'));
	}
}