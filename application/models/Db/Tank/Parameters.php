<?php

class Application_Model_Db_Tank_Parameters extends Application_Model_Db_Abstract
{

    const TABLE_NAME = 'tank_parameters';

    static public function getAllowedParameters()
    {
        $parameters = array(
            'hit_points'            => 'Прочность',
            'weight'                => 'Масса',
            'load_limit'            => 'Предельная масса',
            'price'                 => 'Цена',
            'engine_power'          => 'Мощность двигателя',
            'speed_limit'           => 'Макс. скорость',
            'traverse_speed'        => 'Скорость поворота',
            'turret_traverse_speed' => 'Скорость пов. башни',
            'hull_armor'            => 'Броня корпуса',
            'turret_armor'          => 'Броня башни',
            'gun_name'              => 'Оружие',
            'ammunition'            => 'Боекомплект',
            'avg_damage'            => 'Урон',
            'armor_penetration'     => 'Бронепробиваемость',
            'rate_of_fire'          => 'Скорострельность',
            'view_range'            => 'Обзор',
            'signal_range'          => 'Дальность связи',
        );
        return $parameters;
    }

}