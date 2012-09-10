group { "puppet":
    ensure => "present",
}

$packages = [
    "vim",
    "sqlite3",
    "curl",
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

package { $packages: 
    ensure => installed,
}
