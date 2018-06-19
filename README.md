# Dropbox upload
Upload to dropbox

### Install
```
composer require tbetool/dropbox-php
```

### Initialize
```
$dropbox = new Dropbox($client_key, $client_secret, $access_token);
```

### set access token
```
$dropbox->setAccessToken('access_token');
```

### Uploading
```
$response = $dropbox->upload('/file/path', 'title');
```

##### Response
response will contain
```
[
    'id' => 'upload_id',
    'file_name' => 'uploaded file name'
]
```

### List folder items
```
$dropbox->listFolder('path_of_folder');
```
default: **"/"**

if no path is provided, it will list all items in root folder

Response:
```
[
	'data' => [
		(int) 0 => [
			'.tag' => 'folder',
			'name' => 'my_apps',
			'path_lower' => '/my_apps',
			'path_display' => '/my_apps',
			'id' => 'id:wDFg96ot7lwAAAAAAAAAAw'
		],
	],
	'cursor' => 'AAF8pZtHZJlfwPYrLcKMzbxCNpGaExOHSK-LN8I--pmujanZ1XwEw4CHAzw288liKjDbBCkAy1b5SDZMyKGnRqkpk7heOy3p1MoRt640O6xLleBXRNEn41kTQ4GaWpVLHPuWWiBXTFiOFuC_ig67zb9K2KSwnAW-iLRuaQDDcHZ7Fw',
	'has_more' => false
]
```
### list remaining items from folder
if `has_more` is **true** in above request, pass the **cursor** value as second parameter to the same function
```
$dropbox->listFolder('path_of_folder', $cursor);

```

### Get revisions of file
```
$dropbox->getRevisions('file_path', $limit);
```
if **limit** is not provided, default is **3**

Response:
```
[
	(int) 0 => [
		'name' => 'OBdoTHEm.mp4',
		'path_lower' => '/obdothem.mp4',
		'path_display' => '/OBdoTHEm.mp4',
		'id' => 'id:wDFg96ot7lwAAAAAAAAAKg',
		'client_modified' => '2018-06-11T10:50:17Z',
		'server_modified' => '2018-06-11T10:50:17Z',
		'rev' => '1c1f916734',
		'size' => (int) 72821,
		'content_hash' => '25d03e535dc1e4ec86fb35b8fc56d4075a583adf007339217a906a4dabbed721'
	]
]
```

### search
search for file/folder in folder
```
$dropbox->search('zip', $path, $start, $max_results);
```
if **path** is not provided, default search will be in root directory

if **start** is not provided, default is **0**

if **max_results** is not provided, default is **5**

Response:
```
[
	'data' => [
		(int) 0 => [
			'.tag' => 'file',
			'name' => 'OBdoTHEm.mp4',
			'path_lower' => '/obdothem.mp4',
			'path_display' => '/OBdoTHEm.mp4',
			'id' => 'id:wDFg96ot7lwAAAAAAAAAKg',
			'client_modified' => '2018-06-11T10:50:17Z',
			'server_modified' => '2018-06-11T10:50:17Z',
			'rev' => '1c1f916734',
			'size' => (int) 72821,
			'content_hash' => '25d03e535dc1e4ec86fb35b8fc56d4075a583adf007339217a906a4dabbed721'
		]
	],
	'cursor' => (int) 1,
	'has_more' => false
]
```

### create folder
create folder at specified path
```
$dropbox->createFolder('/Folder Name');
```

Response:
```
[
	'name' => 'Folder Name',
	'path_lower' => '/folder name',
	'path_display' => '/Folder Name',
	'id' => 'id:wDFg96ot7lwAAAAAAAAAUg'
]
```

### delete
delete file/folder
```
$dropbox->delete('/Folder Name');
```

Response:
```
[
	'.tag' => 'folder',
	'name' => 'Folder Name',
	'path_lower' => '/folder name',
	'path_display' => '/Folder Name',
	'id' => 'id:wDFg96ot7lwAAAAAAAAAUg'
]
```

### move
move file/folder to another path
```
$dropbox->move($current_path, $move_to_path);
```

### copy
copy file/folder to another path
```
$dropbox->copy($current_path, $move_to_path);
```

### get temporary link
get temporary link of file
```
$dropbox->getTemporaryLink('/path to file');
```

Response:
```
[
	'metadata' => [
		'name' => 'file_name.zip',
		'path_lower' => '/file_name.zip',
		'path_display' => '/file_name.zip',
		'id' => 'id:wDFg96ot7lwAAAAAAAAACg',
		'client_modified' => '2016-02-20T17:59:59Z',
		'server_modified' => '2016-02-20T17:59:59Z',
		'rev' => '91f916734',
		'size' => (int) 218245,
		'content_hash' => '5e838e8a2dfa077c732e2aa95b2dbd2c0b549a96b728af36db84c5d17c899895'
	],
	'link' => 'https://dl.dropboxusercontent.com/apitl/1/...'
]
```

### download file
download file to specified path
```
$dropbox->download('/file paht', $save_to);
```
**save_to** is the local path of the file to save it.

Example: `$dropbox->download('/file.zip', '/path/to/file.zip');`

Response: Downloaded file information
```
[
	'name' => 'PHP_QR_Code_Generate.zip',
	'path_lower' => '/php_qr_code_generate.zip',
	'path_display' => '/PHP_QR_Code_Generate.zip',
	'id' => 'id:wDFg96ot7lwAAAAAAAAACg',
	'client_modified' => '2016-02-20T17:59:59Z',
	'server_modified' => '2016-02-20T17:59:59Z',
	'rev' => '91f916734',
	'size' => (int) 218245,
	'content_hash' => '5e838e8a2dfa077c732e2aa95b2dbd2c0b549a96b728af36db84c5d17c899895'
]
```