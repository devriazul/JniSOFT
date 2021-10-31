<?php 
namespace App\Filter;

use HybridLogic\Validation\Validator;
use HybridLogic\Validation\Rule;

/**
* 	class for validating form
*/
class Filter
{
	
	function __construct()
	{
		  if (session_status() == PHP_SESSION_NONE) {
		      session_start();
		  }
	}
	/*
	*	SETTINGS
	*
	*	Settings page account form
	*	@return $validator object
	*/
	public function settings_account()
	{
		$validator = new Validator();

		$validator
			->set_label('user_name', 'User name')
			->add_filter('user_name', 'trim')
			->add_filter('user_name', 'strtolower')
			->add_rule('user_name', new Rule\NotEmpty())
			->add_rule('user_name', new Rule\MinLength(5))
			->add_rule('user_name', new Rule\MaxLength(30));

		return $validator;
	}
	/*
	*	SETTINGS
	*
	*	Settings page change password form
	*	@return $validator object
	*/
	public function settings_password()
	{
		$validator = new Validator();

		$validator
			->set_label('old_password', 'Current password')
			->add_filter('old_password', 'trim')
			->add_filter('old_password', 'strtolower')
			->add_rule('old_password', new Rule\NotEmpty())

			->set_label('new_password', 'New password')
			->add_filter('new_password', 'trim')
			->add_filter('new_password', 'strtolower')
			->add_rule('new_password', new Rule\NotEmpty())
			->add_rule('new_password', new Rule\MinLength(6));

		return $validator;
	}

	/*
	*	USER
	*
	*	Add user form
	*	@return $validator object
	*/
	public function user_create()
	{
		$validator = new Validator();

		$validator
			->set_label('user_name', 'User name')
			->add_filter('user_name', 'trim')
			->add_filter('user_name', 'strtolower')
			->add_rule('user_name', new Rule\NotEmpty())
			->add_rule('user_name', new Rule\MinLength(5))

			->set_label('email', 'Email')
			->add_filter('email', 'trim')
			->add_filter('email', 'strtolower')
			->add_rule('email', new Rule\NotEmpty())
			->add_rule('email', new Rule\MinLength(3))
			->add_rule('email', new Rule\Email())

			->set_label('password', 'Password')
			->add_filter('password', 'trim')
			->add_filter('password', 'strtolower')
			->add_rule('password', new Rule\NotEmpty())
			->add_rule('password', new Rule\MinLength(6))

			->set_label('user_type', 'User type')
			->add_filter('user_type', 'trim')
			->add_filter('user_type', 'strtolower')
			->add_rule('user_type', new Rule\NotEmpty());

		return $validator;
	}

	/*
	*	SESSION
	*
	*	Add user form
	*	@return $validator object
	*/
	public function session_create()
	{
		$validator = new Validator();

		$validator
			->set_label('session_name', 'Session name')
			->add_filter('session_name', 'trim')
			->add_filter('session_name', 'strtolower')
			->add_rule('session_name', new Rule\NotEmpty())
			->add_rule('session_name', new Rule\MinLength(5));

		return $validator;
	}
	/*
	*	MARKS
	*
	*	Add marks form
	*	@return $validator object
	*/
	public function marks_add()
	{
		$validator = new Validator();

		$validator
			->set_label('session', 'Session name')
			->add_filter('session', 'trim')
			->add_rule('session', new Rule\NotEmpty())
			
			->set_label('semester', 'Semester name')
			->add_filter('semester', 'trim')
			->add_rule('semester', new Rule\NotEmpty())

			->set_label('course_id', 'Course ID')
			->add_filter('course_id', 'trim')
			->add_rule('course_id', new Rule\NotEmpty())

			;

		return $validator;
	}
	/*
	*	MARKS
	*
	*	Add marks form
	*	@return $validator object
	*/
	public function marks_search()
	{
		$validator = new Validator();

		$validator
			->set_label('search_roll', 'Roll number')
			->add_filter('search_roll', 'trim')
			->add_rule('search_roll', new Rule\NotEmpty())
			;

		return $validator;
	}
	/*
	*	Course
	*
	*	Add marks form
	*	@return $validator object
	*/
	public function course_add()
	{
		$validator = new Validator();

		$validator
			->set_label('course_name', 'Course name')
			->add_filter('course_name', 'trim')
			->add_rule('course_name', new Rule\NotEmpty())

			->set_label('course_code', 'Course code')
			->add_filter('course_code', 'trim')
			->add_rule('course_code', new Rule\NotEmpty())

			->set_label('credit', 'Credit')
			->add_filter('credit', 'trim')
			->add_rule('credit', new Rule\Number())
			->add_rule('credit', new Rule\NumMin(0))

			->set_label('session', 'session')
			->add_filter('session', 'trim')
			->add_rule('session', new Rule\NotEmpty())

			->set_label('semester', 'Semester')
			->add_filter('semester', 'trim')
			->add_rule('semester', new Rule\NotEmpty())
			;

		return $validator;
	}
	/*
	*	Course
	*
	*	Course edit form
	*	@return $validator object
	*/
	public function course_edit()
	{
		$validator = new Validator();

		$validator

			->set_label('course_code', 'Course code')
			->add_filter('course_code', 'trim')
			->add_rule('course_code', new Rule\NotEmpty())

			->set_label('credit', 'Credit')
			->add_filter('credit', 'trim')
			->add_rule('credit', new Rule\Number())
			->add_rule('credit', new Rule\NumMin(0))

			->set_label('session', 'session')
			->add_filter('session', 'trim')
			->add_rule('session', new Rule\NotEmpty())

			->set_label('semester', 'Semester')
			->add_filter('semester', 'trim')
			->add_rule('semester', new Rule\NotEmpty())
			;

		return $validator;
	}

