# Google Drive Manager
Google Drive Manager

### Install
```
composer require tbetool/google-drive-manager
```

### Permission required
```
https://www.googleapis.com/auth/drive
```

### Initialize
```
$drive = new GoogleDriveManager('client_id', 'client_secret', 'access_token');
```

### set access token
```
$drive->setAccessToken('access_token');
```

### List items from folder
```
$response = $drive->listFolder();
```

##### Response
response will contain
```
[
	(int) 0 => [
		'id' => '1YoSEbFDSOejkZmtPmfOyBIoWODhllPjJ',
		'name' => 'Selling Digital Items Modules to be updated ',
		'kind' => null,
		'type' => 'folder',
		'created_time' => null,
		'file_extension' => null,
		'mime_type' => null,
		'modified_time' => null,
		'original_filename' => null,
		'size' => null
	],
]
```

### Search for item
```
$drive->search('query');
```

Response:
```
[
	(int) 0 => [
		'id' => '1YoSEbFDSOejkZmtPmfOyBIoWODhllPjJ',
		'name' => 'Selling Digital Items Modules to be updated ',
		'kind' => null,
		'type' => 'folder',
		'created_time' => null,
		'file_extension' => null,
		'mime_type' => null,
		'modified_time' => null,
		'original_filename' => null,
		'size' => null
	],
]

```
### download file

*currently in development*

```
$drive->download('item_id', $save_to_path);

```
**save_to_path** should be absolute local path where to save file

Response:
```
[
	'id' => '1lJNyeIx5BpyK88Vj31YFG6WVVNY_g9Hj',
	'name' => 'file.zip',
	'kind' => 'drive#file',
	'type' => 'application/zip',
	'created_time' => null,
	'file_extension' => null,
	'mime_type' => 'application/zip',
	'modified_time' => null,
	'original_filename' => null,
	'size' => null,
	'download_path' => '/home/path/to/local/save/to/file.zip'
]
```

### create folder
```
$drive->createFolder('folder name', $parent_folder_id);
```
if **$parent_folder_id** is not provided, folder will be created in root folder
Response:

```
[
	'id' => '1Q6fozdc2JK32HO2nimSKz1lQ0AVxl413',
	'name' => 'New Folder 123',
	'kind' => 'drive#file',
	'type' => 'folder',
	'created_time' => null,
	'file_extension' => null,
	'mime_type' => 'application/vnd.google-apps.folder',
	'modified_time' => null,
	'original_filename' => null,
	'size' => null
]
```


### upload file to folder
```
$drive->upload('file/path/', $folder_id);
```
**file_path** must be absolute path of local file

if **folder_id** is not provided, file will be uploaded to root folder

Response:
```
[
	'id' => '1Q6fozdc2JK32HO2nimSKz1lQ0AVxl413',
	'name' => 'New Folder 123',
	'kind' => 'drive#file',
	'type' => 'folder',
	'created_time' => null,
	'file_extension' => null,
	'mime_type' => 'application/vnd.google-apps.folder',
	'modified_time' => null,
	'original_filename' => null,
	'size' => null
]
```

### move file to another folder
```
$drive->move($file_id, $folder_id);
```

both **file_id** and **folder_id** are required.

Response:
```
[
	'id' => '1Q6fozdc2JK32HO2nimSKz1lQ0AVxl413',
	'name' => 'New Folder 123',
	'kind' => 'drive#file',
	'type' => 'folder',
	'created_time' => null,
	'file_extension' => null,
	'mime_type' => 'application/vnd.google-apps.folder',
	'modified_time' => null,
	'original_filename' => null,
	'size' => null
]
```


