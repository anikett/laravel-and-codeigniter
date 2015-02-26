<?php

class ConnectionsController extends ClientController {

    protected $title = "Connections";

	public function getIndex()
	{
		// Set the header link
		$this->data['header_add_link'] = route('people.create');
		
		// Add includes to query
		$this->filter->query['include'] = ['people', 'organizations'];
		
		// Get the api response
		$this->response = Api::getConnections($this->filter->query);
		
        parent::getIndex();
	}

	public function getCreate()
	{

		parent::getCreate();
	}
}
