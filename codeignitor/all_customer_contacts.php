<div class="main-container inner">
    <!-- start: PAGE -->
    <div class="main-content">
        <div class="container">
            <div class="toolbar row">
                <div class="col-sm-6 hidden-xs">
                    <div class="page-header">
                        <h1>Customer Contacts</h1>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <a class="close-subviews">
                        <i class="fa fa-times"></i> CLOSE
                    </a>
                    <div class="toolbar-tools pull-right">
                        <!-- start: TOP NAVIGATION MENU -->
                        <ul class="nav navbar-right">

                        </ul>
                        <!-- end: TOP NAVIGATION MENU -->
                    </div>
                </div>
            </div>

            <!-- start: BREADCRUMB -->
            <div class="row">
                <div class="col-md-12">
                    <ol class="breadcrumb">
                        <li><a href="<?php echo base_url(); ?>">Dashboard</a></li>
                        <li class="active">Customer Contacts</li>
                    </ol>
                </div>
            </div>
            <!-- end: BREADCRUMB -->
            <!-- start: PAGE CONTENT -->

            <div class="row">
                <div class="col-md-12">
                    <!-- start: EXPORT DATA TABLE PANEL  -->
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h4 class="panel-title">All <span class="text-bold">Customer Contacts</span></h4>
                            <div class="panel-tools">
                                <div class="dropdown">
                                    <a data-toggle="dropdown" class="btn btn-xs dropdown-toggle btn-transparent-grey">
                                        <i class="fa fa-cog"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-light pull-right" role="menu">
                                        <li>
                                            <a class="panel-refresh" href="#">
                                                <i class="fa fa-refresh"></i> <span>Refresh</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>                         
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12 space20">
                                    <a class="btn btn-green show-sv" href="#addCustomerContact">Add a Contact <i class="fa fa-plus"></i></a>                                    
                                </div>
                            </div>

                            <div class="table-responsive">
                                <a class="show-sv" id="edit_popup"  href="#editCustomerContactForm"></a>
                                <div id="customerContactTableBody">
                                    <table class="table table-striped table-hover" id="customer_contact_list_table">
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
                                        <tbody>
                                            <?php
                                            if ($all_contacts) {
                                                $l = count($all_contacts);
                                                foreach ($all_contacts as $contact) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $l; ?></td>

                                                        <td><?php echo $contact->name; ?></td>

                                                        <td><?php echo $contact->phone; ?></td>

                                                        <td><a href="mailto:<?php echo $contact->email; ?>"><?php echo $contact->email; ?></a></td>

                                                        <td><?php echo $contact->street; ?></td>

                                                        <td><?php echo $contact->city; ?></td>

                                                        <td><?php echo $contact->state; ?></td>

                                                        <td class="action_buttons">
                                                            <div class="visible-md visible-lg hidden-sm hidden-xs"> 
                                                                <a id="<?php echo base64_encode($contact->contact_id); ?>" data-original-title="View/Edit" data-placement="top" class="edit_customer_contact btn btn-xs btn-orange tooltips" href="#"><i class="fa fa-edit fa-white fa-lg"></i></a> 

                                                                <?php if ($contact->status == '1') { ?>    
                                                                    <a id="<?php echo base64_encode($contact->contact_id); ?>" data-original-title="Deactivate" data-placement="top" class="deactivate_customer_contact btn btn-xs btn-light-green tooltips" href="#"><i class="fa-check-circle-o fa fa-white fa-lg"></i></a>
                                                                <?php } else { ?>
                                                                    <a id="<?php echo base64_encode($contact->contact_id); ?>" data-original-title="Activate" data-placement="top" class="activate_customer_contact btn btn-xs btn-red tooltips" href="#"><i class="fa-ban fa fa-white fa-lg"></i></a>
                                                                <?php } ?>
                                                                <?php if ($this->session->userdata('is_company')) { ?>
                                                                    <a id="<?php echo base64_encode($contact->contact_id); ?>" data-original-title="Delete" data-placement="top" class="delete_customer_contact btn btn-xs btn-red tooltips" href="#" tabindex="-1" role="menuitem" >
                                                                        <i class="fa fa-times fa fa-white fa-lg"></i> 
                                                                    </a> 
                                                                <?php } ?>
                                                            </div>
                                                            <div class="visible-xs visible-sm hidden-md hidden-lg">
                                                                <div class="btn-group">
                                                                    <a href="#" data-toggle="dropdown" class="btn btn-green dropdown-toggle btn-sm">
                                                                        <i class="fa fa-cog"></i> <span class="caret"></span>
                                                                    </a>
                                                                    <ul class="dropdown-menu pull-right dropdown-dark" role="menu">
                                                                        <li>
                                                                            <a id="<?php echo base64_encode($contact->contact_id); ?>" data-toggle="dropdown" class="edit_customer_contact btn btn-green dropdown-toggle btn-sm">
                                                                                <i class="fa fa-edit"></i> View/Edit
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <?php if ($contact->status == '1') { ?>    
                                                                                <a id="<?php echo base64_encode($contact->contact_id); ?>" tabindex="-1" role="menuitem" class="deactivate_customer_contact btn btn-green dropdown-toggle btn-sm" href="#"><i class="fa-check-circle-o fa fa-white fa-lg"></i> Deactivate</a>
                                                                            <?php } else { ?>
                                                                                <a id="<?php echo base64_encode($contact->contact_id); ?>" tabindex="-1" role="menuitem" class="activate_customer_contact btn btn-green dropdown-toggle btn-sm" href="#"><i class="fa-ban fa fa-white fa-lg"></i> Activate</a>
                                                                            <?php } ?>

                                                                        </li>
                                                                        <?php if ($this->session->userdata('is_company')) { ?>
                                                                            <li>
                                                                                <a id="<?php echo base64_encode($contact->contact_id); ?>" href="#" tabindex="-1" role="menuitem"  class="delete_customer_contact btn btn-green dropdown-toggle btn-sm">
                                                                                    <i class="fa fa-times"></i> Delete
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $l--;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- end: EXPORT DATA TABLE PANEL -->
                </div>
            </div>
            <!-- end: PAGE CONTENT-->
        </div>
        <div class="subviews">
            <div class="subviews-container"></div>
        </div>
    </div>
    <!-- end: PAGE -->
</div>
<div id="addCustomerContact" class="no-display">
    <div class="noteWrap col-md-8 col-md-offset-2">
        <h3>Add Customer Contact</h3>
        <form class="form-contributor" id="addCustomerContactForm" method="post" action="#">
            <div class="row">
                <div class="col-md-12">

                    <div class="errorHandler alert alert-danger no-display" id="ajaxerror">

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Name:  <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="name">                        
                        </div>  
                        <div class="form-group">                            
                            <label class="control-label">Email:</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Phone:<span class="symbol required"></span></span>
                            </label>
                            <input type="text" class="form-control input-mask-ausphone" name="phone">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Street No.Name/POBOX:<span class="symbol required"></span>
                            </label>
                            <input type="text" class="form-control" name="street">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Suburb:<span class="symbol required"></span>
                            </label>
                            <input type="text" class="form-control " name="city">
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">                            
                            <label class="control-label">State:</label>
                            <input type="text" class="form-control" value="<?php echo set_value('state'); ?>" name="state">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Country:</label>
                            <input type="text" class="form-control"  name="country">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Post Code:</label>
                            <input type="text" class="form-control"  name="post_code">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Site Note: </label>
                            <textarea class="autosize form-control" name="notes" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;"></textarea>
                        </div> 
                        <div class="form-group">
                            <label class="checkbox-inline">
                                <input type="checkbox" id="sameas" value="yes" checked="checked" name="billing_same" id="sameas">Billing Address same as Customer address.</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="samebilling" style="display:none;">
                        <h3>Billing Address</h3>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Name:  </label>
                                <input type="text" class="form-control contact-method" name="billing[name]" id="billing_name">
                            </div>  
                            <div class="form-group">
                                <input class="contributor-id hide" type="text">
                                <label class="control-label">Email:</label>
                                <input type="email" class="form-control contact-method" name="billing[email]" id="billing_email">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Phone.:
                                </label>
                                <input type="text" class="form-control input-mask-ausphone" name="billing[phone]" id="billing_phone">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Street No.Name/POBOX:
                                </label>
                                <input type="text" class="form-control" name="billing[street]" id="billing_street">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Suburb:
                                </label>
                                <input type="text" class="form-control " name="billing[city]" id="billing_city">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <input class="contributor-id hide" type="text">
                                <label class="control-label">State:</label>
                                <input type="text" class="form-control" name="billing[state]" id="billing_state">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Country:  </label>
                                <input type="text" class="form-control" name="billing[country]" id="billing_country">
                            </div>  
                            <div class="form-group">
                                <input class="contributor-id hide" type="text">
                                <label class="control-label">Post Code.:</label>
                                <input type="text" class="form-control" name="billing[post_code]" id="billing_post_code">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Site Note: </label>
                                <textarea class="autosize form-control" name="billing[notes]" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 69px;"></textarea>
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
            </div>

        </form>
    </div>
</div>

<div id="editCustomerContactForm" class="no-display">
    <div class="noteWrap col-md-8 col-md-offset-2">
        <h3>Edit Customer Contact</h3>
        <form class="form-contributor" id="edit_customer_contact" method="post" action="#">
            <div id="editCustomerContactUI">

            </div>            
        </form>
    </div>
</div>




<!-- start: MAIN JAVASCRIPTS -->
<!--[if lt IE 9]>
<script src="<?php echo base_url(); ?>assets/plugins/respond.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/excanvas.min.js"></script>
                <![endif]-->
<!--[if gte IE 9]><!-->

<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->

<script src="<?php echo base_url(); ?>assets/js/ui-subview.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/autosize/jquery.autosize.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/form-elements.js"></script>
<script src="<?php echo base_url(); ?>assets/js/contact_validation.js"></script>
<script src="<?php echo base_url(); ?>assets/js/ui-notifications.js"></script>
<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->

<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
<script src="<?php echo base_url(); ?>assets/js/customer_contact_actions.js"></script>
<script>

jQuery(document).ready(function() {
        UISubview.init();
        FormElements.contact_needs();
        Validation.addContactValidation();
        contact_table_inititalization();
        
        jQuery('.dropdown').on('click', '.panel-refresh', function(e) {
            refresh_all_customer_contacts();
        });
        jQuery('.tooltips').tooltip();
        pageActions();
        toastr.options = {
            "closeButton": true,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "2000",
            "hideDuration": "1500",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    });
    </script>
    