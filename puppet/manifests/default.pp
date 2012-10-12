$packages = [
    "vim",
    "sqlite3",
    "curl",
    "nginx",
    "php5-fpm",
    "php5-sqlite",
    "php-apc",
    "git",
    "php5-cli",
    "php5-xdebug",
]

$dev_packages = [
    "libssl-dev",
    "libxml2-dev",
    "libgmp-dev",
    "libmcrypt-dev",
    "libxslt-dev",
    "libbz2-dev",
    "libcurl4-openssl-dev",
    "libpq-dev",
    "libpcre3-dev",
]

$services = ["nginx", "php5-fpm"]

group { "puppet":
    ensure => "present",
}

exec { "apt-get update":
    command => "/usr/bin/apt-get update",
}


package { $packages: 
    ensure  => installed,
    require => Exec["apt-get update"],
    notify  => Service[$services],
}

service { $services:
    ensure => running,
}

file {
    "/etc/nginx/sites-enabled/default":
        ensure    => absent,
        subscribe => Package["nginx"],
    ;

    "/etc/nginx/sites-available/pastebin":
        source  => "/vagrant/puppet/files/etc/nginx/sites-available/pastebin",
        notify  => [
            File["/etc/nginx/sites-enabled/pastebin"],
            Service["nginx"]
        ],
        require => Package["nginx"],
    ;

    "/etc/nginx/sites-enabled/pastebin":
        ensure => link,
        target => "/etc/nginx/sites-available/pastebin",
        notify => Service["nginx"],
    ;

    "/etc/php5/conf.d/timezone.ini":
        source  => "/vagrant/puppet/files/etc/php5/conf.d/timezone.ini",
        notify  => Service["php5-fpm"],
        require => Package["php5-fpm"],
    ;

    "/etc/localtime":
        source => "/usr/share/zoneinfo/America/Chicago",
    ;
}

exec { "update-alternatives":
    command   => "/usr/sbin/update-alternatives --set editor /usr/bin/vim.basic",
    subscribe => Package["vim"],
}
