<?php

class Controller_User_Resetpassword extends Controller_Template
{

	public function action_index()
	{
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'User/resetpassword &raquo; Index';
		$this->template->content = View::forge('user/resetpassword/index', $data);
	}

}
