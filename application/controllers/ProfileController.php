<?php

use Koldy\View;
use Koldy\Timezone;
use Koldy\Input;
use Koldy\Validator;
use Koldy\Session;

class ProfileController extends AbstractSessionController {
	
	public function indexAction() {
		$content = Bootstrap::row();
		
			$panel = Bootstrap::panel('My profile');
			
				$form = BootstrapUI::form()
					->horizontal(3)
					->add(Bootstrap::textfield('username', $this->getUser('username'), 'username')->required())
					->add(Bootstrap::textfield('first_name', $this->getUser('first_name'), 'first name'))
					->add(Bootstrap::textfield('last_name', $this->getUser('last_name'), 'last name'))
					->add(Bootstrap::textfield('password', null, 'password')->type('password'))
					->add(Bootstrap::textfield('password2', null, 'password again')->type('password'))
					->add(BootstrapUI::select2('timezone', 'time zone', Timezone::$timezones, $this->getUser('timezone')))
					->addSubmit('Update');
		
			$panel->content($form);
		
		$content->add(6, $panel, 3);
		
		return View::create('base')
			->with('title', 'My profile')
			->with('content', $content);
	}
	
	public function indexAjax() {
		$input = Input::requireParams('username');
		$username = $input->username;
		$id = (int) $this->getUser('id');
		
		$validator = Validator::create(array(
			'username' => "required|unique:\User,username,{$id},id",
			'first_name' => 'max:80',
			'last_name' => 'max:160',
			'password' => 'min:5|max:32',
			'password2' => 'identical:password',
			'timezone' => 'required|max:80'
		));
		
		if ($validator->failed()) {
			return BootstrapUI::formResponse()->failedOn($validator);
		}
		
		$input = $validator->getParamsObj();
		$user = User::fetchOne($this->getUser('id'));
		$userSessionData = Session::get('user');
		$refresh = ($user->timezone != $input->timezone);
		
		$user->username = $input->username;
		$user->first_name = $input->first_name;
		$user->last_name = $input->last_name;
		$user->timezone = $input->timezone;
		
		if ($input->password !== null && strlen($input->password) >= 5) {
			$user->pass = md5($input->password);
		}
		
		$user->save();
		
		foreach ($user->getData() as $key => $value) {
			$userSessionData[$key] = $value;
		}
		
		Session::set('user', $userSessionData);
		
		if ($refresh) {
			return BootstrapUI::formResponse()->refresh();
		} else {
			return BootstrapUI::formResponse();
		}
	}
}