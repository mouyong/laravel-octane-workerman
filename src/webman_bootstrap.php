<?php

# autoload files
foreach (config('autoload.files', []) as $file) {
    include_once $file;
}

foreach (webman_config('plugin', []) as $firm => $projects) {
    foreach ($projects as $name => $project) {
        foreach ($project['autoload']['files'] ?? [] as $file) {
            include_once $file;
        }
    }
}

# bootstrap
foreach (config('bootstrap', []) as $class_name) {
    /** @var \Webman\Bootstrap $class_name */
    $class_name::start($worker);
}

foreach (webman_config('plugin', []) as $firm => $projects) {
    foreach ($projects as $name => $project) {
        foreach ($project['bootstrap'] ?? [] as $class_name) {
            /** @var \Webman\Bootstrap $class_name */
            $class_name::start($worker);
        }
    }
}

# webman middleware
if (class_exists(\Webman\Middleware::class)) {
    \Webman\Middleware::container(\Illuminate\Container\Container::getInstance());
    \Webman\Middleware::load(config('middleware', []));
    foreach (webman_config('plugin', []) as $firm => $projects) {
        foreach ($projects as $name => $project) {
            \Webman\Middleware::load($project['middleware'] ?? []);
        }
    }
    \Webman\Middleware::load(['__static__' => config('static.middleware', [])]);
}