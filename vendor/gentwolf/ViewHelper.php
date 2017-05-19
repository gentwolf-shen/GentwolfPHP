<?php

namespace gentwolf;

class ViewHelper {
    public static function input($name, $options) {
        $html = '';

        $type = $options['type'];
        $value = isset($options['value']) ? $options['value'] : '';
        switch ($type) {
            case 'text':
            case 'number':
            case 'date':
                $html = self::text($name, $value, $options, $type);
                break;
            case 'textarea':
                $html = self::textarea($name, $value, $options);
                break;
            case 'category':
                $html = self::category($name, $value, $options);
                break;
            case 'radio':
                $html = self::radio($name, $value, $options);
                break;
            case 'editor':
                $html = self::editor($name, $value, $options);
                break;
            case 'checkbox':
                $html = self::checkbox($name, $value, $options);
                break;
        }

        return $html;
    }

    /**
     * 普通文本框
     * @param string $name
     * @param string $value
     * @param array $options
     * @param string $type
     * @return string
     */
    public static function text($name, $value, $options, $type = 'text') {
        $tmp[] = '<input type="'. $type .'" class="form-control '.
            'input-'. $name .'" name="data['. $name .']" id="'. $name .'" value="'. $value .'" ';
        $tmp[] = self::getValid($options['valid']);
        $tmp[] = ' />';
        $tmp[] = self::getFeedback();

        return implode('', $tmp);
    }

    /**
     * 文本域
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function textarea($name, $value, $options) {
        $tmp[] = '<textarea class="form-control input-'. $name .'" name="data['. $name .']" id="'. $name .'" ';
        $tmp[] = self::getValid($options['valid']);
        $tmp[] = '>'. $value .'</textarea>';
        $tmp[] = self::getFeedback();

        return implode('', $tmp);
    }

    /**
     * 分类选择框
     * @param $name
     * @param $value
     * @param $options
     * @return string
     */
    public static function category($name, $value, $options) {
        $tmp[] = '<select class="form-control input-'. $name .'" name="data['. $name .']" id="'. $name .'" ';

        $tmp[] = self::getValid($options['valid']);
        $tmp[] = ' >';

        $settings = $options['settings'];

        if (isset($settings['default'])) {
            $tmp[] = '<option value="'. $settings['default'][0] .'">'. $settings['default'][1] .'</option>';
        }

        if (isset($settings['topCategoryId'])) {
            $tmp[] = self::getCategory($settings['topCategoryId']);
        }

        $tmp[] = '</select>';
        $tmp[] = self::getFeedback();

        return implode('', $tmp);
    }

    /**
     * 从数据库中取分类
     * @param $parentId
     * @return bool|string
     */
    private static function getCategory($parentId) {
        $rows = SqlBuilder::instance()->select('id,name,parent_id AS pId')
            ->from('category')
            ->where(array('top_id' => $parentId))
            ->orderBy('show_order', 'DESC')
            ->fetchAll();
        return $rows ? self::createOption($rows, $parentId) : false;
    }

    /**
     * 生成多级分类
     * @param array $rows
     * @param int $parentId
     * @param string $indent
     * @return string
     */
    private static function createOption(&$rows, $parentId = 0, $indent = '|－') {
        $rs = false;
        foreach ($rows as $k => $v) {
            if ($parentId == $v['pId']) {
                $rs[] = '<option value="'. $v['id'] .'">'. $indent . $v['name'] .'</option>';
                unset($rows[$k]);
                $rs[] = self::createOption($rows, $v['id'], $indent . '－');
            }
        }
        return $rs ? implode('', $rs) : '';
    }

    /**
     * 单选
     * @param string $name
     * @param string|integer|mixed $value
     * @param $options
     * @return string
     */
    public static function radio($name, $value, $options) {
        $tmp = false;
        $settings = $options['settings'];

        $tmp[] = '<div class="radioHolder" ';
        $tmp[] = self::getValid($options['valid']);
        $tmp[] = '>';

        switch ($settings['type']) {
            case 'config':
                $tmp[] = self::getRadioFromConfig($name, $value, $settings);
                break;
        }
        $tmp[] = '</div>';

        return implode('', $tmp);
    }

