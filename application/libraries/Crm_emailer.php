<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CRM Common Emailer
 * PHP 7.2 compatible
 * Uses CI Email library
 * Logs mails in crm_email_logs
 * Logs cron runs in crm_report_cron_log
 */
class Crm_emailer
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('email');

        // Email configuration (can move to config/email.php later)
        $config = array(
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'newline'  => "\r\n"
        );

        $this->CI->email->initialize($config);
    }

    /**
     * Send email and log in crm_email_logs
     */
    public function send_and_log($subject, $htmlBody, $toEmail, $options = array())
    {
        $fromEmail = isset($options['from_email']) ? $options['from_email'] : 'no-reply@yourcrm.com';
        $fromName  = isset($options['from_name'])  ? $options['from_name']  : 'CRM';
        $forUser   = isset($options['for_user'])   ? (int)$options['for_user'] : 0;
        $toAdminId = isset($options['to_adminid']) ? (int)$options['to_adminid'] : 0;
        $type      = isset($options['type'])       ? $options['type'] : 'general';

        // 1) Send mail
        $this->CI->email->clear();
        $this->CI->email->from($fromEmail, $fromName);
        $this->CI->email->to($toEmail);
        $this->CI->email->subject($subject);
        $this->CI->email->message($htmlBody);

        $sent = $this->CI->email->send();

        // 2) Log email in crm_email_logs
        $this->CI->db->insert('crm_email_logs', array(
            'for_user'      => $forUser,
            'from_user'     => $fromName,
            'to_user'       => is_array($toEmail) ? implode(',', $toEmail) : $toEmail,
            'to_adminid'    => $toAdminId,
            'email_subject' => $subject,
            'mailsend_date' => date('Y-m-d'),
            'type'          => $type
        ));

        return $sent;
    }

    /**
     * Log cron execution in crm_report_cron_log
     */
    public function log_cron_run($reportName, $data = array(), $emailSend = 1)
    {
        $this->CI->db->insert('crm_report_cron_log', array(
            'report_name'   => $reportName,
            'report_data'   => json_encode($data),
            'email_send'    => $emailSend,
            'send_datetime' => date('Y-m-d H:i:s')
        ));
    }
}