CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "password_resets"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime
);
CREATE INDEX "password_resets_email_index" on "password_resets"("email");
CREATE TABLE IF NOT EXISTS "oauth_auth_codes"(
  "id" varchar not null,
  "user_id" integer not null,
  "client_id" integer not null,
  "scopes" text,
  "revoked" tinyint(1) not null,
  "expires_at" datetime,
  primary key("id")
);
CREATE INDEX "oauth_auth_codes_user_id_index" on "oauth_auth_codes"("user_id");
CREATE TABLE IF NOT EXISTS "oauth_access_tokens"(
  "id" varchar not null,
  "user_id" integer,
  "client_id" integer not null,
  "name" varchar,
  "scopes" text,
  "revoked" tinyint(1) not null,
  "created_at" datetime,
  "updated_at" datetime,
  "expires_at" datetime,
  primary key("id")
);
CREATE INDEX "oauth_access_tokens_user_id_index" on "oauth_access_tokens"(
  "user_id"
);
CREATE TABLE IF NOT EXISTS "oauth_refresh_tokens"(
  "id" varchar not null,
  "access_token_id" varchar not null,
  "revoked" tinyint(1) not null,
  "expires_at" datetime,
  primary key("id")
);
CREATE INDEX "oauth_refresh_tokens_access_token_id_index" on "oauth_refresh_tokens"(
  "access_token_id"
);
CREATE TABLE IF NOT EXISTS "oauth_clients"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "name" varchar not null,
  "secret" varchar,
  "provider" varchar,
  "redirect" text not null,
  "personal_access_client" tinyint(1) not null,
  "password_client" tinyint(1) not null,
  "revoked" tinyint(1) not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "oauth_clients_user_id_index" on "oauth_clients"("user_id");
CREATE TABLE IF NOT EXISTS "oauth_personal_access_clients"(
  "id" integer primary key autoincrement not null,
  "client_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "domains"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "framework" varchar
);
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" varchar not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE TABLE IF NOT EXISTS "documents"(
  "id" integer primary key autoincrement not null,
  "control_id" integer not null,
  "filename" varchar not null,
  "mimetype" varchar not null,
  "size" integer not null,
  "hash" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("control_id") references "controls"("id")
);
CREATE TABLE IF NOT EXISTS "control_user"(
  "control_id" integer not null,
  "user_id" integer not null,
  foreign key("control_id") references "controls"("id") on delete CASCADE on update NO ACTION,
  foreign key("user_id") references "users"("id") on delete CASCADE on update NO ACTION
);
CREATE INDEX "control_id_fk_5920381" on "control_user"("control_id");
CREATE INDEX "user_id_fk_5837573" on "control_user"("user_id");
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "login" varchar not null,
  "name" varchar not null,
  "email" varchar not null,
  "title" varchar not null,
  "role" integer not null,
  "profile_image" integer,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "language" varchar
);
CREATE UNIQUE INDEX "users_login_unique" on "users"("login");
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "attributes"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "values" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "tags_name_unique" on "attributes"("name");
CREATE TABLE IF NOT EXISTS "control_measure"(
  "control_id" integer not null,
  "measure_id" integer not null,
  foreign key("control_id") references "controls"("id"),
  foreign key("measure_id") references "measures"("id")
);
CREATE TABLE IF NOT EXISTS "controls"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "objective" text,
  "input" text,
  "model" text,
  "indicator" text,
  "action_plan" text,
  "periodicity" integer,
  "plan_date" date not null,
  "realisation_date" date,
  "observations" text,
  "score" integer,
  "note" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "next_id" integer,
  "standard" varchar,
  "attributes" varchar,
  "site" varchar,
  "scope" varchar,
  "status" integer not null default('0'),
  foreign key("next_id") references controls("id") on delete no action on update no action
);
CREATE TABLE IF NOT EXISTS "measures"(
  "id" integer primary key autoincrement not null,
  "domain_id" integer not null,
  "name" varchar not null,
  "clause" varchar not null,
  "objective" text,
  "input" text,
  "model" text,
  "indicator" text,
  "action_plan" text,
  "created_at" datetime,
  "updated_at" datetime,
  "standard" varchar,
  "attributes" varchar,
  foreign key("domain_id") references domains("id") on delete no action on update no action
);
CREATE UNIQUE INDEX "measures_clause_unique" on "measures"("clause");

INSERT INTO migrations VALUES(1,'2014_10_12_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO migrations VALUES(3,'2016_06_01_000001_create_oauth_auth_codes_table',1);
INSERT INTO migrations VALUES(4,'2016_06_01_000002_create_oauth_access_tokens_table',1);
INSERT INTO migrations VALUES(5,'2016_06_01_000003_create_oauth_refresh_tokens_table',1);
INSERT INTO migrations VALUES(6,'2016_06_01_000004_create_oauth_clients_table',1);
INSERT INTO migrations VALUES(7,'2016_06_01_000005_create_oauth_personal_access_clients_table',1);
INSERT INTO migrations VALUES(8,'2019_07_28_175941_create_domains_table',1);
INSERT INTO migrations VALUES(9,'2019_08_09_084322_create_measures_table',1);
INSERT INTO migrations VALUES(10,'2019_08_09_105245_create_controls_table',1);
INSERT INTO migrations VALUES(11,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(12,'2020_04_12_073028_create_documents_table',1);
INSERT INTO migrations VALUES(13,'2022_04_23_081110_add_next_control_id',1);
INSERT INTO migrations VALUES(14,'2022_05_15_030940_control_score_to_int',1);
INSERT INTO migrations VALUES(15,'2022_12_21_113730_add_user_language',1);
INSERT INTO migrations VALUES(16,'2023_01_29_114100_add_tags',1);
INSERT INTO migrations VALUES(17,'2023_01_30_180336_normalization',1);
INSERT INTO migrations VALUES(18,'2023_03_09_222639_alter_attributes_values',1);
INSERT INTO migrations VALUES(19,'2023_04_06_202034_alter_attribute_length',1);
INSERT INTO migrations VALUES(20,'2023_04_19_112145_change_clause_type',1);
INSERT INTO migrations VALUES(21,'2023_06_18_170340_owner',1);
INSERT INTO migrations VALUES(22,'2023_08_22_095642_add_scope',1);
INSERT INTO migrations VALUES(23,'2024_04_15_193546_attributes_values_text',1);
INSERT INTO migrations VALUES(24,'2024_04_20_192325_add_control_status',1);
INSERT INTO migrations VALUES(25,'2024_06_27_123923_add_control_measure_table',1);
INSERT INTO migrations VALUES(26,'2024_07_02_101657_add_framework_to_domains',1);
INSERT INTO migrations VALUES(27,'2024_07_05_174735_clause_unique',1);
INSERT INTO migrations VALUES(28,'2024_10_01_181052_remove_clause',1);
