<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_082 extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    public function run() {
        $token = isset($_GET['t']) ? $_GET['t'] : '';
        if ($token !== '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo') {
            echo json_encode(array('ok'=>false,'error'=>'auth'));
            exit;
        }
        header('Content-Type: application/json');
        $sql = "CREATE TABLE IF NOT EXISTS `push_token_log` (
          `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `uid`         INT UNSIGNED NOT NULL DEFAULT 0,
          `expo_token`  VARCHAR(255) NOT NULL,
          `platform`    VARCHAR(20)  DEFAULT NULL,
          `app_version` VARCHAR(20)  DEFAULT NULL,
          `registered_at` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uq_uid_token` (`uid`, `expo_token`),
          KEY `idx_uid` (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        try {
            $this->db->query($sql);
            echo json_encode(array('ok'=>true,'msg'=>'push_token_log created or already exists'));
        } catch (Exception $e) {
            echo json_encode(array('ok'=>false,'error'=>$e->getMessage()));
        }
    }
}
