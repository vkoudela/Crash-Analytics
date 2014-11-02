<?php

use Koldy\View;
use Koldy\Url;
use Koldy\Timezone;
use Koldy\Session;
use Koldy\Cache;

class DashboardController extends AbstractSessionController {
	
	public function indexAction() {
		$calculationInProgress = Status::isCalculationInProgress();
		$elements = array();
		$user = Session::get('user');
		
		// Check for currently ongoing calculation
		if ($calculationInProgress) {
			$alert = Bootstrap::alert('<img src="' . Url::link('img/alert.png') . '" /> Data calculation is in progress! Reports might be slower then usual! Status: ' . Status::getCalculationStatus())
				->color('warning')
				->dismissable();
			
			$elements[] = $alert;
		}
		
		$title = Bootstrap::h(1, 'Welcome ' . ($this->getUser('first_name') === null ? $this->getUser('username') : $this->getUser('first_name')))
			->secondaryText('Your timezone is <a href="' . Url::href('profile') . '">' . $user['timezone'] . '</a>, ' . Timezone::date($user['timezone'], 'M dS, H:i:s'));
		$elements[] = Bootstrap::row()->add(12, $title);
		
		// last reports row
		$row = Bootstrap::row();

		$minutes = 15;
		$panel = Bootstrap::panel("Reports per minute in last {$minutes} minutes", new Chart\RequestsPerSecond($minutes))->color('blue');
		$row->add(4, $panel);
		
		$panel = Bootstrap::panel("Problematic apps in last {$minutes} minutes", new Chart\ProblematicApps($minutes))->color('blue');
		$row->add(4, $panel);
		
		$panel = Bootstrap::panel("Problematic brands in last {$minutes} minutes", new Chart\ProblematicBrands($minutes))->color('blue');
		$row->add(4, $panel);
		
		$elements[] = $row;
		
		// quick reports
		$row = Bootstrap::row();
		
			// quick links
			$listGroup = Bootstrap::listGroup();
			foreach (Menu::getReportLinks() as $url => $text) {
				$listGroup->addLink($url, $text);
			}
			
			$row->add(4, Bootstrap::panel('Quick links', $listGroup));
			
			// countries
			$panel = Bootstrap::panel("Reports from countries in last {$minutes} minutes", new Chart\ProblematicCountries($minutes))->color('blue');
			$row->add(4, $panel);
			
			// OS versions
			$panel = Bootstrap::panel("OS and version in last {$minutes} minutes", new Chart\ProblematicOsVersions($minutes))->color('blue');
			$row->add(4, $panel);
		
			$elements[] = $row;

		
		$dashboardDaysCacheKey = "DashboardDays-last-10-days-offset-0min";
		if (!$calculationInProgress || Cache::has($dashboardDaysCacheKey)) {
			// dahsboard last 10 days
			$row = Bootstrap::row();
			$row->add(12, Bootstrap::panel('Number of crash reports in last 10 days', new Chart\DashboardDays(10))->color('blue'));
			$elements[] = $row;
		}

		return View::create('base')
			->with('title', 'Dashboard')
			->with('content', $elements);
	}
	
	public function firstAction() {
		return $this->indexAction();
	}
}