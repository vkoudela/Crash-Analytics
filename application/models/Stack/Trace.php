<?php namespace Stack;

use Koldy\Db\Expr;
use Koldy\Db;
use Koldy\Db\Model;
use Koldy\Db\Select;
use Koldy\Log;
use \Status;

class Trace extends Model {
	
	/**
	 * Get stack trace ID
	 * @param string $stackTraceSummary
	 * @return int
	 */
	public function getId($stackTraceSummary, $createdAt = null) {
		$md5 = md5($stackTraceSummary);
		if (($e = \Stack\Trace::fetchOne(array('hash' => $md5))) === false) {
			$e = \Stack\Trace::create(array(
				'hash' => $md5,
				'summary' => $stackTraceSummary,
				'created_at' => $createdAt,
				'total' => 0
			));
		}
		
		return $e->id;
	}
	
	/**
	 * Recalculate the table totals
	 */
	public static function recalculate() {
		static::getAdapter()->query('
			INSERT INTO stack_trace (id, total)

				SELECT
					a.stack_trace_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN stack_trace st ON st.id = a.stack_trace_id
				GROUP BY a.stack_trace_id
			
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}
	
	/**
	 * Get the count of new stack traces since last login
	 * @param string $previousLogin
	 * @return int
	 */
	public static function getNewCount($previousLogin) {
		$query = static::query();
		
		$query->field(new Expr('COUNT(*)'), 'total')
			->where('created_at', $previousLogin, null, '>=');
		
		$records = $query->fetchAllObj();
		return (int) $records[0]->total;
	}
	
	/**
	 * Get the summary stack trace from full stack trace
	 * @param string $fullStackTrace
	 * @return string
	 */
	public static function getSummary($fullStackTrace) {
		$trace = explode("\n", $fullStackTrace);
		$newTrace = array();
		$fullLine = -1;
		
		for ($i = 0; $i < sizeof($trace); $i++) {
			$line = $trace[$i];
			$first = substr($line, 0, 1);
			if ($first != "\t") {
				$fullLine = 0;
			} else {
				$fullLine++;
			}
				
			if ($fullLine <= 2) {
				$newTrace[] = $line;
			}
		}
		
		$newTrace = implode("\n", $newTrace);
		
		if (preg_match_all('$@([0-9a-f]+)$', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], '@addr ', $newTrace);
			}
		}
		
		if (preg_match_all('$.java:([0-9]+)\)$', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], '.java)', $newTrace);
			}
		}
		
		if (preg_match_all('$\$([0-9]+)\($', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], '(', $newTrace);
			}
		}
		
		if (preg_match_all('$\$([0-9]+)$', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], '', $newTrace);
			}
		}
		
		if (preg_match_all('$\$([0-9]+)\.$', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], '.', $newTrace);
			}
		}
		
		if (preg_match_all('$\(Heap Size=([0-9]+)KB, Allocated=([0-9]+)KB, Bitmap Size=([0-9]+)KB\)$', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'Heap info', $newTrace);
			}
		}
		
		if (preg_match_all('|([A-Za-z]{3,9})://([-;:&=\+\$,\w]+@{1})?([-A-Za-z0-9\.]+)+:?(\d+)?((/[-\+~%/\.\w]+)?\??([-\+=&;%@\.\w]+)?#?([\w]+)?)?|', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'URL', $newTrace);
			}
		}
		
		if (preg_match_all('/mailto:[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'EMAIL', $newTrace);
			}
		}
		
		if (preg_match_all('/\#0x([0-9a-f]+)/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], '#0xHEX', $newTrace);
			}
		}
		
		if (preg_match_all('/=0x([0-9a-f]+)/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], '=0xHEX', $newTrace);
			}
		}
		
		if (preg_match_all('/line\ #([0-9]+)/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'line #X', $newTrace);
			}
		}
		
		if (preg_match_all('/Bad message id: ([0-9]+)/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'Bad message id: X', $newTrace);
			}
		}
		
		if (preg_match_all('/ListView\(([0-9]+),/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'ListView(X,', $newTrace);
			}
		}
		
		if (preg_match_all('/Neither user ([0-9]+) nor/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'Neither user nor', $newTrace);
			}
		}
		
		if (preg_match_all('/pid(=|\ )([0-9]+)/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'pid=PID', $newTrace);
				$pid = $match[2];
				$newTrace = str_replace($pid, 'PID', $newTrace);
			}
		}
		
		if (preg_match_all('/uid(=|\ )([0-9]+)/', $newTrace, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$newTrace = str_replace($match[0], 'uid=UID', $newTrace);
				$uid = $match[2];
				$newTrace = str_replace($uid, 'UID', $newTrace);
			}
		}
		
		return trim($newTrace);
	}
	
	/**
	 * Rebuild stack_trace table
	 * CAUTION!!!! If you have a lot of records, this could take hours or days do get done!
	 */
	public static function rebuild() {
		Status::calculationStarted();
		Status::setCalculationStatus('Initializing');
		Status::setCalculationStatus('Reseting stack_trace_ids to NULL');
		
		Db::query('UPDATE crash_archive SET stack_trace_id = NULL');
		
		Status::setCalculationStatus('Emptying stack_trace table');
		Db::query('TRUNCATE TABLE stack_trace');
		
		$stacks = array();
		
		// 		foreach (Stack\Trace::all() as $r) {
		// 			$stacks[$r['hash']] = (int) $r['id'];
		// 		}
		
		$total = \Crash\Archive::count();
		$index = 0;
		
		$start = 0;
		do {
			$query = new Select('crash_archive');
			$query
				->field('id')
				->where('id', '>', $start)
				->where('id', '<=', $start + 100000);
				
			Status::setCalculationStatus("Taking IDs from {$start} to " . ($start + 100000));
				
			$records = $query->fetchAll();
			$sizeofIds = count($records);
			foreach ($records as $r) {
				$id = (int) $r['id'];
				$crash = new \Crash\Archive(array('id' => $id));
				$stackTrace = $crash->getMeta('stack_trace');
				
				if ($stackTrace !== null) {
					
					$summary = static::getSummary($stackTrace);
					$md5 = md5($summary);
					
					if (isset($stacks[$md5])) {
						$stackTraceId = $stacks[$md5];
					} else {
						$tmp = \Stack\Trace::create(array(
							'hash' => $md5,
							'summary' => $summary,
							'created_at' => $crash->created_at
						));
						$stackTraceId = (int) $tmp->id;
						$stacks[$md5] = $stackTraceId;
					}
					
					$crash->stack_trace_id = $stackTraceId;
					$crash->save();
					
					Log::info("Updated #{$id} with stack={$stackTraceId}");
				} else {
					Log::info("Crash report #{$id} is skipped because stack_trace is missing in meta table");
				}

				if ($index % 25 == 0) {
					$percent = round($index / $total * 100, 2);
					Status::setCalculationStatus("Working on {$index}/{$sizeofIds} {$percent}%");
				}

				$index++;
			}
				
			$start += 100000;
		} while (sizeof($records) > 0);
		
		Status::setCalculationStatus('Started stack trace recalculation!');
		Log::info('Started recalculation of stack_trace counts');
		
		static::recalculate();
		
		Status::calculationFinished('all');
	}
}