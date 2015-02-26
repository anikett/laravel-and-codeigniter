<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Customer_contacts_m extends CI_Model {
    /*
     * to fetch all customer listings data
     */
    function all_customer_contacts($data) {
        $this->db->select('*');
        $this->db->from('customer_contacts');
        $this->db->where($data);
        $this->db->order_by("contact_id", "DESC");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }
    
     /*
     * To add a customer into contacts
     */

    function add_customer_contact($contact_data) {
        $result= $this->db->insert('customer_contacts', $contact_data);
        if($result){
            return $this->db->insert_id();
        } else {
            return $this->db->_error_message();
        }        
    }
    
     /*
     * To update a customer into contacts
      * parameter- updated data in array
      * $where- array with conditions
     */
    
    function update_customer_contact($contact_data, $where) {
        $result = $this->db->update("customer_contacts", $contact_data, $where);
        return $result;
    }
    
     /*
     * To get information of a single customer
     */

    function getCustomerInfo($data) {
        $this->db->select('*');
        $this->db->from('customer_contacts');
        $this->db->where($data);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    } 
    
}
