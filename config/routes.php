<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
	'/'             => 'User#logInUser',
	'/login'        => 'User#logInUser',
	'/signup'       => 'User#signUpUser',
	'/tasks'        => 'Task#index',
	'/create'       => 'Task#create',
	'/update'       => 'Task#update',
	'/delete'       => 'Task#delete',
);
