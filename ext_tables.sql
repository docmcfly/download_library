CREATE TABLE tx_downloadlibrary_domain_model_document
(
   title varchar (255) NOT NULL,
   file_reference tinytext DEFAULT NULL,
   owner int (11) unsigned NOT NULL DEFAULT '0',
   final int (1) NOT NULL DEFAULT '0',
   archived int (1) NOT NULL DEFAULT '0',
   status DATE  DEFAULT '0000-00-00' NOT NULL
);