# godot-masterserver
A simple php master server for godot engine

# Server setup
You have to create a new database (I use mysql but you can use what you want with few changes):<br>

TABLE : games <br>
FIELDS:
- ip char(15) unique
- name char(35)
- timestamp int(11)


Upload masterserver.php in your server.

# Usage
######Load MasterServer.gd <br>
```
var MasterServer = preload("MasterServer.gd")
```

######Create a new instance
```
var ms = MasterServer.new("SERVER_URL",2560)
```

######Add your match to database
```
ms.add_game("MyMatch")
```

######Alternatively you can specify another ip
```
ms.add_game("235.52.12.54","MyMatch2")
```

######List all the game running at the moment
```
var games = ms.get_games()
for game in games:
	print("Game: "+game["Name"])
	print("IP: "+game["IP"])
	print()
```

######Delete a game previously created by ip
```
ms.del_game_by_ip("235.52.12.54")
```

######or by name
```
ms.del_game_by_name("MyMatch")
```

