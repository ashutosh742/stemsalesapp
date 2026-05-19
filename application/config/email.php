<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol']         = 'smtp';                      // Protocol: mail, sendmail, or smtp
$config['smtp_host']        = 'mail.stemapp.in';         // SMTP Server Address
$config['smtp_port']        = 587;                        // SMTP Port: e.g., 587 for TLS, 465 for SSL
$config['smtp_user']        = 'crm.help@stemapp.in';   // SMTP Username
$config['smtp_pass']        = 'crmhelp@2024';            // SMTP Password
$config['smtp_crypto']      = 'TLS';                    // Security: tls or ssl (leave blank for no encryption)
$config['mailtype']         = 'html';                      // Email format: text or html
$config['charset']          = 'utf-8';                     // Character set
$config['wordwrap']         = TRUE;                        // Enable word-wrapping
$config['newline']          = "\r\n";                       // Newline for email compatibility
$config['crlf']             = "\r\n";                          // CRLF for email compatibility
$config['validate']         = TRUE;                        // Enable email

?>