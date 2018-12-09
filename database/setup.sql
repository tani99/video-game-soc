create database lab_project;
use lab_project;

create table users
(
  id serial primary key,
  name varchar(255) not null,
  email varchar(255) not null,
  password varchar(255) not null,
  volunteer boolean not null default false,
  banned boolean not null default false
);

create table roles
(
  id serial not null primary key,
  name varchar(255) not null
);

create table user_roles
(
  idUser serial not null,
  idRole serial not null,
  primary key (idUser, idRole),
  foreign key (idUser) references users (id) on delete CASCADE on update CASCADE,
  foreign key (idRole) references roles (id)
);

-- ALTER TABLE users ADD COLUMN owingGame int;
CREATE TYPE platform AS ENUM ('PC', 'PS4', 'Xbox One', 'Nintendo Switch');
create table game
(
  id serial primary key,
  name varchar(255),
  releaseYear INT,
  type varchar(255),
  description varchar(1000),
  rating decimal(1,1) CHECK (rating<=5.0 and rating>0.0),
  imageURL varchar(255),
  recommendedURL varchar(255)
  onplatform platform,
);

create table rentals
(
  id serial primary key,
  iduser serial not null,
  idgame serial,
  startdate timestamp DEFAULT NOW(),
  enddate timestamp default (NOW() + rules.rentalPeriod),
  extensions int check (extensions <= rules.extensionLimit) default (0),
  foreign key (iduser) references users (id) on delete CASCADE on update CASCADE,
  foreign key (idgame) references Game (id) on delete set null on update CASCADE
);

create table violations 
(
  iduser serial not null,
  violationdate date,
  timesviolated int check (timesviolated <= rules.rentGameLimit) default (1),
  firstviolation timestamp,
  latestviolation timestamp check (latestviolation - firstviolation < rules.ruleVioPeriod) default now(),
  primary key (iduser),
  foreign key (iduser) references users (id) on delete set null on update cascade
);



create table bannedmembers 
( 
  iduser serial not null,
  datebanned timestamp,
  banperiod interval not null,
  banneduntil timestamp default null,
  primary key (iduser),
  foreign key (banperiod) references rules (banPeriod) on delete set null on update cascade,
  foreign key (iduser) references users (id) on delete set null on update cascade
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

insert into rules
values(2, '3 weeks', 2, 3, '1 years', '6 months');


-- create rule bannedperiod as on insert to bannedmembers where ((SELECT(banneduntil) FROM bannedmembers) = null)
-- do insert into bannedmembers (banneduntil) values((SELECT(datebanned)) + (SELECT (banperiod) FROM rules));