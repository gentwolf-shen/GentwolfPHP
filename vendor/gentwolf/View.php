<?php

namespace gentwolf;

use Exception;

class View {
    private $config = array();
    public $params = [];
    private $viewPath = '';

    function __construct($config) {
		$this->config = $config;
    	$this->viewPath = gentwolf::$modulePath . gentwolf::$module .'/view/';
    }

    /*
     * 设置变量
     */
    public function __set($name, $value) {
        $this->params[$name] = $value;
    }

    public function __get($name) {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

	/**
	 * 清除缓存
	 */
    public function clear() {
    	$items = @scandir($this->config['cachePath']);
    	if ($items) {
    		foreach ($items as $item) {
    			if ($item == '.' || $item == '..') continue;
    			unlink($this->config['cachePath'] . $item);
			}
		}
	}

    /**
     * 设置变量
     * @param array | string $data
     * @param string $val
     */
    public function assign($data, $val = null) {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $this->params[$key] = $val;
            }
        } else {
            $this->params[$data] = $val;
        }
    }

    /**
     * 取出模板内容
     * @param string $tpl
     * @param array $data
     * @return string
     */
    public function fetch($tpl, $data = null) {
		$this->assign($data);

		ob_start();
		ob_implicit_flush(false);

		include $this->compileTpl($tpl);

		return ob_get_clean();
    }

    /**
     * 输出模板内容到浏览器
     * @param string $tpl
     * @param array $data
     */
    public function render($tpl, $data = null) {
		$this->assign($data);

		include $this->compileTpl($tpl);
    }

	/**
	 * 取出模板内容
	 * @param string $tpl
	 * @return bool|string
	 * @throws Exception
	 */
    private function fetchPartial($tpl) {
        $filename = $this->viewPath . $tpl . $this->config['ext'];
        if (!is_file($filename)) throw new Exception('file not found '. $filename);

		return file_get_contents($filename);
    }

    /**
     * 取include内容
     * @param string $con
     * @return string
     */
    private function getInclude($con) {
        $bl = preg_match_all('/{{\s*include\s+[\'\"](\S*)[\'\"]\s*}}/U', $con, $out);
        if ($bl) {
            foreach ($out[1] as $k => $tpl) {
                $tplCon = $this->fetchPartial($tpl);
                $con = str_replace($out[0][$k], $tplCon, $con);
            }
        }
        return $con;
    }

    /**
     * 解析模板标签
     * @param string $tpl
     * @return string view cache filename
     */
    private function compileTpl($tpl) {
		$filename = $this->config['cachePath'] . md5($tpl) . $this->config['ext'];

		if (!is_file($filename) || filemtime($filename) < time() - $this->config['cacheTime']) {
			$con = $this->fetchPartial($tpl);

			//取出已经包含include的内容
			$con = $this->getInclude($con);

			//检测是否使用了layout
			$bl = preg_match('/^{{\s*extends\s+[\'\"](\S*)[\'\"]\s*}}/', $con, $out);
			if ($bl) {
				//取出layout内容
				$layoutCon = $this->fetchPartial($out[1]);
				unset($out);

				//取出layout中的include内容
				$layoutCon = $this->getInclude($layoutCon);

				//取出block内容
				$bl = preg_match_all('/{{\s*block\s+[\'\"](\S*)[\'\"]\s*}}([\s\S\w\W]*){{\s*\/block\s*}}/U', $con, $blocks);
				if ($bl) {
					//替换layout中block内容
					foreach ($blocks[2] as $k => $v) {
						$layoutCon = preg_replace('/{{\s*block\s+[\'\"]'.  $blocks[1][$k] .'[\'\"]\s*}}/U', trim($v), $layoutCon);
					}
					unset($blocks);
				}

				//清除layout中未被替换的block
				$con = preg_replace('/{{\s*block\s+[\'\"](.*)[\'\"]\s*}}/U', '', $layoutCon);
			}

			$con = strtr($con, [
				'{{' => '<?php',
				'{=' => '<?=',
				'}}' => '?>',
			]);


			file_put_contents($filename, $con);
		}

		return $filename;
    }
}