<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->database();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

        header('Content-Type: application/json');
    }

    public function login()
    {
        $json = file_get_contents('php://input');

        $data = json_decode($json, true);

        $email = $data['email'];
        $password = $data['password'];

        $check = $this->db
            ->where('email', $email)
            ->where('password', md5($password))
            ->get('users');

        if($check->num_rows() > 0)
        {
            $user = $check->row();

            echo json_encode([
                'status' => true,
                'message' => 'Login Success',
                'user' => $user
            ]);

        } else {

            echo json_encode([
                'status' => false,
                'message' => 'Invalid Email or Password'
            ]);
        }
    }
}