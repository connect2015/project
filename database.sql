create database connect;

grant all on connect.* to dbuser@localhost identified by 'connect2015';

use connect;

//usersのテーブル作成
create table users (
    id int not null auto_increment primary key,
    username varchar(50),
    password varchar(255),
    email varchar(255),
    university_id int,
    created datetime default null,
    modified datetime default null
);

//postsのテーブル作成
create table posts (
    id int not null auto_increment primary key,
    user_id int,
    university_id int,
    title text,
    body text,
    created datetime default null,
    modified datetime default null
);

//reviewsのテーブル作成
create table reviews (
    id int not null auto_increment primary key,
    user_id int,
    university_id int,
    category_id int,
    body text,
    created datetime default null,
    modified datetime default null
);

//countriesのテーブル作成
create table countries (
    id int not null auto_increment primary key,
    countryname varchar(255)
  );

//データの挿入
insert into countries (countryname) values
('America'),
('Japan'),
('Belgium'),
('France'),
('Italy'),
('Korea'),
('China'),
('India');


//universitiesのテーブル作成
create table universities (
    id int not null auto_increment primary key,
    country_id int ,
    universityname varchar(255)
   );

//データの挿入
insert into universities (country_id, universityname) values 
(3,'Ghent'),
(1,'UCLA'),
(2,'Keio'),
(2,'Titech');



//categoriesのテーブル作成
create table categories (
    id int not null auto_increment primary key,
    categoryname varchar(50)
    );

//データの挿入
insert into categories (categoryname) values 
('city'),
('university'),
('food');


//imagesのテーブル作成
create table images (
    id int not null auto_increment primary key,
    post_id int,
    user_id int,
    university_id int,
    filename varchar(255),
    filepath varchar(255),
    uploaded datetime default null
);

