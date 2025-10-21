<?php
return array (
  'version' => array(  
    'app' => array(    
      'default' => array(      
        0 => '001_create_admins_table',
        1 => '002_create_categories_table',
        2 => '003_create_authors_table',
        3 => '004_create_stories_table',
        4 => '005_create_story_categories_table',
        5 => '006_create_chapters_table',
        6 => '007_seed_fake_data',
        7 => '008_seed_fake_data_with_faker',
        8 => '009_alter_categories_add_missing_fields',
      ),
    ),
    'module' => array(    
    ),
    'package' => array(    
    ),
  ),
  'folder' => 'migrations/',
  'table' => 'migration',
  'flush_cache' => false,
  'flag' => NULL,
);
