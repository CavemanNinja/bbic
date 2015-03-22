<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
jimport('joomla.utilities.date');

class plgContentBillrepeat extends JPlugin
{
    
    /**
     * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
     * If you want to support 3.0 series you must override the constructor
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;
    
    /**
     * Plugin method with the same name as the event will be called automatically.
     */
    
    // function onContentBeforeDisplay($context, &$article, &$params, $limit=0) {
    //         JError::raiseNotice( 100, 'onContentBeforeDisplay plugin fired!' );
    //         return "";
    // }
    
    function onContentBeforeSave($context, $article, $isNew) {
        if(property_exists($article, 'catid') && property_exists($article, 'attribs')){

            // JError::raiseNotice( 100, 'onContentBeforeSave plugin fired!' );
            $db = JFactory::getDbo();
            $catid = $article->catid;
            $attribs_json = $article->attribs;
            $attribs = json_decode($attribs_json);

            //Get the parent category id - For Company Profiles
            $query = $db->getQuery(true);
            $query->select($db->quoteName("parent_id"));
            $query->from($db->quoteName("#__categories"));
            $query->where($db->quoteName("id") . " = " . $article->catid);
            $db->setQuery($query);
            $parent_id = $db->loadResult(); //loads single record.
            
            $current_user = JFactory::getUser();
            $isTenant = in_array(10, $current_user->getAuthorisedGroups());

            //Set company profile approval to pending if current user is tenant
            if ($isTenant && ($catid == 9 || $parent_id == 9)) {
                $attribs->companyprofile_approval = "0";
                $article->attribs = json_encode($attribs);
            }

            //Put a company profile in the right category based on laguage and subcategory choice.
            if ($catid == 9 || $parent_id == 9) {
                $subcategory_choice = $attribs->companyprofile_category;
                switch($subcategory_choice) {
                    case 0: 
                        $article->catid = 56;
                        break;
                    case 1:
                        $article->catid = 62;
                        break;
                    case 2: 
                        $article->catid = 49;
                        break;
                    case 3: 
                        $article->catid = 54;
                        break;              
                    case 4: 
                        $article->catid = 65;
                        break;          
                    case 5: 
                        $article->catid = 63;
                        break;
                    case 6: 
                        $article->catid = 57;
                        break;
                    case 7: 
                        $article->catid = 50;
                        break;
                    case 8: 
                        $article->catid = 58;
                        break;
                    case 9: 
                        $article->catid = 33;
                        break;
                    case 10: 
                        $article->catid = 53;
                        break;
                    case 11: 
                        $article->catid = 61;
                        break;
                    case 12: 
                        $article->catid = 64;
                        break;
                    case 13: 
                        $article->catid = 55;
                        break;
                    case 14: 
                        $article->catid = 34;
                        break;
                    case 15: 
                        $article->catid = 51;
                        break;
                    case 16: 
                        $article->catid = 59;
                        break;
                    case 17: 
                        $article->catid = 52;
                        break;
                    case 18: 
                        $article->catid = 60;
                        break;
                }
            }

            //If Bill, add the tenant name to attribs
            if ($catid == 10) {
                $attribs = json_decode($article->attribs);
                $tenant_id = $attribs->billing_tenant_id;
                
                $query = $db->getQuery(true);
                $query->select($db->quoteName('username'));
                $query->from($db->quoteName('#__users'));
                $query->where($db->quoteName('id') . " = " . $tenant_id);
                $db->setQuery($query);
                $result = $db->loadResult();
                
                $attribs->billing_tenant_name = $result;
                $article->attribs = json_encode($attribs);
                
                $article_info = $article->attribs;
                
                // JFactory::getApplication()->enqueueMessage("onContentBeforeSave BILL CALLED");
                
                
            }
            
            //Service Request, add service name+price to attribs
            elseif ($catid == 12) {
                $service_id = $attribs->servicerequest_item;

                $query = $db->getQuery(true);
                $query->select($db->quoteName('attribs'));
                $query->from($db->quoteName('#__content'));
                $query->where($db->quoteName('id') . " = " . $service_id);
                $db->setQuery($query);
                $result = $db->loadResult();
                $service_attribs = json_decode($result);
                
                $attribs->service_price = $service_attribs->service_price;
                $attribs->service_name = $service_attribs->service_name;

                //If service date is not set, set to current date.
                if ($attribs->servicerequest_date = "") {
                    $attribs->servicerequest_date = JFactory::getDate()->toSQL();
                }

                $article->attribs = json_encode($attribs);

            }
            
            //If Service add the price to the title.
            //Add the service name as well as the id
            elseif ($catid == 19) {
                $name = $attribs->service_name;
                $price = $attribs->service_price;
                $article->title = $name . " - BD " . $price;
            }
        }

        $article->state = 1;

        return true;
    }
    
