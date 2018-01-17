<?php

/**
 * @package Joomla.Site
 */

 class modZinioConnector {
    const ENCRYPTION_KEY = "xxxxxxxxxx";
    private function encrypt($pure_string, $encryption_key) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
        return $encrypted_string;
    }

    private function decrypt($encrypted_string, $encryption_key) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
        return $decrypted_string;
    }

    private static function createAPIURL($params) {
        $apiURL = "";
        if ($params->get('useSSL') == 1) {
            $apiURL = "https://";
        } else {
            $apiURL = "http://";
        }
        if ($params->get('sandModeSwitch') == 1) {
            $apiURL = $apiURL . "testapi.rbdigital.com";
        } else {
            if ($params->get('overrideURL') != '') {
                $apiURL = $apiURL . $params->get('overrideURL');
            } else {
                $apiURL = $apiURL . "www.rbdigital.com/api/";
            }
        }
        return $apiURL;
    }

    private static function patronCreateAccount($params, $url, $email, $first_name, $last_name, $password) {
        $create_api_parameters = array(
            'cmd' => "p_account_create",
            'lib_id' => $params->get('libID'),
            'token' => $params->get('libToken'),
            'email' => $email,
            'name' => $first_name,
            'last_name' => $last_name,
            'pwd' => $params->get('passwordpostfix'),
            'pwd2' => $params->get('passwordpostfix')
        );
        $callback_info = self::CallAPI($url, $create_api_parameters);
        return $callback_info;
    }

    public static function connectToZinio($params) {
        $api_URL = self::createAPIURL($params);
        $user_info = self::getInfoFromCelexCatcher($params);
        $user_library_info = self::fetchRBDigitalSubs($user_info['user_id'], $params->get('libID'));
        switch ($user_library_info['has_activated']) {
            // User Already has an RBDigital Account
            case 'Y': {
                    $create_account_info = array('error' => '',
                        'http_code' => '200',
                        'response_status' => 'OK',
                        'launch_url' => '');
                    $callback_info = self::launchService('OK', $create_account_info, $api_URL, $user_info['user_id'], $params);
                    break;
                }
            // User doesn't have a RBDigital Account
            default: {
                    $create_account_info = self::patronCreateAccount($params, $api_URL, $user_info['email'], $user_info['first_name'], $user_info['last_name'], $user_info['password']);
                    if ($create_account_info['error'] != "") {
                        self::updateRBDigitalFlag($user_info['user_id'], $params->get('libID'), 'N');
                        $callback_info = self::launchService($create_account_info['error'], $create_account_info, $api_URL, $user_info['user_id'], $params);
                    } else {
                        self::updateRBDigitalFlag($user_info['user_id'], $params->get('libID'), 'Y');
                        $callback_info = self::launchService('OK', $create_account_info, $api_URL, $user_info['user_id'], $params);
                    }
                }
        }
        return $callback_info;
    }

    private static function launchService($status, $usercreationinfo, $url, $userid, $params) {
        if (!$params->get('debugModeSwitch')) {
            $callback_info = array(
                'status' => $status,
                'api_url' => $url,
                'user_id' => $userid,
                'library_id' => $params->get('libID'),
                'library_token' => $params->get('libToken'),
                'launcher_url' => $params->get('launcherpath')
            );
        } else {
            $callback_info = array(
                'status' => 'DEBUG',
                'api_url' => $url,
                'user_id' => $userid,
                'library_id' => $params->get('libID'),
                'library_token' => $params->get('libToken'),
                'http_code' => $usercreationinfo['http_code'],
                'response_status' => $usercreationinfo['response_status']
            );
        }

        return $callback_info;
    }

    private static function fetchRBDigitalSubs($userid, $libraryid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select($db->quoteName(array('id', 'libid', 'has_activated')))
                ->from($db->quoteName('#__rbdigital_subs'))
                ->where($db->quoteName('id') . '=\'' . $userid . '\' AND libid=\'' . $libraryid . '\'');
        $db->setQuery($query);
        $results = $db->loadObject();
        if (!$results) {
            self::insertRBDigitalSubs($userid, $libraryid, 'N');
            $user_detail = array(
                'id' => $userid,
                'libid' => $libraryid,
                'has_activated' => 'N'
            );
        } else {
            $user_detail = array(
                'id' => $results->id,
                'libid' => $results->libid,
                'has_activated' => $results->has_activated
            );
        }
        return $user_detail;
    }

    private static function deleteRBDigitalSubs($userid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('id') . '=\'' . $userid . '\''
        );
        $query->delete($db->quoteName('#__rbdigital_subs'));
        $query->where($conditions);
        $db->setQuery($query);
        $result = $db->query();
    }

    private static function updateRBDigitalFlag($userid, $libraryid, $status) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $hasActivated = $db->quoteName('has_activated') . '=\'' . $status . '\'';
        $conditions = $db->quoteName('id') . '=' . $userid . ' AND ' . $db->quoteName('libid') . '=\'' . $libraryid . '\'';
        $query->update($db->quoteName('#__rbdigital_subs'))->set($hasActivated)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
    }

    private static function insertRBDigitalSubs($userid, $libraryid, $status) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $columns = array('id', 'libid', 'has_activated');
        $values = array('\'' . $userid . '\'', '\'' . $libraryid . '\'', '\'' . $status . '\'');
        $query
                ->insert($db->quoteName('#__rbdigital_subs'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));
        $db->setQuery($query);
        $db->execute();
    }

    private static function getInfoFromCelexCatcher($params) {
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select($db->quoteName('userpwd'))
                ->from($db->quoteName('#__user_catcher'))
                ->where($db->quoteName('id') . '=\'' . $user->id . '\'');
        $db->setQuery($query);
        $results = $db->loadObject();
        $user_detail = array(
            'user_id' => $user->id,
            'user_name' => $user->username,
            'password' =>  $params->get('passwordpostfix'),
            'email' => $user->email,
            'first_name' => $user->name,
            'last_name' => " "
        );
        return $user_detail;
    }

    private static function CallAPI($url, $data) {
        try {
            $curl = curl_init();
            $curl_options = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_HTTP_VERSION => 1.1,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false
            );
            curl_setopt_array($curl, $curl_options);
            $response = curl_exec($curl);
            $error = curl_error($curl);
            $result = array('error' => '',
                'http_code' => '',
                'response_status' => '',
                'launch_url' => '');
            $responseString = explode("\t", $response);
            if ($error != "" || strtolower($responseString[0]) != "ok") {
                $result['error'] = $error . "\n" . $responseString[0];
                $result['launch_url'] = '#';
            } else {
                $result['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $result['launch_url'] = $responseString[1];
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        curl_close($curl);
        return $result;
    }

    public static function getDescription($params) {
        return $params->get('description');
    }

}

?>
