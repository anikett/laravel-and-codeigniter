<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customer_contacts extends CI_Controller {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->model('customer_contacts_m');
        $this->load->helper('customer');
    }
    
    
    /*
     * index function is for showing listing all the customer contacts of the company
     */

    function index() {
        if (($this->session->userdata('user_logged_in')) && (($this->session->userdata('is_manager')) || ($this->session->userdata('is_company')))) {
            $where = array(
                "company_user_id" => $this->session->userdata('company_user_id'),
                "is_deleted" => 0
            );
            $data['all_contacts'] = $this->customer_contacts_m->all_customer_contacts($where);
            layout("contacts/all_customer_contacts", $data);
        } else {
            error_message("You have not permission to access this page", true);
        }
    }
    
    /*
     * To add a new customer contact
     */
    
    function addCustomerContact() {
        if (($this->session->userdata('user_logged_in')) && ($this->session->userdata('is_manager') || $this->session->userdata('is_company'))) {
            if ($this->input->post()) {
                $this->form_validation->set_rules('name', 'Name', 'required');
                $this->form_validation->set_rules('email', 'Email', 'email');
                $this->form_validation->set_rules('country', 'Country', '');
                $this->form_validation->set_rules('city', 'City', 'required');
                $this->form_validation->set_rules('state', 'State', '');
                $this->form_validation->set_rules('post_code', 'Pin Code', '');
                $this->form_validation->set_rules('street', 'Street', 'required');

                if ($this->form_validation->run() == FALSE) {
                    echo validation_errors();
                    die();
                } else {
                    //echo "<pre>"; print_r($this->input->post()); echo "</pre>";die();
                    $client_data = $this->input->post();
                    $billing = array();
                    if ($this->input->post('billing_same') == 'yes') {
                        $billing['Billing']['name'] = $this->input->post('name');
                        $billing['Billing']['email'] = $this->input->post('email');
                        $billing['Billing']['phone'] = $this->input->post('phone');
                        $billing['Billing']['street'] = $this->input->post('street');
                        $billing['Billing']['state'] = $this->input->post('state');
                        $billing['Billing']['post_code'] = $this->input->post('post_code');
                        $billing['Billing']['city'] = $this->input->post('city');
                        $billing['Billing']['state'] = $this->input->post('state');
                        $billing['Billing']['country'] = $this->input->post('country');
                        $billing['Billing']['notes'] = $this->input->post('notes');

                        $added_client_site_data = array(
                            'added_by' => $this->session->userdata('user_id'),
                            'company_user_id' => $this->session->userdata('company_user_id'),
                            'created_date' => date('Y-m-d'),
                            'billing_same' => 'yes',
                            'billing_address' => json_encode($billing),
                            'status' => 1,
                        );
                    } else {
                        $billing['Billing'] = $this->input->post('billing');
                        $added_client_site_data = array(
                            'added_by' => $this->session->userdata('user_id'),
                            'company_user_id' => $this->session->userdata('company_user_id'),
                            'created_date' => date('Y-m-d'),
                            'status' => 1,
                            'billing_same' => 'no',
                            'billing_address' => json_encode($billing),
                        );
                    }
                    unset($client_data['billing'], $client_data['billing_same']);
                    $client_data = array_merge((array) $client_data, $added_client_site_data);
                    //echo "<pre>"; print_r($client_data); echo "</pre>";die();
                    $client_id = $this->customer_contacts_m->add_customer_contact($client_data);
                    if ($client_id) {
                        echo "success";
                    } else
                        echo "Error";
                    die();
                }
            }
        } else
            error_message("You have not permission to access this page", true);
    }
    
    /*
     * To refresh the listing of customer contcts if any chnages happened dynamically
     */

    public function refresh() {
        if (($this->session->userdata('user_logged_in')) && ($this->session->userdata('is_manager') || $this->session->userdata('is_company'))) {
            $where = array(
                "company_user_id" => $this->session->userdata('company_user_id'),
                "is_deleted" => 0
            );
            $all_contacts = $this->customer_contacts_m->all_customer_contacts($where);
            if ($all_contacts) {
                $table = "";
                $l = count($all_contacts);
                $table .= '<table class="table table-striped table-hover" id="customer_contact_list_table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Street No.Name/POBOX:</th>
                                                <th>Suburb</th>
                                                <th>State</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                foreach ($all_contacts as $contacts) {
                    
                    $table .= '<tr>';
                    $table .= '<td>' . $l . '</td>';
                    $table .= '<td>' . $contacts->name . '</td>';
                    $table .= '<td>' . $contacts->phone . '</td>';
                    $table .= '<td><a href="mailto:' . $contacts->email . '">' . $contacts->email . '</a></td>';
                    $table .= '<td>' . $contacts->street . '</td>                 
                               <td>' . $contacts->city . '</td>
                               <td>' . $contacts->state . '</td>';
                    $table .= '<td class="action_buttons">
                                    <div class="visible-md visible-lg hidden-sm hidden-xs"> 
                                         <a id="' . base64_encode($contacts->contact_id) . '" data-original-title="View/Edit" data-placement="top" class="edit_customer_contact btn btn-xs btn-orange tooltips" href="#"><i class="fa fa-edit fa-white fa-lg"></i></a> ';
                    if ($contacts->status == '1') {
                        $table .= '<a id="' . base64_encode($contacts->contact_id) . '" data-original-title="Deactivate" data-placement="top" class="deactivate_customer_contact btn btn-xs btn-light-green tooltips" href="#"><i class="fa-check-circle-o fa fa-white fa-lg"></i></a> ';
                    } else {
                        $table .= '<a id="' . base64_encode($contacts->contact_id) . '" data-original-title="Activate" data-placement="top" class="activate_customer_contact btn btn-xs btn-red tooltips" href="#"><i class="fa-ban fa fa-white fa-lg"></i></a> ';
                    }
                    if ($this->session->userdata('is_company')) {
                        $table .='<a id="' . base64_encode($contacts->contact_id) . '" data-original-title="Delete" data-placement="top" class="delete_customer_contact btn btn-xs btn-red tooltips" href="#" tabindex="-1" role="menuitem" >
                    <i class="fa fa-times fa fa-white fa-lg"></i> 
                    </a> ';
                    }
                    $table .= ' </div>';
                    $table .= '<div class="visible-xs visible-sm hidden-md hidden-lg">
                                    <div class="btn-group">
                                        <a href="#" data-toggle="dropdown" class="btn btn-green dropdown-toggle btn-sm">
                                            <i class="fa fa-cog"></i> <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu pull-right dropdown-dark" role="menu">
                                            <li>
                                                <a id="' . base64_encode($contacts->contact_id) . '" data-toggle="dropdown" class="edit_customer_contact btn btn-green dropdown-toggle btn-sm">
                                                <i class="fa fa-edit"></i> View/Edit
                                                </a>
                                            </li>';
                    $table .= '             <li>';
                    if ($contacts->status == '1') {
                        $table .= '             <a id="' . base64_encode($contacts->contact_id) . '" tabindex="-1" role="menuitem" class="deactivate_customer_contact btn btn-green dropdown-toggle btn-sm" href="#"><i class="fa-check-circle-o fa fa-white fa-lg"></i> Deactivate</a>';
                    } else {
                        $table .= '             <a id="' . base64_encode($contacts->contact_id) . '" tabindex="-1" role="menuitem" class="activate_customer_contact btn btn-green dropdown-toggle btn-sm" href="#"><i class="fa-ban fa fa-white fa-lg"></i> Activate</a>';
                    }
                    $table .= '             </li>';
                    if ($this->session->userdata('is_company')) {
                        $table .= '         <li>
                                                <a id="' . base64_encode($contacts->contact_id) . '" href="#" tabindex="-1" role="menuitem"  class="delete_customer_contact btn btn-green dropdown-toggle btn-sm">
                                                    <i class="fa fa-times"></i> Delete
                                                </a>
                                            </li>';
                    }
                    $table .= '     </ul>
                                    </div>
                                </div>';
                    $table .='</td>';
                    $table .='</tr>';
                    $l--;
                }
                $table .= '</tbody> </table>';
                
                echo $table;
            } else
                echo "No records found";
        } else
            error_message("You have not permission to access this page", true);
    }
    
    /*
     * To deactivate a customer contact
     * paramtere needed- contact_id, passed through post and ajax
     *       
     */

    function deactivate() {
        if (($this->session->userdata('user_logged_in')) && ($this->session->userdata('is_company') || $this->session->userdata('is_manager'))) {
            $contact_data = array(
                "status" => 0,
            );
            $where = array(
                "contact_id" => base64_decode($this->input->post('hash_code'))
            );
            $result = $this->customer_contacts_m->update_customer_contact($contact_data, $where);

            if ($result) {
                echo "success";
            } else {
                echo "Please Try Again";
            }
        } else {
            echo "You have no permission to deactivate a supplier.";
        }
    }
    
    /*
     * To Activate a customer contact
     * paramtere needed- contact_id, passed through post and ajax
     *       
     */

    function activate() {
        if (($this->session->userdata('user_logged_in')) && ($this->session->userdata('is_company') || $this->session->userdata('is_manager'))) {
            $contact_data = array(
                "status" => 1,
            );

            $where = array(
                "contact_id" => base64_decode($this->input->post('hash_code'))
            );
            $result = $this->customer_contacts_m->update_customer_contact($contact_data, $where);

            if ($result) {
                echo "success";
            } else {
                echo "Please Try Again";
            }
        } else {
            echo "You have not permission to do the action.";
        }
    }
    
    /*
     * To Delete a customer contact
     * Paramtere needed- contact_id, passed through post and ajax
     *       
     */

    function delete() {
        if (($this->session->userdata('user_logged_in')) && ($this->session->userdata('is_company') || $this->session->userdata('is_manager'))) {
            $contact_data = array(
                'status' => 0,
                'is_deleted' => 1
            );

            $where = array(
                "contact_id" => base64_decode($this->input->post('hash_code'))
            );
            $result = $this->customer_contacts_m->update_customer_contact($contact_data, $where);

            if ($result) {
                echo "success";
            } else {
                echo "Please Try Again";
            }
        } else {
            echo "You have not permission to do the action.";
        }
    }
    
    /*
     * To get the exisitng data of a customer for edit
     * Paramtere needed- contact_id, passed through post and ajax
     *       
     */

    function edit_data() {
        if (($this->session->userdata('user_logged_in')) && (($this->session->userdata('is_manager')) || ($this->session->userdata('is_company')))) {
            $contact_id = base64_decode($this->input->post('hash_code'));
            $contact_details = get_customer_contact_details($contact_id);
            $billing_address = json_decode($contact_details->billing_address)->Billing;

            if ($contact_details->billing_same == 'yes') {
                $billing_address_new = array();
                foreach ($billing_address as $key => $value) {
                    $billing_address_new[$key] = null;
                }
                $billing_address = json_decode(json_encode($billing_address_new), FALSE);
            }
            $data = '';
            $data .='<div class="row">
                        <div class="col-md-12">
                            <div class="errorHandler alert alert-danger no-display" id="ajaxerror">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Name:<span class="symbol required"></span></label>
                                    <input type="text" class="form-control" name="name" value="' . $contact_details->name . '">
                                    <input type="hidden" class="form-control" name="hash_code" value="' . base64_encode($contact_details->contact_id) . '">
                                </div>  
                                <div class="form-group">                            
                                    <label class="control-label">Email:</label>
                                    <input type="email" class="form-control" name="email" value="' . $contact_details->email . '">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Phone:<span class="symbol required"></span></label>
                                    <input type="text" class="form-control" name="phone" value="' . $contact_details->phone . '">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Street No.Name/POBOX:<span class="symbol required"></span></label>
                                    <input type="text" class="form-control" name="street" value="' . $contact_details->street . '">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Suburb:<span class="symbol required"></span></label>
                                    <input type="text" class="form-control" name="city" value="' . $contact_details->city . '">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">                            
                                    <label class="control-label">State:</label>
                                    <input type="text" class="form-control" name="state" value="' . $contact_details->state . '">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Country:</label>
                                    <input type="text" class="form-control"  name="country" value="' . $contact_details->country . '">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Post Code:</label>
                                    <input type="text" class="form-control"  name="post_code" value="' . $contact_details->post_code . '">
                                    <input type="hidden" name="billing_same" id="hidden_billing_Same" value="' . $contact_details->billing_same . '">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Notes:</label>
                                    <textarea class="autosize form-control" name="notes" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;">' . $contact_details->notes . '</textarea>
                                </div>';
            if ($contact_details->billing_same == 'yes') {
                $data.= '<div class="form-group">
                             <label class="control-label"><input type="checkbox" id="sameas_edit" checked="checked"> Same as Customer Address</label>                       
                        </div>';
            } else {
                $data.= '<div class="form-group">
                            <label class="control-label"><input type="checkbox" id="sameas_edit"> Same as Customer Address</label>                        
                        </div>';
            }
            $data.= '   </div>
                    </div>
                    
                    <div class="row">';
            if ($contact_details->billing_same == 'yes') {
                $data.= '   <div class="col-md-12" id="samebilling_edit" style="display:none;">';
            } else {
                $data.= '   <div class="col-md-12" id="samebilling_edit" style="display:block;">';
            }
            $data.= '           <h3>Billing Address</h3>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Name:  </label>
                                        <input type="text" class="form-control" name="billing[name]" value="' . $billing_address->name . '">
                                    </div>  
                                    <div class="form-group">                                        
                                        <label class="control-label">Email:</label>
                                        <input type="email" class="form-control" name="billing[email]" value="' . $billing_address->email . '">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Phone:</label>
                                        <input type="text" class="form-control" name="billing[phone]" value="' . $billing_address->phone . '">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Street No.Name/POBOX:</label>
                                        <input type="text" class="form-control" name="billing[street]" value="' . $billing_address->street . '">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Suburb:</label>
                                        <input type="text" class="form-control " name="billing[city]" value="' . $billing_address->city . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">State:</label>
                                        <input type="text" class="form-control" name="billing[state]" value="' . $billing_address->state . '">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Country:</label>
                                        <input type="text" class="form-control" name="billing[country]" value="' . $billing_address->country . '">
                                    </div>  
                                    <div class="form-group">
                                        <label class="control-label">Post Code:</label>
                                        <input type="text" class="form-control" name="billing[post_code]" value="' . $billing_address->post_code . '">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Notes: </label>
                                        <textarea class="autosize form-control" name="billing[notes]" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;">' . $billing_address->notes . '</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pull-right">               
                            <div class="btn-group">
                                <button class="btn btn-info" id="formreset" style="display:none" type="reset">Reset</button>
                                <button class="btn btn-info" type="submit">Save</button>
                            </div>
                        </div>
                </div>';
            echo $data;
        }
    }
    
    /*
     * To update a customer contact
     * Paramtere needed- contact_id, passed through post and ajax
     *       
     */
    
    function update() {
        if (($this->session->userdata('user_logged_in')) && ($this->session->userdata('is_manager') || $this->session->userdata('is_company'))) {
            if ($this->input->post()) {
                $this->form_validation->set_rules('hash_code', '', 'required');
                $this->form_validation->set_rules('name', 'Name', 'required');
                $this->form_validation->set_rules('email', 'Email', 'email');
                $this->form_validation->set_rules('country', 'Country', '');
                $this->form_validation->set_rules('city', 'City', 'required');
                $this->form_validation->set_rules('state', 'State', '');
                $this->form_validation->set_rules('post_code', 'Post Code', '');
                $this->form_validation->set_rules('street', 'Street', 'required');

                if ($this->form_validation->run() == FALSE) {
                    echo validation_errors();
                    die();
                } else {                    
                    $client_data = $this->input->post();
                    $billing = array();
                    if ($this->input->post('billing_same') == 'yes') {
                        $billing['Billing']['name'] = $this->input->post('name');
                        $billing['Billing']['email'] = $this->input->post('email');
                        $billing['Billing']['phone'] = $this->input->post('phone');
                        $billing['Billing']['street'] = $this->input->post('street');
                        $billing['Billing']['state'] = $this->input->post('state');
                        $billing['Billing']['post_code'] = $this->input->post('post_code');
                        $billing['Billing']['city'] = $this->input->post('city');
                        $billing['Billing']['state'] = $this->input->post('state');
                        $billing['Billing']['country'] = $this->input->post('country');
                        $billing['Billing']['notes'] = $this->input->post('notes');

                        $added_client_site_data = array(
                            'last_updated_by' => $this->session->userdata('user_id'),
                            'billing_same' => 'yes',
                            'billing_address' => json_encode($billing),
                        );
                    } else {
                        $billing['Billing'] = $this->input->post('billing');
                        $added_client_site_data = array(
                            'last_updated_by' => $this->session->userdata('user_id'),
                            'billing_same' => 'no',
                            'billing_address' => json_encode($billing),
                        );
                    }
                    unset($client_data['hash_code'],$client_data['billing'], $client_data['billing_same']);
                    $client_data = array_merge((array) $client_data, $added_client_site_data);
                    $where = array(
                        'contact_id' => base64_decode($this->input->post('hash_code')),
                        'is_deleted'=>0,
                    );
                    $client_id = $this->customer_contacts_m->update_customer_contact($client_data, $where);
                    if ($client_id) {
                        echo "success";
                    } else
                        echo "Error";
                    die();
                }
            }
        } else
            error_message("You have not permission to access this page", true);
    }

}
