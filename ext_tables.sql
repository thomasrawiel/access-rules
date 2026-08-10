CREATE TABLE tx_accessrules_rule
(
	`parent`       int(11)      DEFAULT '0' NOT NULL,
	`parent_table` varchar(512) DEFAULT ''  NOT NULL,
	`mode`         int(10)      default '0' NOT NULL,
	`usergroups`   int(10)      default '0' NOT NULL
);
