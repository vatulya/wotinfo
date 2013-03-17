<?php

require_once 'bootstrap.php';

include('simple_html_dom.php');

$arguments = new Zend_Console_Getopt(array(
    'help|h'   => 'Help. Show all allowed params.',
    'skip-i'   => 'How many need to skip.',
    'limit-i'  => 'Limit to insert.',
), $_SERVER['argv']);

$arguments->parse();

if ($arguments->help) {
    die($arguments->getUsageMessage());
}

/** GET ARGS **/

$skip  = (int)$arguments->skip;
$limit = (int)$arguments->limit;

/** GO-GO-GO **/

print 'Script Grabber started at ' . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

$grabber = new Grabber(array('skip' => $skip, 'limit' => $limit));
$grabber->grab();

print 'Script Grabber at ' . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

/*******************************************************/

function romanToArabic($roman) {
    $romans = array(
        'M'  => 1000,
        'CM' => 900,
        'D'  => 500,
        'CD' => 400,
        'C'  => 100,
        'XC' => 90,
        'L'  => 50,
        'XL' => 40,
        'X'  => 10,
        'IX' => 9,
        'V'  => 5,
        'IV' => 4,
        'I'  => 1,
    );

    $result = 0;

    foreach ($romans as $key => $value) {
        while (strpos($roman, $key) === 0) {
            $result += $value;
            $roman = substr($roman, strlen($key));
        }
    }
    return  $result;
}

class Grabber
{

    protected $_skip  = 0;
    protected $_limit = null;

    protected $_modelTank;

    protected $_client;

    protected $_allowedTankTypesAliases = array(
        'b-encyclopedia-type_lighttank'  => 1,
        'b-encyclopedia-type_mediumtank' => 2,
        'b-encyclopedia-type_heavytank'  => 3,
        'b-encyclopedia-type_at-spg'     => 4,
        'b-encyclopedia-type_spg'        => 5,
    );

    protected $_allowedNations = array();

    public function __construct(array $options = array())
    {
        if (isset($options['skip'])) {
            $this->_skip = (int)$options['skip'];
        }
        if (isset($options['limit'])) {
            $this->_limit = (int)$options['limit'];
        }
        $this->_modelTank = new Application_Model_Tank();
    }

    public function grab()
    {
        $tanks = $this->_getAllTankUrls();
        print PHP_EOL . 'URLs have grabbed. Time: ' . date('Y-m-d H:i:s') . PHP_EOL;
        list($tanks, $parameters) = $this->_getTankParameters($tanks);
        print PHP_EOL . 'Tank Parameters have grabbed. Time: ' . date('Y-m-d H:i:s') . PHP_EOL;
        $result = $this->_insertIntoDatabase($tanks, $parameters);
        print PHP_EOL . 'Tanks have inserted into database. Time: ' . date('Y-m-d H:i:s') . PHP_EOL;
    }

    protected function _getAllowedNations()
    {
        if ( ! $this->_allowedNations) {
            $allowedNations = $this->_modelTank->getAllNations();
            foreach ($allowedNations as $nation) {
                $this->_allowedNations[$nation['code']] = $nation;
            }
        }
        return $this->_allowedNations;
    }

    protected function _getAllTankUrls()
    {
        $allowedTankTypes = array_keys($this->_allowedTankTypesAliases);
        $tanks = array();

        $client = $this->_getClient();
        $client->setUri('http://worldoftanks.ru/encyclopedia/vehicles/');
        /** @var $response Zend_Http_Response */
        $response = $client->request('GET');
        $body = $response->getBody();
        $html = new simple_html_dom();
        $html->load($body);
        $trees = $html->find('.i-three-coll');
        foreach ($trees as $tree) {
            $typeEl = $tree->children(0);
            $classes = explode(' ', $typeEl->class);
            $compare = array_intersect($classes, $allowedTankTypes);
            $tankType = null;
            if ( ! empty($compare)) {
                $tankType = $this->_allowedTankTypesAliases[current($compare)];
            }
            if ( ! $tankType) {
                continue;
            }

            $items = $tree->find('.b-encyclopedia-list_linc');
            foreach ($items as $item) {
                $tank = array();
                $href = $item->href;
                $lvl = $item->children(0);
                $tank['tank_type'] = $tankType;
                $tank['href']      = $href;
                $tank['lvl']       = romanToArabic($lvl->plaintext);
                $tanks[] = $tank;
            }

        }
        return $tanks;
    }

