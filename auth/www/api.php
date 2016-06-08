<?php
header('Content-Type: text/plain');

require 'common.php';

/* Status codes

    200 - OK
    401 - Invalid appsecret
    403 - Token needed but not specified
    404 - Result not found, data specified (e.g. username or password) is incorrect
    460 - Needed parameters not specified
    461 - Token specified for login method
    500 - Internal server error
    503 - System overloaded or down for maintenance
    540 - Database error
*/


function doApi($q)
{
    $db = zc_init_pdo();
    $userid = 0;
    $token_valid = false;
    $token = "";
    
    //Check if valid app secret was specified
    if(zc_string_empty($q['appsecret'])) return json_encode(array('status' => 401, 'statusmsg' => 'appsecret not specified/REQUESTFAIL'));
    
    try
    {
        //the appsecret is stored as a hash
        $appsecret_verified = false;
        
        $as_q = $db->prepare("select * from `appsecret`");
        
        $as_q->execute();
                  
        while($row = $as_q->fetch())
        {
            if(password_verify($q['appsecret'], $row['appsecret'])) $appsecret_verified = true;
            
        }
        
        if(!$appsecret_verified) return json_encode(array('status' => 401, 'statusmsg' => 'appsecret invalid/REQUESTFAIL'));
    }
    catch(PDOException $ex)
    {
        return json_encode(array('status' => 540, 'statusmsg' => $ex->getMessage()));
    }
    
    //Check if method was supplied
    if(zc_string_empty($q['method'])) return json_encode(array('status' => 400, 'statusmsg' => 'method not specified/REQUESTFAIL'));
    
    //If token was supplied, verify it
    if(!zc_string_empty($q['token']))
    {
        $token = $q['token'];
        
        $token_vq = $db->prepare("select * from `tokens` where `token`=:token");
        $token_vq->execute(array(':token' => $token));
        
        $row = $token_vq->fetch();
        
        if($row)
        {
            //Ensure token has not expired
            if(time() <= intval($row['expiration']))
            {
                $userid = intval($row['user']);
                $token_valid = true;
            }
        }
    }      
    
    //Now do each method
    if($q['method'] == "login")
    {
        //Check if username and password was supplied
        if(zc_string_empty($q['username']) or zc_string_empty($q['password'])) return json_encode(array('status' => 460, 'statusmsg' => 'username or password not specified/REQUESTFAIL'));
        
        //If token is supplied and already valid, don't allow login to proceed
        if($token_valid) return json_encode(array('status' => 461, 'statusmsg' => 'already valid token received for login/REQUESTFAIL'));
        
        $login_success = false;
        $userid_new = 0;
        
        try
        {
            //Now perform the login logic
            $login_q = $db->prepare("select * from `users` where `name`=:username");
            $login_q->execute(array(':username' => $q['username']));
            
            $row = $login_q->fetch();
            
            //Verify password, if username was found
            if($row)
            {
                if(password_verify($q['password'], $row['password']))
                {
                    $login_success = true;
                    $userid_new = intval($row['id']);
                }
            }
                       
        }
        catch(PDOException $ex)
        {
            return json_encode(array('status' => 540, 'statusmsg' => $ex->getMessage()));
        }
        
        if($login_success)
        {
            //Login was successful, create token
            $token_new = bin2hex(openssl_random_pseudo_bytes(16));
            $expiration = time() + (60*45); //45 minutes plus current time
            
            try
            {
                $token_q = $db->prepare("insert into `tokens` (token, user, expiration) values (:token,:user,:expiration)");
                $token_q->execute(array(':token' => $token_new, ':user' => $userid_new, ':expiration' => $expiration));
            }
            catch(PDOException $ex)
            {
                return json_encode(array('status' => 540, 'statusmsg' => $ex->getMessage()));
            }
            
            //Return token and user ID as response
            return json_encode(array('userid' => $userid_new, 'token' => $token_new, 'expiration' => $expiration, 'status' => 200, 'statusmsg' => 'logged in/OK'));
            
         }
         else
         {
            //Login was not successful
            return json_encode(array('status' => 404, 'statusmsg' => 'invalid credentials/USERFAIL'));
         }
     }
     else if($q['method'] == "gettoken")
     {
        //Make sure a valid access code was supplied
        $access_code_valid = false;
        
        if(zc_string_empty($q['accesscode'])) return json_encode(array('status' => 460, 'statusmsg' => 'access code not specified/REQUESTFAIL'));
        
        
        
        //Validate the access code & appsecret and get the token ID
        try
        {
            $token_id = 0;
            $access_cq = $db->prepare("select * from `tokencode` where `code`=:accesscode");
            
            $access_cq->execute(array(':accesscode' => $q['accesscode']));
            
            $row = $access_cq->fetch();
            
            if($row)
            {
                //Ensure that the access code is still valid time-wise
                if(time() > intval($row['expiration'])) return json_encode(array('status' => 403, 'statusmsg' => 'access code not found or expired/REQUESTFAIL'));
                               
                //Get the ID of the token
                $token_id = intval($row['token']);
                
                $access_code_valid = true;
                
            }
            
            if($access_code_valid)
            {
            
                //Get the token
                $token_ac_q = $db->prepare("select * from `tokens` where `id`=:tokenid");
                $token_ac_q->execute(array(':tokenid' => $token_id));
                
                $row_token_ac = $token_ac_q->fetch();
                
                if($row_token_ac)
                {
                    //Return the token as if the user had been logged in directly through the API
                    return json_encode(array('userid' => intval($row_token_ac['user']), 'token' => $row_token_ac['token'], 'expiration' => intval($row_token_ac['expiration']), 'status' => 200, 'statusmsg' => 'token found/OK'));
                }
                else
                {
                    //This isn't supposed to happen
                    return json_encode(array('status' => 500, 'statusmsg' => 'token not found/FAIL'));
                }
                
                //Delete the access code entry
                $ac_delete_q = $db->prepare("delete from `tokencode` where `code`=:accesscode");
                $ac_delete_q->execute(array(':accesscode' => $q['accesscode']));
            }
        }
        catch(PDOException $ex)
        {
            return json_encode(array('status' => 540, 'statusmsg' => $ex->getMessage()));
        }
        
        //Access code was invalid
        return json_encode(array('status' => 403, 'statusmsg' => 'access code invalid/AUTHFAIL'));

     }
     else if($q['method'] == "logout")
     {
        //A valid token is required
        if(!$token_valid) return json_encode(array('status' => 403, 'statusmsg' => 'invalid token/AUTHFAIL'));
        
        //Remove the token entry from the database
        try
        {
            $logout_q = $db->prepare("delete from `tokens` where `token`=:token");
            $logout_q->execute(array(':token' => $token));
        }
        catch(PDOException $ex)
        {
            return json_encode(array('status' => 540, 'statusmsg' => $ex->getMessage()));
        }
        
        return json_encode(array('status' => 200, 'statusmsg' => 'logged out/OK'));
     }        
     else if($q['method'] == "verify")
     {
        if($token_valid) return json_encode(array('status' => 200, 'statusmsg' => 'token verified/OK'));
        
        return json_encode(array('status' => 403, 'statusmsg' => 'invalid token/AUTHFAIL'));
     }
     else if($q['method'] == "refresh")
     {
        //A valid token is required
        if(!$token_valid) return json_encode(array('status' => 403, 'statusmsg' => 'invalid token/AUTHFAIL'));
        
        
        //Add 45 minutes to the current time for a new token expiration time
        $expiration = time() + (60*45);
        
        try
        {
            $refresh_q = $db->prepare("update `tokens` set `expiration`=:expiration where `token`=:token");
            $refresh_q->execute(array(':expiration' => $expiration, ':token' => $token));
        }
        catch(PDOException $ex)
        {
            return json_encode(array('status' => 540, 'statusmsg' => $ex->getMessage()));
        }
        
        //Return new expiration time
        return json_encode(array('expiration' => $expiration, 'status' => 200, 'statusmsg' => 'token refreshed/OK'));
     }      
     else
     {
        //Method specified was invalid
        return json_encode(array('status' => 400, 'statusmsg' => 'invalid method specified/REQUESTFAIL'));
     }
     
     //Somehow we fell through
     //This isn't supposed to happen
     return json_encode(array('status' => 500, 'statusmsg' => 'internal error/FAIL'));
 
}

//!!!!! SET TO POST FOR PRODUCTION
echo doApi($_GET);

?>

