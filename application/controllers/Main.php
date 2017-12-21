<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  // public function view($home = 'home')
  // {
  //   if ( ! file_exists(APPPATH.'views/home/'.$home.'.php'))
  //   {
  //     // おっと、そのページはありません！
  //     show_404();
  //   }
  //   $this->load->view('templates/header');
  //   $this->load->view('home/'.$home);
  //   $this->load->view('templates/footer');
  // }

  public function index()
  {
    $this->login();
  }

  public function login()
  {
    $this->load->view('templates/header');
    $this->load->view('login');
    $this->load->view('templates/footer');
  }

  public function login_validation(){

    $this->load->helper('url');
    // ユーザープロフィールの取得
    $uid;
    $secretkey;
    $u_name;
    $u_dan;
    $mona;

    // ユーザープロフィールの取得
    $data = $this->input->get();
    list ($uid, $secretkey) = $this->get_id($data);
    list ($u_name,$u_dan) = $this->get_profile($uid);
    $mona = $this->get_mona($uid,$secretkey);
    // var_dump($uid,$secretkey,$u_name,$u_dan,$mona);
    $data = array(
      'u_name' => $u_name,
      'u_dan' => $u_dan
    );

    if($uid){ //useridが存在した場合
      redirect("main/members");
    }else{
      $this->load->view('templates/header');
      $this->load->view('login');
      $this->load->view('templates/footer');
    }
  }

  public function members(){
    $this->load->view("members");
  }

  private function get_id($data)
  {
    if ($data !== NULL)
    {
      foreach ($data as $arr)
      {
        foreach (json_decode($arr) as $key => $value)
        {
          if($key == "u_id")
          {
            $uid = $value;
          }
          elseif($key == "secretkey")
          {
            $secretkey = $value;
          }
        }
      }
    }
    return array($uid,$secretkey);
  }

  private function get_profile($uid)
  {
    $url = "http://askmona.org/v1/users/profile" . "?u_id=" . $uid;
    $response = file_get_contents($url);
    foreach (json_decode($response) as $key => $value)
    {
      if($key == "u_name"){
        $u_name = $value;
      }elseif($key == "u_dan"){
        $u_dan = $value;
      }
    }
    return  array($u_name,$u_dan);
  }

  private function get_mona($uid,$secretkey){
    $url = "http://askmona.org/v1/account/balance";

    $app_id = "4241";
    $uid = $uid;
    $nonce = base64_encode(mcrypt_create_iv(32,MCRYPT_DEV_URANDOM));
    $time = time();
    // 認証キーの作成
    $app_secretkey = "AnsoK+Sjz9vIv/c8F3qx19+JExp2LTL9CRzujSsRdHiE=";
    $secretkey = $secretkey;
    $auth_key = base64_encode(hash('sha256',$app_secretkey.$nonce.$time.$secretkey,TRUE));
    $auth_key = $auth_key;

    $detail = "0";

    $data = [
      "app_id" => $app_id,
      "u_id" => $uid,
      "nonce" => $nonce,
      "time" => $time,
      "auth_key" => $auth_key,
      "detail" => $detail,
    ];
    $data = http_build_query($data, "", "&");

    $headers = array(
      'Content-Type: application/x-www-form-urlencoded',
    );

    $options = array(
      'http' => array(
        'method' => 'POST',
        'content' => $data,
        'header' => implode("\r\n", $headers),
      )
    );

    $options = stream_context_create($options);
    $response = file_get_contents($url, false, $options);

    foreach (json_decode($response) as $key => $value)
    {
      if($key == "balance"){
        $mona = (int)$value;
        $mona = sprintf('%09d', $mona);
        $mona = $this->insertStr($mona, 1, ',');
      }
    }
    return $mona;
  }

  public function insertStr($text1, $num, $text2){
    return substr($text1, 0, $num).$text2.substr($text1, $num, strlen($text1));
  }

}
