-- This is base SQL script for create core database structure.
-- This script don't drop tables.

CREATE TABLE IF NOT EXISTS tanks (
  id INT NOT NULL AUTO_INCREMENT,
  code VARCHAR(255) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  lvl INT NOT NULL,
  tank_type_id INT NOT NULL,
  nation_id INT NOT NULL,
  premium TINYINT(1) NOT NULL DEFAULT 0,
  order_number INT NOT NULL DEFAULT 0,
  PRIMARY KEY(id),
  UNIQUE(code),
  INDEX(tank_type_id),
  INDEX(nation_id),
  INDEX(order_number)
)
;

CREATE TABLE IF NOT EXISTS tank_types (
  id INT NOT NULL AUTO_INCREMENT,
  code VARCHAR(255) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  order_number INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE (code),
  INDEX(order_number)
)
;

CREATE TABLE IF NOT EXISTS nations (
  id INT NOT NULL AUTO_INCREMENT,
  code VARCHAR(255) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  order_number INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE (code),
  INDEX(order_number)
)
;

CREATE TABLE IF NOT EXISTS tank_parameters (
  tank_id INT NOT NULL AUTO_INCREMENT,
  hit_points INT NOT NULL,
  weight INT NOT NULL, -- *don't forget *100 , because 10000 = 100,00 tons
  load_limit INT NOT NULL, -- *
  price INT NOT NULL, -- *
  engine_power INT NOT NULL,
  speed_limit INT NOT NULL,
  traverse_speed INT NOT NULL,
  turret_traverse_speed INT NOT NULL,
  hull_armor INT NOT NULL DEFAULT 000000000, -- **format 000/000/000 where front/sides/rear
  turret_armor INT NOT NULL DEFAULT 000000000, -- **
  gun_name VARCHAR(255) NOT NULL,
  ammunition VARCHAR(255) NOT NULL, -- example: 60 pcs
  avg_damage VARCHAR(255) NOT NULL, -- example: 100-200
  armor_penetration VARCHAR(255) NOT NULL, -- example: 100-200
  rate_of_fire VARCHAR(255) NOT NULL, -- example: 7.0 rounds/min
  view_range INT NOT NULL,
  signal_range INT NOT NULL,
  PRIMARY KEY (tank_id)
)
;

-- --------------------------------------------------------
INSERT IGNORE INTO tank_types (id, code, full_name, order_number) VALUES
(1, 'lt', 'Light tank', 1),
(2, 'mt', 'Medium tank', 2),
(3, 'ht', 'Heavy tank', 3),
(4, 'td', 'Tank Destroyers', 4),
(5, 'spg', 'SPG', 5)
;

INSERT IGNORE INTO nations (id, code, full_name, order_number) VALUES
(1, 'soviet', 'Soviet Vehicles', 1),
(2, 'german', 'German Vehicles', 2),
(3, 'usa', 'USA Vehicles', 3)
;

INSERT IGNORE INTO tanks (id, code, full_name, lvl, tank_type_id, nation_id, premium) VALUES
(1, 'kv-5', 'KV-5', 8, 3, 1, 1),
(2, 'lowe', 'Lowe', 8, 3, 2, 1),
(3, 'm6a2e1', 'M6A2E1', 8, 3, 3, 1)
;

INSERT IGNORE INTO tank_parameters (tank_id, hit_points, weight, load_limit, price, engine_power, speed_limit, traverse_speed, turret_traverse_speed, hull_armor, turret_armor, gun_name, ammunition, avg_damage, armor_penetration, rate_of_fire, view_range, signal_range) VALUES
(1, 1780, 10018, 10500, 0, 1200, 40, 18, 21, 180150140, 180150140, '107 mm ZiS-6M', '60 pcs', '225-375', '125-209', '7.0', 350, 440),
(2, 1650, 9255, 9985, 0, 800, 35, 24, 23, 120080080, 120080080, '10,5 cm KwK46 L/70', '40 pcs', '240-400', '176-293', '5.0', 400, 710),
(3, 1500, 6696, 7050, 0, 960, 29, 24, 23, 191044041, 191089208, '105 mm Gun T5E1E', '60 pcs', '240-400', '149-248', '5.05', 380, 570)
;