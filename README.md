# Google Drive Manager
Google Drive Manager

### Install
```
composer require tbetool/google-drive-manager
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
		'id' => '1BEfQmdMja45M3ewc548OhiQLh9or9P6KGpqYECipV6c',
		'name' => 'Resume of Anuj Sharma',
		'mime_type' => 'application/vnd.google-apps.document',
		'kind' => 'drive#file'
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
$drive->download('item_id');

```

