<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

	
	public function index()
	{
		header('Content-Type: application/json; charset=utf-8');
		$return = array();
		$db = $this->db->get_where("menu")->result();
		echo json_encode($db);
	} 

	public function transactions()
	{
		header('Content-Type: application/json; charset=utf-8');
		$id = $this->uri->segment(4);
		if($id == "")
		{
			$db = $this->db->get("pesanan");
			$array = array();
			foreach($db->result() as $key => $dbs)
			{
				$amount = 0;
				$t = json_decode($dbs->detail);
				foreach($t as $ts)
				{
					$price = $ts->price * $ts->quantity;
					$amount = $amount + $price;
				}
				$array[$key]["id"] = $dbs->kode_pesanan;
				$array[$key]["detail"] = $t;
				$array[$key]["amount"] = $amount;
				$array[$key]["paidAmount"] = intval($dbs->paidamount);
				$array[$key]["date"] = $dbs->tanggal." ".$dbs->jam;
			}
		} else {
			$db = $this->db->get_where("pesanan", array("kode_pesanan" => $id));
			$dbs = $db->row();
			$amount = 0;
			$t = json_decode($dbs->detail);
			foreach($t as $ts)
			{
				$price = $ts->price * $ts->quantity;
				$amount = $amount + $price;
			}
			$array["id"] = $dbs->kode_pesanan;
			$array["detail"] = $t;
			$array["amount"] = $amount;
			$array["paidAmount"] = $dbs->paidamount;
			$array["date"] = $dbs->tanggal." ".$dbs->jam;
		}
		echo json_encode($array);
	}

	public function delete()
	{
		header('Content-Type: application/json; charset=utf-8');
		$id = $this->uri->segment(4);
		$db = $this->db->delete("menu", array("id" => $id));
		if($db)
		{
			$this->output->set_status_header(200);
            $response = array('message' => 'Menu deleted successfully');
            $this->output->set_output(json_encode($response));
		} else {
			$this->output->set_status_header(500);
                $response = array('message' => 'Failed to delete menu');
                $this->output->set_output(json_encode($response));
		}
	}

	public function checkout() {
		$orderData = json_decode(file_get_contents('php://input'), true);
        $kode_barang = $this->Order_model->CreateCode();

		$this->db->insert("pesanan", array("kode_pesanan" => $kode_barang,"paidamount" => $orderData["paid_amount"] ,"detail" => json_encode($orderData["order_items"]), "jam" => date("H:i:s"), "tanggal" => date("Y-m-d")));
        $response = array(
            'status' => 'success',
            'message' => 'Order processed successfully',
			'queue_number' => $kode_barang
        );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
	}

	public function add()
	{
		header('Content-Type: application/json; charset=utf-8');
		$return = array("error" => "error", "messagess" => "gagal menyimpan data");
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
			  $file = $_FILES['gambar'];
			  $namaFile = $file['name'];
			  $lokasiFile = $file['tmp_name'];
			  $name = md5(date("Y-d-m H:i:s"));
			  $tujuan = './asset/img/' . $name.$namaFile;
			  $r = 'asset/img/' . $name.$namaFile;
			  if (move_uploaded_file($lokasiFile, $tujuan)) {
				$price = $this->input->post('price');
				$detail = $this->input->post('detail');
				$title = $this->input->post("name");
				$db = $this->db->insert("menu", array("title" => $title, "image" => $r, "description" => $detail, "price" => $price));
				
				$return = array("error" => "success", "messagess" => "Gambar berhasil diunggah");
			  } else {
				$return = array("error" => "error", "messagess" => "Gagal mengunggah gambar");
			  }
			} else {
				$return = array("error" => "error", "messagess" => "Gagal gambar");
			}
		} else {
			$return = array("error" => "error", "messagess" => "invalid");
		}
		echo json_encode($return);
	}
}
