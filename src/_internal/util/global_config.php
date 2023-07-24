<?php
namespace GlobalConfig;

use ImageNavbarLink;
use RuntimeException;
use TextNavbarLink;
use RelMeLink;

if (getenv('TOPAZ_CONFIG') === null) {
    throw new RuntimeException('No config specified! Please supply the path to the config file in the TOPAZ_CONFIG '
                              .'environment variable.');
}

require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/ext_link.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/navbar_link.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/model/rel_me_link.php';

class DatabaseConfig {
    public string $address;
    public string $name;
    public string $user;
    public string $password;
}

class UserPermsConfig {
    public string $admin_ticket;
    public bool $open_registration;
    public bool $show_login_links;
    public bool $allow_read_by_default;
}

class ContentConfig {
    public string $site_title;
    public string $site_description;
    public string $site_image;
    public string $site_icon;
    public string $site_header;
    public string $copyright_start_year;
    public ?string $copyright_end_year;
    public string $copyright_name;
    public string $contact_email;
    public string $contact_email_text;
    public string $github_url;
    public array $rel_me_links;
    public array $navbar_links;
    public array $external_links;
}

class DisplayConfig {
    public int $home_recent_post_count;
    public int $sidebar_recent_post_count;
    public int $post_preview_chars;
    public int $post_social_chars;
}

class InternalConfig {
    public bool $use_google_fonts;
}

class BlogConfig {
    public DatabaseConfig $database;
    public UserPermsConfig $user_perms;
    public ContentConfig $content;
    public DisplayConfig $display;
    public InternalConfig $internal;
}

function load_db_config($config_json): DatabaseConfig {
    $db_json = $config_json['database'];

    $db_config = new DatabaseConfig();
    $db_config->address = $db_json['address'];
    $db_config->name = $db_json['name'];
    $db_config->user = $db_json['user'];
    $db_config->password = $db_json['password'];

    return $db_config;
}

function load_user_perms_config($config_json): UserPermsConfig {
    $up_json = $config_json['user_permissions'];

    $up_config = new UserPermsConfig();
    $up_config->admin_ticket = $up_json['admin_ticket'];
    $up_config->open_registration = $up_json['open_registration'];
    $up_config->show_login_links = $up_json['show_login_links'];
    $up_config->allow_read_by_default = $up_json['allow_read_by_default'];

    return $up_config;
}

function load_content_config($config_json): ContentConfig {
    $content_json = $config_json['content'];

    $navbar_links = array();
    foreach ($content_json['navbar_links'] as $link_json) {
        if ($link_json['type'] === 'text') {
            $navbar_links[] = new TextNavbarLink($link_json['url'], $link_json['text'], $link_json['title_text']);
        } else if ($link_json['type'] === 'icon') {
            $navbar_links[] = new ImageNavbarLink($link_json['url'], $link_json['icon_namespace'],
                    $link_json['icon_id'], get_array_item($link_json, 'title_text'),
                    get_array_item($link_json, 'new_tab', false));
        } else {
            throw new RuntimeException('Found invalid navbar link type (must be "text" or "icon")');
        }
    }

    $external_links = array();
    foreach ($content_json['external_links'] as $link_json) {
        $external_links[] = new \ExternalLink($link_json['url'], $link_json['text'],
                get_array_item($link_json, 'title_text'), get_array_item($link_json, 'new_tab', false));
    }

    $rel_me_links = array();
    foreach ($content_json['rel_me_links'] as $link_json) {
        $rel_me_links[] = new RelMeLink($link_json['url'], $link_json['label']);
    }

    $content_config = new ContentConfig();

    $content_config->site_title = $content_json['site_title'];
    $content_config->site_description = $content_json['site_description'];
    $content_config->site_image = $content_json['site_image'];
    $content_config->site_icon = $content_json['site_icon'];
    $content_config->site_header = $content_json['site_header'];
    $content_config->copyright_name = $content_json['copyright']['name'];
    $content_config->copyright_start_year = $content_json['copyright']['start_year'];
    $content_config->copyright_end_year = get_array_item($content_json['copyright'], 'end_year', null); 
    $content_config->contact_email = $content_json['contact_email'];
    $content_config->contact_email_text = $content_json['contact_email_text'];
    $content_config->github_url = $content_json['github_url'];
    $content_config->navbar_links = $navbar_links;
    $content_config->external_links = $external_links;
    $content_config->rel_me_links = $rel_me_links;

    return $content_config;
}

function load_display_config($config_json): DisplayConfig {
    $display_json = $config_json['display'];

    $display_config = new DisplayConfig();

    $display_config->home_recent_post_count = $display_json['home_recent_post_count'];
    $display_config->sidebar_recent_post_count = $display_json['sidebar_recent_post_count'];
    $display_config->post_preview_chars = $display_json['post_preview_chars'];
    $display_config->post_social_chars = $display_json['post_social_chars'];

    return $display_config;
}

function load_internal_config($config_json): InternalConfig {
    $internal_json = $config_json['internal'];

    $internal_config = new InternalConfig();

    $internal_config->use_google_fonts = $internal_json['use_google_fonts'];

    return $internal_config;
}

function parse_config(): BlogConfig {
    $cfg_path = getenv('TOPAZ_CONFIG');

    $config_contents = file_get_contents($cfg_path);
    $config_json = json_decode($config_contents, true);

    $config = new BlogConfig();
    $config->database = load_db_config($config_json);
    $config->user_perms = load_user_perms_config($config_json);
    $config->content = load_content_config($config_json);
    $config->display = load_display_config($config_json);
    $config->internal = load_internal_config($config_json);

    $_SESSION['last_config_path'] = $cfg_path;
    $_SESSION['last_config_update'] = filemtime($cfg_path);
    $_SESSION['last_config_obj'] = $config;
    
    return $config;
}

function init_config(): BlogConfig {
    if (session_id() !== '') {
        throw new RuntimeException('Global config must be initialized before any session is started');
    }

    if (!file_exists(getenv('TOPAZ_CONFIG'))) {
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