    protected function _getTankParameters(array $tanks)
    {
        $allowedNations = $this->_getAllowedNations();
        $completedTanks = array();
        $completedTankParameters = array();

        $toParse = array(
            1 => 'hit_points',
            2 => 'weight',
//            1 => 'load_limit',
            3 => 'price',
//            4 => 'Crew'
            5 => 'engine_power',
            6 => 'speed_limit',
            7 => 'traverse_speed',
            8 => 'turret_traverse_speed',
            9 => 'hull_armor',
            10 => 'turret_armor', // optional !
            11 => 'gun_name',
            12 => 'ammunition',
            13 => 'avg_damage',
            14 => 'armor_penetration',
            15 => 'rate_of_fire',
            16 => 'view_range',
            17 => 'signal_range',
        );

        $total = count($tanks);
        $count = 0;

        foreach ($tanks as $key => $tank) {

            if ($this->_skip > 0) {
                $this->_skip--;
                continue;
            }
            if ($this->_limit > 0 && $count >= $this->_limit) {
                break;
            }

            /*************/

            $toParseCopy = $toParse;

            preg_match('/\/vehicles\/([^\/]+)\/([^\/]+)/', $tank['href'], $matches);
            $nationCode = $matches[1];
            $tankCode = $matches[2];
            if ( ! isset($allowedNations[$nationCode])) {
                $nationCode = '';
            }
            if ( ! $nationCode || ! $tankCode) {
                continue;
            }
            $nationId = $allowedNations[$nationCode]['id'];
            $temporaryId = $nationCode . '|' . $tankCode;

            /*************/

            $client = $this->_getClient();
            $client->setUri('http://worldoftanks.ru/' . $tank['href']);
            $response = $client->request('GET');
            $body = $response->getBody();
            $html = new simple_html_dom();
            $html->load($body);

            $content = $html->find('.l-content', 0);

            $fullName = $content->children(0)->plaintext;
            $fullName = explode(':', $fullName);
            $fullName = trim($fullName[1]); // [0] - Text Tank Type like 'Tank Destroyer'

            $parameters = array();
            $blocks = $content->find('.t-encyclopedia_item__big');

            if (count($blocks) < 18) {
                $parameters['turret_armor'] = '000000000';
                $tmp = array();
                $found = false;
                foreach ($toParseCopy as $key => $value) {
                    if ($value == 'turret_armor') {
                        $found = true;
                    } else {
                        if ($found) {
                            $key--;
                        }
                        $tmp[$key] = $value;
                    }
                }
                $toParseCopy = $tmp;
            }

            $isPremium = 0;
            foreach ($blocks as $k => $block) {

                if ( ! isset($toParseCopy[$k])) {
                    continue;
                }
                $field = $toParseCopy[$k];
                $value = $block->plaintext;
                if ($field == 'price') {
                    $block = $block->children(0);
                    if ( ! $block || $block->class != 'currency-credit') {
                        $value = 'Gift Tank';
                        $isPremium = 1;
                    } else {
                        $value = $block->plaintext;
                    }
                }
                $parameters[$field] = $this->_unformatValue($field, $value);
                if ($field == 'weight') {
                    // sorry
                    list($weight, $loadLimit) = explode('/', $parameters[$field]);
                    $weight    = (float)$weight;
                    $loadLimit = (float)$loadLimit;
                    $weight    = $weight * 100;
                    $loadLimit = $loadLimit * 100;
                    $parameters['weight']     = $weight;
                    $parameters['load_limit'] = $loadLimit;
                }

            }

            /*************/

            $completedTank = array(
                'temporary_id' => $temporaryId,
                'code'         => $tankCode,
                'full_name'    => $fullName,
                'lvl'          => $tank['lvl'],
                'tank_type_id' => $tank['tank_type'],
                'nation_id'    => $nationId,
                'premium'      => $isPremium,
                'order_number' => $key,
            );
            $completedTankParameter = array(
                'temporary_id'          => $temporaryId,
                'hit_points'            => $parameters['hit_points'],
                'weight'                => $parameters['weight'],
                'load_limit'            => $parameters['load_limit'],
                'price'                 => $parameters['price'],
                'engine_power'          => $parameters['engine_power'],
                'speed_limit'           => $parameters['speed_limit'],
                'traverse_speed'        => $parameters['traverse_speed'],
                'turret_traverse_speed' => $parameters['turret_traverse_speed'],
                'hull_armor'            => $parameters['hull_armor'],
                'turret_armor'          => $parameters['turret_armor'],
                'gun_name'              => $parameters['gun_name'],
                'ammunition'            => $parameters['ammunition'],
                'avg_damage'            => $parameters['avg_damage'],
                'armor_penetration'     => $parameters['armor_penetration'],
                'rate_of_fire'          => $parameters['rate_of_fire'],
                'view_range'            => $parameters['view_range'],
                'signal_range'          => $parameters['signal_range'],
            );

            /*************/

            $completedTanks[$completedTank['temporary_id']]                   = $completedTank;
            $completedTankParameters[$completedTankParameter['temporary_id']] = $completedTankParameter;

            print 'Progress: ' . $count++ . '/' . $total . '.' . "            \r";
        }
        print PHP_EOL;
        return array($completedTanks, $completedTankParameters);
    }