	/*
	*	Student 
	*
	*	student edit form
	*	@return $validator object
	*/
	public function student_basic()
	{
		$validator = new Validator();

		$validator
			->set_label('student_name', 'Student name')
			->add_filter('student_name', 'trim')
			->add_rule('student_name', new Rule\NotEmpty())
			->add_rule('student_name', new Rule\MinLength(5))

			->set_label('roll', 'Course code')
			->add_filter('roll', 'trim')
			->add_rule('roll', new Rule\NotEmpty())
			->add_rule('roll', new Rule\Number())
			->add_rule('roll', new Rule\NumMin(1))

			->set_label('session', 'session')
			->add_filter('session', 'trim')
			->add_rule('session', new Rule\NotEmpty())

			->set_label('semester', 'Semester')
			->add_filter('semester', 'trim')
			->add_rule('semester', new Rule\NotEmpty())
			
			;

		return $validator;
	}
	/*
	*	Student 
	*
	*	student edit form
	*	@return $validator object
	*/
	public function student_basic_update()
	{
		$validator = new Validator();

		$validator
			->set_label('student_name', 'Student name')
			->add_filter('student_name', 'trim')
			->add_rule('student_name', new Rule\NotEmpty())
			->add_rule('student_name', new Rule\MinLength(5))

			->set_label('session', 'session')
			->add_filter('session', 'trim')
			->add_rule('session', new Rule\NotEmpty())

			->set_label('semester', 'Semester')
			->add_filter('semester', 'trim')
			->add_rule('semester', new Rule\NotEmpty())
			;

		return $validator;
	}
	/*
	*	Student 
	*
	*	student edit form
	*	@return $validator object
	*/
	public function student_add_profile()
	{
		$validator = new Validator();

		$validator
			->set_label('father_name', 'Father\'s name')
			->add_filter('father_name', 'trim')
			->add_rule('father_name', new Rule\MinLength(5))

			->set_label('mother_name', 'Mother\'s name')
			->add_filter('mother_name', 'trim')
			->add_rule('mother_name', new Rule\MinLength(5))

			->set_label('guardian_name', 'Guardian Name')
			->add_filter('guardian_name', 'trim')
			->add_rule('guardian_name', new Rule\MinLength(5))

			->set_label('relation_to_guardian', 'Relation to Guardian')
			->add_filter('relation_to_guardian', 'trim')
			->add_rule('relation_to_guardian', new Rule\MinLength(1))

			->set_label('nid', 'National ID')
			->add_filter('nid', 'trim')
			->add_rule('nid', new Rule\AlphaNumeric())

			->set_label('contact_number', 'Contact number')
			->add_filter('contact_number', 'trim')
			->add_rule('contact_number', new Rule\AlphaNumeric())

			->set_label('nationality', 'Nationality')
			->add_filter('nationality', 'trim')
			->add_rule('nationality', new Rule\MinLength(3))

			->set_label('marital_status', 'Marital status')
			->add_filter('marital_status', 'trim')
			->add_rule('marital_status', new Rule\NumNatural())

			->set_label('gender', 'Gender')
			->add_filter('gender', 'trim')
			->add_rule('gender', new Rule\NotEmpty())
			->add_rule('gender', new Rule\MinLength(1))

			->set_label('religion', 'Religion')
			->add_filter('religion', 'trim')
			->add_rule('religion', new Rule\MinLength(3))

			->set_label('service_type', 'Service type')
			->add_filter('service_type', 'trim')
			->add_rule('service_type', new Rule\MinLength(1))

			->set_label('permanent_address', 'Permanent  address')
			->add_filter('permanent_address', 'trim')
			->add_rule('permanent_address', new Rule\MinLength(5))

			->set_label('current_address', 'Current address')
			->add_filter('current_address', 'trim')
			->add_rule('current_address', new Rule\MinLength(5))

			->set_label('school_name', 'School name')
			->add_filter('school_name', 'trim')
			->add_rule('school_name', new Rule\MinLength(5))

			->set_label('college_name', 'College name')
			->add_filter('college_name', 'trim')
			->add_rule('college_name', new Rule\MinLength(5))

			->set_label('ssc_gpa', 'SSC GPA')
			->add_filter('ssc_gpa', 'trim')
			->add_rule('ssc_gpa', new Rule\Number())

			->set_label('hsc_gpa', 'HSC GPA')
			->add_filter('hsc_gpa', 'trim')
			->add_rule('hsc_gpa', new Rule\Number())

			->set_label('ssc_passing_year', 'SSC passing year')
			->add_filter('ssc_passing_year', 'trim')
			->add_rule('ssc_passing_year', new Rule\NumNatural())

			->set_label('hsc_passing_year', 'HSC passing year')
			->add_filter('hsc_passing_year', 'trim')
			->add_rule('hsc_passing_year', new Rule\NumNatural())

			->set_label('original_ssc_doc', 'SSC doc list')
			->add_filter('original_ssc_doc', 'trim')
			->add_rule('original_ssc_doc', new Rule\MinLength(1))

			->set_label('original_hsc_doc', 'HSSC doc list')
			->add_filter('original_hsc_doc', 'trim')
			->add_rule('original_hsc_doc', new Rule\MinLength(1))
			;

		return $validator;
	}
}


?>