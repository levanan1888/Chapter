<?php

class Controller_User_Forgotpassword extends Controller_Template
{

	public function action_index()
	{
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'User/forgotpassword &raquo; Index';
		$this->template->content = View::forge('user/forgotpassword/index', $data);
	}

}
