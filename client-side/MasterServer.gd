var _host = "localhost" # url of masterserver
var _port = 1337		# listen port for udp

var _headers = {}

var client = HTTPClient.new()

# To create a new MasterServer object use MasterServer.new(url,port)
# Port will be used for UDP connection (MasterServer interrogations uses port 80)
func _init(host, port):
	_host = host
	_port = port
	_headers = {"User-Agent": "Godot Game Engine","Content-Type": "application/x-www-form-urlencoded"}
	print("MasterServer url: "+host)

# Server respond with a JSON. Here JSON is parsed in Dictionary and then in a handy array
func get_games():
	var json = _request('ac=get')['body']
	print(json)
	var dic = {}
	
	var err = dic.parse_json(json)
	assert(err==OK)
	var arr = Array()
	for key in dic.keys():
		arr.append(dic[key])
	return arr
	
# To add a game you need to specify only the first parameter.
# Use master_server.add_game(name). It's possible specify ip too.
func add_game(name, ip=null):
	if(ip!=null):
		return _request('ac=add&ip='+ip+'&name='+name)
	else:
		return _request('ac=add&name='+name)
	
func del_game_by_ip(ip):
	return _request('ac=del&ip='+ip)
	
func del_game_by_name(name):
	return _request('ac=del&name='+name)
	
# masterserver.php must be in the folder specified by _host in a PHP server
func _request(body):
	var res = _connect()
	if( res["error"] ):
		return res
	else:
		var headers = StringArray()
		for h in _headers:
			headers.push_back(h + ": " + _headers[h])
		client.request( HTTPClient.METHOD_POST, "/masterserver.php", headers, body)
	
	res = _poll();
	client.close()
	
	return res
	
func _connect():
	client.connect(_host, 80)
	return _poll()
	
func _poll():
	var status = -1
	var current_status
	while(true):
		client.poll()
		current_status = client.get_status()
		if( status != current_status ):
			status = current_status
			print("HTTPClient entered status ", status)
			if( status == HTTPClient.STATUS_RESOLVING ):
				continue
			if( status == HTTPClient.STATUS_REQUESTING ):
				continue
			if( status == HTTPClient.STATUS_CONNECTING ):
				continue
			if( status == HTTPClient.STATUS_CONNECTED ):
				return _respond(status)
			if( status == HTTPClient.STATUS_DISCONNECTED ):
				return _errorResponse("Disconnected from Host")
			if( status == HTTPClient.STATUS_CANT_RESOLVE ):
				return _errorResponse("Can't Resolve Host")
			if( status == HTTPClient.STATUS_CANT_CONNECT ):
				return _errorResponse("Can't Connect to Host")
			if( status == HTTPClient.STATUS_CONNECTION_ERROR ):
				return _errorResponse("Connection Error")
			if( status == HTTPClient.STATUS_BODY ):
				return _parseBody()
				
func _parseBody():
	var body = client.read_response_body_chunk().get_string_from_utf8()
	var response = _respond(body)
	if( response["code"] >= 400 ):
		response["error"] = true
		
	return response

# Returns a Dictionary with details of the response
func _respond(body):
	var response = Dictionary()
	response["body"] = body
	response["code"] = client.get_response_code()
	response["length"] = client.get_response_body_length()
	response["headers"] = client.get_response_headers_as_dictionary()
	response["error"] = false
	return response

func _errorResponse(body):
	var response = _respond(body)
	response["error"] = true
	return response
