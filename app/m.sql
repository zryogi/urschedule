Enum role_name_enum {
  admin
  professor
}

Enum event_type_name_enum {
  lecture
  seminar
  talk
  office_hours
  meeting
  conference_presentation
  workshop
  other
}

Table users {
  id bigserial [pk, increment]
  first_name varchar(255) [not null]
  last_name varchar(255) [not null]
  email varchar(255) [unique, not null]
  password_hash varchar(255) [not null]
  role_id bigint [not null, ref: > roles.id]
  created_at timestamptz [default: `now()`, not null]
  updated_at timestamptz [default: `now()`, not null]
}

Table roles {
  id bigserial [pk, increment]
  name role_name_enum [unique, not null]
  description text
  created_at timestamptz [default: `now()`, not null]
  updated_at timestamptz [default: `now()`, not null]
}

Table event_types {
  id bigserial [pk, increment]
  name event_type_name_enum [unique, not null]
  description text
  created_at timestamptz [default: `now()`, not null]
  updated_at timestamptz [default: `now()`, not null]
}

Table events {
  id bigserial [pk, increment]
  title varchar(255) [not null]
  description text
  start_datetime timestamptz [not null]
  end_datetime timestamptz [not null]
  event_type_id bigint [not null, ref: > event_types.id]
  location varchar(255)
  is_recurring boolean [default: false, not null]
  recurrence_rule text
  created_by_user_id bigint [not null, ref: > users.id]
  created_at timestamptz [default: `now()`, not null]
  updated_at timestamptz [default: `now()`, not null]
  status event_status_enum [default: 'scheduled']

  Note: '''
    Table constraint: end_datetime > start_datetime
  '''
}

Enum event_status_enum {
   scheduled
   cancelled
   completed
   rescheduled
}

Table user_events {
  user_id bigint [not null, ref: > users.id]
  event_id bigint [not null, ref: > events.id]
  is_mandatory boolean
  rsvp_status rsvp_status_enum
  created_at timestamptz [default: `now()`, not null]

  Indexes {
    (user_id, event_id) [pk]
  }
}

Enum rsvp_status_enum {
  accepted
  declined
  tentative
  no_response
}



