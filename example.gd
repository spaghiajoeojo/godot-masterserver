
extends Node

func _init():

	# Load MasterServer.gd 
	var MasterServer = preload("MasterServer.gd")
	# Create a new instance
	var ms = MasterServer.new("www.mushywarriors.com",2560)
	
	# Add your match to database
	ms.add_game("MyMatch")
	# Alternatively you can specify another ip
	ms.add_game("235.52.12.54","MyMatch2")
	
	# List all the game running at the moment
	var games = ms.get_games()
	for game in games:
		print("Game: "+game["Name"])
		print("IP: "+game["IP"])
		print()
	
	# Delete a game previously created by ip
	ms.del_game_by_ip("235.52.12.54")
	# or by name
	ms.del_game_by_name("MyMatch")
	
	
