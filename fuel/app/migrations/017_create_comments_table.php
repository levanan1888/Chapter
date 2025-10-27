<?php

namespace Fuel\Migrations;

class Create_comments_table
{
    public function up()
    {
        \DBUtil::create_table('comments', array(
            'id' => array('type' => 'int', 'null' => false, 'auto_increment' => true),
            'story_id' => array('type' => 'int', 'null' => false),
            'chapter_id' => array('type' => 'int', 'null' => true),
            'user_id' => array('type' => 'int', 'null' => false),
            'parent_id' => array('type' => 'int', 'null' => true, 'default' => null),
            'content' => array('type' => 'text', 'null' => false),
            'is_approved' => array('type' => 'tinyint', 'null' => false, 'default' => 1),
            'created_at' => array('type' => 'datetime', 'null' => false),
            'updated_at' => array('type' => 'datetime', 'null' => false),
        ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

        // Add foreign key constraints after table creation
        try {
            \DBUtil::add_foreign_key('comments', array(
                'constraint' => 'fk_comments_story_id',
                'key' => 'story_id',
                'reference' => array(
                    'table' => 'stories',
                    'column' => 'id',
                ),
                'on_update' => 'CASCADE',
                'on_delete' => 'CASCADE',
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to add story foreign key: ' . $e->getMessage());
        }

        try {
            \DBUtil::add_foreign_key('comments', array(
                'constraint' => 'fk_comments_chapter_id',
                'key' => 'chapter_id',
                'reference' => array(
                    'table' => 'chapters',
                    'column' => 'id',
                ),
                'on_update' => 'CASCADE',
                'on_delete' => 'CASCADE',
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to add chapter foreign key: ' . $e->getMessage());
        }

        try {
            \DBUtil::add_foreign_key('comments', array(
                'constraint' => 'fk_comments_user_id',
                'key' => 'user_id',
                'reference' => array(
                    'table' => 'admins',
                    'column' => 'id',
                ),
                'on_update' => 'CASCADE',
                'on_delete' => 'CASCADE',
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to add user foreign key: ' . $e->getMessage());
        }

        try {
            \DBUtil::add_foreign_key('comments', array(
                'constraint' => 'fk_comments_parent_id',
                'key' => 'parent_id',
                'reference' => array(
                    'table' => 'comments',
                    'column' => 'id',
                ),
                'on_update' => 'CASCADE',
                'on_delete' => 'CASCADE',
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to add parent foreign key: ' . $e->getMessage());
        }
    }

    public function down()
    {
        \DBUtil::drop_table('comments');
    }
}
