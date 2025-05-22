CREATE TYPE "role_name_enum" AS ENUM (
  'admin',
  'professor'
);

CREATE TYPE "event_type_name_enum" AS ENUM (
  'lecture',
  'seminar',
  'talk',
  'office_hours',
  'meeting',
  'conference_presentation',
  'workshop',
  'other'
);

CREATE TYPE "event_status_enum" AS ENUM (
  'scheduled',
  'cancelled',
  'completed',
  'rescheduled'
);

CREATE TYPE "rsvp_status_enum" AS ENUM (
  'accepted',
  'declined',
  'tentative',
  'no_response'
);

CREATE TABLE "users" (
  "id" BIGSERIAL PRIMARY KEY,
  "first_name" varchar(255) NOT NULL,
  "last_name" varchar(255) NOT NULL,
  "email" varchar(255) UNIQUE NOT NULL,
  "password_hash" varchar(255) NOT NULL,
  "role_id" bigint NOT NULL,
  "work_day_id" biginf,
  "created_at" timestamptz NOT NULL DEFAULT (now()),
  "updated_at" timestamptz NOT NULL DEFAULT (now())
);

CREATE TABLE "roles" (
  "id" BIGSERIAL PRIMARY KEY,
  "name" role_name_enum UNIQUE NOT NULL,
  "description" text,
  "created_at" timestamptz NOT NULL DEFAULT (now()),
  "updated_at" timestamptz NOT NULL DEFAULT (now())
);

CREATE TABLE "event_types" (
  "id" BIGSERIAL PRIMARY KEY,
  "name" event_type_name_enum UNIQUE NOT NULL,
  "description" text,
  "created_at" timestamptz NOT NULL DEFAULT (now()),
  "updated_at" timestamptz NOT NULL DEFAULT (now())
);

CREATE TABLE "events" (
  "id" BIGSERIAL PRIMARY KEY,
  "title" varchar(255) NOT NULL,
  "description" text,
  "start_datetime" timestamptz NOT NULL,
  "end_datetime" timestamptz NOT NULL,
  "event_type_id" bigint NOT NULL,
  "location" varchar(255),
  "classroom_id" bigint,
  "is_recurring" boolean NOT NULL DEFAULT false,
  "occurrence_id" bigint,
  "created_by_user_id" bigint NOT NULL,
  "created_at" timestamptz NOT NULL DEFAULT (now()),
  "updated_at" timestamptz NOT NULL DEFAULT (now()),
  "status" event_status_enum DEFAULT 'scheduled',
  "hourly_rate" decimal
);

CREATE TABLE "user_events" (
  "user_id" bigint NOT NULL,
  "event_id" bigint NOT NULL,
  "is_mandatory" boolean,
  "rsvp_status" rsvp_status_enum,
  "created_at" timestamptz NOT NULL DEFAULT (now()),
  PRIMARY KEY ("user_id", "event_id")
);

CREATE TABLE "campus" (
  "id" BIGSERIAL PRIMARY KEY,
  "name" varchar(255) NOT NULL,
  "address" varchar(255) NOT NULL
);

CREATE TABLE "classroom" (
  "id" BIGSERIAL PRIMARY KEY,
  "campus_id" bigint NOT NULL,
  "name" varchar(255) NOT NULL
);

CREATE TABLE "work_day" (
  "id" SERIAL PRIMARY KEY,
  "name" varchar(64) NOT NULL,
  "start_hour" int NOT NULL,
  "end_hour" int NOT NULL
);

CREATE TABLE "event_recurrence_rule" (
  "id" SERIAL PRIMARY KEY,
  "start_date" date NOT NULL,
  "end_date" date NOT NULL,
  "start_time" time NOT NULL,
  "end_time" time NOT NULL,
  "number_occurrences" int NOT NULL,
  "days" varchar(127) NOT NULL
);

COMMENT ON TABLE "events" IS 'Table constraint: end_datetime > start_datetime
';

ALTER TABLE "users" ADD FOREIGN KEY ("role_id") REFERENCES "roles" ("id");

ALTER TABLE "users" ADD FOREIGN KEY ("work_day_id") REFERENCES "work_day" ("id");

ALTER TABLE "events" ADD FOREIGN KEY ("event_type_id") REFERENCES "event_types" ("id");

ALTER TABLE "events" ADD FOREIGN KEY ("classroom_id") REFERENCES "classroom" ("id");

ALTER TABLE "events" ADD FOREIGN KEY ("occurrence_id") REFERENCES "event_recurrence_rule" ("id");

ALTER TABLE "events" ADD FOREIGN KEY ("created_by_user_id") REFERENCES "users" ("id");

ALTER TABLE "user_events" ADD FOREIGN KEY ("user_id") REFERENCES "users" ("id");

ALTER TABLE "user_events" ADD FOREIGN KEY ("event_id") REFERENCES "events" ("id");

ALTER TABLE "classroom" ADD FOREIGN KEY ("campus_id") REFERENCES "campus" ("id");
