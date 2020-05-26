<?php
namespace GlobalConfig;

use ImageNavbarLink;
use RuntimeException;
use TextNavbarLink;

const CONFIG_PATH = "/etc/blog/config.json";

require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/_utility.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/_ext_link.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/_navbar_link.php';

class DatabaseConfig {
    public string $address;
    public string $name;
    public string $user;
    public string $password;
}

class BlogConfig {
    public DatabaseConfig $database;
    public string $admin_ticket;
    public string $site_title;
    public string $site_header;
    public int $home_recent_post_count;
    public int $post_preview_chars;
    public int $sidebar_recent_post_count;
    public string $copyright_start_year;
    public ?string $copyright_end_year;
    public string $copyright_name;
    public string $contact_email;
    public string $contact_email_text;
    public string $github_url;
    public array $navbar_links;
    public array $external_links;
}

function parse_config(): BlogConfig {
    $config_contents = file_get_contents(CONFIG_PATH);
    $config_json = json_decode($config_contents, true);

    $db_config = new DatabaseConfig();
    $db_config->address = $config_json['database']['address'];
    $db_config->name = $config_json['database']['name'];
    $db_config->user = $config_json['database']['user'];
    $db_config->password = $config_json['database']['password'];

    $navbar_links = array();
    foreach ($config_json['navbar_links'] as $link_json) {
        if ($link_json['type'] === 'text') {
            $navbar_links[] = new TextNavbarLink($link_json['url'], $link_json['text'], $link_json['title_text']);
        } else if ($link_json['type'] === 'icon') {
            $navbar_links[] = new ImageNavbarLink($link_json['url'], $link_json['icon_namespace'],
                    $link_json['icon_id'], get_array_item($link_json, 'title_text'));
        } else {
            throw new RuntimeException('Found invalid navbar link type (must be "text" or "icon")');
        }
    }

    $external_links = array();
    foreach ($config_json['external_links'] as $link_json) {
        $external_links[] = new \ExternalLink($link_json['url'], $link_json['text'],
                get_array_item($link_json, 'title_text'));
    }

    $config = new BlogConfig();
    $config->database = $db_config;
    $config->admin_ticket = $config_json['admin_ticket'];
    $config->site_title = $config_json['site_title'];
    $config->site_header = $config_json['site_header'];
    $config->home_recent_post_count = $config_json['home_recent_post_count'];
    $config->sidebar_recent_post_count = $config_json['sidebar_recent_post_count'];
    $config->post_preview_chars = $config_json['post_preview_chars'];
    $config->copyright_name = $config_json['copyright']['name'];
    $config->copyright_start_year = $config_json['copyright']['start_year'];
    $config->copyright_end_year = get_array_item($config_json['copyright'], 'end_year', null); 
    $config->contact_email = $config_json['contact_email'];
    $config->contact_email_text = $config_json['contact_email_text'];
    $config->github_url = $config_json['github_url'];
    $config->navbar_links = $navbar_links;
    $config->external_links = $external_links;

    $_SESSION['last_config_path'] = CONFIG_PATH;
    $_SESSION['last_config_update'] = filemtime(CONFIG_PATH);
    $_SESSION['last_config_obj'] = $config;
    
    return $config;
}

function init_config(): BlogConfig {
    if (session_id() !== '') {
        throw new RuntimeException('Global config must be initialized before any session is started');
    }

    if (!file_exists(CONFIG_PATH)) {
        throw new RuntimeException('Config file is missing');
    }

    return parse_config();

    //TODO: cache config across requests
    /*$last_config_path = get_session_var('last_config_path', '');
    $last_config_update = get_session_var('last_config_update', 0);
    $last_config_obj = get_session_var('last_config_obj', null);
    if (true || $last_config_path !== CONFIG_PATH
            || $last_config_update !== filemtime(CONFIG_PATH)
            || $last_config_obj === null) {
        return parse_config();
    }

    return $last_config_obj;*/
}

$_blog_config = init_config();

function get_config(): BlogConfig {
    global $_blog_config;
    return $_blog_config;
}
