

DROP TABLE IF EXISTS ip_page;

CREATE TABLE ip_page (
  "id" serial PRIMARY KEY,
  "languageCode" varchar(6) DEFAULT NULL,
  "urlPath" varchar(140) DEFAULT NULL,
  "parentId" int DEFAULT NULL,
  "pageOrder" int NOT NULL DEFAULT 0,
  "title" varchar(255) NOT NULL,
  "metaTitle" text,
  "keywords" text,
  "description" text,
  "type" varchar(255) NOT NULL DEFAULT 'default',
  "alias" varchar(255) DEFAULT NULL,
  "layout" varchar(255) DEFAULT NULL,
  "redirectUrl" varchar(255) DEFAULT NULL,
  "isVisible" smallint NOT NULL DEFAULT 0,
  "isDisabled" smallint NOT NULL DEFAULT 0,
  "isSecured" smallint NOT NULL DEFAULT 0,
  "isDeleted" smallint NOT NULL DEFAULT 0,
  "isBlank" BOOLEAN NOT NULL DEFAULT FALSE,
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updatedAt" timestamp NULL DEFAULT NULL,
  "deletedAt" timestamp NULL DEFAULT NULL
);

CREATE INDEX url ON ip_page("urlPath", "languageCode");


DROP TABLE IF EXISTS ip_page_storage;

CREATE TABLE ip_page_storage (
  "pageId" int NOT NULL,
  key varchar(255) NOT NULL,
  value text NOT NULL,
  PRIMARY KEY ("pageId",key)
);

DROP TABLE IF EXISTS ip_permission;

CREATE TABLE ip_permission (
  "administratorId" int NOT NULL DEFAULT 0,
  permission varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY ("administratorId",permission)
);

DROP TABLE IF EXISTS ip_language;

CREATE TABLE ip_language (
  id serial PRIMARY KEY,
  abbreviation varchar(255) NOT NULL DEFAULT '',
  title varchar(255) NOT NULL DEFAULT '',
  "languageOrder" smallint NOT NULL DEFAULT 0,
  "isVisible" smallint NOT NULL DEFAULT 0,
  url varchar(255) NOT NULL DEFAULT '',
  code varchar(255) NOT NULL,
  "textDirection" varchar(10) NOT NULL DEFAULT 'ltr'
);

DROP TABLE IF EXISTS ip_log;

CREATE TABLE ip_log (
  id serial PRIMARY KEY,
  time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  level varchar(255) NOT NULL,
  message varchar(255) DEFAULT NULL,
  context text
);



DROP TABLE IF EXISTS ip_email_queue;

CREATE TABLE ip_email_queue (
  id serial PRIMARY KEY,
  email text NOT NULL,
  "to" varchar(255) NOT NULL,
  "toName" varchar(255) DEFAULT NULL,
  "from" varchar(255) NOT NULL,
  "fromName" varchar(255) DEFAULT NULL,
  subject varchar(255) NOT NULL,
  "immediate" smallint NOT NULL DEFAULT 0,
  html smallint NOT NULL,
  send timestamp DEFAULT NULL,
  "lock" varchar(32) DEFAULT NULL,
  "lockedAt" timestamp NOT NULL DEFAULT '-infinity',
  files text,
  "fileNames" text,
  "fileMimeTypes" text NOT NULL
);

DROP TABLE IF EXISTS ip_repository_file;

CREATE SEQUENCE "ip_repository_file_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE ip_repository_file (
  "fileId" integer DEFAULT nextval('"ip_repository_file_id_seq"'::regclass) PRIMARY KEY,
  filename varchar(255) NOT NULL,
  plugin varchar(255) NOT NULL,
  "baseDir" VARCHAR(255) NOT NULL,
  "instanceId" int8 NOT NULL,
  "createdAt" timestamp NOT NULL default CURRENT_TIMESTAMP
);


CREATE INDEX filenameIdx ON ip_repository_file(filename);

COMMENT ON COLUMN ip_repository_file."instanceId" IS 'Unique identificator. Tells in which part of the module the file is used. Theoretically there could be two identical records. The same module binds the same file to the same instance. For example: gallery widget adds the same photo twice.';
COMMENT ON COLUMN ip_repository_file."createdAt" IS 'Time, when this module started to use this resource.';