    /**
     * 从字典配置中构造单选
     * @param string $name
     * @param string $value
     * @param array $settings
     * @return string
     */
    private static function getRadioFromConfig($name, $value, $settings) {
        $tmp = [];
        $items = Gentwolf::loadConfig($settings['name'], $settings['key']);
        foreach ($items as $v => $k) {
            $sel = ($v == $value) ? ' checked': '';
            $tmp[] = '<label><input class="input-'. $name .'" type="radio" name="data['. $name .']" '. $sel .' value="'. $v .'"> '. $k .'</label>';
        }
        return implode('&nbsp; &nbsp; ', $tmp);
    }

    /**
     * 富文本编辑器
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function editor($name, $value, $options) {
        $tmp[] = '<textarea id="'. $name .'" name="data['. $name .']" class="editor input-'. $name .'" ';
        $tmp[] = self::getValid($options['valid']);
        $tmp[] = ' data-height="'. $options['settings']['height'] .'" >'. $value .'</textarea>';

        return implode('', $tmp);
    }

    /**
     * 生成输出文本
     * @param string $text
     * @param array $item
     * @return string|mixed
     */
    public static function field($text, $item) {
        if ('' != $text && null !== $text && false !== $text) {
            if (isset($item['callback']) && is_array($item['callback'])) {
                $callback = $item['callback'];
                if ('config' == $callback['type']) {
                    $arr = Gentwolf::loadConfig($callback['name'], $callback['key']);
                    $text = $arr[$text];
                } else if ('function' == $callback['type']) {
                    $params = explode(',', $callback['params']);
                    foreach ($params as &$value) {
                        $value = trim($value);
                        if ('{VALUE}' == $value) {
                            $value = $text;
                        }
                    }
                    $text = call_user_func_array($callback['name'], $params);
                }
            }
        }

        return $text;
    }

    /**
     * 生成输出属性
     * @param array $options
     * @return string
     */
    public static function attr($options) {
        $str = '';
        if ($options) {
            $tmp = false;
            foreach ($options as $k => $v) {
                $tmp[] = $k .'="'. $v .'"';
            }
            $str = implode(' ', $tmp);
        }
        return $str;
    }

    /**
     * 生成多选框
     * @param string $name
     * @param array $value
     * @param array $options
     * @return string
     */
    public static function checkbox($name, $value, $options) {
        $settings = $options['settings'];

        $value = is_array($value) ? $value : array();
        $html[] = '<div class="checkboxHolder form-control" id="checkbox-'. $name .'" data-name="'. $name .'" ';
        $html[] = self::getValid($options['valid']);
        $html[] = '>';
        switch ($settings['type']) {
            case 'db':
                $html[] = self::getCheckboxFromDB($name, $value, $settings);
                break;
            case 'config':
                break;
        }
        $html[] = '</div>';
        $html[] = self::getFeedback();

        return implode('', $html);
    }

    /**
     * 从数据库中生成多选框
     * @param $name
     * @param $value
     * @param $settings
     * @return string
     */
    private static function getCheckboxFromDB($name, $value, $settings) {
        $tmp = false;
        $rows = SqlBuilder::instance()->select($settings['field'])
                    ->from($settings['table'])
                    ->where($settings['where'])
                    ->orderBy($settings['order'])
                    ->fetchAll();
        if ($rows) {
            foreach ($rows as $row) {
                $sel = in_array($row['k'], $value) ? 'checked' : '';
                $tmp[] = '<label><input class="input-'. $name .'" type="checkbox" '.
                    'name="data['. $name .'][]" value="'. $row['k'] .'" '. $sel .' /> '. $row['v'] . $sel .'</label>';
            }
        }

        return $tmp ? implode(' &nbsp; ', $tmp) : '';
    }

    /**
     * 取校验字段
     * @param array $options
     * @return string
     */
    private static function getValid($options) {
        $tmp = false;
        foreach ($options as $k => $v) {
            $tmp[] = $k .'="'. $v .'"';
        }
        return implode(' ', $tmp);
    }

    private static function getFeedback() {
        return '<span class="glyphicon form-control-feedback" aria-hidden="true"></span>';
    }
}