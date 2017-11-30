<?php

class ClassVideos 
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getVideos() {
        $sql = "SELECT *
            from videos t";
            
        $stmt = $this->db->query($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    public function getTopTen() {
        $sql = "SELECT *
            from videos ORDER BY votes DESC LIMIT 0, 10";
            
        $stmt = $this->db->query($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;
    }
    
     public function getVideoById($id) {
        $sql = "SELECT *
            from videos WHERE id = $id";
            
        $stmt = $this->db->query($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

	/*    public function AddNewVideo($title, $link) {
        //$sql = "INSERT INTO videos (title,link,votes) VALUES('$title','$link',0)";

        $sql = "insert into videos
            (title, link, votes) values
            (:title, :link, :votes )";
		

		function get_youtube_id_from_url($url)
			{
			if (stristr($url,'youtu.be/'))
				{preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $final_ID); return $final_ID[4]; }
			else 
				{@preg_match('/(https:|http:|):(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $IDD); return $IDD[5]; }
			}


			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute([
            "title" => $title,
            "link" => get_youtube_id_from_url($link),
            "votes" => 0,
        ]);

			if(!$result) {
				return 'Error! No database connection.';

			}
			return 'New Youtube Video has been added!';
		
    }*/

    public function VotesVideo($id, $votes) {
        //$sql = "UPDATE videos SET votes = $votes WHERE id = $id";
        $sql = "update videos
            set votes = :votes WHERE id = :id";

        $stmt = $this->db->prepare($sql);    
        $result = $stmt->execute([
            "votes" => $votes,
            "id" => $id           
        ]);

        if(!$result) {
            return 'Error! No database connection.';

        }
        return 'You voted for video!';

    }
    
}
