<?php

/**
 * Queue controls the amount of emails per hour.
 * All emails ar placed in queue and send as quiq as possible.
 * Parameter "emails_per_hour" defines how mutch emails can be send in one hour.
 * If required amount of emails is bigger than this parameter, part of messages will wait until next hour.
 *
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\administrator\email_queue;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

/** @private */
require_once (__DIR__.'/db.php');
require_once(BASE_DIR.LIBRARY_DIR."php/text/html2text.php");
require_once(BASE_DIR.LIBRARY_DIR.'php/mail/phpmailer/class.phpmailer.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/file/functions.php');

/**
 * Class to send emails. Typically all emails should be send trouht this class.
 * @package ImpressPages
 */
class Module{


    /**
     * Adds email to the queue
     *
     * Even if there is a big amount of emails, there is always reserved 20% of traffic for immediate emails.
     * Such emails are: registration cofirmation, contact form data and other.
     * Newsletters, greetings always can wait a litle. So they are not immediate and will not be send if is less than 20% of traffic left.
     *
     * @param string $from email address from whish an email should be send
     * @param string @from_name
     * @param string $to email address where an email should be send
     * @param string @to_name
     * @param string $email email html text
     * @param bool $immediate indicate hurry of an email.
     * @param bool $html true if email message should be send as html
     * @param array $files files that should be attached to the email. Files should be accessible for php at this moment. They will be cached until send time.
     */
    function addEmail($from, $fromName, $to, $toName, $subject, $email, $immediate, $html, $files = null){
        $cached_files = array();
        $cached_file_names = array();
        $cached_file_mime_types = array();
        if($files)
        foreach($files as $key => $file){
            $new_name = 'contact_form_'.rand();
            $new_name = \Library\Php\File\Functions::genUnoccupiedName($new_name, TMP_FILE_DIR);
            if(copy($file['real_name'], TMP_FILE_DIR.$new_name)){
                $cached_files[] = TMP_FILE_DIR.$new_name;
                $cached_file_names[] = $file['required_name'];
                $tmpMimeType = \Library\Php\File\Functions::getMimeType($file['real_name']);
                if($tmpMimeType == null)
                $tmpMimeType = 'Application/octet-stream';
                $cached_file_mime_types[] = $tmpMimeType;
            }else{
                trigger_error('File caching failed');
            }
        }

        $cachedFilesStr = implode("\n", $cached_files);
        $cachedFileNamesStr = implode("\n", $cached_file_names);
        $cachedFileMimeTypesStr = implode("\n", $cached_file_mime_types);
         
        $email = str_replace('src="'.BASE_URL, 'src="', $email);
         
        Db::addEmail($from, $fromName, $to, $toName, $subject, $email, $immediate, $html, $cachedFilesStr, $cachedFileNamesStr, $cachedFileMimeTypesStr);
    }

    /**
     * Checks if there is some emails waiting in queue and sends them if possible.
     */
    function send(){
        global $parametersMod;
        global $log;
        $alreadySent = Db::sentOrLockedCount(60);
        if($alreadySent !== false){
            $available = floor($parametersMod->getValue('administrator', 'email_queue', 'options', 'emails_per_hour')*0.8 - $alreadySent); //20% for imediate emails
            $lockKey = md5(uniqid(rand(), true));
            if($available > 0) {
                if($available > 5 && !defined('CRON')) { //only cron job can send many emails at once.
                    $available = 5;
                }
                $locked = Db::lock($available, $lockKey);
            } else{
                $available = 0;
                $locked = 0;
            }
             
            if($locked == $available) //if in queue left some messages
            $locked = $locked + Db::lockOnlyImmediate($parametersMod->getValue('administrator', 'email_queue', 'options', 'emails_per_hour') - ($alreadySent + $available), $lockKey);
            if($locked){
                $emails = Db::getLocked($lockKey);

                 
                $errors = false;


                foreach($emails as $key => $email){

                    if(function_exists('set_time_limit')) {
                        set_time_limit((sizeof($emails) - $key)*10 + 100);
                    }

                    $mail = new \PHPMailer();
                    /*          $mail->Sender = $email['from'];
                     $mail->addCustomHeader("Return-Path: " . $email['from']);*/

                    $mail->From = $email['from'];
                    $mail->FromName = $email['from_name'];
                    $mail->AddReplyTo($email['from'],$email['from_name']);
                     
                    $mail->WordWrap = 50;                              // set word wrap
                    $mail->CharSet = CHARSET;
                    $mail->Subject  =  $email['subject'];

                    /*	foreach($this->posted_files as $file){
                     if(isset($_FILES[$file]['tmp_name']) && $_FILES[$file]['error'] == 0){
                     $mail->AddAttachment($_FILES[$file]['tmp_name'], $_FILES[$file]['name']);
                     }
                     }*/
                    $files = explode("\n", $email['files']);
                    $file_names = explode("\n", $email['file_names']);
                    $file_mime_types = explode("\n", $email['file_mime_types']);
                    for($i =0; $i<sizeof($files) && $i<sizeof($file_names)&& $i<sizeof($file_mime_types); $i++){
                        if($files[$i] != ''){

                            if($file_mime_types[$i] == '')
                            $answer = $mail->AddAttachment($files[$i], $file_names[$i]);
                            else
                            $answer = $mail->AddAttachment($files[$i], $file_names[$i], "base64", $file_mime_types[$i]);


                            if(!$answer)
                            trigger_error("Can't add attachment. Queue id ".$lock['id']);
                        }
                    }
                     

                    if($email['html']){
                        $mail->IsHTML(true);// send as HTML
                         
                        $h2t = new \Library\Php\Text\Html2text($email['email'], false);
                        //$mail->Body = $email['email'];
                        $mail->MsgHTML($email['email']);
                        $mail->AltBody  =  $h2t->get_text();
                    }else{
                        /*$h2t = new \Library\Php\Text\Html2text($content, false);
                         $mail->Body  =  $h2t->get_text();*/
                        $mail->Body = $email['email'];
                    }

                    $mail->AddAddress($email['to'], $email['to_name']);
                    if(!$mail->Send()){
                        trigger_error("Can't send email ".$email['to']." ".$email['email']);
                        $errors = true;
                    }
                     
                    if(sizeof($emails) > 5) {
                        sleep(1);
                    }
                     
                    Db::unlockOne($email['id']);
                }

            }
        }

    }

}
