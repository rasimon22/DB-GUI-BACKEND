<?php
use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';
//require __DIR__ . '/../classes/ClassVideos.php';
//require __DIR__ . '/../classes/ClassUsers.php';
//require __DIR__ . '/../classes/ClassUsersLikeVideos.php';

$app->get('/listusers', function (Request $request, Response $response, array $args) {
	$stmt = $this->db->prepare('SELECT user_id, username, password
				    FROM users');
	$stmt->execute();
	$row = $stmt->fetchAll();
	#$row['successfully'] = true;
	
	return $response->withJson($row);
});
$app->get('/login', function (Request $request, Response $response, array $args) {
	$stuff = array("successfully" => true);
    return $response->withJSON($stuff);
});


$app->post('/login', function (Request $request, Response $response, array $args) {
    $json = $request->getBody();   
    $mydata = json_decode($json,true);    
    $username = $mydata["username"];
    $pass = $mydata["password"];
    $pass = md5($pass);
    //$pass = m$pass);	
    $stmt = $this->db->prepare("SELECT user_id
            from users WHERE username =:user AND password =:pass");
	$stmt->bindParam('user', $username);
	$stmt->bindParam('pass', $pass);
	$stmt->execute();
        $row = $stmt->fetchAll();
	if ($row)
	{
		$getid = $this->db->prepare('SELECT user_id FROM users WHERE username=:user');
		$getid->bindParam('user', $username);
		$getid->execute();
		$check = $getid->fetch(PDO::FETCH_ASSOC);
		$uid = $check['user_id'];
		$jsondata = json_encode(array('user_id' => $uid));
		
		$TOKEN = encodeJWT($jsondata);
		$return = array("Token Created" => true, "TOKEN" => $TOKEN, "user_id" => $uid);
		return $response->withJson($return);
	}  
	else{
	$return = array("Token Created" => false);
    return $response->withJson($return)->withStatus(401);
}
});

$app->get('/addvideos', function (Request $request, Response $response) {
        if(session_id() == ''){session_start();}
        if(!isset($_SESSION['username']) )
        {
                return $response->withRedirect('/login');
        }
     $stuff = array("successfully" => true);
    return $request->withJSON($stuff);
});
$app->post('/playlist/{id}/addvideos', function (Request $request, Response $response, array $args){
        $data = $request->getBody();
	$playlist_id = (int)$args['id'];
        $data = json_decode($data,true);
        $title = $data["title"];
        $link = $data["url"];
	$user_id = $data["user_id"];
        //$classvideoss = new ClassVideos($this->db);
        //$addvideo =  $classvideoss->AddNewVideo($title, $link);
	$sql = "SELECT count(*) FROM  library WHERE url = '$link';";
	$stmt = $this->db->query($sql);
        $results = $stmt->fetch();
        if($results['count(*)'] == 0){
		$sql = "INSERT INTO library (url) VALUES ('$link');";
		$stmt = $this->db->query($sql);
	}
	$sql = "SELECT song_id FROM library WHERE url = '$link';";
	$stmt = $this->db->query($sql);
	$results = $stmt->fetch();
	$song_id = $results['song_id'];
	$sql = "INSERT INTO active(user_id, song_id,playlist_id,likes) VALUES ('$user_id','$song_id','$playlist_id',0);";
        $stmt = $this->db->query($sql);
	$sql = "SELECT active_id FROM active WHERE user_id = '$user_id' AND song_id = '$song_id' AND playlist_id = '$playlist_id';";
	$stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $active_id = $row['active_id'];
	$JSON = array("title" => $title, "url" => $link, "active_id" => $active_id, "successfully" => true);
        $response =  $response->withJSON($JSON);
       // $response =  $response->withRedirect("/");
        return $response;
});
$app->get('/active/{id}', function ( Request $request, Response $response, array $args) {
        $active_id = $args['id'];
        $sql = "  SELECT library.url FROM library INNER JOIN
                active ON library.song_id = active.song_id
                 WHERE active_id =:acid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('acid', $active_id);      
	$stmt->execute();
        $row = $stmt->fetchAll();
	$array = array("url" => $row, "successfully" => true);
        $response = $response->withJson($array);
        return $response;
});

$app->post('/active/{id}/like',  function ( Request $request, Response $response, array $args) {
        $json = $request->getBody();
        $mydata = json_decode($json,true);
        $active_id = (int)$args['id'];
        $user_id = $mydata["user_id"];
        $sql = "SELECT like_id FROM user_likes WHERE user_id = '$user_id' 
                AND active_id = $active_id;";
        $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
                $results[] = $row;
        }
        //check if the user already liked the video
        if(count($results)> 0 ? true : false){
        //$response = $response->withRedirect("/active/");
        //might need to return some stuff JSON later
       		$stuff = array("successfully" => true);
	    	return $response->withJSON($stuff);
        }
        $sql = "UPDATE active SET likes = likes + 1
                    WHERE active_id = '$active_id';";
        $this->db->query($sql);
        $sql = "INSERT INTO user_likes ( user_id, active_id) VALUES 
                ('$user_id','$active_id');";
        $this->db->query($sql);
        //$response = $response->withRedirect("/active/{id}");
        //might need to return some stuff JSON later*/
         $stuff = array("successfully" => true);
    return $response->withJSON($stuff);
});

$app->post('/active/{id}/dislike',  function ( Request $request, Response $response, array $args) {
        $json = $request->getBody();
        $mydata = json_decode($json,true);
        $active_id = (int)$args['id'];
        $user_id = $mydata["user_id"];

        $sql = "SELECT like_id FROM user_likes WHERE user_id = '$user_id' 
                AND active_id = $active_id;";
        $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
                $results[] = $row;
        }
        //check if the user already liked the video
        if(count($results)> 0 ? true : false){
        //$response = $response->withRedirect("/active/");
        //might need to return some stuff JSON later
      		 $stuff = array("successfully" => true);
    		 return $response->withJSON($stuff);
        }
        $sql = "UPDATE active SET likes = likes - 1
                 WHERE active_id = '$active_id';";
        $this->db->query($sql);
        $sql = "INSERT INTO user_likes ( user_id, active_id) VALUES 
                ('$user_id','$active_id');";
        $this->db->query($sql);
        //$response = $response->withRedirect("/active/{id}");
        //might need to return some stuff JSON later
         $stuff = array("successfully" => true);
    return $response->withJSON($stuff);
});     


