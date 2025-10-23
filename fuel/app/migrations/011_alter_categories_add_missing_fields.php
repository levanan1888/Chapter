<?php

namespace Fuel\Migrations;

class Alter_categories_add_missing_fields
{
    public function up()
    {
        // Add slug if missing
        if (!\DBUtil::field_exists('categories', array('slug'))) {
            \DBUtil::add_fields('categories', array(
                'slug' => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
            ));
            // Try to backfill simple slugs from name
            try {
                $results = \DB::query('SELECT id, name FROM categories WHERE slug IS NULL OR slug = ""')->execute();
                foreach ($results as $row) {
                    $slug = strtolower($row['name']);
                    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
                    $slug = preg_replace('/[\s-]+/', '-', $slug);
                    $slug = trim($slug, '-');
                    \DB::query('UPDATE categories SET slug = :slug WHERE id = :id')
                        ->param('slug', $slug)
                        ->param('id', $row['id'])
                        ->execute();
                }
            } catch (\Exception $e) {
                // ignore
            }
            // Make not null if your DB supports it
            try {
                \DB::query('ALTER TABLE categories MODIFY slug VARCHAR(255) NOT NULL')->execute();
            } catch (\Exception $e) {
                // ignore differences across drivers
            }
            // Create unique index
            try {
                \DBUtil::create_index('categories', 'slug', 'categories_slug_unique', 'unique');
            } catch (\Exception $e) {
                // might already exist
            }
        }

        // Add is_active if missing
        if (!\DBUtil::field_exists('categories', array('is_active'))) {
            \DBUtil::add_fields('categories', array(
                'is_active' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
            ));
            try {
                \DB::query('UPDATE categories SET is_active = 1 WHERE is_active IS NULL')->execute();
            } catch (\Exception $e) {
                // ignore
            }
        }
    }

    public function down()
    {
        // Drop added columns/indexes if exist
        try { \DBUtil::drop_index('categories', 'categories_slug_unique'); } catch (\Exception $e) {}
        try { \DBUtil::drop_fields('categories', array('slug')); } catch (\Exception $e) {}
        try { \DBUtil::drop_fields('categories', array('is_active')); } catch (\Exception $e) {}
    }
}



