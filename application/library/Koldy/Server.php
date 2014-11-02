<?php namespace Koldy;

/**
 * This is another utility class that will get you some info about the server
 * where your PHP scripts are running.
 *
 */
class Server {


	/**
	 * Get server load ... if linux, returns all three averages, if windows, returns
	 * average load for all CPU cores
	 *
	 * @return string|null
	 */
	public static function getServerLoad() {
		if (function_exists('sys_getloadavg')) {
			$a = sys_getloadavg();
			foreach ($a as $k => $v) {$a[$k] = round($v, 2);}
			return implode(', ', $a);
		} else {
			$os = strtolower(PHP_OS);
			if (strpos($os, 'win') === false) {
				if (@file_exists('/proc/loadavg') && @is_readable('/proc/loadavg')) {
					$load = file_get_contents('/proc/loadavg');
					$load = explode(' ', $load);
					return implode(',', $load);
				} else if (function_exists('shell_exec')) {
					$load = @shell_exec('uptime');
					$load = split('load average' . (PHP_OS == 'Darwin' ? 's' : '') . ':', $load);
					return implode(',', $load);
					//return $load[count($load)-1];
				} else {
					return null;
				}
			} else if (class_exists('COM')) {
				$wmi = new COM("WinMgmts:\\\\.");
				$cpus = $wmi->InstancesOf('Win32_Processor');

				$cpuload = 0;
				$i = 0;
				while ($cpu = $cpus->Next()) {
					$cpuload += $cpu->LoadPercentage;
					$i++;
				}

				$cpuload = round($cpuload / $i, 2);
				return $cpuload . '%';
			}
		}

		return null;
	}

}
