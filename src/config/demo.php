<?php

return [

    /*
    | ID of the (public) label tree to attach to each demo project.
    */
    'label_tree_id' => env('DEMO_LABEL_TREE_ID', null),

    /*
    | ID of the volume to attach to each demo project. The volume is cloned without
    | existing annotations, i.e. each demo project gets its own volume.
    */
    'volume_id' => env('DEMO_VOLUME_ID', null),

    /*
    | Name of each new demo project.
    */
    'project_name' => env('DEMO_PROJECT_NAME', 'Demo Project'),

    /*
    | ID of the video to create for each demo project. Requires the biigle/videos module.
    */
    'video_id' => env('DEMO_VIDEO_ID', null),

];