    function onContentAfterSave($context, $article, $isNew) {
        if(property_exists($article, 'catid') && property_exists($article, 'attribs')){
            // JFactory::getApplication()->enqueueMessage("oncontentaftersave catid and attribs article detected");
            // Get DB Object.
            $db = JFactory::getDbo();
            $catid = $article->catid;

            //Get the parent category id - For Company Profiles
            $query = $db->getQuery(true);
            $query->select($db->quoteName("parent_id"));
            $query->from($db->quoteName("#__categories"));
            $query->where($db->quoteName("id") . " = " . $article->catid);
            $db->setQuery($query);
            $parent_id = $db->loadResult(); //loads single record.

            //Collect some article attributes.
            $id = intval($article->id);
            $asset_id = intval($article->asset_id);
            $attribs_json = $article->attribs;
            $attribs = json_decode($attribs_json);
            
            //Check if current user is staff member.
            $current_user = JFactory::getUser();
            $isStaff = in_array(11, $current_user->getAuthorisedGroups());
            $isTenant = in_array(10, $current_user->getAuthorisedGroups());
            

            //Create bill automatically when service request made
            if ($catid == 12) {
                
                
                //Get the service price and name from database
                $tenant_id = $article->created_by;
                $service_id = $attribs->servicerequest_item;
                $query = $db->getQuery(true);
                $query->select($db->quoteName('attribs'));
                $query->from($db->quoteName('#__content'));
                $query->where($db->quoteName('id') . " = " . $service_id);
                $db->setQuery($query);
                $result = $db->loadResult();
                $service_attribs_json = $result;
                $service_attribs = json_decode($result);
                $service_price = $service_attribs->service_price;
                $service_name = $service_attribs->service_name;
                
                //Lookup the tenant name in db.
                /*
                             TODO: Should just be able to use getUser to get this much easier. 
                         
                         
                         $query = $db->getQuery(true);
                         $query->select($db->quoteName('username'));
                         $query->from($db->quoteName('#__users'));
                         $query->where($db->quoteName('id') . " = " . $tenant_id);
                         $db->setQuery($query);
                         $result = $db->loadResult();
                         $tenant_name = $result;
                */
                
                $tenant_user = JFactory::getUser($tenant_id);
                $tenant_email = $tenant_user->email;
                $tenant_username = $tenant_user->username;
                
                //If the SR is new email the user of new SR.
                if ($isNew) {
                    
                    /*
                        Send Email Case (1) to tenant about New Service Request
                    */
                    $body = "Dear " . $tenant_user->name . ",\n";
                    $body.= "Your service request has been received. A member of the BBIC will approve the request as soon as possible.";
                    
                    //TODO: Insert Proper LINK!!
                    $subject = "Your Service Request has been received.";
                    
                    $send = $this->sendEmail($tenant_email, $subject, $body);
                    if ($send == true) {
                        // JFactory::getApplication()->enqueueMessage("Mail Sent.");
                    } else {
                        // JFactory::getApplication()->enqueueMessage("Mail Error.");
                    }

                    /*
                        Send Email Case (2) to Staff Members of Manage Service Requests about New Service Request
                    */
                    //Query for all users in Group Staff>Manage Service Requests (groupid: 15)
                    $query = $db->getQuery(true);
                    $query->select($db->quoteName("user_id"));
                    $query->from($db->quoteName("#__user_usergroup_map"));
                    $query->where($db->quoteName("group_id") . "=15");
                    $db->setQuery($query);
                    $servicerequests_group_userids = $db->loadColumn();
                    $staff_emails = array();
                    $i = 0;
                    if (!empty($servicerequests_group_userids)) {
                        foreach ($servicerequests_group_userids as $staff_id) {
                            $staff_emails[$i] = JFactory::getUser($staff_id)->email;
                            $i++;
                        }
                        // JFactory::getApplication()->enqueueMessage("staff emails: " . print_r($staff_emails, true));
                        $staff_subjuct = "A Tenant has created a new service request.";
                        $staff_body = "A Tenant has added a new service request.";
                        $send = $this->sendEmail($staff_emails, $staff_subject, $staff_body);
                        if ($send == true) {
                            // JFactory::getApplication()->enqueueMessage("Mail Sent.");
                        } else {
                            // JFactory::getApplication()->enqueueMessage("Mail Error.");
                        }
                    }
                }
                
                //If the SR is saved by staff member with the status approved and flag indicating it was just approved
                elseif ($isStaff && $attribs->servicerequest_approval == "1" && $article->metakey == "") {
                    
                    // JFactory::getApplication()->enqueueMessage("first time approved.");
                    /*
                                    Create Bill for Service Request.
                    */
                    $billing_attribs = '{"billing_tenant_name":"' . $tenant_username . '","billing_tenant_id":"' . $tenant_id . '","billing_amount":"' . $service_price . '","billing_invoice_date":"","billing_status":"0","billing_repeatcycle":"","billing_repeatstart":"","billing_repeatend":""}';
                    $jt_article = JTable::getInstance('content');
                    $jt_article->title = "Service Request: " . $article->title;
                    $jt_article->alias = $tenant_id . "-" . $service_name . "-" . $article->id;
                    $jt_article->state = 1;
                    $jt_article->catid = 10;
                    $jt_article->access = 1;
                    $jt_article->attribs = $billing_attribs;
                    $jt_article->metadata = '{"page_title":"","author":"","robots":""}';
                    $jt_article->language = '*';
                    $jt_article->created = JFactory::getDate()->toSQL();
                    
                    // $jt_article->publish_up = JFactory::getDate()->toSQL();
                    
                    //Insert new article into database
                    if (!$jt_article->check()) {
                        JError::raiseNotice(500, $jt_article->getError());
                    }
                    if (!$jt_article->store(TRUE)) {
                        JError::raiseNotice(500, $jt_article->getError() . ", " . $jt_article->alias);
                    } else {
                      // JFactory::getApplication()->enqueueMessage("New Bill created.");  
                    }
                    
                    /*
                        Send Email Service Request Has been approved.
                            Success: Set flag email has been sent on service request attribs.
                    */
                    $subject = "Your Service Request has been Approved.";
                    $body = "Dear " . $tenant_user->name . ",\n";
                    $body.= "Your service request for ".$attribs->servicerequest_item.", has been approved and will be attended to as soon as possible.\n";
                    
                    $send = $this->sendEmail($tenant_email, $subject, $body);
                    if ($send == true) {
                         //Send email successful.
                        
                        $query = $db->getQuery(true);
                        $query->update($db->quoteName('#__content'));
                        $query->set($db->quoteName("metakey") . " = 1");
                        $query->where($db->quoteName("id") . " = " . $id);
                        $db->setQuery($query);
                        $result = $db->query();
                        // JFactory::getApplication()->enqueueMessage("Notification Email sent to Tenant.");
                    } else {
                        // JError::raiseNotice(500, "Mail Error.");
                    }
                }
            }
            
            //Repeating Bills + Bill emails.
            if ($catid == 10) {
                
                //Check bill is set to repeat
                if ($attribs->billing_repeat == 1) {
                    
                    //First remove any previous repeating bills.
                    $query = $db->getQuery(true);
                    
                    $query->delete($db->quoteName('#__content'));
                    $query->where($db->quoteName('alias') . " LIKE " . $db->quote("id-" . $id . "-repeat%"));
                    $db->setQuery($query);
                    $deleteresult = $db->query();
                    
                    $start_time = strtotime($attribs->billing_repeatstart);
                    $end_time = strtotime($attribs->billing_repeatend);
                    $cycle = $attribs->billing_repeatcycle * (24 * 60 * 60);
                    
                    if ($start_time && $end_time && $cycle) {
                        
                        //remove repeated billing from new_article
                        $new_attribs = clone $attribs;
                        $new_attribs->billing_repeat = 0;
                        $new_attribs_json = json_encode($new_attribs);
                        
                        $i = 0;
                        while ($start_time < $end_time) {
                            ++$i;
                            
                            //Joomla Table Method of Creating Article
                            $jt_article = JTable::getInstance('content');
                            $jt_article->title = $article->title . ", Repeated Invoice #" . $i;
                            $jt_article->alias = 'id-' . $id . "-repeat-" . $i;
                            $jt_article->catid = 10;
                            $jdate_start_time = new JDate($start_time);
                            $mysql_start_time = date('Y-m-d h:i:s', $start_time);
                            $jt_article->publish_up = $mysql_start_time;
                            $jt_article->attribs = $new_attribs_json;
                            
                            $jt_article->state = 0;
                            $jt_article->access = 1;
                            $jt_article->metadata = '{"page_title":"","author":"","robots":""}';
                            $jt_article->language = '*';
                            $jt_article->created = JFactory::getDate()->toSQL();
                            
                            //insert new article into database
                            if (!$jt_article->check()) {
                                JError::raiseNotice(500, $jt_article->getError());
                                
                                // return FALSE;
                                
                                
                            }
                            if (!$jt_article->store(TRUE)) {
                                JError::raiseNotice(500, $jt_article->getError());
                                
                                // return FALSE;
                                
                                
                            }
                            
                            $start_time = $start_time + $cycle;
                        }
                    } else {
                        JError::raiseWarning(100, 'Warning: Unable to create repeated invoices, please check repeated billing information.');
                    }
                }
                
                //If the bill is new send a notification email to the Tenant.
                if ($isNew) {
                    
                    //Send Notification Email to tenant, New Bill Created
                    $attribs = json_decode($article->attribs);
                    $tenant_id = $attribs->billing_tenant_id;
                    $tenant_user = JFactory::getUser($tenant_id);
                    $tenant_email = $tenant_user->email;
                    
                    $subject = "A new bill has been added to you account.";
                    
                    $body = "Dear " . $tenant_user->name . ",\n";
                    $body.= "You have received the following bill ".$attribs->billing_invoice_id.", ". $attribs->billing_description. "\n";
                    $body.= "Please arrange for the bill to be paid.\n";
                    $body.= "If arrangements have been made and the charges have been received, we will confirm the payment of the bill through an e-mail reply.";
                    
                    //TODO: Insert Proper LINK!!
                    
                    $send = $this->sendEmail($tenant_email, $subject, $body);
                    if ($send == true) {
                        // JFactory::getApplication()->enqueueMessage("Mail Sent.");
                    } else {
                        // JError::raiseNotice(500, "Mail Error.");
                    }


                }

                elseif ($isStaff && $attribs->billing_status != "0" && $article->metakey == "") {
                    //Send Notification Email to tenant, New Bill Created
                    $attribs = json_decode($article->attribs);
                    $tenant_id = $attribs->billing_tenant_id;
                    $tenant_user = JFactory::getUser($tenant_id);
                    $tenant_email = $tenant_user->email;

                    $subject = "Thank you, Your Bill has been Paid.";

                    $body = "Hello " . $tenant_user->name . ",\n";
                    $body .= "Bill ".$attribs->billing_invoice_id.", ". $attribs->billing_description. " has been paid in full. Thank You.";

                    $send = $this->sendEmail($tenant_email, $subject, $body);
                    if ($send == true) {
                        // JFactory::getApplication()->enqueueMessage("Mail Sent.");
                    } else {
                        // JError::raiseNotice(500, "Mail Error.");
                    }   
                }
            }

            //Company Profile Emails. Must use Parent category and catid!

            
            if ($catid == 9 || $parent_id == 9) {
                $tenant_id = $article->created_by;
                $tenant_user = JFactory::getUser($tenant_id);
                $tenant_email = $tenant_user->email;
                
                //Company Profile saved by Tenant with Pending Status
                if ($isTenant && $attribs->companyprofile_approval == "0") {
                    //Send Email, 
                    $subject = "Your Company Profile has been saved.";
                    $body = "Dear " . $tenant_user->name . ",\n";
                    $body .= "Your company profile has been saved and is pending for review with the BBIC staff.\n";

                    $send = $this->sendEmail($tenant_email, $subject, $body);
            
                    if ($send == true) {
                        // JFactory::getApplication()->enqueueMessage("Mail Sent.");
                    } else {
                        // JError::raiseNotice(500, "Mail Error.");
                    }

                    //Query for all users in Group Staff>Manage Service Requests (groupid: 15)
                    $query = $db->getQuery(true);
                    $query->select($db->quoteName("user_id"));
                    $query->from($db->quoteName("#__user_usergroup_map"));
                    $query->where($db->quoteName("group_id") . "=13");
                    $db->setQuery($query);
                    $servicerequests_group_userids = $db->loadColumn();
                    $staff_emails = array();
                    $i = 0;
                    if (!empty($servicerequests_group_userids)) {
                        foreach ($servicerequests_group_userids as $staff_id) {
                            $staff_emails[$i] = JFactory::getUser($staff_id)->email;
                            $i++;
                        }
                        // JFactory::getApplication()->enqueueMessage("staff emails: " . print_r($staff_emails, true));
                        $staff_subject = $tenant_user." Tenant has updated their profile.";
                        $staff_body = "Dear BBIC Staff Member,\n";
                        $staff_body .= $tenant_user." has updated thier profile, please review and approve the company profile.";
                        $send = $this->sendEmail($staff_emails, $staff_subject, $staff_body);
                        if ($send == true) {
                            // JFactory::getApplication()->enqueueMessage("Mail Sent.");
                        } else {
                            // JFactory::getApplication()->enqueueMessage("Mail Error.");
                        }
                    }
                }
                
                //Company Profile saved by Staff with Approved Status
                if ($isStaff && $attribs->companyprofile_approval == "1") {
                    $subject = "Your Company Profile has been approved.";
                    $body = "Dear " . $tenant_user->name . ",\n";
                    $body .= "Your company profile has been approved by BBIC staff . The company profile is displayed now or will be displayed shortly.\n";

                    $send = $this->sendEmail($tenant_email, $subject, $body);
                    if ($send == true) {
                        // JFactory::getApplication()->enqueueMessage("Mail Sent.");
                    } else {
                        // JError::raiseNotice(500, "Mail Error.");
                    }
                }
            }
        }        

        return true;
    }
    
