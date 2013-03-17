<?php
class My_View_Helper_TankParameters extends Zend_View_Helper_Abstract
{

    protected $_allowedParameters = array(
        'hit_points',     'weight',                'load_limit',
        'price',          'engine_power',          'speed_limit',
        'traverse_speed', 'turret_traverse_speed', 'hull_armor',
        'turret_armor',   'gun_name',              'ammunition',
        'avg_damage',     'armor_penetration',     'rate_of_fire',
        'view_range',     'signal_range',
    );

    public function tankParameters()
    {
        return $this;
    }

    public function url($nation, $code)
    {
        $url = 'http://worldoftanks.ru/encyclopedia/vehicles/' . urlencode($nation) . '/' . urlencode($code) . '/';
        return $url;
    }

    public function format($name, $value)
    {
        if (in_array($name, $this->_allowedParameters) !== false) {
            return call_user_func(array($this, '_format_' . $name), $value);
        }
        return '';
    }

    protected function _format_hit_points($value)
    {
        return $value . ' hp';
    }

    protected function _format_weight($value)
    {
        return sprintf("%01.2f", ($value / 100)) . ' т';
    }

    protected function _format_load_limit($value)
    {
        return sprintf("%01.2f", ($value / 100)) . ' т';
    }

    protected function _format_price($value)
    {
        if ($value > 0) {
            $value = ($value / 100) . ' кредитов';
        } else {
            $value = 'Подарочный';
        }
        return $value;
    }

    protected function _format_engine_power($value)
    {
        return $value . ' л.с.';
    }

    protected function _format_speed_limit($value)
    {
        return $value . ' км/ч';
    }

    protected function _format_traverse_speed($value)
    {
        return $value . ' град/сек';
    }

    protected function _format_turret_traverse_speed($value)
    {
        return $value . ' град/сек';
    }

    protected function _format_hull_armor($value)
    {
        $value = sprintf('%09s', $value);
        $value = str_split($value, 3);
        $value = array_map('intval', $value);
        return implode('/', $value);
    }

    protected function _format_turret_armor($value)
    {
        $value = sprintf('%09s', $value);
        $value = str_split($value, 3);
        $value = array_map('intval', $value);
        return implode('/', $value);
    }

    protected function _format_gun_name($value)
    {
        return $value;
    }

    protected function _format_ammunition($value)
    {
        return $value . ' шт';
    }

    protected function _format_avg_damage($value)
    {
        return $value . ' HP';
    }

    protected function _format_armor_penetration($value)
    {
        return $value . ' мм';
    }

    protected function _format_rate_of_fire($value)
    {
        return $value . ' выстр/мин';
    }

    protected function _format_view_range($value)
    {
        return $value . ' м';
    }

    protected function _format_signal_range($value)
    {
        return $value . ' м';
    }

}