USE myplaylist;
CREATE TABLE users (user_id int AUTO_INCREMENT,
username varchar(255), password varchar(255),
fName varchar(255), lName varchar(255), email varchar(255), 'date' TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
guestBool boolean,
PRIMARY KEY(user_id));

CREATE TABLE playlists(playlist_id int AUTO_INCREMENT, title varchar(255),
user_id int NOT NULL, access_code int NOT NULL,isPublic boolean, PRIMARY KEY(playlist_id));

CREATE TABLE access(access_id int AUTO_INCREMENT, user_id int NOT NULL,
access_code int NOT NULL, PRIMARY KEY(access_id), FOREIGN KEY(user_id) REFERENCES users(user_id));

CREATE TABLE active(active_id int AUTO_INCREMENT, user_id int NOT NULL,
song_id int NOT NULL, playlist_id int NOT NULL, likes int NOT NULL,
PRIMARY KEY(active_id), FOREIGN KEY(user_id) REFERENCES users(user_id),
FOREIGN KEY(playlist_id) REFERENCES playlists(playlist_id));

CREATE TABLE library(song_id int AUTO_INCREMENT, url varchar(255),
PRIMARY KEY(song_id));
 
CREATE TABLE user_likes(like_id int AUTO_INCREMENT, access_id int NOT NULL,
user_id int NOT NULL,
active_id int NOT NULL,
PRIMARY KEY(like_id),
FOREIGN KEY (user_id) REFERENCES users(user_id),
FOREIGN KEY (active_id) REFERENCES active(active_id),
FOREIGN KEY (access_id) REFERENCES access(access_id));

ALTER TABLE playlists ADD FOREIGN KEY(user_id) REFERENCES users(user_id);
ALTER TABLE access ADD FOREIGN KEY(user_id) REFERENCES users(user_id);
ALTER TABLE active ADD FOREIGN KEY(song_id) REFERENCES library(song_id);
ALTER TABLE user_likes ADD FOREIGN KEY(active_id) REFERENCES active(active_id);
ALTER TABLE user_likes ADD FOREIGN KEY(access_id) REFERENCES access(access_id);
ALTER TABLE user_likes ADD FOREIGN KEY(user_id) REFERENCES users(user_id);
