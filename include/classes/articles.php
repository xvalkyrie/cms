<?php

/*

 * Articles Class

 * 
 
 * Handles the nitty-gritty of storing articles in the database, as well as 

 * retrieving articles from the database. 
 
 * Other CMS scripts can easily create, update, retrieve and delete articles using
 
 * this class file.
 
 *

*/


class Article
{

	// Properties
	
	/**
	* @var int the article ID from the database
	*/
	public $id = null;

	/**
	* @var int when the article was published
	*/
	public $post_date = null;
	
	/**
	* @var string full title of the article
	*/
	public $post_title = null;

	/**
	* @var string a short summary of the article
	*/
	public $post_excerpt = null;

	/**
	* @var string the HTML content of the article
	*/
	public $post_content = null;


	/**
	* Sets the object's properties using the values in the supplied array
	*
	* @param assoc The property values
	*/	
	
	public function __construct( $data=array() ) {
		if ( isset( $data['id'] ) ) 
			$this->id = (int) $data['id'];
		if ( isset( $data['post_date'] ) ) 
			$this->post_date = (int) $data['post_date'];
		if ( isset( $data['post_title'] ) ) 
			$this->post_title = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['post_title'] );
		if ( isset( $data['post_excerpt'] ) ) 
			$this->post_excerpt = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['post_excerpt'] );
		if ( isset( $data['post_content'] ) ) 
			$this->post_content = $data['post_content'];
	}
	
	/**
	* Sets the object's properties using the edit form post values in the supplied array
	*
	* @param assoc The form post values
	*/

	public function storeFormValues ( $params ) {

		// Store all the parameters
		$this->__construct( $params );

		// Parse and store the publication date
		if ( isset($params['post_date']) ) {
			$post_date = explode ( '-', $params['post_date'] );
			if ( count($post_date) == 3 ) {
				list ( $y, $m, $d ) = $post_date;
				$this->post_date = mktime ( 0, 0, 0, $m, $d, $y );
			}
		}
	}

	/**
	* Returns an Article object matching the given article ID
	*
	* @param int The article ID
	* @return Article|false The article object, or false if the record was not found or there was a problem
	*/

	public static function getById( $id ) {
		
		$conn = new PDO( DB_HOST, DB_USER, DB_PASSWORD );
		$sql = "SELECT *, UNIX_TIMESTAMP(post_date) AS post_date FROM articles WHERE id = :id";
		$st = $conn->prepare( $sql );
		$st->bindValue( ":id", $id, PDO::PARAM_INT );
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		
		// Checks for data and create new Article object with that data
		if ( $row )
			return new Article( $row );
	}

	/**
	* Returns all (or a range of) Article objects in the DB
	*
	* @return Array|false A two-element array : results => array, a list of Article objects; totalRows => Total number of articles
	* 
	* 
	* $numRows sets the maximum number of articles to retrieve. We want them all so default is a very high number, 1,000,000.
	*
	* $order sets the sort order to display the articles. Default is sort by publication date, newest first ("post_date DESC").
	*
	*/
	
	public static function getList( $numRows=1000000, $order="post_date DESC" ) { 
		
		$conn = new PDO( DB_HOST, DB_USER, DB_PASSWORD );
		$sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(post_date) AS post_date FROM articles
				ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";
		$st = $conn->prepare( $sql );
		$st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
		$st->execute();
		
		// Create an array to hold the articles
		$list = array();
		
		// Fill the array with articles
		while ( $row = $st->fetch() ) {
			$article = new Article( $row );
			$list[] = $article;
		}
	
		// Now get the total number of articles that matched the criteria
		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query( $sql )->fetch();
		$conn = null;
		return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
	}
	
	/**
	* Inserts the current Article object into the database, and sets its ID property
	* 
	*/

	public function insert() {

		// Does the Article object already have an ID?
		if ( !is_null( $this->id ) )
			trigger_error ( "Article::insert(): Attempt to insert an Article object that already has its ID property set (to $this->id).", E_USER_ERROR );

		// Insert the Article
		$conn = new PDO( DB_HOST, DB_USER, DB_PASSWORD );
		$sql = "INSERT INTO articles ( post_date, post_title, post_excerpt, post_content ) VALUES ( FROM_UNIXTIME(:post_date), :post_title, :post_excerpt, :post_content )";
		$st = $conn->prepare ( $sql );
		$st->bindValue( ":post_date", $this->post_date, PDO::PARAM_INT );
		$st->bindValue( ":post_title", $this->post_title, PDO::PARAM_STR );
		$st->bindValue( ":post_excerpt", $this->post_excerpt, PDO::PARAM_STR );
		$st->bindValue( ":post_content", $this->post_content, PDO::PARAM_STR );
		$st->execute();
		$this->id = $conn->lastInsertId();
		$conn = null;
	}


	/**
	* Updates the current Article object in the database
	* 
	*/

	public function update() {

		// Does the Article object have an ID yet?
		if ( is_null( $this->id ) )
			trigger_error ( "Article::update(): Attempt to update an Article object that does not have its ID property set.", E_USER_ERROR );
   
		// Update the Article
		$conn = new PDO( DB_HOST, DB_USER, DB_PASSWORD );
		$sql = "UPDATE articles SET post_date=FROM_UNIXTIME(:post_date), post_title=:post_title, post_excerpt=:post_excerpt, post_content=:post_content WHERE id = :id";
		$st = $conn->prepare ( $sql );
		$st->bindValue( ":post_date", $this->post_date, PDO::PARAM_INT );
		$st->bindValue( ":post_title", $this->post_title, PDO::PARAM_STR );
		$st->bindValue( ":post_excerpt", $this->post_excerpt, PDO::PARAM_STR );
		$st->bindValue( ":post_content", $this->post_content, PDO::PARAM_STR );
		$st->bindValue( ":id", $this->id, PDO::PARAM_INT );
		$st->execute();
		$conn = null;
	}


	/**
	* Deletes the current Article object from the database
	* 
	* @param LIMIT 1 ensures only one article can be deleted at once. 
	* 
	*/

	public function delete() {

		// Does the Article object have an ID yet?
		if ( is_null( $this->id ) )
			trigger_error ( "Article::delete(): Attempt to delete an Article object that does not have its ID property set.", E_USER_ERROR );

		// Delete the Article
		$conn = new PDO( DB_HOST, DB_USER, DB_PASSWORD );
		$st = $conn->prepare ( "DELETE FROM articles WHERE id = :id LIMIT 1" );
		$st->bindValue( ":id", $this->id, PDO::PARAM_INT );
		$st->execute();
		$conn = null;
	}

}
	
	
?>
