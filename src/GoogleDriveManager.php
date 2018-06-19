<?php
/**
 * Created by PhpStorm.
 * User: anuj
 * Date: 19/6/18
 * Time: 5:34 PM
 */

namespace App\Library;
use Aura\Intl\Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Exception;

/**
 * Class GoogleDriveManager
 * @package App\Library
 * @property Google_Service_Drive drive
 */
class GoogleDriveManager
{
    private $access_token;
    private $client_key;
    private $client_secret;

    private $drive;

    function __construct($client_key, $client_secret, $access_token)
    {
        $this->client_key = $client_key;
        $this->client_secret = $client_secret;

        if ($access_token)
            $this->access_token = $access_token;
    }


    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    private function initializeApp()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Drive Manager');
        $client->setClientId($this->client_key);
        $client->setClientSecret($this->client_secret);
        $client->setAccessToken($this->access_token);

//        if ($client->isAccessTokenExpired())
//            throw new Exception('Access token is expired. Generate new one');

        $driveService = new Google_Service_Drive($client);

        $this->drive = $driveService;
    }

    /**
     * list folder items
     *
     * @return array
     */
    public function listFolder()
    {
        $this->initializeApp();

        $items = [];

        $optParams = [
            'pageSize' => 10
        ];

        $results = $this->drive->files->listFiles($optParams);

        foreach ($results->getFiles() as $file) {
            $data['id'] = $file->id;
            $data['name'] = $file->name;
            $data['mime_type'] = $file->mimeType;
            $data['kind'] = $file->kind;
            $items[] = $data;
        }

        return $items;
    }

    /**
     * search for files
     *
     * @param $query
     * @return array
     */
    public function search($query)
    {
        $this->initializeApp();

        $items = [];
        $pageToken = null;
        do {
            $response = $this->drive->files->listFiles([
                'q' => "name contains '".$query."'",
                'spaces' => 'drive',
                'pageToken' => $pageToken,
                'fields' => 'nextPageToken, files(id, name)',
            ]);

            foreach ($response->getFiles() as $file) {
                $data['id'] = $file->id;
                $data['name'] = $file->name;
                $data['kind'] = $file->kind;
                $data['created_time'] = $file->createdTime;
                $data['file_extension'] = $file->fileExtension;
                $data['mime_type'] = $file->mimeType;
                $data['modified_time'] = $file->modifiedTime;
                $data['original_filename'] = $file->originalFilename;
                $data['size'] = $file->size;
                $items[] = $data;
            }

        } while ($pageToken != null);

        return $items;
    }

    /**
     * download file
     *
     * @param $item_id
     * @return mixed
     * @throws Exception
     */
    public function download($item_id)
    {
        $this->initializeApp();

        try {
            $response = $this->drive->files->get($item_id, [
                'alt' => 'media'
            ]);
        } catch (Google_Service_Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        $content = $response->getBody()->getContents();

        return $content;
    }
}