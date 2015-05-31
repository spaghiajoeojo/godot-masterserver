# godot-masterserver
A simple php master server for godot engine

# Server setup
You have to create a new database mySQL then create a table: <br>
<code>
CREATE TABLE games (
ip CHAR(15) NOT NULL CONSTRAINT ip_unique UNIQUE,
name CHAR(35) NOT NULL,
timestamp INT(11) NOT NULL
)
</code><br>
Upload masterserver.php in your server.

# Usage
See example.gd
