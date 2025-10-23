<?php

namespace Fuel\Migrations;

class Add_user_type_to_admins
{
    public function up()
    {
        \DBUtil::add_fields('admins', array(
            'user_type' => array(
                'type' => 'varchar',
                'constraint' => 20,
                'default' => 'admin',
                'comment' => 'Loại người dùng: admin, user'
            ),
        ));
    }

    public function down()
    {
        \DBUtil::drop_fields('admins', array(
            'user_type'
        ));
    }
}
