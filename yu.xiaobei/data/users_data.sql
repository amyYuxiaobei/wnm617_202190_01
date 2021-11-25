CREATE TABLE IF NOT EXISTS `track_users` (
`id` INT NULL,
`name` VARCHAR(MAX) NULL,
`username` VARCHAR(MAX) NULL,
`email` VARCHAR(MAX) NULL,
`password` VARCHAR(MAX) NULL,
`img` VARCHAR(MAX) NULL,
`date_create` VARCHAR(MAX) NULL
);

INSERT INTO track_users VALUES
(1,'Bennett Fernandez','user1','user1@gmail.com',md5('pass'),'https://via.placeholder.com/400/778/fff/?text=user1','2021-05-03 04:28:03'),
(2,'Bates Cleveland','user2','user2@gmail.com',md5('pass'),'https://via.placeholder.com/400/972/fff/?text=user2','2020-08-04 09:09:44'),
(3,'Tate Cross','user3','user3@gmail.com',md5('pass'),'https://via.placeholder.com/400/885/fff/?text=user3','2020-05-23 12:25:27'),
(4,'Copeland Guerra','user4','user4@gmail.com',md5('pass'),'https://via.placeholder.com/400/766/fff/?text=user4','2020-08-26 04:58:39'),
(5,'Harvey Davenport','user5','user5@gmail.com',md5('pass'),'https://via.placeholder.com/400/738/fff/?text=user5','2020-05-03 10:50:50'),
(6,'Merrill Francis','user6','user6@gmail.com',md5('pass'),'https://via.placeholder.com/400/991/fff/?text=user6','2021-06-01 02:48:17'),
(7,'Cecilia Tillman','user7','user7@gmail.com',md5('pass'),'https://via.placeholder.com/400/701/fff/?text=user7','2021-09-03 01:55:10'),
(8,'Hewitt Valdez','user8','user8@gmail.com',md5('pass'),'https://via.placeholder.com/400/945/fff/?text=user8','2020-04-07 11:32:20'),
(9,'Kasey Foley','user9','user9@gmail.com',md5('pass'),'https://via.placeholder.com/400/950/fff/?text=user9','2021-09-28 05:16:14'),
(10,'Brenda Burris','user10','user10@gmail.com',md5('pass'),'https://via.placeholder.com/400/843/fff/?text=user10','2020-07-14 01:22:31');