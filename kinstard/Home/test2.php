<?php

function clean_data($data) {
  // Prevent SQL injection by converting all characters to html characters
  $data = trim(htmlentities($_POST[$data]));
  return $data;
}

function session_inactive($expire_after = 30) {
  // Expire page after user has been inactive for a specified minutes
  // Default time is 30 minutes, a new default can be set or overwritten

  if(isset($_SESSION['LAST_LOGIN_TIME'])){

    // Figure out how many seconds have passed
    // since the user was last active.
    $seconds_inactive = time() - $_SESSION['LAST_LOGIN_TIME'];

    // Convert our minutes into seconds.
    $expire_after_seconds = $expire_after * 60;

    // Check to see if they have been inactive for too long.
    if($seconds_inactive >= $expire_after_seconds){
        // User has been inactive for too long.
        // Kill their session.
        session_unset();
        session_destroy();
    }

  }

  // Assign the current timestamp as the user's
  // latest activity
  return $_SESSION['LAST_LOGIN_TIME'] = time();
}

function user_authenticated() {
  // Check if user has been granted access to restricted area or
  // redirect to get authorization
  if (isset($_SESSION['GRANT_ACCESS']) && $_SESSION['GRANT_ACCESS'] == TRUE) {
    // User failed to prove authorization
    return TRUE;
  } else {
    return FALSE;
  }
}

// function do_login($username, $password){
//   // $hashed_password = sha1($password);
//   $user = User::find_by_username_and_password($username, $hashed_password);
//   if($user!==null) {
//     // Find user id, user_role and contain it in a session
//     $_SESSION['user_role'] = $user->user_role;
//     $_SESSION['user_id'] = $user->id;

//     return true;

//   } else {
//     return false;
//   }
// }

function login($username, $password) {
  $user = User::find_by_username($username);

  if ($user !== null) {
    if (password_verify($password, $user->password)) {
      // Find user id, user_role and contain it in a session
      $_SESSION['user_role'] = $user->user_role;
      $_SESSION['user_id'] = $user->id;
      $_SESSION['active'] = $user->active; // Added later on to help in redirecting suspended users

      return true;

    } else {
       return false;
    }

  }

}

function user_is_admin($user_role) {
  if ($user_role !== 1) {
    session_unset();
    session_destroy();
  } else {
    return true;
  }
}

function user_is_suspended($set_status) {
  if($set_status == 1){
    $_SESSION['flash_message'] = 'Your account has been suspended. Contact your Account Manager to resolve this issue.';
    header('location: kicked_out.php');
  }
}
