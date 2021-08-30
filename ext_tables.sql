CREATE TABLE sys_file_reference (
    controls tinyint(4) DEFAULT '1' NOT NULL,
    loop tinyint(4) DEFAULT '0' NOT NULL,
    muted tinyint(4) DEFAULT '0' NOT NULL,
    preload tinyint(4) DEFAULT '0' NOT NULL,
    defer tinyint(4) DEFAULT '0' NOT NULL,
    relatedVideos tinyint(4) DEFAULT '0' NOT NULL,
    preview_image int(11) unsigned NOT NULL default '0',
);
