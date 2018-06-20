<?php
/**
 * Created by PhpStorm.
 * User: anuj
 * Date: 19/6/18
 * Time: 5:34 PM
 */

namespace TBETool;

use Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Exception;
use Google_Service_Drive_DriveFile;

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
    public function listFolder($folder_id = null)
    {
        $this->initializeApp();

        $items = [];
        $pageToken = NULL;

        try {
            $parameters = [];

//                $parameters['trashed'] = false;
            if ($folder_id) {
                $parameters['q'] = "'".$folder_id."' in parents";
            } else {
                $parameters['q'] = "'root' in parents";
            }

            if ($pageToken) {
                $parameters['pageToken'] = $pageToken;
            }

            $results = $this->drive->files->listFiles($parameters);


            foreach ($results->getFiles() as $file) {
                $items[] = $this->_prepareData($file);
            }

            $pageToken = $results->getNextPageToken();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
            $pageToken = NULL;
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
            ]);

            foreach ($response->getFiles() as $file) {
                $items[] = $this->_prepareData($file);
            }

        } while ($pageToken != null);

        return $items;
    }

    /**
     * download file
     *
     * @param string $item_id Item id of the file to download
     * @param string $save_to Local path to save file to
     * @return mixed
     * @throws Exception
     */
    public function download($item_id, $save_to)
    {
        $this->initializeApp();

        if (empty($item_id))
            throw new Exception('item_id is empty');

        if (empty($save_to))
            throw new Exception('save_to path is emtpy');

        try {
            $response = $this->drive->files->get($item_id, [
                'alt' => 'media'
            ]);
        } catch (Google_Service_Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        // retrieve file info to get file name
        try {
            $file = $this->drive->files->get($item_id);
        } catch (Google_Service_Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        $file_name = $file->name;

        if (substr($save_to, -1) != '/')
            $save_to .= '/';

        $file_path = $save_to . $file_name;

        $content = $response->getBody()->getContents();

        file_put_contents($file_path, $content);

        $return_data = $this->_prepareData($file);
        $return_data['download_path'] = $file_path;

        return $return_data;
    }

    /**
     * create new folder
     *
     * @param string $folder_name Folder name to create
     * @param string $folder_id Folder id to create folder in
     * @return mixed
     * @throws Exception
     */
    public function createFolder($folder_name, $parent_folder_id = null)
    {
        $this->initializeApp();

        $meta_options = [
            'name' => $folder_name,
            'memeType' => 'application/vnd.google-apps.folder'
        ];

        if ($parent_folder_id)
            $meta_options['parents'] = $parent_folder_id;

        $fileMetaData = new Google_Service_Drive_DriveFile($meta_options);

        try {
            $file = $this->drive->files->create($fileMetaData);
        } catch (Google_Service_Exception $exception) {
            throw new Exception($exception->getMessage());
        }


        return $this->_prepareData($file);
    }

    /**
     * upload file to particular folder
     *
     * @param string $file_path Local file path
     * @param string $folder_id Folder id to create in
     * @return mixed
     * @throws Exception
     */
    public function upload($file_path, $folder_id = null)
    {
        $this->initializeApp();

        if (empty($file_path))
            throw new Exception('File path is emtpy');

        if (!is_file($file_path))
            throw new Exception('file_path is not a valid file');

        // if folder_id is not provided, upload into root direcotry

        if (!$folder_id)
            $folder_id = 'root';

        $explode_file_path = explode('/', $file_path);
        $file_name = end($explode_file_path);

        $fileMetaData = new Google_Service_Drive_DriveFile([
            'name' => $file_name,
            'parents' => [$folder_id]
        ]);

        $content = file_get_contents($file_path);

        try {
            $file = $this->drive->files->create($fileMetaData, [
                'data' => $content,
                'memeType' => mime_content_type($file_path),
                'uploadType' => 'multipart',
            ]);
        } catch (Google_Service_Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return $this->_prepareData($file);

    }

    /**
     * move file to another folder
     *
     * @param string $file_id File id to move
     * @param string $folder_id Folder id to move to
     * @return mixed
     * @throws Exception
     */
    public function move($file_id, $folder_id)
    {
        $this->initializeApp();

        if (empty($file_id))
            throw new Exception('file_id is empty');

        if (empty($folder_id))
            throw new Exception('folder_id is empty');

        $emptyFileMetadata = new Google_Service_Drive_DriveFile();

        // Retrive the existing parents to remove
        $file = $this->drive->files->get($file_id, ['fields' => 'parents']);
        $previousParents = join(',', $file->parents);

        // Move the file to the new folder
        try {
            $file = $this->drive->files->update($file_id, $emptyFileMetadata, [
                'addParents' => $folder_id,
                'removeParents' => $previousParents,
            ]);
        } catch (Google_Service_Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return $this->_prepareData($file);
    }

    /**
     * prepare output data
     *
     * @param $file
     * @return mixed
     */
    private function _prepareData($file)
    {
        $data['id'] = $file->id;
        $data['name'] = $file->name;
        $data['kind'] = $file->kind;
        $data['type'] = $this->_extractItemType($file->mimeType);
        $data['created_time'] = $file->createdTime;
        $data['file_extension'] = $file->fileExtension;
        $data['mime_type'] = $file->mimeType;
        $data['modified_time'] = $file->modifiedTime;
        $data['original_filename'] = $file->originalFilename;
        $data['size'] = $file->size;

        return $data;
    }

    /**
     * extract item type by exploding mime type of the item
     *
     * @param $mime_string
     * @return mixed|string
     */
    private function _extractItemType($mime_string)
    {
        if (empty($mime_string))
            return '';

        $explode_mime = explode('.', $mime_string);

        return end($explode_mime);
    }
}
