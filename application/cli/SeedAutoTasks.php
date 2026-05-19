<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SeedAutoTasks CLI controller
 *
 * Called by host cron at 15:00 IST weekdays:
 *   0 9 * * 1-5 cd /var/www/stemapp && php index.php cli/seed_auto_tasks >> logs/seeder.log 2>&1
 *   (9:00 UTC = 14:30 IST. Use 9:30 UTC for 15:00 IST exactly.)
 *
 * Calls sp_seed_auto_tasks for today, which drops MoM completions, stuck-lead
 * nudge calls, and proposal follow-up emails into the 15:00 to 17:30 auto band
 * for every BD whose plan was approved.
 *
 * Migration 017_4 ships the proc. This CLI is the trigger.
 */
class SeedAutoTasks extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if (!$this->input->is_cli_request()) {
      show_error('CLI only', 403);
    }
    $this->load->database();
  }

  public function index() {
    $today = date('Y-m-d');
    $started = microtime(true);

    echo "[" . date('Y-m-d H:i:s') . "] Auto-task seeder starting for " . $today . "\n";

    // count BDs eligible (plan approved, not yet seeded)
    $q = $this->db->query("
      SELECT COUNT(*) AS n
      FROM autotask_time
      WHERE date = ? AND shape_locked = 1 AND auto_seeded = 0
    ", [$today]);
    $eligible = (int) $q->row()->n;
    echo "  eligible BDs: " . $eligible . "\n";

    if ($eligible === 0) {
      echo "  nothing to seed - exiting\n";
      return;
    }

    // run the proc
    $this->db->query("CALL sp_seed_auto_tasks(?)", [$today]);

    // report
    $r = $this->db->query("
      SELECT user_id, auto_seeded_count, auto_seeded_at
      FROM autotask_time
      WHERE date = ? AND auto_seeded = 1
      ORDER BY auto_seeded_at DESC
    ", [$today]);
    foreach ($r->result() as $row) {
      echo "  BD uid=" . $row->user_id . " seeded=" . $row->auto_seeded_count
         . " at=" . $row->auto_seeded_at . "\n";
    }

    $elapsed = round(microtime(true) - $started, 2);
    echo "[" . date('Y-m-d H:i:s') . "] done in " . $elapsed . "s\n";
  }
}