$app->get('/user/{id}', function( Request $request, Response $response, array $args){
        $user_id = (int) $args['id'];
        $sql = "SELECT username,fName,lName,email FROM users WHERE user_id = '$user_id'";
         $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
                $results[] = $row;
        }
        $json = array("user_info" => $results, "successfully" => true);
        $response = $response->withJSON($json);
        return $response;
});

$app->post('/user/{id}', function( Request $request, Response $response, array $args){
        $json = $request->getBody();
        $mydata = json_decode($json,true);
        $user_id = (int) $args['id'];
        $username = $mydata["username"];
        $fName = $mydata["fName"];
        $lName = $mydata["lName"];
        $email = $mydata["email"];
        $sql = "UPDATE users 
                SET username = '$username', fName = '$fName', lName = '$lName', email = '$email'
                WHERE user_id = '$user_id';";
        $this->db->query($sql);
	$array = array("successfully" => true);
        $response = $response->withJSON($array);
});

//copied from stackoverflow: https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
//by Neal:  https://stackoverflow.com/users/561731/neal
function randomCode() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
 function getNewCode($dbObj){
        $access_code = randomCode();
        $sql = "SELECT count(*) FROM access WHERE access_code = '$access_code';";
        $stmt = $dbObj->db->query($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        if(count($results)> 0 ? true : false){
        getNewCode();
        }
        return $access_code;
}

$app->post('/playlist', function( Request $request, Response $response, array $args){
	$json = $request->getBody();
	$mydata = json_decode($json,true);
	$user_id = $mydata["user_id"];
	$title = $mydata["title"];
	$public = $mydata["public"];
 	$test = true;
	$access_code;
	while($test){
		$access_code = randomCode();
	 	$sql = "SELECT count(*) FROM access WHERE access_code = '$access_code';";
        	$stmt = $this->db->query($sql);
        	$results = $stmt->fetch();
        	if($results['count(*)'] == 0){
			$test = false;
        	}
	}
	$sql = "INSERT INTO playlists (title,user_id,access_code,isPublic) 
		VALUES ('$title', '$user_id', '$access_code', '$public');";
	$this->db->query($sql);
	$sql = "INSERT INTO access (user_id, access_code) VALUES ('$user_id','$access_code');";
	$this->db->query($sql);
	$sql = "SELECT playlist_id FROM playlists WHERE user_id = '$user_id' AND title = '$title' AND access_code = '$access_code';";
	$stmt = $this->db->prepare($sql);
	$stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
	$plid = $row['playlist_id'];
	$data = array('user_id' => $user_id, 'title' => $title, 'public' => $public, 'access_code' => $access_code, "playlist_id" => $plid, "succesfully" => true);
	$response = $response->withJSON($data);
	//$response = $response->withRedirect('/playlist/' + $row);
	return $response;      
});







