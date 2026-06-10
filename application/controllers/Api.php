<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // CORS (must be inside constructor, not top)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    public function login()
    {
        header('Content-Type: application/json');

        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));

        if(empty($username) || empty($password)){
            echo json_encode([
                'status' => false,
                'message' => 'Username and Password Required'
            ]);
            return;
        }

        // IMPORTANT FIX: password should be compared properly
        $check = $this->db
            ->where('username', $username)
            ->where('password', $password)
            ->get('user_details');

        if($check->num_rows() > 0){

            $user = $check->row();

            echo json_encode([
                'status' => true,
                'message' => 'Login Success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'photo' => $user->photo,
                    'type_id' => $user->type_id,
                    'user_id' => $user->user_id
                ]
            ]);

        } else {

            echo json_encode([
                'status' => false,
                'message' => 'Invalid Login Credentials'
            ]);

        }
    }
}