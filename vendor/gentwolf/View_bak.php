<?php

namespace gentwolf;

use Exception;

class View {
    private $config = array();
    public $params = null;
    private $viewPath = '';

    function __construct() {
    	$this->viewPath = gentwolf::$modulePath . gentwolf::$module .'/view/';
    }

    public function __set($name, $value) {
        $this->params[$name] = $value;
    }

    public function __get($name) {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * 设置视图配置信息
     * @param array $config
     */
    public function setConfig($config) {
        $this->config = $config;
    }

    /**
     * 赋值到模板
     * @param $data
     * @param mixed $val
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
     * @param $tpl
     * @param mixed $data
     * @return string
     */
    public function fetch($tpl, $data = null) {
        $this->assign($data);

        return $this->compileTpl($tpl);
    }

    /**
     * 输出模板内容到浏览器
     * @param $tpl
     * @param mixed $data
     */
    public function render($tpl, $data = null) {
        echo $this->fetch($tpl, $data);
    }

    /**
     * 输出模板内容到变量
     * @param string $tpl
     * @return string
     * @throws Exception
     */
    private function fetchPartial($tpl) {
        ob_start();
        ob_implicit_flush(false);

        $filename = $this->viewPath . $tpl . $this->config['ext'];
        if (!is_file($filename)) throw new Exception('file not found '. $filename);

        include $filename;

        return ob_get_clean();
    }

    /**
     * 取include内容
     * @param $con
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
     * @param $tpl
     * @return mixed
     */
    private function compileTpl($tpl) {
        $con = $this->fetchPartial($tpl);

        //取出已经包含include的内容
        $con = $this->getInclude($con);

		//检测是否使用了layout
        $bl = preg_match('/^{{\s*extends\s+[\'\"](\S*)[\'\"]\s*}}/', $con, $out);
        if (!$bl) return $con;

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
		$layoutCon = preg_replace('/{{\s*block\s+[\'\"](.*)[\'\"]\s*}}/U', '', $layoutCon);

		return $layoutCon;
    }
}