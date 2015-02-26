<?php

use ClientLib\Controller\AppController;
use ClientLib\Controller\ControllerInterface;

// Load Controller Filters
use ClientLib\Routing\Filters\Library\QueryFilter;
use ClientLib\Utilities\Record;

class ClientController extends AppController implements ControllerInterface {
	
    protected $model;

    protected $resource;

    protected $parent_resource;

    protected $params;
    
    /**
     * Initialize function for setting default 
     * dynamic content
     *
     * @return void
     */
    protected function init()
    {
        // Set resource vars
        $this->resource = $this->model->getResource();
        $this->parent_resource = head(explode("_", $this->resource));
        $this->params = new Record();

        // Set the default title
    	$title = is_object($this->model) ? ucwords($this->resource) : '';

    	// Setup Default page data requirements
    	$this->data->with([
    		'title' => $title,
    		'title_singular' => str_singular($title),
    		'route_group' => route_group(Route::CurrentRouteName()),
    		'header_add_link' => null
    	]);
    }

    /**
   	 * Sets up a filter interface
   	 *
   	 * @return void
   	 */
   	public function setFilters()
   	{
   		// Check if a user is logged in
   		$this->beforeFilter('auth');
   		// CSRF protection for all post put patch delete
		$this->beforeFilter('csrf', array('on' => array('post', 'put', 'patch', 'delete')));
		// Run defualt filters
		parent::setFilters();
	}

    /**
	 * Runs Before Filter Methods
	 *
	 * @param object $route
	 * @param object $request
	 * @return mixed
	 */
	public function beforeFilters($route, $request)
	{
        // Add any page paremeters to the request
        // for correct active pagination link
        if($page = $route->parameter('page', false)) {
            Request::merge(['page' => (int) $page]);
        }

		switch($this->action) {
			case 'getIndex':
				$this->filter->query = QueryFilter::filter();
				$this->template = 'landing';
				break;
			case 'getCreate':
				$this->template = 'create';
				break;
		}
	}

	/**
	 * Default Index/List View
	 *
	 * @return view
	 */
	public function getIndex()
	{
		$this->setHeaderLinkMaybe(route($this->resource.'.create'));

		if(isset($this->filter->query['q']) && $this->filter->query['q'] !== '') {
		    $this->response = $this->model->search($this->filter->query)
		        ->results();

		}
		else {
		    $this->response = $this->model->getAllWithPrimary(array_merge($this->filter->query, $this->params->toArray()));
		}
	}

	/**
	 * Default page View
	 *
	 * @param int $id
	 * @return view
	 */
	public function getView($id)
	{
		$this->setHeaderLinkMaybe(route($this->resource.'.edit', ['id' => $id]));

        	$this->response = $this->model->findWithEmbeds($id, $this->params->toArray());
	}

	/**
	 * Default Create View
	 *
	 * @return object
	 */
	public function getCreate()
	{
		return View::make(
		    $this->parent_resource."::create",
		    $this->data->toArray()
        	);
	}

	/**
	 * Process the the create view form
	 *
	 * @return view
	 */
	public function postCreate($success = "")
	{
		$item = $this->model->newInstance(Input::all());

		if(!$item->save()) {
            $default = $item->messages()->get('default') ?: '';
			return Redirect::route($this->resource.'.create', Request::all())
				->withInput(Input::all())
				->withErrors($item->messages())
                ->with('error_msg',$default);
		}

        // Create success message if var is empty
        if($success === "") {
            $name = find_input_name($item->data);
            $success = ucwords($name). ' successfully created';
        }

		// Set a relationship if passed via query param
		/*
		if($rid = Request::get('rel_id')) {
            $item->addPrimaryRelationship($rid);
		}
		*/

		// Get the correct view
		if($input = Input::get('save-only')) {
			// Send the proper modal response
			if($input === 'module') {
				return $this->intercoolerModuleResponse('success', $success);
			}
			$redirect = $this->resource.".index";
			$params = [];
		}
		else if(Input::get('save-and-edit')) {
			$redirect = $this->resource.'.edit';
			$params = ['id' => $item->getId()];
		}
		else if(Input::get('save-and-edit-modal'))
		    return Redirect::route($this->resource.'.edit',['id' => $item->getId()] )
		    ->with(['success' => $success]);
		else {
			$redirect = $this->resource.'.view';
			$params = ['id' => $item->getId()];
		}

		return $this->redirect($redirect, $params, [
			'success' => $success
		]);
	}

