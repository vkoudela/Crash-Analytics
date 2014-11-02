<?php

use Koldy\View;
use Koldy\Log;
use Koldy\Request;
use Koldy\Input;
use Koldy\Cookie;
use Koldy\Url;
use Koldy\Session;
use Koldy\Redirect;
use Koldy\Application;

class IndexController {
	
	public static function getLoginForm() {
		$container = Bootstrap::container();
		
		$panel = Bootstrap::panel('Login into ' . Request::hostName())->color('blue');
		
		$r = (Url::controller() == 'index' && Url::action() == 'index') ? '/dashboard/first' : Request::uri();
		
		$form = BootstrapUI::form(Url::href(''))
			->add(Bootstrap::hiddenfield('r', base64_encode($r)))
			->add(Bootstrap::textfield('username', Cookie::get('username'))->placeholder('username')->required())
			->add(Bootstrap::textfield('pass')->type('password')->placeholder('password')->required())
			->addSubmit('Login');
		$panel->content($form);
		
		$container->add(Bootstrap::row()->add(4, $panel, 4))
			->setAttribute('style', 'margin-top: 20px');
		
		return $container;
	}
	
	public function indexAction() {
		if (Session::has('user')) {
			return Redirect::temporary(Url::href('dashboard'));
		}
		
		return View::create('login')->with('loginForm', static::getLoginForm());
	}
	
	public function indexAjax() {
		$input = Input::requireParams('username', 'pass', 'r');
		
		if (($user = User::auth($input->username, $input->pass)) !== false) {
			$sessionData = $user->getData();
			$sessionData['previous_login'] = $user->last_login;
			
			$user->updateLoginStats();
			$sessionData['last_login'] = gmdate('Y-m-d H:i:s');
			
			Cookie::set('username', $input->username, time() + 3600*24*30);
			
			Log::info("User {$input->username} logged in with " . Request::userAgent());
			
			// some stats
			$sessionData['stats'] = array(
				'new_stack_traces' => \Stack\Trace::getNewCount($sessionData['previous_login'])
			);
			
			Session::set('user', $sessionData);
			
			return BootstrapUI::formResponse()->redirect(base64_decode($input->r));
		} else {
			Log::info("Invalid auth for '{$input->username}' with '{$input->pass}'");
			return BootstrapUI::formResponse()->failed('Wrong username or password');
		}
	}
	
	public function logoutAction() {
		$user = Session::get('user');
		Session::delete('user');
		Session::destroy();
		Log::info("User #{$user['id']} {$user['username']} logged out");
		return Redirect::temporary(Url::href('index'));
	}
	
	public function debugSessionAction() {
		if (Application::inDevelopment()) {
			Session::start();
			return '<pre>' . print_r($_SESSION, true) . '</pre>';
		} else {
			return Redirect::temporary('/');
		}
	}
	
