<?php

function Register()
{
	if(isset($_POST['register']))
	{
		if(!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['re-password']))
		{
			function ValidateEmail($email)
			{
				if(filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					if(strlen($email) <= 255)
					{
						return true;
					}
					else
					{
						return false;
					}
				}

				return false;
			}

			function ValidateUsername($username)
			{
				if(strlen($username) <= 32)
				{
					if(ctype_alnum($username))
					{
						return true;
					}
					else
					{
						return false;
					}
				}

				return false;
			}

			function Captcha($secret, $captcha, $lastip)
			{
				$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $captcha . "&remoteip=" . $lastip);
				$decode   = json_decode($response, true);

				if(intval($decode['success']) == 1)
				{
					return true;
				}
				
				return false;
			}

			global $con;

			$username   = $_POST['username'];
			$email      = $_POST['email'];
			$password   = $_POST['password'];
			$repassword = $_POST['re-password'];
			$captcha    = $_POST['g-recaptcha-response'];
			$ip_address = $_SERVER['REMOTE_ADDR'];
			$secret     = CAPTCHA_SECRET;
			$expansion  = EXPANSION;

    function getregdata($username, $password)
    {
        // generate a random salt
        $salt = random_bytes(32);
        
        // calculate verifier using this salt
        $verifier = calculateverifier($username, $password, $salt);
        
        // done - this is what you put in the account table!
        return array($salt, $verifier);
    }

 function calculateverifier($username, $password, $salt)
    {
        // algorithm constants
        $g = gmp_init(7);
        $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
        
        // calculate first hash
        $h1 = sha1(strtoupper($username . ':' . $password), TRUE);
        
        // calculate second hash
        $h2 = sha1($salt.$h1, TRUE);
        
        // convert to integer (little-endian)
        $h2 = gmp_import($h2, 1, GMP_LSW_FIRST);
        
        // g^h2 mod N
        $verifier = gmp_powm($g, $h2, $N);
        
        // convert back to a byte array (little-endian)
        $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);
        
        // pad to 32 bytes, remember that zeros go on the end in little-endian!
        $verifier = str_pad($verifier, 32, chr(0), STR_PAD_RIGHT);
        
        // done!
        return $verifier;
    }

			if(ValidateUsername($username) && ValidateEmail($email))
			{
				$data = $con->prepare('SELECT COUNT(*) FROM account WHERE username = :username OR email = :email');
				$data->execute(array(
					':username' => $username,
					':email'    => $email
				));

				if($data->fetchColumn() == 0)
				{
					if(Captcha($secret, $captcha, $ip_address))
					{
						$data = $con->prepare('INSERT INTO account (username, salt, verifier, email, last_ip, expansion) 
							VALUES(:username, :salt, :verifier, :email, :ip, :expansion)');
						$data->execute(array(
							':username'  => $username,
							':salt'      => ('".bindParam($salt)"'),
							':verifier'  => ('".bin2Param($verifier)."'),
							':email'     => $email,
							':ip'        => $ip_address,
							':expansion' => $expansion
						));

						echo '<div class="callout success">' . SUCCESS_MESSAGE . '</div>';
						echo '<div class="callout warning">' . REALMLIST . '</div>';
					}
					else
					{
						echo '<div class="callout alert">Capctha was invalid!</div>';
					}
				}
				else
				{
					echo '<div class="callout alert">Username or Email is already in use!</div>';
				}
			}
			else
			{
				echo '<div class="callout alert">Username or Email is not valid!</div>';
			}
		}
		else
		{
			echo '<div class="callout alert">All fields are required!</div>';
		}
	}
}

?>