	/**
	 * Default Edit View
	 *
	 * @param int $id
	 * @return view
	 */
	public function getEdit($id)
	{
		try {

			$this->response = $this->model->findWithPrimary($id, $this->params->toArray());

            if(method_exists ( $this->response , 'connections' )) {
                $this->response->connections = $this->response->connections(1,3);
            }

            $name = find_input_name($this->response->data);

			// Set the title
			$this->data->title = 'Edit '.$this->data->title_singular.': ' . $name;

		}
		catch(Exception $e) {
			return $this->redirect($this->data->route_group.'.index', [], [
				'error' => $this->data->title_singular.' not found.'
			]);
		}
	}

	/**
	 * Default Post Edit
	 *
	 * @return view
	 */
	public function postEdit($id)
	{
		$item = $this->model->newInstance(Input::all() + ['id' => $id]);

		if(!$item->save()) {

            $default = $item->messages()->get('default') ?: '';
		    
		    if(Request::ajax()):
    		    return Redirect::route($this->resource.'.edit',['id' => $id] )
    		    ->withInput(Input::all())
    		    ->withErrors($item->messages())
		        ->with('error_msg',$default);
		    else:
    			return $this->redirect($this->resource.'.edit', ['id' => $id], [
    				'input' => Input::all(),
    				'errors' => head($item->messages())
    			]);
		    endif;
		}

		// Get the correct view
		if($input = Input::get('save-only')) {
		    $redirect = $this->resource.".index";
		    $params = [];
		}else {
		    $redirect = $this->resource.'.view';
		    $params = ['id' => $id];
		}
		
		return $this->redirect($redirect, $params, [
		        'success' => find_input_name($item->data).' successfully updated.'
		        ]);
	}

	/**
	 * Default Delete Method/View
	 *
	 * @return view
	 */
	public function getDelete($id)
	{
		$item = $this->model->findById($id, $this->params->toArray());

		if(!is_array($item->data)) {
			return $this->redirect($this->resource.'.index', ['ic-request' => 'true'], [
				'error' => head($item->messages())
			]);
		}

		$name = find_input_name($item->data);

		$this->data->with([
			'id' => $id,
			'header' => 'Delete '.$this->data->title_singular,
			'body' => 'Are you sure you want to permanently remove '.$name."?"
		]);

		return View::make('partials.modal.delete', $this->data->toArray());
	}

	/**
	 * Default Delete Post Method
	 *
	 * @return view
	 */
	public function postDelete($id)
	{
		$item = $this->model->findById($id, $this->params->toArray());
		
		$name = find_input_name($item->data);
		
		if($item->destroy()) {
			$flash = [
				'success' => $name.' successfully deleted.'
			];
		}
		else {
			$flash = [
				'error' => head($item->messages())
			];
		}
		
		return $this->redirect($this->resource.'.index', [], $flash);
	}
	
	protected function hasHeaderLink()
	{
		if(is_null($this->data->header_add_link)) {
			return false;
		}
		return true;
	}
	
	protected function getHeaderLink()
	{
		return $this->data->header_add_link;
	}
	
	protected function setHeaderLink($link) 
	{
		$this->data->header_add_link = (string) $link;
	}
	
	protected function setHeaderLinkMaybe($link) 
	{
		if(!$this->hasHeaderLink() || $this->getHeaderLink() !== false) {
			$this->setHeaderLink((string) $link);
		}
	}
}
