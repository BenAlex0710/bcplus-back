<?php

/**
 * The Agora Cloud Recording API Client
 *
 * @link       agora.co
 * @since      1.0.0
 *
 * @package    wp-agora-io
 * @subpackage wp-agora-io/includes
 */

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Http;

define('AGORA_MIN_RAND_VALUE', 10000000);
define('AGORA_MAX_RAND_VALUE', 4294967295);

class AgoraCloudRecording
{
    private $app_url = 'https://api.agora.io/v1/apps/';
    private $settings = null;
    private $authAgoraSDK = '';

    public function __construct()
    {
        $settings = AdminSetting::where('name', 'like', '%agora_%')->pluck('value', 'name');
        $this->settings = [
            'customer_id' => $settings['agora_customer_id'],
            'customer_certificate' => $settings['agora_customer_certificate'],
            'app_id' => $settings['agora_app_id'],
            'app_certificate' => $settings['agora_app_certificate'],
            'recording' => [
                'vendor' => $settings['agora_recording_vendor'],
                'region' => $settings['agora_recording_region'],
                'bucket' => $settings['agora_recording_bucket'],
                'access_key' => $settings['agora_recording_access_key'],
                'secret_key' => $settings['agora_recording_secret_key'],
            ]
        ];

        $this->authAgoraSDK = $this->settings['customer_id'] . ':' . $this->settings['customer_certificate'];
        $this->authAgoraSDK_B64 = base64_encode($this->authAgoraSDK);
    }

    private function acquire($data)
    {
        $endpoint = $this->settings['app_id'] . "/cloud_recording/acquire";
        $params = array(
            'cname' => $data['cname'],
            'uid' => $data['uid'],
            'clientRequest' => json_decode("{}")
        );
        return $this->callAPI($endpoint, $params, 'POST');
    }

    public function updateLayout($data)
    {
        if (isset($data['resource_id']) && !empty($data['resource_id'])) {
            $resource_id = $data['resource_id'];
            $sid = $data['sid'];
        } else {
            $resource = $this->acquire($data);
            $resource_id = $resource['resourceId'];
            $sid = $resource['sid'];
        }

        $endpointUL = $this->settings['app_id'] . '/cloud_recording/resourceid/' . $resource_id . '/sid/' . $sid . '/mode/mix/updateLayout';

        $clientRequest = new stdClass();
        $clientRequest->mixedVideoLayout = 1; // best fit layout
        $clientRequest->backgroundColor = "#000000";

        $params = array(
            'cname' => $data['cname'],
            'uid' => $data['uid'],
            'clientRequest' => $clientRequest
        );
        return $this->callAPI($endpointUL, $params, 'POST');
    }

    private function queryRecording($data)
    {

        if (isset($data['resourceId']) && !empty($data['resourceId'])) {
            $resourceId = $data['resourceId'];
        } else {
            $resource = $this->acquire($data);
            $resourceId = $resource->resourceId;
        }

        if (!isset($data['recordingId'])) {
            return false;
            // return new WP_Error('data', "Incomplete data", $data);
        }

        $sid = $data['recordingId'];
        $endpoint = $this->settings['app_id'] . '/cloud_recording/resourceid/' . $resourceId . '/sid/' . $sid . '/mode/mix/query';

        $params = array(
            'cname' => $data['cname'],
            'uid' => $data['uid'],
            'clientRequest' => json_decode("{}")
        );
        // header('HTTP/1.1 500 Internal Server Error');
        // die("<pre>QUERY:".print_r($endpoint, true)."</pre>");
        return $this->callAPI($endpoint, array(), 'GET');
    }

