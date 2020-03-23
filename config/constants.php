<?php

define('MY_CRAWL_TODO', 1);
define('MY_CRAWL_ANCHOR_GENERATE', 2);
define('MY_CRAWL_URL_GENERATE', 3);
define('MY_CRAWL_DONE', 4);
define('MY_CRAWL_NO_RESULT', 5);
define('MY_CRAWL_INIT', 'https://www.google.co.jp/search?start=0&gl=jp&q=');
define('MY_SERPSTACK_URL', 'http://api.serpstack.com/search');
define('MY_SERPSTACK_KEY', 'c06d8e2f37e9ad28c7baa29ffaacdfe6');

return [
'status' => [
           '1' => 'Todo',
           '2' => 'In Progress[Anchor generate]',
           '3' => 'In Progress[Url List generate]',
           '4' => 'Done',
           '5' => 'No result',
         ],
];
