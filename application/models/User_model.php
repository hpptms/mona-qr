<?php
class User_model extends CI_Model {

  public function __construct()
  {
    $this->load->database();
  }

  public function set_user()
  {

    $data = array(
      'u_id' => $this->input->post('u_id'),
      'secretkey' => $this->input->post('secretkey')
    );

    return $this->db->insert('user', $data);
  }

  public function can_log_in(){	//can_log_inメソッドを作っていく

    $this->db->where("u_id", $this->input->post("u_id"));	//POSTされたemailデータとDB情報を照合する
    $this->db->where("secretkey", ($this->input->post("secretkey")));	//POSTされたパスワードデータとDB情報を照合する
    $query = $this->db->get("users");

    if($query->num_rows() == 1){	//ユーザーが存在した場合の処理
      return true;
    }else{					//ユーザーが存在しなかった場合の処理
      return false;
    }
  }
