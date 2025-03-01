<?php

function makeConn() {
   include "auth.php";
   try {
      $conn = new PDO(...Auth());
      $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      return $conn;
   } catch(PDOException $e) {
      die('{"error":"'.$e->getMessage().'"}');
   }
}


/* $r = PDO result */
function fetchAll($r) {
   $a = [];
   while($row = $r->fetch(\PDO::FETCH_OBJ)) $a[] = $row;
   return $a;
}

/*
$c = connection
$ps = prepared statement
$p = parameters
*/
function makeUpload($file,$folder) {
   $filename = microtime(true) . "_" . $_FILES[$file]['name'];

   if(move_uploaded_file(
      $_FILES[$file]['tmp_name'],
      $folder.$filename
   )) return ['result'=>$filename];
   else {

      return [
         "folder"=>$folder,
         "folder_exists"=>is_dir($folder),
         "error"=>"File Upload Failed",
         "_FILES"=>$_FILES,
         "filename"=>$filename
      ];
   }
}
function makeQuery($c,$ps,$p,$makeResults=true) {
   try {
      if(count($p)) {
         $stmt = $c->prepare($ps);
         $stmt->execute($p);
      } else {
         $stmt = $c->query($ps);
      }

      $r = $makeResults ? fetchAll($stmt) : [];

      return [
         // "statement"=>$ps,
         // "params"=>$p,
         "result"=>$r
      ];
   } catch(PDOException $e) {
      return ["error"=>"Query Failed: ".$e->getMessage()];
   }
}



function makeStatement($data) {
   try{
      $c = makeConn();
      $t = @$data->type;
      $p = @$data->params;

      switch($t) {
         // case "users_all":
         //    return makeQuery($c,"SELECT * FROM `track_users`",$p);
         // case "animals_all":
         //    return makeQuery($c,"SELECT * FROM `track_animals`",$p);
         // case "locations_all":
         //    return makeQuery($c,"SELECT * FROM `track_locations`",$p);

         // CRUD 

         /* READ */

         case "user_by_id":
            return makeQuery($c,"SELECT id,username,name,email,img FROM `track_users` WHERE `id`=?",$p);
         case "animal_by_id":
            return makeQuery($c,"SELECT * FROM `track_animals` WHERE `id`=?",$p);
         case "location_by_id":
            return makeQuery($c,"SELECT * FROM `track_locations` WHERE `id`=?",$p);

         case "animals_by_user_id":
            return makeQuery($c,"SELECT * FROM `track_animals` WHERE `user_id`=?",$p);
         case "locations_by_animal_id":
            return makeQuery($c,"SELECT * FROM `track_locations` WHERE `animal_id`=?",$p);


         case "check_signin":
            return makeQuery($c,"SELECT id FROM `track_users` WHERE `username`=? AND `password`=md5(?)",$p);

         case "recent_animal_locations":
            return makeQuery($c,"SELECT *
               FROM `track_animals` a
               JOIN (
                  SELECT lg.*
                  FROM `track_locations` lg
                  WHERE lg.id = (
                     SELECT lt.id
                     FROM `track_locations` lt
                     WHERE lt.animal_id = lg.animal_id
                     ORDER BY lt.date_create DESC
                     LIMIT 1
                  )
               ) l
               ON a.id = l.animal_id
               WHERE a.user_id = ?
               ORDER BY l.animal_id, l.date_create DESC
               ",$p);



         /* CREATE */

         case "insert_user":
            $r = makeQuery($c,"SELECT id FROM `track_users` WHERE `username`=? OR `email` = ?",[$p[0],$p[1]]);
            if(count($r['result'])) return ["error"=>"Username or Email already exists"];

            $r = makeQuery($c,"INSERT INTO
               `track_users`
               (`name`, `username`, `email`, `password`, `img`, `date_create`)
               VALUES
               ('New User', ?, ?, md5(?), 'http://via.placeholder.com/400/?text=USER',  NOW())
               ",$p,false);
            $r['id'] = $c->lastInsertId();
            return $r;

         case "insert_animal":
            $r = makeQuery($c,"INSERT INTO
               `track_animals`
               (`user_id`, `name`, `type`, `breed`, `description`, `color`, `img`, `date_create`)
               VALUES
               (?, ?, ?, ?, ?, 'white', 'http://via.placeholder.com/400/?text=ANIMAL', NOW())
               ",$p,false);
            $r['id'] = $c->lastInsertId();
            return $r;

         case "insert_location":
            $r = makeQuery($c,"INSERT INTO
               `track_locations`
               (`animal_id`, `lat`, `lng`, `description`, `photo`, `icon`, `date_create`)
               VALUES
               (?, ?, ?, ?, 'http://via.placeholder.com/400/?text=PHOTO', 'http://via.placeholder.com/400/?text=ICON', NOW())
               ",$p,false);
            $r['id'] = $c->lastInsertId();
            return $r;


         /* UPDATE */
         case "update_user_onboard":
            $r = makeQuery($c,"UPDATE
               `track_users`
               SET
                  `name` = ?,
                  `img` = ?
               WHERE `id` = ?
               ",$p,false);
            return ["result" => "success"];

         case "update_user":
            $r = makeQuery($c,"UPDATE
               `track_users`
               SET
                  `username` = ?,
                  `name` = ?,
                  `email` = ?
               WHERE `id` = ?
               ",$p,false);
            return ["result" => "success"];

         case "update_animal_image":
            $r = makeQuery($c,"UPDATE
               `track_animals`
               SET `img` = ?
               WHERE `id` = ?
               ",$p,false);
            return $r;

         case "update_user_image":
            $r = makeQuery($c,"UPDATE
               `track_users`
               SET `img` = ?
               WHERE `id` = ?
               ",$p,false);
            return $r;

         case "update_user_password":
            $r = makeQuery($c,"UPDATE
               `track_users`
               SET
                  `password` = md5(?)
               WHERE `id` = ?
               ",$p,false);
            return ["result" => "success"];

         case "update_animal":
            $r = makeQuery($c,"UPDATE
               `track_animals`
               SET
                  `name` = ?,
                  `type` = ?,
                  `breed` = ?,
                  `description` = ?
               WHERE `id` = ?
               ",$p,false);
            return ["result" => "success"];

         case "update_location":
            $r = makeQuery($c,"UPDATE
               `track_locations`
               SET
                  `description` = ?
               WHERE `id` = ?
               ",$p,false);
            return ["result" => "success"];

         case "search_animals":
            $p = ["%$p[0]%",$p[1]];
            return makeQuery($c,"SELECT *
               FROM `track_animals`
               WHERE
                  `name` LIKE ? AND
                  `user_id` = ?
               ",$p);

         case "filter_animals":
            return makeQuery($c,"SELECT *
               FROM `track_animals`
               WHERE
                  `$p[0]` = ? AND
                  `user_id` = ?
               ",[$p[1],$p[2]]);

                /* DELETE */
         case "delete_animal":
            $r = makeQuery($c,"DELETE FROM `track_animals` WHERE `id` = ?",$p,false);
            return ["result" => "success"];

         case "delete_location":
            $r = makeQuery($c,"DELETE FROM `track_locations` WHERE `id` = ?",$p,false);
            return ["result" => "success"];



         default: return ["error"=>"No Matched Type"];
      }
   } catch(Exception $e) {
      return ["error"=>"Bad Data"];
   }
}


$data = json_decode(file_get_contents("php://input"));
if(!empty($_FILES)) {
   $r = makeUpload("image","../uploads/");
   die(json_encode($r));
}
die(
   json_encode(
      makeStatement($data),
      JSON_NUMERIC_CHECK
   )
);