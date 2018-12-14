create database lab_project;
use lab_project;

create table users
(
  id serial primary key,
  name varchar(255) not null,
  email varchar(255) not null,
  password varchar(255) not null,
  email_verified_at timestamp(0)
  without time zone,
  remember_token varchar
  (100),
  created_at timestamp
  (0) without time zone,
  updated_at timestamp
  (0) without time zone,
  banned boolean not null default false,
  idrole int,
  foreign key
  (idrole) references roles
  (id)
);

  create table roles
  (
    id serial not null primary key,
    name varchar(255) not null
  );

  CREATE TYPE platform AS ENUM
  ('PC', 'PS4', 'Xbox One', 'Nintendo Switch');

  create table game
  (
    id serial primary key,
    name varchar(255),
    releaseYear INT,
    type varchar(255),
    description varchar(1000),
    rating decimal(1,1) CHECK (rating<=10.0 and rating>0.0),
    recommendedURL varchar(255),
    onplatform platform,
    damaged boolean default false
  );

  create table rentals
  (
    id serial primary key,
    iduser serial not null,
    idgame serial,
    startdate timestamp DEFAULT NOW(),
    enddate timestamp,
    extensions int default 0,
    foreign key (iduser) references users (id) on delete CASCADE on update CASCADE,
    foreign key (idgame) references Game (id) on delete set null on update CASCADE
  );

  create table violations
  (
    id serial not null primary key,
    iduser serial not null,
    violationdate timestamp not null,
    reason varchar,
    foreign key (iduser) references users (id) on delete set null on update cascade
  );

  create table bannedmembers
  (
    iduser serial not null,
    datebanned timestamp,
    primary key (iduser),
    foreign key (iduser) references users (id) on delete set null on update cascade
  );

  create table damagedrefunds
  (
    id serial not null primary key,
    iduser int not null,
    idgame int not null,
    refunded boolean default false,
    foreign key (iduser) references users (id) on delete set null on update cascade,
    foreign key (idgame) references game (id) on delete set null on update cascade
  );

  create table rules
  (
    rentGameLimit int not null,
    rentalPeriod interval not null,
    extensionLimit int not null,
    ruleVioLimitPerPeriod int not null,
    ruleVioPeriod interval not null,
    banPeriod interval not null
  );

  create or replace view currentrentals
as
  (SELECT iduser, idgame, startdate, enddate, users.name as username, extensions, startdate+ (extensions+1)* (SELECT rentalperiod
    FROM rules) as duedate, rentals.id as rentalid
  from rentals inner join game on rentals.idgame=game.id
    inner join
    users
    on rentals.iduser=users.id
  where enddate is null);

  create or replace view currentviolations
as
  select iduser, count(*)
  from violations
  where violationdate>(NOW()- (SELECT rulevioperiod
  FROM rules))
  group by iduser;

  create or replace view currentusers
  as
  SELECT id, name, email, created_at, updated_at, volunteer, secretary,
    (CASE WHEN isbanned is null THEN false ELSE true END) as banned,
    CASE WHEN currentrentals is NULL THEN 0 ELSE currentrentals END AS currentrentals
  from
    (select *,
      (CASE WHEN idrole is null THEN false ELSE true END) as volunteer,
      (CASE WHEN idrole = 2 THEN true ELSE false END) as secretary
    from users) users
    LEFT JOIN
    (select iduser,
      count(*) as currentrentals
    from currentrentals
    group by iduser) rentals
    ON (users.id = rentals.iduser)
    left outer join
    (select iduser,
      count(*) as isbanned
    from bannedmembers
    group by iduser) bannedmembers
    on users.id=bannedmembers.iduser;

  create or replace view currentgames
    as
  SELECT game.id, name, startdate, enddate, iduser,
    (CASE WHEN iduser is not null and enddate is null THEN false ELSE true END) as isavailable
  FROM game
    LEFT JOIN
    (select *
    from rentals
    where enddate is null) currentrentals
    ON (game.id = currentrentals.idgame);

  insert into rules
  values(2, '3 weeks', 2, 3, '1 years', '6 months');