    public function startRecording($data)
    {
        if (isset($data['resourceId']) && !empty($data['resourceId'])) {
            $resourceId = $data['resourceId'];
        } else {
            $resource = $this->acquire($data);
            $resourceId = $resource['resourceId'];
        }

        $endpoint = $this->settings['app_id'] . '/cloud_recording/resourceid/' . $resourceId . '/mode/mix/start';

        $clientRequest = new stdClass();
        $clientRequest->recordingConfig = new stdClass();
        $clientRequest->recordingConfig->channelType = 1; // 1 = broadcast,  0=Communication
        $clientRequest->recordingConfig->transcodingConfig = new stdClass();
        $clientRequest->recordingConfig->transcodingConfig->mixedVideoLayout = 1; // best fit layout
        $clientRequest->recordingConfig->transcodingConfig->backgroundColor = "#000000";
        $clientRequest->recordingConfig->transcodingConfig->width = 640;
        $clientRequest->recordingConfig->transcodingConfig->height = 480;
        $clientRequest->recordingConfig->transcodingConfig->bitrate = 930;
        $clientRequest->recordingConfig->transcodingConfig->fps = 30;

        $clientRequest->storageConfig = new stdClass();
        $clientRequest->storageConfig->vendor = intval($this->settings['recording']['vendor']);
        $clientRequest->storageConfig->region = intval($this->settings['recording']['region']);
        $clientRequest->storageConfig->bucket = $this->settings['recording']['bucket'];
        $clientRequest->storageConfig->accessKey = $this->settings['recording']['access_key'];
        $clientRequest->storageConfig->secretKey = $this->settings['recording']['secret_key'];

        $t = date('d-m-Y');
        $day = strtolower(date("d", strtotime($t)));
        $month = strtolower(date("m", strtotime($t)));
        $year = strtolower(date("Y", strtotime($t)));

        $fixedTitle = $data['event_id'] . $data['event_title'];
        $folderName = $month . $day . $year . preg_replace('/[^\da-z]/i', '', $fixedTitle);
        $clientRequest->storageConfig->fileNamePrefix = array($folderName);

        $role = RtcTokenBuilder::RolePublisher;

        $expireTimeInSeconds = 360000;
        $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $newToken = RtcTokenBuilder::buildTokenWithUid($this->settings['app_id'], $this->settings['app_certificate'], $data['cname'], $data['uid'], $role, $privilegeExpiredTs);

        // die("<pre>".print_r($newToken, true)."</pre>");
        $clientRequest->token = $newToken;

        $params = array(
            'cname' => $data['cname'],
            'uid' => $data['uid'],
            'clientRequest' => $clientRequest
        );
        $out = $this->callAPI($endpoint, $params, 'POST');
        return $out;
    }


    public function stopRecording($data)
    {
        if (isset($data['resource_id']) && !empty($data['resource_id'])) {
            $resource_id = $data['resource_id'];
            $sid = $data['sid'];
        } else {
            $resource = $this->acquire($data);
            $resource_id = $resource['resourceId'];
            $sid = $resource['sid'];
        }

        if (!isset($data['sid']) || !isset($data['recordingId'])) {
            return false;
        }

        $endpoint = $this->settings['app_id'] . '/cloud_recording/resourceid/' . $resource_id . '/sid/' . $sid . '/mode/mix/stop';

        $params = array(
            'cname' => $data['cname'],
            'uid' => $data['uid'],
            'clientRequest' => json_decode("{}")
        );
        return $this->callAPI($endpoint, $params, 'POST');
    }

    private function callAPI($url = false, $params = array(), $method = 'GET')
    {
        if ($url) {
            $url = $this->app_url . $url;

            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $this->authAgoraSDK_B64
            ];

            if ($method === 'GET') {
                if (!empty($params)) {
                    $query_params = http_build_query($params);
                    $url = sprintf("%s?%s", $url, $query_params);
                }
                $response =  Http::withHeaders($headers)->get($url);
            } else {
                $response =  Http::withHeaders($headers)->post($url, $params);
            }

            if ($response->ok()) {
                return $response->json();
            }
            throw new ErrorException(json_encode($response->json()));
        }
        return false;
    }
}