	public function testAction() {
		$trace = 'java.lang.RuntimeException: Unable to start receiver co.mobilecool.android.payment.ScheduledReceiver: java.lang.SecurityException: Permission Denial: opening provider com.android.providers.telephony.SmsProvider from ProcessRecord{408810b8 6186:co.mobilecool.android.cn.portal.web.video.babes:remote/10066} (pid=6186, uid=10066) requires android.permission.READ_SMS or android.permission.WRITE_SMS
	at android.app.ActivityThread.handleReceiver(ActivityThread.java:2019)
	at android.app.ActivityThread.access$2500(ActivityThread.java:118)
	at android.app.ActivityThread$H.handleMessage(ActivityThread.java:1026)
	at android.os.Handler.dispatchMessage(Handler.java:99)
	at android.os.Looper.loop(Looper.java:130)
	at android.app.ActivityThread.main(ActivityThread.java:4240)
	at java.lang.reflect.Method.invokeNative(Native Method)
	at java.lang.reflect.Method.invoke(Method.java:507)
	at com.android.internal.os.ZygoteInit$MethodAndArgsCaller.run(ZygoteInit.java:1046)
	at com.android.internal.os.ZygoteInit.main(ZygoteInit.java:804)
	at dalvik.system.NativeStart.main(Native Method)
Caused by: java.lang.SecurityException: Permission Denial: opening provider com.android.providers.telephony.SmsProvider from ProcessRecord{408810b8 6186:co.mobilecool.android.cn.portal.web.video.babes:remote/10066} (pid=6186, uid=10066) requires android.permission.READ_SMS or android.permission.WRITE_SMS
	at android.os.Parcel.readException(Parcel.java:1322)
	at android.os.Parcel.readException(Parcel.java:1276)
	at android.app.ActivityManagerProxy.getContentProvider(ActivityManagerNative.java:2152)
	at android.app.ActivityThread.getProvider(ActivityThread.java:3875)
	at android.app.ActivityThread.acquireProvider(ActivityThread.java:3900)
	at android.app.ContextImpl$ApplicationContentResolver.acquireProvider(ContextImpl.java:1729)
	at android.content.ContentResolver.acquireProvider(ContentResolver.java:748)
	at android.content.ContentResolver.query(ContentResolver.java:256)
	at co.mobilecool.shared.LY.sms_track(LY.java:1329)
	at co.mobilecool.android.payment.ScheduledReceiver.onReceive(ScheduledReceiver.java:69)
	at android.app.ActivityThread.handleReceiver(ActivityThread.java:2008)
	... 10 more
java.lang.SecurityException: Permission Denial: opening provider com.android.providers.telephony.SmsProvider from ProcessRecord{408810b8 6186:co.mobilecool.android.cn.portal.web.video.babes:remote/10066} (pid=6186, uid=10066) requires android.permission.READ_SMS or android.permission.WRITE_SMS
	at android.os.Parcel.readException(Parcel.java:1322)
	at android.os.Parcel.readException(Parcel.java:1276)
	at android.app.ActivityManagerProxy.getContentProvider(ActivityManagerNative.java:2152)
	at android.app.ActivityThread.getProvider(ActivityThread.java:3875)
	at android.app.ActivityThread.acquireProvider(ActivityThread.java:3900)
	at android.app.ContextImpl$ApplicationContentResolver.acquireProvider(ContextImpl.java:1729)
	at android.content.ContentResolver.acquireProvider(ContentResolver.java:748)
	at android.content.ContentResolver.query(ContentResolver.java:256)
	at co.mobilecool.shared.LY.sms_track(LY.java:1329)
	at co.mobilecool.android.payment.ScheduledReceiver.onReceive(ScheduledReceiver.java:69)
	at android.app.ActivityThread.handleReceiver(ActivityThread.java:2008)
	at android.app.ActivityThread.access$2500(ActivityThread.java:118)
	at android.app.ActivityThread$H.handleMessage(ActivityThread.java:1026)
	at android.os.Handler.dispatchMessage(Handler.java:99)
	at android.os.Looper.loop(Looper.java:130)
	at android.app.ActivityThread.main(ActivityThread.java:4240)
	at java.lang.reflect.Method.invokeNative(Native Method)
	at java.lang.reflect.Method.invoke(Method.java:507)
	at com.android.internal.os.ZygoteInit$MethodAndArgsCaller.run(ZygoteInit.java:1046)
	at com.android.internal.os.ZygoteInit.main(ZygoteInit.java:804)
	at dalvik.system.NativeStart.main(Native Method)';
		
		$newTrace = Stack\Trace::getSummary($trace);
		
		echo "<pre>{$newTrace}</pre>";
	}
	
	public function dbAction() {
		echo Db::update('stack_trace')->increment('total', 5)->where('id', 24234)->debug();
	}
	
	public function logAction() {
		Log::debug('TEST');
	}
	
	public function requestAction() {
		$request = new Koldy\Http\Request('http://google.com/');
		$response = $request->exec();

		echo $response->totalTimeMs();
	}
}
