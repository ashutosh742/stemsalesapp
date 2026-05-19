<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anaya_reports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Only run via CLI or internal cron URL (add your own check if needed)
        if (!$this->input->is_cli_request()) {
            // comment this if you want to test via browser
            // show_error('CLI only', 403);
        }

        $this->load->library(['email', 'AnayaAgent']);
        $this->load->database();
        $this->load->helper(['url', 'html']);
    }

    /**
     * Daily email to all BDs.
     * Cron example:
     *   0 8 * * * /usr/bin/php /var/www/yourapp/index.php cron/Anaya_reports/daily_bd
     */
    public function daily_bd()
    {
        $today = date('Y-m-d');

        // CI email config – you might want to move this into application/config/email.php
        $config = [
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'newline'  => "\r\n",
        ];
        $this->email->initialize($config);

        // 1) get all BD users (adjust user_type.name for your BD role)
        $bdUsers = $this->db
            ->select('ud.id AS user_details_id, ud.name, ud.email, ut.name AS role_name')
            ->from('user_details ud')
            ->join('user_type ut', 'ut.id = ud.type_id', 'left')
            ->where_in('ut.name', ['BD', 'Business Developer']) // change to your actual BD type names
            ->where('ud.status', 'active')
            ->get()
            ->result_array();

        foreach ($bdUsers as $bd) {

            // 2) get BD pack from Anaya
            $pack = $this->anayaagent->bd_daily_pack($bd['user_details_id'], $today);

            // 3) render email HTML
            $html = $this->load->view('emails/anaya_bd_daily', [
                'bd'      => $bd,
                'pack'    => $pack,
                'metrics' => $pack['metrics'],
            ], true);

            // 4) send email
            $this->email->clear();
            $this->email->from('anaya@yourcrm.com', 'Anaya - CRM Agent');
            $this->email->to($bd['email']);
            $this->email->subject('Anaya – Your Day Plan & Funnel | '.$today);
            $this->email->message($html);
            $sent = $this->email->send();

            // 5) log email in crm_email_logs
            $this->db->insert('crm_email_logs', [
                'for_user'      => $bd['user_details_id'],
                'from_user'     => 'Anaya',
                'to_user'       => $bd['email'],
                'to_adminid'    => 0,
                'email_subject' => 'Anaya – Your Day Plan & Funnel | '.$today,
                'mailsend_date' => $today,
                'type'          => 'daily_bd',
            ]);
        }

        // cron log
        $this->db->insert('crm_report_cron_log', [
            'report_name'  => 'daily_bd',
            'report_data'  => json_encode(['date' => $today]),
            'email_send'   => 1,
            'send_datetime'=> date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Daily email to owner / leadership.
     * Cron example:
     *   5 8 * * * /usr/bin/php /var/www/yourapp/index.php cron/Anaya_reports/daily_owner
     */
    public function daily_owner()
    {
        $today = date('Y-m-d');

        $config = [
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'newline'  => "\r\n",
        ];
        $this->email->initialize($config);

        // You can store owner emails & names in config/anaya.php
        $this->config->load('anaya', true);
        $anayaConfig = $this->config->item('anaya');

        $ownerList = $anayaConfig['owner_recipients'] ?? [
            ['name' => 'Owner', 'email' => 'you@yourcompany.com'],
        ];

        // 1) build owner pack once (company-wide)
        $pack = $this->anayaagent->owner_daily_pack($today);

        foreach ($ownerList as $owner) {

            $html = $this->load->view('emails/anaya_owner_daily', [
                'owner'   => $owner,
                'pack'    => $pack,
                'metrics' => $pack['metrics'],
            ], true);

            $this->email->clear();
            $this->email->from('anaya@yourcrm.com', 'Anaya - CRM Agent');
            $this->email->to($owner['email']);
            $this->email->subject('Anaya – Daily Sales & Discipline Snapshot | '.$today);
            $this->email->message($html);
            $this->email->send();

            // log in crm_email_logs
            $this->db->insert('crm_email_logs', [
                'for_user'      => 0,
                'from_user'     => 'Anaya',
                'to_user'       => $owner['email'],
                'to_adminid'    => 0,
                'email_subject' => 'Anaya – Daily Sales & Discipline Snapshot | '.$today,
                'mailsend_date' => $today,
                'type'          => 'daily_owner',
            ]);
        }

        // cron log
        $this->db->insert('crm_report_cron_log', [
            'report_name'  => 'daily_owner',
            'report_data'  => json_encode(['date' => $today]),
            'email_send'   => 1,
            'send_datetime'=> date('Y-m-d H:i:s'),
        ]);
    }
}