    protected function _insertIntoDatabase(array $tanks, array $parameters)
    {
        $count = 0;
        if (1) {
            $this->_modelTank->truncateTanks();
        }
        foreach ($tanks as $tank) {
            $temporaryId    = $tank['temporary_id'];
            $tankParameters = $parameters[$temporaryId];
            unset($tank['temporary_id']);
            unset($tankParameters['temporary_id']);
            $tankId = $this->_modelTank->insert($tank, $tankParameters);
            if ($tankId > 0) {
                $count++;
            }
        }
        return $count;
    }

    protected function _unformatValue($field, $value)
    {
        switch ($field) {

            case 'hit_points':
                $value = preg_replace('/[^0-9]/', '', $value);
                break;

            case 'weight':
                $value = preg_replace('/[^0-9.,\/]/', '', $value); // to format 100.18/105.0
                break;

//            case 'load_limit': // look prev comment

            case 'price':
                $value = preg_replace('/[^0-9]/', '', $value);
                $value = $value * 100;
                break;

            case 'engine_power':
                $value = preg_replace('/[^0-9]/', '', $value);

            case 'speed_limit':
                $value = preg_replace('/[^0-9]/', '', $value);
                break;

            case 'traverse_speed':
                $value = preg_replace('/[^0-9]/', '', $value);
                break;

            case 'turret_traverse_speed':
                $value = preg_replace('/[^0-9]/', '', $value);
                break;

            case 'hull_armor':
                preg_match('/лоб ([0-9]+)\s*борта ([0-9]+)\s*корма ([0-9]+)/', $value, $match);
                $front = sprintf("%03s", $match[1]);
                $sides = sprintf("%03s", $match[2]);
                $rear  = sprintf("%03s", $match[3]);
                $value = $front . $sides . $rear;
                break;

            case 'turret_armor':
                preg_match('/лоб ([0-9]+)\s*борта ([0-9]+)\s*корма ([0-9]+)/', $value, $match);
                $front = sprintf("%03s", $match[1]);
                $sides = sprintf("%03s", $match[2]);
                $rear  = sprintf("%03s", $match[3]);
                $value = $front . $sides . $rear;
                break;

            case 'gun_name':
                $value = trim($value);
                break;

            case 'ammunition':
                $value = preg_replace('/[^0-9]/', '', $value);
                break;

            case 'avg_damage':
                $value = preg_replace('/[^0-9\-]/', '', $value);
                break;

            case 'armor_penetration':
                $value = preg_replace('/[^0-9\-]/', '', $value);
                break;

            case 'rate_of_fire':
                $value = preg_replace('/[^0-9.,]/', '', $value);
                break;

            case 'view_range':
                $value = preg_replace('/[^0-9]/', '', $value);
                break;

            case 'signal_range':
                $value = preg_replace('/[^0-9]/', '', $value);
                break;

            default:
                $value = '';
        }
        return $value;
    }

    protected function _getClient()
    {
        if ( ! $this->_client) {
            $this->_client =  new Zend_Http_Client();
        }
        return $this->_client;
    }

}