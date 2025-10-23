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
        8 => '009_add_slug_is_active_to_categories',
        9 => '011_alter_categories_add_missing_fields',
        10 => '012_add_is_visible_to_stories',
        11 => '013_add_user_type_to_admins',
      ),
    ),
    'module' => array(    
    ),
    'package' => array(    
      'auth' => array(      
        0 => '001_auth_create_usertables',
        1 => '002_auth_create_grouptables',
        2 => '003_auth_create_roletables',
        3 => '004_auth_create_permissiontables',
        4 => '005_auth_create_authdefaults',
        5 => '006_auth_add_authactions',
        6 => '007_auth_add_permissionsfilter',
        7 => '008_auth_create_providers',
        8 => '009_auth_create_oauth2tables',
        9 => '010_auth_fix_jointables',
        10 => '011_auth_group_optional',
        11 => '012_auth_update_userindex',
      ),
    ),
  ),
  'folder' => 'migrations/',
  'table' => 'migration',
  'flush_cache' => false,
  'flag' => NULL,
);
