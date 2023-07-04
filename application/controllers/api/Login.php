<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
	public function index()
	{
		$return = array("error" => "error", "messege" => "error login");
		header('Content-Type: application/json; charset=utf-8');
		// $request = json_decode(file_get_contents('php://input'), true);

        // // Mendapatkan email dan password dari permintaan POST
        // $username = $request['email'];
        // $password = $request['password'];
		$username = $this->input->post("email");
		$password = $this->input->post("password");
		$db = $this->db->get_where("user", array("username" => $username, "password" => md5($password)));
		if($db->num_rows() > 0)
		{
			$return = array("error" => "success", "messege" => "success login");
		}
		echo json_encode($return);
		// $this->load->view('welcome_message');
	} 
}
