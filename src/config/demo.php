<?php

return [

    /*
    | ID of the (public) label tree to attach to each demo project.
    */
    'label_tree_id' => env('DEMO_LABEL_TREE_ID', null),

    /*
    | IDs of the volumes to attach to each demo project. The volumes are cloned without
    | existing annotations, i.e. each demo project gets its own volumes.
    */
    'volume_ids' => array_filter(explode(',', env('DEMO_VOLUME_IDS', ''))),

    /*
    | Name of each new demo project.
    */
    'project_name' => env('DEMO_PROJECT_NAME', 'Demo Project'),

];