DROP TABLE IF EXISTS ip_repository_reflection;

CREATE SEQUENCE "ip_repository_reflection_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE ip_repository_reflection (
  "reflectionId" integer DEFAULT nextval('"ip_repository_reflection_id_seq"'::regclass) PRIMARY KEY,
  options text NOT NULL,
  "optionsFingerprint" char(32),
  original varchar(255) NOT NULL,
  reflection varchar(255) NOT NULL,
  "createdAt" timestamp NOT NULL default CURRENT_TIMESTAMP
);

CREATE INDEX fingerprint ON ip_repository_reflection("optionsFingerprint");

COMMENT ON TABLE ip_repository_reflection IS 'Cropped versions of original image file';
COMMENT ON COLUMN ip_repository_reflection."optionsFingerprint" IS 'unique options cropping key';

DROP TABLE IF EXISTS ip_widget;

CREATE TABLE ip_widget (
  id serial PRIMARY KEY,
  name varchar(50) NOT NULL,
  skin varchar(25) NOT NULL,
  data text NOT NULL,
  "revisionId" int NOT NULL,
  "languageId" int NOT NULL,
  "blockName" varchar(25) NOT NULL,
  position int NOT NULL,
  "isVisible" smallint NOT NULL DEFAULT 1,
  "isDeleted" smallint NOT NULL DEFAULT 0,
  "createdAt" timestamp NOT NULL default CURRENT_TIMESTAMP,
  "updatedAt" timestamp,
  "deletedAt" timestamp
);

DROP TABLE IF EXISTS ip_theme_storage;

CREATE TABLE ip_theme_storage (
  theme varchar(100) NOT NULL,
  key varchar(100) NOT NULL,
  value varchar(255) NOT NULL,
  PRIMARY KEY (theme,key)
);

DROP TABLE IF EXISTS ip_widget_order;

CREATE TABLE ip_widget_order (
  "widgetName" varchar(255) NOT NULL,
  priority int NOT NULL DEFAULT 0,
  PRIMARY KEY ("widgetName")
);


DROP TABLE IF EXISTS ip_inline_value_global;

CREATE TABLE ip_inline_value_global (
  plugin varchar(100) NOT NULL,
  key varchar(100) NOT NULL,
  value text NOT NULL,
  PRIMARY KEY (plugin,key)
);



DROP TABLE IF EXISTS ip_inline_value_language;

CREATE TABLE ip_inline_value_language (
  plugin varchar(100) NOT NULL,
  key varchar(100) NOT NULL,
  "languageId" int NOT NULL,
  value text NOT NULL,
  PRIMARY KEY (plugin,key,"languageId")
);


DROP TABLE IF EXISTS ip_inline_value_page;

CREATE TABLE ip_inline_value_page (
  plugin varchar(100) NOT NULL,
  key varchar(100) NOT NULL,
  "pageId" int NOT NULL,
  value text NOT NULL,
  PRIMARY KEY (plugin,key,"pageId")
);



DROP TABLE IF EXISTS ip_plugin;

CREATE TABLE ip_plugin (
  title varchar(100) NOT NULL,
  name varchar(30),
  version decimal(10,2) NOT NULL,
  "isActive" int NOT NULL DEFAULT 1,
  PRIMARY KEY (name)
);


DROP TABLE IF EXISTS ip_revision;

CREATE SEQUENCE "ip_revision_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


CREATE TABLE ip_revision (
  "revisionId" integer  DEFAULT nextval('"ip_revision_id_seq"'::regclass)  PRIMARY KEY,
  "pageId" int NOT NULL DEFAULT 0,
  "isPublished" smallint NOT NULL DEFAULT 0,
  "createdAt" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS ip_storage;

CREATE TABLE ip_storage (
    plugin varchar(40) NOT NULL,
    key varchar(100) NOT NULL,
    value text NOT NULL,
    PRIMARY KEY (plugin,key)
);



DROP TABLE IF EXISTS ip_administrator;

CREATE TABLE ip_administrator (
  id serial PRIMARY KEY,
  username varchar(255) UNIQUE NOT NULL DEFAULT '' ,
  hash text NOT NULL,
  email varchar(255) NOT NULL DEFAULT '',
  "resetSecret" varchar(32) DEFAULT NULL,
  "resetTime" timestamp DEFAULT NULL
);

