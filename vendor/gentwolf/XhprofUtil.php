<?php

namespace gentwolf;

class XhprofUtil {
	private static $mode = '';

	public static function startXhprof($mode) {
		self::$mode = $mode;
		if (self::$mode != '' && function_exists('xhprof_disable')) {
			xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
		}
	}

	public static function stopXhprof() {
		if (self::$mode != '') {
			$data = xhprof_disable();

			require_once Gentwolf::$libPath .'xhprof/utils/xhprof_runs.php';
			$xhprof = new \XHProfRuns_Default();
			$runId = $xhprof->save_run($data, 'gentwolf');
			
			if (self::$mode == 'link') {
				echo '<a href="/xhprof/?run='. $runId .'&source=gentwolf" target="_blank">xhrpof</a>';
			}
		}
	}
}