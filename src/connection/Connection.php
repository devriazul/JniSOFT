<?php
	namespace App\Connection;
	/**
	* 	This soft is design for Jamalpur Nursing Institute
	*	Database Configuration
	* 	Set database information 
	*/
	class Connection
	{
		public $host;
		public $db_name;
		public $user;
		public $password;

		function __construct()
		{
			$this->host 	= 'localhost';
			$this->db_name 	= 'saicgrou_jnisoft';
			$this->user 	= 'root';
			$this->password = '';
		}
	}
?>