    function onContentChangeState($context, $pks, $value) {
        
        // JFactory::getApplication()->enqueueMessage("onContentChangeState Called");
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('catid'));
        $query->from($db->quoteName('#__content'));
        $query->where($db->quoteName('id') . " = " . $pks[0]);
        $db->setQuery($query);
        $result = $db->loadResult();
        
        if ($value == 1 && $result == "10") {
            
            // JFactory::getApplication()->enqueueMessage("onContentChangeState: Billed state change unpublished->published");
            //Send an Email to Tenant whern Bill changes from unpublished to published.
            $mailer = JFactory::getMailer();
            $config = JFactory::getConfig();
            $sender = array($config->get('config.mailfrom'), $config->get('config.fromname'));
            $mailer->setSender($sender);
            
            //Get the tenant associated with the bill
            $query = $db->getQuery(true);
            $query->select($db->quoteName('attribs'));
            $query->from($db->quoteName('#__content'));
            $query->where($db->quoteName('id') . " = " . $pks[0]);
            $db->setQuery($query);
            $result = $db->loadResult();
            $billing_attribs = json_decode($result);
            $recipient_id = $billing_attribs->billing_tenant_id;
            
            //Get the tenants email their user ID and add them as recipient.
            $recipient = JFactory::getUser($recipient_id);
            $recipient_email = $recipient->email;
            $mailer->addRecipient($recipient_email);
            
            $body = "Hello " . $recipient->name . ",\n";
            $body.= "A new bill has been added to your account.\n";
            $body.= "Follow this link to your billing page to view details.\n";
            
            //TODO: Juri base adds administrator to the url if used from plugin.. change this when you know the address.
            $body.= JUri::base() . "/bbic/joomla/index.php/en/my-billing";
            
            $mailer->setSubJect("A new bill has been added to you account.");
            $mailer->setBody($body);
            
            $send = $mailer->Send();
            if ($send == true) {
                JError::raiseNotice(500, $send->__toString());
            } else {
                echo "Mail Sent";
            }
        }
    }
    
    function sendEmail($recipient, $subject, $body) {
        $mailer = JFactory::getMailer();
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $config = JFactory::getConfig();
        $sender = array($config->get('config.mailfrom'), $config->get('config.fromname'));
        $mailer->setSender($sender);
        
        $signature = '<div>';
        $signature .= "<br/>Regards,";
        $signature .='<br/><img src="'.JURI::base().'/images/logo.jpg" alt="logo"/>';
        $signature .= '<br/>Tel: +973 17358888';
        $signature .= '<br/>Fax: +973 17358888';
        $signature .= '<br/>Email: ';
        $signature .= '<br/>Website: www.bbicbahrain.com / www.bdb-bh.com';
        $signature .= '<br/>PO Box 50450, Kingdom of Bahrain';
        $signature .= '<br/>BBIC Arabic Ad : http://youtu.be/-y6HnxI9Ooc';
        $signature .= '<br/>BBIC English Ad : http://youtu.be/pI5lTbC9Dkg';
        $signature .= '</div>';
        $body .= $signature;

        // JFactory::getApplication()->enqueueMessage("Send Email, Subject: " . $subject);
        $mailer->addRecipient($recipient);
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        
        return $mailer->Send();
    }
}
?>